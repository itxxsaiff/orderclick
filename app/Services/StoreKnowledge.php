<?php

namespace App\Services;

use App\Helpers\Systems;
use App\Helpers\helper;
use App\Models\Activity;
use App\Models\BookingService;
use App\Models\Category;
use App\Models\Doctor;
use App\Models\Item;
use App\Models\ItemImages;
use App\Models\Payment;
use App\Models\QuestionAnswer;
use App\Models\Settings;
use App\Models\Timing;
use App\Models\User;
use App\Models\VendorBranch;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Everything an assistant may tell a customer about ONE business: identity, branches, hours,
 * delivery, payment, policies, the catalogue and the merchant's Knowledge Base.
 *
 * Shared by the store-page assistant and the WhatsApp assistant so both give the same answers.
 * Every query is scoped to a single vendor_id — another business's data can never leak in.
 */
class StoreKnowledge
{
    /** Business types that run on the booking module (no cart). */
    private const BOOKING_TYPES = ['booking', 'clinic', 'salon'];

    /**
     * How a customer completes things in this store:
     *   'orders'  — product pages, cart and checkout
     *   'booking' — the booking page (services, plus doctors/team for clinics and salons)
     *   'service' — the service request page
     */
    public static function flow($vendorId): string
    {
        $type = optional(Settings::where('vendor_id', $vendorId)->first())->business_type;
        if (in_array($type, self::BOOKING_TYPES, true)) {
            return 'booking';
        }
        $system = Systems::normalise(optional(User::find($vendorId))->system);
        if ($system === Systems::BOOKING) {
            return 'booking';
        }
        if ($system === Systems::SERVICE || $type === 'service') {
            return 'service';
        }

        return 'orders';
    }

    /**
     * What the store offers, keyed by a short reference the assistant can cite:
     * P{id} product/service item, S{id} bookable service, D{id} doctor / team member.
     */
    public static function catalog($vendorId): array
    {
        $out = [];
        $flow = self::flow($vendorId);

        if ($flow === 'booking') {
            foreach (BookingService::where('vendor_id', $vendorId)->where('is_available', 1)->orderBy('reorder_id')->limit(80)->get() as $s) {
                $out['S' . $s->id] = [
                    'type' => 'service', 'id' => $s->id, 'name' => $s->name,
                    'price' => self::price($s->price, $vendorId), 'note' => trim(implode(' · ', array_filter([$s->category, $s->duration ? $s->duration . ' min' : null]))),
                    'description' => self::short($s->description), 'image' => $s->image ? url(env('ASSETSPATHURL') . 'item/' . $s->image) : null,
                ];
            }
            foreach (Doctor::where('vendor_id', $vendorId)->where('is_available', 1)->orderBy('reorder_id')->limit(40)->get() as $d) {
                $out['D' . $d->id] = [
                    'type' => 'doctor', 'id' => $d->id, 'name' => $d->name,
                    'price' => $d->fee ? self::price($d->fee, $vendorId) : null, 'note' => (string) $d->specialty,
                    'description' => self::short($d->about), 'image' => $d->image ? url(env('ASSETSPATHURL') . 'item/' . $d->image) : null,
                ];
            }

            return $out;
        }

        $items = Item::where('vendor_id', $vendorId)->where('is_available', 1)->orderBy('reorder_id')->limit(120)
            ->get(['id', 'cat_id', 'item_name', 'item_price', 'item_original_price', 'description', 'slug', 'image']);
        $cats = Category::where('vendor_id', $vendorId)->pluck('name', 'id');
        $images = ItemImages::whereIn('item_id', $items->pluck('id'))->orderBy('reorder_id')->get()->groupBy('item_id');
        foreach ($items as $i) {
            $img = optional(optional($images->get($i->id))->first())->image ?: $i->image;
            $out['P' . $i->id] = [
                'type' => 'product', 'id' => $i->id, 'name' => $i->item_name, 'slug' => $i->slug,
                'price' => self::price($i->item_price, $vendorId), 'note' => (string) ($cats[$i->cat_id] ?? ''),
                'description' => self::short($i->description), 'image' => $img ? url(env('ASSETSPATHURL') . 'item/' . $img) : null,
            ];
        }

        return $out;
    }

    /**
     * The business information as plain text for an AI prompt. With $refs the catalogue lines
     * start with their reference (e.g. "[P12]") so the assistant can point at items.
     */
    public static function context($vendorId, bool $refs = false): string
    {
        $vendor   = User::find($vendorId);
        $settings = Settings::where('vendor_id', $vendorId)->first();
        $system   = Systems::normalise(optional($vendor)->system);
        $activity = optional($vendor)->activity_id ? Activity::find($vendor->activity_id) : null;
        $flow     = self::flow($vendorId);

        $out = [];
        $out[] = 'Business name: ' . self::businessName($vendorId);
        $out[] = 'Type of business: ' . Systems::label($system) . ($activity ? ' — ' . $activity->name : '');
        if (optional($settings)->description) $out[] = 'About: ' . self::short($settings->description, 600);

        // Contact + location, from the branch records.
        $branches = VendorBranch::where('vendor_id', $vendorId)->where('is_available', 1)->orderBy('reorder_id')->get();
        foreach ($branches as $b) {
            $line = 'Branch: ' . $b->name;
            if ($b->address) $line .= ' — ' . $b->address;
            if ($b->city) $line .= ', ' . $b->city;
            if ($b->phone) $line .= ' (phone ' . $b->phone . ')';
            if ($b->coverage_km) $line .= ' — serves about ' . rtrim(rtrim(number_format($b->coverage_km, 1), '0'), '.') . ' km around it';
            $out[] = $line;
        }
        if ($branches->isEmpty() && optional($settings)->address) {
            $out[] = 'Address: ' . $settings->address;
        }
        if (optional($settings)->contact) $out[] = 'Phone: ' . $settings->contact;
        if (optional($settings)->email)   $out[] = 'Email: ' . $settings->email;

        // Opening hours.
        $timings = Timing::where('vendor_id', $vendorId)->get();
        if ($timings->isNotEmpty()) {
            $out[] = 'Opening hours: ' . $timings->map(function ($t) {
                if ((int) $t->is_always_close === 1) {
                    return $t->day . ': closed';
                }
                $line = $t->day . ': ' . $t->open_time . ' - ' . $t->close_time;
                if ($t->break_start && $t->break_end) {
                    $line .= ' (break ' . $t->break_start . ' - ' . $t->break_end . ')';
                }

                return $line;
            })->implode('; ');
        }

        // Fulfilment, delivery and payment.
        if ($flow === 'orders') {
            $ful = optional($branches->first())->fulfilment;
            if (!empty($ful)) {
                $out[] = 'Order options: ' . implode(', ', array_map(fn($f) => str_replace('_', ' ', $f), (array) $ful));
            }
            if (optional($settings)->min_order_amount) $out[] = 'Minimum order: ' . self::price($settings->min_order_amount, $vendorId);
            $areas = DB::table('shipping_area')->where('vendor_id', $vendorId)->where('is_available', 1)->orderBy('reorder_id')->get();
            if ($areas->isNotEmpty()) {
                $out[] = 'Delivery areas and charges: ' . $areas->map(fn($a) => $a->area_name . ' — ' . self::price($a->delivery_charge, $vendorId))->implode('; ');
            }
        }
        $payments = Payment::where('vendor_id', $vendorId)->where('is_available', 1)->pluck('payment_name')->implode(', ');
        if ($payments) $out[] = 'Payment methods: ' . $payments;

        // The catalogue.
        $label = ['orders' => 'Products', 'booking' => 'Bookable services and team', 'service' => 'Services'][$flow];
        $lines = collect(self::catalog($vendorId))->map(function ($c, $ref) use ($refs) {
            $line = ($refs ? '[' . $ref . '] ' : '') . $c['name'];
            if ($c['type'] === 'doctor') $line .= ' (team member' . ($c['note'] ? ', ' . $c['note'] : '') . ')';
            elseif ($c['note']) $line .= ' (' . $c['note'] . ')';
            if ($c['price']) $line .= ' — ' . $c['price'];
            if ($c['description']) $line .= ' — ' . $c['description'];

            return '- ' . $line;
        });
        if ($lines->isNotEmpty()) {
            $out[] = $label . ":\n" . $lines->implode("\n");
        }

        // Policies the merchant wrote on their store.
        foreach (['refund_policy' => ['refund_policy_content', 'Refund policy'], 'terms' => ['terms_content', 'Terms and conditions'], 'privacypolicy' => ['privacypolicy_content', 'Privacy policy']] as $table => [$col, $title]) {
            $text = self::short(DB::table($table)->where('vendor_id', $vendorId)->value($col), 1200);
            if ($text !== '') $out[] = $title . ': ' . $text;
        }

        // The merchant's own Knowledge Base — highest authority.
        $out[] = self::knowledgeBase($vendorId);

        $slug = optional($vendor)->slug;
        if ($slug) $out[] = 'Online store / booking link: ' . url('/' . $slug);

        return implode("\n", array_filter($out));
    }

    /**
     * The store's display name. New stores start with a copy of the platform's title as it was at
     * signup ("Order Click", "Order Click | Multi-Business ..."); until the merchant sets their own,
     * their account name is used.
     */
    public static function businessName($vendorId): string
    {
        $title = trim((string) Settings::where('vendor_id', $vendorId)->value('website_title'));
        $platform = trim((string) Settings::where('vendor_id', 1)->value('website_title'));
        $isDefault = $title === '' || ($platform !== '' && Str::startsWith($title, $platform));

        return $isDefault ? (string) optional(User::find($vendorId))->name : $title;
    }

    /** Merchant-written Q&A, answered entries only. */
    public static function knowledgeBase($vendorId): string
    {
        $qa = QuestionAnswer::where('vendor_id', $vendorId)
            ->where('is_available', 1)
            ->whereNotNull('answer')->where('answer', '!=', '')
            ->orderBy('reorder_id')->limit(60)->get();

        if ($qa->isEmpty()) {
            return '';
        }

        return "Business Q&A (use these answers first, they are written by the business):\n"
            . $qa->map(fn($q) => '- Q: ' . $q->question . ' A: ' . $q->answer)->implode("\n");
    }

    private static function price($amount, $vendorId): ?string
    {
        return $amount === null || $amount === '' ? null : strip_tags(html_entity_decode((string) helper::currency_formate($amount, $vendorId)));
    }

    /** Plain text, whitespace collapsed, cut to $limit characters. */
    private static function short($html, int $limit = 160): string
    {
        $text = trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags((string) $html), ENT_QUOTES | ENT_HTML5, 'UTF-8')));

        return Str::limit($text, $limit);
    }
}
