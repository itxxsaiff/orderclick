<?php

namespace App\Http\Controllers\admin;

use App\Helpers\helper;
use App\Helpers\Systems;
use App\Http\Controllers\Controller;
use App\Models\PricingPlan;
use App\Models\Settings;
use App\Models\User;
use App\Models\VendorAuditLog;
use App\Models\VendorBranch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Locations & Branches (vendor side).
 *
 * Cities and areas are never typed in — the map picker reverse-geocodes them. The number of
 * branches a vendor may create comes straight from the purchased plan; at the limit the vendor
 * gets an Upgrade action instead of an Add button.
 */
class BranchController extends Controller
{
    private function vendorId()
    {
        return Auth::user()->type == 4 ? Auth::user()->vendor_id : Auth::user()->id;
    }

    /** Branch allowance from the plan. Returns null for unlimited. */
    public static function branchLimit($vendor): ?int
    {
        $plan = $vendor->plan_id ? PricingPlan::find($vendor->plan_id) : null;
        if (empty($plan) || empty($plan->plan_limits)) {
            return 1; // no plan / no extended limits configured yet -> a single primary branch
        }

        $limits = is_array($plan->plan_limits) ? $plan->plan_limits : json_decode($plan->plan_limits, true);
        $row = $limits['branch'] ?? null;
        if (empty($row)) {
            return 1;
        }
        // type 2 = Unlimited in the plan system engine.
        if (($row['type'] ?? '1') == '2') {
            return null;
        }

        return max(1, (int) ($row['count'] ?? 1));
    }

    public function index()
    {
        $vendorId = $this->vendorId();
        $vendor   = User::find($vendorId);
        $branches = VendorBranch::where('vendor_id', $vendorId)->orderBy('reorder_id')->get();

        $limit     = self::branchLimit($vendor);
        $used      = $branches->count();
        $canAdd    = $limit === null || $used < $limit;
        $system    = Systems::normalise($vendor->system);

        return view('admin.branches.index', compact('vendor', 'branches', 'limit', 'used', 'canAdd', 'system'));
    }

    public function add()
    {
        $vendorId = $this->vendorId();
        $vendor   = User::find($vendorId);

        $limit = self::branchLimit($vendor);
        $used  = VendorBranch::where('vendor_id', $vendorId)->count();
        if ($limit !== null && $used >= $limit) {
            return redirect('admin/branches')->with('error', trans('messages.branch_limit_reached'));
        }

        $branch = null;
        $system = Systems::normalise($vendor->system);

        return view('admin.branches.form', compact('vendor', 'branch', 'system'));
    }

    public function edit($id)
    {
        $vendorId = $this->vendorId();
        $branch   = VendorBranch::where('id', $id)->where('vendor_id', $vendorId)->firstOrFail();
        $vendor   = User::find($vendorId);
        $system   = Systems::normalise($vendor->system);

        return view('admin.branches.form', compact('vendor', 'branch', 'system'));
    }

    public function save(Request $request, $id = null)
    {
        $vendorId = $this->vendorId();
        $vendor   = User::find($vendorId);
        $system   = Systems::normalise($vendor->system);

        $request->validate(['name' => 'required|string|max:190']);

        $branch = $id
            ? VendorBranch::where('id', $id)->where('vendor_id', $vendorId)->firstOrFail()
            : new VendorBranch(['vendor_id' => $vendorId]);

        // Creating a new branch has to fit inside the plan allowance.
        if (!$id) {
            $limit = self::branchLimit($vendor);
            $used  = VendorBranch::where('vendor_id', $vendorId)->count();
            if ($limit !== null && $used >= $limit) {
                return redirect('admin/branches')->with('error', trans('messages.branch_limit_reached'));
            }
        }

        $isRemote = $request->boolean('is_remote');

        // A pin is required unless this is an online/remote provider, which stays searchable in
        // the Marketplace without one.
        if (!$isRemote && (!is_numeric($request->latitude) || !is_numeric($request->longitude))) {
            return redirect()->back()->withInput()->with('error', trans('messages.location_required'));
        }

        $oldPin = $branch->latitude ? $branch->latitude . ',' . $branch->longitude : null;
        $newPin = is_numeric($request->latitude) ? $request->latitude . ',' . $request->longitude : null;

        $branch->vendor_id   = $vendorId;
        $branch->name        = $request->name;
        $branch->country     = $request->country;
        $branch->city        = $request->city;
        $branch->area        = $request->area;
        $branch->address     = $request->address;
        $branch->latitude    = is_numeric($request->latitude) ? $request->latitude : null;
        $branch->longitude   = is_numeric($request->longitude) ? $request->longitude : null;
        $branch->phone       = $request->phone;
        $branch->email       = $request->email;
        $branch->is_remote   = $isRemote ? 1 : 2;
        $branch->coverage_km = is_numeric($request->coverage_km) ? $request->coverage_km : null;
        $branch->geo_source  = $request->geo_source ?: 'manual';
        $branch->geocoded_at = now();
        $branch->is_available = $request->boolean('is_available', true) ? 1 : 2;

        if ($system === Systems::ORDERS) {
            $branch->fulfilment = array_values(array_intersect(
                (array) $request->fulfilment,
                ['delivery', 'pickup', 'dine_in']
            ));
        }

        // The first branch a vendor creates is their primary one.
        if (!VendorBranch::where('vendor_id', $vendorId)->where('is_primary', 1)->where('id', '!=', $branch->id ?? 0)->exists()) {
            $branch->is_primary = 1;
        }

        // A moved pin goes back for admin review — and the marketplace listing follows the review.
        if ($oldPin !== $newPin) {
            $branch->review_status = 'pending';
            $branch->reviewed_by = null;
            $branch->reviewed_at = null;
        }

        $branch->reorder_id = $branch->reorder_id ?: (VendorBranch::where('vendor_id', $vendorId)->max('reorder_id') + 1);
        $branch->save();

        if ($oldPin !== $newPin) {
            VendorAuditLog::record($vendorId, 'branch_location', $oldPin, $newPin, $request->get('change_reason'), 'vendor');
        }

        // Keep the legacy single-store fields in step with the primary branch so every existing
        // storefront / marketplace query keeps working unchanged.
        if ($branch->is_primary == 1) {
            User::where('id', $vendorId)->update([
                'country'   => $branch->country,
                'city_name' => $branch->city,
                'area_name' => $branch->area,
                'latitude'  => $branch->latitude,
                'longitude' => $branch->longitude,
            ]);
            $settings = Settings::where('vendor_id', $vendorId)->first();
            if ($settings && $branch->address) {
                $settings->address = $branch->address;
                $settings->save();
            }
        }

        return redirect('admin/branches')->with('success', trans('messages.success'));
    }

    public function delete($id)
    {
        $vendorId = $this->vendorId();
        $branch = VendorBranch::where('id', $id)->where('vendor_id', $vendorId)->firstOrFail();

        if ($branch->is_primary == 1) {
            return redirect('admin/branches')->with('error', trans('messages.cannot_delete_primary_branch'));
        }

        VendorAuditLog::record($vendorId, 'branch_deleted', $branch->name, null, null, 'vendor');
        $branch->delete();

        return redirect('admin/branches')->with('success', trans('messages.success'));
    }
}
