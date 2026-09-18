<?php

namespace App\Http\Controllers\web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Category;
use App\Models\Item;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Settings;
use App\Models\Subscriber;
use App\Models\User;
use App\Models\Timing;
use App\Models\Payment;
use App\Models\Contact;
use App\Models\Terms;
use App\Models\About;
use App\Models\Privacypolicy;
use App\Models\Banner;
use App\Models\CustomStatus;
use App\Models\SystemAddons;
use App\Helpers\helper;
use App\Models\Blog;
use App\Models\TopDeals;
use App\Models\ItemImages;
use App\Models\RefundPrivacypolicy;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Charge;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\TableQR;
use App\Models\Variants;
use Illuminate\Support\Facades\URL;
use Lunaweb\RecaptchaV3\Facades\RecaptchaV3;
use App\Helpers\whatsapp_helper;
use App\Models\Faq;
use App\Models\Promocode;
use App\Models\QuestionAnswer;
use App\Models\Shipping;
use App\Models\Testimonials;
use App\Models\Transaction;
use App\Models\WhoWeAre;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{

    public function index(Request $request)
    {
        if ($request->tid) {
            Session::put('table_id', $request->tid);
        }
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
        // V2: booking stores get a dedicated storefront (services grid, no cart/products).
        // Query Settings directly by vendor_id — appdata() is host/port-sensitive and unreliable here.
        $storeSettings = Settings::where('vendor_id', $vdata)->first();
        if (in_array(optional($storeSettings)->business_type, ['booking', 'clinic', 'salon'])) {
            $services = \App\Models\BookingService::where('vendor_id', $vdata)->where('is_available', 1)
                ->orderBy('reorder_id')->orderByDesc('id')->get();
            $categories = $services->pluck('category')->filter()->unique()->values();
            $timings = \App\Models\Timing::where('vendor_id', $vdata)->orderBy('id')->get();
            // Clinics also list doctors + patient reviews on the storefront.
            $doctors = \App\Models\Doctor::where('vendor_id', $vdata)->where('is_available', 1)
                ->orderBy('reorder_id')->orderByDesc('id')->get();
            $storereview = \App\Models\Testimonials::where('vendor_id', $vdata)->where('status', 1)->get();
            return $this->themedView($vdata, 'index', 'front.booking.storefront', compact('storeinfo', 'vdata', 'services', 'storeSettings', 'categories', 'timings', 'doctors', 'storereview'));
        }

        $getcategory = Category::where('vendor_id', $vdata)->where('is_available', '=', '1')->where('is_deleted', '2')->orderBy('reorder_id')->get();
        if (Auth::user() && Auth::user()->type == 3) {
            $user_id = Auth::user()->id;
            $getitem = Item::with(['variation', 'extras', 'item_image', 'multi_image'])->select('items.*', DB::raw('(case when favorite.item_id is null then 0 else 1 end) as is_favorite'))
                ->leftJoin('favorite', function ($query) use ($user_id) {
                    $query->on('favorite.item_id', '=', 'items.id')
                        ->where('favorite.user_id', '=', $user_id);
                })
                ->where('items.vendor_id', $vdata)
                ->where('is_available', '1')->orderBy('reorder_id', 'ASC')->get();

            $topdealsproducts = Item::with(['variation', 'extras', 'item_image', 'multi_image'])->select('items.*', DB::raw('(case when favorite.item_id is null then 0 else 1 end) as is_favorite'))
                ->leftJoin('favorite', function ($query) use ($user_id) {
                    $query->on('favorite.item_id', '=', 'items.id')
                        ->where('favorite.user_id', '=', $user_id);
                })->where('items.top_deals', '1')
                ->where('items.vendor_id', $vdata)
                ->where('is_available', '1')
                ->orderBy('reorder_id', 'ASC')->get();
        } else {
            $getitem = Item::with(['variation', 'extras', 'item_image', 'multi_image'])->where('vendor_id', $vdata)->where('is_available', '1')->orderBy('reorder_id')->get();
            $topdealsproducts = Item::with(['variation', 'extras', 'item_image', 'multi_image'])->where('items.top_deals', '1')->where('vendor_id', $vdata)->where('is_available', '1')->orderBy('reorder_id')->get();
        }
        $paymentlist = Payment::where('vendor_id', $vdata)->where('is_available', 1)->where('is_activate', '1')->get();
        $settingdata = Settings::where('vendor_id', $vdata)->select('template')->first();
        if (empty($settingdata) || empty($settingdata->template)) {
            $settingdata = (object) [
                'template' => optional(helper::appdata($vdata))->template ?: 1,
            ];
        }

        $bannerimage = Banner::where('vendor_id', $vdata)->orderBy('reorder_id')->get();
        $cartitems = Cart::select('id', 'item_id', 'item_name', 'item_image', 'item_price', 'extras_id', 'extras_name', 'extras_price', 'qty', 'price', 'tax', 'variants_id', 'variants_name')
            ->where('vendor_id', $vdata);
        if (Auth::user() && Auth::user()->type == 3) {
            $cartitems->where('user_id', @Auth::user()->id);
        } else {
            $cartitems->where('session_id', Session::getId());
        }
        $blogs = Blog::orderBy('reorder_id')->where('vendor_id', $vdata)->get();
        $whowearedata = WhoWeAre::orderBy('reorder_id')->where('vendor_id', $vdata)->get();
        $storereview = Testimonials::where('vendor_id', $vdata)->where('user_id', null)->where('item_id', null)->orderBy('reorder_id')->get();

        $cartdata = $cartitems->get();
        if (empty($storeinfo)) {

            abort(404);
        }
        if (Auth::user() && Auth::user()->type == 3) {
            $count = Cart::where('user_id', Auth::user()->id)->where('vendor_id', $vdata)->count();
        } else {
            $count = Cart::where('session_id', Session::getId())->where('vendor_id', $vdata)->count();
        }
        session()->put('cart', $count);
        $topdeals = TopDeals::where('vendor_id', $vdata)->first();
        if ($request->is($request->vendor . '/pwa')) {
            return view('front.themepwa', compact('getcategory', 'paymentlist', 'getitem', 'vdata', 'storeinfo', 'bannerimage', 'cartdata', 'whowearedata', 'blogs', 'storereview', 'topdealsproducts', 'topdeals'));
        } else {
            // V2: honour the merchant's chosen template (register picker); fall back to a
            // premium theme by business type; then to the basic default.
            $chosen = (int) ($settingdata->template ?? 0);
            $premiumByType = ['food' => 3, 'grocery' => 5, 'retail' => 6, 'pharmacy' => 7];
            $bt = optional($storeSettings)->business_type;
            if ($chosen && \Illuminate\Support\Facades\View::exists('front.template-' . $chosen . '.index')) {
                $template = $chosen;
            } elseif (isset($premiumByType[$bt]) && \Illuminate\Support\Facades\View::exists('front.template-' . $premiumByType[$bt] . '.index')) {
                $template = $premiumByType[$bt];
            } else {
                $template = 1;
            }
            $templateView = 'front.template-' . $template . '.index';
            if (!\Illuminate\Support\Facades\View::exists($templateView)) {
                $templateView = 'front.template-1.index';
            }
            return view($templateView, compact('getcategory', 'paymentlist', 'getitem', 'vdata', 'storeinfo', 'bannerimage', 'cartdata', 'whowearedata', 'blogs', 'storereview', 'topdealsproducts', 'topdeals'));
        }
    }

    public function categories(Request $request)
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
        $getcategory = Category::where('vendor_id', $vdata)->where('is_available', '=', '1')->where('is_deleted', '2')->orderBy('reorder_id', 'ASC')->get();
        if (Auth::user() && Auth::user()->type == 3) {
            $user_id = Auth::user()->id;
            $getitem = Item::with(['variation', 'extras', 'item_image'])->select('items.*', DB::raw('(case when favorite.item_id is null then 0 else 1 end) as is_favorite'))
                ->leftJoin('favorite', function ($query) use ($user_id) {
                    $query->on('favorite.item_id', '=', 'items.id')
                        ->where('favorite.user_id', '=', $user_id);
                })->where('items.vendor_id', $vdata)->where('is_available', '1')->orderBy('reorder_id', 'ASC')->get();
        } else {
            $getitem = Item::with(['variation', 'extras', 'item_image'])->where('vendor_id', $vdata)->where('is_available', '1')->orderBy('reorder_id', 'ASC')->get();
        }
        $bannerimage = Banner::where('vendor_id', $vdata)->orderBy('reorder_id')->get();
        $cartitems = Cart::select('id', 'item_id', 'item_name', 'item_image', 'item_price', 'extras_id', 'extras_name', 'extras_price', 'qty', 'price', 'tax', 'variants_id', 'variants_name')
            ->where('vendor_id', $vdata);
        if (Auth::user() && Auth::user()->type == 3) {
            $cartitems->where('user_id', @Auth::user()->id);
        } else {
            $cartitems->where('session_id', Session::getId());
        }
        $blogs = Blog::orderBy('reorder_id')->where('vendor_id', $vdata)->get();
        $cartdata = $cartitems->get();
        if (empty($storeinfo)) {

            abort(404);
        }
        if (Auth::user() && Auth::user()->type == 3) {
            $count = Cart::where('user_id', Auth::user()->id)->where('vendor_id', $vdata)->count();
        } else {
            $count = Cart::where('session_id', Session::getId())->where('vendor_id', $vdata)->count();
        }
        session()->put('cart', $count);

        return $this->themedView($vdata, 'category', 'front.template-3.category', compact('getcategory', 'getitem', 'vdata', 'storeinfo', 'bannerimage', 'cartdata', 'blogs'));
    }
    public function user_subscribe(Request $request)
    {
        try {
            $host = $_SERVER['HTTP_HOST'];
            if ($host  ==  env('WEBSITE_HOST')) {
                $vdata = $request->id;
            }
            // if the current host doesn't contain the website domain (meaning, custom domain)
            else {
                $storeinfo = Settings::where('custom_domain', $host)->first();
                $vdata = $storeinfo->vendor_id;
            }

            // Explicit opt-in only — a customer account is never added to the list on its own.
            $subscribe = Subscriber::subscribeEmail($vdata, (string) $request->email, 'storefront_form');
            if (empty($subscribe)) {
                return redirect()->back()->with('error', trans('messages.invalid_email'));
            }
            return redirect()->back()->with('success', trans('messages.success'));
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', trans('messages.wrong'));
        }
    }
    public function contact(Request $request)
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
        $timings = Timing::where('vendor_id', $vdata)->get();
        return $this->themedView($vdata, 'contact', 'front.contactus', compact('vdata', 'storeinfo', 'timings'));
    }
    public function save_contact(Request $request)
    {
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
        $newinquiry = new Contact;
        $newinquiry->vendor_id = $request->vendor_id;
        $newinquiry->name = $request->first_name . " " . $request->last_name;
        $newinquiry->email = $request->email;
        $newinquiry->mobile = $request->mobile;
        $newinquiry->message = $request->message;
        // Link to the sender's account when the email belongs to a registered vendor or customer;
        // a general inquiry from an unknown address stays unlinked.
        $newinquiry->linked_user_id = Contact::linkToAccount((string) $request->email);
        $newinquiry->status = Contact::STATUS_NEW;
        $newinquiry->save();
        $vendordata = User::where('id', $request->vendor_id)->first();
        $emaildata = helper::emailconfigration($vendordata->id);
        Config::set('mail', $emaildata);
        helper::vendor_contact_data($vendordata->id, $vendordata->name, $vendordata->email, $request->first_name . " " . $request->last_name, $request->email, $request->mobile, $request->message);
        return redirect()->back()->with('success', trans('messages.success'));
    }

    // ================= V2 Booking module =================
    private function resolveStore(Request $request)
    {
        $host = $_SERVER['HTTP_HOST'];
        if ($host == env('WEBSITE_HOST')) {
            $storeinfo = helper::storeinfo($request->vendor);
            return [$storeinfo, $storeinfo->id];
        }
        $storeinfo = Settings::where('custom_domain', $host)->first();
        return [$storeinfo, $storeinfo->vendor_id];
    }

    /**
     * Render the restaurant/café custom theme page (template-3/4) when the store uses it,
     * otherwise fall back to the classic view. Keeps every storefront page on one design.
     */
    private function themedView($vdata, string $page, string $classic, array $data)
    {
        $tpl = (int) optional(Settings::where('vendor_id', $vdata)->first())->template;
        $folder = $tpl == 4 ? 3 : $tpl; // café(4) shares restaurant(3); grocery(5) retail(6) pharmacy(7) booking(8) clinic(9) have their own
        $view = in_array($tpl, [3, 4, 5, 6, 7, 8, 9, 10]) && \Illuminate\Support\Facades\View::exists("front.template-$folder.$page")
            ? "front.template-$folder.$page"
            : $classic;
        return view($view, $data);
    }

    /** Which custom-theme folder (3 restaurant / 5 grocery / 6 retail / 7 pharmacy / 8 booking / 9 clinic) a store uses. */
    private function themeFolder($vdata)
    {
        $tpl = (int) optional(Settings::where('vendor_id', $vdata)->first())->template;
        if (in_array($tpl, [3, 4])) return 3;
        if (in_array($tpl, [5, 6, 7, 8, 9, 10])) return $tpl;
        return null;
    }

    /** Live cart drawer contents for the premium (template-3) theme. */
    public function cartFragment(Request $request)
    {
        [$storeinfo, $vdata] = $this->resolveStore($request);
        $cartitems = Cart::where('vendor_id', $vdata)->where('buynow', 0);
        if (Auth::user() && Auth::user()->type == 3) {
            $cartitems->where('user_id', Auth::user()->id);
        } else {
            $cartitems->where('session_id', Session::getId());
        }
        $cartdata = $cartitems->get();
        $count = helper::getcartcount($vdata, Auth::user() && Auth::user()->type == 3 ? Auth::user()->id : "");
        $subtotal = $cartdata->sum(fn($c) => $c->price * $c->qty);
        $folder = $this->themeFolder($vdata) ?: 3;
        $html = view("front.template-$folder.partials.cart_body", compact('cartdata', 'vdata', 'storeinfo', 'subtotal'))->render();
        return response()->json(['count' => $count, 'subtotal' => $subtotal, 'html' => $html]);
    }

    public function bookingpage(Request $request)
    {
        [$storeinfo, $vdata] = $this->resolveStore($request);
        $services = \App\Models\BookingService::where('vendor_id', $vdata)->where('is_available', 1)
            ->orderBy('reorder_id')->orderByDesc('id')->get();
        $paymentlist = Payment::where('vendor_id', $vdata)->where('is_available', 1)->where('is_activate', '1')
            ->orderBy('reorder_id')->get();
        $selected = $request->query('service');
        $doctors = \App\Models\Doctor::where('vendor_id', $vdata)->where('is_available', 1)
            ->orderBy('reorder_id')->orderByDesc('id')->get();
        $selectedDoctor = $request->query('doctor');
        return $this->themedView($vdata, 'listing', 'front.booking', compact('storeinfo', 'vdata', 'services', 'paymentlist', 'selected', 'doctors', 'selectedDoctor'));
    }

    public function savebooking(Request $request)
    {
        [$storeinfo, $vdata] = $this->resolveStore($request);
        $request->validate([
            'customer_name' => 'required',
            'mobile' => 'required',
            'service_name' => 'required',
            'booking_date' => 'required',
        ]);
        $settings = Settings::where('vendor_id', $vdata)->first();
        $prefix = @$settings->order_prefix ?: 'BK';
        $booking_number = $prefix . '-' . (\App\Models\Booking::where('vendor_id', $vdata)->count() + 1001);

        // Resolve price from the catalog server-side (never trust the client).
        $amount = 0;
        if (is_numeric($request->service_id)) {
            $svc = \App\Models\BookingService::where('id', $request->service_id)->where('vendor_id', $vdata)->first();
            if ($svc) {
                $amount = $svc->price;
            }
        }
        // Resolve the chosen payment method's display name.
        $payment_method = null;
        if ($request->filled('payment_type')) {
            $pm = Payment::where('vendor_id', $vdata)->where('payment_type', $request->payment_type)->first();
            $payment_method = $pm ? $pm->payment_name : null;
        }

        $booking = new \App\Models\Booking;
        $booking->vendor_id = $vdata;
        $booking->booking_number = $booking_number;
        $booking->service_id = is_numeric($request->service_id) ? $request->service_id : null;
        $booking->service_name = $request->service_name;
        $booking->amount = $amount;
        $booking->payment_type = $request->payment_type;
        $booking->payment_method = $payment_method;
        $booking->payment_status = 1; // pay at location / unpaid until merchant confirms
        $booking->staff = $request->staff;
        $booking->customer_name = $request->customer_name;
        $booking->mobile = $request->mobile;
        $booking->email = $request->email;
        $booking->booking_date = $request->booking_date;
        $booking->booking_time = $request->booking_time;
        $booking->notes = $request->notes;
        $booking->status = 1;
        $booking->save();

        $host = $_SERVER['HTTP_HOST'];
        return redirect(URL::to(($host == env('WEBSITE_HOST') ? $storeinfo->slug : '') . '/booking-success/' . $booking_number));
    }

    public function bookingsuccess(Request $request)
    {
        [$storeinfo, $vdata] = $this->resolveStore($request);
        $booking = \App\Models\Booking::where('booking_number', $request->booking_number)->where('vendor_id', $vdata)->first();
        if (empty($booking)) {
            abort(404);
        }
        $booking_whatsapp_url = helper::booking_whatsapp_url($booking, $vdata, $storeinfo);
        return $this->themedView($vdata, 'bookingsuccess', 'front.bookingsuccess', compact('storeinfo', 'vdata', 'booking', 'booking_whatsapp_url'));
    }

    public function servicepage(Request $request)
    {
        [$storeinfo, $vdata] = $this->resolveStore($request);
        $services = Item::where('vendor_id', $vdata)->where('is_available', 1)->orderBy('reorder_id')->get();
        return view('front.servicerequest', compact('storeinfo', 'vdata', 'services'));
    }

    public function saveservice(Request $request)
    {
        [$storeinfo, $vdata] = $this->resolveStore($request);
        $request->validate([
            'customer_name' => 'required',
            'mobile' => 'required',
            'service_name' => 'required',
            'address' => 'required',
        ]);
        $settings = Settings::where('vendor_id', $vdata)->first();
        $prefix = @$settings->order_prefix ?: 'SR';
        $request_number = $prefix . '-' . (\App\Models\ServiceRequest::where('vendor_id', $vdata)->count() + 1001);

        $sr = new \App\Models\ServiceRequest;
        $sr->vendor_id = $vdata;
        $sr->request_number = $request_number;
        $sr->service_id = is_numeric($request->service_id) ? $request->service_id : null;
        $sr->service_name = $request->service_name;
        $sr->customer_name = $request->customer_name;
        $sr->mobile = $request->mobile;
        $sr->email = $request->email;
        $sr->address = $request->address;
        $sr->preferred_date = $request->preferred_date;
        $sr->preferred_time = $request->preferred_time;
        $sr->notes = $request->notes;
        $sr->status = 1;
        $sr->save();

        $host = $_SERVER['HTTP_HOST'];
        return redirect(URL::to(($host == env('WEBSITE_HOST') ? $storeinfo->slug : '') . '/service-success/' . $request_number));
    }

    public function servicesuccess(Request $request)
    {
        [$storeinfo, $vdata] = $this->resolveStore($request);
        $srequest = \App\Models\ServiceRequest::where('request_number', $request->request_number)->where('vendor_id', $vdata)->first();
        if (empty($srequest)) {
            abort(404);
        }
        $service_whatsapp_url = helper::service_whatsapp_url($srequest, $vdata, $storeinfo);
        return view('front.servicesuccess', compact('storeinfo', 'vdata', 'srequest', 'service_whatsapp_url'));
    }

    public function aboutus(Request $request)
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

        $aboutus = About::select('about_content')->where('vendor_id', $vdata)->first();

        return $this->themedView($vdata, 'about', 'front.about', compact('aboutus', 'vdata', 'storeinfo'));
    }
    public function terms_condition(Request $request)
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
        $terms = Terms::where('vendor_id', $vdata)->orderBy('id', 'ASC')->first();
        return $this->themedView($vdata, 'terms', 'front.terms_and_condition', compact('vdata', 'storeinfo', 'terms'));
    }
    public function privacyshow(Request $request)
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
        $privacy = Privacypolicy::where('vendor_id', $vdata)->orderBy('id', 'ASC')->first();
        return $this->themedView($vdata, 'privacy', 'front.privacy', compact('storeinfo', 'vdata', 'privacy'));
    }
    public function refundprivacypolicy(Request $request)
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
        $refund_policy = RefundPrivacypolicy::where('vendor_id', $vdata)->orderBy('id', 'ASC')->first();
        return $this->themedView($vdata, 'refund', 'front.refund_policy', compact('vdata', 'storeinfo', 'refund_policy'));
    }
    public function addtocart(Request $request)
    {
        try {
            $host = $_SERVER['HTTP_HOST'];
            if ($host  ==  env('WEBSITE_HOST')) {
                $storeinfo = User::where('id', $request->vendor_id)->first();
                $vdata =  $storeinfo->id;
            }
            // if the current host doesn't contain the website domain (meaning, custom domain)
            else {
                $storeinfo = Settings::where('custom_domain', $host)->first();
                $vdata = $storeinfo->vendor_id;
            }

            if ($request->buynow == 1) {
                if (Auth::user() && Auth::user()->type == 3) {
                    $checkcart = Cart::where('buynow', 1)->where('user_id', Auth::user()->id)->delete();
                } else {
                    $checkcart = Cart::where('buynow', 1)->where('session_id', Session::getId())->delete();
                }
            }
            $totalprice = 0;
            $cart = new Cart;
            $variant_name = str_replace('_', ' ', $request->variants_name);
            $variation = Variants::where('name', $variant_name)->where('item_id', $request->item_id)->first();
            $item = Item::where('id', $request->item_id)->first();

            if (Auth::user() && Auth::user()->type == 3) {
                $cart->user_id = Auth::user()->id;
            } else {
                $cart->session_id = Session::getId();
            }

            if ($request->variants_name != null && $request->variants_name != "" && !empty($variation)) {
                if (Auth::user() && Auth::user()->type == 3) {
                    $cartqty = Cart::select(DB::raw("SUM(qty) as totalqty"))->where('variants_id', $variation->id)->where('user_id', Auth::user()->id)->first();
                } else {
                    $cartqty = Cart::select(DB::raw("SUM(qty) as totalqty"))->where('variants_id', $variation->id)->where('session_id', Session::getId())->first();
                }
            } else {
                if (Auth::user() && Auth::user()->type == 3) {
                    $cartqty = Cart::select(DB::raw("SUM(qty) as totalqty"))->where('item_id', $request->item_id)->where('user_id', Auth::user()->id)->first();
                } else {
                    $cartqty = Cart::select(DB::raw("SUM(qty) as totalqty"))->where('item_id', $request->item_id)->where('session_id', Session::getId())->first();
                }
            }
            if ($cartqty->totalqty != null && $cartqty->totalqty != "") {
                $qty = $cartqty->totalqty + $request->qty;
            } else {
                $qty = $request->qty;
            }
            if (empty($variation)) {

                if ($item->stock_management == 1) {
                    if ($item->min_order != null && $item->min_order != ""  && $item->min_order != 0) {
                        if ($qty < $item->min_order) {
                            return response()->json(['status' => 0, 'message' => trans('messages.min_qty_message') . $item->min_order], 200);
                        }
                    }


                    if ($item->max_order != null && $item->max_order != "" && $item->max_order != 0) {
                        if ($qty > $item->max_order) {
                            if ($cartqty->totalqty == null) {
                                return response()->json(['status' => 0, 'message' => trans('messages.max_qty_message') . $item->max_order], 200);
                            } else {
                                return response()->json(['status' => 0, 'message' => trans('messages.cart_qty_msg') . ' ' . trans('messages.max_qty_message') . $item->max_order], 200);
                            }
                        }
                    }
                    if ($qty > $item->qty) {
                        return response()->json(['status' => 0, 'message' => trans('labels.out_of_stock_msg') . ' ' . $item->item_name], 200);
                    }
                }
            } else {
                if ($variation->stock_management == 1) {
                    if ($variation->min_order != null && $variation->min_order != ""  && $variation->min_order != 0) {
                        if ($qty < $variation->min_order) {
                            return response()->json(['status' => 0, 'message' => trans('messages.min_qty_message') . $variation->min_order], 200);
                        }
                    }


                    if ($variation->max_order != null && $variation->max_order != "" && $variation->max_order != 0) {
                        if ($qty > $variation->max_order) {
                            if ($cartqty->totalqty == null) {
                                return response()->json(['status' => 0, 'message' => trans('messages.max_qty_message') . $variation->max_order], 200);
                            } else {
                                return response()->json(['status' => 0, 'message' => trans('messages.cart_qty_msg') . ' ' . trans('messages.max_qty_message') . $variation->max_order], 200);
                            }
                        }
                    }

                    if ($qty > $variation->qty) {
                        return response()->json(['status' => 0, 'message' => trans('labels.out_of_stock_msg') . ' ' . $item->item_name . '(' . $variation->name . ')'], 200);
                    }
                }
            }
            if (!empty($variation)) {
                $iprice = $variation->price;
            } else {
                $iprice = $request->item_price;
            }
            if ($item->top_deals == 1 && helper::top_deals($item->vendor_id) != null) {
                if (@helper::top_deals($item->vendor_id)->offer_type == 1) {
                    if (
                        $iprice >
                        @helper::top_deals($item->vendor_id)->offer_amount
                    ) {
                        $cartprice = $iprice - @helper::top_deals($item->vendor_id)->offer_amount;
                    } else {
                        $cartprice = $iprice;
                    }
                } else {
                    $cartprice =
                        $iprice - $iprice * (@helper::top_deals($item->vendor_id)->offer_amount / 100);
                }
            } else {
                $cartprice = $iprice;
            }

            $extra_price = explode('|', $request->extras_price);
            if ($request->extras_price != null || $request->extras_price != "") {
                $price = 0;
                foreach ($extra_price as $eprice) {
                    $price += $eprice;
                }
                $totalcartprice = $cartprice + $price;
            } else {
                $totalcartprice = $cartprice;
            }
            $cart->vendor_id = $request->vendor_id;
            $cart->item_id = $request->item_id;
            $cart->item_name = $request->item_name;
            $cart->item_image = $request->item_image;
            $cart->item_price = $cartprice;
            $cart->tax = $request->tax;
            $cart->extras_name = $request->extras_name;
            $cart->extras_price = $request->extras_price;
            $cart->extras_id = $request->extras_id;
            $cart->qty = $request->qty;
            $cart->price = (float)$totalcartprice;
            if (!empty($variation)) {
                $cart->variants_id = $variation->id;
                $cart->variants_name = $variant_name;
            } elseif (!empty($request->variants_name)) {
                // attribute-style options (size/colour) that have no Variants-table row —
                // keep the chosen label so it shows in the cart/checkout.
                $cart->variants_name = $variant_name;
            }
            $cart->buynow = $request->buynow;
            $cart->save();
            if (Auth::user() && Auth::user()->type == 3) {
                $count = Cart::where('user_id', Auth::user()->id)->where('vendor_id', $vdata)->where('buynow', 0)->count();
            } else {
                $count = Cart::where('session_id', Session::getId())->where('vendor_id', $vdata)->where('buynow', 0)->count();
            }
            if (Auth::user() && Auth::user()->type == 3) {
                $totalcart = helper::getcartcount($request->vendor_id, Auth::user() && Auth::user()->type == 3 ? Auth::user()->id : "");
            } else {
                $totalcart = helper::getcartcount($request->vendor_id, '');
            }
            session()->put('cart', $count);
            session()->put('vendor_id', $request->vendor_id);
            session()->put('old_session_id', Session::getId());
            $checkouturl = URL::to($storeinfo->slug . '/checkout?buy_now=' . $request->buynow);

            return response()->json(['status' => 1, 'message' => $request->item_name . ' ' . 'has been added to your cart', 'totalcart' => $totalcart, 'buynow' => $request->buynow, 'checkouturl' => $checkouturl], 200);
        } catch (\Exception $e) {
            \Log::error('addtocart failed: ' . $e->getMessage());
            return response()->json(['status' => 0, 'message' => trans('messages.wrong')], 200);
        }
    }
    public function details(Request $request)
    {
        $host = $_SERVER['HTTP_HOST'];

        if ($host  ==  env('WEBSITE_HOST')) {
            $storeinfo = User::where('id', $request->vendor_id)->where('is_available', 1)->where('is_deleted', 2)->first();
            $vdata = $request->vendor_id;
        }
        // if the current host doesn't contain the website domain (meaning, custom domain)
        else {
            $storeinfo = Settings::where('custom_domain', $host)->first();
            $vdata = $storeinfo->vendor_id;
        }
        $user_id = @Auth::user()->id;
        $getitem = Item::with(['variation', 'extras', 'item_image'])->select(
            'items.id',
            'items.item_original_price',
            'items.image',
            'items.description',
            'items.tax',
            DB::raw("CONCAT('" . url(env('ASSETSPATHURL') . 'item/') . "/',items.image) AS image_url"),
            DB::raw('(case when favorite.item_id is null then 0 else 1 end) as is_favorite'),
            'items.has_variants',
            'items.has_extras',
            'items.variants_json',
            'items.min_order',
            'items.max_order',
            'items.qty',
            'items.low_qty',
            'items.stock_management',
            'items.item_name',
            'items.item_price',
            'items.item_original_price',
            'items.vendor_id',
            'items.is_available',
            'items.top_deals',
            'items.avg_ratting',
            'items.video_url'
        )->leftJoin('favorite', function ($query) use ($user_id) {
            $query->on('favorite.item_id', '=', 'items.id')
                ->where('favorite.user_id', '=', $user_id);
        })->where('items.id', $request->id)
            ->where('items.vendor_id', $request->vendor_id)->first();
        $getitem->variants_json = json_decode($getitem->variants_json, true);

        $itemimages  = ItemImages::select('id', 'image', 'item_id', DB::raw("CONCAT('" . url(env('ASSETSPATHURL') . 'item/') . "/', image) AS image_url"))->where('item_id', $request->id)->orderBy('reorder_id')->get();
        App::setLocale(session()->get('locale'));

        $topdeals = TopDeals::where('vendor_id', $request->vendor_id)->first();
        $html = view('front.product.productmodalview', compact('getitem', 'itemimages', 'vdata', 'storeinfo', 'topdeals'))->render();
        return response()->json(['status' => 1, 'output' => $html], 200);
    }
    public function reviews(Request $request)
    {
        $host = $_SERVER['HTTP_HOST'];

        if ($host  ==  env('WEBSITE_HOST')) {
            $vdata = $request->vendor_id;
        }
        // if the current host doesn't contain the website domain (meaning, custom domain)
        else {
            $storeinfo = Settings::where('custom_domain', $host)->first();
            $vdata = $storeinfo->vendor_id;
        }
        $orders = Order::where('orders.user_id', @Auth::user()->id)->where('orders.vendor_id', $vdata)->join('order_details', 'orders.id', 'order_details.order_id')->where('order_details.item_id', $request->item_id)->where('orders.status_type', '3')->count();
        $rattingcount = Testimonials::where('user_id', @Auth::user()->id)->where('vendor_id', $vdata)->where('item_id', $request->item_id)->count();

        $getitem = Item::select('id', 'item_name', 'avg_ratting')->where('id', $request->item_id)->where('vendor_id', $vdata)->first();

        $itemreviewdata = Testimonials::with('user_info')->where('vendor_id', $vdata)->where('item_id', $request->item_id)->where('status', 1)->get();
        $fivestaraverage = Testimonials::where('vendor_id', $vdata)->where('item_id', $request->item_id)->where('status', 1)->where('star', 5)->count();
        $fourstaraverage = Testimonials::where('vendor_id', $vdata)->where('item_id', $request->item_id)->where('status', 1)->where('star', 4)->count();
        $threestaraverage = Testimonials::where('vendor_id', $vdata)->where('item_id', $request->item_id)->where('status', 1)->where('star', 3)->count();
        $twostaraverage = Testimonials::where('vendor_id', $vdata)->where('item_id', $request->item_id)->where('status', 1)->where('star', 2)->count();
        $onestaraverage = Testimonials::where('vendor_id', $vdata)->where('item_id', $request->item_id)->where('status', 1)->where('star', 1)->count();
        $html = view('front.reviews.product_reviews', compact('vdata', 'orders', 'rattingcount', 'getitem', 'itemreviewdata', 'fivestaraverage', 'fourstaraverage', 'threestaraverage', 'twostaraverage', 'onestaraverage'))->render();
        return response()->json(['status' => 1, 'output' => $html], 200);
    }
    public function cart(Request $request)
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
        $cartitems = Cart::select('id', 'item_id', 'item_name', 'item_image', 'item_price', 'extras_id', 'extras_name', 'extras_price', 'qty', 'price', 'tax', 'variants_id', 'variants_name')
            ->where('vendor_id', $vdata);
        if (Auth::user() && Auth::user()->type == 3) {
            $cartitems->where('user_id', @Auth::user()->id);
        } else {
            $cartitems->where('session_id', Session::getId());
        }
        $cartdata = $cartitems->where('buynow', 0)->get();
        return $this->themedView($vdata, 'cart', 'front.cart', compact('cartdata', 'vdata', 'storeinfo'));
    }
    public function qtyupdate(Request $request)
    {
        if ($request->cart_id == "") {
            return response()->json(["status" => 0, "message" => "Cart ID is required"], 200);
        }
        if ($request->qty == "") {
            return response()->json(["status" => 0, "message" => "Qty is required"], 200);
        }
        $cartdata = Cart::where('id', $request->cart_id)->first();
        $item = Item::where('id', $request->item_id)->where('vendor_id', $cartdata->vendor_id)->first();

        $variation = Variants::where('item_id', $request->item_id)->first();
        if ($request->type == "minus") {
            $update = Cart::where('id', $request->cart_id)->update(['qty' => $request->qty]);
            return response()->json(['status' => 1, 'message' => 'success', 'qty' => $request->qty], 200);
        } else {
            if ($item->has_variants == 1) {

                if ($variation->stock_management == 1) {
                    if (Auth::user() && Auth::user()->type == 3) {
                        $cartqty = Cart::select(DB::raw("SUM(qty) as totalqty"))->where('variants_id', $variation->id)->where('id', '!=', $request->cart_id)->where('user_id', Auth::user()->id)->where('buynow', 0)->first();
                    } else {

                        $cartqty = Cart::select(DB::raw("SUM(qty) as totalqty"))->where('variants_id', $variation->id)->where('id', '!=', $request->cart_id)->where('session_id', Session::getId())->where('buynow', 0)->first();
                    }

                    if ($cartqty->totalqty != null && $cartqty->totalqty != "") {
                        $qty = $cartqty->totalqty + $request->qty;
                    } else {
                        $qty = $request->qty;
                    }
                    if ($variation->min_order != null && $variation->min_order != "" && $variation->min_order != 0) {
                        if ($variation->min_order > $qty && $variation->min_order != $qty) {
                            return response()->json(['status' => 0, 'message' => trans('messages.min_qty_message') . $variation->min_order, 'qty' => $request->qty], 200);
                        }
                    }
                    if ($variation->max_order != null && $variation->max_order != "" && $variation->max_order != 0) {
                        if ($variation->max_order < $qty && $variation->max_order != $qty) {
                            if ($cartqty->totalqty == null) {
                                return response()->json(['status' => 0, 'message' => trans('messages.max_qty_message') . $variation->max_order, 'qty' => $request->qty - 1], 200);
                            } else {
                                return response()->json(['status' => 0, 'message' => trans('messages.cart_qty_msg') . ' ' . trans('messages.max_qty_message') . $variation->max_order, 'qty' => $request->qty - 1], 200);
                            }
                        }
                    }
                    if ($qty == $variation->qty) {
                        return response()->json(['status' => 1, 'message' => 'success', 'qty' => $qty], 200);
                    }
                    if ($qty > $variation->qty  && ($variation->qty != null && $variation->qty != "")) {
                        return response()->json(['status' => 0, 'message' => trans('labels.out_of_stock_msg') . ' ' . $item->item_name . '(' . $variation->name . ')', 'qty' => $request->qty - 1], 200);
                    } else {
                        $update = Cart::where('id', $request->cart_id)->update(['qty' => $request->qty]);
                        return response()->json(['status' => 1, 'message' => 'success', 'qty' => $request->qty], 200);
                    }
                } else {
                    $update = Cart::where('id', $request->cart_id)->update(['qty' => $request->qty]);
                    return response()->json(['status' => 1, 'message' => 'success', 'qty' => $request->qty], 200);
                }
            } elseif ($item->has_variants == 2) {

                if ($item->stock_management == 1) {
                    if (Auth::user() && Auth::user()->type == 3) {
                        $cartqty = Cart::select(DB::raw("SUM(qty) as totalqty"))->where('item_id', $item->id)->where('id', '!=', $request->cart_id)->where('user_id', Auth::user()->id)->where('buynow', 0)->first();
                    } else {

                        $cartqty = Cart::select(DB::raw("SUM(qty) as totalqty"))->where('item_id', $item->id)->where('id', '!=', $request->cart_id)->where('session_id', Session::getId())->where('buynow', 0)->first();
                    }

                    if ($cartqty->totalqty != null && $cartqty->totalqty != "") {
                        $qty = $cartqty->totalqty + $request->qty;
                    } else {
                        $qty = $request->qty;
                    }

                    if ($item->min_order != null && $item->min_order != "" && $item->min_order != 0) {
                        if ($item->min_order > $qty && $item->min_order != $qty) {
                            return response()->json(['status' => 0, 'message' => trans('messages.min_qty_message') . $item->min_order, 'qty' => $request->qty], 200);
                        }
                    }

                    if ($item->max_order != null && $item->max_order != "" && $item->max_order != 0) {
                        if ($item->max_order < $qty) {
                            if ($cartqty->totalqty == null) {
                                return response()->json(['status' => 0, 'message' => trans('messages.max_qty_message') . $item->max_order, 'qty' => $request->qty - 1], 200);
                            } else {
                                return response()->json(['status' => 0, 'message' => trans('messages.cart_qty_msg') . ' ' . trans('messages.max_qty_message') . $item->max_order, 'qty' => $request->qty - 1], 200);
                            }
                        }
                    }
                    if ($qty == $item->qty) {

                        $update = Cart::where('id', $request->cart_id)->update(['qty' => $request->qty]);
                        return response()->json(['status' => 1, 'message' => 'success', 'qty' => $request->qty], 200);
                    }
                    if ($qty > $item->qty  && ($item->qty != null && $item->qty != "")) {

                        return response()->json(['status' => 0, 'message' => trans('labels.out_of_stock_msg') . ' ' . $item->item_name, 'qty' => $request->qty - 1], 200);
                    } else {

                        $update = Cart::where('id', $request->cart_id)->update(['qty' => $request->qty]);

                        return response()->json(['status' => 1, 'message' => 'success', 'qty' => $request->qty], 200);
                    }
                } else {
                    $update = Cart::where('id', $request->cart_id)->update(['qty' => $request->qty]);

                    return response()->json(['status' => 1, 'message' => 'success', 'qty' => $request->qty], 200);
                }
            }
        }
    }
    public function deletecartitem(Request $request)
    {
        if ($request->cart_id == "") {
            return response()->json(["status" => 0, "message" => "Cart Id is required"], 200);
        }
        $cartdata = Cart::where('id', $request->cart_id)->first();
        $cart = Cart::where('id', $request->cart_id)->delete();
        if (Auth::user() && Auth::user()->type == 3) {
            $count = Cart::where('user_id', Auth::user()->id)->where('vendor_id', @$cartdata->vendor_id)->count();
        } else {
            $count = Cart::where('session_id', Session::getId())->where('vendor_id', @$cartdata->vendor_id)->count();
        }
        session()->forget(['offer_amount', 'offer_code', 'offer_type']);
        if ($cart) {
            return response()->json(['status' => 1, 'message' => 'Success', 'cartcnt' => $count], 200);
        } else {
            return response()->json(['status' => 0], 200);
        }
    }
    public function checkout(Request $request)
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


        if ($request->buy_now == 1) {
            $cartitems = Cart::select('id', 'item_id', 'item_name', 'item_image', 'item_price', 'extras_id', 'extras_name', 'extras_price', 'qty', 'price', 'tax', 'variants_id', 'variants_name')
                ->where('vendor_id', $vdata)->where('buynow', 1);
        } else {
            $cartitems = Cart::select('id', 'item_id', 'item_name', 'item_image', 'item_price', 'extras_id', 'extras_name', 'extras_price', 'qty', 'price', 'tax', 'variants_id', 'variants_name')
                ->where('vendor_id', $vdata)->where('buynow', 0);
        }
        if (Auth::user() && Auth::user()->type == 3) {
            $cartitems->where('user_id', @Auth::user()->id);
        } else {
            $cartitems->where('session_id', Session::getId());
        }
        $cartdata = $cartitems->get();

        if (count($cartdata) == 0) {
            return redirect($storeinfo->slug . '/cart')->with('error', trans('messages.cart_empty'));
        }
        foreach ($cartdata as $cart) {
            if ($cart->variants_id != "" && $cart->variants_id != null) {
                if (Auth::user() && Auth::user()->type == 3) {
                    $cartqty = Cart::select(DB::raw("SUM(qty) as totalqty"))->where('variants_id', $cart->variants_id)->where('user_id', Auth::user()->id)->first();
                } else {
                    $cartqty = Cart::select(DB::raw("SUM(qty) as totalqty"))->where('variants_id', $cart->variants_id)->where('session_id', Session::getId())->first();
                }
                $variant = Variants::where('id', $cart->variants_id)->first();
                $item_name = Item::select('item_name')->where('id', $cart->item_id)->first();
                if ($variant->stock_management == 1) {

                    if ($variant->min_order != null && $variant->min_order != ""  && $variant->min_order != 0) {
                        if ($cartqty->totalqty < $variant->min_order) {
                            return redirect()->back()->with('error', trans('messages.min_qty_message') . $variant->min_order . " " . ($item_name->item_name));
                        }
                    }
                    if ($variant->max_order != null && $variant->max_order != "" && $variant->max_order != 0) {
                        if ($variant->max_order < $cartqty->totalqty) {
                            return redirect()->back()->with('error', trans('messages.max_qty_message') . $variant->max_order . ' ' . ($item_name->item_name));
                        }
                    }
                    if ($cart->qty > $variant->qty) {
                        return redirect()->back()->with('error', trans('messages.cart_qty_msg') . ' ' . trans('labels.out_of_stock_msg') . ' ' . $item_name->item_name . '(' . $variant->name . ')');
                    }
                }
            } else {

                $item = Item::where('id', $cart->item_id)->first();

                if (Auth::user() && Auth::user()->type == 3) {
                    $cartqty = Cart::select(DB::raw("SUM(qty) as totalqty"))->where('item_id', $cart->item_id)->where('user_id', Auth::user()->id)->first();
                } else {
                    $cartqty = Cart::select(DB::raw("SUM(qty) as totalqty"))->where('item_id', $cart->item_id)->where('session_id', Session::getId())->first();
                }

                if ($item->stock_management == 1) {
                    if ($item->min_order != null && $item->min_order != ""  && $item->min_order != 0) {
                        if ($cartqty->totalqty < $item->min_order) {
                            return redirect()->back()->with('error', trans('messages.min_qty_message') . $item->min_order . ' ' . ($item->item_name));
                        }
                    }

                    if ($item->max_order != null && $item->max_order != "" && $item->max_order != 0) {
                        if ($item->max_order < $cartqty->totalqty) {
                            return redirect()->back()->with('error', trans('messages.max_qty_message') . $item->max_order . ' ' . ($item->item_name));
                        }
                    }
                    if ($cart->qty > $item->qty) {
                        return redirect()->back()->with('error',  trans('messages.cart_qty_msg') . ' ' . trans('labels.out_of_stock_msg') . ' ' . $item->item_name);
                    }
                }
            }
        }

        //  product count tax
        $itemtaxes = [];
        $producttax = 0;
        $tax_name = [];
        $tax_price = [];

        foreach ($cartdata as $cart) {
            $taxlist =  helper::gettax($cart->tax);
            if (!empty($taxlist)) {
                foreach ($taxlist as $tax) {
                    if (!empty($tax)) {
                        $producttax = helper::taxRate($tax->tax, $cart->price, $cart->qty, $tax->type);
                        $itemTax['tax_name'] = $tax->name;
                        $itemTax['tax'] = $tax->tax;
                        $itemTax['tax_rate'] = $producttax;
                        $itemtaxes[] = $itemTax;

                        if (!in_array($tax->name, $tax_name)) {
                            $tax_name[] = $tax->name;

                            if ($tax->type == 1) {
                                $price = $tax->tax * $cart->qty;
                            }

                            if ($tax->type == 2) {
                                $price = ($tax->tax / 100) * ($cart->price * $cart->qty);
                            }
                            $tax_price[] = $price;
                        } else {
                            if ($tax->type == 1) {
                                $price = $tax->tax * $cart->qty;
                            }

                            if ($tax->type == 2) {
                                $price = ($tax->tax / 100) * ($cart->price * $cart->qty);
                            }
                            $tax_price[array_search($tax->name, $tax_name)] += $price;
                        }
                    }
                }
            }
        }

        $taxArr['tax'] = $tax_name;
        $taxArr['rate'] = $tax_price;
        if (Auth::user() && Auth::user()->type == 3) {
            $paymentlist = Payment::where('is_available', '1')->where('vendor_id', $vdata)->where('is_activate', 1)->orderBy('reorder_id')->get();
        } else {
            $paymentlist = Payment::where('payment_type', '!=', '16')->where('is_available', '1')->where('vendor_id', $vdata)->where('is_activate', 1)->orderBy('reorder_id')->get();
        }

        $coupons = Promocode::where('vendor_id', $vdata)->where('is_available', 1)->where('start_date', '<=', date('Y-m-d'))->where('exp_date', '>=', date('Y-m-d'))->orderBy('reorder_id')->get();
        $tableqrs = TableQR::where('vendor_id', $vdata)->orderBy('id', 'ASC')->get();
        $getshippingarealist = Shipping::where('vendor_id', $vdata)->where('is_available', 1)->orderBy('reorder_id')->get();

        return $this->themedView($vdata, 'checkout', 'front.checkout', compact('cartdata', 'vdata', 'storeinfo', 'paymentlist', 'coupons', 'tableqrs', 'taxArr', 'getshippingarealist'));
    }

    public function applypromocode(Request $request)
    {
        if ($request->promocode == "") {
            return response()->json(["status" => 0, "message" => trans('messages.enter_promocode')], 200);
        }
        $promocode = Promocode::where('offer_code', $request->promocode)->where('vendor_id', $request->vendor_id)->first();
        if (@helper::appdata($request->vendor_id)->timezone != "") {
            date_default_timezone_set(helper::appdata($request->vendor_id)->timezone);
        }
        $current_date = date('Y-m-d');
        $start_date = date('Y-m-d', strtotime($promocode->start_date));
        $end_date = date('Y-m-d', strtotime($promocode->exp_date));
        if ($start_date <= $current_date && $current_date <= $end_date) {
            if ($request->sub_total >= @$promocode->min_amount) {
                if ($promocode->usage_type == 1) {
                    $checkcount = Order::select('couponcode')->where('couponcode', $request->promocode)->count();
                    if ($checkcount >= $promocode->usage_limit) {
                        return response()->json(['status' => 0, 'message' => trans('messages.usage_limit_exceeded')], 200);
                    }
                }
                if ($promocode->offer_type == 1) {
                    $offer_amount = $promocode->offer_amount;
                } else {
                    $offer_amount = $request->sub_total * $promocode->offer_amount / 100;
                }
                session([
                    'offer_amount' => @$offer_amount,
                    'offer_code' => @$promocode->offer_code,
                    'offer_type' => 'promocode',
                ]);
            } else {
                return response()->json(["status" => 0, "message" => trans('messages.order_amount_greater_then') . ' ' . helper::currency_formate($promocode->min_amount, $request->vendor_id)], 200);
            }
        } else {
            return response()->json(['status' => 0, 'message' => trans('messages.promocode_expired')], 200);
        }


        if (@$promocode->offer_code == $request->promocode) {
            return response()->json(['status' => 1, 'message' => trans('messages.promocode_applied'), 'data' => $promocode, 'offer_type' => Session::get('offer_type')], 200);
        } else {
            return response()->json(['status' => 0, 'message' => trans('messages.wrong_promocode')], 200);
        }
    }
    public function removepromocode()
    {
        $remove = session()->forget(['offer_amount', 'offer_code', 'offer_type']);
        if (!$remove) {
            return response()->json(['status' => 1, 'message' => trans('messages.promocode_removed')], 200);
        } else {
            return response()->json(['status' => 0, 'message' => trans('messages.wrong')], 200);
        }
    }

    public function timeslot(Request $request)
    {
        try {
            $vdata = $request->vendor_id;
            $slots = [];
            // Read this store's OWN settings (helper::appdata can resolve to the wrong vendor on some hosts).
            $store_settings = \App\Models\Settings::where('vendor_id', $vdata)->first() ?: helper::appdata($vdata);
            date_default_timezone_set($store_settings->timezone ?: 'UTC');

            if ($request->inputDate != "" || $request->inputDate != null) {
                $day = date('l', strtotime(helper::date_format($request->inputDate, $vdata)));

                $minute = "";
                // Scope timings to THIS vendor (was an unscoped query returning another store's hours).
                $time = Timing::where('vendor_id', $vdata)->where('day', $day)->first();

                // No opening hours configured for this day -> just return no slots instead of crashing.
                if ($time == null) {
                    return response()->json([]);
                }

                if ($time->is_always_close == 1) {
                    $slots = "1";
                } else {
                    if ($store_settings->interval_type == 2) {
                        $minute = (float)$store_settings->interval_time * 60;
                    }
                    if ($store_settings->interval_type == 1) {
                        $minute = $store_settings->interval_time;
                    }
                    // Interval not configured -> default to 30-minute slots (prevents the CarbonPeriod crash).
                    if ($minute === "" || (float)$minute <= 0) {
                        $minute = 30;
                    }

                    $firsthalf = new CarbonPeriod(date("H:i", strtotime($time->open_time)), $minute . ' minutes', date("H:i", strtotime($time->break_start))); // for create use 24 hours format later change format 

                    $secondhalf =  new CarbonPeriod(date("H:i", strtotime($time->break_end)), $minute . ' minutes', date("H:i", strtotime($time->close_time)));

                    foreach ($firsthalf as $item) {
                        $starttime[] = helper::time_format($item, $vdata);
                    }
                    foreach ($secondhalf as $item) {
                        $endtime[] = helper::time_format($item, $vdata);
                    }

                    for ($i = 0; $i < count($starttime) - 1; $i++) {
                        $temparray[] = $starttime[$i] . ' ' . '-' . ' ' . next($starttime);
                    }
                    for ($i = 0; $i < count($endtime) - 1; $i++) {
                        $temparray[] = $endtime[$i] . ' ' . '-' . ' ' . next($endtime);
                    }

                    $currenttime = Carbon::now()->format('H:i');
                    $current_date = helper::date_format(Carbon::now(), $vdata);

                    foreach ($temparray as $item) {
                        $slot_parts = explode(' - ', $item);
                        if ($request->inputDate === $current_date) {
                            if ($currenttime < date('H:i', strtotime($slot_parts[1]))) {
                                $slots[] = array(
                                    'slot' => $item,
                                );
                            }
                        } else {
                            $slots[] = array(
                                'slot' => $item,
                            );
                        }
                    }
                }
            }
            return $slots;
        } catch (\Throwable $th) {
            \Log::error('timeslot generation failed for vendor ' . $request->vendor_id . ': ' . $th->getMessage());
            return response()->json([]);
        }
    }
    public function checkplan(Request $request)
    {
        $checkplan = helper::checkplan($request->vendor_id, '3');
        return $checkplan;
    }
    public function paymentmethod(Request $request)
    {
        try {
            if ($request->payment_type == 6) {
                $vendor_id = $request->modal_vendor_id;
                $buynow = $request->modal_buynow;
            } else {
                $vendor_id = $request->vendor_id;
                $buynow = $request->buynow;
            }
            $payment_id = "";
            $user_id = "";
            $session_id = "";
            $filename = "";
            $storeinfo = helper::storeinfo($request->vendor);
            if (Auth::user() && Auth::user()->type == 3) {
                $user_id = Auth::user()->id;
            } else {
                $session_id = session()->getId();
            }
            $cartitems = Cart::select('carts.id', 'carts.item_id', 'carts.item_name', 'carts.item_image', 'carts.item_price', 'carts.extras_name', 'carts.extras_price', 'carts.qty', 'carts.price', 'carts.tax', 'carts.variants_id', 'carts.variants_name', DB::raw("GROUP_CONCAT(tax.name) as name"))
                ->leftjoin("tax", DB::raw("FIND_IN_SET(tax.id,carts.tax)"), ">", DB::raw("'0'"))
                ->where('carts.vendor_id', $vendor_id);
            if (Auth::user() && Auth::user()->type == 3) {
                $cartitems->where('carts.user_id', @$user_id);
            } else {
                $cartitems->where('carts.session_id', $session_id);
            }
            if ($buynow == 1) {
                $cartitems = $cartitems->where('carts.buynow', 1);
            } else {
                $cartitems = $cartitems->where('carts.buynow', 0);
            }
            $cartdata = $cartitems->groupBy("carts.id")->get();

            if ($cartdata->count() == 0) {
                return response()->json(['status' => 0, 'message' => trans('messages.cart_empty')], 200);
            }

            foreach ($cartdata as $cart) {
                if ($cart->variants_id != "" && $cart->variants_id != null) {
                    $variant = Variants::where('id', $cart->variants_id)->first();
                    $item_name = Item::select('item_name')->where('id', $cart->item_id)->first();
                    if ($variant->stock_management == 1) {
                        if ($variant->min_order != null && $variant->min_order != ""  && $variant->min_order != 0) {
                            if ($cart->qty < $variant->min_order) {
                                return response()->json(['status' => 0, 'message' => trans('messages.min_qty_message') . $variant->min_order . ' ' . ($item_name->item_name)], 200);
                            }
                        }


                        if ($variant->max_order != null && $variant->max_order != "" && $variant->max_order != 0) {
                            if ($cart->qty > $variant->max_order) {
                                return response()->json(['status' => 0, 'message' => trans('messages.max_qty_message') . $variant->max_order . ' ' . ($item_name->item_name)], 200);
                            }
                        }
                        if ($cart->qty > $variant->qty) {
                            return response()->json(['status' => 0, 'message' => trans($variant->name . 'qty not enough for order !!')], 200);
                        }
                    }
                } else {
                    $item = Item::where('id', $cart->item_id)->first();
                    if ($item->stock_management == 1) {
                        if ($item->min_order != null && $item->min_order != ""  && $item->min_order != 0) {
                            if ($cart->qty < $item->min_order) {
                                return response()->json(['status' => 0, 'message' => trans('messages.min_qty_message') . $item->min_order . ' ' . ($item->item_name)], 200);
                            }
                        }


                        if ($item->max_order != null && $item->max_order != "" && $item->max_order != 0) {
                            if ($cart->qty > $item->max_order) {
                                return response()->json(['status' => 0, 'message' => trans('messages.max_qty_message') . $item->max_order . ' ' . ($item->item_name)], 200);
                            }
                        }
                        if ($cart->qty > $item->qty) {
                            return response()->json(['status' => 0, 'message' => trans($item->name . 'qty not enough for order !!')], 200);
                        }
                    }
                }
            }

            $payment_id = "";
            $user_id = "";
            $session_id = "";
            if (Auth::user() && Auth::user()->type == 3) {
                $user_id = Auth::user()->id;
            } else {
                $session_id = session()->getId();
                $user_id = null;
            }

            if (Auth::user() && Auth::user()->type == 3) {
                $checkuser = User::where('is_available', 1)->where('id', Auth::user()->id)->first();
                if ($request->payment_type == 16) {
                    if ($checkuser->wallet == "" || ($checkuser->wallet < $request->grand_total)) {
                        return response()->json(['status' => 0, 'message' => trans('messages.insufficient_wallet')], 200);
                    }
                }
            }
            $payment_id = $request->payment_id;
            if ((string) $request->payment_type === (string) Payment::TYPE_STRIPE) {
                $getstripe = Payment::select('environment', 'secret_key', 'currency')
                    ->where('payment_type', Payment::TYPE_STRIPE)
                    ->where('vendor_id', $request->vendor_id)
                    ->where('is_available', 1)
                    ->where('is_activate', 1)
                    ->first();

                if (empty($getstripe) || empty($getstripe->secret_key)) {
                    // Stripe row missing/disabled, or no secret key saved — tell the merchant exactly what to fix.
                    \Log::warning('Stripe checkout blocked: no enabled Stripe key for vendor ' . $request->vendor_id);
                    return response()->json(['status' => 0, 'message' => trans('messages.stripe_not_configured')], 200);
                }

                Stripe::setApiKey($getstripe->secret_key);

                try {
                    $checkoutSession = \Stripe\Checkout\Session::create([
                        'mode' => 'payment',
                        'customer_email' => $request->customer_email,
                        'line_items' => [[
                            'price_data' => [
                                'currency' => strtolower($getstripe->currency ?: 'usd'),
                                'product_data' => [
                                    'name' => (optional(helper::appdata($request->vendor_id))->website_title ?: 'Store') . ' Order',
                                ],
                                'unit_amount' => (int) round($request->grand_total * 100),
                            ],
                            'quantity' => 1,
                        ]],
                        'metadata' => [
                            'vendor_id' => (string) $request->vendor_id,
                            'payment_type' => (string) Payment::TYPE_STRIPE,
                        ],
                        'success_url' => URL::to($request->slug . '/stripe/success') . '?session_id={CHECKOUT_SESSION_ID}',
                        'cancel_url' => URL::to($request->slug . '/checkout?buy_now=' . $buynow),
                    ]);
                } catch (\Stripe\Exception\ApiErrorException $se) {
                    // Surface Stripe's real reason (invalid key, wrong currency, amount too small, test-key-in-live…) so it's fixable.
                    \Log::error('Stripe checkout failed for vendor ' . $request->vendor_id . ': ' . $se->getMessage());
                    return response()->json(['status' => 0, 'message' => 'Stripe: ' . $se->getMessage()], 200);
                }

                Session::put('stripe_checkout_order_' . $checkoutSession->id, [
                    'vendor_id' => $request->vendor_id,
                    'user_id' => $user_id,
                    'session_id' => $session_id,
                    'payment_type' => $request->payment_type,
                    'customer_email' => $request->customer_email,
                    'customer_name' => $request->customer_name,
                    'customer_mobile' => $request->customer_mobile,
                    'grand_total' => $request->grand_total,
                    'delivery_charge' => $request->delivery_charge,
                    'address' => $request->address,
                    'building' => $request->building,
                    'landmark' => $request->landmark,
                    'postal_code' => $request->postal_code,
                    'discount_amount' => $request->discount_amount,
                    'offer_type' => $request->offer_type,
                    'sub_total' => $request->sub_total,
                    'tax' => $request->tax,
                    'tax_name' => $request->tax_name,
                    'delivery_time' => $request->delivery_time,
                    'delivery_date' => $request->delivery_date,
                    'delivery_area' => $request->delivery_area,
                    'couponcode' => $request->couponcode,
                    'order_type' => $request->order_type,
                    'notes' => $request->notes,
                    'table' => $request->table,
                    'buynow' => $buynow,
                    'tips' => $request->tips,
                    'slug' => $request->slug,
                ]);

                return response()->json(['status' => 1, 'url' => $checkoutSession->url], 200);
            }
            if ($request->payment_type == '6') {
                // The receipt is optional (a phone often fails to attach the picked photo), so
                // $filename must exist either way - it is passed to createorder() below.
                $filename = null;
                if ($request->hasFile('screenshot')) {
                    $validator = Validator::make($request->all(), [
                        'screenshot' => 'image|max:' . helper::imagesize() . '|' . helper::imageext(),
                    ], [
                        'screenshot.max' => trans('messages.image_size_message'),
                    ]);
                    if ($validator->fails()) {
                        return redirect()->back()->withErrors($validator)->withInput();
                    } else {
                        $filename = 'screenshot-' . uniqid() . "." . $request->file('screenshot')->getClientOriginalExtension();
                        $request->file('screenshot')->move(env('ASSETSPATHURL') . 'admin-assets/images/screenshot/', $filename);
                    }
                }
                $payment_id = "";
                $orderresponse = helper::createorder($request->modal_vendor_id, $user_id, $session_id, $request->payment_type, $payment_id, $request->modal_customer_email, $request->modal_customer_name, $request->modal_customer_mobile, $request->stripeToken, $request->modal_grand_total, $request->modal_delivery_charge, $request->modal_address, $request->modal_building, $request->modal_landmark, $request->modal_postal_code, $request->modal_discount_amount, $request->modal_offer_type, $request->modal_subtotal, $request->modal_tax, $request->modal_tax_name, $request->modal_delivery_time, $request->modal_delivery_date, $request->modal_delivery_area, $request->modal_couponcode, $request->modal_order_type, $request->modal_notes, $request->modal_table, $filename, $buynow, $request->modal_tips);

                if ($orderresponse == "false") {
                    return redirect()->back()->with('error', trans('order not placed without default status !!'));
                } else {
                    return redirect($request->modal_slug . '/success/' . $orderresponse)->with('success', trans('messages.order_placed'));
                }
            } else {
                $orderresponse = helper::createorder($request->vendor_id, $user_id, $session_id, $request->payment_type, $payment_id, $request->customer_email, $request->customer_name, $request->customer_mobile, $request->stripeToken, $request->grand_total, $request->delivery_charge, $request->address, $request->building, $request->landmark, $request->postal_code, $request->discount_amount, $request->offer_type, $request->sub_total, $request->tax, $request->tax_name, $request->delivery_time, $request->delivery_date, $request->delivery_area, $request->couponcode, $request->order_type, $request->notes, $request->table, $filename, $buynow, $request->tips);

                $url = URL::to(@$request->slug . "/success/" . $orderresponse);
                if ($orderresponse == "false") {
                    return response()->json(['status' => 0, 'message' => trans('order not placed without default status !!')], 200);
                } else {
                    return response()->json(['status' => 1, 'message' => trans('messages.order_placed'), "order_number" => $orderresponse, "url" =>  $url], 200);
                }
            }
        } catch (\Exception $e) {
            \Log::error('Checkout failed (vendor ' . $request->vendor_id . ', payment ' . $request->payment_type . '): ' . $e->getMessage());
            return response()->json(['status' => 0, 'message' => trans('messages.unable_to_complete_payment')], 200);
        }
    }

    public function stripeCheckoutSuccess(Request $request)
    {
        $sessionId = $request->get('session_id');
        $orderData = Session::get('stripe_checkout_order_' . $sessionId);

        if (empty($sessionId) || empty($orderData)) {
            return redirect($request->vendor . '/checkout')->with('error', trans('messages.unable_to_complete_payment'));
        }

        $getstripe = Payment::select('secret_key')
            ->where('payment_type', Payment::TYPE_STRIPE)
            ->where('vendor_id', $orderData['vendor_id'])
            ->where('is_available', 1)
            ->where('is_activate', 1)
            ->first();

        if (empty($getstripe) || empty($getstripe->secret_key)) {
            return redirect($orderData['slug'] . '/checkout')->with('error', trans('messages.unable_to_complete_payment'));
        }

        try {
            Stripe::setApiKey($getstripe->secret_key);
            $checkoutSession = \Stripe\Checkout\Session::retrieve($sessionId);

            if ($checkoutSession->payment_status !== 'paid') {
                return redirect($orderData['slug'] . '/checkout')->with('error', trans('messages.unable_to_complete_payment'));
            }

            $orderresponse = helper::createorder(
                $orderData['vendor_id'],
                $orderData['user_id'],
                $orderData['session_id'],
                $orderData['payment_type'],
                $checkoutSession->payment_intent,
                $orderData['customer_email'],
                $orderData['customer_name'],
                $orderData['customer_mobile'],
                '',
                $orderData['grand_total'],
                $orderData['delivery_charge'],
                $orderData['address'],
                $orderData['building'],
                $orderData['landmark'],
                $orderData['postal_code'],
                $orderData['discount_amount'],
                $orderData['offer_type'],
                $orderData['sub_total'],
                $orderData['tax'],
                $orderData['tax_name'],
                $orderData['delivery_time'],
                $orderData['delivery_date'],
                $orderData['delivery_area'],
                $orderData['couponcode'],
                $orderData['order_type'],
                $orderData['notes'],
                $orderData['table'],
                '',
                $orderData['buynow'],
                $orderData['tips']
            );

            Session::forget('stripe_checkout_order_' . $sessionId);

            if ($orderresponse == "false") {
                return redirect($orderData['slug'] . '/checkout')->with('error', trans('order not placed without default status !!'));
            }

            return redirect($orderData['slug'] . '/success/' . $orderresponse)->with('success', trans('messages.order_placed'));
        } catch (\Throwable $th) {
            return redirect($orderData['slug'] . '/checkout')->with('error', trans('messages.unable_to_complete_payment'));
        }
    }

    public function ordersuccess(Request $request)
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
        $order_number = $request->order_number;
        $whmessage = "";
        if (SystemAddons::where('unique_identifier', 'whatsapp_message')->first() != null && SystemAddons::where('unique_identifier', 'whatsapp_message')->first()->activated == 1) {
            if (@whatsapp_helper::whatsapp_message_config($vdata)->order_created == 1) {
                if (whatsapp_helper::whatsapp_message_config($vdata)->message_type == 1) {
                    whatsapp_helper::whatsappmessage($order_number, $vdata, $storeinfo);
                } else {
                    $whmessage = whatsapp_helper::whatsappmessage($order_number, $vdata, $storeinfo);
                }
            }
        }
        // V2: build a keys-free WhatsApp deep-link with the full order details.
        $order_whatsapp_url = helper::order_whatsapp_url($order_number, $vdata, $storeinfo);
        return $this->themedView($vdata, 'success', 'front.ordersuccess', compact('vdata', 'storeinfo', 'order_number', 'whmessage', 'order_whatsapp_url'));
    }
    public function trackorder(Request $request)
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

        $status = Order::select('order_number', DB::raw('DATE_FORMAT(created_at, "%d %M %Y") as date'), 'address', 'building', 'landmark', 'pincode', 'order_type', 'id', 'discount_amount', 'order_number', 'status', 'status_type', 'order_notes', 'tax', 'tax_name', 'delivery_charge', 'delivery_area', 'couponcode', 'offer_type', 'sub_total', 'grand_total', 'customer_name', 'customer_email', 'mobile', 'tips')->where('order_number', $request->ordernumber)->where('vendor_id', $vdata)->first();
        $orderdata = Order::with('tableqr')->where('order_number', $request->ordernumber)->where('vendor_id', $vdata)->first();
        $orderdetails = OrderDetails::where('order_details.order_id', $status->id)->get();
        $summery = array(
            'id' => $status->id,
            'tax' => $status->tax,
            'tax_name' => $status->tax_name,
            'discount_amount' => $status->discount_amount,
            'order_number' => $status->order_number,
            'created_at' => $status->date,
            'delivery_charge' => $status->delivery_charge,
            'address' => $status->address,
            'building' => $status->building,
            'landmark' => $status->landmark,
            'pincode' => $status->pincode,
            'order_notes' => $status->order_notes,
            'status' => $status->status,
            'status_type' => $status->status_type,
            'order_type' => $status->order_type,
            'couponcode' => $status->couponcode,
            'offer_type' => $status->offer_type,
            'sub_total' => $status->sub_total,
            'grand_total' => $status->grand_total,
            'customer_name' => $status->customer_name,
            'customer_email' => $status->customer_email,
            'mobile' => $status->mobile,
            'tips' => $status->tips,
            'delivery_area' => $status->delivery_area,
        );

        return $this->themedView($vdata, 'trackorder', 'front.track-order', compact('vdata', 'storeinfo', 'orderdata', 'summery', 'orderdetails'));
    }
    public function cancelorder(Request $request, $order_number)
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
        $orderdata = Order::where('order_number', $request->ordernumber)->where('vendor_id', $vdata)->first();

        $orderdetail = OrderDetails::where('order_id', $orderdata->id)->get();

        if ($orderdata->status_type == 2) {
            return redirect()->back()->with('error', trans('messages.already_accepted'));
        } else if ($orderdata->status_type == 4) {
            return redirect()->back()->with('error', trans('messages.already_rejected'));
        } else if ($orderdata->status_type == 3) {
            return redirect()->back()->with('error', trans('messages.already_delivered'));
        }
        $defaultsatus = CustomStatus::where('vendor_id', $vdata)->where('order_type', $orderdata->order_type)->where('type', 4)->where('is_available', 1)->where('is_deleted', 2)->first();
        if (empty($defaultsatus) && $defaultsatus == null) {
            return redirect()->back()->with('error', trans('messages.wrong'));
        } else {
            $orderdata->status_type = $defaultsatus->type;
            $orderdata->status = $defaultsatus->id;
            $orderdata->update();
            foreach ($orderdetail as $order) {
                if ($order->variants_id != null && $order->variants_id != "") {
                    $item = Variants::where('id', $order->variants_id)->where('item_id', $order->item_id)->first();
                } else {
                    $item = Item::where('id', $order->item_id)->where('vendor_id', $vdata)->first();
                }
                $item->qty = $item->qty + $order->qty;
                $item->update();
            }
            if ($orderdata->payment_type == 16) {
                if (Auth::user() && Auth::user()->type == 3) {
                    $walletuser = User::where('id', $orderdata->user_id)->first();
                    $walletuser->wallet += $orderdata->grand_total;
                    $walletuser->save();

                    $transaction = new Transaction();
                    $transaction->vendor_id = $orderdata->vendor_id;
                    $transaction->user_id = $orderdata->user_id;
                    $transaction->order_id = $orderdata->id;
                    $transaction->transaction_type = 3;
                    $transaction->amount = $orderdata->grand_total;
                    $transaction->order_number = $orderdata->order_number;
                    $transaction->save();
                }
            }
            $title = helper::gettype($orderdata->status, $orderdata->status_type, $orderdata->order_type, $vdata)->name;

            $message_text = 'Order ' . $orderdata->order_number . ' has been cancelled by ' . $orderdata->customer_name;
            $emaildata = helper::emailconfigration($vdata);
            Config::set('mail', $emaildata);
            $checkmail = helper::cancel_order($storeinfo->email, $storeinfo->name, $title, $message_text, $orderdata);
            if (Auth::user() && Auth::user()->type == 3) {
                $emaildata = User::select('id', 'name', 'slug', 'email', 'mobile', 'token')->where('id', $orderdata->vendor_id)->first();
                $body = "#" . $request->order_number . " has been cancelled";
                helper::push_notification($emaildata->token, $title, $body, "order", $orderdata->id);
            }
            return redirect()->back()->with('success', trans('messages.success'));
        }
    }
    public function ordercreate(Request $request)
    {

        if ($request->paymentId != "") {
            $paymentid = $request->paymentId;
        }
        if ($request->payment_id != "") {
            $paymentid = $request->payment_id;
        }
        if ($request->transaction_id != "") {
            $paymentid = $request->transaction_id;
        }

        if (session()->get('payment_type') == "11") {
            if ($request->code == "PAYMENT_SUCCESS") {
                $paymentid = $request->transactionId;
            }
        }

        if (Session::get('payment_type') == "12") {
            $checkstatus = app('App\Http\Controllers\addons\PayTabController')->checkpaymentstatus(Session::get('tran_ref'), Session::get('vendor_id'));

            if ($checkstatus == "A") {
                $paymentid = Session::get('tran_ref');
            } else {
                return redirect(Session::get('failureurl'))->with('error', session()->get('paytab_response'));
            }
        }

        if (Session::get('payment_type') == "13") {
            $checkstatus = app('App\Http\Controllers\addons\MollieController')->checkpaymentstatus(Session::get('tran_ref'), Session::get('vendor_id'));

            if ($checkstatus == "A") {
                $paymentid = Session::get('tran_ref');
            } else {
                return redirect(Session::get('failureurl'))->with('error', session()->get('paytab_response'));
            }
        }

        if (Session::get('payment_type') == "14") {
            if ($request->status == "Completed") {
                $paymentid = $request->transaction_id;
            } else {
                return redirect(Session::get('failureurl'))->with('error', session()->get('paytab_response'));
            }
        }

        if (session()->get('payment_type') == "15") {

            $checkstatus = app('App\Http\Controllers\addons\XenditController')->checkpaymentstatus(session()->get('tran_ref'), Session::get('vendor_id'));

            if ($checkstatus == "PAID") {
                $paymentid = session()->get('payment_id');
            } else {
                return redirect(Session::get('failureurl'))->with('error', session()->get('paytab_response'));
            }
        }

        $user_id = "";
        $session_id = "";
        if (Auth::user() && Auth::user()->type == 3) {
            $user_id = Auth::user()->id;
        } else {
            $session_id = session()->getId();
        }
        $orderresponse = helper::createorder(Session::get('vendor_id'), $user_id, $session_id, Session::get('payment_type'), $paymentid, Session::get('customer_email'), Session::get('customer_name'), Session::get('customer_mobile'), Session::get('stripeToken'), Session::get('grand_total'), Session::get('delivery_charge'), Session::get('address'), Session::get('building'), Session::get('landmark'), Session::get('postal_code'), Session::get('discount_amount'), Session::get('offer_type'), Session::get('sub_total'), Session::get('tax'), Session::get('tax_name'), Session::get('delivery_time'), Session::get('delivery_date'), Session::get('delivery_area'), Session::get('couponcode'), Session::get('order_type'), Session::get('notes'), Session::get('table'), '', Session::get('buynow'), Session::get('tips'));

        $slug = Session::get('slug');
        $order_number = $orderresponse;

        return view('front.mercadoorder', compact('slug', 'order_number'));
    }
    public function search(Request $request)
    {
        $user_id = @Auth::user()->id;
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

        if ($user_id != null) {
            $getsearchitems = Item::with(['variation', 'extras'])->select('items.*', DB::raw('(case when favorite.item_id is null then 0 else 1 end) as is_favorite'))
                ->leftJoin('favorite', function ($query) use ($user_id) {
                    $query->on('favorite.item_id', '=', 'items.id')
                        ->where('favorite.user_id', '=', $user_id);
                })->where('items.vendor_id', $vdata)
                ->where('is_available', '1')
                ->where('items.item_name', 'LIKE', '%' . $request->search . '%');
            if ($request->has('filter') && $request->filter != "") {
                if ($request->filter == "price-high-to-low") {
                    $getsearchitems = $getsearchitems->orderByDesc('items.item_price');
                }
                if ($request->filter == "price-low-to-high") {
                    $getsearchitems = $getsearchitems->orderBy('items.item_price');
                }
                if ($request->filter == "ratting-high-to-low") {
                    $getsearchitems = $getsearchitems->orderByDesc('items.avg_ratting');
                }
                if ($request->filter == "ratting-low-to-high") {
                    $getsearchitems = $getsearchitems->orderBy('items.avg_ratting');
                }
            }
            $getsearchitems = $getsearchitems->orderBy('items.reorder_id')->paginate(12)->onEachSide(0);
        } else {
            $getsearchitems = Item::with(['variation', 'extras'])
                ->where('vendor_id', $vdata)
                ->where('is_available', '1')
                ->where('item_name', 'LIKE', '%' . $request->search . '%');
            if ($request->has('filter') && $request->filter != "") {
                if ($request->filter == "price-high-to-low") {
                    $getsearchitems = $getsearchitems->orderByDesc('items.item_price');
                }
                if ($request->filter == "price-low-to-high") {
                    $getsearchitems = $getsearchitems->orderBy('items.item_price');
                }
                if ($request->filter == "ratting-high-to-low") {
                    $getsearchitems = $getsearchitems->orderByDesc('items.avg_ratting');
                }
                if ($request->filter == "ratting-low-to-high") {
                    $getsearchitems = $getsearchitems->orderBy('items.avg_ratting');
                }
            }
            $getsearchitems = $getsearchitems->orderBy('items.reorder_id')->paginate(12)->onEachSide(0);
        }
        return view('front.search', compact('getsearchitems', 'vdata', 'storeinfo'));
    }
    public function changeqty(Request $request)
    {
        try {

            if ($request->variants_name == null) {

                $item = Item::where('id', $request->item_id)->where('vendor_id', $request->vendor_id)->first();

                if (Auth::user() && Auth::user()->type == 3) {
                    $cartqty = Cart::select(DB::raw("SUM(qty) as totalqty"))->where('item_id', $item->id)->where('user_id', Auth::user()->id)->where('buynow', 0)->first();
                } else {

                    $cartqty = Cart::select(DB::raw("SUM(qty) as totalqty"))->where('item_id', $item->id)->where('session_id', Session::getId())->where('buynow', 0)->first();
                }
                if ($cartqty->totalqty != null && $cartqty->totalqty != "") {
                    $qty = $cartqty->totalqty + $request->qty;
                } else {
                    $qty = $request->qty;
                }

                if ($item->stock_management == 1) {
                    if ($item->max_order != null && $item->max_order != "" && $item->max_order != 0) {
                        if ($item->max_order < $qty) {
                            if ($cartqty->totalqty == null) {
                                return response()->json(['status' => 0, 'message' => trans('messages.max_qty_message') . $item->max_order, 'qty' => $request->qty - 1], 200);
                            } else {
                                return response()->json(['status' => 0, 'message' => trans('messages.cart_qty_msg') . ' ' . trans('messages.max_qty_message') . $item->max_order, 'qty' => $request->qty - 1], 200);
                            }
                        }
                    }
                    if ($qty == $item->qty) {
                        return response()->json(['status' => 1, 'message' => 'success', 'qty' => $qty], 200);
                    }
                    if ($qty > $item->qty && ($item->qty != null && $item->qty != "")) {
                        return response()->json(['status' => 0, 'message' => trans('labels.out_of_stock_msg') . ' ' . $item->item_name, 'qty' => $request->qty - 1], 200);
                    } else {
                        return response()->json(['status' => 1, 'message' => 'success', 'qty' => $request->qty], 200);
                    }
                } else {
                    return response()->json(['status' => 1, 'message' => 'success', 'qty' => $request->qty], 200);
                }
            } else {
                $variant_name = str_replace('_', ' ', $request->variants_name);
                $variation = Variants::where('name', $variant_name)->where('item_id', $request->item_id)->first();
                $item_name = Item::select('item_name')->where('id', $request->item_id)->first();
                if (Auth::user() && Auth::user()->type == 3) {
                    $cartqty = Cart::select(DB::raw("SUM(qty) as totalqty"))->where('variants_id', $variation->id)->where('user_id', Auth::user()->id)->where('buynow', 0)->first();
                } else {

                    $cartqty = Cart::select(DB::raw("SUM(qty) as totalqty"))->where('variants_id', $variation->id)->where('session_id', Session::getId())->where('buynow', 0)->first();
                }

                if ($cartqty->totalqty != null && $cartqty->totalqty != "") {
                    $qty = $cartqty->totalqty + $request->qty;
                } else {
                    $qty = $request->qty;
                }

                if ($variation->stock_management == 1) {
                    if ($variation->min_order != null && $variation->min_order != "" && $variation->min_order != 0) {
                        if ($variation->min_order > $qty && $variation->min_order != $qty) {
                            return response()->json(['status' => 0, 'message' => trans('messages.min_qty_message') . $variation->min_order, 'qty' => $request->qty], 200);
                        }
                    }
                    if ($variation->max_order != null && $variation->max_order != "" && $variation->max_order != 0) {
                        if ($variation->max_order < $qty && $variation->max_order != $qty) {
                            if ($cartqty->totalqty == null) {
                                return response()->json(['status' => 0, 'message' => trans('messages.max_qty_message') . $variation->max_order, 'qty' => $request->qty - 1], 200);
                            } else {
                                return response()->json(['status' => 0, 'message' => trans('messages.cart_qty_msg') . ' ' . trans('messages.max_qty_message') . $variation->max_order, 'qty' => $request->qty - 1], 200);
                            }
                        }
                    }
                    if ($qty == $variation->qty) {
                        return response()->json(['status' => 1, 'message' => 'success', 'qty' => $qty], 200);
                    }
                    if ($qty > $variation->qty  && ($variation->qty != null && $variation->qty != "")) {
                        return response()->json(['status' => 0, 'message' => trans('labels.out_of_stock_msg') . ' ' . $item_name->item_name . '(' . $item_name->item_name . ')', 'qty' => $request->qty - 1], 200);
                    } else {
                        return response()->json(['status' => 1, 'message' => 'success', 'qty' => $request->qty], 200);
                    }
                } else {
                    return response()->json(['status' => 1, 'message' => 'success', 'qty' => $request->qty], 200);
                }
            }
        } catch (\Throwable $th) {
            dd($th);
            return redirect()->back()->with('error', trans('messages.wrong'));
        }
    }
    public function getProductsVariantQuantity(Request $request)
    {
        $quantity = $variant_id = 0;
        $price = 0;
        $item = Item::where('id', $request->item_id)->first();
        $variant = Variants::where('item_id', $request->item_id)->where('name', $request->name)->first();
        $status = 1;
        $quantity = @$variant->qty - (isset($cart[@$variant->id]['qty']) ? $cart[@$variant->id]['qty'] : 0);
        $variant_id = @$variant->id;
        $min_order = @$variant->min_order;
        $max_order = @$variant->max_order;
        $stock_management = @$variant->stock_management;
        $variants_name = @$request->name;
        if ($item->top_deals == 1 && helper::top_deals($item->vendor_id) != null) {
            if (@helper::top_deals($item->vendor_id)->offer_type == 1) {
                if (
                    $variant->price >
                    @helper::top_deals($item->vendor_id)->offer_amount
                ) {
                    $price =
                        $variant->price -
                        @helper::top_deals($item->vendor_id)->offer_amount;
                } else {
                    $price = $variant->price;
                }
            } else {
                $price =
                    $variant->price -
                    $variant->price *
                    (@helper::top_deals($item->vendor_id)->offer_amount / 100);
            }
            $original_price = $variant->price;
        } else {
            $price = $variant->price;
            $original_price = $variant->original_price;
        }
        if ($item->is_available == 2 || $item->is_deleted == 1) {
            $is_available = 2;
        } else {
            $is_available = @$variant->is_available;
        }
        return response()->json([
            'status' => 1,
            'id' => $request->item_id,
            'price' => $price,
            'original_price' => $original_price,
            'quantity' => $quantity,
            'variant_id' => $variant_id,
            'min_order' => $min_order,
            'max_order' => $max_order,
            'stock_management' => $stock_management,
            'variants_name' => $variants_name,
            'is_available' => $is_available
        ], 200);
    }

    public function alltopdeals(Request $request)
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
        if (Auth::user() && Auth::user()->type == 3) {
            $user_id = Auth::user()->id;

            $topdealsproducts = Item::with(['variation', 'extras', 'item_image'])
                ->select('items.*', DB::raw('(case when favorite.item_id is null then 0 else 1 end) as is_favorite'))
                ->leftJoin('favorite', function ($query) use ($user_id) {
                    $query->on('favorite.item_id', '=', 'items.id')
                        ->where('favorite.user_id', '=', $user_id);
                })
                ->where('items.top_deals', '1')
                ->where('items.vendor_id', $vdata)
                ->where('is_available', '1');
            if ($request->has('filter') && $request->filter != "") {
                if ($request->filter == "price-high-to-low") {
                    $topdealsproducts = $topdealsproducts->orderByDesc('items.item_price');
                }
                if ($request->filter == "price-low-to-high") {
                    $topdealsproducts = $topdealsproducts->orderBy('items.item_price');
                }
                if ($request->filter == "ratting-high-to-low") {
                    $topdealsproducts = $topdealsproducts->orderByDesc('items.avg_ratting');
                }
                if ($request->filter == "ratting-low-to-high") {
                    $topdealsproducts = $topdealsproducts->orderBy('items.avg_ratting');
                }
            }
            $topdealsproducts = $topdealsproducts->orderBy('items.reorder_id')->paginate(12)->onEachSide(0);
        } else {

            $topdealsproducts = Item::with(['variation', 'extras', 'item_image'])
                ->where('items.top_deals', '1')
                ->where('vendor_id', $vdata)
                ->where('is_available', '1');
            if ($request->has('filter') && $request->filter != "") {
                if ($request->filter == "price-high-to-low") {
                    $topdealsproducts = $topdealsproducts->orderByDesc('items.item_price');
                }
                if ($request->filter == "price-low-to-high") {
                    $topdealsproducts = $topdealsproducts->orderBy('items.item_price');
                }
                if ($request->filter == "ratting-high-to-low") {
                    $topdealsproducts = $topdealsproducts->orderByDesc('items.avg_ratting');
                }
                if ($request->filter == "ratting-low-to-high") {
                    $topdealsproducts = $topdealsproducts->orderBy('items.avg_ratting');
                }
            }
            $topdealsproducts = $topdealsproducts->orderBy('items.reorder_id')->paginate(12)->onEachSide(0);
        }
        $topdeals = TopDeals::where('vendor_id', $storeinfo->id)->first();
        return view('front.viewalltopdeals', compact('topdealsproducts', 'vdata', 'storeinfo', 'topdeals'));
    }

    public function storereviewshow(Request $request)
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
        if (
            SystemAddons::where('unique_identifier', 'store_reviews')->first() != null &&
            SystemAddons::where('unique_identifier', 'store_reviews')->first()->activated == 1
        ) {
            $storereview = Testimonials::where('vendor_id', $vdata)->where('user_id', null)->where('item_id', null)->orderBy('reorder_id')->get();
            return view('front.review', compact('storeinfo', 'vdata', 'storereview'));
        } else {
            abort(404);
        }
    }
    public function faqshow(Request $request)
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
        $allfaqs = Faq::where('vendor_id', $vdata)->orderBy('reorder_id')->get();
        return $this->themedView($vdata, 'faq', 'front.faq', compact('storeinfo', 'vdata', 'allfaqs'));
    }
    public function whoweareshow(Request $request)
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
        $allwhoweare = WhoWeAre::where('vendor_id', $vdata)->orderBy('reorder_id')->get();
        return view('front.whoweare', compact('storeinfo', 'vdata', 'allwhoweare'));
    }

    public function productdetails(Request $request)
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
        $user_id = @Auth::user()->id;
        $getitem = Item::with(['variation', 'extras', 'item_image'])->select(
            'items.id',
            'items.cat_id',
            'items.item_original_price',
            'items.image',
            'items.description',
            'items.tax',
            DB::raw('(case when favorite.item_id is null then 0 else 1 end) as is_favorite'),
            'items.has_variants',
            'items.has_extras',
            'items.variants_json',
            'items.min_order',
            'items.max_order',
            'items.qty',
            'items.low_qty',
            'items.stock_management',
            'items.item_name',
            'items.item_price',
            'items.item_original_price',
            'items.vendor_id',
            'items.is_available',
            'items.top_deals',
            'items.avg_ratting',
            'items.video_url'
        )->leftJoin('favorite', function ($query) use ($user_id) {
            $query->on('favorite.item_id', '=', 'items.id')
                ->where('favorite.user_id', '=', $user_id);
        })->where('items.slug', $request->slug)
            ->where('items.vendor_id', $vdata)->first();
        $getitem->variants_json = json_decode($getitem->variants_json, true);
        $itemimages  = ItemImages::select('id', 'image', 'item_id', DB::raw("CONCAT('" . url(env('ASSETSPATHURL') . 'item/') . "/', image) AS image_url"))->where('item_id', $getitem->id)->orderBy('reorder_id')->get();

        $orders = Order::where('orders.user_id', @Auth::user()->id)->where('orders.vendor_id', $vdata)->join('order_details', 'orders.id', 'order_details.order_id')->where('order_details.item_id', $getitem->id)->where('orders.status_type', '3')->count();
        $rattingcount = Testimonials::where('user_id', @Auth::user()->id)->where('vendor_id', $vdata)->where('item_id', $getitem->id)->count();

        $itemreviewdata = Testimonials::with('user_info')->where('vendor_id', $vdata)->where('item_id', $getitem->id)->where('status', 1)->get();
        $fivestaraverage = Testimonials::where('vendor_id', $vdata)->where('item_id', $getitem->id)->where('status', 1)->where('star', 5)->count();
        $fourstaraverage = Testimonials::where('vendor_id', $vdata)->where('item_id', $getitem->id)->where('status', 1)->where('star', 4)->count();
        $threestaraverage = Testimonials::where('vendor_id', $vdata)->where('item_id', $getitem->id)->where('status', 1)->where('star', 3)->count();
        $twostaraverage = Testimonials::where('vendor_id', $vdata)->where('item_id', $getitem->id)->where('status', 1)->where('star', 2)->count();
        $onestaraverage = Testimonials::where('vendor_id', $vdata)->where('item_id', $getitem->id)->where('status', 1)->where('star', 1)->count();

        if ($user_id != null) {
            $getrelateditems = Item::with(['variation', 'extras'])->select('items.*', DB::raw('(case when favorite.item_id is null then 0 else 1 end) as is_favorite'))
                ->leftJoin('favorite', function ($query) use ($user_id) {
                    $query->on('favorite.item_id', '=', 'items.id')
                        ->where('favorite.user_id', '=', $user_id);
                })->where('items.vendor_id', $vdata)
                ->where('is_available', '1')
                ->where('items.id', '!=', $getitem->id)
                ->where('items.cat_id', $getitem->cat_id)
                ->orderBy('reorder_id')->take(8)->get();
        } else {
            $getrelateditems = Item::with(['variation', 'extras'])->where('vendor_id', $vdata)
                ->where('is_available', '1')
                ->where('items.id', '!=', $getitem->id)
                ->where('items.cat_id', $getitem->cat_id)
                ->orderBy('reorder_id')->take(8)->get();
        }

        $question_answer = QuestionAnswer::where('product_id', $getitem->id)->where('vendor_id', $vdata)->whereNot('answer', '')->get();
        
        return $this->themedView($vdata, 'productdetail', 'front.product.productdetails', compact('storeinfo', 'getitem', 'itemimages', 'orders', 'rattingcount', 'itemreviewdata', 'fivestaraverage', 'fourstaraverage', 'threestaraverage', 'twostaraverage', 'onestaraverage', 'getrelateditems', 'vdata', 'question_answer'));
    }
}
