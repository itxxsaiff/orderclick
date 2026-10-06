<?php

namespace App\Http\Controllers\admin;

use App\Helpers\helper;
use App\Helpers\Onboarding;
use App\Helpers\Systems;
use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Settings;
use App\Models\StoreCategory;
use App\Models\User;
use App\Models\VendorAuditLog;
use App\Models\VendorBranch;
use App\Models\VendorDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Vendor setup wizard — six steps from business details to submitting for verification.
 *
 * Each step saves independently ("Save as Draft"), so a merchant can leave and come back. Nothing
 * is required to move between steps; the requirements are enforced once, at submission.
 */
class SetupController extends Controller
{
    private function vendorId()
    {
        return Auth::user()->type == 4 ? Auth::user()->vendor_id : Auth::user()->id;
    }

    private function primaryBranch($vendorId): VendorBranch
    {
        $branch = VendorBranch::where('vendor_id', $vendorId)->where('is_primary', 1)->first();
        if (!$branch) {
            $branch = new VendorBranch([
                'vendor_id' => $vendorId,
                'name'      => optional(User::find($vendorId))->name ?: 'Main Branch',
                'is_primary' => 1,
                'is_available' => 1,
                'reorder_id' => 1,
            ]);
            $branch->save();
        }

        return $branch;
    }

    public function index(Request $request)
    {
        $vendorId = $this->vendorId();
        $vendor   = User::find($vendorId);
        $settings = Settings::where('vendor_id', $vendorId)->first();
        $branch   = $this->primaryBranch($vendorId);

        $system     = Systems::normalise($vendor->system);
        $progress   = Onboarding::progress($vendor);
        $percent    = Onboarding::percent($progress);
        $documents  = Onboarding::documents($vendor);
        $uploaded   = Onboarding::uploaded($vendorId);
        $activities = Systems::activities($system);
        $categories = StoreCategory::where('system', $system)->where('is_deleted', 2)->where('is_available', 1)
            ->orderBy('reorder_id')->get();

        $step = (int) $request->get('step', $vendor->setup_step ?: 1);
        $step = ($step >= 1 && $step <= 6) ? $step : 1;

        return view('admin.setup.index', compact(
            'vendor', 'settings', 'branch', 'system', 'progress', 'percent',
            'documents', 'uploaded', 'activities', 'categories', 'step'
        ));
    }

    /** Save one step and move on. */
    public function save(Request $request, $step)
    {
        $vendorId = $this->vendorId();
        $vendor   = User::find($vendorId);
        $step     = (int) $step;

        // Step 1 can change the account email/mobile, so guard uniqueness before writing — and
        // return the input, so a clash never empties the form the merchant just filled in.
        if ($step === 1) {
            $clash = [];
            foreach (['email', 'mobile'] as $field) {
                if (!$request->filled($field)) {
                    continue;
                }
                $taken = User::where($field, $request->get($field))
                    ->whereIn('type', [1, 2])
                    ->where('is_deleted', 2)
                    ->where('id', '!=', $vendorId)
                    ->exists();
                if ($taken) {
                    $clash[$field] = trans($field === 'email' ? 'messages.unique_email' : 'messages.unique_mobile');
                }
            }
            if (!empty($clash)) {
                return redirect('admin/setup?step=1')
                    ->withInput()
                    ->withErrors($clash)
                    ->with('error', implode(' ', $clash));
            }
        }

        switch ($step) {
            case 1: $this->saveBusiness($request, $vendor); break;
            case 2: $this->saveVerification($request, $vendor); break;
            case 3: $this->saveAuthorization($request, $vendor); break;
            case 4: $this->saveAgreement($request, $vendor); break;
        }

        $next = $request->get('action') === 'draft' ? $step : min(6, $step + 1);
        User::where('id', $vendorId)->update(['setup_step' => $next]);

        return redirect('admin/setup?step=' . $next)->with('success', trans('messages.success'));
    }

    /** Step 1 — business identity, contact, address and GPS. */
    private function saveBusiness(Request $request, User $vendor): void
    {
        $settings = Settings::where('vendor_id', $vendor->id)->first();
        $branch   = $this->primaryBranch($vendor->id);

        $update = [];
        if ($request->filled('business_name')) $update['trade_name'] = $request->business_name;

        // The public link is derived from the BUSINESS name (registration only had a provisional
        // one). It can still be edited by hand, and is never changed once the site is live.
        // A link that is taken or reserved gets the next free variant instead of being dropped.
        if (!Systems::isLive($vendor)) {
            // The link follows the business name unless the merchant typed a link of their own
            // (slug_custom, set by the page when the link field is edited).
            $custom = $request->input('slug_custom') == '1' && trim((string) $request->slug) !== '';
            $base = \Illuminate\Support\Str::slug((string) ($custom ? $request->slug : ($request->business_name ?: $request->slug)), '-');
            $wanted = $base !== '' && $base !== $vendor->slug ? Onboarding::availableSlug($base, $vendor->id) : $vendor->slug;
            if ($wanted !== $vendor->slug) {
                $update['slug'] = $wanted;
                VendorAuditLog::record($vendor->id, $custom ? 'store_link_custom' : 'store_link', $vendor->slug, $wanted, null, 'vendor');
            }
        }
        if ($request->filled('email'))         $update['email'] = $request->email;
        if ($request->filled('mobile'))        $update['mobile'] = $request->mobile;
        if ($request->filled('country_code'))  $update['country_code'] = $request->country_code;
        if ($request->filled('country'))       $update['country'] = $request->country;
        if ($request->filled('city'))          $update['city_name'] = $request->city;

        // Activity drives the marketplace category, so apply it through the shared helper.
        if ($request->filled('activity_id') && Systems::activityBelongsTo($request->activity_id, $vendor->system)) {
            Systems::applyActivity($vendor->id, $request->activity_id, $request->specialization_id);
        }
        // An explicit category choice overrides the automatic one.
        if ($request->filled('store_id')) {
            $category = StoreCategory::find($request->store_id);
            if ($category && Systems::normalise($category->system) === Systems::normalise($vendor->system)) {
                $update['store_id'] = $category->id;
            }
        }

        if (!empty($update)) {
            User::where('id', $vendor->id)->update($update);
        }

        if ($settings && $request->filled('business_name')) {
            $settings->website_title = $request->business_name;
            $settings->save();
        }

        $oldPin = $branch->latitude ? $branch->latitude . ',' . $branch->longitude : null;
        $branch->name      = $request->business_name ?: $branch->name;
        $branch->country   = $request->country ?: $branch->country;
        $branch->city      = $request->city ?: $branch->city;
        $branch->area      = $request->area ?: $branch->area;
        $branch->address   = $request->address ?: $branch->address;
        if (is_numeric($request->latitude) && is_numeric($request->longitude)) {
            $branch->latitude   = $request->latitude;
            $branch->longitude  = $request->longitude;
            $branch->geo_source = $request->geo_source ?: 'map';
            $branch->geocoded_at = now();
        }
        $branch->save();

        $newPin = $branch->latitude ? $branch->latitude . ',' . $branch->longitude : null;
        if ($oldPin !== $newPin) {
            $branch->review_status = 'pending';
            $branch->save();
            VendorAuditLog::record($vendor->id, 'branch_location', $oldPin, $newPin, 'Set during setup', 'vendor');
        }

        // Keep the legacy single-store fields aligned with the primary branch.
        User::where('id', $vendor->id)->update([
            'latitude'  => $branch->latitude,
            'longitude' => $branch->longitude,
        ]);
        if ($settings && $branch->address) {
            $settings->address = $branch->address;
            $settings->save();
        }
    }

    /** Step 2 — registration, licence details and their documents. */
    private function saveVerification(Request $request, User $vendor): void
    {
        User::where('id', $vendor->id)->update(array_filter([
            'cr_number'          => $request->cr_number,
            'license_number'     => $request->license_number,
            'license_expiry_date' => $request->license_expiry_date ?: null,
        ], fn($v) => $v !== null && $v !== ''));

        $this->storeDocuments($request, $vendor, ['commercial_registration', 'business_license', 'sector_license', 'additional']);
    }

    /** Step 3 — who is authorised to act for the business. */
    private function saveAuthorization(Request $request, User $vendor): void
    {
        User::where('id', $vendor->id)->update(array_filter([
            'authorized_person'    => $request->authorized_person,
            'authorized_position'  => $request->authorized_position,
            'authorized_id_number' => $request->authorized_id_number,
        ], fn($v) => $v !== null && $v !== ''));

        $this->storeDocuments($request, $vendor, ['id_document', 'authorization_letter']);
    }

    /** Step 4 — the countersigned service agreement. */
    private function saveAgreement(Request $request, User $vendor): void
    {
        $this->storeDocuments($request, $vendor, ['signed_agreement']);
    }

    /**
     * Persist uploaded files as vendor_documents rows.
     *
     * Re-uploading a document creates a NEW row with a bumped version rather than overwriting, so
     * the correction history the admin reviews is never lost.
     */
    private function storeDocuments(Request $request, User $vendor, array $types): void
    {
        $allowed = ['pdf', 'jpg', 'jpeg', 'png'];

        foreach ($types as $type) {
            $files = $request->file($type);
            if (empty($files)) {
                continue;
            }
            // A single upload arrives as an UploadedFile; casting that with (array) would explode
            // the object into its properties, so normalise explicitly.
            $files = is_array($files) ? $files : [$files];

            foreach ($files as $file) {
                if (!$file || !$file->isValid()) {
                    continue;
                }
                if (!in_array(strtolower($file->getClientOriginalExtension()), $allowed, true)) {
                    continue;
                }
                if ($file->getSize() > 5 * 1024 * 1024) { // 5 MB, as stated on the form
                    continue;
                }

                $stored = helper::store_upload($file, 'admin-assets/documents', 'doc');
                $version = (int) VendorDocument::where('vendor_id', $vendor->id)->where('doc_type', $type)->max('version');

                VendorDocument::create([
                    'vendor_id'     => $vendor->id,
                    'doc_type'      => $type,
                    'file'          => $stored,
                    'original_name' => $file->getClientOriginalName(),
                    'expiry_date'   => $type === 'business_license' ? ($request->license_expiry_date ?: null) : null,
                    'status'        => 'pending',
                    'version'       => $version + 1,
                ]);

                VendorAuditLog::record($vendor->id, 'document_uploaded', null, $type, null, 'vendor');
            }
        }
    }

    /** Step 6 — hand the account to the admin for verification. */
    public function submit(Request $request)
    {
        $vendorId = $this->vendorId();
        $vendor   = User::find($vendorId);
        $progress = Onboarding::progress($vendor);

        foreach ($progress as $step => $state) {
            if ($step < 6 && !$state['done']) {
                return redirect('admin/setup?step=' . $step)->with('error', trans('messages.complete_setup_first'));
            }
        }

        User::where('id', $vendorId)->update([
            'document_submitted_date' => now(),
            'verification_status'     => 'pending_review',
            'setup_completed'         => 1,
            'setup_step'              => 6,
        ]);

        VendorAuditLog::record($vendorId, 'verification_status', $vendor->verification_status, 'pending_review', 'Submitted by vendor', 'vendor');

        // Submitting activates the public website — this is when the subscription clock starts.
        Systems::activateWebsite($vendorId);

        return redirect('admin/setup?step=6')->with('success', trans('messages.setup_submitted'));
    }

    /**
     * The service agreement the merchant downloads, signs and re-uploads. Rendered from the
     * platform's own company details so it always carries the current legal entity.
     */
    public function agreement()
    {
        $vendor  = User::find($this->vendorId());
        $company = \App\Helpers\Subscriptions::company();

        $pdf = \PDF::loadView('admin.setup.agreement', compact('vendor', 'company'));

        return $pdf->download('order-click-service-agreement.pdf');
    }

    /** Dependent dropdown: specializations for an activity. */
    public function specializations(Request $request)
    {
        $vendor = User::find($this->vendorId());

        if (!Systems::activityBelongsTo($request->activity_id, $vendor->system)) {
            return response()->json(['status' => 0, 'specializations' => []], 200);
        }

        $list = Systems::specializations($request->activity_id)
            ->map(fn($s) => ['id' => $s->id, 'name' => $s->display_name])->values();

        return response()->json(['status' => 1, 'specializations' => $list], 200);
    }

    /** Remove an uploaded document before submission. */
    public function delete_document($id)
    {
        $vendorId = $this->vendorId();
        $doc = VendorDocument::where('id', $id)->where('vendor_id', $vendorId)->firstOrFail();

        VendorAuditLog::record($vendorId, 'document_removed', $doc->doc_type, null, null, 'vendor');
        $doc->delete();

        return redirect()->back()->with('success', trans('messages.success'));
    }
}
