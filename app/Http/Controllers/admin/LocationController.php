<?php

namespace App\Http\Controllers\admin;

use App\Helpers\Systems;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\VendorAuditLog;
use App\Models\VendorBranch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Admin Locations & Marketplace coverage.
 *
 * One row per vendor branch: who it belongs to, the purchased system, the auto-filled location,
 * branch usage against the plan, coverage, GPS state and whether it is visible in the Marketplace.
 * Reviewing a location is what makes it publicly discoverable.
 */
class LocationController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'all');

        $query = VendorBranch::query()
            ->join('users', 'users.id', '=', 'vendor_branches.vendor_id')
            ->where('users.type', 2)
            ->where('users.is_deleted', 2)
            ->whereNull('users.archived_at')
            ->select('vendor_branches.*', 'users.name as vendor_name', 'users.trade_name', 'users.vendor_code',
                'users.system', 'users.plan_id', 'users.slug', 'users.account_status', 'users.is_sandbox');

        if (Systems::isValid($tab)) {
            $query->where(function ($q) use ($tab) {
                $q->where('users.system', $tab);
                if ($tab === Systems::ORDERS) {
                    $q->orWhereNull('users.system')->orWhere('users.system', '');
                }
            });
        }

        if ($search = trim((string) $request->get('q'))) {
            $like = '%' . $search . '%';
            $query->where(function ($w) use ($like) {
                $w->where('users.name', 'like', $like)
                    ->orWhere('users.trade_name', 'like', $like)
                    ->orWhere('users.vendor_code', 'like', $like)
                    ->orWhere('vendor_branches.name', 'like', $like)
                    ->orWhere('vendor_branches.city', 'like', $like)
                    ->orWhere('vendor_branches.area', 'like', $like)
                    ->orWhere('vendor_branches.address', 'like', $like);
            });
        }

        if ($request->filled('gps')) {
            $request->get('gps') === 'missing'
                ? $query->whereNull('vendor_branches.latitude')
                : $query->whereNotNull('vendor_branches.latitude');
        }
        if ($request->filled('review')) {
            $query->where('vendor_branches.review_status', $request->get('review'));
        }

        $branches = $query->orderBy('users.id')->orderBy('vendor_branches.reorder_id')->get();

        // Branch usage against each vendor's plan, computed once per vendor.
        $vendors = User::whereIn('id', $branches->pluck('vendor_id')->unique())->get()->keyBy('id');
        $usage = [];
        foreach ($vendors as $v) {
            $usage[$v->id] = [
                'limit' => BranchController::branchLimit($v),
                'used'  => $branches->where('vendor_id', $v->id)->count(),
            ];
        }

        $tabCounts = [];
        foreach (array_merge(['all'], Systems::keys()) as $key) {
            $c = VendorBranch::query()
                ->join('users', 'users.id', '=', 'vendor_branches.vendor_id')
                ->where('users.type', 2)->where('users.is_deleted', 2)->whereNull('users.archived_at');
            if ($key !== 'all') {
                $c->where(function ($q) use ($key) {
                    $q->where('users.system', $key);
                    if ($key === Systems::ORDERS) $q->orWhereNull('users.system')->orWhere('users.system', '');
                });
            }
            $tabCounts[$key] = $c->count();
        }

        $pendingReview = VendorBranch::where('review_status', 'pending')->count();

        return view('admin.location.index', compact('branches', 'vendors', 'usage', 'tab', 'tabCounts', 'pendingReview'));
    }

    /** Approve or reject a branch location. Approving is what puts it in the Marketplace. */
    public function review(Request $request, $id)
    {
        $branch = VendorBranch::findOrFail($id);
        $status = $request->get('status');

        if (!in_array($status, ['verified', 'rejected', 'pending'], true)) {
            return redirect()->back()->with('error', trans('messages.wrong'));
        }

        VendorAuditLog::record($branch->vendor_id, 'branch_review', $branch->review_status, $status, $request->get('reason'), 'admin');

        $branch->review_status = $status;
        $branch->reviewed_by = Auth::id();
        $branch->reviewed_at = now();
        $branch->save();

        return redirect()->back()->with('success', trans('messages.success'));
    }
}
