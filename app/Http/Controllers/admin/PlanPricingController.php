<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\PricingPlan;
use App\Models\User;
use App\Models\Item;
use App\Models\Tax;
use App\Models\Transaction;
use App\Models\SystemAddons;
use App\Helpers\helper;
use App\Helpers\Systems;
use App\Helpers\Subscriptions;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Stripe;
use PDF;
use Illuminate\Support\Str;

class PlanPricingController extends Controller
{
    public function view_plan(Request $request)
    {
        if (SystemAddons::where('unique_identifier', 'subscription')->first() == null) {
            return redirect()->back()->with(['error' => 'You can not charge your end customers in regular license. Please purchase extended license to charge your end customers']);
        } else {
            $allplan = PricingPlan::orderBy('reorder_id');
            if (Auth::user()->type == 2) {
                $allplan = $allplan->where('is_available', '1');
                // V2: a merchant only ever sees the plans of the System they signed up for.
                // Plans created before the `system` column existed count as Orders & Stores.
                $system = Systems::normalise(Auth::user()->system);
                $allplan = $allplan->where(function ($q) use ($system) {
                    $q->where('system', $system);
                    if ($system === Systems::ORDERS) {
                        $q->orWhereNull('system')->orWhere('system', '');
                    }
                });
            }
            $allplan = $allplan->get();

            // Dashboard context: which system was purchased, the current plan name and whether
            // this visit is a first purchase or an upgrade.
            $vendor = Auth::user()->type == 4 ? User::find(Auth::user()->vendor_id) : Auth::user();
            $planSystem = Systems::normalise(optional($vendor)->system);
            $currentPlan = Systems::planName($vendor);
            $isUpgrade = Systems::hasPaid($vendor);

            // Benefit / bank transfer / cash are approved by hand, so the merchant must be able to
            // see that their receipt is in and still waiting - not just a silent "success".
            $pendingPayment = null;
            if (!empty($vendor)) {
                $pendingPayment = Transaction::where('vendor_id', $vendor->id)
                    ->whereNull('transaction_type')
                    ->where('status', 1)
                    ->whereIn('payment_type', Subscriptions::MANUAL)
                    ->orderByDesc('id')
                    ->first();
            }

            return view('admin.plan.plan', compact("allplan", "planSystem", "currentPlan", "isUpgrade", "pendingPayment"));
        }
    }
    /**
     * V2 rule 7: the subscription period starts when the merchant activates their public website,
     * not when they pay. So a first purchase is stored with no expiry — Systems::activateWebsite()
     * fills it in later. A renewal on an already-live store keeps the old behaviour.
     */
    private function deferredExpiry($vendorId, $plan)
    {
        $vendor = User::find($vendorId);
        if (Systems::isLive($vendor)) {
            return helper::get_plan_exp_date($plan->duration, $plan->days) ?: null;
        }
        return null;
    }

    public function add_plan(Request $request)
    {
        if (Auth::user()->type == 4) {
            $vendor_id = Auth::user()->vendor_id;
        } else {
            $vendor_id = Auth::user()->id;
        }
        $gettaxlist = Tax::where('vendor_id', $vendor_id)->where('is_deleted', 2)->where('is_available', 1)->orderBy('reorder_id')->get();
        $vendors = User::where('type', 2)->where('is_available', 1)->where('is_deleted', 2)->get();
        return view('admin.plan.add_plan', compact('gettaxlist', 'vendors'));
    }

    /**
     * Persist the shared "system engine" fields (system, currency, offer, extra limits,
     * add-ons, display/status, per-system features) onto a plan. Used by save + update.
     */
    private function applySystemFields($plan, Request $request): void
    {
        $plan->system = in_array($request->system, ['orders', 'booking', 'service'], true) ? $request->system : 'orders';
        $plan->currency = $request->currency ?: 'USD';
        $plan->recommended = $request->recommended == '1' ? 1 : 2;
        $plan->visibility = $request->visibility == '2' ? 2 : 1;
        $plan->reorder_id = (int) ($request->display_order ?: 1);
        $plan->is_available = $request->plan_status == '2' ? 2 : 1;

        // Extra limits (branch / whatsapp / team) + a copy of the primary/secondary limits.
        $limits = is_array($request->limits) ? $request->limits : [];
        $limits['primary'] = ['type' => $request->service_limit_type, 'count' => $request->service_limit_type == '2' ? -1 : $request->plan_max_business];
        $limits['secondary'] = ['type' => $request->booking_limit_type, 'count' => $request->booking_limit_type == '2' ? -1 : $request->plan_appoinment_limit];
        $plan->plan_limits = $limits;

        // Offer engine.
        $offer = is_array($request->offer) ? $request->offer : [];
        $offer['enabled'] = !empty($offer['enabled']) ? 1 : 0;
        $plan->plan_offer = $offer;

        // Paid add-ons (allow + price per extra unit).
        $addons = is_array($request->addon) ? $request->addon : [];
        foreach (['branch', 'whatsapp', 'team'] as $k) {
            $addons[$k]['allow'] = !empty($addons[$k]['allow']) ? 1 : 0;
            $addons[$k]['price'] = $addons[$k]['price'] ?? 0;
        }
        $plan->plan_addons = $addons;

        // Per-system feature toggles (stored as a map for easy lookup).
        $xf = (array) $request->input('xfeat_' . $plan->system, []);
        $plan->plan_extra_features = array_fill_keys(array_filter($xf), 1);
    }

    /**
     * Server-side checks for the system engine: Limited counts must be > 0, enabled add-ons need a
     * price > 0, and an enabled offer needs its value.
     * Returns an error message, or null when everything is valid.
     */
    private function validateSystemFields(Request $request): ?string
    {
        // Primary/secondary limits (existing fields) — Limited must be > 0.
        if ($request->service_limit_type == '1' && (float) $request->plan_max_business <= 0) {
            return 'The main limit is set to Limited — please enter a maximum greater than zero.';
        }
        if ($request->booking_limit_type == '1' && (float) $request->plan_appoinment_limit <= 0) {
            return 'The monthly limit is set to Limited — please enter a maximum greater than zero.';
        }
        // Extended limits.
        foreach (['branch' => 'Branches', 'whatsapp' => 'WhatsApp Numbers', 'team' => 'Team / Staff'] as $k => $lbl) {
            $t = $request->limits[$k]['type'] ?? '1';
            if ($t == '1' && (float) ($request->limits[$k]['count'] ?? 0) <= 0) {
                return $lbl . ' is set to Limited — please enter a maximum greater than zero.';
            }
        }
        // Add-ons — enabled needs a price > 0.
        foreach (['branch' => 'Branch', 'whatsapp' => 'WhatsApp Number', 'team' => 'Staff / Provider'] as $k => $lbl) {
            if (!empty($request->addon[$k]['allow']) && (float) ($request->addon[$k]['price'] ?? 0) <= 0) {
                return 'The "Extra ' . $lbl . '" add-on is enabled — please enter a price greater than zero.';
            }
        }
        // Offer — enabled needs its value(s).
        if (!empty($request->offer['enabled'])) {
            $ot = $request->offer['type'] ?? '';
            if ($ot === 'percentage' && (float) ($request->offer['discount_percentage'] ?? 0) <= 0) {
                return 'Please enter a discount percentage greater than zero for the offer.';
            }
            if ($ot === 'fixed' && (float) ($request->offer['offer_amount'] ?? 0) <= 0) {
                return 'Please enter an offer price greater than zero.';
            }
            if ($ot === 'free_duration' && (float) ($request->offer['free_duration'] ?? 0) <= 0) {
                return 'Please enter the free duration for the offer.';
            }
            if ($ot === 'pay_x_get_y' && ((int) ($request->offer['paid_months'] ?? 0) <= 0 || (int) ($request->offer['free_months'] ?? 0) <= 0)) {
                return 'Please enter both paid and free months for the offer.';
            }
        }
        return null;
    }

    public function save_plan(Request $request)
    {
        $check = User::where('id', '1')->first();

        $request->validate([
            'plan_name' => 'required',
            'plan_price' => 'required',
            'plan_duration' => 'required_if:type,1',
            'plan_max_business' => 'required_if:service_limit_type,1',
            'plan_description' => 'required',
            'plan_features' => 'required',
            'plan_appoinment_limit' => 'required_if:booking_limit_type,1',
            'plan_days' => 'required_if:type,2',
        ], [
            'plan_name.required' => trans('messages.name_required'),
            'plan_price.required' => trans('messages.price_required'),
            'plan_duration.required_if' => trans('messages.duration_required'),
            'plan_max_business.required_if' => trans('messages.plan_max_business'),
            'plan_description.required' => trans('messages.description_required'),
            'plan_features.required' => trans('messages.plan_features'),
            'plan_appoinment_limit.required_if' => trans('messages.appoinment_limit'),
            'plan_days.required_if' => trans('messages.days_required'),
        ]);
        if ($err = $this->validateSystemFields($request)) {
            return redirect()->back()->withInput()->with('error', $err);
        }
        $exitplan = PricingPlan::where('price', '0')->count();
        if ($exitplan > 0 && $request->plan_price == '0') {
            return redirect('admin/plan/add')->with('error', trans('messages.already_exist_plan'));
        } else {
            if ($request->custom_domain == "on") {
                $custom_domain = 1;
            } else {
                $custom_domain = "2";
            }
            if ($request->vendor_app == "on") {
                $vendor_app = 1;
            } else {
                $vendor_app = "2";
            }
            if ($request->google_analytics == "on") {
                $google_analytics = 1;
            } else {
                $google_analytics = "2";
            }

            if ($request->coupons == "on") {
                $coupons = 1;
            } else {
                $coupons = "2";
            }

            if ($request->blogs == "on") {
                $blogs = 1;
            } else {
                $blogs = "2";
            }

            if ($request->google_login == "on") {
                $google_login = 1;
            } else {
                $google_login = "2";
            }

            if ($request->facebook_login == "on") {
                $facebook_login = 1;
            } else {
                $facebook_login = "2";
            }

            if ($request->sound_notification == "on") {
                $sound_notification = 1;
            } else {
                $sound_notification = "2";
            }

            if ($request->whatsapp_message == "on") {
                $whatsapp_message = 1;
            } else {
                $whatsapp_message = "2";
            }
            if ($request->employee == "on") {
                $employee = 1;
            } else {
                $employee = "2";
            }
            if ($request->telegram_message == "on") {
                $telegram_message = 1;
            } else {
                $telegram_message = "2";
            }

            if ($request->pos == "on") {
                $pos = 1;
            } else {
                $pos = "2";
            }
            if ($request->pwa == "on") {
                $pwa = 1;
            } else {
                $pwa = "2";
            }
            if ($request->tableqr == "on") {
                $tableqr = 1;
            } else {
                $tableqr = "2";
            }
        }

        $saveplan = new PricingPlan();
        $saveplan->name = $request->plan_name;
        $saveplan->themes_id = ''; // stores are designed by the AI designer — plans carry no themes
        $saveplan->description = $request->plan_description;
        $saveplan->features = self::joinField($request->plan_features, "|");
        $saveplan->price = $request->plan_price;
        $saveplan->plan_type = $request->type;
        $saveplan->tax = empty($request->plan_tax) ? null : self::joinField($request->plan_tax, "|");
        if ($request->type == "1") {
            $saveplan->duration = $request->plan_duration;
            $saveplan->days = "";
        }
        if ($request->type == "2") {
            $saveplan->duration = "";
            $saveplan->days = $request->plan_days;
        }
        if ($request->service_limit_type == "1") {
            $saveplan->order_limit = $request->plan_max_business;
        } elseif ($request->service_limit_type == "2") {
            $saveplan->order_limit = -1;
        }
        if ($request->booking_limit_type == "1") {
            $saveplan->appointment_limit = $request->plan_appoinment_limit;
        } elseif ($request->booking_limit_type == "2") {
            $saveplan->appointment_limit = -1;
        }
        $saveplan->custom_domain = $custom_domain;
        $saveplan->vendor_app = $vendor_app;
        $saveplan->google_analytics = $google_analytics;
        $saveplan->coupons = $coupons;
        $saveplan->blogs = $blogs;
        $saveplan->google_login = $google_login;
        $saveplan->facebook_login = $facebook_login;
        $saveplan->sound_notification = $sound_notification;
        $saveplan->whatsapp_message = $whatsapp_message;
        $saveplan->telegram_message = $telegram_message;
        $saveplan->pos = $pos;
        $saveplan->pwa = $pwa;
        $saveplan->tableqr = $tableqr;
        $saveplan->role_management = $employee;
        $saveplan->vendor_id = self::joinField($request->vendors, "|") ?: $request->vendors;
        $this->applySystemFields($saveplan, $request);
        $saveplan->save();
        return redirect('admin/plan')->with('success', trans('messages.success'));
    }
    public function edit_plan($id)
    {
        if (Auth::user()->type == 4) {
            $vendor_id = Auth::user()->vendor_id;
        } else {
            $vendor_id = Auth::user()->id;
        }
        $editplan = PricingPlan::where('id', $id)->first();
        $gettaxlist = Tax::where('vendor_id', $vendor_id)->where('is_deleted', 2)->where('is_available', 1)->get();
        $vendors = User::where('type', 2)->where('is_available', 1)->where('is_deleted', 2)->get();
        return view('admin.plan.edit_plan', compact("editplan", "gettaxlist", "vendors"));
    }
    public function update_plan(Request $request, $id)
    {

        $request->validate([
            'plan_name' => 'required',
            'plan_price' => 'required',
            'plan_duration' => 'required_if:type,1',
            'plan_max_business' => 'required_if:service_limit_type,1',
            'plan_description' => 'required',
            'plan_features' => 'required',
            'plan_appoinment_limit' => 'required_if:booking_limit_type,1',
            'plan_days' => 'required_if:type,2',
        ], [
            'plan_name.required' => trans('messages.name_required'),
            'plan_price.required' =>  trans('messages.price_required'),
            'plan_duration.required_if' => trans('messages.plan_duration'),
            'plan_max_business.required_if' => trans('messages.plan_max_business'),
            'plan_description.required' => trans('messages.description_required'),
            'plan_features.required' => trans('messages.plan_features'),
            'plan_appoinment_limit.required_if' => trans('messages.appoinment_limit'),
            'plan_days.required_if' => trans('messages.days_required'),
        ]);
        if ($err = $this->validateSystemFields($request)) {
            return redirect()->back()->withInput()->with('error', $err);
        }

        if ($request->custom_domain == "on") {
            $custom_domain = 1;
        } else {
            $custom_domain = "2";
        }
        if ($request->google_analytics == "on") {
            $google_analytics = 1;
        } else {
            $google_analytics = "2";
        }
        if ($request->vendor_app == "on") {
            $vendor_app = 1;
        } else {
            $vendor_app = "2";
        }
        if ($request->coupons == "on") {
            $coupons = 1;
        } else {
            $coupons = "2";
        }
        if ($request->blogs == "on") {
            $blogs = 1;
        } else {
            $blogs = "2";
        }
        if ($request->google_login == "on") {
            $google_login = 1;
        } else {
            $google_login = "2";
        }
        if ($request->facebook_login == "on") {
            $facebook_login = 1;
        } else {
            $facebook_login = "2";
        }
        if ($request->sound_notification == "on") {
            $sound_notification = 1;
        } else {
            $sound_notification = "2";
        }
        if ($request->whatsapp_message == "on") {
            $whatsapp_message = 1;
        } else {
            $whatsapp_message = "2";
        }
        if ($request->telegram_message == "on") {
            $telegram_message = 1;
        } else {
            $telegram_message = "2";
        }
        if ($request->pos == "on") {
            $pos = 1;
        } else {
            $pos = "2";
        }
        if ($request->pwa == "on") {
            $pwa = 1;
        } else {
            $pwa = "2";
        }
        if ($request->tableqr == "on") {
            $tableqr = 1;
        } else {
            $tableqr = "2";
        }
        if ($request->employee == "on") {
            $employee = 1;
        } else {
            $employee = "2";
        }

        $exitplan = PricingPlan::where('price', '0')->count();
        if ($exitplan > 1 && $request->plan_price == '0') {
            return redirect('admin/plan/edit-' . $id)->with('error', trans('messages.already_exist_plan'));
        } else {
            $editplan = PricingPlan::where('id', $id)->first();
            $editplan->name = $request->plan_name;
            $editplan->themes_id = '';
            $editplan->description = $request->plan_description;
            $editplan->features = self::joinField($request->plan_features, "|");
            $editplan->price = $request->plan_price;
            $editplan->plan_type = $request->type;
            $editplan->tax = empty($request->plan_tax) ? null : self::joinField($request->plan_tax, "|");
            if ($request->type == "1") {
                $editplan->duration = $request->plan_duration;
                $editplan->days = "";
            }
            if ($request->type == "2") {
                $editplan->duration = "";
                $editplan->days = $request->plan_days;
            }
            if ($request->service_limit_type == "1") {
                $editplan->order_limit = $request->plan_max_business;
            } elseif ($request->service_limit_type == "2") {
                $editplan->order_limit = -1;
            }
            if ($request->booking_limit_type == "1") {
                $editplan->appointment_limit = $request->plan_appoinment_limit;
            } elseif ($request->booking_limit_type == "2") {
                $editplan->appointment_limit = -1;
            }
            $editplan->custom_domain = $custom_domain;
            $editplan->google_analytics = $google_analytics;
            $editplan->vendor_app = $vendor_app;
            $editplan->coupons = $coupons;
            $editplan->blogs = $blogs;
            $editplan->google_login = $google_login;
            $editplan->facebook_login = $facebook_login;
            $editplan->sound_notification = $sound_notification;
            $editplan->whatsapp_message = $whatsapp_message;
            $editplan->telegram_message = $telegram_message;
            $editplan->pos = $pos;
            $editplan->pwa = $pwa;
            $editplan->tableqr = $tableqr;
            $editplan->role_management = $employee;
            $editplan->vendor_id = self::joinField($request->vendors, "|") ?: $request->vendors;
            $this->applySystemFields($editplan, $request);
            $editplan->update();
            return redirect('admin/plan')->with('success', trans('messages.success'));
        }
    }
    /**
     * Join a repeating form field into the pipe/comma separated string these columns store.
     *
     * Unchecked checkboxes and removed repeater rows are simply absent from the POST, so the
     * value arrives as null and implode() throws a TypeError ("argument #2 must be of type
     * array, null given"). A plan with no themes selected — the Service system currently has
     * none at all — is a legitimate state, so it saves as an empty string instead of crashing.
     */
    private static function joinField($value, string $glue): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        $items = array_filter(
            array_map('trim', (array) $value),
            fn($v) => $v !== ''
        );

        return implode($glue, $items);
    }

    public function status_change($id, $status)
    {
        PricingPlan::where('id', $id)->update(['is_available' => $status]);
        return redirect('admin/plan')->with('success', trans('messages.success'));
    }
    public function select_plan($id)
    {

        if (Auth::user()->type == 4) {
            $vendor_id = Auth::user()->vendor_id;
        } else {
            $vendor_id = Auth::user()->id;
        }
        $plan = PricingPlan::where('id', $id)->first();
        $totalitem = Item::where('vendor_id', $vendor_id)->count();
        if (!empty($totalitem)) {
            if ($plan->order_limit != -1) {
                if ($plan->order_limit < $totalitem) {
                    return redirect('admin/plan')->with('error', trans('messages.not_eligible_for_plan'));
                }
            }
        }
        $checkbanktransfer = helper::checkplan($vendor_id, '');
        $data = json_decode(json_encode($checkbanktransfer), true);
        if ($data['original']['status'] == 2 && $data['original']['bank_transfer'] == 1) {
            return redirect('admin/plan')->with('error', $data['original']['message']);
        }
        if ($plan->price > 0) {
            // Whatever the admin has switched on under Payment Methods is what a merchant can pay
            // with. Turning a method off in the panel is the only thing that removes it.
            $paymentmethod = Subscriptions::availableMethods();
            return view('admin.plan.plan_payment', compact('plan', 'paymentmethod'));
        } else {
            $transaction = new Transaction();
            $transaction->vendor_id = $vendor_id;
            $transaction->plan_name = $plan->name;
            $transaction->plan_id = $id;
            $transaction->payment_type = "";
            $transaction->payment_id = "";
            $transaction->amount = $plan->price;
            $transaction->service_limit = $plan->order_limit;
            $transaction->appoinment_limit = $plan->appointment_limit;
            // Plan taxes + any ACTIVE platform rule scoped to subscription invoices for this
            // system. With every rule inactive this returns empty arrays and nothing is charged.
            [$tax_amount, $tax_name] = Subscriptions::taxForPlan($plan);
            $transaction->tax = implode('|', $tax_amount);
            $transaction->tax_name = implode('|', $tax_name);
            $transaction->grand_total =  0;
            $transaction->system = $plan->system;
            $transaction->currency = $plan->currency ?: 'USD';
            $transaction->plan_snapshot = Subscriptions::snapshot($plan);
            $transaction->expire_date = $this->deferredExpiry($vendor_id, $plan);
            $transaction->duration = $plan->duration;
            $transaction->days = $plan->days;
            $transaction->purchase_date = date("Y-m-d h:i:sa");
            $transaction->duration = $plan->duration;
            $transaction->themes_id = $plan->themes_id;
            $transaction->custom_domain = $plan->custom_domain;
            $transaction->google_analytics = $plan->google_analytics;
            $transaction->vendor_app = $plan->vendor_app;
            $transaction->coupons = $plan->coupons;
            $transaction->blogs = $plan->blogs;
            $transaction->google_login = $plan->google_login;
            $transaction->facebook_login = $plan->facebook_login;
            $transaction->sound_notification = $plan->sound_notification;
            $transaction->whatsapp_message = $plan->whatsapp_message;
            $transaction->telegram_message = $plan->telegram_message;
            $transaction->pos = $plan->pos;
            $transaction->pwa = $plan->pwa;
            $transaction->tableqr = $plan->tableqr;
            $transaction->transaction_number = Str::upper(Str::random(8));
            $transaction->save();
            if (session()->has('discount_data')) {
                session()->forget('discount_data');
            }
            User::where('id', $vendor_id)->update(['plan_id' => $id, 'purchase_amount' => $plan->price, 'purchase_date' => Carbon::now()->toDateTimeString()]);
            Systems::markPaid($vendor_id, $plan->system);
            // Straight from signup: go to Dashboard Setup, which collects the business details
            // and verification documents.
            if (session()->pull('oc_after_payment') === 'setup') {
                return redirect('admin/setup')->with('success', trans('messages.success'));
            }
            $emaildata = helper::emailconfigration(helper::appdata('')->id);
            Config::set('mail', $emaildata);
            helper::send_subscription_email(Auth::user()->email, Auth::user()->name, $plan->name, helper::get_plan_exp_date($plan->duration, $plan->days), helper::currency_formate($plan->price, ""), "-", "-");
            return redirect()->back()->with('success', trans('messages.success'));
        }
    }


    public function success(Request $request)
    {
        if (Auth::user()->type == 4) {
            $vendor_id = Auth::user()->vendor_id;
        } else {
            $vendor_id = Auth::user()->id;
        }
        try {
            if (@$request->paymentId != "") {
                $paymentid = $request->paymentId;
            }
            if (@$request->payment_id != "") {
                $paymentid = $request->payment_id;
            }
            if (@$request->transaction_id != "") {
                $paymentid = $request->transaction_id;
            }

            if (session()->get('payment_type') == "11") {
                if ($request->code == "PAYMENT_SUCCESS") {
                    $paymentid = $request->transactionId;
                } else {
                    return redirect('admin/plan')->with('error', trans('messages.wrong'));
                }
            }

            if (session()->get('payment_type') == "12") {
                $checkstatus = app('App\Http\Controllers\addons\PayTabController')->checkpaymentstatus(session()->get('tran_ref'), '1');

                if ($checkstatus == "A") {
                    $paymentid = session()->get('tran_ref');
                } else {
                    return redirect('admin/plan')->with('error', session()->get('paytab_response'));
                }
            }

            if (session()->get('payment_type') == "13") {

                $checkstatus = app('App\Http\Controllers\addons\MollieController')->checkpaymentstatus(session()->get('tran_ref'), '1');

                if ($checkstatus == "A") {
                    $paymentid = session()->get('tran_ref');
                } else {
                    return redirect('admin/plan')->with('error', session()->get('messages.wrong'));
                }
            }

            if (session()->get('payment_type') == "14") {
                if ($request->status == "Completed") {
                    $paymentid = $request->transaction_id;
                } else {
                    return redirect('admin/plan')->with('error', trans('messages.wrong'));
                }
            }

            if (session()->get('payment_type') == "15") {
                $checkstatus = app('App\Http\Controllers\addons\XenditController')->checkpaymentstatus(session()->get('tran_ref'), '1');

                if ($checkstatus == "PAID") {
                    $paymentid = session()->get('payment_id');
                } else {
                    return redirect('admin/plan')->with('error', session()->get('messages.wrong'));
                }
            }

            if (session()->get('payment_type') == (string) Payment::TYPE_STRIPE) {
                $paymentmethod = Payment::where('vendor_id', 1)
                    ->where('payment_type', Payment::TYPE_STRIPE)
                    ->where('is_available', 1)
                    ->where('is_activate', 1)
                    ->first();

                if (empty($paymentmethod) || empty($paymentmethod->secret_key) || empty($request->stripe_plan_session_id)) {
                    return redirect('admin/plan')->with('error', trans('messages.unable_to_complete_payment'));
                }

                Stripe\Stripe::setApiKey($paymentmethod->secret_key);
                $checkoutSession = Stripe\Checkout\Session::retrieve($request->stripe_plan_session_id);

                if ($checkoutSession->payment_status !== 'paid') {
                    return redirect('admin/plan')->with('error', trans('messages.unable_to_complete_payment'));
                }

                $paymentid = $checkoutSession->payment_intent;
            }

            $plan = PricingPlan::where('id', session()->get('plan_id'))->first();
            $checkuser = User::find($vendor_id);
            $checkuser->plan_id = session()->get('plan_id');
            $checkuser->purchase_amount = session()->get('amount');
            $checkuser->purchase_date = date("Y-m-d h:i:sa");
            $checkuser->save();
            $transaction = new Transaction;
            $transaction->vendor_id = $vendor_id;
            $transaction->plan_name = $plan->name;
            // Plan taxes + any ACTIVE platform rule scoped to subscription invoices for this
            // system. With every rule inactive this returns empty arrays and nothing is charged.
            [$tax_amount, $tax_name] = Subscriptions::taxForPlan($plan);
            $transaction->tax = implode('|', $tax_amount);
            $transaction->tax_name = implode('|', $tax_name);
            $transaction->plan_id = session()->get('plan_id');
            $transaction->payment_type = session()->get('payment_type');
            $transaction->amount = $plan->price;
            if (session()->has('discount_data')) {
                $transaction->offer_code = session()->get('discount_data')['offer_code'];
                $transaction->offer_amount = session()->get('discount_data')['offer_amount'];
            }
            $transaction->grand_total =  session()->get('amount');
            $transaction->payment_id = @$paymentid;
            $transaction->service_limit = $plan->order_limit;
            $transaction->appoinment_limit = $plan->appointment_limit;
            $transaction->system = $plan->system;
            $transaction->currency = $plan->currency ?: 'USD';
            $transaction->plan_snapshot = Subscriptions::snapshot($plan, [
                'offer_code'   => session()->has('discount_data') ? session()->get('discount_data')['offer_code'] : null,
                'offer_amount' => session()->has('discount_data') ? session()->get('discount_data')['offer_amount'] : null,
            ]);
            $transaction->expire_date = $this->deferredExpiry($vendor_id, $plan);
            $transaction->duration = $plan->duration;
            $transaction->days = $plan->days;
            $transaction->custom_domain = $plan->custom_domain;
            $transaction->google_analytics = $plan->google_analytics;
            $transaction->vendor_app = $plan->vendor_app;
            $transaction->coupons = $plan->coupons;
            $transaction->blogs = $plan->blogs;
            $transaction->google_login = $plan->google_login;
            $transaction->facebook_login = $plan->facebook_login;
            $transaction->sound_notification = $plan->sound_notification;
            $transaction->whatsapp_message = $plan->whatsapp_message;
            $transaction->telegram_message = $plan->telegram_message;
            $transaction->pos = $plan->pos;
            $transaction->pwa = $plan->pwa;
            $transaction->tableqr = $plan->tableqr;
            $transaction->status = "2";
            $transaction->themes_id = $plan->themes_id;
            $transaction->role_management = $plan->role_management;
            $transaction->purchase_date = date("Y-m-d h:i:sa");
            $transaction->transaction_number = Str::upper(Str::random(8));
            if (\Illuminate\Support\Facades\Schema::hasColumn('transactions', 'addons')) {
                $sa = session()->get('selected_addons');
                $transaction->addons = $sa ? json_decode($sa, true) : null;
            }
            $transaction->save();
            Systems::markPaid($vendor_id, $plan->system);
            $emaildata = helper::emailconfigration(helper::appdata('')->id);
            Config::set('mail', $emaildata);

            helper::send_subscription_email(Auth::user()->email, Auth::user()->name, $plan->name, helper::get_plan_exp_date($plan->duration, $plan->days), helper::currency_formate($plan->price, ""), helper::getpayment(session()->get('payment_type'), 1)->payment_name, @$paymentid);
            session()->forget(['amount', 'plan_id', 'payment_type', 'currency', 'returnUrl', 'successurl', 'failureurl', 'invoicepay', 'discount_data', 'selected_addons']);

            // A merchant who just finished the registration wizard goes straight to the dashboard.
            // Straight from signup: go to Dashboard Setup, which collects the business details
            // and verification documents.
            if (session()->pull('oc_after_payment') === 'setup') {
                return redirect('admin/setup')->with('success', trans('messages.success'));
            }

            return redirect('admin/plan')->with('success', trans('messages.success'));
        } catch (\Throwable $th) {
            return redirect('admin/plan')->with('error', trans('messages.unable_to_complete_payment'));
        }
    }
    public function buyplan(Request $request)
    {
        // Guard the POST as well as the UI — COD/wallet must never settle a subscription.
        if ($request->filled('payment_type') && !Subscriptions::isAllowedMethod($request->payment_type)) {
            return $request->payment_type == '6'
                ? redirect()->back()->with('error', trans('messages.payment_method_not_allowed'))
                : response()->json(['status' => 0, 'message' => trans('messages.payment_method_not_allowed')], 200);
        }

        if (Auth::user()->type == 4) {
            $vendor_id = Auth::user()->vendor_id;
        } else {
            $vendor_id = Auth::user()->id;
        }
        try {
            // Remember the selected paid add-ons for this purchase (recorded on the transaction).
            session()->put('selected_addons', $request->selected_addons ?: '');
            $paymenttype = "";
            //payment_type = COD : 1,RazorPay : 2, Stripe : 3, Flutterwave : 4, Paystack : 5, Mercado Pago : 7, PayPal : 8, MyFatoorah : 9, toyyibpay : 10, phonepe : 11, paytab : 12, Mollie : 13, Khalti = 14, Xendit = 15
            $plan = PricingPlan::where('id', $request->plan_id)->first();
            if ((string) $request->payment_type === (string) Payment::TYPE_STRIPE) {
                $paymentmethod = Payment::where('vendor_id', 1)
                    ->where('payment_type', Payment::TYPE_STRIPE)
                    ->where('is_available', 1)
                    ->where('is_activate', 1)
                    ->first();

                if (empty($paymentmethod) || empty($paymentmethod->secret_key)) {
                    return response()->json(['status' => 0, 'message' => trans('messages.unable_to_complete_payment')], 200);
                }

                Stripe\Stripe::setApiKey($paymentmethod->secret_key);

                if (!empty($request->offer_code) && (float) $request->discount > 0) {
                    session()->put('discount_data', [
                        'offer_code' => $request->offer_code,
                        'offer_amount' => $request->discount,
                    ]);
                }

                session()->put('amount', $request->amount);
                session()->put('plan_id', $request->plan_id);
                session()->put('payment_type', (string) Payment::TYPE_STRIPE);

                $checkoutSession = Stripe\Checkout\Session::create([
                    'mode' => 'payment',
                    'customer_email' => Auth::user()->email,
                    'line_items' => [[
                        'price_data' => [
                            'currency' => strtolower($paymentmethod->currency),
                            'product_data' => [
                                'name' => $plan->name . ' Subscription',
                            ],
                            'unit_amount' => (int) round($request->amount * 100),
                        ],
                        'quantity' => 1,
                    ]],
                    'metadata' => [
                        'vendor_id' => (string) $vendor_id,
                        'plan_id' => (string) $request->plan_id,
                    ],
                    'success_url' => URL::to('admin/plan/buyplan/paymentsuccess/success') . '?stripe_plan_session_id={CHECKOUT_SESSION_ID}',
                    'cancel_url' => URL::to('admin/plan/selectplan-' . $request->plan_id),
                ]);

                return response()->json(['status' => 1, 'url' => $checkoutSession->url], 200);
            } else {
                $payment_id = $request->payment_id;
            }
            // Bank transfer, Benefit, Al Salam Bank QR, payment link and cash all settle offline:
            // the merchant uploads proof and an admin approves. Only type 6 used to be handled.
            // The receipt is optional, so $filename must exist either way — without this a
            // transfer submitted with no attachment crashed on "Undefined variable $filename".
            $filename = null;
            if (Subscriptions::isManual($request->payment_type)) {
                if ($request->hasFile('screenshot')) {
                    if (env('Environment') == 'sendbox') {
                        return $this->sendError("This operation was not performed due to demo mode");
                    }
                    $validator = Validator::make($request->all(), [
                        'screenshot' => 'image|max:' . helper::imagesize() . '|' . helper::imageext(),
                    ], [
                        'screenshot.max' => trans('messages.image_size_message'),
                    ]);

                    if ($validator->fails()) {
                        return redirect()->back()->withErrors($validator)->withInput();
                    } else {
                        $filename = 'screenshot-' . uniqid() . "." . $request->file('screenshot')->getClientOriginalExtension();
                        $request->file('screenshot')->move(env('ASSETSPATHURL') . 'admin-assets/images/screenshot/', $filename);
                    }
                }
                // Offline payment: stays pending until an admin approves it.
                $payment_id = "";
                $status = 1;
            } else {
                $status = 2;
            }

            $checkuser = User::find($vendor_id);
            $checkuser->plan_id = $request->plan_id;
            $checkuser->purchase_amount = $request->amount;
            $checkuser->purchase_date = date("Y-m-d h:i:sa");
            $checkuser->save();
            $transaction = new Transaction();
            if ($filename) {
                $transaction->screenshot = $filename;
            }
            // Plan taxes + any ACTIVE platform rule scoped to subscription invoices for this
            // system. With every rule inactive this returns empty arrays and nothing is charged.
            [$tax_amount, $tax_name] = Subscriptions::taxForPlan($plan);
            $transaction->tax = implode('|', $tax_amount);
            $transaction->tax_name = implode('|', $tax_name);
            $transaction->vendor_id = $vendor_id;
            $transaction->plan_name = $plan->name;
            $transaction->plan_id = $request->plan_id;
            $transaction->payment_type = $request->payment_type;
            $transaction->payment_id = $payment_id;
            $transaction->amount = $plan->price;
            $transaction->grand_total = $request->amount;
            if (\Illuminate\Support\Facades\Schema::hasColumn('transactions', 'addons')) {
                $transaction->addons = $request->selected_addons ? json_decode($request->selected_addons, true) : null;
            }
            $transaction->service_limit = $plan->order_limit;
            $transaction->appoinment_limit = $plan->appointment_limit;
            $transaction->status = $status;
            $transaction->purchase_date = date("Y-m-d h:i:sa");
            $transaction->system = $plan->system;
            $transaction->currency = $plan->currency ?: 'USD';
            $transaction->plan_snapshot = Subscriptions::snapshot($plan, [
                'offer_code'   => $request->payment_type == 6 ? $request->modal_offer_code : $request->offer_code,
                'offer_amount' => $request->payment_type == 6 ? $request->modal_discount : $request->discount,
            ]);
            $transaction->expire_date = $this->deferredExpiry($vendor_id, $plan);
            $transaction->duration = $plan->duration;
            $transaction->days = $plan->days;
            $transaction->themes_id = $plan->themes_id;
            $transaction->custom_domain = $plan->custom_domain;
            $transaction->google_analytics = $plan->google_analytics;
            $transaction->vendor_app = $plan->vendor_app;
            $transaction->coupons = $plan->coupons;
            $transaction->blogs = $plan->blogs;
            $transaction->google_login = $plan->google_login;
            $transaction->facebook_login = $plan->facebook_login;
            $transaction->sound_notification = $plan->sound_notification;
            $transaction->whatsapp_message = $plan->whatsapp_message;
            $transaction->telegram_message = $plan->telegram_message;
            $transaction->pos = $plan->pos;
            $transaction->pwa = $plan->pwa;
            $transaction->tableqr = $plan->tableqr;
            $transaction->role_management = $plan->role_management;
            if ($request->payment_type == 6) {
                $transaction->offer_code = $request->modal_offer_code;
                $transaction->offer_amount = $request->modal_discount;
            } else {
                $transaction->offer_code = $request->offer_code;
                $transaction->offer_amount = $request->discount;
            }
            $transaction->transaction_number = Str::upper(Str::random(8));
            $transaction->save();
            // Bank transfer / COD stay pending until the admin approves them, so only a settled
            // payment moves the account into setup mode.
            if ($status == 2) {
                Systems::markPaid($vendor_id, $plan->system);
            }
            $emaildata = helper::emailconfigration(helper::appdata('')->id);
            Config::set('mail', $emaildata);
            session()->forget('discount_data');
            if (Subscriptions::isManual($request->payment_type)) {
                helper::bank_transfer_request(Auth::user()->email, Auth::user()->name, $plan->name, helper::get_plan_exp_date($plan->duration, $plan->days), helper::currency_formate($plan->price, ""), helper::getpayment($request->payment_type, 1)->payment_name, @$payment_id);

                return redirect('admin/plan')->with('success', trans('messages.payment_receipt_pending_approval'));
            } else {

                helper::send_subscription_email(Auth::user()->email, Auth::user()->name, $plan->name, helper::get_plan_exp_date($plan->duration, $plan->days), helper::currency_formate($plan->price, ""), helper::getpayment($request->payment_type, 1)->payment_name, @$payment_id);
                return response()->json(['status' => 1, 'message' => trans('messages.success')], 200);
            }
        } catch (\Throwable $th) {
            // Swallowing this made an offline payment look like it silently did nothing.
            \Illuminate\Support\Facades\Log::error('buyplan failed: ' . $th->getMessage(), [
                'file' => $th->getFile(), 'line' => $th->getLine(), 'payment_type' => $request->payment_type,
            ]);
            if (Subscriptions::isManual($request->payment_type)) {
                return redirect()->back()->with('error', trans('messages.wrong'));
            } else {
                return response()->json(['status' => 0, 'message' => trans('messages.unable_to_complete_payment')], 200);
            }
        }
    }
    public function delete($id)
    {
        PricingPlan::where('id', $id)->delete();
        return redirect('admin/plan')->with('success', trans('messages.success'));
    }
    public function plan_details(Request $request)
    {
        $plan = Transaction::with('vendor_info', 'plan_info')->where('id', $request->id)->first();
        return view('admin.plan.plan_details', compact('plan'));
    }
    public function generatepdf(Request $request)
    {
        $plan = Transaction::where('id', $request->id)->first();
        $user = User::where('id', $plan->vendor_id)->first();
        $pdf = PDF::loadView('admin.plan.plandetailspdf', ['plan' => $plan, 'user' => $user]);
        return $pdf->download(trans('labels.transaction') . $plan->transaction_number . '.pdf');
    }

    public function reorder_plan(Request $request)
    {

        if ($request->has('ids')) {

            $arr = explode('|', $request->input('ids'));
            foreach ($arr as $sortOrder => $id) {
                $menu = PricingPlan::find($id);
                $menu->reorder_id = $sortOrder;
                $menu->save();
            }
        }
        return response()->json(['status' => 1, 'msg' => trans('messages.success')], 200);
    }
}
