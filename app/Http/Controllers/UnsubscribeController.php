<?php

namespace App\Http\Controllers;

use App\Models\Subscriber;
use Illuminate\Http\Request;

/**
 * One-click unsubscribe from a marketing email.
 *
 * Public and token-based so it works straight from an inbox with no login. The row is kept and
 * marked Unsubscribed — never deleted — which is what stops future sends reaching the address.
 */
class UnsubscribeController extends Controller
{
    public function unsubscribe(Request $request, string $token)
    {
        $subscriber = Subscriber::where('token', $token)->first();

        if (empty($subscriber)) {
            return response(view('emails.unsubscribed', [
                'ok'    => false,
                'email' => null,
            ]), 404);
        }

        if ($subscriber->isSubscribed()) {
            $subscriber->unsubscribe();
        }

        return view('emails.unsubscribed', [
            'ok'       => true,
            'email'    => $subscriber->email,
            'resubUrl' => url('resubscribe/' . $subscriber->token),
        ]);
    }

    /** Undo, in case the link was clicked by mistake. */
    public function resubscribe(Request $request, string $token)
    {
        $subscriber = Subscriber::where('token', $token)->first();

        if (empty($subscriber)) {
            return response(view('emails.unsubscribed', ['ok' => false, 'email' => null]), 404);
        }

        $subscriber->resubscribe();

        return view('emails.unsubscribed', [
            'ok'         => true,
            'email'      => $subscriber->email,
            'resubscribed' => true,
        ]);
    }
}
