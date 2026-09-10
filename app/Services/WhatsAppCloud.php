<?php

namespace App\Services;

use App\Models\WhatsappSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Thin wrapper over the Meta WhatsApp Cloud API.
 *
 * Everything that talks to Meta goes through here, so credentials, the API version and error
 * handling live in one place.
 */
class WhatsAppCloud
{
    public function __construct(private WhatsappSetting $settings)
    {
    }

    public static function for(WhatsappSetting $settings): self
    {
        return new self($settings);
    }

    private function endpoint(string $path): string
    {
        $version = $this->settings->api_version ?: 'v21.0';

        return "https://graph.facebook.com/{$version}/{$path}";
    }

    /**
     * Send a plain text reply.
     * Returns ['success' => bool, 'id' => ?string, 'error' => ?string].
     */
    public function sendText(string $to, string $body): array
    {
        if (!$this->settings->isReady()) {
            return ['success' => false, 'error' => 'WhatsApp is not configured or is switched off.'];
        }

        // WhatsApp hard-caps a text body at 4096 characters.
        $body = mb_substr(trim($body), 0, 4000);
        if ($body === '') {
            return ['success' => false, 'error' => 'Empty message body.'];
        }

        try {
            $response = Http::withToken($this->settings->access_token)
                ->timeout(20)
                ->acceptJson()
                ->post($this->endpoint($this->settings->phone_number_id . '/messages'), [
                    'messaging_product' => 'whatsapp',
                    'recipient_type'    => 'individual',
                    'to'                => preg_replace('/[^0-9]/', '', $to),
                    'type'              => 'text',
                    'text'              => ['preview_url' => false, 'body' => $body],
                ]);

            if ($response->failed()) {
                $error = $response->json('error.message') ?: $response->body();
                Log::warning('WhatsApp send failed', ['to' => $to, 'error' => $error]);

                return ['success' => false, 'error' => $error];
            }

            return ['success' => true, 'id' => $response->json('messages.0.id')];
        } catch (\Throwable $e) {
            Log::error('WhatsApp send exception: ' . $e->getMessage());

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /** Blue ticks on the customer's message. Best-effort; never blocks a reply. */
    public function markRead(string $waMessageId): void
    {
        if (!$this->settings->isReady()) {
            return;
        }

        try {
            Http::withToken($this->settings->access_token)->timeout(10)
                ->post($this->endpoint($this->settings->phone_number_id . '/messages'), [
                    'messaging_product' => 'whatsapp',
                    'status'            => 'read',
                    'message_id'        => $waMessageId,
                ]);
        } catch (\Throwable $e) {
            // Read receipts are cosmetic — a failure here must not stop the conversation.
        }
    }

    /**
     * Verify Meta's X-Hub-Signature-256 header against the app secret.
     * Returns true when no secret is configured, so setup is not blocked before it is filled in.
     */
    public function verifySignature(?string $header, string $rawBody): bool
    {
        $secret = $this->settings->app_secret;
        if (empty($secret)) {
            return true;
        }
        if (empty($header) || !str_starts_with($header, 'sha256=')) {
            return false;
        }

        $expected = 'sha256=' . hash_hmac('sha256', $rawBody, $secret);

        return hash_equals($expected, $header);
    }
}
