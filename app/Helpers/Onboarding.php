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

    /** Document slots for this account, in the order they appear in the wizard. */
    public static function documents($vendor): array
    {
        $freelancer = ($vendor->legal_status ?? 'company') === 'freelancer';

        $docs = [
            'commercial_registration' => [
                'label'    => $freelancer ? 'Freelancer Permit / Registration' : 'Commercial Registration',
                'hint'     => $freelancer ? 'Upload your freelancer permit' : 'Upload valid commercial registration',
                'required' => true,
                'step'     => 2,
            ],
            'business_license' => [
                'label'    => 'Business License',
                'hint'     => 'Upload your activity license',
                'required' => true,
                'step'     => 2,
            ],
            'id_document' => [
                'label'    => 'ID Document',
                'hint'     => 'Owner or authorised representative ID',
                'required' => true,
                'step'     => 3,
            ],
            'authorization_letter' => [
                'label'    => 'Authorization Letter (Power of Attorney)',
                'hint'     => 'Upload letter authorizing you to act on behalf of the business',
                // A freelancer represents themselves, so no letter of authority is needed.
                'required' => !$freelancer,
                'step'     => 3,
            ],
            'signed_agreement' => [
                'label'    => 'Signed Agreement',
                'hint'     => 'Upload the signed and stamped agreement',
                'required' => true,
                'step'     => 4,
            ],
        ];

        // Regulated activities need their sector licence on top of the general one.
        $sector = ['clinic', 'salon', 'pharmacy'];
        $businessType = optional(Settings::where('vendor_id', $vendor->id)->first())->business_type;
        if (in_array($businessType, $sector, true)) {
            $docs['sector_license'] = [
                'label'    => 'Professional / Sector Licence',
                'hint'     => 'Required for regulated activities',
                'required' => true,
                'step'     => 2,
            ];
        }

        $docs['additional'] = [
            'label'    => 'Additional Documents (if required)',
            'hint'     => 'Any other documents related to your business',
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
        if (!$has(optional($settings)->website_title) && !$has($vendor->trade_name)) $missing[] = 'Business name';
        if (!$has($vendor->activity_id))                                            $missing[] = 'Business type';
        if (!$has($vendor->store_id))                                               $missing[] = 'Category';
        if (!$has($vendor->mobile))                                                 $missing[] = 'Phone number';
        if (!$has($vendor->email))                                                  $missing[] = 'Email address';
        if (!$has(optional($branch)->address))                                      $missing[] = 'Full address';
        if (!$has(optional($branch)->city))                                         $missing[] = 'City';
        if (!$has($vendor->country))                                                $missing[] = 'Country';
        if (!$has(optional($branch)->latitude))                                     $missing[] = 'GPS location';
        $state[1] = ['done' => empty($missing), 'missing' => $missing];

        // 2 — Verification & licensing
        $missing = [];
        foreach ($docs as $type => $d) {
            if ($d['step'] === 2 && $d['required'] && !$hasDoc($type)) $missing[] = $d['label'];
        }
        if (!$has($vendor->cr_number))          $missing[] = 'Registration number';
        if (!$has($vendor->license_number))     $missing[] = 'License number';
        if (!$has($vendor->license_expiry_date))$missing[] = 'License expiry date';
        $state[2] = ['done' => empty($missing), 'missing' => $missing];

        // 3 — Authorization
        $missing = [];
        if (!$has($vendor->authorized_person))     $missing[] = 'Authorized person';
        if (!$has($vendor->authorized_position))   $missing[] = 'Position / title';
        if (!$has($vendor->authorized_id_number))  $missing[] = 'ID / passport number';
        foreach ($docs as $type => $d) {
            if ($d['step'] === 3 && $d['required'] && !$hasDoc($type)) $missing[] = $d['label'];
        }
        $state[3] = ['done' => empty($missing), 'missing' => $missing];

        // 4 — Agreement
        $state[4] = [
            'done'    => $hasDoc('signed_agreement'),
            'missing' => $hasDoc('signed_agreement') ? [] : ['Signed agreement'],
        ];

        // 5 — Services & availability
        $missing = [];
        if (Item::where('vendor_id', $vendor->id)->count() === 0) $missing[] = 'At least one service or product';
        if (!$has(optional($settings)->whatsapp_number))          $missing[] = 'WhatsApp number';
        $state[5] = ['done' => empty($missing), 'missing' => $missing];

        // 6 — Review & submit
        $allDone = collect($state)->every(fn($s) => $s['done']);
        $state[6] = ['done' => $allDone && !empty($vendor->document_submitted_date), 'missing' => $allDone ? [] : ['Complete the steps above']];

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
}
