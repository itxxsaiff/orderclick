<?php

namespace App\Http\Controllers\addons\included;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Settings;
use App\Models\WhatsappMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\WhatsAppWebhookService;

class WhatsappController extends Controller
{
    public function index()
    {
        if (Auth::user()->type == 4) {
            $vendor_id = Auth::user()->vendor_id;
        } else {
            $vendor_id = Auth::user()->id;
        }
        return view('admin.included.whatsapp_message.setting_form', compact('vendor_id'));
    }

    public function order_message_update(Request $request)
    {
        try {
            if (Auth::user()->type == 4) {
                $vendor_id = Auth::user()->vendor_id;
            } else {
                $vendor_id = Auth::user()->id;
            }
            $order_message = WhatsappMessage::where('vendor_id', $vendor_id)->first();
            if (empty($order_message)) {
                $order_message = new WhatsappMessage();
                $order_message->vendor_id = $vendor_id;
            }
            if (Auth::user()->type == 2 || (Auth::user()->type == 4 && Auth::user()->vendor_id != 1)) {
                $order_message->item_message = $request->item_message;
                $order_message->order_whatsapp_message = $request->order_whatsapp_message;
                $order_message->order_created = isset($request->order_created) ? 1 : 2;
            }
            $order_message->whatsapp_mobile_view_on_off = isset($request->whatsapp_mobile_view_on_off) ? 1 : 2;
            $order_message->whatsapp_chat_on_off = isset($request->whatsapp_chat_on_off) ? 1 : 2;
            $order_message->whatsapp_chat_position = $request->whatsapp_chat_position;
            if (Auth::user()->type == 1 || (Auth::user()->type == 4 && Auth::user()->vendor_id == 1)) {
                $order_message->whatsapp_number = $request->whatsapp_number;
            }
            $order_message->save();
            return redirect()->back()->with('success', trans('messages.success'));
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function status_message(Request $request)
    {
        try {
            if (Auth::user()->type == 4) {
                $vendor_id = Auth::user()->vendor_id;
            } else {
                $vendor_id = Auth::user()->id;
            }
            $about = WhatsappMessage::where('vendor_id', $vendor_id)->first();
            if (empty($about)) {
                $about = new WhatsappMessage();
                $about->vendor_id = $vendor_id;
            }
            $about->order_status_message = $request->order_status_message;
            $about->status_change = isset($request->status_change) ? 1 : 2;
            $about->save();
            return redirect()->back()->with('success', trans('messages.success'));
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', trans('messages.wrong'));
        }
    }

    public function business_api(Request $request)
    {
        try {
            if (Auth::user()->type == 4) {
                $vendor_id = Auth::user()->vendor_id;
            } else {
                $vendor_id = Auth::user()->id;
            }
            $about = WhatsappMessage::where('vendor_id', $vendor_id)->first();
            if (empty($about)) {
                $about = new WhatsappMessage();
                $about->vendor_id = $vendor_id;
            }
            $about->whatsapp_number = $request->whatsapp_number;
            $about->whatsapp_phone_number_id = $request->whatsapp_phone_number_id;
            $about->whatsapp_access_token = $request->whatsapp_access_token;
            $about->message_type = $request->message_type;
            $about->save();
            return redirect()->back()->with('success', trans('messages.success'));
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', trans('messages.wrong'));
        }
    }

    // public function sendonwhatsapp(Request $request)
    // {
    //     try {
    //         $vendor_slug = $request->vendor;
    //         $host = $_SERVER['HTTP_HOST'];
    //         if ($host  ==  env('WEBSITE_HOST')) {
    //             $vendordata = helper::vendor_data($request->vendor);
    //         }
    //         // if the current host doesn't contain the website domain (meaning, custom domain)
    //         else {
    //             $vendordata = Settings::where('custom_domain', $host)->first();
    //         }
    //         $booking_number = 'PITS1007';
    //         $message = whatsapp_helper::whatsappmessage($booking_number, $vendor_slug, $vendordata);

    //         return response()->json(['status' => 1, 'data' => $message, 'message' => trans('messages.success')]);
    //     } catch (\Throwable $th) {
    //         dd($th);
    //         return response()->json(['status' => 0, 'message' => trans('messages.wrong')]);
    //     }
    // }

    public function webhook(Request $request)
    {
        // ✅ VERIFY
        if ($request->isMethod('get')) {
    
            $verify_token = "restro_verify_123";
    
            if ($request->get('hub_verify_token') === $verify_token) {
                return response($request->get('hub_challenge'), 200);
            }
    
            return response('Invalid token', 403);
        }
    
        // ✅ RECEIVE
        $data = $request->all();
    
        Log::info('WhatsApp Webhook:', $data);
    
        app(\App\Services\WhatsAppWebhookService::class)->process($data);
    
        return response('EVENT_RECEIVED', 200);
    }

}
