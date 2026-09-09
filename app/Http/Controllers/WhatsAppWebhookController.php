<?php

namespace App\Http\Controllers;

use App\Models\WhatsappChatMessage;
use App\Models\WhatsappConversation;
use App\Models\WhatsappSetting;
use App\Services\WhatsAppBrain;
use App\Services\WhatsAppCloud;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Meta WhatsApp Cloud API webhook.
 *
 * GET  — the one-time handshake Meta performs when the Callback URL is saved.
 * POST — every inbound message and status update.
 *
 * Flow: customer → Meta → here → identify the business from phone_number_id → log → AI reply
 * (when enabled and no human has taken the thread over) → send back through the Cloud API.
 */
class WhatsAppWebhookController extends Controller
{
    /** Meta's verification handshake. */
    public function verify(Request $request)
    {
        $mode      = $request->get('hub_mode', $request->get('hub.mode'));
        $token     = $request->get('hub_verify_token', $request->get('hub.verify_token'));
        $challenge = $request->get('hub_challenge', $request->get('hub.challenge'));

        if ($mode !== 'subscribe' || empty($token)) {
            return response('Bad request', 400);
        }

        // Any configured business's verify token may complete the handshake.
        $matches = WhatsappSetting::where('verify_token', $token)->exists();
        if (!$matches) {
            Log::warning('WhatsApp webhook verify failed: token did not match.');

            return response('Forbidden', 403);
        }

        return response((string) $challenge, 200)->header('Content-Type', 'text/plain');
    }

    /**
     * Inbound events. Always answers 200 quickly — Meta retries anything else, which would
     * duplicate replies.
     */
    public function receive(Request $request)
    {
        $payload = $request->all();

        try {
            foreach (($payload['entry'] ?? []) as $entry) {
                foreach (($entry['changes'] ?? []) as $change) {
                    $value = $change['value'] ?? [];
                    $phoneNumberId = $value['metadata']['phone_number_id'] ?? null;

                    $settings = WhatsappSetting::byPhoneNumberId($phoneNumberId);
                    if (empty($settings)) {
                        Log::warning('WhatsApp webhook: unknown phone_number_id', ['id' => $phoneNumberId]);
                        continue;
                    }

                    // Reject forged payloads when an app secret is configured.
                    $cloud = WhatsAppCloud::for($settings);
                    if (!$cloud->verifySignature($request->header('X-Hub-Signature-256'), $request->getContent())) {
                        Log::warning('WhatsApp webhook: bad signature.');
                        continue;
                    }

                    foreach (($value['statuses'] ?? []) as $status) {
                        $this->recordStatus($status);
                    }

                    foreach (($value['messages'] ?? []) as $message) {
                        $this->handleMessage($settings, $cloud, $value, $message);
                    }
                }
            }
        } catch (\Throwable $e) {
            // Never bubble up: a 500 makes Meta retry and the customer gets duplicate replies.
            Log::error('WhatsApp webhook error: ' . $e->getMessage(), [
                'file' => $e->getFile(), 'line' => $e->getLine(),
            ]);
        }

        return response('EVENT_RECEIVED', 200);
    }

    private function handleMessage(WhatsappSetting $settings, WhatsAppCloud $cloud, array $value, array $message): void
    {
        $waId = $message['from'] ?? null;
        $waMessageId = $message['id'] ?? null;
        if (empty($waId) || empty($waMessageId)) {
            return;
        }

        // Meta re-delivers on any non-200; skip anything already stored.
        if (WhatsappChatMessage::where('wa_message_id', $waMessageId)->exists()) {
            return;
        }

        $profileName = $value['contacts'][0]['profile']['name'] ?? null;
        $body = $this->extractBody($message);

        $conversation = WhatsappConversation::firstOrNew([
            'vendor_id' => $settings->vendor_id,
            'wa_id'     => $waId,
        ]);
        if (!$conversation->exists) {
            $conversation->handled_by = 'bot';
        }
        $conversation->profile_name = $profileName ?: $conversation->profile_name;
        $conversation->last_message_at = now();
        $conversation->unread = (int) $conversation->unread + 1;
        $conversation->save();

        WhatsappChatMessage::create([
            'conversation_id' => $conversation->id,
            'vendor_id'       => $settings->vendor_id,
            'direction'       => 'in',
            'wa_message_id'   => $waMessageId,
            'type'            => $message['type'] ?? 'text',
            'body'            => $body,
        ]);

        $cloud->markRead($waMessageId);

        // A human has taken this thread over, or AI replies are switched off.
        if (!$conversation->isBotHandled() || (int) $settings->ai_enabled !== 1) {
            return;
        }

        // Only text is answered automatically; anything else waits for a person.
        if (($message['type'] ?? 'text') !== 'text' || $body === '') {
            $this->send($settings, $cloud, $conversation, trans('messages.wa_non_text'), 'system');
            return;
        }

        $answer = app(WhatsAppBrain::class)->reply($conversation, $body);

        if ($answer === null) {
            $this->handOff($settings, $cloud, $conversation, trans('messages.wa_handoff'));
            return;
        }

        // The model marks a request that needs a person.
        if (str_contains($answer, '[HANDOFF]')) {
            $clean = trim(str_replace('[HANDOFF]', '', $answer));
            $this->handOff($settings, $cloud, $conversation, $clean !== '' ? $clean : trans('messages.wa_handoff'));
            return;
        }

        $this->send($settings, $cloud, $conversation, $answer, 'ai');
    }

    /** Move the thread to a human and tell the customer. */
    private function handOff(WhatsappSetting $settings, WhatsAppCloud $cloud, WhatsappConversation $conversation, string $text): void
    {
        $conversation->handled_by = 'human';
        $conversation->save();

        $this->send($settings, $cloud, $conversation, $text, 'system');
    }

    private function send(WhatsappSetting $settings, WhatsAppCloud $cloud, WhatsappConversation $conversation, string $text, string $sentBy): void
    {
        $result = $cloud->sendText($conversation->wa_id, $text);

        WhatsappChatMessage::create([
            'conversation_id' => $conversation->id,
            'vendor_id'       => $settings->vendor_id,
            'direction'       => 'out',
            'wa_message_id'   => $result['id'] ?? null,
            'type'            => 'text',
            'body'            => $text,
            'sent_by'         => $sentBy,
            'status'          => !empty($result['success']) ? 'sent' : 'failed',
            'error'           => $result['error'] ?? null,
        ]);
    }

    /** Delivery receipts for messages we sent. */
    private function recordStatus(array $status): void
    {
        if (empty($status['id'])) {
            return;
        }

        WhatsappChatMessage::where('wa_message_id', $status['id'])
            ->where('direction', 'out')
            ->update([
                'status' => $status['status'] ?? null,
                'error'  => $status['errors'][0]['title'] ?? null,
            ]);
    }

    /** Pull readable text out of whatever message type arrived. */
    private function extractBody(array $message): string
    {
        return trim((string) match ($message['type'] ?? 'text') {
            'text'        => $message['text']['body'] ?? '',
            'button'      => $message['button']['text'] ?? '',
            'interactive' => $message['interactive']['button_reply']['title']
                             ?? $message['interactive']['list_reply']['title'] ?? '',
            default       => '',
        });
    }
}
