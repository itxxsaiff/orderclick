<?php

namespace App\Services;

use App\Helpers\Systems;
use App\Models\Activity;
use App\Models\Category;
use App\Models\Item;
use App\Models\PricingPlan;
use App\Models\QuestionAnswer;
use App\Models\Settings;
use App\Models\Timing;
use App\Models\User;
use App\Models\VendorBranch;
use App\Models\WhatsappChatMessage;
use App\Models\WhatsappConversation;

/**
 * Turns a customer's WhatsApp message into an answer, using that business's own data.
 *
 * Reuses the existing AiAssistant service rather than adding a second AI integration, and builds
 * its context from the SAME sources the website uses — settings, hours, branches, catalogue and
 * the merchant's Q&A knowledge base — so a customer gets the same answer on either channel.
 *
 * The context is always scoped to one vendor_id. A customer talking to Business A can never be
 * answered with Business B's data.
 */
class WhatsAppBrain
{
    public function __construct(private AiAssistant $ai)
    {
    }

    /** Is the platform's own number answering (vendor 1) rather than a merchant's? */
    private function isPlatform($vendorId): bool
    {
        return (int) $vendorId === 1;
    }

    /**
     * Compose a reply. Returns null when the AI cannot answer, so the caller can fall back to a
     * hand-off message instead of sending nonsense.
     */
    public function reply(WhatsappConversation $conversation, string $question): ?string
    {
        if (!AiAssistant::enabled()) {
            return null;
        }

        $context = $this->isPlatform($conversation->vendor_id)
            ? $this->platformContext()
            : $this->businessContext($conversation->vendor_id);

        $result = $this->ai->run(
            $this->instructions($conversation),
            "BUSINESS INFORMATION:\n" . $context
                . "\n\nRECENT CONVERSATION:\n" . $this->history($conversation)
                . "\n\nCUSTOMER MESSAGE:\n" . $question,
            700
        );

        if (empty($result['success'])) {
            return null;
        }

        $text = trim((string) ($result['text'] ?? ''));

        return $text === '' ? null : $text;
    }

    /** How the assistant should behave. */
    private function instructions(WhatsappConversation $conversation): string
    {
        $settings = \App\Models\WhatsappSetting::forVendor($conversation->vendor_id);
        $extra = trim((string) $settings->ai_instructions);

        $base = <<<'TXT'
You are a helpful WhatsApp assistant for a business on the Order Click platform.

Rules:
- Answer ONLY from the BUSINESS INFORMATION provided below. Never invent prices, hours,
  addresses, stock, products or policies.
- If the information is not there, say you are not sure and offer to pass the customer to a
  member of the team.
- Keep replies short and friendly — this is WhatsApp, not a web page. Two or three short
  sentences, plain text, no markdown headings or tables.
- Reply in the SAME language the customer wrote in (Arabic or English).
- Never mention that you are an AI model, and never mention other businesses.
- Answer normally whenever the information is available. Do NOT add any marker to a normal answer.
- ONLY if the customer explicitly asks for a human, makes a complaint, or the request needs a
  human decision (refund, dispute, custom quote, cancelling a paid order), reply briefly and put
  the exact text [HANDOFF] on its own at the very end. Never use it in any other situation.
TXT;

        return $extra === '' ? $base : $base . "\n\nExtra instructions from the business:\n" . $extra;
    }

    /** The last few turns, so the assistant does not repeat itself. */
    private function history(WhatsappConversation $conversation): string
    {
        $lines = WhatsappChatMessage::where('conversation_id', $conversation->id)
            ->orderByDesc('id')->limit(8)->get()->reverse()
            ->map(fn($m) => ($m->isIncoming() ? 'Customer: ' : 'Business: ') . \Illuminate\Support\Str::limit((string) $m->body, 300))
            ->implode("\n");

        return $lines ?: '(no earlier messages)';
    }

    /**
     * Everything a merchant's customer might ask: identity, hours, branches, delivery, payment,
     * catalogue and the merchant's own Q&A.
     */
    private function businessContext($vendorId): string
    {
        $vendor   = User::find($vendorId);
        $settings = Settings::where('vendor_id', $vendorId)->first();
        $system   = Systems::normalise(optional($vendor)->system);
        $activity = optional($vendor)->activity_id ? Activity::find($vendor->activity_id) : null;

        $out = [];
        $out[] = 'Business name: ' . (optional($settings)->website_title ?: optional($vendor)->name);
        $out[] = 'Type of business: ' . Systems::label($system) . ($activity ? ' — ' . $activity->name : '');
        if (optional($settings)->description) $out[] = 'About: ' . strip_tags($settings->description);

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
            $hours = $timings->map(function ($t) {
                if ((int) $t->is_always_close === 1) {
                    return $t->day . ': closed';
                }
                $line = $t->day . ': ' . $t->open_time . ' - ' . $t->close_time;
                if ($t->break_start && $t->break_end) {
                    $line .= ' (break ' . $t->break_start . ' - ' . $t->break_end . ')';
                }
                return $line;
            })->implode('; ');
            $out[] = 'Opening hours: ' . $hours;
        }

        // Fulfilment + payment, phrased per system.
        if ($system === Systems::ORDERS) {
            $ful = optional($branches->first())->fulfilment;
            if (!empty($ful)) {
                $out[] = 'Order options: ' . implode(', ', array_map(fn($f) => str_replace('_', ' ', $f), (array) $ful));
            }
            if (optional($settings)->min_order_amount) $out[] = 'Minimum order: ' . $settings->min_order_amount;
        }
        $payments = \App\Models\Payment::where('vendor_id', $vendorId)->where('is_available', 1)->pluck('payment_name')->implode(', ');
        if ($payments) $out[] = 'Payment methods: ' . $payments;

        // What they sell / offer — capped so the prompt stays small.
        $items = Item::where('vendor_id', $vendorId)->where('is_available', 1)
            ->orderBy('reorder_id')->limit(60)->get(['item_name', 'item_price']);
        if ($items->isNotEmpty()) {
            $label = $system === Systems::ORDERS ? 'Products' : 'Services';
            $out[] = $label . ' (name — price): ' . $items->map(
                fn($i) => $i->item_name . ' — ' . $i->item_price
            )->implode('; ');
        }
        $cats = Category::where('vendor_id', $vendorId)->where('is_deleted', 2)->pluck('name')->implode(', ');
        if ($cats) $out[] = 'Categories: ' . $cats;

        // The merchant's own knowledge base — highest authority, so it goes last and is labelled.
        $out[] = $this->knowledgeBase($vendorId);

        $slug = optional($vendor)->slug;
        if ($slug) $out[] = 'Online store / booking link: ' . url('/' . $slug);

        return implode("\n", array_filter($out));
    }

    /** What the Order Click platform number itself should be able to answer. */
    private function platformContext(): string
    {
        $settings = Settings::where('vendor_id', 1)->first();

        $out = [];
        $out[] = 'You are the assistant for Order Click, a platform where businesses create an '
            . 'online store, booking system or service profile and take orders through WhatsApp.';
        $out[] = 'Systems available: ' . collect(Systems::all())->map(fn($s) => $s['name'] . ' (' . $s['desc'] . ')')->implode('; ');

        $plans = PricingPlan::where('is_available', 1)->orderBy('reorder_id')->get();
        if ($plans->isNotEmpty()) {
            // Quote what checkout actually charges: effectivePrice() applies an active offer,
            // exactly as the plan payment page does.
            $out[] = 'Subscription plans: ' . $plans->map(function ($p) {
                $currency = $p->currency ?: 'USD';
                $line = $p->name . ' — ' . number_format($p->effectivePrice(), 2) . ' ' . $currency;
                if ($label = $p->offerLabel()) {
                    $line .= ' (offer: ' . $label . ', regular ' . number_format((float) $p->price, 2) . ' ' . $currency . ')';
                }

                return $line . ' for ' . Systems::label($p->system);
            })->implode('; ');
        }

        $out[] = 'How to register: ' . url('register/1')
            . ' — choose a system and activity, choose a plan, create the account and pay, then complete the dashboard setup.';
        $out[] = 'Subscription note: the subscription period starts when the business website is activated, not on the payment date.';
        if (optional($settings)->contact) $out[] = 'Support phone: ' . $settings->contact;
        if (optional($settings)->email)   $out[] = 'Support email: ' . $settings->email;

        $out[] = $this->knowledgeBase(1);

        return implode("\n", array_filter($out));
    }

    /** Merchant-authored Q&A — the same records the website assistant uses. */
    private function knowledgeBase($vendorId): string
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
}
