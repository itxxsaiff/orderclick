<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\QuestionAnswer;
use App\Models\WhatsappChatMessage;
use App\Models\WhatsappConversation;
use App\Models\WhatsappSetting;
use App\Services\WhatsAppCloud;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * WhatsApp Cloud API admin: connection settings, the conversation inbox with human takeover,
 * and the AI knowledge base.
 */
class WhatsAppController extends Controller
{
    public function __construct()
    {
        // Hidden tools stay closed even to someone typing the URL. The Knowledge Base stays open:
        // it also feeds the AI assistant on the store page.
        $this->middleware(function ($request, $next) {
            abort_unless(WhatsappSetting::toolsEnabledFor($this->vendorId()), 404);

            return $next($request);
        })->except(['knowledge', 'knowledge_save', 'knowledge_delete', 'knowledge_status']);
    }

    private function vendorId()
    {
        return Auth::user()->type == 4 ? Auth::user()->vendor_id : Auth::user()->id;
    }

    public function settings()
    {
        $vendorId = $this->vendorId();
        $settings = WhatsappSetting::forVendor($vendorId);

        return view('admin.whatsapp.settings', compact('settings'));
    }

    public function save_settings(Request $request)
    {
        $vendorId = $this->vendorId();
        $settings = WhatsappSetting::forVendor($vendorId);

        $settings->display_number  = $request->display_number;
        $settings->phone_number_id = trim((string) $request->phone_number_id);
        $settings->waba_id         = trim((string) $request->waba_id);
        $settings->api_version     = $request->api_version ?: 'v21.0';
        $settings->is_active       = (int) $request->is_active === 1 ? 1 : 2;
        $settings->ai_enabled      = (int) $request->ai_enabled === 1 ? 1 : 2;
        $settings->ai_greeting     = $request->ai_greeting;
        $settings->ai_instructions = $request->ai_instructions;

        // Blank means "keep the stored secret" — the form never echoes it back.
        if ($request->filled('access_token')) {
            $settings->access_token = trim((string) $request->access_token);
        }
        // A wrong app secret makes every incoming webhook fail its signature check, and the
        // messages are dropped silently. Because blank means "keep", there would otherwise be no
        // way back from that through the UI, so removal is an explicit checkbox.
        if ($request->has('clear_app_secret')) {
            $settings->app_secret = null;
        } elseif ($request->filled('app_secret')) {
            $settings->app_secret = trim((string) $request->app_secret);
        }

        $settings->save();

        return redirect('admin/whatsapp/settings')->with('success', trans('messages.success'));
    }

    /** Rotate the verify token — use it if the old one was ever shared in the clear. */
    public function regenerate_token()
    {
        $settings = WhatsappSetting::forVendor($this->vendorId());
        $settings->verify_token = 'oc_' . Str::random(32);
        $settings->save();

        return redirect('admin/whatsapp/settings')->with('success', trans('messages.wa_token_regenerated'));
    }

    // ---------------------------------------------------------------- inbox

    public function conversations(Request $request)
    {
        $vendorId = $this->vendorId();

        $conversations = WhatsappConversation::where('vendor_id', $vendorId)
            ->when($request->filled('q'), fn($q) => $q->where(function ($w) use ($request) {
                $like = '%' . $request->q . '%';
                $w->where('wa_id', 'like', $like)->orWhere('profile_name', 'like', $like);
            }))
            ->orderByDesc('last_message_at')->orderByDesc('id')
            ->limit(100)->get();

        $active = $request->filled('id')
            ? WhatsappConversation::where('vendor_id', $vendorId)->find($request->id)
            : $conversations->first();

        $messages = $active
            ? WhatsappChatMessage::where('conversation_id', $active->id)->orderBy('id')->limit(200)->get()
            : collect();

        if ($active && $active->unread) {
            $active->update(['unread' => 0]);
        }

        return view('admin.whatsapp.conversations', compact('conversations', 'active', 'messages'));
    }

    /** Toggle between AI handling and a human handling one thread. */
    public function takeover(Request $request, $id)
    {
        $conversation = WhatsappConversation::where('vendor_id', $this->vendorId())->findOrFail($id);
        $conversation->handled_by = $conversation->isBotHandled() ? 'human' : 'bot';
        $conversation->assigned_to = $conversation->isBotHandled() ? null : Auth::id();
        $conversation->save();

        return redirect()->back()->with('success', trans('messages.success'));
    }

    /** An admin replying by hand. */
    public function reply(Request $request, $id)
    {
        $vendorId = $this->vendorId();
        $conversation = WhatsappConversation::where('vendor_id', $vendorId)->findOrFail($id);
        $settings = WhatsappSetting::forVendor($vendorId);

        $body = trim((string) $request->body);
        if ($body === '') {
            return redirect()->back()->with('error', trans('messages.wa_empty_message'));
        }

        // Replying by hand implies taking the thread off the bot.
        if ($conversation->isBotHandled()) {
            $conversation->handled_by = 'human';
            $conversation->assigned_to = Auth::id();
            $conversation->save();
        }

        $result = WhatsAppCloud::for($settings)->sendText($conversation->wa_id, $body);

        WhatsappChatMessage::create([
            'conversation_id' => $conversation->id,
            'vendor_id'       => $vendorId,
            'direction'       => 'out',
            'wa_message_id'   => $result['id'] ?? null,
            'type'            => 'text',
            'body'            => $body,
            'sent_by'         => 'admin',
            'status'          => !empty($result['success']) ? 'sent' : 'failed',
            'error'           => $result['error'] ?? null,
        ]);

        $conversation->update(['last_message_at' => now()]);

        return empty($result['success'])
            ? redirect()->back()->with('error', $result['error'])
            : redirect()->back()->with('success', trans('messages.success'));
    }

    // ---------------------------------------------------------------- knowledge base

    public function knowledge(Request $request)
    {
        $vendorId = $this->vendorId();
        $entries = QuestionAnswer::where('vendor_id', $vendorId)
            ->where('source', 'knowledge_base')
            ->orderBy('reorder_id')->orderBy('id')->get();

        return view('admin.whatsapp.knowledge', compact('entries'));
    }

    public function knowledge_save(Request $request, $id = null)
    {
        $vendorId = $this->vendorId();
        $request->validate([
            'question' => 'required|string|max:500',
            'answer'   => 'required|string|max:2000',
        ]);

        $entry = $id
            ? QuestionAnswer::where('vendor_id', $vendorId)->findOrFail($id)
            : new QuestionAnswer(['vendor_id' => $vendorId, 'source' => 'knowledge_base']);

        $entry->vendor_id = $vendorId;
        $entry->source = 'knowledge_base';
        $entry->product_id = null;
        $entry->question = $request->question;
        $entry->answer = $request->answer;
        $entry->is_available = (int) $request->is_available === 1 ? 1 : 2;
        $entry->reorder_id = is_numeric($request->reorder_id) ? (int) $request->reorder_id
            : (int) QuestionAnswer::where('vendor_id', $vendorId)->max('reorder_id') + 1;
        $entry->save();

        return redirect('admin/whatsapp/knowledge')->with('success', trans('messages.success'));
    }

    public function knowledge_delete($id)
    {
        QuestionAnswer::where('vendor_id', $this->vendorId())->findOrFail($id)->delete();

        return redirect('admin/whatsapp/knowledge')->with('success', trans('messages.success'));
    }

    public function knowledge_status($id, $status)
    {
        $entry = QuestionAnswer::where('vendor_id', $this->vendorId())->findOrFail($id);
        $entry->is_available = (int) $status === 1 ? 1 : 2;
        $entry->save();

        return redirect('admin/whatsapp/knowledge')->with('success', trans('messages.success'));
    }
}
