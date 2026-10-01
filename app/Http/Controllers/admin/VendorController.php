<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\helper;
use App\Helpers\Systems;
use App\Helpers\Vendor360;
use App\Models\VendorAuditLog;
use App\Models\Areas;
use App\Models\User;
use App\Models\Settings;
use App\Models\Transaction;
use App\Models\StoreCategory;
use App\Models\PricingPlan;
use App\Models\City;
use App\Models\CustomDomain;
use App\Models\OtherSettings;
use App\Models\SystemAddons;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Config;
use Illuminate\Support\Facades\URL;
use Lunaweb\RecaptchaV3\Facades\RecaptchaV3;

class VendorController extends Controller
{
    /**
     * Vendors list — step 2 of the Vendor 360 rollout: system tabs, global search, filters and
     * summary data on the existing card. The card layout itself is unchanged.
     */
    public function index(Request $request)
    {
        $query = User::where('type', 2)->where('is_deleted', 2);

        // ---- System tab (All / Orders & Stores / Booking / Service Marketplace) ----
        $tab = $request->get('tab', 'all');
        if (Systems::isValid($tab)) {
            $query->where(function ($q) use ($tab) {
                $q->where('system', $tab);
                // Vendors created before the system column existed count as Orders & Stores.
                if ($tab === Systems::ORDERS) {
                    $q->orWhereNull('system')->orWhere('system', '');
                }
            });
        }

        // ---- Global search across every identifier the client listed ----
        if ($search = trim((string) $request->get('q'))) {
            $like = '%' . $search . '%';
            $vendorIdsByWhatsapp = \App\Models\VendorWhatsappNumber::where('number', 'like', $like)->pluck('vendor_id');
            $vendorIdsBySettings = Settings::where('whatsapp_number', 'like', $like)
                ->orWhere('contact', 'like', $like)
                ->orWhere('website_title', 'like', $like)
                ->pluck('vendor_id');

            $query->where(function ($q) use ($like, $search, $vendorIdsByWhatsapp, $vendorIdsBySettings) {
                $q->where('name', 'like', $like)
                    ->orWhere('trade_name', 'like', $like)
                    ->orWhere('legal_name', 'like', $like)
                    ->orWhere('owner_name', 'like', $like)
                    ->orWhere('vendor_code', 'like', $like)
                    ->orWhere('cr_number', 'like', $like)
                    ->orWhere('email', 'like', $like)
                    ->orWhere('mobile', 'like', $like)
                    ->orWhere('slug', 'like', $like)
                    ->orWhere('custom_domain', 'like', $like)
                    ->orWhere('id', $search)
                    ->orWhereIn('id', $vendorIdsByWhatsapp)
                    ->orWhereIn('id', $vendorIdsBySettings);
            });
        }

        // ---- Column filters ----
        if ($request->filled('plan'))                $query->where('plan_id', $request->plan);
        if ($request->filled('verification'))        $query->where('verification_status', $request->verification);
        if ($request->filled('subscription'))        $query->where('subscription_status', $request->subscription);
        if ($request->filled('page_status'))         $query->where('public_page_status', $request->page_status);
        if ($request->filled('country'))             $query->where('country', $request->country);
        if ($request->filled('city'))                $query->where('city_name', $request->city);
        if ($request->filled('legal_status'))        $query->where('legal_status', $request->legal_status);
        if ($request->filled('activity'))            $query->where('activity_id', $request->activity);
        if ($request->filled('specialization'))      $query->where('specialization_id', $request->specialization);

        // Account status is derived, so it filters on the columns it is derived from.
        switch ($request->get('account_status')) {
            case 'archived':   $query->whereNotNull('archived_at'); break;
            case 'restricted': $query->whereNull('archived_at')->where('account_status', Systems::RESTRICTED); break;
            case 'suspended':  $query->whereNull('archived_at')->where('is_available', 2); break;
            case 'active':     $query->whereNull('archived_at')->where('is_available', 1)->where('account_status', '!=', Systems::RESTRICTED); break;
        }

        // Sandbox/test accounts are hidden from the production list unless asked for.
        $env = $request->get('env', 'production');
        if ($env === 'production')   $query->where('is_sandbox', 2);
        elseif ($env === 'sandbox')  $query->where('is_sandbox', 1);

        // Archived vendors stay out of the default list; the Archived filter brings them back.
        if ($request->get('account_status') !== 'archived') {
            $query->whereNull('archived_at');
        }

        $getuserslist = $query->orderByDesc('id')->get();

        // Per-card summary + the one alert that needs attention.
        $summaries = [];
        $alerts = [];
        foreach ($getuserslist as $vendor) {
            $summaries[$vendor->id] = Vendor360::summary($vendor);
            $alerts[$vendor->id]    = Vendor360::alert($vendor, $summaries[$vendor->id]);
        }

        // A queue click filters the already-computed alerts, so the counts and the list always agree.
        if ($queue = $request->get('queue')) {
            $getuserslist = $getuserslist->filter(fn($v) => ($alerts[$v->id]['key'] ?? '') === $queue)->values();
        }

        // Counts for the tab pills and the quick-review chips.
        $tabCounts = [];
        foreach (['all' => null] + array_fill_keys(Systems::keys(), null) as $key => $_) {
            $c = User::where('type', 2)->where('is_deleted', 2)->whereNull('archived_at')->where('is_sandbox', 2);
            if ($key !== 'all') {
                $c->where(function ($q) use ($key) {
                    $q->where('system', $key);
                    if ($key === Systems::ORDERS) $q->orWhereNull('system')->orWhere('system', '');
                });
            }
            $tabCounts[$key] = $c->count();
        }

        $filters = [
            'plans'       => PricingPlan::orderBy('reorder_id')->get(),
            'activities'  => \App\Models\Activity::orderBy('reorder_id')->get(),
            'countries'   => User::where('type', 2)->whereNotNull('country')->distinct()->orderBy('country')->pluck('country'),
            'cities'      => User::where('type', 2)->whereNotNull('city_name')->distinct()->orderBy('city_name')->pluck('city_name'),
        ];

        return view('admin.user.index', compact('getuserslist', 'summaries', 'alerts', 'tab', 'tabCounts', 'filters', 'env'));
    }
    public function add(Request $request)
    {
        $cities = City::where('Is_deleted', 2)->where('is_available', 1)->orderBy('reorder_id')->get();
        $stores = StoreCategory::where('is_deleted', 2)->where('is_available', 1)->orderBy('reorder_id')->get();
        return view('admin.user.add', compact('cities', 'stores'));
    }
    public function edit($id)
    {
        $getuserdata = User::where('id', $id)->first();
        $getplanlist = PricingPlan::where('is_available', 1)->get();
        $cities = City::where('Is_deleted', 2)->where('is_available', 1)->orderBy('reorder_id')->get();
        $stores = StoreCategory::where('is_deleted', 2)->where('is_available', 1)->orderBy('reorder_id')->get();
        return view('admin.user.edit', compact('getuserdata', 'getplanlist', 'cities', 'stores'));
    }
    public function update(Request $request)
    {
        if (Auth::user()->type == 4) {
            $vendor_id = Auth::user()->vendor_id;
        } else {
            $vendor_id = Auth::user()->id;
        }
        $edituser = User::where('id', $request->id)->first();
        $validatoremail = Validator::make(['email' => $request->email], [
            'email' => [
                Rule::unique('users')->whereIn('type', [1, 2])->where('is_deleted', 2)->ignore($edituser->id),
            ]
        ]);
        if ($validatoremail->fails()) {
            return redirect()->back()->with('error', trans('messages.unique_email'));
        }
        $validatormobile = Validator::make(['mobile' => $request->mobile], [
            'mobile' => [
                Rule::unique('users')->whereIn('type', [1, 2])->where('is_deleted', 2)->ignore($edituser->id),
            ]
        ]);
        if ($validatormobile->fails()) {
            return redirect()->back()->with('error', trans('messages.unique_mobile'));
        }
        $validatorslug = Validator::make(['slug' => $request->slug], [
            'slug' => [
                Rule::unique('users')->where('type', 2)->where('is_deleted', 2)->ignore($edituser->id),
            ]
        ]);
        if ($validatorslug->fails()) {
            return redirect()->back()->with('error', trans('messages.unique_slug'));
        }
        $edituser->name = $request->name;
        $edituser->email = $request->email;
        $edituser->mobile = $request->mobile;
        $edituser->city_id = $request->city;
        $edituser->area_id = $request->area;
        if ($request->store != null && $request->store != "") {
            $edituser->store_id = $request->store;
        }
        if ($request->has('profile')) {
            if (env('Environment') == 'sendbox') {
                return $this->sendError("This operation was not performed due to demo mode");
            }
            $validator = Validator::make($request->all(), [
                'profile' => 'image|max:' . helper::imagesize() . '|' . helper::imageext(),
            ], [
                'profile.max' => trans('messages.image_size_message'),
            ]);
            if ($validator->fails()) {
                return redirect()->back()->with('error', trans('messages.image_size_message') . ' ' . helper::appdata('')->image_size . ' ' . 'MB');
            }

            if ($edituser->image != "default.png" && file_exists(storage_path('app/public/admin-assets/images/profile/' . $edituser->image))) {
                unlink(storage_path('app/public/admin-assets/images/profile/' . $edituser->image));
            }
            $edit_image = $request->file('profile');
            $profileImage = 'profile-' . uniqid() . "." . $edit_image->getClientOriginalExtension();
            $edit_image->move(storage_path('app/public/admin-assets/images/profile/'), $profileImage);
            $edituser->image = $profileImage;
        }
        if (!isset($request->allow_store_subscription)) {
            if ($request->plan != null && !empty($request->plan)) {
                $plan = PricingPlan::where('id', $request->plan)->first();
                $edituser->plan_id = $plan->id;
                $edituser->purchase_amount = $plan->price;
                $edituser->purchase_date = date("Y-m-d h:i:sa");

                $transaction = new Transaction();
                $transaction->vendor_id = $edituser->id;
                $transaction->plan_name = $plan->name;
                $transaction->plan_id = $plan->id;
                $transaction->payment_type = "";
                $transaction->payment_id = "";
                $transaction->amount = $plan->price;
                $transaction->service_limit = $plan->order_limit;
                $transaction->appoinment_limit = $plan->appointment_limit;
                $transaction->expire_date = helper::get_plan_exp_date($plan->duration, $plan->days);
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
                $transaction->role_management = $plan->role_management;
                $transaction->save();
                if ($plan->custom_domain == "2") {
                    Settings::where('vendor_id', $vendor_id)->update(['custom_domain' => "-"]);
                }
                if ($plan->custom_domain == "1") {
                    $checkdomain = CustomDomain::where('vendor_id', $vendor_id)->first();
                    if (@$checkdomain->status == 2) {
                        Settings::where('vendor_id', $vendor_id)->update(['custom_domain' => $checkdomain->current_domain]);
                    }
                }
            }
        }
        if (Str::contains(request()->url(), 'user')) {
            if (isset($request->allow_store_subscription)) {
                $edituser->plan_id = "";
                $edituser->purchase_amount = "";
                $edituser->purchase_date = "";
            }
            $edituser->allow_without_subscription = isset($request->allow_store_subscription) ? 1 : 2;
            $edituser->available_on_landing = isset($request->show_landing_page) ? 1 : 2;
        }
        if (helper::checkcustomdomain($edituser->id) == null) {
            $edituser->slug = $request->slug;
        }
        $edituser->update();
        if ($request->has('updateprofile') && $request->updateprofile == 1) {
            return redirect('admin/settings')->with('success', trans('messages.success'));
        } else {
            return redirect('admin/users')->with('success', trans('messages.success'));
        }
    }
    public function status(Request $request)
    {
        User::where('slug', $request->slug)->update(['is_available' => $request->status]);
        return redirect('admin/users')->with('success', trans('messages.success'));
    }
    /**
     * Log in as a vendor. The admin must give a reason; the impersonation is written to the audit
     * log and the session carries the banner data shown while it is active.
     */
    public function vendor_login(Request $request)
    {
        $user = User::where('id', $request->id)->first();
        if (empty($user)) {
            return redirect()->back()->with('error', 'Vendor account was created incompletely. Please contact admin or try again.');
        }

        $reason = trim((string) $request->get('reason'));
        if ($reason === '') {
            return redirect()->back()->with('error', trans('messages.impersonation_reason_required'));
        }

        $admin = Auth::user();
        VendorAuditLog::record($user->id, 'admin_impersonation', null, 'login', $reason, 'admin');

        session()->put('vendor_login', $admin->id);
        session()->put('vendor_login_meta', [
            'admin_name' => $admin->name,
            'vendor'     => $user->trade_name ?: $user->name,
            'reason'     => $reason,
            'started_at' => now()->toDateTimeString(),
        ]);

        Auth::login($user);

        return redirect('admin/dashboard');
    }

    public function admin_back(Request $request)
    {
        $vendorId = Auth::id();
        $getuser = User::where('id', session()->get('vendor_login'))->first();
        $meta = session()->get('vendor_login_meta', []);

        Auth::login($getuser);
        session()->forget(['vendor_login', 'vendor_login_meta']);

        VendorAuditLog::record($vendorId, 'admin_impersonation', 'login', 'logout', @$meta['reason'], 'admin');

        return redirect('admin/users');
    }

    /**
     * Archive a vendor. Reversible: the account, its setup and its data all stay. Permanent
     * deletion is a separate action behind a second confirmation.
     */
    public function archive(Request $request)
    {
        $user = User::where('id', $request->id)->where('type', 2)->first();
        if (empty($user)) {
            abort(404);
        }

        VendorAuditLog::record($user->id, 'account_status', 'active', 'archived', $request->get('reason'), 'admin');

        $user->archived_at = now();
        $user->archived_by = Auth::id();
        $user->save();

        return redirect()->back()->with('success', trans('messages.vendor_archived'));
    }

    public function restore(Request $request)
    {
        $user = User::where('id', $request->id)->where('type', 2)->first();
        if (empty($user)) {
            abort(404);
        }

        VendorAuditLog::record($user->id, 'account_status', 'archived', 'active', $request->get('reason'), 'admin');

        $user->archived_at = null;
        $user->archived_by = null;
        $user->save();

        return redirect()->back()->with('success', trans('messages.vendor_restored'));
    }

    /** Mark an account as sandbox/test so it drops out of production totals, or back again. */
    public function sandbox(Request $request)
    {
        $user = User::where('id', $request->id)->where('type', 2)->first();
        if (empty($user)) {
            abort(404);
        }

        $new = (int) $user->is_sandbox === 1 ? 2 : 1;
        VendorAuditLog::record($user->id, 'is_sandbox', $user->is_sandbox, $new, null, 'admin');
        $user->is_sandbox = $new;
        $user->save();

        return redirect()->back()->with('success', trans('messages.success'));
    }
    // ------------------------------------------------------------------------
    // ----------------- registration & Auth pages ----------------------------
    // ------------------------------------------------------------------------
    public function register()
    {
        Helper::language(1);
        $cities = City::where('Is_deleted', 2)->where('is_available', 1)->orderBy('reorder_id')->get();
        return view('admin.auth.register', compact('cities'));
    }

    /**
     * Live availability check for the registration form. Called as the merchant types, so a
     * duplicate email / mobile / store link is caught on the spot instead of after they have
     * filled in every remaining step.
     */
    public function check_availability(Request $request)
    {
        $field = $request->get('field');
        $value = trim((string) $request->get('value'));

        if ($value === '' || !in_array($field, ['email', 'mobile', 'slug'], true)) {
            return response()->json(['status' => 1], 200);
        }

        $query = User::where('is_deleted', 2);
        if ($field === 'slug') {
            $query->where('type', 2)->where('slug', $value);
        } else {
            $query->whereIn('type', [1, 2])->where($field, $value);
        }

        $taken = $query->exists();
        $messages = [
            'email'  => trans('messages.unique_email'),
            'mobile' => trans('messages.unique_mobile'),
            'slug'   => trans('messages.unique_slug'),
        ];

        return response()->json([
            'status'  => $taken ? 0 : 1,
            'message' => $taken ? $messages[$field] : '',
        ], 200);
    }

    public function register_vendor(Request $request)
    {
        // V2: the System is chosen before the plan is paid for, and is locked onto the account
        // afterwards. Everything downstream (plans, activities, themes) is filtered by it.
        if (!Systems::isValid($request->system)) {
            return redirect()->back()->withInput()->with('error', 'Please choose a system (Orders & Stores, Booking or Service Marketplace).');
        }
        // Check email, mobile and store link together and keep the merchant's input, so a
        // duplicate on the last step never wipes the whole form. `focus` tells the page which
        // step to reopen and which field to highlight.
        $errors = [];
        $focus = null;

        $emailTaken = Validator::make(['email' => $request->email], [
            'email' => ['required', 'email', Rule::unique('users')->whereIn('type', [1, 2])->where('is_deleted', 2)],
        ])->fails();
        if ($emailTaken) {
            $errors['email'] = trans('messages.unique_email');
            $focus = $focus ?: 'email';
        }

        $mobileTaken = Validator::make(['mobile' => $request->mobile], [
            'mobile' => ['required', 'numeric', Rule::unique('users')->whereIn('type', [1, 2])->where('is_deleted', 2)],
        ])->fails();
        if ($mobileTaken) {
            $errors['mobile'] = trans('messages.unique_mobile');
            $focus = $focus ?: 'mobile';
        }

        $slugTaken = Validator::make(['slug' => $request->slug], [
            'slug' => ['required', Rule::unique('users')->where('type', 2)->where('is_deleted', 2)],
        ])->fails();
        if ($slugTaken) {
            $errors['slug'] = trans('messages.unique_slug');
            $focus = $focus ?: 'slug';
        }

        if (!empty($errors)) {
            return redirect()->back()
                ->withInput()
                ->withErrors($errors)
                ->with('focus_field', $focus)
                ->with('error', implode(' ', $errors));
        }

        if (session()->has('social_login')) {
            if (session()->get('social_login')['google_id'] != "") {
                $email = session()->get('social_login')['email'];
            }
            if (session()->get('social_login')['facebook_id'] != "") {
                $email = session()->get('social_login')['email'];
            }
        } else {

            $email = $request->email;
            $password = Hash::make($request->password);
        }
        if (
            SystemAddons::where('unique_identifier', 'google_recaptcha')->first() != null &&
            SystemAddons::where('unique_identifier', 'google_recaptcha')->first()->activated == 1
        ) {
            if (@Auth::user() && @Auth::user()->type != 1) {
                if (helper::appdata('')->recaptcha_version == 'v2') {
                    $request->validate([
                        'g-recaptcha-response' => 'required'
                    ], [
                        'g-recaptcha-response.required' => 'The g-recaptcha-response field is required.'
                    ]);
                }

                if (helper::appdata('')->recaptcha_version == 'v3') {
                    $score = RecaptchaV3::verify($request->get('g-recaptcha-response'), 'contact');
                    if ($score <= helper::appdata('')->score_threshold) {
                        return redirect()->back()->withInput()->with('error', 'You are most likely a bot');
                    }
                }
            }
        }

        // City/area are now free-text (stored in city_name/area_name below), so don't pass them as FK ids.
        $data = helper::vendor_register($request->name, $email, $request->mobile, $password, '', $request->slug, '', '', null, null, $request->store);
        $recoveredUser = null;
        if (empty($data)) {
            $recoveredUser = User::where('email', $email)->where('type', 2)->orderByDesc('id')->first();
            if (empty($recoveredUser)) {
                return redirect()->back()->withInput()->with('error', 'Vendor registration could not be completed. Please try again.');
            }
            $data = $recoveredUser->id;
        }
        $newuser = User::select('id', 'name', 'email', 'mobile', 'image')->where('id', $data)->first();
        if (empty($newuser)) {
            $recoveredUser = User::where('email', $email)->where('type', 2)->orderByDesc('id')->first();
            if (empty($recoveredUser)) {
                return redirect()->back()->withInput()->with('error', 'Vendor account was not created correctly. Please try again.');
            }
            $newuser = User::select('id', 'name', 'email', 'mobile', 'image')->where('id', $recoveredUser->id)->first();
        }

        $vendorId = $newuser->id;

        // V2 onboarding: store country, phone country code, GPS location and the lifecycle stamps.
        User::where('id', $vendorId)->update([
            'system' => Systems::normalise($request->system),
            'account_status' => Systems::PENDING_PAYMENT,
            'setup_completed' => 2,
            'account_created_date' => now(),
            'country' => $request->country ?: null,
            'country_code' => $request->country_code ?: null,
            'city_name' => trim((string) $request->city) ?: null,
            'area_name' => trim((string) $request->area) ?: null,
            'latitude' => is_numeric($request->latitude) ? $request->latitude : null,
            'longitude' => is_numeric($request->longitude) ? $request->longitude : null,
        ]);

        $adminSettings = Settings::where('vendor_id', 1)->first();
        if (!Settings::where('vendor_id', $vendorId)->exists() && !empty($adminSettings)) {
            $vendorSettings = $adminSettings->replicate();
            $vendorSettings->vendor_id = $vendorId;
            $vendorSettings->custom_domain = null;
            // V2 onboarding: honour the template the merchant picked, else fall back to the admin default.
            // 1 classic · 2 booking · 3 restaurant · 4 café · 5 grocery · 6 retail
            $allowedTemplates = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10'];
            $vendorSettings->template = in_array((string) $request->template, $allowedTemplates, true)
                ? (int) $request->template
                : (!empty($adminSettings->template) ? $adminSettings->template : 1);
            // V2 onboarding: remember the merchant's business type — but only one that actually
            // belongs to the System they chose, since the two are picked on different steps.
            $allowedTypes = Systems::businessTypes($request->system);
            $vendorSettings->business_type = in_array($request->business_type, $allowedTypes, true)
                ? $request->business_type
                : $allowedTypes[0];
            $vendorSettings->save();
        }

        $adminOtherSettings = OtherSettings::where('vendor_id', 1)->first();
        if (!OtherSettings::where('vendor_id', $vendorId)->exists() && !empty($adminOtherSettings)) {
            $vendorOtherSettings = $adminOtherSettings->replicate();
            $vendorOtherSettings->vendor_id = $vendorId;
            $vendorOtherSettings->save();
        }

        if (!\App\Models\Payment::where('vendor_id', $vendorId)->exists()) {
            $adminPayments = \App\Models\Payment::where('vendor_id', 1)->get();
            foreach ($adminPayments as $adminPayment) {
                $vendorPayment = $adminPayment->replicate();
                $vendorPayment->vendor_id = $vendorId;
                if ((int) $vendorPayment->payment_type === \App\Models\Payment::TYPE_STRIPE) {
                    $vendorPayment->public_key = '';
                    $vendorPayment->secret_key = '';
                    $vendorPayment->is_available = 2;
                    $vendorPayment->is_activate = 1;
                }
                $vendorPayment->save();
            }
        }

        if (!\App\Models\Timing::where('vendor_id', $vendorId)->exists()) {
            $adminTimings = \App\Models\Timing::where('vendor_id', 1)->get();
            foreach ($adminTimings as $adminTiming) {
                $vendorTiming = $adminTiming->replicate();
                $vendorTiming->vendor_id = $vendorId;
                $vendorTiming->save();
            }
        }

        if (!\App\Models\CustomStatus::where('vendor_id', $vendorId)->exists()) {
            $adminStatuses = \App\Models\CustomStatus::where('vendor_id', 1)->get();
            foreach ($adminStatuses as $adminStatus) {
                $vendorStatus = $adminStatus->replicate();
                $vendorStatus->vendor_id = $vendorId;
                $vendorStatus->save();
            }
        }

        if (@Auth::user() && @Auth::user()->type == 1) {
            return redirect('admin/users')->with('success', trans('messages.success'));
        } else {
            Auth::login($newuser);
            // V2: pay first. The dashboard unlocks once the plan for the chosen system is paid.
            return redirect('admin/plan')->with('success', trans('messages.success'));
        }
    }
    public function forgot_password()
    {
        Helper::language(1);
        return view('admin.auth.forgotpassword');
    }
    /** Accounts that sign in on the admin login page: super admin, vendors and their staff. */
    private function resettableUser(?string $email)
    {
        return User::where('email', trim((string) $email))->whereIn('type', [1, 2, 4])
            ->where('is_deleted', 2)->where('is_available', 1)->first();
    }

    /**
     * Email a password-reset link. Tokens come from Laravel's password broker (hashed in
     * password_reset_tokens, 60 min expiry, 60 s resend throttle — config/auth.php).
     * The reply is the same whether or not the email has an account, so the form cannot be
     * used to discover which emails are registered.
     */
    public function send_password(Request $request)
    {
        $validator = Validator::make($request->all(), ['email' => 'required|email']);
        if ($validator->fails()) {
            return redirect('admin/forgot_password')->withErrors($validator)->withInput();
        }

        $user = $this->resettableUser($request->email);
        if ($user) {
            $tokens = \Illuminate\Support\Facades\Password::broker()->getRepository();
            if ($tokens->recentlyCreatedToken($user)) {
                return redirect('admin/forgot_password')->withInput()->with('error', trans('messages.reset_link_throttled'));
            }
            $token = $tokens->create($user);
            $sent = helper::send_account_email('resetpassword', $user->email, trans('labels.reset_email_subject'), [
                'name'    => $user->name,
                'url'     => URL::to('admin/reset-password/' . $token) . '?email=' . urlencode($user->email),
                'minutes' => config('auth.passwords.users.expire', 60),
            ]);
            if (!$sent) {
                $tokens->delete($user);

                return redirect('admin/forgot_password')->withInput()->with('error', trans('messages.email_send_failed'));
            }
        }

        return redirect('admin/forgot_password')->with('reset_link_sent', trim($request->email));
    }

    /** The page the emailed link opens: email pre-filled, new password + confirmation. */
    public function reset_password_form(Request $request, $token)
    {
        Helper::language(1);
        $email = (string) $request->query('email');
        $user = $this->resettableUser($email);
        $valid = $user && \Illuminate\Support\Facades\Password::broker()->getRepository()->exists($user, $token);

        return view('admin.auth.resetpassword', compact('token', 'email', 'valid'));
    }

    public function reset_password(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => 'required|min:6|confirmed',
        ], [
            'password.min'       => trans('messages.password_min'),
            'password.confirmed' => trans('messages.new_confirm_password_inccorect'),
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $user = $this->resettableUser($request->email);
        $tokens = \Illuminate\Support\Facades\Password::broker()->getRepository();
        if (!$user || !$tokens->exists($user, $request->token)) {
            return redirect('admin/forgot_password')->with('error', trans('messages.reset_link_invalid'));
        }

        $user->password = Hash::make($request->password);
        $user->save();
        $tokens->delete($user); // one-time link

        helper::send_account_email('passwordchanged', $user->email, trans('labels.changed_email_subject'), [
            'name'     => $user->name,
            'when'     => now()->format('Y-m-d H:i'),
            'loginUrl' => URL::to('admin'),
        ]);

        return redirect('admin')->with('success', trans('messages.password_reset_done'));
    }
    public function change_password(Request $request)
    {
        if (Auth::user()->type == 4) {
            $vendor_id = Auth::user()->vendor_id;
        } else {
            $vendor_id = Auth::user()->id;
        }

        if (Hash::check($request->current_password, Auth::user()->password)) {
            if ($request->current_password == $request->new_password) {
                return redirect()->back()->with('error', trans('messages.new_old_password_diffrent'));
            } else {
                if ($request->new_password == $request->confirm_password) {
                    $changepassword = User::where('id', $vendor_id)->first();
                    $changepassword->password = Hash::make($request->new_password);
                    $changepassword->update();

                    // Notify the owner — never email the password itself.
                    helper::send_account_email('passwordchanged', $changepassword->email, trans('labels.changed_email_subject'), [
                        'name'     => $changepassword->name,
                        'when'     => now()->format('Y-m-d H:i'),
                        'loginUrl' => URL::to('admin'),
                    ]);


                    return redirect()->back()->with('success', trans('messages.success'));
                } else {
                    return redirect()->back()->with('error', trans('messages.new_confirm_password_inccorect'));
                }
            }
        } else {
            return redirect()->back()->with('error', trans('messages.old_password_incorect'));
        }
    }

    public function is_allow(Request $request)
    {
        $status = User::where('id', $request->id)->update(['allow_without_subscription' => $request->status]);
        if ($status) {
            return 1;
        } else {
            return 0;
        }
    }

    public function getarea(Request $request)
    {
        try {
            $data['area'] = Areas::select("id", "area")->where('city_id', $request->city)->where('is_available', 1)->where('is_deleted', 2)->orderBy('reorder_id')->get();
            return response()->json($data);
        } catch (\Throwable $th) {
            return response()->json(['status' => 0, 'message' => trans('messages.wrong')], 200);
        }
    }
    public function deletevendor(Request $request)
    {
        $user = User::where('id', $request->id)->first();
        $user->is_deleted = 1;
        $user->slug = '';
        $user->update();
        $emaildata = helper::emailconfigration(helper::appdata("")->id);
        Config::set('mail', $emaildata);
        helper::send_mail_delete_account($user);
        return redirect('admin/users')->with('success', trans('messages.success'));
    }
}
