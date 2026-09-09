<?php

namespace App\Http\Controllers\addons\included;

use App\Helpers\helper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Promocode;
use App\Models\Transaction;
use App\Models\Order;
use App\Models\PricingPlan;
use App\Helpers\Systems;
use Illuminate\Support\Facades\Auth;

class PromocodeController extends Controller
{
    public function index()
    {
        if (Auth::user()->type == 4) {
            $vendor_id = Auth::user()->vendor_id;
        } else {
            $vendor_id = Auth::user()->id;
        }
        $getpromocodeslist = Promocode::where('vendor_id', $vendor_id)->orderBy('reorder_id')->get();
        return view('admin.included.promocode.index', compact('getpromocodeslist'));
    }

    public function add()
    {
        return view('admin.included.promocode.add', ['plans' => $this->planOptions()]);
    }

    /** Plans a subscription coupon can be limited to, grouped so the picker can filter by system. */
    private function planOptions()
    {
        return PricingPlan::orderBy('reorder_id')
            ->get(['id', 'name', 'system', 'price', 'currency']);
    }

    /**
     * Persist the coupon-scope fields. Only the super admin's coupons carry a system/plan scope —
     * a vendor's own storefront coupon has neither, so those fields are left untouched for them.
     */
    private function applyScope(Promocode $promocode, Request $request): void
    {
        $isPlatform = Auth::user()->type == 1 || (Auth::user()->type == 4 && Auth::user()->vendor_id == 1);
        if (!$isPlatform) {
            return;
        }

        $system = $request->applicable_system;
        $promocode->applicable_system = ($system === 'all' || Systems::isValid($system)) ? $system : 'all';

        // Keep only plans that really belong to the chosen system.
        $planIds = array_filter((array) $request->applicable_plans);
        if (!empty($planIds) && $promocode->applicable_system !== 'all') {
            $planIds = PricingPlan::whereIn('id', $planIds)
                ->where('system', $promocode->applicable_system)
                ->pluck('id')->map(fn($i) => (string) $i)->all();
        }
        $promocode->applicable_plans = empty($planIds) ? null : implode('|', $planIds);

        $promocode->max_total_uses = is_numeric($request->max_total_uses) && (int) $request->max_total_uses > 0
            ? (int) $request->max_total_uses
            : null; // blank = unlimited

        $promocode->is_available = (int) $request->is_available === 1 ? 1 : 2;
    }

    public function save(Request $request)
    {
        if (Auth::user()->type == 4) {
            $vendor_id = Auth::user()->vendor_id;
        } else {
            $vendor_id = Auth::user()->id;
        }
        $promocode = new Promocode();
        $promocode->vendor_id = $vendor_id;
        $promocode->offer_name = $request->offer_name;
        $promocode->offer_type = $request->offer_type;
        $promocode->usage_type = $request->usage_type;
        if ($request->usage_type == 1) {
            $promocode->usage_limit = $request->usage_limit;
        }
        if ($request->usage_type == 2) {
            $promocode->usage_limit = 0;
        }
        $promocode->offer_code = $request->offer_code;
        $promocode->start_date = $request->start_date;
        $promocode->exp_date = $request->end_date;
        $promocode->offer_amount = $request->amount;
        $promocode->min_amount = $request->order_amount;
        $promocode->description = $request->description;
        $this->applyScope($promocode, $request);
        $promocode->save();

        return redirect('admin/coupons')->with('success', trans(('messages.success')));
    }

    public function edit($id)
    {
        $promocode = Promocode::where('id', $id)->first();
        $plans = $this->planOptions();
        return view('admin.included.promocode.edit', compact('promocode', 'plans'));
    }

    public function update(Request $request, $id)
    {
        $editpromocode = Promocode::where('id', $id)->first();
        $editpromocode->offer_name = $request->offer_name;
        $editpromocode->offer_type = $request->offer_type;
        $editpromocode->usage_type = $request->usage_type;
        $editpromocode->usage_limit = $request->usage_limit;
        $editpromocode->offer_code = $request->offer_code;
        $editpromocode->start_date = $request->start_date;
        $editpromocode->exp_date = $request->end_date;
        $editpromocode->offer_amount = $request->amount;
        $editpromocode->min_amount = $request->order_amount;
        $editpromocode->description = $request->description;
        $this->applyScope($editpromocode, $request);
        $editpromocode->update();

        return redirect('admin/coupons')->with('success', trans(('messages.success')));
    }

    public function status($id, $status)
    {

        Promocode::where('id', $id)->update(['is_available' => $status]);

        return redirect('admin/coupons')->with('success', trans('messages.success'));
    }

    public function delete($id)
    {
        try {
            Promocode::where('id', $id)->delete();

            return redirect('admin/coupons')->with('success', trans('messages.success'));
        } catch (\Exception $th) {
            dd($th);
            return redirect()->back()->with('error', trans('messages.wrong'));
        }
    }
    /**
     * Apply a coupon to a subscription purchase.
     *
     * All the rules live in Promocode::validateForPlan() so the checkout and the API cannot drift
     * apart: status, date window, applicable system, applicable plan, minimum amount, per-customer
     * usage and the total redemption cap.
     *
     * Only ONE coupon may be applied per payment — the session holds a single discount, and
     * applying a new code replaces the previous one rather than stacking.
     */
    public function vendorapplypromocode(Request $request)
    {
        if ($request->promocode == "") {
            return response()->json(["status" => 0, "message" => trans('messages.promocode_required')], 200);
        }
        date_default_timezone_set(helper::appdata('')->timezone);

        $vendor_id = Auth::user()->type == 4 ? Auth::user()->vendor_id : Auth::user()->id;

        $coupon = Promocode::where('offer_code', $request->promocode)
            ->where('vendor_id', 1)
            ->first();

        if (empty($coupon)) {
            return response()->json(['status' => 0, 'message' => trans('messages.invalid_promocode')], 200);
        }

        $plan = $request->plan_id ? PricingPlan::find($request->plan_id) : null;
        $subtotal = (float) $request->sub_total;

        if ($error = $coupon->validateForPlan($plan, $subtotal, $vendor_id)) {
            return response()->json(['status' => 0, 'message' => $error], 200);
        }

        session()->put('discount_data', [
            'offer_code'   => $coupon->offer_code,
            'offer_amount' => $coupon->discountOn($subtotal),
            'offer_type'   => $coupon->usage_type,
            'plan_id'      => $plan->id ?? null,
        ]);

        return response()->json(['status' => 1, 'message' => trans('messages.success')], 200);
    }

    public function removepromocode()
    {
        if (session()->has('discount_data')) {
            session()->forget('discount_data');
            return response()->json(['status' => 1, 'message' => trans('messages.success')], 200);
        }
        abort(404);
    }

    // api----------------------------------
    public function promocode(Request $request)
    {
        if ($request->vendor_id == "") {
            return response()->json(["status" => 0, "message" => trans('messages.vendor_id_required')], 200);
        }
        $date = date('Y-m-d');
        $promocodelist = Promocode::where("vendor_id", $request->vendor_id)->where("start_date", '<=', $date)->where("exp_date", '>=', $date)->where('is_available', '1')->get();
        return response()->json(['status' => 1, 'message' => trans('messages.success'), 'promocodes' => $promocodelist], 200);
    }
    public function applypromocode(Request $request)
    {

        $user_id = "";
        if ($request->user_id != "") {
            $user_id = $request->user_id;
        }
        if ($request->vendor_id == "") {
            return response()->json(["status" => 0, "message" => trans('messages.vendor_id_required')], 400);
        }
        if ($request->subtotal == "") {
            return response()->json(["status" => 0, "message" => trans('messages.sub_total_required')], 400);
        }
        if ($request->offer_code == "") {
            return response()->json(["status" => 0, "message" => trans('messages.promocode_required')], 400);
        }
        date_default_timezone_set(helper::appdata($request->vendor_id)->timezone);
        $checkoffercode = Promocode::where('offer_code', $request->offer_code)->where('vendor_id', $request->vendor_id)->where('is_available', 1)->first();

        if (!empty($checkoffercode)) {
            if ((date('Y-m-d') >= $checkoffercode->start_date) && (date('Y-m-d') <= $checkoffercode->exp_date)) {

                if ($request->subtotal >= $checkoffercode->min_amount) {
                    if ($checkoffercode->usage_type == 1) {
                        if ($user_id != "") {
                            $checkcount = Order::select('offer_code')->where('offer_code', $request->offer_code)->where('vendor_id', $request->vendor_id)->where('user_id', $user_id)->count();
                        } else {
                            $checkcount = Order::select('offer_code')->where('offer_code', $request->offer_code)->where('vendor_id', $request->vendor_id)->where('session_id', $request->session_id)->count();
                        }
                        if ($checkcount >= $checkoffercode->usage_limit) {
                            return response()->json(["status" => 0, "message" => trans('messages.usage_limit_exceeded')], 400);
                        }
                    }
                    $offer_amount = $checkoffercode->offer_amount;
                    if ($checkoffercode->offer_type == 2) {
                        $offer_amount = $request->subtotal * $checkoffercode->offer_amount / 100;
                    }
                    $arr = array(
                        "offer_code" => $checkoffercode->offer_code,
                        "offer_amount" => $offer_amount,
                        "vendor_id" => $request->vendor_id,
                    );
                    session()->put('discount_data', $arr);
                    return response()->json(["status" => 1, "message" => trans('messages.success'), 'data' => $arr], 200);
                } else {
                    return response()->json(["status" => 0, "message" => trans('messages.order_amount_greater_then') . ' ' . helper::currency_formate($checkoffercode->min_amount, $request->vendor_id)], 200);
                }
            } else {
                return response()->json(["status" => 0, "message" => trans('messages.invalid_promocode')], 200);
            }
        } else {
            return response()->json(["status" => 0, "message" => trans('messages.invalid_promocode')], 200);
        }
    }
    public function reorder_coupon(Request $request)
    {
        if (Auth::user()->type == 4) {
            $vendor_id = Auth::user()->vendor_id;
        } else {
            $vendor_id = Auth::user()->id;
        }
        $getpromocodeslist = Promocode::where('vendor_id', $vendor_id)->get();
        foreach ($getpromocodeslist as $coupon) {
            foreach ($request->order as $order) {
                $coupon = Promocode::where('id', $order['id'])->first();
                $coupon->reorder_id = $order['position'];
                $coupon->save();
            }
        }
        return response()->json(['status' => 1, 'msg' => trans('messages.success')], 200);
    }
}
