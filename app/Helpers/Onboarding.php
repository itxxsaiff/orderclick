<?php

namespace App\Helpers;

use App\Models\Item;
use App\Models\Settings;
use App\Models\User;
use App\Models\VendorBranch;
use App\Models\VendorDocument;

/**
 * The vendor setup wizard: six steps, a completion percentage, and the documents each account
 * type has to provide.
 *
 * Which documents are required varies by system, legal status and country — so the requirement
 * list is computed, never hard-coded into the view.
 */
class Onboarding
{
    public const STEPS = [
        1 => ['key' => 'business',     'label' => 'Business Info',        'full' => 'Business Information'],
        2 => ['key' => 'verification', 'label' => 'Verification',         'full' => 'Verification & Licensing'],
        3 => ['key' => 'authorization','label' => 'Authorization',        'full' => 'Account Authorization'],
        4 => ['key' => 'agreement',    'label' => 'Agreement',            'full' => 'Agreement'],
        5 => ['key' => 'services',     'label' => 'Services & Availability', 'full' => 'Services & Availability'],
        6 => ['key' => 'review',       'label' => 'Review & Submit',      'full' => 'Review & Submit'],
    ];

    /** STEPS with labels in the current language (labels.setup_step_{key} / _full). */
    public static function steps(): array
    {
        return collect(self::STEPS)->map(fn($s) => array_merge($s, [
            'label' => trans('labels.setup_step_' . $s['key']),
            'full'  => trans('labels.setup_step_' . $s['key'] . '_full'),
        ]))->all();
    }

    /** Document slots for this account, in the order they appear in the wizard. */
    public static function documents($vendor): array
    {
        $freelancer = ($vendor->legal_status ?? 'company') === 'freelancer';

        $docs = [
            'commercial_registration' => [
                'label'    => trans($freelancer ? 'labels.doc_freelancer_permit' : 'labels.doc_commercial_registration'),
                'hint'     => trans($freelancer ? 'labels.doc_freelancer_permit_hint' : 'labels.doc_commercial_registration_hint'),
                'required' => true,
                'step'     => 2,
            ],
            'business_license' => [
                'label'    => trans('labels.doc_business_license'),
                'hint'     => trans('labels.doc_business_license_hint'),
                'required' => true,
                'step'     => 2,
            ],
            'id_document' => [
                'label'    => trans('labels.doc_id_document'),
                'hint'     => trans('labels.doc_id_document_hint'),
                'required' => true,
                'step'     => 3,
            ],
            'authorization_letter' => [
                'label'    => trans('labels.doc_authorization_letter'),
                'hint'     => trans('labels.doc_authorization_letter_hint'),
                // A freelancer represents themselves, so no letter of authority is needed.
                'required' => !$freelancer,
                'step'     => 3,
            ],
            'signed_agreement' => [
                'label'    => trans('labels.doc_signed_agreement'),
                'hint'     => trans('labels.doc_signed_agreement_hint'),
                'required' => true,
                'step'     => 4,
            ],
        ];

        // Regulated activities need their sector licence on top of the general one.
        $sector = ['clinic', 'salon', 'pharmacy'];
        $businessType = optional(Settings::where('vendor_id', $vendor->id)->first())->business_type;
        if (in_array($businessType, $sector, true)) {
            $docs['sector_license'] = [
                'label'    => trans('labels.doc_sector_license'),
                'hint'     => trans('labels.doc_sector_license_hint'),
                'required' => true,
                'step'     => 2,
            ];
        }

        $docs['additional'] = [
            'label'    => trans('labels.doc_additional'),
            'hint'     => trans('labels.doc_additional_hint'),
            'required' => false,
            'step'     => 2,
            'multiple' => true,
        ];

        return $docs;
    }

    /** Uploaded documents keyed by type. */
    public static function uploaded($vendorId)
    {
        return VendorDocument::where('vendor_id', $vendorId)->orderByDesc('id')->get()->groupBy('doc_type');
    }

    /**
     * Per-step completion. Returns [step => ['done' => bool, 'missing' => [...]]].
     * The Review step is complete only once every other step is.
     */
    public static function progress($vendor): array
    {
        $settings = Settings::where('vendor_id', $vendor->id)->first();
        $uploaded = self::uploaded($vendor->id);
        $docs     = self::documents($vendor);
        $branch   = VendorBranch::where('vendor_id', $vendor->id)->where('is_primary', 1)->first();

        $has = fn($v) => !empty($v);
        $hasDoc = fn($type) => $uploaded->has($type) && $uploaded[$type]->isNotEmpty();

        $state = [];

        // 1 — Business information
        $missing = [];
        if (!$has(optional($settings)->website_title) && !$has($vendor->trade_name)) $missing[] = trans('labels.miss_business_name');
        if (!$has($vendor->activity_id))                                            $missing[] = trans('labels.miss_business_type');
        if (!$has($vendor->store_id))                                               $missing[] = trans('labels.miss_category');
        if (!$has($vendor->mobile))                                                 $missing[] = trans('labels.miss_phone');
        if (!$has($vendor->email))                                                  $missing[] = trans('labels.miss_email');
        if (!$has(optional($branch)->address))                                      $missing[] = trans('labels.miss_address');
        if (!$has(optional($branch)->city))                                         $missing[] = trans('labels.miss_city');
        if (!$has($vendor->country))                                                $missing[] = trans('labels.miss_country');
        if (!$has(optional($branch)->latitude))                                     $missing[] = 'GPS location';
        $state[1] = ['done' => empty($missing), 'missing' => $missing];

        // 2 — Verification & licensing
        $missing = [];
        foreach ($docs as $type => $d) {
            if ($d['step'] === 2 && $d['required'] && !$hasDoc($type)) $missing[] = $d['label'];
        }
        if (!$has($vendor->cr_number))          $missing[] = trans('labels.miss_registration_number');
        if (!$has($vendor->license_number))     $missing[] = trans('labels.miss_license_number');
        if (!$has($vendor->license_expiry_date))$missing[] = trans('labels.miss_license_expiry');
        $state[2] = ['done' => empty($missing), 'missing' => $missing];

        // 3 — Authorization
        $missing = [];
        if (!$has($vendor->authorized_person))     $missing[] = trans('labels.miss_authorized_person');
        if (!$has($vendor->authorized_position))   $missing[] = trans('labels.miss_position');
        if (!$has($vendor->authorized_id_number))  $missing[] = 'ID / passport number';
        foreach ($docs as $type => $d) {
            if ($d['step'] === 3 && $d['required'] && !$hasDoc($type)) $missing[] = $d['label'];
        }
        $state[3] = ['done' => empty($missing), 'missing' => $missing];

        // 4 — Agreement
        $state[4] = [
            'done'    => $hasDoc('signed_agreement'),
            'missing' => $hasDoc('signed_agreement') ? [] : [trans('labels.miss_signed_agreement')],
        ];

        // 5 — Services & availability
        $missing = [];
        if (Item::where('vendor_id', $vendor->id)->count() === 0) $missing[] = trans('labels.miss_service_or_product');
        if (!$has(optional($settings)->whatsapp_number))          $missing[] = trans('labels.miss_whatsapp');
        $state[5] = ['done' => empty($missing), 'missing' => $missing];

        // 6 — Review & submit
        $allDone = collect($state)->every(fn($s) => $s['done']);
        $state[6] = ['done' => $allDone && !empty($vendor->document_submitted_date), 'missing' => $allDone ? [] : [trans('labels.miss_complete_steps')]];

        return $state;
    }

    /** Overall completion percentage shown in the progress ring. */
    public static function percent(array $progress): int
    {
        $total = count($progress);
        $done  = collect($progress)->where('done', true)->count();

        return $total === 0 ? 0 : (int) round(($done / $total) * 100);
    }

    /** The first step that still needs attention. */
    public static function nextStep(array $progress): int
    {
        foreach ($progress as $step => $state) {
            if (!$state['done']) {
                return $step;
            }
        }

        return 6;
    }

    /**
     * Is the store link still the temporary one made at signup ("{email name}-{5 random}")?
     * Account setup replaces it with the business name.
     */
    public static function slugIsProvisional($vendor): bool
    {
        $slug = (string) ($vendor->slug ?? '');
        if ($slug === '') {
            return true;
        }
        $prefix = \Illuminate\Support\Str::slug((string) ($vendor->name ?? ''), '-');

        return $prefix !== '' && (bool) preg_match('/^' . preg_quote($prefix, '/') . '-[a-z0-9]{5}$/', $slug);
    }

    /**
     * Did the merchant type their own store link? Account setup records a hand-typed link as
     * "store_link_custom" in the audit log; until then the link follows the business name.
     */
    public static function slugIsCustom($vendor): bool
    {
        return \App\Models\VendorAuditLog::where('vendor_id', $vendor->id)
            ->whereIn('field', ['store_link', 'store_link_custom'])
            ->orderByDesc('id')->value('field') === 'store_link_custom';
    }

    /**
     * $base, or the first free variant of it ("-2", "-3" ...): not used by another account and
     * not a word the site itself uses as a page address (admin, register, marketplace ...).
     */
    public static function availableSlug(string $base, $vendorId): string
    {
        $reserved = collect(\Illuminate\Support\Facades\Route::getRoutes()->getRoutes())
            ->map(fn($r) => strtolower(explode('/', trim($r->uri(), '/'))[0]))
            ->reject(fn($s) => $s === '' || str_starts_with($s, '{'))
            ->unique()->all();

        $candidate = $base;
        for ($n = 2; in_array($candidate, $reserved, true) || User::where('slug', $candidate)->where('id', '!=', $vendorId)->exists(); $n++) {
            $candidate = $base . '-' . $n;
        }

        return $candidate;
    }
}
