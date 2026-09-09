<?php

namespace App\Http\Controllers\admin;

use App\Helpers\helper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\SystemAddons;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    public function index()
    {

        if (Auth::user()->type == 4) {
            $vendor_id = Auth::user()->vendor_id;
        } else {
            $vendor_id = Auth::user()->id;
        }
        if (SystemAddons::where('unique_identifier', 'subscription')->first() == null && Auth::user()->type == 1) {
            return redirect()->back()->with(['error' => 'You can not charge your end customers in regular license. Please purchase extended license to charge your end customers']);
        } else {

            $getpayment = Payment::where('vendor_id', $vendor_id)->orderBy('reorder_id')->where('is_activate', 1)->get();


            return view('admin.payment.payment', compact("getpayment"));
        }
    }
    public function update(Request $request)
    {

        try {
            $data = Payment::find($request->transaction_type);

            if (empty($data)) {
                return redirect()->back()->with('error', trans('messages.wrong'));
            }

            if ((int) $data->payment_type === Payment::TYPE_STRIPE && (int) $request->is_available === 1) {
                $publicKey = trim((string) $request->public_key);
                $secretKey = trim((string) $request->secret_key);
                // Secret fields are masked in the UI: a blank submit means "keep the stored key".
                if ($publicKey === '') { $publicKey = (string) $data->public_key; }
                if ($secretKey === '') { $secretKey = (string) $data->secret_key; }
                $environment = (int) $request->environment;

                $validator = Validator::make([
                    'public_key' => $publicKey,
                    'secret_key' => $secretKey,
                    'currency' => $request->currency,
                ], [
                    'public_key' => 'required',
                    'secret_key' => 'required',
                    'currency' => 'required',
                ]);

                if ($validator->fails()) {
                    return redirect()->back()->with('error', trans('messages.wrong'))->withInput();
                }

                $expectedPublicPrefix = $environment === 2 ? 'pk_live_' : 'pk_test_';
                $expectedSecretPrefix = $environment === 2 ? 'sk_live_' : 'sk_test_';

                if (
                    !str_starts_with($publicKey, $expectedPublicPrefix) ||
                    !str_starts_with($secretKey, $expectedSecretPrefix) ||
                    substr_count($publicKey, $expectedPublicPrefix) !== 1 ||
                    substr_count($secretKey, $expectedSecretPrefix) !== 1
                ) {
                    return redirect()->back()
                        ->with('error', 'Stripe keys do not match the selected environment, or a key was pasted more than once.')
                        ->withInput();
                }

                $request->merge([
                    'public_key' => $publicKey,
                    'secret_key' => $secretKey,
                ]);
            }

            if (isset($request->is_available)) {
                $data->is_available = $request->is_available;
            } else {
                $data->is_available = 2;
            }
            if (in_array($data->payment_type, ['2', '3', '4', '5', '7', '8', '9', '10', '11', '12', '13', '14', '15'])) {
                $data->environment = @$request->environment != "" ? $request->environment : "";
                if (trim((string) $request->public_key) !== "") {
                    $data->public_key = trim($request->public_key);
                }
                // Blank secret => keep the existing (masked) key instead of wiping it.
                if (trim((string) $request->secret_key) !== "") {
                    $data->secret_key = trim($request->secret_key);
                }
                $data->currency = @$request->currency != "" ? strtoupper(trim($request->currency)) : "";
            }

            if ($data->payment_type == '4') {
                // Blank => keep the existing encryption key.
                if (trim((string) $request->encryption_key) !== "") {
                    $data->encryption_key = $request->encryption_key;
                }
            } else {
                $data->encryption_key = "";
            }

            if ($data->payment_type == '12') {
                $data->base_url_by_region = $request->base_url_by_region;
            } else {
                $data->base_url_by_region = "";
            }

            if ($request->image) {
                $validator = Validator::make($request->all(), [
                    'image' => 'image|max:' . helper::imagesize() . '|' . helper::imageext(),
                ], [
                    'image.max' => trans('messages.image_size_message'),
                ]);
                if ($validator->fails()) {
                    return redirect()->back()->with('error', trans('messages.image_size_message') . ' ' . helper::appdata('')->image_size . ' ' . 'MB');
                }
                if (env('Environment') == 'sendbox') {
                    return $this->sendError("This operation was not performed due to demo mode");
                }
                // The old file is only removed when there actually is one. Methods added in V2
                // (Cash, Cash on Pickup, BenefitPay, Bank QR, Payment Link) ship with an empty
                // `image`, so this used to build the bare folder path, file_exists() returned
                // true for the directory and unlink() threw "Operation not permitted" — which the
                // catch below turned into a generic "something went wrong" on every logo upload.
                self::deletePaymentFile($data->image, $data->payment_name . '.png');

                $image = 'payment-' . uniqid() . '.' . $request->image->getClientOriginalExtension();
                $request->image->move(env('ASSETSPATHURL') . 'admin-assets/images/about/payment/', $image);
                $data->image = $image;
            }
            $data->payment_name = $request->name;
            if ($data->payment_type == '6') {
                $data->payment_description = $request->payment_description;
            }
            // V2 manual/offline methods (Cash, Cash on Pickup, BenefitPay, Bank QR, Payment Link).
            if (in_array((int) $data->payment_type, [17, 18, 19, 20, 21], true)) {
                if ($request->hasFile('qr_image')) {
                    $validator = Validator::make($request->all(), [
                        'qr_image' => 'image|max:' . helper::imagesize() . '|' . helper::imageext(),
                    ], [
                        'qr_image.max' => trans('messages.image_size_message'),
                    ]);
                    if ($validator->fails()) {
                        return redirect()->back()->with('error', trans('messages.image_size_message') . ' ' . helper::appdata('')->image_size . ' ' . 'MB');
                    }
                    if (env('Environment') != 'sendbox') {
                        self::deletePaymentFile($data->qr_image);

                        $qr = 'payment-' . uniqid() . '.' . $request->qr_image->getClientOriginalExtension();
                        $request->qr_image->move(env('ASSETSPATHURL') . 'admin-assets/images/about/payment/', $qr);
                        $data->qr_image = $qr;
                    }
                }
                if ($request->has('payment_link')) {
                    $data->payment_link = $request->payment_link;
                }
                if ($request->has('payment_description')) {
                    $data->payment_description = $request->payment_description;
                }
            }
            $data->save();
            return redirect()->back()->with('success', trans('messages.success'));
        } catch (\Throwable $th) {
            \Log::error('Payment method update failed: ' . $th->getMessage(), [
                'payment_id' => $request->transaction_type,
                'file' => $th->getFile() . ':' . $th->getLine(),
            ]);

            return redirect()->back()->with('error', trans('messages.wrong'));
        }
    }

    /**
     * Remove a file from the payment image folder, but only when the stored name points at a
     * real file. An empty name would otherwise resolve to the folder itself, and a name matching
     * $keep is one of the shipped default logos that must stay in place.
     */
    private static function deletePaymentFile(?string $name, ?string $keep = null): void
    {
        $name = trim((string) $name);
        if ($name === '' || ($keep !== null && $name === $keep)) {
            return;
        }

        $path = env('ASSETSPATHURL') . 'admin-assets/images/about/payment/' . basename($name);
        if (is_file($path)) {
            @unlink($path);
        }
    }
    public function reorder_payment(Request $request)
    {

        if ($request->has('ids')) {


            $arr = explode('|', $request->input('ids'));
            foreach ($arr as $sortOrder => $id) {
                $menu = Payment::find($id);
                $menu->reorder_id = $sortOrder;
                $menu->save();
            }
        }

        return response()->json(['status' => 1, 'msg' => trans('messages.success')], 200);
    }
}
