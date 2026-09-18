<?php

namespace App\Http\Controllers\admin;

use App\Helpers\helper;
use App\Helpers\Systems;
use App\Helpers\Vendor360;
use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Booking;
use App\Models\Order;
use App\Models\ServiceRequest;
use App\Models\Settings;
use App\Models\Specialization;
use App\Models\Transaction;
use App\Models\User;
use App\Models\VendorAddon;
use App\Models\VendorAuditLog;
use App\Models\VendorBranch;
use App\Models\VendorDocument;
use App\Models\VendorEvent;
use App\Models\VendorWhatsappNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Vendor 360 record — step 3 of the client's sequence.
 *
 * One page per vendor, tabbed, reading from the tables created in step 1. Every figure here comes
 * from real rows; sections whose source data does not exist yet render an explicit empty state
 * rather than a placeholder number.
 */
class VendorRecordController extends Controller
{
    public function show(Request $request, $id)
    {
        $vendor = User::where('id', $id)->where('type', 2)->first();
        if (empty($vendor)) {
            abort(404);
        }

        $settings = Settings::where('vendor_id', $vendor->id)->first();
        $summary  = Vendor360::summary($vendor);

        $statuses = [
            'account'      => Vendor360::accountStatus($vendor),
            'verification' => Vendor360::verificationStatus($vendor),
            'subscription' => Vendor360::subscriptionStatus($vendor),
            'page'         => Vendor360::pageStatus($vendor),
        ];

        $tab = $request->get('tab', 'overview');

        $data = [
            'vendor'         => $vendor,
            'settings'       => $settings,
            'summary'        => $summary,
            'statuses'       => $statuses,
            'alert'          => Vendor360::alert($vendor, $summary),
            'activity'       => $vendor->activity_id ? Activity::find($vendor->activity_id) : null,
            'specialization' => $vendor->specialization_id ? Specialization::find($vendor->specialization_id) : null,
            'tab'            => $tab,
        ];

        // Only load what the open tab needs — the record is one page but not one giant query.
        switch ($tab) {
            case 'branches':
                $data['branches'] = VendorBranch::where('vendor_id', $vendor->id)->orderBy('reorder_id')->get();
                break;

            case 'whatsapp':
                $data['numbers'] = VendorWhatsappNumber::where('vendor_id', $vendor->id)->orderBy('priority')->get();
                $data['branches'] = VendorBranch::where('vendor_id', $vendor->id)->orderBy('reorder_id')->get();
                break;

            case 'verification':
                $data['documents'] = VendorDocument::where('vendor_id', $vendor->id)->orderByDesc('id')->get();
                break;

            case 'billing':
                $data['transactions'] = Transaction::where('vendor_id', $vendor->id)
                    ->whereNull('transaction_type')->orderByDesc('id')->get();
                $data['entitlements'] = $this->entitlements($vendor);
                break;

            case 'addons':
                $data['addons'] = VendorAddon::where('vendor_id', $vendor->id)->orderByDesc('id')->get();
                break;

            case 'analytics':
                $data['analytics'] = $this->analytics($vendor, $request);
                break;

            case 'audit':
                $data['logs'] = VendorAuditLog::where('vendor_id', $vendor->id)->orderByDesc('id')->limit(200)->get();
                break;
        }

        return view('admin.user.record', $data);
    }

    /**
     * Plan entitlement vs current usage vs remaining, for the Plan & Billing tab.
     * Limits come off the live transaction; usage comes off the real tables.
     */
    private function entitlements($vendor): array
    {
        $t = Transaction::where('vendor_id', $vendor->id)->whereNull('transaction_type')->orderByDesc('id')->first();
        $system = Systems::normalise($vendor->system);

        $volume = $system === Systems::BOOKING
            ? Booking::where('vendor_id', $vendor->id)->count()
            : ($system === Systems::SERVICE
                ? ServiceRequest::where('vendor_id', $vendor->id)->count()
                : Order::where('vendor_id', $vendor->id)->count());

        $rows = [
            ['key' => 'products',  'label' => $system === Systems::ORDERS ? 'Products' : 'Services',
             'used' => \App\Models\Item::where('vendor_id', $vendor->id)->count(), 'limit' => $t->service_limit ?? null],
            ['key' => 'volume',    'label' => ucfirst(Vendor360::summary($vendor)['volume_label']),
             'used' => $volume, 'limit' => $t->appoinment_limit ?? null],
            ['key' => 'branches',  'label' => 'Branches',
             'used' => VendorBranch::where('vendor_id', $vendor->id)->count(), 'limit' => $this->planLimit($vendor, 'branch')],
            ['key' => 'whatsapp',  'label' => 'WhatsApp numbers',
             'used' => VendorWhatsappNumber::where('vendor_id', $vendor->id)->count(), 'limit' => $this->planLimit($vendor, 'whatsapp')],
            ['key' => 'team',      'label' => 'Team / providers',
             'used' => User::where('type', 4)->where('vendor_id', $vendor->id)->count(), 'limit' => $this->planLimit($vendor, 'team')],
        ];

        foreach ($rows as &$r) {
            $r['percent']   = Vendor360::usagePercent($r['used'], $r['limit']);
            $r['remaining'] = ($r['limit'] === null || (int) $r['limit'] === -1)
                ? null
                : max(0, (int) $r['limit'] - $r['used']);
        }

        return $rows;
    }

    /** Extended limits live in the plan's plan_limits JSON (added by the plan system engine). */
    private function planLimit($vendor, string $key)
    {
        $plan = $vendor->plan_id ? \App\Models\PricingPlan::find($vendor->plan_id) : null;
        if (empty($plan) || empty($plan->plan_limits)) {
            return null;
        }
        $limits = is_array($plan->plan_limits) ? $plan->plan_limits : json_decode($plan->plan_limits, true);
        $row = $limits[$key] ?? null;
        if (empty($row)) {
            return null;
        }

        return ($row['type'] ?? '1') == '2' ? -1 : ($row['count'] ?? null);
    }

    /**
     * Analytics computed from the vendor_events log and the real transaction tables.
     *
     * A WhatsApp click is deliberately NOT a completed transaction — the funnel keeps
     * click -> started -> completed as three separate counts.
     */
    private function analytics($vendor, Request $request): array
    {
        $from = $request->get('from') ?: date('Y-m-d', strtotime('-30 days'));
        $to   = $request->get('to') ?: date('Y-m-d');
        $days = max(1, (int) ((strtotime($to) - strtotime($from)) / 86400));
        $prevFrom = date('Y-m-d', strtotime($from . ' -' . $days . ' days'));

        $events = fn($type, $a, $b) => VendorEvent::where('vendor_id', $vendor->id)
            ->where('event_type', $type)
            ->whereBetween('created_at', [$a . ' 00:00:00', $b . ' 23:59:59']);

        $system = Systems::normalise($vendor->system);
        $txnQuery = function ($a, $b) use ($vendor, $system) {
            $q = $system === Systems::BOOKING
                ? Booking::where('vendor_id', $vendor->id)
                : ($system === Systems::SERVICE
                    ? ServiceRequest::where('vendor_id', $vendor->id)
                    : Order::where('vendor_id', $vendor->id));

            return $q->whereBetween('created_at', [$a . ' 00:00:00', $b . ' 23:59:59']);
        };

        $current = [
            'visitors'        => (clone $events('page_view', $from, $to))->distinct('session_id')->count('session_id'),
            'sessions'        => (clone $events('page_view', $from, $to))->distinct('session_id')->count('session_id'),
            'page_views'      => (clone $events('page_view', $from, $to))->count(),
            'whatsapp_clicks' => (clone $events('whatsapp_click', $from, $to))->count(),
            'started'         => (clone $events('checkout_started', $from, $to))->count(),
            'transactions'    => (clone $txnQuery($from, $to))->count(),
        ];
        $previous = [
            'visitors'        => (clone $events('page_view', $prevFrom, $from))->distinct('session_id')->count('session_id'),
            'sessions'        => (clone $events('page_view', $prevFrom, $from))->distinct('session_id')->count('session_id'),
            'page_views'      => (clone $events('page_view', $prevFrom, $from))->count(),
            'whatsapp_clicks' => (clone $events('whatsapp_click', $prevFrom, $from))->count(),
            'started'         => (clone $events('checkout_started', $prevFrom, $from))->count(),
            'transactions'    => (clone $txnQuery($prevFrom, $from))->count(),
        ];

        $current['conversion'] = $current['visitors'] > 0
            ? round(($current['transactions'] / $current['visitors']) * 100, 1)
            : 0;

        return [
            'from' => $from, 'to' => $to,
            'current' => $current, 'previous' => $previous,
            'has_events' => VendorEvent::where('vendor_id', $vendor->id)->exists(),
        ];
    }

    /** Update one of the four status axes, always through the audit log. */
    public function update_status(Request $request, $id)
    {
        $vendor = User::where('id', $id)->where('type', 2)->first();
        if (empty($vendor)) {
            abort(404);
        }

        $map = [
            'verification_status' => Vendor360::VERIFICATION_STATUSES,
            'subscription_status' => Vendor360::SUBSCRIPTION_STATUSES,
            'public_page_status'  => Vendor360::PAGE_STATUSES,
        ];

        $field = $request->get('field');
        $value = $request->get('value');

        if (!isset($map[$field]) || !array_key_exists($value, $map[$field])) {
            return redirect()->back()->with('error', trans('messages.wrong'));
        }

        VendorAuditLog::record($vendor->id, $field, $vendor->{$field}, $value, $request->get('reason'), 'admin');
        $vendor->{$field} = $value;
        if ($field === 'verification_status' && $value === 'approved') {
            $vendor->account_status = Systems::VERIFIED_ACTIVE;
            $vendor->verified_date = now();
        }
        $vendor->save();

        return redirect()->back()->with('success', trans('messages.success'));
    }

    /**
     * Approve or reject one uploaded verification document.
     *
     * Rejecting records the reason against that document and puts the whole account back into
     * "Changes Required", so the vendor is told exactly which file to replace and why.
     */
    public function review_document(Request $request, $id, $docId)
    {
        $vendor = User::where('id', $id)->where('type', 2)->firstOrFail();
        $doc = VendorDocument::where('id', $docId)->where('vendor_id', $vendor->id)->firstOrFail();

        $decision = $request->get('decision') === 'approve' ? 'approved' : 'rejected';
        $reason = trim((string) $request->get('review_note'));

        if ($decision === 'rejected' && $reason === '') {
            return redirect()->back()->with('error', app()->getLocale() === 'ar'
                ? 'يرجى كتابة سبب رفض هذا المستند.'
                : 'Please write the reason for rejecting this document.');
        }

        $doc->status = $decision;
        $doc->review_note = $reason ?: null;
        $doc->reviewed_by = Auth::id();
        $doc->reviewed_at = now();
        $doc->save();

        VendorAuditLog::record($vendor->id, 'document_' . $decision, $doc->doc_type, $decision, $reason ?: null, 'admin');

        if ($decision === 'rejected') {
            $vendor->verification_status = 'changes_required';
            $vendor->account_status = Systems::CORRECTION_REQUIRED;
            $vendor->save();
        } elseif ($this->allDocumentsApproved($vendor)) {
            // Every required document is approved - move the account to Pending Review so the
            // admin only has to press "Approve verification" once.
            if ($vendor->verification_status !== 'approved') {
                $vendor->verification_status = 'pending_review';
                $vendor->save();
            }
        }

        return redirect()->back()->with('success', trans('messages.success'));
    }

    private function allDocumentsApproved(User $vendor): bool
    {
        $docs = VendorDocument::where('vendor_id', $vendor->id)->get();

        return $docs->isNotEmpty() && $docs->every(fn($d) => $d->status === 'approved');
    }

    /** Save a private admin note. Never shown to the vendor, never exposed to the AI layer. */
    public function save_note(Request $request, $id)
    {
        $vendor = User::where('id', $id)->where('type', 2)->firstOrFail();
        VendorAuditLog::record($vendor->id, 'admin_notes', null, 'updated', null, 'admin');
        $vendor->admin_notes = $request->get('admin_notes');
        $vendor->save();

        return redirect()->back()->with('success', trans('messages.success'));
    }
}
