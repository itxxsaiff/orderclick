<?php

namespace App\Http\Controllers\landing;

use App\Helpers\helper;
use App\Http\Controllers\Controller;
use App\Models\About;
use App\Models\Areas;
use App\Models\Blog;
use App\Models\City;
use App\Models\Contact;
use App\Models\Faq;
use App\Models\Features;
use Illuminate\Http\Request;
use App\Models\PricingPlan;
use App\Models\Privacypolicy;
use App\Models\Promotionalbanner;
use App\Models\RefundPrivacypolicy;
use App\Models\Subscriber;
use App\Models\Terms;
use App\Models\Testimonials;
use App\Models\StoreCategory;
use App\Models\Timing;
use App\Models\User;
use App\Models\Works;
use Config;
use Lunaweb\RecaptchaV3\Facades\RecaptchaV3;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $planlist = PricingPlan::where('is_available', 1)->where('vendor_id', null)->orderBy('reorder_id')->get();
        $features = Features::where('vendor_id', '1')->orderBy('reorder_id')->get();
        $testimonials = Testimonials::where('vendor_id', '1')->orderBy('reorder_id')->get();
        $blogs = Blog::where('vendor_id', '1')->orderBy('reorder_id')->get();
        $works = Works::where('vendor_id', '1')->orderBy('reorder_id')->get();
        $userdata = User::select('users.id', 'name', 'slug', 'settings.description', 'website_title', 'cover_image')->where('available_on_landing', 1)->whereIn('users.id', self::liveStoreIds())->join('settings', 'users.id', '=', 'settings.vendor_id')->get();

        return view('landing.index', compact('planlist', 'features', 'testimonials', 'blogs', 'works', 'userdata'));
    }

    public function emailsubscribe(Request $request)
    {
        try {
            // Explicit opt-in only. Re-subscribing an address that previously opted out flips it
            // back rather than creating a duplicate row.
            $subscribe = Subscriber::subscribeEmail(1, (string) $request->email, 'landing_form');
            if (empty($subscribe)) {
                return redirect()->back()->with('error', trans('messages.invalid_email'));
            }
            return redirect()->back()->with('success', trans('messages.success'));
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', trans('messages.wrong'));
        }
    }

    public function inquiry(Request $request)
    {

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

        $newinquiry = new Contact;
        $newinquiry->vendor_id = '1';
        $newinquiry->name = $request->first_name . " " . $request->last_name;
        $newinquiry->email = $request->emaill;
        $newinquiry->mobile = $request->mobile;
        $newinquiry->message = $request->message;
        $newinquiry->inquiry_type = array_key_exists((string) $request->inquiry_type, Contact::typeOptions())
            ? $request->inquiry_type : 'general';
        $newinquiry->related_system = \App\Helpers\Systems::isValid($request->related_system)
            ? $request->related_system : null;
        $newinquiry->status = Contact::STATUS_NEW;
        // Link to the sender's account when the email belongs to a registered vendor or customer.
        $newinquiry->linked_user_id = Contact::linkToAccount((string) $request->emaill);
        $newinquiry->save();
        $vendordata = User::where('id', 1)->first();
        $emaildata = helper::emailconfigration($vendordata->id);
        Config::set('mail', $emaildata);
        helper::vendor_contact_data('1', $vendordata->name, $vendordata->email, $request->first_name . " " . $request->last_name, $request->email, $request->mobile, $request->message);
        return redirect()->back()->with('success', trans('messages.success'));
    }

    public function blogs(Request $request)
    {
        $blogs = Blog::where('vendor_id', '1')->orderBy('reorder_id')->paginate(8)->onEachSide(1);
        return view('landing.blog_list', compact('blogs'));
    }

    public function privacy_policy(Request $request)
    {
        $privacy_policy = Privacypolicy::where('vendor_id', '1')->first();
        return view('landing.privacy_policy', compact('privacy_policy'));
    }

    public function about_us(Request $request)
    {
        $about_us = About::where('vendor_id', '1')->first();

        return view('landing.about_us', compact('about_us'));
    }

    public function refund_policy(Request $request)
    {
        $refund_policy = RefundPrivacypolicy::where('vendor_id', '1')->first();
        return view('landing.refund_policy', compact('refund_policy'));
    }

    public function terms_condition(Request $request)
    {
        $terms_condition = Terms::where('vendor_id', '1')->first();
        return view('landing.terms_condition', compact('terms_condition'));
    }

    public function allstores(Request $request)
    {

        $cities = City::where('is_deleted', 2)->where('is_available', 1)->orderBy('reorder_id')->get();
        $banners = Promotionalbanner::with('vendor_info')->orderBy('reorder_id')->get();
        $stores = User::where('type', 2)->where('available_on_landing', 1)->whereIn('id', self::liveStoreIds());
        if ($request->country == "" && $request->city == "" && $request->stores == "") {
            $stores = $stores;
        }
        $city_name = "";
        if ($request->has('city') && $request->city != "") {
            $city = City::select('id')->where('name', $request->city)->first();
            $stores = $stores->where('city_id', $city->id);
        }
        if ($request->has('area') && $request->area != "") {
            $area = Areas::where('area', $request->area)->first();
            $stores = $stores->where('area_id', $area->id);
            $area_name = $area->area;
        }
        if ($request->has('store') && $request->store != "") {
            $store = StoreCategory::where('name', $request->store)->first();
            $stores = $stores->where('store_id', $store->id);
        }
        if ($stores != null) {
            $stores = $stores->paginate(12)->onEachSide(1);
        }

        return view('landing.store_list', compact('cities', 'stores', 'city_name', 'banners'));
    }

    /**
     * Marketplace — the customer discovery page (its own page). Auto-lists every live
     * merchant store (type=2, not deleted, available) with country/city/category/search + filters.
     */
    /**
     * A business enters the Marketplace exactly once, automatically: there is no separate
     * marketplace signup. It becomes discoverable when its account is live AND its primary
     * branch location has passed admin review (or it is an online/remote provider).
     * Shared with the sitemap so both always list the same stores.
     */
    /**
     * Why this store's public page would show "Store not available" — null when it opens. Same
     * rules as FrontMiddleware: account active, website activated, and a subscription that is
     * paid / approved / not expired (helper::checkplan).
     */
    public static function storefrontBlocker($vendor): ?string
    {
        if ((int) $vendor->is_deleted !== 2 || (int) $vendor->is_available !== 1 || !empty($vendor->archived_at)) {
            return trans('labels.mp_account_inactive');
        }
        if (\App\Helpers\Systems::hasPaid($vendor) && !\App\Helpers\Systems::isLive($vendor)) {
            return trans('labels.mp_website_not_activated');
        }

        $timezone = date_default_timezone_get(); // checkplan switches to the vendor's timezone
        try {
            $plan = helper::checkplan($vendor->id, '3')->getData();
        } catch (\Throwable $th) {
            $plan = null;
        } finally {
            date_default_timezone_set($timezone);
        }
        if ($plan === null) {
            return trans('labels.mp_no_active_plan');
        }
        if ((int) ($plan->status ?? 1) === 2) {
            $message = trim(strip_tags((string) ($plan->message ?? ''))) ?: trans('labels.mp_no_active_plan');

            return trim($message . ' ' . (!empty($plan->plan_date) ? helper::date_format($plan->plan_date, 1) : ''));
        }

        return null;
    }

    /**
     * Why this store is not listed in the Marketplace — null when it is. The storefront must open,
     * the account must be live, and the main branch must pass the location review (Admin >
     * Locations), exactly what marketplaceVisibleIds() queries.
     */
    public static function marketplaceBlocker($vendor): ?string
    {
        if ($reason = self::storefrontBlocker($vendor)) {
            return $reason;
        }
        if (!in_array($vendor->account_status, ['provisionally_active', 'correction_required', 'verified_active'], true)) {
            return trans('labels.mp_website_not_activated');
        }
        $branch = \App\Models\VendorBranch::where('vendor_id', $vendor->id)->where('is_primary', 1)->first();
        if (!$branch) {
            return trans('labels.mp_no_branch');
        }
        if ((int) $branch->is_available !== 1) {
            return trans('labels.mp_branch_inactive');
        }
        if ((int) $branch->is_remote !== 1 && (empty($branch->latitude) || empty($branch->longitude))) {
            return trans('labels.mp_branch_no_gps');
        }
        if ($branch->review_status === 'rejected') {
            return trans('labels.mp_branch_rejected');
        }
        if ($branch->review_status !== 'verified') {
            return trans('labels.mp_branch_pending');
        }

        return null;
    }

    /**
     * Vendor ids whose public storefront actually opens. Every public store listing filters by
     * this, so a visitor never clicks through to "Store not available".
     */
    public static function liveStoreIds()
    {
        static $ids = null; // once per request — checkplan costs a few queries per vendor
        if ($ids === null) {
            $ids = User::where('type', 2)->where('is_deleted', 2)->where('is_available', 1)->whereNull('archived_at')->get()
                ->filter(fn($vendor) => self::storefrontBlocker($vendor) === null)
                ->pluck('id')->values();
        }

        return $ids;
    }
    public static function marketplaceVisibleIds()
    {
        return \App\Models\VendorBranch::where('is_primary', 1)
            ->whereIn('vendor_id', self::liveStoreIds())
            ->where('is_available', 1)
            ->where('review_status', 'verified')
            ->where(function ($q) {
                $q->where('is_remote', 1)
                    ->orWhere(function ($w) {
                        $w->whereNotNull('latitude')->whereNotNull('longitude');
                    });
            })
            ->pluck('vendor_id');
    }

    public static function marketplaceVendors($visibleIds = null)
    {
        return User::where('users.type', 2)
            ->where('users.is_deleted', 2)
            ->where('users.is_available', 1)
            ->whereNull('users.archived_at')
            ->whereIn('users.account_status', ['provisionally_active', 'correction_required', 'verified_active'])
            ->whereIn('users.id', $visibleIds ?? self::marketplaceVisibleIds());
    }

    public function marketplace(Request $request)
    {
        // ---- selected filters ----
        $country = trim((string) $request->country);
        $city    = trim((string) $request->city);
        $store   = trim((string) $request->store);   // store category name
        $q       = trim((string) $request->q);
        $filter  = trim((string) $request->filter);  // near | open | top | featured | new
        $lat     = $request->lat;                     // optional (Near Me)
        $lng     = $request->lng;

        // ---- base: live/approved merchant stores ----
        $storeCols = [
            'users.id', 'users.name', 'users.slug', 'users.city_id', 'users.store_id',
            'users.country', 'users.is_delivery', 'users.available_on_landing',
            'users.latitude', 'users.longitude',
            'settings.website_title', 'settings.description', 'settings.logo',
            'settings.cover_image', 'settings.business_type',
        ];
        $visibleIds = self::marketplaceVisibleIds();

        $base = function () use ($storeCols, $visibleIds) {
            return self::marketplaceVendors($visibleIds)
                ->join('settings', 'users.id', '=', 'settings.vendor_id')
                ->select($storeCols);
        };

        // ---- filter option lists ----
        $countries = User::where('type', 2)->where('is_deleted', 2)->where('is_available', 1)
            ->whereNotNull('country')->where('country', '!=', '')
            ->distinct()->orderBy('country')->pluck('country');
        // Cities are no longer hand-maintained — they come from what reverse geocoding filled in.
        $cities = \App\Models\VendorBranch::whereIn('vendor_id', $visibleIds)
            ->whereNotNull('city')->where('city', '!=', '')
            ->distinct()->orderBy('city')->pluck('city')
            ->map(fn($c) => (object) ['name' => $c])->values();
        $categories = StoreCategory::where('is_deleted', 2)->where('is_available', 1)->orderBy('reorder_id')->get();
        $banners    = Promotionalbanner::with('vendor_info')->orderBy('reorder_id')->get();

        // ---- ratings (approved reviews) keyed by vendor ----
        $ratings = Testimonials::where('status', 1)
            ->selectRaw('vendor_id, ROUND(AVG(star),1) as avg_rating, COUNT(*) as cnt')
            ->groupBy('vendor_id')->get()->keyBy('vendor_id');

        // ---- main query with location/category/search filters ----
        $query = $base();
        if ($country !== '') $query->where('users.country', $country);
        if ($city !== '') {
            $legacy = City::where('name', $city)->first();
            $query->where(function ($w) use ($city, $legacy) {
                $w->where('users.city_name', $city);
                if ($legacy) $w->orWhere('users.city_id', $legacy->id);
            });
        }
        if ($store !== '') { $sc = StoreCategory::where('name', $store)->first(); if ($sc) $query->where('users.store_id', $sc->id); }
        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('settings.website_title', 'like', "%{$q}%")
                    ->orWhere('users.name', 'like', "%{$q}%")
                    ->orWhere('settings.description', 'like', "%{$q}%");
            });
        }

        $pool = $query->get();

        // timings for "open now" (one query, grouped by vendor)
        $timings = Timing::whereIn('vendor_id', $pool->pluck('id'))->get()->groupBy('vendor_id');

        // category name lookup for cards
        $catNames = $categories->pluck('name', 'id');

        // decorate each store with rating / open-now / distance / display bits
        $decorate = function ($s) use ($ratings, $timings, $catNames, $lat, $lng) {
            $r = $ratings->get($s->id);
            $s->avg_rating   = $r ? (float) $r->avg_rating : 0;
            $s->rating_count = $r ? (int) $r->cnt : 0;
            $s->is_open      = $this->isOpenNow($timings->get($s->id));
            $s->cat_name     = $s->store_id && isset($catNames[$s->store_id]) ? $catNames[$s->store_id] : ucfirst((string) $s->business_type);
            $s->distance     = ($lat && $lng && $s->latitude && $s->longitude)
                ? $this->haversine($lat, $lng, $s->latitude, $s->longitude) : null;
            return $s;
        };
        $pool = $pool->map($decorate);

        // ---- discovery rails (reflect country/city context, ignore chip filter) ----
        // Keep the rails MUTUALLY EXCLUSIVE so a store never repeats across Featured/Popular/Latest,
        // and only surface the extra rails when there are enough stores to make them meaningful —
        // otherwise the "All stores" grid alone is cleaner (no repetition).
        $poolCount = $pool->count();
        $featured = collect();
        $popular  = collect();
        $latest   = collect();
        if ($poolCount >= 6) {
            $featured = $pool->where('available_on_landing', 1)->take(8)->values();
            if ($featured->isEmpty()) $featured = $pool->sortByDesc('rating_count')->take(8)->values();
            $usedIds = $featured->pluck('id')->all();
            if ($poolCount >= 12) {
                $popular = $pool->whereNotIn('id', $usedIds)
                    ->sortByDesc(function ($s) { return [$s->rating_count, $s->avg_rating]; })->take(8)->values();
                $usedIds = array_merge($usedIds, $popular->pluck('id')->all());
                $latest  = $pool->whereNotIn('id', $usedIds)->sortByDesc('id')->take(8)->values();
            }
        }

        // ---- main grid: apply the active chip filter + sort ----
        $stores = $pool;
        switch ($filter) {
            case 'featured': $stores = $stores->where('available_on_landing', 1)->values(); break;
            case 'open':     $stores = $stores->where('is_open', true)->values(); break;
            case 'top':      $stores = $stores->sortByDesc('avg_rating')->values(); break;
            case 'new':      $stores = $stores->sortByDesc('id')->values(); break;
            case 'near':
                $stores = ($lat && $lng)
                    ? $stores->filter(fn($s) => $s->distance !== null)->sortBy('distance')->values()
                    : $stores->sortByDesc('id')->values();
                break;
            default:         $stores = $stores->sortByDesc('available_on_landing')->sortByDesc('id')->values();
        }

        $totalStores = $pool->count();

        // Platform default title (vendor 1) — used to fall back to the store's own name when uncustomised.
        $platformTitle = optional(\App\Models\Settings::where('vendor_id', 1)->first())->website_title;

        return view('landing.marketplace', compact(
            'stores', 'featured', 'latest', 'popular', 'countries', 'cities', 'categories',
            'banners', 'ratings', 'country', 'city', 'store', 'q', 'filter', 'totalStores', 'platformTitle'
        ));
    }

    /** Best-effort "open now" from a vendor's timing rows. No timings set = treated as open. */
    private function isOpenNow($rows): bool
    {
        if (empty($rows) || count($rows) === 0) return true;
        try {
            $today = strtolower(date('l'));
            $now = date('H:i');
            foreach ($rows as $t) {
                if (strtolower($t->day) !== $today) continue;
                if ($t->is_always_close == 1) return false;
                $open  = substr((string) $t->open_time, 0, 5);
                $close = substr((string) $t->close_time, 0, 5);
                if ($open === '' || $close === '') return true;
                return $now >= $open && $now <= $close;
            }
        } catch (\Throwable $e) {
            return true;
        }
        return true;
    }

    /** Distance in km between two lat/lng points. */
    private function haversine($lat1, $lon1, $lat2, $lon2): float
    {
        $r = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) ** 2;
        return $r * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }


    public function blogs_details($id)
    {
        $blog = Blog::where('vendor_id', '1')->where('id', $id)->first();
        $blogdata = Blog::where('vendor_id', '1')->where('id', '!=', $id)->orderBy('reorder_id')->get();
        return view('landing.blog_details', compact('blog', 'blogdata'));
    }

    public function faqs()
    {
        $allfaqs = Faq::where('vendor_id', '1')->orderBy('reorder_id')->get();
        return view('landing.faqs', compact('allfaqs'));
    }
}
