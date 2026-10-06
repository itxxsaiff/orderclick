<?php

namespace App\Services;

use App\Helpers\Systems;
use App\Models\PricingPlan;
use App\Models\Settings;
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

    /** Everything a merchant's customer might ask — shared with the store-page assistant. */
    private function businessContext($vendorId): string
    {
        return StoreKnowledge::context($vendorId);
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

    /** Merchant-authored Q&A — the same records the store-page assistant uses. */
    private function knowledgeBase($vendorId): string
    {
        return StoreKnowledge::knowledgeBase($vendorId);
    }
}
