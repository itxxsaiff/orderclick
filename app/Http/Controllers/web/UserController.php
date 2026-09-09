<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Helpers\helper;
use App\Models\User;
use App\Models\Settings;
use App\Models\Cart;
use App\Models\Payment;
use App\Models\SystemAddons;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Lunaweb\RecaptchaV3\Facades\RecaptchaV3;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Stripe;

class UserController extends Controller
{
    public function user_login(Request $request)
    {
        $host = $_SERVER['HTTP_HOST'];
        if ($host  ==  env('WEBSITE_HOST')) {
            $storeinfo = helper::storeinfo($request->vendor);
            $vdata = $storeinfo->id;
        }
        // if the current host doesn't contain the website domain (meaning, custom domain)
        else {
            $storeinfocustom = Settings::where('custom_domain', $host)->first();
            $vendorinfo = User::where('id', $storeinfocustom->vendor_id)->first();
            $storeinfo = helper::storeinfo($vendorinfo->slug);
            $vdata = $storeinfocustom->vendor_id;
        }
        if (SystemAddons::where('unique_identifier', 'customer_login')->first() != null && SystemAddons::where('unique_identifier', 'customer_login')->first()->activated == 1) {
            if (helper::appdata(@$vdata)->checkout_login_required == 1) {
                $slug = $request->vendor;
                return view('front.auth.login', compact('slug', 'vdata', 'storeinfo'));
            } else {
                abort(404);
            }
        } else {
            abort(404);
        }
    }

    public function user_register(Request $request)
    {
        $host = $_SERVER['HTTP_HOST'];
        if ($host  ==  env('WEBSITE_HOST')) {
            $storeinfo = helper::storeinfo($request->vendor);
            $vdata = $storeinfo->id;
        }
        // if the current host doesn't contain the website domain (meaning, custom domain)
        else {
            $storeinfocustom = Settings::where('custom_domain', $host)->first();
            $vendorinfo = User::where('id', $storeinfocustom->vendor_id)->first();
            $storeinfo = helper::storeinfo($vendorinfo->slug);
            $vdata = $storeinfocustom->vendor_id;
        }
        if (SystemAddons::where('unique_identifier', 'customer_login')->first() != null && SystemAddons::where('unique_identifier', 'customer_login')->first()->activated == 1) {
            if (helper::appdata(@$vdata)->checkout_login_required == 1) {
                $slug = $request->vendor;
                return view('front.auth.register', compact('slug', 'vdata', 'storeinfo'));
            } else {
                abort(404);
            }
        } else {
            abort(404);
        }
    }

    public function userforgotpassword(Request $request)
    {
        $host = $_SERVER['HTTP_HOST'];
        if ($host  ==  env('WEBSITE_HOST')) {
            $storeinfo = helper::storeinfo($request->vendor);
            $vdata = $storeinfo->id;
        }
        // if the current host doesn't contain the website domain (meaning, custom domain)
        else {
            $storeinfocustom = Settings::where('custom_domain', $host)->first();
            $vendorinfo = User::where('id', $storeinfocustom->vendor_id)->first();
            $storeinfo = helper::storeinfo($vendorinfo->slug);
            $vdata = $storeinfocustom->vendor_id;
        }
        if (SystemAddons::where('unique_identifier', 'customer_login')->first() != null && SystemAddons::where('unique_identifier', 'customer_login')->first()->activated == 1) {
            if (helper::appdata(@$vdata)->checkout_login_required == 1) {
                $slug = $request->vendor;
                return view('front.auth.forgotpassword', compact('slug', 'vdata', 'storeinfo'));
            } else {
                abort(404);
            }
        } else {
            abort(404);
        }
    }
    public function send_password(Request $request)
    {
        $host = $_SERVER['HTTP_HOST'];
        if ($host  ==  env('WEBSITE_HOST')) {
            $storeinfo = helper::storeinfo($request->vendor);
            $vdata = $storeinfo->id;
        }
        // if the current host doesn't contain the website domain (meaning, custom domain)
        else {
            $storeinfo = Settings::where('custom_domain', $host)->first();
            $vdata = $storeinfo->vendor_id;
        }

        $request->validate([
            'email' => 'required|email',
        ], [
            'email.required' => trans('messages.email_required'),
            'email.email' => trans('messages.invalid_email'),
        ]);
        $checkuser = User::where('email', $request->email)->where('is_available', 1)->where('type', 3)->where('vendor_id', $vdata)->first();
        if (!empty($checkuser)) {
            $password = substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 1, 6);
            $emaildata = helper::emailconfigration($vdata);
            Config::set('mail', $emaildata);
            $pass = helper::send_pass($request->email, $checkuser->name, $password, '1');
            if ($pass == 1) {
                $checkuser->password = Hash::make($password);
                $checkuser->save();
                return redirect(URL::to($storeinfo->slug . '/login'))->with('success', trans('messages.success'));
            } else {
                return redirect(URL::to($storeinfo->slug . '/forgotpassword'))->with('error', trans('messages.wrong'));
            }
        } else {
            return redirect()->back()->with('error', trans('messages.invalid_user'));
        }
    }
    public function register_customer(Request $request)
    {
        $host = $_SERVER['HTTP_HOST'];
        if ($host  ==  env('WEBSITE_HOST')) {
            $storeinfo = helper::storeinfo($request->vendor);
            $vdata = $storeinfo->id;
        }
        // if the current host doesn't contain the website domain (meaning, custom domain)
        else {
            $storeinfocustom = Settings::where('custom_domain', $host)->first();
            $vendorinfo = User::where('id', $storeinfocustom->vendor_id)->first();
            $storeinfo = helper::storeinfo($vendorinfo->slug);
            $vdata = $storeinfocustom->vendor_id;
        }

        $validatoremail = Validator::make(['email' => $request->email], [
            'email' => [
                'required',
                'email',
                Rule::unique('users')->where('vendor_id', $vdata)->where('is_deleted', 2)->where('type', 3),
            ]
        ]);
        if ($validatoremail->fails()) {
            return redirect()->back()->with('error', trans('messages.unique_email'));
        }
        $validatormobile = Validator::make(['mobile' => $request->mobile], [
            'mobile' => [
                'required',
                'numeric',
                Rule::unique('users')->where('vendor_id', $vdata)->where('is_deleted', 2)->where('type', 3),
            ]
        ]);
        if ($validatormobile->fails()) {
            return redirect()->back()->with('error', trans('messages.unique_mobile'));
        }
        if (
            SystemAddons::where('unique_identifier', 'google_recaptcha')->first() != null &&
            SystemAddons::where('unique_identifier', 'google_recaptcha')->first()->activated == 1
        ) {
            if (helper::appdata('')->recaptcha_version == 'v2') {
                $request->validate([
                    'g-recaptcha-response' => 'required'
                ], [
                    'g-recaptcha-response.required' => 'The g-recaptcha-response field is required.'
                ]);
            }

            if (helper::appdata('')->recaptcha_version == 'v3') {
                $score = RecaptchaV3::verify($request->get('g-recaptcha-response'), 'contact');
                if ($score <= helper::appdata('')->score_threshold) {
                    return redirect()->back()->with('error', 'You are most likely a bot');
                }
            }
        }



        $old_sid = session()->get('old_session_id');

        $newuser = new User();
        $newuser->name = $request->name;
        $newuser->email = $request->email;
        $newuser->vendor_id = $vdata;
        $newuser->password = hash::make($request->password);
        $newuser->mobile = $request->mobile;
        $newuser->type = "3";
        $newuser->login_type = "email";
        $newuser->image = "default-logo.png";
        $newuser->is_available = "1";
        $newuser->is_verified = "1";
        $newuser->save();


        Auth::login($newuser);

        Cart::where('session_id', $old_sid)->update(['user_id' => @Auth::user()->id, 'session_id' => NULL]);

        $count = Cart::where('user_id', @Auth::user()->id)->count();

        session()->put('cart', $count);


        $host = $_SERVER['HTTP_HOST'];
        if ($host  ==  env('WEBSITE_HOST')) {
            return redirect($request->vendor)->with('success', trans('messages.success'));
        }
        // if the current host doesn't contain the website domain (meaning, custom domain)
        else {
            return redirect('/')->with('sucess', trans('messages.success'));
        }
    }

    public function check_login(Request $request)
    {
        $host = $_SERVER['HTTP_HOST'];
        if ($host  ==  env('WEBSITE_HOST')) {
            $storeinfo = helper::storeinfo($request->vendor);
            $vdata = $storeinfo->id;
        }
        // if the current host doesn't contain the website domain (meaning, custom domain)
        else {
            $storeinfo = Settings::where('custom_domain', $host)->first();
            $vdata = $storeinfo->vendor_id;
        }

        try {
            if ($request->logintype == "normal") {
                $request->validate([
                    'email' => 'required|email',
                    'password' => 'required',
                ], [
                    'email.required' => trans('messages.email_required'),
                    'email.email' =>  trans('messages.invalid_email'),
                    'password.required' => trans('messages.password_required'),
                ]);
                $old_sid = session()->get('old_session_id');
                session()->put('user_login', '1');
                if (Auth::attempt(['email' => $request->email, 'password' => $request->password, 'vendor_id' => $vdata, 'is_deleted' => 2, 'type' => 3])) {
                    if (Auth::user()->type == 3 && Auth::user()->is_deleted == 2) {
                        if (Auth::user()->is_available == 1) {
                            session()->put('old_sid', $old_sid);

                            Cart::where('session_id', $old_sid)->update(['user_id' => Auth::user()->id, 'session_id' => NULL]);

                            $count = Cart::where('user_id', Auth::user()->id)->count();

                            session()->put('cart', $count);

                            session()->put('vendor_id', $vdata);
                            session()->forget('user_login', '1');
                            $host = $_SERVER['HTTP_HOST'];
                            if ($host  ==  env('WEBSITE_HOST')) {
                                return redirect('/' . $request->vendor)->with('sucess', trans('messages.success'));
                            }
                            // if the current host doesn't contain the website domain (meaning, custom domain)
                            else {
                                return redirect('/')->with('sucess', trans('messages.success'));
                            }
                        } else {
                            Auth::logout();
                            return redirect()->back()->with('error', trans('messages.block'));
                        }
                    } else {
                        Auth::logout();
                        return redirect()->back()->with('error', trans('messages.email_password_not_match'));
                    }
                } else {
                    return redirect()->back()->with('error', trans('messages.email_password_not_match'));
                }
            }
        } catch (\Exception $exception) {
            return back()->withError($exception->getMessage())->withInput();
        }
    }
    public function logout(Request $request)
    {
        session()->flush();
        Auth::logout();
        $host = $_SERVER['HTTP_HOST'];
        if ($host  ==  env('WEBSITE_HOST')) {
            return redirect($request->vendor);
        }
        // if the current host doesn't contain the website domain (meaning, custom domain)
        else {
            return redirect('/')->with('sucess', trans('messages.success'));
        }
    }

    public function profile(Request $request)
    {
        $host = $_SERVER['HTTP_HOST'];
        if ($host  ==  env('WEBSITE_HOST')) {
            $storeinfo = helper::storeinfo($request->vendor);
            $vdata = $storeinfo->id;
        }
        // if the current host doesn't contain the website domain (meaning, custom domain)
        else {
            $storeinfocustom = Settings::where('custom_domain', $host)->first();
            $vendorinfo = User::where('id', $storeinfocustom->vendor_id)->first();
            $storeinfo = helper::storeinfo($vendorinfo->slug);
            $vdata = $storeinfocustom->vendor_id;
        }
        return view('front.profile', compact('vdata', 'vdata', 'storeinfo'));
    }

    public function updateprofile(Request $request)
    {
        try {
            $host = $_SERVER['HTTP_HOST'];
            if ($host  ==  env('WEBSITE_HOST')) {
                $storeinfo = helper::storeinfo($request->vendor);
                $vdata = $storeinfo->id;
            }
            // if the current host doesn't contain the website domain (meaning, custom domain)
            else {
                $storeinfo = Settings::where('custom_domain', $host)->first();
                $vdata = $storeinfo->vendor_id;
            }
            $edituser = User::where('id', $request->id)->first();
            $validatoremail = Validator::make(['email' => $request->email], [
                'email' => [
                    'required',
                    'email',
                    Rule::unique('users')->where('vendor_id', $vdata)->where('is_deleted', 2)->where('type', 3)->ignore($edituser->id),
                ]
            ]);
            if ($validatoremail->fails()) {
                return redirect()->back()->with('error', trans('messages.unique_email'));
            }
            $validatormobile = Validator::make(['mobile' => $request->mobile], [
                'mobile' => [
                    'required',
                    'numeric',
                    Rule::unique('users')->where('vendor_id', $vdata)->where('is_deleted', 2)->where('type', 3)->ignore($edituser->id),
                ]
            ]);
            if ($validatormobile->fails()) {
                return redirect()->back()->with('error', trans('messages.unique_mobile'));
            }
            $edituser->name = $request->name;
            $edituser->email = $request->email;
            $edituser->mobile = $request->mobile;
            $edituser->vendor_id = $vdata;
            if ($request->has('profile')) {
                $validator = Validator::make($request->all(), [
                    'profile' => 'image|max:' . helper::imagesize() . '|' . helper::imageext(),
                ], [
                    'profile.max' => trans('messages.image_size_message'),
                ]);
                if ($validator->fails()) {
                    return redirect()->back()->with('error', trans('messages.image_size_message') . ' ' . helper::appdata('')->image_size . ' ' . 'MB');
                }
                if (env('Environment') == 'sendbox') {
                    return $this->sendError("This operation was not performed due to demo mode");
                }
                if ($edituser->image != "" && file_exists(storage_path('app/public/admin-assets/images/profile/' . $edituser->image))) {
                    unlink(storage_path('app/public/admin-assets/images/profile/' . $edituser->image));
                }
                $edit_image = $request->file('profile');
                $profileImage = 'profile-' . uniqid() . "." . $edit_image->getClientOriginalExtension();
                $edit_image->move(storage_path('app/public/admin-assets/images/profile/'), $profileImage);
                $edituser->image = $profileImage;
            }
            $edituser->update();
            if ($edituser) {
                return redirect($request->vendor . '/profile')->with('success', trans('messages.success'));
            } else {
                return redirect($request->vendor . '/profile')->with('error', trans('messages.wrong'));
            }
        } catch (\Throwable $th) {
            dd($th->getMessage());
        }
    }

    public function changepassword(Request $request)
    {
        $host = $_SERVER['HTTP_HOST'];
        if ($host  ==  env('WEBSITE_HOST')) {
            $storeinfo = helper::storeinfo($request->vendor);
            $vdata = $storeinfo->id;
        }
        // if the current host doesn't contain the website domain (meaning, custom domain)
        else {
            $storeinfocustom = Settings::where('custom_domain', $host)->first();
            $vendorinfo = User::where('id', $storeinfocustom->vendor_id)->first();
            $storeinfo = helper::storeinfo($vendorinfo->slug);
            $vdata = $storeinfocustom->vendor_id;
        }
        return view('front.change-password', compact('vdata', 'storeinfo'));
    }

    public function change_password(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required',
            'confirm_password' => 'required',
        ], [
            'current_password.required' => trans('messages.cuurent_password_required'),
            'new_password.required' => trans('messages.new_password_required'),
            'confirm_password.required' => trans('messages.confirm_password_required'),
        ]);
        if (Hash::check($request->current_password, Auth::user()->password)) {
            if ($request->current_password == $request->new_password) {
                return redirect()->back()->with('error', trans('messages.new_old_password_diffrent'));
            } else {
                if ($request->new_password == $request->confirm_password) {
                    $changepassword = User::where('id', Auth::user()->id)->first();
                    $changepassword->password = Hash::make($request->new_password);
                    $changepassword->update();
                    return redirect()->back()->with('success', trans('messages.success'));
                } else {
                    return redirect()->back()->with('error', trans('messages.new_confirm_password_inccorect'));
                }
            }
        } else {
            return redirect()->back()->with('error', trans('messages.old_password_incorect'));
        }
    }

    public function orders(Request $request)
    {
        $host = $_SERVER['HTTP_HOST'];
        if ($host  ==  env('WEBSITE_HOST')) {
            $storeinfo = helper::storeinfo($request->vendor);
            $vdata = $storeinfo->id;
        }
        // if the current host doesn't contain the website domain (meaning, custom domain)
        else {
            $storeinfocustom = Settings::where('custom_domain', $host)->first();
            $vendorinfo = User::where('id', $storeinfocustom->vendor_id)->first();
            $storeinfo = helper::storeinfo($vendorinfo->slug);
            $vdata = $storeinfocustom->vendor_id;
        }
        $getorders = Order::where('user_id', Auth::user()->id)->where('vendor_id', $vdata);

        if (!empty($request->type)) {
            if ($request->type == "cancelled") {
                $getorders = $getorders->where('status_type',  4);
            }
            if ($request->type == "preparing") {
                $getorders = $getorders->whereIn('status_type', [1, 2]);
            }
            if ($request->type == "delivered") {
                $getorders = $getorders->where('status_type', 3);
            }
        }
        $getorders = $getorders->orderByDesc('id')->get();
        $totalprocessing = Order::where('user_id', Auth::user()->id)->whereIn('status_type', [1, 2])->where('vendor_id', $vdata)->count();
        $totalrejected = Order::where('user_id', Auth::user()->id)->where('status_type',  4)->where('vendor_id', $vdata)->count();
        $totalcompleted = Order::where('user_id', Auth::user()->id)->where('status_type', 3)->where('vendor_id', $vdata)->count();
        return view('front.orders', compact('vdata', 'storeinfo', 'getorders', 'totalprocessing', 'totalrejected', 'totalcompleted'));
    }

    public function loyality(Request $request)
    {
        $host = $_SERVER['HTTP_HOST'];
        if ($host  ==  env('WEBSITE_HOST')) {
            $storeinfo = helper::storeinfo($request->vendor);
            $vdata = $storeinfo->id;
        }
        // if the current host doesn't contain the website domain (meaning, custom domain)
        else {
            $storeinfocustom = Settings::where('custom_domain', $host)->first();
            $vendorinfo = User::where('id', $storeinfocustom->vendor_id)->first();
            $storeinfo = helper::storeinfo($vendorinfo->slug);
            $vdata = $storeinfocustom->vendor_id;
        }

        $slug = $request->vendor;

        return view('front.loyality', compact('slug', 'vdata', 'storeinfo'));
    }

    public function deletepassword(Request $request)
    {
        $host = $_SERVER['HTTP_HOST'];
        if ($host  ==  env('WEBSITE_HOST')) {
            $storeinfo = helper::storeinfo($request->vendor);
            $vdata = $storeinfo->id;
        }
        // if the current host doesn't contain the website domain (meaning, custom domain)
        else {
            $storeinfocustom = Settings::where('custom_domain', $host)->first();
            $vendorinfo = User::where('id', $storeinfocustom->vendor_id)->first();
            $storeinfo = helper::storeinfo($vendorinfo->slug);
            $vdata = $storeinfocustom->vendor_id;
        }
        return view('front.delete', compact('vdata', 'storeinfo'));
    }
    public function deleteaccount(Request $request)
    {

        if (Auth::user() && Auth::user()->type == 3) {
            $user  = User::where('id', Auth::user()->id)->first();
            $user->is_deleted = 1;
            $user->update();
            $emaildata = helper::emailconfigration('');
            Config::set('mail', $emaildata);
            helper::send_mail_delete_account($user);
            session()->flush();
            Auth::logout();
            $host = $_SERVER['HTTP_HOST'];
            if ($host  ==  env('WEBSITE_HOST')) {
                return redirect($request->vendor);
            }
            // if the current host doesn't contain the website domain (meaning, custom domain)
            else {
                return redirect('/')->with('sucess', trans('messages.success'));
            }
        }
    }


    public function wallet(Request $request)
    {
        $host = $_SERVER['HTTP_HOST'];
        if ($host  ==  env('WEBSITE_HOST')) {
            $storeinfo = helper::storeinfo($request->vendor);
            $vdata = $storeinfo->id;
        }
        // if the current host doesn't contain the website domain (meaning, custom domain)
        else {
            $storeinfo = Settings::where('custom_domain', $host)->first();
            $vdata = $storeinfo->vendor_id;
        }
        if (empty($storeinfo)) {
            abort(404);
        }
        if (
            SystemAddons::where('unique_identifier', 'customer_login')->first() != null &&
            SystemAddons::where('unique_identifier', 'customer_login')->first()->activated == 1
        ) {
            if (helper::appdata($vdata)->checkout_login_required == 1) {
                $gettransactions = Transaction::where('user_id', Auth::user()->id)->orderByDesc('id')->paginate(10);
                return view('front.wallet', compact('storeinfo', 'vdata', 'gettransactions'));
            } else {
                abort(404);
            }
        }
    }
    public function addmoneywallet(Request $request)
    {
        $host = $_SERVER['HTTP_HOST'];
        if ($host  ==  env('WEBSITE_HOST')) {
            $storeinfo = helper::storeinfo($request->vendor);
            $vdata = $storeinfo->id;
        }
        // if the current host doesn't contain the website domain (meaning, custom domain)
        else {
            $storeinfo = Settings::where('custom_domain', $host)->first();
            $vdata = $storeinfo->vendor_id;
        }
        if (empty($storeinfo)) {
            abort(404);
        }
        if (
            SystemAddons::where('unique_identifier', 'customer_login')->first() != null &&
            SystemAddons::where('unique_identifier', 'customer_login')->first()->activated == 1
        ) {
            if (helper::appdata($vdata)->checkout_login_required == 1) {
                $getpaymentmethods = Payment::select('id', 'environment', 'unique_identifier', 'payment_name', 'payment_type', 'currency', 'public_key', 'secret_key', 'encryption_key', 'image')
                    ->whereNotIn('payment_type', array(1, 6, 16))->where('vendor_id', $vdata)->where('is_available', 1)->where('is_activate', '1')->orderBy('reorder_id')->get();
                return view('front.addmoney', compact('storeinfo', 'vdata', 'getpaymentmethods'));
            } else {
                abort(404);
            }
        }
    }

    public function addwallet(Request $request)
    {
        if (empty($request->transaction_type) && empty($request->amount)) {
            $vendor_id = Session::get('vendor_id');
            $amount = Session::get('grand_total');
            $transaction_type = Session::get('payment_type');
        } else {
            $vendor_id = $request->vendor_id;
            $amount = $request->amount;
            $transaction_type = $request->payment_type;
        }

        try {
            $checkuser = User::where('id', Auth::user()->id)->where('is_available', 1)->where('is_deleted', 2)->where('type', 3)->first();
            if (empty($checkuser)) {
                return response()->json(["status" => 0, "message" => trans('messages.invalid_user')], 200);
            }
            if ($transaction_type == "") {
                return response()->json(["status" => 0, "message" => trans('messages.payment_selection_required')], 200);
            }
            if ($amount == "") {
                return response()->json(["status" => 0, "message" => trans('messages.enter_amount')], 200);
            }
            if ($transaction_type == 3) {
                $getstripe = Payment::select('environment', 'secret_key', 'currency')->where('payment_type', 3)->where('vendor_id', $request->vendor_id)->first();
                $skey = $getstripe->secret_key;
                $token = $request->transaction_id;
                try {
                    Stripe\Stripe::setApiKey($skey);
                    $charge = Stripe\Charge::create([
                        'amount' => $amount * 100,
                        'currency' => $getstripe->currency,
                        'description' => 'Restro',
                        'source' => $token,
                    ]);
                    $transaction_id = $charge['id'];
                } catch (\Throwable $th) {
                    dd($th);
                    return response()->json(['status' => 0, 'message' => trans('messages.unable_to_complete_payment')], 200);
                }
            } else {
                if ($request->transaction_id == "") {
                    return response()->json(["status" => 0, "message" => trans('messages.enter_transaction_id')], 200);
                }
                $transaction_id = $request->transaction_id;
            }
            // 2 = added-money-wallet-using- Razorpay 
            // 3 = added-money-wallet-using- Stripe 
            // 4 = added-money-wallet-using- Flutterwave 
            // 5 = added-money-wallet-using- Paystack
            // 7 = added-money-wallet-using- mercadopago
            // 8 = added-money-wallet-using- paypal
            // 9 = added-money-wallet-using- myfatoorah
            // 10 = added-money-wallet-using- toyyibpay
            // 11 = added-money-wallet-using- phonepe
            // 12 = added-money-wallet-using- paytab
            // 13 = added-money-wallet-using- mollie
            // 14 = added-money-wallet-using- khalti
            // 15 = added-money-wallet-using- xendit

            $transaction = new Transaction();
            $transaction->vendor_id = $vendor_id;
            $transaction->user_id = $checkuser->id;
            $transaction->payment_id = $transaction_id;
            $transaction->payment_type = $transaction_type;
            $transaction->transaction_type = 1;
            $transaction->amount = $amount;
            $transaction->save();
            $checkuser->wallet += $amount;
            $checkuser->save();

            if ($transaction_type == 7 || $transaction_type == 8 || $transaction_type == 9 || $transaction_type == 10 || $transaction_type == 11 || $transaction_type == 12 || $transaction_type == 13 || $transaction_type == 14 || $transaction_type == 15) {
                return redirect(Session::get('slug') . '/' . 'wallet')->with('success', trans('messages.add_money_success'));
            }
            return response()->json(['status' => 1, 'message' => trans('messages.success')], 200);
        } catch (\Throwable $th) {
            dd($th);
            return response()->json(['status' => 0, 'message' => trans('messages.wrong')], 200);
        }
    }

    public function addsuccess(Request $request)
    {
        try {
            if ($request->has('paymentId')) {
                $paymentId = request('paymentId');
                $response = ['status' => 1, 'msg' => 'paid', 'transaction_id' => $paymentId];
            }
            if ($request->has('payment_id')) {
                $paymentId = request('payment_id');
                $response = ['status' => 1, 'msg' => 'paid', 'transaction_id' => $paymentId];
            }

            if ($request->has('transaction_id')) {
                $paymentId = request('transaction_id');
                $response = ['status' => 1, 'msg' => 'paid', 'transaction_id' => $paymentId];
            }
            if (session()->get('payment_type') == "11") {
                if ($request->code == "PAYMENT_SUCCESS") {
                    $paymentId = $request->transactionId;
                    $response = ['status' => 1, 'msg' => 'paid', 'transaction_id' => $paymentId];
                } else {
                    return redirect(Session::get('slug') . '/' . ' wallet')->with('error', trans('messages.unable_to_complete_payment'));
                }
            }
            if (session()->get('payment_type') == "12") {
                $checkstatus = app('App\Http\Controllers\addons\PayTabController')->checkpaymentstatus(Session::get('tran_ref'), Session::get('vendor_id'));
                if ($checkstatus == "A") {
                    $paymentId = Session::get('tran_ref');
                    $response = ['status' => '1', 'msg' => 'paid', 'transaction_id' => $paymentId];
                } else {
                    return redirect(Session::get('failureurl'))->with('error', session()->get('paytab_response'));
                }
            }


            if (session()->get('payment_type') == "13") {
                $checkstatus = app('App\Http\Controllers\addons\MollieController')->checkpaymentstatus(Session::get('tran_ref'), Session::get('vendor_id'));

                if ($checkstatus == "A") {
                    $paymentId = Session::get('tran_ref');
                    $response = ['status' => 1, 'msg' => 'paid', 'transaction_id' => $paymentId];
                } else {
                    return redirect(Session::get('failureurl'))->with('error', session()->get('paytab_response'));
                }
            }

            if (session()->get('payment_type') == "14") {

                if ($request->status == "Completed") {
                    $paymentId = $request->transaction_id;
                    $response = ['status' => 1, 'msg' => 'paid', 'transaction_id' => $paymentId];
                } else {
                    return redirect(Session::get('failureurl'))->with('error', session()->get('paytab_response'));
                }
            }

            if (session()->get('payment_type') == "15") {

                $checkstatus = app('App\Http\Controllers\addons\XenditController')->checkpaymentstatus(Session::get('tran_ref'), Session::get('vendor_id'));

                if ($checkstatus == "PAID") {
                    $paymentId = Session::get('payment_id');
                    $response = ['status' => 1, 'msg' => 'paid', 'transaction_id' => $paymentId];
                } else {
                    return redirect(Session::get('failureurl'))->with('error', session()->get('paytab_response'));
                }
            }
        } catch (\Exception $e) {
            dd($e);
            $response = ['status' => 0, 'msg' => $e->getMessage()];
        }

        $request = new Request($response);
        return $this->addwallet($request);
    }

    public function addfail()
    {
        if (count(request()->all()) > 0) {
            return redirect(Session::get('slug') . '/' . 'wallet')->with('error', trans('messages.unable_to_complete_payment'));
        } else {
            return redirect(Session::get('slug') . '/' . 'wallet');
        }
    }
}
