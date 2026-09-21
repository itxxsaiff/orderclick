<?php

namespace App\Helpers;

use App\Models\Item;
use App\Models\Settings;
use App\Models\User;
use App\Models\Timing;
use App\Models\Order;
use App\Models\Variants;
use App\Models\OrderDetails;
use App\Models\Transaction;
use App\Models\CustomStatus;
use App\Models\Payment;
use App\Models\PricingPlan;
use App\Models\SocialLinks;
use App\Models\SystemAddons;
use App\Models\RoleAccess;
use App\Models\RoleManager;
use App\Models\TopDeals;
use App\Models\Cart;
use App\Models\Tax;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use App\Models\Languages;
use App\Models\StoreCategory;
use Illuminate\Support\Str;
use App\Models\City;
use App\Models\Pixcel;
use App\Models\AppSettings;
use App\Helpers\loyaltyhelper;
use App\Models\AgeVerification;
use App\Models\CurrencySettings;
use App\Models\CustomDomain;
use App\Models\Footerfeatures;
use App\Models\OtherSettings;
use App\Models\WhatsappMessage;
use App\Models\TelegramMessage;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;

class helper
{
    public static function appdata($vendor_id)
    {
        if (file_exists(storage_path('installed'))) {
            $host = request()->getHost() ?: ($_SERVER['HTTP_HOST'] ?? env('WEBSITE_HOST'));
            if ($host  ==  env('WEBSITE_HOST')) {
                $data = Settings::first();
                if (!empty($vendor_id)) {
                    $data = Settings::where('vendor_id', $vendor_id)->first();
                    if (empty($data)) {
                        $data = Settings::where('vendor_id', 1)->first();
                    }
                }
            }
            // if the current host doesn't contain the website domain (meaning, custom domain)
            else {
                $data = Settings::where('custom_domain', $host)->first();
                if (empty($data)) {
                    $data = Settings::where('vendor_id', 1)->first();
                }
            }
            return $data;
        } else {
            return redirect('install');
            exit;
        }
    }
    public static function adminappdata()
    {
        if (file_exists(storage_path('installed'))) {
            $data = Settings::where('vendor_id', 1)->first();
            return $data;
        } else {
            return redirect('install');
            exit;
        }
    }
    public static function otherappdata($vendor_id)
    {

        if (file_exists(storage_path('installed'))) {
            if (!empty($vendor_id)) {
                $data = OtherSettings::where('vendor_id', $vendor_id)->first();
                if (empty($data)) {
                    $data = OtherSettings::where('vendor_id', 1)->first();
                }
            } else {
                $data = OtherSettings::where('vendor_id', 1)->first();
            }
            return $data;
        } else {
            return redirect('install');
            exit;
        }
    }

    public static function telegramdata($vendor_id)
    {
        $data = TelegramMessage::where('vendor_id', $vendor_id)->first();
        return $data;
    }

    // front
    public static function vendordata($id)
    {
        $data = User::where('id', $id)->where('is_available', 1)->where('is_deleted', 2)->first();
        return $data;
    }

    public static function vendor_data()
    {
        $host = $_SERVER['HTTP_HOST'];
        if ($host  ==  env('WEBSITE_HOST')) {
            $vendordata = User::first();
            if (!empty(request()->vendor)) {
                $vendordata = User::where('slug', request()->vendor)->first();
            }
        }
        // if the current host doesn't contain the website domain (meaning, custom domain)
        else {
            $data = Settings::where('custom_domain', $host)->first();
            $vendordata = User::where('id', $data->vendor_id)->first();
        }
        return $vendordata;
    }

    /**
     * A real food photo (Unsplash) matched to a dish name — used as a nice fallback
     * when a product/service has no uploaded image. $seed keeps the pick stable per item.
     */
    public static function food_image($name = '', $seed = 0, $set = 'food')
    {
        if ($set === 'retail') {
            return self::retail_image($name, $seed);
        }
        $map = [
            'margherita' => '1513104890138-7c749659a591', 'pepperoni' => '1513104890138-7c749659a591',
            'hawaiian' => '1513104890138-7c749659a591', 'calzone' => '1513104890138-7c749659a591',
            'cheeseburger' => '1568901346375-23c9450c58cd', 'hamburger' => '1568901346375-23c9450c58cd',
            'cappuccino' => '1495474472287-4d71bcdd2085', 'espresso' => '1495474472287-4d71bcdd2085',
            'mocha' => '1495474472287-4d71bcdd2085', 'smoothie' => '1572490122747-3968b75cc699',
            'pizza' => '1513104890138-7c749659a591', 'burger' => '1568901346375-23c9450c58cd',
            'pasta' => '1551183053-bf91a1d81141', 'spaghetti' => '1551183053-bf91a1d81141',
            'noodle' => '1585032226651-759b368d7246', 'salad' => '1512621776951-a57141f2eefd',
            'fries' => '1630384060421-cb20d0e0649d', 'fry' => '1630384060421-cb20d0e0649d',
            'chicken' => '1587593810167-a84920ea0781', 'grill' => '1544025162-d76694265947',
            'shawarma' => '1529006557810-274b9b2fc783', 'wrap' => '1529006557810-274b9b2fc783',
            'roll' => '1553909489-cd47e0907980e', 'sandwich' => '1528735602780-2552fd46c7af',
            'sushi' => '1579871494447-9811cf80d66c', 'fish' => '1467003909585-2f8a72700288',
            'curry' => '1631452180519-c014fe946bc7', 'rice' => '1512058564366-18510be2db19',
            'biryani' => '1563379091339-03b21ab4a4f8', 'steak' => '1546964124-0cce460f38ef',
            'soup' => '1547592166-23ac45744acd', 'taco' => '1565299624946-b28f40a0ae38',
            'dessert' => '1551024601-bec78aea704b', 'cake' => '1578985545062-69928b1d9587',
            'ice cream' => '1497034825429-c343d7c6a68f', 'donut' => '1551024506-0bccd828d307',
            'coffee' => '1495474472287-4d71bcdd2085', 'latte' => '1495474472287-4d71bcdd2085',
            'juice' => '1600271886742-f049cd451bba', 'drink' => '1544145945-f90425340c7e',
            'cola' => '1554866585-cd94860890b7', 'water' => '1523362628745-0c100150b504',
            'tea' => '1544787219-7f47ccb76574', 'shake' => '1572490122747-3968b75cc699',
            'breakfast' => '1533089860892-a7c6f0a88666', 'bread' => '1509440159596-0249088772ff',
        ];
        $n = strtolower((string) $name);
        foreach ($map as $k => $id) {
            if (str_contains($n, $k)) {
                return 'https://images.unsplash.com/photo-' . $id . '?auto=format&fit=crop&w=640&q=72';
            }
        }
        $fallbacks = ['1504674900247-0877df9cc836', '1546069901-ba9599a7e63c', '1476224203421-9ac39bcb3327', '1540189549336-e6e99c3679fe', '1555939594-58d7cb561ad1', '1567620905732-2d1ec7ab7445'];
        $pick = $fallbacks[abs((int) $seed) % count($fallbacks)];
        return 'https://images.unsplash.com/photo-' . $pick . '?auto=format&fit=crop&w=640&q=72';
    }

    /** Product photo for retail / fashion / electronics stores. */
    public static function retail_image($name = '', $seed = 0)
    {
        $map = [
            'dress' => '1595777457583-95e059d581b8', 'shirt' => '1521572163474-6864f9cf17ab',
            'tshirt' => '1521572163474-6864f9cf17ab', 't-shirt' => '1521572163474-6864f9cf17ab',
            'jean' => '1542272604-787c3835535d', 'trouser' => '1473966968600-fa801b869a1a',
            'jacket' => '1551028719-00167b16eac5', 'hoodie' => '1556821840-3a63f95609a7',
            'shoe' => '1542291026-7eec264c27ff', 'sneaker' => '1600185365483-26d7a4cc7519',
            'heel' => '1543163521-1bf539c55dd2', 'bag' => '1584917865442-de89df76afd3',
            'handbag' => '1584917865442-de89df76afd3', 'watch' => '1524592094714-0f0654e20314',
            'ring' => '1605100804763-247f67b3557e', 'jewel' => '1515562141207-7a88fb7ce338',
            'perfume' => '1541643600914-78b084683601', 'sunglass' => '1511499767150-a48a237f0083',
            'phone' => '1511707171634-5f897ff02aa9', 'mobile' => '1511707171634-5f897ff02aa9',
            'laptop' => '1496181133206-80ce9b88a853', 'headphone' => '1505740420928-5e560c06d30e',
            'earbud' => '1590658268037-6bf12165a8df', 'camera' => '1516035069371-29a1b244cc32',
            'watch smart' => '1579586337278-3befd40fd17a', 'flower' => '1490750967868-88aa4486c946',
            'gift' => '1549465220-1a8b9238cd48', 'toy' => '1558877385-8c1b8d2b0f88',
            'book' => '1512820790803-83ca734da794', 'cosmetic' => '1596462502278-27bfdc403348',
            'makeup' => '1596462502278-27bfdc403348', 'hat' => '1521369909029-2afed882baee',
        ];
        $n = strtolower((string) $name);
        foreach ($map as $k => $id) {
            if (str_contains($n, $k)) {
                return 'https://images.unsplash.com/photo-' . $id . '?auto=format&fit=crop&w=640&q=72';
            }
        }
        $fallbacks = ['1441984904996-e0b6ba687e04', '1472851294608-062f824d29cc', '1445205170230-053b83016050', '1483985988355-763728e1935b', '1490481651871-ab68de25d43d', '1560243563-062bfc001d68'];
        $pick = $fallbacks[abs((int) $seed) % count($fallbacks)];
        return 'https://images.unsplash.com/photo-' . $pick . '?auto=format&fit=crop&w=640&q=72';
    }

    /**
     * Public URL of a vendor verification document (PDF or image).
     *
     * These live in admin-assets/documents. image_path() knows nothing about that folder, so it
     * silently returned the "no image" placeholder - the admin opened a document and got a grey
     * picture instead of the file.
     */
    /**
     * The platform logo as a data: URI for PDF invoices.
     *
     * The PDF renderer cannot fetch the logo over HTTP reliably, so the bytes are embedded. The
     * uploaded logo can be several megabytes, which would bloat and slow every invoice, so a
     * height-capped copy is generated once and cached outside the public folder.
     */
    public static function invoice_logo(?string $logo, int $maxHeight = 120): ?string
    {
        $logo = basename((string) $logo);
        if ($logo === '') {
            return null;
        }

        $source = storage_path('app/public/admin-assets/images/about/logo/' . $logo);
        if (!is_file($source)) {
            return null;
        }

        try {
            $cache = storage_path('app/invoice-logo-' . md5($logo . filemtime($source)) . '.png');

            if (!is_file($cache) && extension_loaded('gd') && filesize($source) > 120000) {
                $size = @getimagesize($source);
                if ($size && $size[1] > $maxHeight) {
                    $img = match (strtolower(pathinfo($source, PATHINFO_EXTENSION))) {
                        'png'          => @imagecreatefrompng($source),
                        'jpg', 'jpeg'  => @imagecreatefromjpeg($source),
                        'webp'         => @imagecreatefromwebp($source),
                        default        => null,
                    };
                    if ($img) {
                        $scaled = imagescale($img, (int) round($size[0] * $maxHeight / $size[1]), $maxHeight);
                        if ($scaled) {
                            imagealphablending($scaled, false);
                            imagesavealpha($scaled, true);
                            imagepng($scaled, $cache, 8);
                            imagedestroy($scaled);
                        }
                        imagedestroy($img);
                    }
                }
            }

            $file = is_file($cache) ? $cache : $source;
            $type = strtolower(pathinfo($file, PATHINFO_EXTENSION)) ?: 'png';

            return 'data:image/' . ($type === 'jpg' ? 'jpeg' : $type) . ';base64,' . base64_encode(file_get_contents($file));
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Translated label for a store category. Categories are admin-entered rows, so the name in
     * the database is the English one; when a translation key exists for it (landing.cat_*) the
     * current language wins, otherwise the stored name is shown unchanged.
     */
    public static function category_label($name)
    {
        $name = trim((string) $name);
        if ($name === '') {
            return $name;
        }
        $key = 'landing.cat_' . trim(preg_replace('/_+/', '_', preg_replace('/[^a-z0-9]+/u', '_', mb_strtolower($name))), '_');

        return \Illuminate\Support\Facades\Lang::has($key) ? trans($key) : $name;
    }

    public static function document_path($file)
    {
        $file = basename((string) $file);
        if ($file === '') {
            return '';
        }

        return asset('storage/app/public/admin-assets/documents/' . $file);
    }

    public static function image_path($image)
    {
        if ($image == "" && $image == null) {
            $path = asset('storage/app/public/admin-assets/images/about/defaultimages/item-placeholder.png');
        } else {
            $path = asset('storage/app/public/admin-assets/images/about/defaultimages/item-placeholder.png');
        }

        if (Str::contains($image, 'nodata')) {
            if (file_exists(storage_path('app/public/admin-assets/images/' . $image))) {
                $path = asset('storage/app/public/admin-assets/images/' . $image);
            }
        }
        if (Str::contains($image, 'authformbgimage') || Str::contains($image, 'quick-call')) {
            if (file_exists(storage_path('app/public/admin-assets/images/about/' . $image))) {
                $path = asset('storage/app/public/admin-assets/images/about/' . $image);
            }
        }
        if (Str::contains($image, 'theme-')) {
            if (file_exists(storage_path('app/public/admin-assets/images/theme/' . $image))) {
                $path = asset('storage/app/public/admin-assets/images/theme/' . $image);
            }
        }
        if (Str::contains($image, 'work-')) {
            if (file_exists(storage_path('app/public/landing/images/png/' . $image))) {
                $path = asset('storage/app/public/landing/images/png/' . $image);
            }
        }
        if (Str::contains($image, 'feature-')) {
            if (file_exists(storage_path('app/public/admin-assets/images/feature/' . $image))) {
                $path = asset('storage/app/public/admin-assets/images/feature/' . $image);
            }
        }
        if (Str::contains($image, 'testimonial-')) {
            if (file_exists(storage_path('app/public/admin-assets/images/testimonials/' . $image))) {
                $path = asset('storage/app/public/admin-assets/images/testimonials/' . $image);
            }
        }
        if (Str::contains($image, 'screenshot-')) {
            if (file_exists(storage_path('app/public/admin-assets/images/screenshot/' . $image))) {
                $path = asset('storage/app/public/admin-assets/images/screenshot/' . $image);
            }
        }
        if (Str::contains($image, 'banktransfer') || Str::contains($image, 'cod') || Str::contains($image, 'razorpay') || Str::contains($image, 'stripe') || Str::contains($image, 'wallet') || Str::contains($image, 'flutterwave') || Str::contains($image, 'paystack') || Str::contains($image, 'mercadopago') || Str::contains($image, 'paypal') || Str::contains($image, 'myfatoorah') || Str::contains($image, 'toyyibpay') || Str::contains($image, 'phonepe') || Str::contains($image, 'payment') || Str::contains($image, 'paytab') || Str::contains($image, 'mollie') || Str::contains($image, 'khalti') || Str::contains($image, 'xendit')) {
            if (file_exists(storage_path('app/public/admin-assets/images/about/payment/' . $image))) {
                $path = asset('storage/app/public/admin-assets/images/about/payment/' . $image);
            }
        }
        if (Str::contains($image, 'trusted_badge')) {
            if (file_exists(storage_path('app/public/admin-assets/images/about/trusted_badge/' . $image))) {
                $path = asset('storage/app/public/admin-assets/images/about/trusted_badge/' . $image);
            }
        }
        if (Str::contains($image, 'res')) {
            if (file_exists(storage_path('app/public/admin-assets/images/about/' . $image))) {
                $path = asset('storage/app/public/admin-assets/images/about/' . $image);
            }
        }

        if (Str::contains($image, 'logo')) {
            if (file_exists(storage_path('app/public/admin-assets/images/about/logo/' . $image))) {
                $path = asset('storage/app/public/admin-assets/images/about/logo/' . $image);
            }
            if (file_exists(storage_path('app/public/admin-assets/images/about/defaultimages/' . $image))) {
                $path = asset('storage/app/public/admin-assets/images/about/defaultimages/' . $image);
            }
        }
        if (Str::contains($image, 'darklogo')) {
            if (file_exists(storage_path('app/public/admin-assets/images/about/darklogo/' . $image))) {
                $path = asset('storage/app/public/admin-assets/images/about/darklogo/' . $image);
            }
            if (file_exists(storage_path('app/public/admin-assets/images/about/defaultimages/' . $image))) {
                $path = asset('storage/app/public/admin-assets/images/about/defaultimages/' . $image);
            }
        }

        if (Str::contains($image, 'favicon')) {
            if (file_exists(storage_path('app/public/admin-assets/images/about/favicon/' . $image))) {
                $path = asset('storage/app/public/admin-assets/images/about/favicon/' . $image);
            }
            if (file_exists(storage_path('app/public/admin-assets/images/about/defaultimages/' . $image))) {
                $path = asset('storage/app/public/admin-assets/images/about/defaultimages/' . $image);
            }
        }
        if (Str::contains($image, 'og_image')) {
            if (file_exists(storage_path('app/public/admin-assets/images/about/og_image/' . $image))) {
                $path = asset('storage/app/public/admin-assets/images/about/og_image/' . $image);
            }
            if (file_exists(storage_path('app/public/admin-assets/images/about/defaultimages/' . $image))) {
                $path = asset('storage/app/public/admin-assets/images/about/defaultimages/' . $image);
            }
        }
        if (Str::contains($image, 'item-')) {
            if (file_exists(storage_path('app/public/item/' . $image))) {
                $path = asset('storage/app/public/item/' . $image);
            }
        }
        if (Str::contains($image, 'banner') || Str::contains($image, 'promotion-')) {
            if (file_exists(storage_path('app/public/admin-assets/images/banners/' . $image))) {
                $path = asset('storage/app/public/admin-assets/images/banners/' . $image);
            }
        }
        if (Str::contains($image, 'order')) {
            if (file_exists(storage_path('app/public/front/images/' . $image))) {
                $path = asset('storage/app/public/front/images/' . $image);
            }
        }
        if (Str::contains($image, 'profile')) {
            if (file_exists(storage_path('app/public/admin-assets/images/profile/' . $image))) {
                $path = asset('storage/app/public/admin-assets/images/profile/' . $image);
            }
        }
        if (Str::contains($image, 'category')) {
            if (file_exists(storage_path('app/public/admin-assets/images/category/' . $image))) {
                $path = asset('storage/app/public/admin-assets/images/category/' . $image);
            }
        }
        if (Str::contains($image, 'blog')) {
            if (file_exists(storage_path('app/public/admin-assets/images/blog/' . $image))) {
                $path = asset('storage/app/public/admin-assets/images/blog/' . $image);
            }
        }
        if (Str::contains($image, 'flag')) {
            if (file_exists(storage_path('app/public/admin-assets/images/language/' . $image))) {
                $path = asset('storage/app/public/admin-assets/images/language/' . $image);
            }
        }
        if (Str::contains($image, 'cover')) {
            if (file_exists(storage_path('app/public/admin-assets/images/coverimage/' . $image))) {
                $path = asset('storage/app/public/admin-assets/images/coverimage/' . $image);
            }
        }
        if (Str::contains($image, 'subscribe_bg')) {
            if (file_exists(storage_path('app/public/admin-assets/images/subscribe/' . $image))) {
                $path = asset('storage/app/public/admin-assets/images/subscribe/' . $image);
            }
        }
        if (Str::contains($image, 'maintenance') || Str::contains($image, 'store_unavailable') || Str::contains($image, 'auth') || Str::contains($image, 'whoweare') || Str::contains($image, 'order_success') || Str::contains($image, 'no_data') || Str::contains($image, 'faq') || Str::contains($image, 'book_table') || Str::contains($image, 'subscribe_newsletter')) {
            if (file_exists(storage_path('app/public/admin-assets/images/index/' . $image))) {
                $path = asset('storage/app/public/admin-assets/images/index/' . $image);
            }
        }
        return $path;
    }

    public static function currency_formate($price, $vendor_id)
    {
        $price = floatval($price) * helper::currencyinfo($vendor_id)->exchange_rate;

       if (helper::currencyinfo($vendor_id)->currency_position == "1"){
            if (helper::currencyinfo($vendor_id)->decimal_separator == 1) {
                if (helper::currencyinfo($vendor_id)->currency_space == 1) {
                    return helper::currencyinfo($vendor_id)->currency . ' ' . number_format((float)$price, helper::currencyinfo($vendor_id)->currency_formate, '.', ',');
                } else {
                    return helper::currencyinfo($vendor_id)->currency . number_format((float)$price, helper::currencyinfo($vendor_id)->currency_formate, '.', ',');
                }
            } else {
                if (helper::currencyinfo($vendor_id)->currency_space == 1) {
                    return helper::currencyinfo($vendor_id)->currency . ' ' . number_format((float)$price, helper::currencyinfo($vendor_id)->currency_formate, ',', '.');
                } else {
                    return helper::currencyinfo($vendor_id)->currency . number_format((float)$price, helper::currencyinfo($vendor_id)->currency_formate, ',', '.');
                }
            }
        }
        if (helper::currencyinfo($vendor_id)->currency_position == "2"){
            if (helper::currencyinfo($vendor_id)->decimal_separator == 1) {
                if (helper::currencyinfo($vendor_id)->currency_space == 1) {
                    return number_format((float)$price, helper::currencyinfo($vendor_id)->currency_formate, '.', ',') . ' ' . helper::currencyinfo($vendor_id)->currency;
                } else {
                    return number_format((float)$price, helper::currencyinfo($vendor_id)->currency_formate, '.', ',') . helper::currencyinfo($vendor_id)->currency;
                }
            } else {
                if (helper::currencyinfo($vendor_id)->currency_space == 1) {
                    return number_format((float)$price, helper::currencyinfo($vendor_id)->currency_formate, ',', '.') . ' ' . helper::currencyinfo($vendor_id)->currency;
                } else {
                    return number_format((float)$price, helper::currencyinfo($vendor_id)->currency_formate, ',', '.') . helper::currencyinfo($vendor_id)->currency;
                }
                return number_format((float)$price, helper::currencyinfo($vendor_id)->currency_formate, ',', '.') . helper::currencyinfo($vendor_id)->currency;
            }
        }
        return $price;
    }
    public static function vendortime($vendor)
    {
        date_default_timezone_set(@helper::appdata($vendor)->timezone);
        $t = date('d-m-Y');
        $time = Timing::select('close_time')->where('vendor_id', $vendor)->where('day', date("l", strtotime($t)))->first();
        $txt = "Opened until " . date("D", strtotime($t)) . " " . $time->close_time . "";
        return $txt;
    }
    public static function date_format($date, $vendor_id)
    {
        return date(helper::appdata($vendor_id)->date_format, strtotime($date));
    }
    public static function time_format($time, $vendor_id)
    {
        if (helper::appdata($vendor_id)->time_format == 1) {
            return $time->format('H:i');
        } else {
            return $time->format('h:i A');
        }
    }

    public static function get_city()
    {
        $city =  City::where('is_deleted', '2')->where('is_available', '1')->get();
        return $city;
    }

    public static function get_plan_exp_date($duration, $days)
    {
        date_default_timezone_set(@helper::appdata('')->timezone);
        $purchasedate = date("Y-m-d h:i:sa");
        $exdate = "";
        if (!empty($duration) && $duration != "") {
            if ($duration == "1") {
                $exdate = date('Y-m-d', strtotime($purchasedate . ' + 30 days'));
            }
            if ($duration == "2") {
                $exdate = date('Y-m-d', strtotime($purchasedate . ' + 90 days'));
            }
            if ($duration == "3") {
                $exdate = date('Y-m-d', strtotime($purchasedate . ' + 180 days'));
            }
            if ($duration == "4") {
                $exdate = date('Y-m-d', strtotime($purchasedate . ' + 365 days'));
            }
            if ($duration == "5") {
                $exdate = "";
            }
        }
        if (!empty($days) && $days != "") {
            $exdate = date('Y-m-d', strtotime($purchasedate . ' + ' . $days . 'days'));
        }
        return $exdate;
    }
    public static function timings($vendor)
    {
        $host = $_SERVER['HTTP_HOST'];
        if ($host  ==  env('WEBSITE_HOST')) {
            $vdata = $vendor;
        }
        // if the current host doesn't contain the website domain (meaning, custom domain)
        else {
            $storeinfo = Settings::where('custom_domain', $host)->first();
            $vdata = $storeinfo->vendor_id;
        }
        $timings = Timing::where('vendor_id', @$vdata)->get();
        return $timings;
    }
    public static function storeinfo($vendor)
    {
        $vendorinfo = User::where('slug', $vendor)->first();
        return $vendorinfo;
    }

    public static function getcartcount($vendor_id, $user_id)
    {
        $host = $_SERVER['HTTP_HOST'];
        if ($host  ==  env('WEBSITE_HOST')) {
            $vdata = $vendor_id;
        }
        // if the current host doesn't contain the website domain (meaning, custom domain)
        else {
            $storeinfo = Settings::where('custom_domain', $host)->first();
            $vdata = $storeinfo->vendor_id;
        }
        $session_id = Session::getId();

        if ($user_id != "" && Auth::user()->type == 3) {

            $cnt = Cart::where('vendor_id', $vdata)->where('user_id', $user_id)->where('buynow', 0)->count();
        } else {
            $cnt = Cart::where('vendor_id', $vdata)->where('session_id', $session_id)->where('buynow', 0)->count();
        }

        return $cnt;
    }


    public static function checkplan($id, $type)
    {
        $check = SystemAddons::where('unique_identifier', 'subscription')->first();

        if (@$check->activated != 1) {
            return response()->json(['status' => 1, 'message' => '', 'expdate' => "", 'showclick' => "0", 'plan_message' => '', 'plan_date' => '', 'checklimit' => '', 'bank_transfer' => ''], 200);
        }
        $host = $_SERVER['HTTP_HOST'];
        if ($host  ==  env('WEBSITE_HOST')) {
            $data = Settings::where('vendor_id', $id)->first();
            date_default_timezone_set(@helper::appdata($data->vendor_id)->timezone);
            $vendorinfo = User::where('id', $id)->first();
        }
        // if the current host doesn't contain the website domain (meaning, custom domain)
        else {
            $storeinfo = Settings::where('custom_domain', $host)->first();
            date_default_timezone_set(helper::appdata($storeinfo->vendor_id)->timezone);
            $vendorinfo = User::where('id', $storeinfo->vendor_id)->first();
        }

        $checkplan = Transaction::where('plan_id', $vendorinfo->plan_id)->where('vendor_id', $vendorinfo->id)->where('transaction_type', null)->orderByDesc('id')->first();
        $totalservice = Item::where('vendor_id', $vendorinfo->id)->count();

        // ---------------------------------------------------------------------------------
        // V2 account lifecycle. Runs before the legacy plan checks because a paid account can
        // now sit in setup mode with NO expiry date yet — which the old code below would read
        // as a lifetime subscription and wrongly put the storefront live.
        // $type === 3 means "a visitor is looking at the public storefront".
        // ---------------------------------------------------------------------------------
        $lifecycle = $vendorinfo->account_status ?? null;
        if ((int) $vendorinfo->type === 2 && $lifecycle !== null && $vendorinfo->allow_without_subscription != 1) {
            $blocked = function ($message) {
                return response()->json(['status' => 2, 'message' => $message, 'expdate' => '', 'showclick' => "0", 'plan_message' => $message, 'plan_date' => '', 'checklimit' => '', 'bank_transfer' => ''], 200);
            };

            if ($lifecycle === Systems::RESTRICTED) {
                return $blocked(trans('messages.account_blocked_by_admin'));
            }

            if ($lifecycle === Systems::PAID_SETUP_INCOMPLETE) {
                // Paid, but the public website has not been activated yet. The merchant keeps
                // full dashboard access to finish setting up; the storefront stays private.
                if ($type == 3) {
                    return $blocked(trans('messages.front_store_unavailable'));
                }
                return response()->json([
                    'status' => 1,
                    'message' => Systems::status($lifecycle)['text'],
                    'expdate' => '',
                    'showclick' => "0",
                    'plan_message' => Systems::status($lifecycle)['text'],
                    'plan_date' => '',
                    'checklimit' => '',
                    'bank_transfer' => '',
                ], 200);
            }
        }

        if ($vendorinfo->allow_without_subscription != 1) {
            if (!empty($checkplan)) {
                if ($vendorinfo->is_available == 2) {
                    return response()->json(['status' => 2, 'message' => trans('messages.account_blocked_by_admin'), 'showclick' => "0", 'plan_message' => '', 'plan_date' => '', 'checklimit' => '', 'bank_transfer' => ''], 200);
                }
                if ($checkplan->payment_type == 1) {
                    if ($checkplan->status == 1) {
                        return response()->json(['status' => 2, 'message' => trans('messages.cod_pending'), 'showclick' => "0", 'plan_message' => trans('messages.cod_pending'), 'plan_date' => '', 'checklimit' => '', 'bank_transfer' => '1'], 200);
                    } elseif ($checkplan->status == 3) {
                        return response()->json(['status' => 2, 'message' => trans('messages.cod_rejected'), 'showclick' => "1", 'plan_message' => trans('messages.cod_rejected'), 'plan_date' => '', 'checklimit' => '', 'bank_transfer' => ''], 200);
                    }
                }
                if ($checkplan->payment_type == '6') {
                    if ($checkplan->status == 1) {
                        return response()->json(['status' => 2, 'message' => trans('messages.bank_request_pending'), 'showclick' => "0", 'plan_message' => trans('messages.bank_request_pending'), 'plan_date' => '', 'checklimit' => '', 'bank_transfer' => '1'], 200);
                    } elseif ($checkplan->status == 3) {
                        return response()->json(['status' => 2, 'message' => trans('messages.bank_request_rejected'), 'showclick' => "1", 'plan_message' => trans('messages.bank_request_rejected'), 'plan_date' => '', 'checklimit' => '', 'bank_transfer' => ''], 200);
                    }
                }
                if ($checkplan->expire_date != "") {
                    if (date('Y-m-d') > $checkplan->expire_date) {

                        return response()->json(['status' => 2, 'message' => trans('messages.plan_expired'), 'expdate' => $checkplan->expire_date, 'showclick' => "1", 'plan_message' => trans('messages.plan_expired'), 'plan_date' => $checkplan->expire_date, 'checklimit' => '', 'bank_transfer' => ''], 200);
                    }
                }
                if (Str::contains(request()->url(), 'admin')) {
                    if ($checkplan->service_limit != -1) {
                        if ($totalservice >= $checkplan->service_limit) {
                            if (Auth::user()->type == 1) {
                                return response()->json(['status' => 2, 'message' => trans('messages.products_limit_exceeded'), 'expdate' => '', 'showclick' => "1", 'plan_message' => trans('messages.plan_expires'), 'plan_date' => '', 'checklimit' => '', 'bank_transfer' => ''], 200);
                            }
                            if (Auth::user()->type == 2) {
                                if ($checkplan->expire_date != "") {
                                    return response()->json(['status' => 2, 'message' => trans('messages.vendor_products_limit_message'), 'expdate' => '', 'showclick' => "1", 'plan_message' => trans('messages.plan_expires'), 'plan_date' => $checkplan->expire_date, 'checklimit' => 'service', 'bank_transfer' => ''], 200);
                                } else {
                                    return response()->json(['status' => 2, 'message' => trans('messages.vendor_products_limit_message'), 'expdate' => '', 'showclick' => "1", 'plan_message' => trans('messages.lifetime_subscription'), 'plan_date' => $checkplan->expire_date, 'checklimit' => 'service', 'bank_transfer' => ''], 200);
                                }
                            }
                        }
                    }
                    if ($checkplan->appoinment_limit != -1) {
                        if ($checkplan->appoinment_limit <= 0) {
                            if (Auth::user()->type == 1) {
                                return response()->json(['status' => 2, 'message' => trans('messages.order_limit_exceeded'), 'expdate' => '', 'showclick' => "1", 'plan_message' => trans('messages.plan_expires'), 'plan_date' => '', 'checklimit' => '', 'bank_transfer' => ''], 200);
                            }
                            if (Auth::user()->type == 2) {
                                if ($checkplan->expire_date != "") {
                                    return response()->json(['status' => 2, 'message' => trans('messages.vendor_order_limit_message'), 'expdate' => '', 'showclick' => "1", 'plan_message' => trans('messages.plan_expires'), 'plan_date' => $checkplan->expire_date, 'checklimit' => 'booking', 'bank_transfer' => ''], 200);
                                } else {
                                    return response()->json(['status' => 2, 'message' => trans('messages.vendor_order_limit_message'), 'expdate' => '', 'showclick' => "1", 'plan_message' => trans('messages.lifetime_subscription'), 'plan_date' => $checkplan->expire_date, 'checklimit' => 'service', 'bank_transfer' => ''], 200);
                                }
                            }
                        }
                    }
                }
                if ($type == 3) {
                    if ($checkplan->appoinment_limit != -1) {
                        if ($checkplan->appoinment_limit <= 0) {
                            return response()->json(['status' => 2, 'message' => trans('messages.front_store_unavailable'), 'expdate' => '', 'showclick' => "1", 'plan_message' => trans('messages.plan_expires'), 'plan_date' => '', 'checklimit' => 'booking', 'bank_transfer' => ''], 200);
                        }
                    }
                }
                if ($checkplan->expire_date != "") {

                    return response()->json(['status' => 1, 'message' => trans('messages.plan_expires'), 'expdate' => $checkplan->expire_date, 'showclick' => "0", 'plan_message' => trans('messages.plan_expires'), 'plan_date' => $checkplan->expire_date, 'checklimit' => '', 'bank_transfer' => ''], 200);
                } else {

                    return response()->json(['status' => 1, 'message' => trans('messages.lifetime_subscription'), 'expdate' => $checkplan->expire_date, 'showclick' => "0", 'plan_message' => trans('messages.lifetime_subscription'), 'plan_date' => $checkplan->expire_date, 'checklimit' => '', 'bank_transfer' => ''], 200);
                }
            } else {
                if (@Auth::user() && Auth::user()->type == 1) {
                    return response()->json(['status' => 2, 'message' => trans('messages.doesnot_select_any_plan'), 'expdate' => '', 'showclick' => "0", 'plan_message' => '', 'plan_date' => '', 'checklimit' => '', 'bank_transfer' => ''], 200);
                }
                // Vendor, employee, OR a not-logged-in store visitor -> plan is not active.
                return response()->json(['status' => 2, 'message' => trans('messages.vendor_plan_purchase_message'), 'expdate' => '', 'showclick' => "1", 'plan_message' => '', 'plan_date' => '', 'checklimit' => '', 'bank_transfer' => ''], 200);
            }
        } else {
            return response()->json(['status' => 1, 'message' => trans('messages.success'), 'plan_date' => ''], 200);
        }
    }

    public static function createorder($vendor, $user_id, $session_id, $payment_type_data, $payment_id, $customer_email, $customer_name, $customer_mobile, $stripeToken, $grand_total, $delivery_charge, $address, $building, $landmark, $postal_code, $discount_amount, $offer_type, $sub_total, $tax, $tax_name, $delivery_time, $delivery_date, $delivery_area, $couponcode, $order_type, $notes, $table_id, $filename, $buynow, $tips)
    {
        try {
            $host = $_SERVER['HTTP_HOST'];
            if ($host  ==  env('WEBSITE_HOST')) {
                $vendorinfo = @helper::vendordata($vendor);
            }
            // if the current host doesn't contain the website domain (meaning, custom domain)
            else {
                $vendorinfo = Settings::where('custom_domain', $host)->first();
            }
            date_default_timezone_set(@helper::appdata($vendor)->timezone);
            if ($user_id != "" || $user_id != null) {
                if ($buynow == 1) {
                    $data = Cart::where('user_id', $user_id)->where('vendor_id', $vendor)->where('buynow', 1)->get();
                } else {
                    $data = Cart::where('user_id', $user_id)->where('vendor_id', $vendor)->where('buynow', 0)->get();
                }
            } else {
                if ($buynow == 1) {
                    $data = Cart::where('session_id', $session_id)->where('vendor_id', $vendor)->where('buynow', 1)->get();
                } else {
                    $data = Cart::where('session_id', $session_id)->where('vendor_id', $vendor)->where('buynow', 0)->get();
                }
            }

            $defaultsatus = CustomStatus::where('vendor_id', $vendor)->where('type', 1)->where('order_type', $order_type)->where('is_available', 1)->where('is_deleted', 2)->first();
            if (empty($defaultsatus) && $defaultsatus == null) {
                return "false";
            }
            if ($data->count() > 0) {
                //payment_type = COD : 1,RazorPay : 2, Stripe : 3, Flutterwave : 4, Paystack : 5, Mercado Pago : 7, PayPal : 8, MyFatoorah : 9, toyyibpay : 10, phonepe : 11, paytab : 12

                if ($order_type == "2" || $order_type == "3") {
                    $delivery_charge = "0.00";
                    $address = "";
                    $building = "";
                    $landmark = "";
                    $postal_code = "";
                } else {
                    $delivery_charge = $delivery_charge;
                    $address = $address;
                    $building = $building;
                    $landmark = $landmark;
                    $postal_code = $postal_code;
                }
                if ($discount_amount == "NaN") {
                    $discount_amount = 0;
                } else {
                    $discount_amount = $discount_amount;
                }

                $getordernumber = Order::select('order_number', 'order_number_digit', 'order_number_start')->where('vendor_id', $vendor)->orderBy('id', 'DESC')->first();

                if (empty($getordernumber->order_number_digit)) {
                    $n = helper::appdata($vendor)->order_number_start;
                    $newbooking_number = str_pad($n, 0, STR_PAD_LEFT);
                } else {
                    if ($getordernumber->order_number_start == helper::appdata($vendor)->order_number_start) {
                        $n = (int)($getordernumber->order_number_digit);
                        $newbooking_number = str_pad($n + 1, 0, STR_PAD_LEFT);
                    } else {
                        $n = helper::appdata($vendor)->order_number_start;
                        $newbooking_number = str_pad($n, 0, STR_PAD_LEFT);
                    }
                }

                $order = new Order;
                $order_number = helper::appdata($vendor)->order_prefix . $newbooking_number;
                $order->order_number = $order_number;
                $order->order_number_digit = $newbooking_number;
                $order->order_number_start = helper::appdata($vendor)->order_number_start;

                $order->vendor_id = $vendor;
                $order->user_id = $user_id;
                $order->order_number = $order_number;
                $order->payment_type = $payment_type_data;
                $order->payment_id = @$payment_id;
                $order->sub_total = $sub_total;
                $order->tax = $tax;
                $order->tax_name = $tax_name;
                $order->grand_total = $grand_total - $tips;
                $order->tips = $tips;
                $order->status = $defaultsatus->id;
                $order->status_type = $defaultsatus->type;
                $order->address = $address;
                $order->delivery_time = $delivery_time;
                $order->delivery_date = $delivery_date;
                $order->delivery_area = $delivery_area;
                $order->delivery_charge = $delivery_charge;
                $order->discount_amount = $discount_amount;
                $order->offer_type = $offer_type;
                $order->couponcode = $couponcode;
                $order->order_type = $order_type;
                $order->table_id = $table_id;
                $order->building = $building;
                $order->landmark = $landmark;
                $order->pincode = $postal_code;
                $order->customer_name = $customer_name;
                $order->customer_email = $customer_email;
                $order->mobile = $customer_mobile;
                $order->order_notes = $notes;
                $order->loyalty_amount = '20';
                // Manual / offline methods -> unpaid (merchant confirms). Online gateways + wallet -> paid.
                // 1=Cash on Delivery, 6=Bank Transfer, 17=Cash, 18=Cash on Pickup, 19=BenefitPay, 20=Bank QR, 21=Payment Link
                if (in_array((string) $payment_type_data, ['1', '17', '18', '19', '20', '21'], true)) {
                    $order->payment_status = 1;
                } elseif ($payment_type_data == '6') {
                    $order->screenshot = $filename;
                    $order->payment_status = 1;
                } else {
                    $order->payment_status = 2;
                }

                if ($order->save()) {
                    $order_id = DB::getPdo()->lastInsertId();
                    if ($payment_type_data == 16) {
                        $checkuser = User::where('is_available', 1)->where('id', @Auth::user()->id)->first();
                        $checkuser->wallet = $checkuser->wallet - (float)$grand_total;
                        $transaction = new Transaction();
                        $transaction->vendor_id = @$vendor;
                        $transaction->user_id = @$checkuser->id;
                        $transaction->order_id = $order_id;
                        $transaction->order_number = $order_number;
                        $transaction->payment_type = 16;
                        $transaction->transaction_type = 2;
                        $transaction->amount = $grand_total - $tips;
                        $transaction->tips = $tips;
                        if ($transaction->save()) {
                            $checkuser->save();
                        }
                    }

                    foreach ($data as $value) {

                        $OrderPro = new OrderDetails;
                        $OrderPro->order_id = $order_id;
                        $OrderPro->item_id = $value['item_id'];
                        $OrderPro->item_name = $value['item_name'];
                        $OrderPro->item_image = $value['item_image'];
                        $OrderPro->extras_id = $value['extras_id'];
                        $OrderPro->extras_name = $value['extras_name'];
                        $OrderPro->extras_price = $value['extras_price'];
                        if ($value['variants_id'] == "") {
                            $product = Item::where('id', $value['item_id'])->first();
                            if ($product->stock_management == 1) {
                                $product->qty = (int)$product->qty - (int)$value['qty'];
                            }
                            $product->update();
                        } else {
                            $variant = Variants::where('item_id', $value['item_id'])->where('id', $value['variants_id'])->first();
                            if ($variant->stock_management == 1) {
                                $variant->qty = (int)$variant->qty - (int)$value['qty'];
                            }
                            $variant->update();
                        }
                        $OrderPro->price = $value['price'];
                        $OrderPro->variants_price = $value['item_price'];
                        $OrderPro->variants_id = $value['variants_id'];
                        $OrderPro->variants_name = $value['variants_name'];
                        $OrderPro->qty = $value['qty'];
                        $OrderPro->save();
                    }

                    if ($user_id != "" || $user_id != null) {
                        if ($buynow == 1) {
                            $data = Cart::where('user_id', $user_id)->where('buynow', 1)->delete();
                        } else {
                            $data = Cart::where('user_id', $user_id)->where('buynow', 0)->delete();
                        }
                    } else {
                        if ($buynow == 1) {
                            $data = Cart::where('session_id', $session_id)->where('buynow', 1)->delete();
                        } else {
                            $data = Cart::where('session_id', $session_id)->where('buynow', 0)->delete();
                        }
                    }

                    session()->forget(['offer_amount', 'offer_code', 'offer_type']);

                    if ($user_id != "" || $user_id != null) {
                        $count = Cart::where('user_id', $user_id)->count();
                    } else {
                        $count = Cart::where('session_id', $session_id)->count();
                    }

                    session()->put('cart', $count);

                    $trackurl = URL::to(@$vendorinfo->slug . '/track-order/' . $order_number);
                    $emaildata = @helper::emailconfigration($vendor);
                    Config::set('mail', $emaildata);
                    @helper::create_order_invoice($customer_email, $customer_name, $vendorinfo->email, $vendorinfo->name, $vendor, $order_number, $order_type, @helper::date_format($delivery_date, $vendor), $delivery_time, @helper::currency_formate($grand_total, $vendor), $trackurl);

                    $title = trans('labels.order_update');
                    $body = "Congratulations! Your store just received a new order " . $order_number;

                    @helper::push_notification($vendorinfo->token, $title, $body, "order", $order->id);

                    $checkplan = Transaction::where('vendor_id', $vendor)->where('transaction_type', null)->orderByDesc('id')->first();


                    if ($offer_type == "" || $offer_type == "promocode") {
                        loyaltyhelper::savepoints($vendor, $user_id, $order_number, 1, '');
                    } else {
                        if ($offer_type == "loyalty") {
                            loyaltyhelper::savepoints($vendor, $user_id, $order_number, 2, $couponcode);
                        }
                    }
                    if (!empty($checkplan)) {
                        if ($checkplan->appoinment_limit != -1) {
                            $checkplan->appoinment_limit -= 1;
                            $checkplan->save();
                        }
                    }

                    session()->forget('table_id');

                    return $order_number;
                } else {
                    return response()->json(['status' => 0, 'message' => trans('messages.wrong')], 200);
                }
            } else {
                return response()->json(['status' => 0, 'message' => trans('messages.cart_empty')], 200);
            }
        } catch (\Throwable $th) {
            dd($th);
            return $th;
        }
    }

    public static function get_plan($vendor_id)
    {
        $host = $_SERVER['HTTP_HOST'];
        if ($host  ==  env('WEBSITE_HOST')) {
            $vendorinfo = @helper::storeinfo($vendor_id);
            $vdata = $vendor_id;
        }
        // if the current host doesn't contain the website domain (meaning, custom domain)
        else {
            $vendorinfo = Settings::where('custom_domain', $host)->first();
            $vdata = $vendorinfo->vendor_id;
        }
        $vendorinfo = Transaction::where('vendor_id', $vdata)->orderByDesc('id')->first();;
        return $vendorinfo;
    }

    public static function push_notification($token, $title, $body, $type, $order_id)
    {
        if (Auth::user()->type == 1) {
            $firebase = helper::appdata('')->firebase;
        } else {
            $firebase = @helper::appdata(Auth::user()->id)->firebase;
        }
        $customdata = array(
            "type" => $type,
            "order_id" => $order_id,
        );

        $msg = array(
            'body' => $body,
            'title' => $title,
            'sound' => 1/*Default sound*/
        );
        $fields = array(
            'to'           => $token,
            'notification' => $msg,
            'data' => $customdata
        );
        $headers = array(
            'Authorization: key=' . $firebase,
            'Content-Type: application/json'
        );
        #Send Reponse To FireBase Server
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $firebaseresult = curl_exec($ch);
        curl_close($ch);

        return $firebaseresult;
    }

    public static function vendor_register($vendor_name, $vendor_email, $vendor_mobile, $vendor_password, $firebasetoken, $slug, $google_id, $facebook_id, $city_id, $area_id, $store_id)
    {
        try {
            if (!empty($slug)) {
                $check = User::where('slug', $slug)->first();
                if ($check != "") {
                    $last = User::select('id')->orderByDesc('id')->first();
                    $slug =   Str::slug($slug . " " . ($last->id + 1), '-');
                } else {
                    $slug = $slug;
                }
            } else {
                $check = User::where('slug', Str::slug($vendor_name, '-'))->first();
                if ($check != "") {
                    $last = User::select('id')->orderByDesc('id')->first();
                    $slug =   Str::slug($vendor_name . " " . ($last->id + 1), '-');
                } else {
                    $slug = Str::slug($vendor_name, '-');
                }
            }
            $rec = Settings::where('vendor_id', '1')->first();

            date_default_timezone_set($rec->timezone);
            $logintype = "normal";
            if ($google_id != "") {
                $logintype = "google";
            }

            if ($facebook_id != "") {
                $logintype = "facebook";
            }

            $user = new User;
            $user->name = $vendor_name;
            $user->email = $vendor_email;
            $user->password = $vendor_password;
            $user->google_id = $google_id;
            $user->facebook_id = $facebook_id;
            $user->mobile = $vendor_mobile;
            $user->slug = $slug;
            $user->login_type = $logintype;
            $user->type = 2;
            $user->token = $firebasetoken;
            $user->city_id = $city_id;
            $user->area_id = $area_id;
            $user->is_verified = 2;
            $user->is_available = 1;
            $user->store_id = $store_id;
            $user->save();

            $vendor_id = DB::getPdo()->lastInsertId();

            $days = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];

            foreach ($days as $day) {

                $timedata = new Timing;
                $timedata->vendor_id = $vendor_id;
                $timedata->day = $day;
                $timedata->open_time = '09:00 AM';
                $timedata->break_start = '01:00 PM';
                $timedata->break_end = '02:00 PM';
                $timedata->close_time = '09:00 PM';
                $timedata->is_always_close = '2';
                $timedata->save();
            }

            $status_name = CustomStatus::where('vendor_id', '1')->get();

            foreach ($status_name as $name) {
                $customstatus = new CustomStatus;
                $customstatus->vendor_id = $vendor_id;
                $customstatus->name = $name->name;
                $customstatus->type = $name->type;
                $customstatus->order_type = $name->order_type;
                $customstatus->is_available = $name->is_available;
                $customstatus->is_deleted = $name->is_deleted;
                $customstatus->save();
            }
            $paymentlist = Payment::select('unique_identifier', 'payment_name', 'currency', 'image', 'is_activate', 'payment_type')->where('vendor_id', '1')->get();
            foreach ($paymentlist as $payment) {
                $gateway = new Payment;
                $gateway->vendor_id = $vendor_id;
                $gateway->unique_identifier = $payment->unique_identifier;
                $gateway->payment_name = $payment->payment_name;
                $gateway->payment_type = $payment->payment_type;
                $gateway->currency = $payment->currency;
                $gateway->image = $payment->image;
                $gateway->public_key = '-';
                $gateway->secret_key = '-';
                $gateway->encryption_key = '-';
                $gateway->environment = '1';
                $gateway->is_available = '1';
                if ((int) $payment->payment_type === Payment::TYPE_STRIPE) {
                    $gateway->public_key = '';
                    $gateway->secret_key = '';
                    $gateway->is_available = '2';
                }
                $gateway->is_activate = $payment->is_activate;
                $gateway->save();
            }

            // These defaults are copied from the super-admin (vendor 1). That row can legitimately
            // be missing on a fresh install, so read through optional() instead of crashing the
            // whole registration.
            $whatsappdata = WhatsappMessage::where('vendor_id', 1)->first();
            if (!empty($whatsappdata)) {
                $whatsapp = new WhatsappMessage();
                $whatsapp->vendor_id = $vendor_id;
                $whatsapp->item_message = $whatsappdata->item_message;
                $whatsapp->order_whatsapp_message = $whatsappdata->order_whatsapp_message;
                $whatsapp->order_status_message = $whatsappdata->order_status_message;
                $whatsapp->whatsapp_number = $whatsappdata->whatsapp_number;
                $whatsapp->whatsapp_phone_number_id = $whatsappdata->whatsapp_phone_number_id;
                $whatsapp->whatsapp_access_token = $whatsappdata->whatsapp_access_token;
                $whatsapp->whatsapp_chat_on_off = $whatsappdata->whatsapp_chat_on_off;
                $whatsapp->whatsapp_mobile_view_on_off = $whatsappdata->whatsapp_mobile_view_on_off;
                $whatsapp->whatsapp_chat_position = $whatsappdata->whatsapp_chat_position;
                $whatsapp->order_created = $whatsappdata->order_created;
                $whatsapp->status_change = $whatsappdata->status_change;
                $whatsapp->message_type = $whatsappdata->message_type;
                $whatsapp->save();
            }

            $telegramdata = TelegramMessage::where('vendor_id', 1)->first();
            if (!empty($telegramdata)) {
                $telegram = new TelegramMessage();
                $telegram->vendor_id = $vendor_id;
                $telegram->item_message = $telegramdata->item_message;
                $telegram->telegram_message = $telegramdata->telegram_message;
                $telegram->order_created = $telegramdata->order_created;
                $telegram->telegram_access_token = $telegramdata->telegram_access_token;
                $telegram->telegram_chat_id = $telegramdata->telegram_chat_id;
                $telegram->save();
            }

            $data = new Settings();
            $data->vendor_id = $vendor_id;
            $data->currencies = 'usd';
            $data->default_currency = $rec->default_currency;

            // logo===================================================
            $data->logo = $rec->logo;
            // favicon=============
            $data->favicon = $rec->favicon;
            // og_image
            $data->og_image = $rec->favicon;
            $data->banner = "default-banner.png";
            $data->timezone = $rec->timezone;
            $data->address = "Your address";
            $data->contact = "-";
            $data->email = "youremail@gmail.com";
            $data->description = "Your description";
            $data->copyright = $rec->copyright;
            $data->website_title = "Your store name";
            $data->meta_title = "Your store name";
            $data->meta_description = "Description";
            $data->firebase = '-';
            $data->delivery_type = "1,2";
            $data->interval_time = 1;
            $data->interval_type = 2;
            $data->primary_color = '#181D31';
            $data->secondary_color = '#6096B4';
            $data->contact_email_message = $rec->contact_email_message;
            $data->new_order_invoice_email_message = $rec->new_order_invoice_email_message;
            $data->vendor_new_order_email_message = $rec->vendor_new_order_email_message;
            $data->order_status_email_message = $rec->order_status_email_message;
            $data->time_format = $rec->time_format;
            $data->date_format = $rec->date_format;
            $data->order_prefix = 'PITS';
            $data->order_number_start = 1001;
            $data->save();

            return $vendor_id;
        } catch (\Throwable $th) {
            // Returning the exception made it look like a valid id to callers, which silently
            // produced half-built vendors. Log it and return null instead.
            \Illuminate\Support\Facades\Log::error('vendor_register failed: ' . $th->getMessage(), [
                'file' => $th->getFile(), 'line' => $th->getLine(),
            ]);
            return null;
        }
    }

    public static function plandetail($plan_id)
    {
        $planinfo = PricingPlan::where('id', $plan_id)->first();
        return $planinfo;
    }

    public static function footer_features($vendor_id)
    {
        return Footerfeatures::select('id', 'icon', 'title', 'description')->where('vendor_id', $vendor_id)->get();
    }

    //Send email 

    public static function send_subscription_email($vendor_email, $vendor_name, $plan_name, $duration, $price, $payment_method, $transaction_id)
    {
        $admininfo = User::where('id', '1')->first();
        $vendorvar = ["{vendorname}", "{payment_type}", "{subscription_duration}", "{subscription_price}", "{plan_name}", "{adminname}", "{adminemail}"];
        $vendornewvar = [$vendor_name, $payment_method, $duration, $price, $plan_name, $admininfo->name, $admininfo->email];
        $vendormessage = str_replace($vendorvar, $vendornewvar, nl2br(helper::adminappdata()->subscription_success_email_message));

        $adminvar = ["{adminname}", "{vendorname}", "{vendoremail}", "{plan_name}", "{subscription_duration}", "{subscription_price}", "{payment_type}"];
        $adminnewvar = [$admininfo->name, $vendor_name, $vendor_email, $plan_name, $duration, $price, $payment_method];
        $adminmessage = str_replace($adminvar, $adminnewvar, nl2br(helper::adminappdata()->admin_subscription_success_email_message));

        $data = ['title' => "Subscription Purchase Confirmation", 'vendor_email' => $vendor_email, 'vendormessage' => $vendormessage];

        $adminemail = ['title' => "New Subscription Purchase Notification", 'admin_email' => $admininfo->email, 'adminmessage' => $adminmessage];

        try {
            Mail::send('email.subscription', $data, function ($message) use ($data) {
                $message->to($data['vendor_email'])->subject($data['title']);
            });

            Mail::send('email.adminsubscription', $adminemail, function ($message) use ($adminemail) {
                $message->to($adminemail['admin_email'])->subject($adminemail['title']);
            });
            return 1;
        } catch (\Throwable $th) {
            return 0;
        }
    }

    public static function bank_transfer_request($vendor_email, $vendor_name, $plan_name, $duration, $price, $payment_method, $transaction_id)
    {
        $admininfo = User::where('id', '1')->first();

        $vendorvar = ["{vendorname}", "{adminname}", "{adminemail}"];
        $vendornewvar = [$vendor_name, $admininfo->name, $admininfo->email];
        $vendormessage = str_replace($vendorvar, $vendornewvar, nl2br(helper::adminappdata()->banktransfer_request_email_message));

        $adminvar = ["{adminname}", "{vendorname}", "{vendoremail}", "{plan_name}", "{subscription_duration}", "{subscription_price}", "{payment_type}"];
        $adminnewvar = [$admininfo->name, $vendor_name, $vendor_email, $plan_name, $duration, $price, $payment_method];
        $adminmessage = str_replace($adminvar, $adminnewvar, nl2br(helper::adminappdata()->admin_subscription_request_email_message));

        $data = ['title' => "Bank transfer", 'vendor_email' => $vendor_email, 'vendormessage' => $vendormessage];
        $adminemail = ['title' => "Bank transfer", 'admin_email' => $admininfo->email, 'adminmessage' => $adminmessage,];
        try {
            Mail::send('email.banktransfervendor', $data, function ($message) use ($data) {
                $message->to($data['vendor_email'])->subject($data['title']);
            });

            Mail::send('email.banktransferadmin', $adminemail, function ($message) use ($adminemail) {
                $message->to($adminemail['admin_email'])->subject($adminemail['title']);
            });
            return 1;
        } catch (\Throwable $th) {
            return 0;
        }
    }

    public static function subscription_rejected($vendor_email, $vendor_name, $plan_name, $payment_method)
    {
        $admindata = User::select('name', 'email')->where('id', '1')->first();
        $var = ["{vendorname}", "{payment_type}", "{plan_name}", "{adminname}", "{adminemail}"];
        $newvar = [$vendor_name, $payment_method, $plan_name, $admindata->name, $admindata->email];
        $rejectmessage = str_replace($var, $newvar, nl2br(helper::adminappdata()->subscription_reject_email_message));

        $data = ['title' => "Bank transfer rejected", 'vendor_email' => $vendor_email, 'rejectmessage' => $rejectmessage];
        try {
            Mail::send('email.banktransferreject', $data, function ($message) use ($data) {
                $message->to($data['vendor_email'])->subject($data['title']);
            });
            return 1;
        } catch (\Throwable $th) {
            return 0;
        }
    }

    public static function vendor_contact_data($id, $vendor_name, $vendor_email, $full_name, $useremail, $usermobile, $usermessage)
    {
        $var = ["{vendorname}", "{username}", "{useremail}", "{usermobile}", "{usermessage}"];
        $newvar = [$vendor_name, $full_name, $useremail, $usermobile, $usermessage];
        $vendorcontactmessage = str_replace($var, $newvar, nl2br(helper::appdata($id)->contact_email_message));

        $data = ['title' => "Inquiry", 'vendor_email' => $vendor_email, 'vendorcontactmessage' => $vendorcontactmessage];
        try {
            Mail::send('email.vendorcontcatform', $data, function ($message) use ($data) {
                $message->to($data['vendor_email'])->subject($data['title']);
            });
            return 1;
        } catch (\Throwable $th) {
            return 0;
        }
    }

    public static function create_order_invoice($customer_email, $customer_name, $companyemail, $companyname, $vendorid, $order_number, $order_type, $delivery_date, $delivery_time, $grand_total, $trackurl)
    {
        $orderinvoicevar = ["{customername}", "{ordernumber}", "{date}", "{time}", "{grandtotal}", "{track_order_url}", "{vendorname}"];
        $orderinvoicenewvar = [$customer_name, $order_number, $delivery_date, $delivery_time, $grand_total, $trackurl, $companyname];
        $neworderinvoicemessage = str_replace($orderinvoicevar, $orderinvoicenewvar, nl2br(helper::appdata($vendorid)->new_order_invoice_email_message));

        $orderemailvar = ["{customername}", "{ordernumber}", "{date}", "{time}", "{grandtotal}", "{vendorname}"];
        $orderemailnewvar = [$customer_name, $order_number, $delivery_date, $delivery_time, $grand_total, $companyname];
        $vendorneworderemailmessage = str_replace($orderemailvar, $orderemailnewvar, nl2br(helper::appdata($vendorid)->vendor_new_order_email_message));

        $data = ['title' => "Order Invoice", 'customer_email' => $customer_email, 'company_email' => $companyemail, 'neworderinvoicemessage' => $neworderinvoicemessage, 'vendorneworderemailmessage' => $vendorneworderemailmessage];

        try {
            Mail::send('email.customerorderemail', $data, function ($message) use ($data) {
                $message->to($data['customer_email'])->subject($data['title']);
            });

            Mail::send('email.vendororderemail', $data, function ($companymessage) use ($data) {
                $companymessage->to($data['company_email'])->subject($data['title']);
            });
            return 1;
        } catch (\Throwable $th) {
            return 0;
        }
    }

    public static function order_status_email($email, $name, $title, $message_text, $vendor)
    {
        $var = ["{customername}", "{status_message}", "{vendorname}"];
        $newvar = [$name, $message_text, $vendor->name];
        $orderstatusmessage = str_replace($var, $newvar, nl2br(helper::appdata($vendor->id)->order_status_email_message));

        $data = ['email' => $email, 'title' => $title, 'orderstatusmessage' => $orderstatusmessage, 'logo' => @helper::image_path(@helper::appdata($vendor->id)->logo)];
        try {
            Mail::send('email.orderemail', $data, function ($message) use ($data) {
                $message->to($data['email'])->subject($data['title']);
            });
            return 1;
        } catch (\Throwable $th) {
            return 0;
        }
    }

    public static function send_pass($email, $name, $password, $id)
    {
        $var = ["{user}", "{password}"];
        $newvar = [$name, $password];
        $forpasswordmessage = str_replace($var, $newvar, nl2br(helper::adminappdata()->forget_password_email_message));

        $data = ['title' => "New Password", 'email' => $email, 'forpasswordmessage' => $forpasswordmessage, 'logo' => @helper::appdata($id)->logo];
        try {
            Mail::send('email.sendpassword', $data, function ($message) use ($data) {
                $message->to($data['email'])->subject($data['title']);
            });
            return 1;
        } catch (\Throwable $th) {
            return 0;
        }
    }

    public static function cancel_order($email, $name, $title, $message_text, $vendor)
    {
        $var = ["{customername}", "{status_message}", "{vendorname}"];
        $newvar = [$name, $message_text, $vendor->customer_name];
        $orderstatusmessage = str_replace($var, $newvar, nl2br(helper::appdata($vendor->vendor_id)->order_status_email_message));

        $data = ['email' => $email, 'title' => $title, 'orderstatusmessage' => $orderstatusmessage, 'logo' => Helper::image_path(@Helper::appdata($vendor->id)->logo)];
        try {
            Mail::send('email.orderemail', $data, function ($message) use ($data) {
                $message->to($data['email'])->subject($data['title']);
            });
            return 1;
        } catch (\Throwable $th) {
            return 0;
        }
    }
    public static function send_mail_delete_account($vendor)
    {
        $var = ["{vendorname}"];
        $newvar = [$vendor->name];
        $userdeletemessage = str_replace($var, $newvar, nl2br(helper::adminappdata()->delete_account_email_message));

        $data = ['title' => trans('labels.account_deleted'), 'userdeletemessage' => $userdeletemessage, 'email' => $vendor->email];
        try {
            Mail::send('email.accountdeleted', $data, function ($message) use ($data) {
                $message->to($data['email'])->subject($data['title']);
            });
            return 1;
        } catch (\Throwable $th) {
            return 0;
        }
    }
    // Email end

    public static function language($vendor_id)
    {
        if (session()->get('locale') == null) {
            $layout = Languages::select('name', 'layout', 'image', 'is_default', 'code')->where('code', helper::appdata($vendor_id)->default_language)->first();
            App::setLocale(@$layout->code);
            session()->put('locale', @$layout->code);
            session()->put('language', @$layout->name);
            session()->put('flag', @$layout->image);
            session()->put('direction', @$layout->layout);
        } else {
            $layout = Languages::select('name', 'layout', 'image', 'is_default', 'code')->where('code', session()->get('locale'))->first();
            App::setLocale(session()->get('locale'));
            session()->put('locale', @$layout->code);
            session()->put('language', @$layout->name);
            session()->put('flag', @$layout->image);
            session()->put('direction', @$layout->layout);
        }
    }


    // get language list vendor side.
    public static function available_language($vendor_id)
    {
        if ($vendor_id == "") {
            $listoflanguage = Languages::where('is_available', '1')->where('is_deleted', 2)->get();
        } else {
            $listoflanguage = Languages::where('is_available', '1')->where('is_deleted', 2)->get();
        }
        return $listoflanguage;
    }

    // get language list in atuh pages.
    public static function listoflanguage()
    {
        $listoflanguage = Languages::where('is_available', '1')->get();
        return $listoflanguage;
    }

    // dynamic email configration
    /**
     * V2 WhatsApp order flow — build a wa.me deep-link with a structured order
     * message (no Business API / no keys). Returns null if there's no order or
     * no WhatsApp number configured for the store.
     */
    public static function order_whatsapp_url($order_number, $vdata, $storeinfo = null)
    {
        $order = \App\Models\Order::where('order_number', $order_number)->where('vendor_id', $vdata)->first();
        if (empty($order)) {
            return null;
        }
        $settings = \App\Models\Settings::where('vendor_id', $vdata)->first();
        $number = preg_replace('/[^0-9]/', '', (string) (@$settings->whatsapp_number ?: @$settings->contact));
        if ($number === '') {
            return null;
        }

        $details = \App\Models\OrderDetails::where('order_id', $order->id)->get();
        $cur = function ($amt) use ($vdata) {
            return trim(strip_tags(self::currency_formate($amt, $vdata)));
        };

        $storeName = @$settings->website_title ?: (@$storeinfo->name ?: '');
        $payments = [1 => 'Cash on Delivery', 2 => 'RazorPay', 3 => 'Card (Stripe)', 6 => 'Bank Transfer', 16 => 'Wallet'];
        $fulfil = [1 => 'Delivery', 2 => 'Pickup', 3 => 'Dine-in', 4 => 'POS'];

        $L = [];
        $L[] = "*New Order* #" . $order->order_number;
        if ($storeName) $L[] = "🏪 " . $storeName;
        $L[] = "🗓️ " . date('d M Y, h:i A', strtotime($order->created_at));
        $L[] = "";
        $L[] = "*Customer:* " . $order->customer_name;
        if ($order->mobile) $L[] = "📱 " . $order->mobile;
        if ($order->customer_email) $L[] = "✉️ " . $order->customer_email;
        $L[] = "";
        $L[] = "*Order items:*";
        foreach ($details as $d) {
            $line = "• " . $d->item_name . " × " . $d->qty;
            $ex = [];
            if (!empty($d->variants_name)) $ex[] = $d->variants_name;
            if (!empty($d->extras_name)) $ex[] = $d->extras_name;
            if (!empty($ex)) $line .= " (" . implode(', ', $ex) . ")";
            $line .= " — " . $cur($d->price);
            $L[] = $line;
        }
        $L[] = "";
        $L[] = "Subtotal: " . $cur($order->sub_total);
        if ((float) $order->delivery_charge > 0) $L[] = "Delivery: " . $cur($order->delivery_charge);
        if ((float) $order->discount_amount > 0) $L[] = "Discount: -" . $cur($order->discount_amount);
        if ((float) $order->tax > 0) $L[] = (@$order->tax_name ?: 'Tax') . ": " . $cur($order->tax);
        $L[] = "*Total: " . $cur($order->grand_total) . "*";
        $L[] = "";
        $L[] = "💳 Payment: " . (@$payments[$order->payment_type] ?? 'N/A');
        $L[] = "🧾 Type: " . (@$fulfil[$order->order_type] ?? '');
        if ($order->order_type == 1) {
            $addr = trim(implode(' ', array_filter([$order->address, $order->building, $order->landmark, $order->delivery_area])));
            if ($addr !== '') $L[] = "📍 " . $addr;
        }
        if ($order->delivery_date) $L[] = "🕒 " . trim($order->delivery_date . ' ' . $order->delivery_time);
        if ($order->order_notes) $L[] = "📝 " . $order->order_notes;

        return "https://wa.me/" . $number . "?text=" . rawurlencode(implode("\n", $L));
    }

    /**
     * V2 Booking — build a wa.me deep-link with the booking details (keys-free).
     */
    public static function booking_whatsapp_url($booking, $vdata, $storeinfo = null)
    {
        if (empty($booking)) {
            return null;
        }
        $settings = \App\Models\Settings::where('vendor_id', $vdata)->first();
        $number = preg_replace('/[^0-9]/', '', (string) (@$settings->whatsapp_number ?: @$settings->contact));
        if ($number === '') {
            return null;
        }
        $storeName = @$settings->website_title ?: (@$storeinfo->name ?: '');

        $L = [];
        $L[] = "*New Booking* #" . $booking->booking_number;
        if ($storeName) $L[] = "🏪 " . $storeName;
        $L[] = "";
        $L[] = "*Service:* " . $booking->service_name;
        if ($booking->staff) $L[] = "👤 Staff: " . $booking->staff;
        $L[] = "📅 " . $booking->booking_date . ($booking->booking_time ? ' — ' . $booking->booking_time : '');
        if ($booking->amount > 0) $L[] = "💰 " . self::currency_formate($booking->amount, $vdata);
        if ($booking->payment_method) $L[] = "💳 " . $booking->payment_method;
        $L[] = "";
        $L[] = "*Customer:* " . $booking->customer_name;
        $L[] = "📱 " . $booking->mobile;
        if ($booking->email) $L[] = "✉️ " . $booking->email;
        if ($booking->notes) $L[] = "📝 " . $booking->notes;

        return "https://wa.me/" . $number . "?text=" . rawurlencode(implode("\n", $L));
    }

    public static function service_whatsapp_url($srequest, $vdata, $storeinfo = null)
    {
        if (empty($srequest)) {
            return null;
        }
        $settings = \App\Models\Settings::where('vendor_id', $vdata)->first();
        $number = preg_replace('/[^0-9]/', '', (string) (@$settings->whatsapp_number ?: @$settings->contact));
        if ($number === '') {
            return null;
        }
        $storeName = @$settings->website_title ?: (@$storeinfo->name ?: '');

        $L = [];
        $L[] = "*New Service Request* #" . $srequest->request_number;
        if ($storeName) $L[] = "🏪 " . $storeName;
        $L[] = "";
        $L[] = "*Service:* " . $srequest->service_name;
        if ($srequest->preferred_date) $L[] = "📅 " . $srequest->preferred_date . ($srequest->preferred_time ? ' — ' . $srequest->preferred_time : '');
        if ($srequest->address) $L[] = "📍 " . $srequest->address;
        $L[] = "";
        $L[] = "*Customer:* " . $srequest->customer_name;
        $L[] = "📱 " . $srequest->mobile;
        if ($srequest->email) $L[] = "✉️ " . $srequest->email;
        if ($srequest->notes) $L[] = "📝 " . $srequest->notes;

        return "https://wa.me/" . $number . "?text=" . rawurlencode(implode("\n", $L));
    }

    public static function emailconfigration($vendor_id)
    {
        if ($vendor_id == "" && $vendor_id == null) {
            $vendor_id = 1;
        } else {
            $vendor_id = $vendor_id;
        }
        $mailsettings = Settings::where('vendor_id', $vendor_id)->first();

        if ($mailsettings) {
            $emaildata = [
                'driver' => $mailsettings->mail_driver,
                'host' => $mailsettings->mail_host,
                'port' => $mailsettings->mail_port,
                'encryption' => $mailsettings->mail_encryption,
                'username' => $mailsettings->mail_username,
                'password' => $mailsettings->mail_password,
                'from'     => ['address' => $mailsettings->mail_fromaddress, 'name' => $mailsettings->mail_fromname]
            ];
        }
        return $emaildata;
    }

    public static function getcouponcodecount($offer_code, $vendor_id)
    {
        $count = Order::select('couponcode')->where('couponcode', $offer_code)->where('vendor_id', $vendor_id)->count();
        return $count;
    }

    public static function imageresize($file, $directory_name)
    {
        $reimage = 'item-' . uniqid() . "." . $file->getClientOriginalExtension();

        $new_width = 1000;

        // create image manager with desired driver      

        $manager = new ImageManager(new Driver());

        // read image from file system
        $image = $manager->read($file);


        // Get Height & Width
        list($width, $height) = getimagesize("$file");

        // Get Ratio
        $ratio = $width / $height;

        // Create new height & width
        $new_height = $new_width / $ratio;

        // resize image proportionally to 200px width
        $image->scale(width: $new_width, height: $new_height);

        $extension = File::extension($reimage);

        $exif = @exif_read_data("$file");

        $degrees = 0;
        if (isset($exif['Orientation'])) {
            switch ($exif['Orientation']) {
                case 8:
                    $degrees = 90;
                    break;
                case 3:
                    $degrees = 180;
                    break;
                case 6:
                    $degrees = -90;
                    break;
            }
        }

        // $image->rotate($degrees);
        $convert = $image;
        if (Str::endsWith($reimage, '.jpeg')) {
            $convert = $convert->toJpeg();
        } else if (Str::endsWith($reimage, '.jpg')) {
            $convert = $convert->toJpeg();
        } else if (Str::endsWith($reimage, '.webp')) {
            $convert = $convert->toWebp();
        } else if (Str::endsWith($reimage, '.gif')) {
            $convert = $convert->toGif();
        } else if (Str::endsWith($reimage, '.png')) {
            $convert = $convert->toPng();
        } else if (Str::endsWith($reimage, '.avif')) {
            $convert = $convert->toAvif();
        } else if (Str::endsWith($reimage, '.bmp')) {
            $convert = $convert->toBitmap();
        }

        $convertimg = str_replace($extension, 'webp', $reimage);

        $convert->save("$directory_name/$convertimg");

        return $convertimg;
    }

    public static function checklowqty($item_id, $vendor_id)
    {
        $item = Item::where('id', $item_id)->where('vendor_id', $vendor_id)->first();
        if ($item->has_variants == 1) {
            $qty = Variants::select('item_id', 'qty')->where('item_id', $item_id)->get();
            $array = [];

            foreach ($qty as $qty) {
                array_push($array, $qty->qty);
            }
            if (in_array(0, $array)) {
                return 2;
            }
            if (count(array_filter($array)) == 0) {
                return 3;
            }
            foreach ($array as $qty) {
                if ($qty != null && $qty != "") {
                    if ($qty <= $item->low_qty) {
                        return 1;
                    }
                }
            }
        } else {

            if ($item->qty == null && $item->qty == "") {
                return 3;
            }
            if ((string)$item->qty != null && (string)$item->qty != "") {
                if ((string)$item->qty == 0) {
                    return 2;
                }
                if ($item->qty <= $item->low_qty) {
                    return 1;
                }
            }
        }
    }
    public static function gettax($tax_id)
    {
        $taxArr = explode('|', $tax_id);
        $taxes = [];
        foreach ($taxArr as $tax) {
            $taxes[] = Tax::find($tax);
        }
        return $taxes;
    }

    public static function taxRate($taxRate, $price, $quantity, $tax_type)
    {
        if ($tax_type == 1) {
            return $taxRate * $quantity;
        }

        if ($tax_type == 2) {
            return ($taxRate / 100) * ($price * $quantity);
        }
    }
    // display dynamic paymant name
    public static function getpayment($payment_type, $vendor_id)
    {
        $payment = Payment::select('payment_name')->where('payment_type', $payment_type)->where('vendor_id', $vendor_id)->first();
        return $payment;
    }
    public static function getsociallinks($vendor_id)
    {
        $links = SocialLinks::where('vendor_id', $vendor_id)->get();
        return $links;
    }
    public static function imagesize()
    {
        $imagesize  = (int)1024 * (int)helper::adminappdata()->image_size;
        return $imagesize;
    }
    public static function imageext()
    {
        $imageext = 'mimes:jpeg,jpg,png,webp';
        return $imageext;
    }
    public static function customstauts($vendor_id, $order_type)
    {
        $status = CustomStatus::where('vendor_id', $vendor_id)->where('order_type', $order_type)->where('is_available', 1)->where('is_deleted', 2)->orderBy('reorder_id')->get();
        return $status;
    }
    public static function gettype($status, $type, $order_type, $vendor_id)
    {
        $status = CustomStatus::where('vendor_id', $vendor_id)->where('order_type', $order_type)->where('type', $type)->where('id', $status)->first();
        return $status;
    }
    public static function top_deals($vendor_id)
    {
        date_default_timezone_set(helper::appdata($vendor_id)->timezone);
        $current_date  = Carbon::now()->format('Y-m-d');
        $current_time  = Carbon::now()->format('H:i:s');
        $topdeal = TopDeals::where('vendor_id', $vendor_id)->first();
        $topdeals = null;
        if (SystemAddons::where('unique_identifier', 'top_deals')->first() != null && SystemAddons::where('unique_identifier', 'top_deals')->first()->activated == 1) {
            if (isset($topdeal) && $topdeal->top_deals_switch == 1) {
                $startDate = $topdeal['start_date'];
                $starttime = $topdeal['start_time'];
                $endDate = $topdeal['end_date'];
                $endtime = $topdeal['end_time'];
                // Checking validity of top deal offer
                if ($topdeal->deal_type == 1) {
                    if ($current_date > $startDate) {
                        if ($current_date < $endDate) {
                            $topdeals = TopDeals::where('vendor_id', $vendor_id)->first();
                        } elseif ($current_date == $endDate) {
                            if ($current_time < $endtime) {
                                $topdeals = TopDeals::where('vendor_id', $vendor_id)->first();
                            }
                        }
                    } elseif ($current_date == $startDate) {
                        if ($current_date < $endDate && $current_time >= $starttime) {
                            $topdeals = TopDeals::where('vendor_id', $vendor_id)->first();
                        } elseif ($current_date == $endDate) {
                            if ($current_time >= $starttime && $current_time <= $endtime) {
                                $topdeals = TopDeals::where('vendor_id', $vendor_id)->first();
                            }
                        }
                    }
                } else if ($topdeal->deal_type == 2) {
                    if ($current_time >= $starttime && $current_time <= $endtime) {
                        $topdeals = TopDeals::where('vendor_id', $vendor_id)->first();
                    }
                }
            }
        }
        return $topdeals;
    }
    public static function getagedetails($vendor_id)
    {
        $agedetails = AgeVerification::where('vendor_id', $vendor_id)->first();
        return $agedetails;
    }
    public static function getpixelid($vendor_id)
    {
        $pixcel = Pixcel::where('vendor_id', $vendor_id)->first();
        return $pixcel;
    }
    public static function role($id)
    {
        $role = RoleManager::select('role')->where('id', $id)->first();
        return $role;
    }
    public static function checkthemeaddons($addons)
    {
        if (session()->get('demo') == "free-addon") {
            $check = SystemAddons::where('unique_identifier', 'LIKE', '%' . $addons . '%')->where('activated', 1)->where('type', 1)->get();
        } elseif (session()->get('demo') == "free-with-extended-addon") {
            $check = SystemAddons::where('unique_identifier', 'LIKE', '%' . $addons . '%')->where('activated', 1)->whereIn('type', ['1', '2'])->get();
        } elseif (session()->get('demo') == "all-addon") {
            $check = SystemAddons::where('unique_identifier', 'LIKE', '%' . $addons . '%')->where('activated', 1)->whereIn('type', ['1', '2', '3'])->get();
        } else {
            $check = SystemAddons::where('unique_identifier', 'LIKE', '%' . $addons . '%')->where('activated', 1)->get();
        }
        return $check;
    }
    public static function check_menu($role_id, $slug)
    {
        if (Auth::user()->type == 4) {
            $vendor_id = Auth::user()->vendor_id;
        } else {
            $vendor_id = Auth::user()->id;
        }
        if ($role_id == "" || $role_id == null || $role_id == 0) {
            return 1;
        } else {
            $module = RoleManager::where('id', $role_id)->where('vendor_id', $vendor_id)->first();
            $module = explode('|', $module->module);
            if (in_array($slug, $module)) {
                return 1;
            } else {

                return 0;
            }
        }
    }
    public static function check_access($module, $role_id, $vendor_id, $action)
    {

        $module = RoleAccess::where('module_name', $module)->where('role_id', $role_id)->where('vendor_id', $vendor_id)->first();
        if (!empty($module) && $module != null) {
            if ($action == 'add' && $module->add == 1) {
                return 1;
            } elseif ($action == 'edit' && $module->edit == 1) {
                return 1;
            } elseif ($action == 'delete' && $module->delete == 1) {
                return 1;
            } elseif ($action == 'manage' && $module->manage == 1) {
                return 1;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }
    public static function getplantransaction($vendor_id)
    {
        $plan = Transaction::where('vendor_id', $vendor_id)->orderbyDesc('id')->first();
        return $plan;
    }
    public static function getallpayment($vendor_id)
    {
        $payment = Payment::where('vendor_id', $vendor_id)->where('is_available', 1)->where('is_activate', 1)->get();
        return $payment;
    }
    public static function storecategory()
    {
        $storecategory = StoreCategory::where('is_available', 1)->where('is_deleted', 2)->get();
        return $storecategory;
    }

    public static function app_settings($vendor_id)
    {
        $app = AppSettings::where('vendor_id',  $vendor_id)->first();
        return $app;
    }

    public static function checkcustomdomain($vendor_id)
    {
        $customdomain = CustomDomain::select('current_domain')->where('vendor_id', $vendor_id)->where('status', 2)->first();
        return @$customdomain->current_domain;
    }

    // get currency list vendor side.
    public static function available_currency()
    {
        $listofcurrency = CurrencySettings::where('is_available', '1')->get();
        return $listofcurrency;
    }

    /**
     * Full country list (name, international dial code, ISO code, flag emoji).
     * One source of truth for the register page + AI setup phone-code pickers.
     * The flag emoji is derived from the ISO code, so there's nothing to keep in sync.
     */
    public static function countries(): array
    {
        static $list = null;
        if ($list !== null) return $list;
        $raw = [
            ['Afghanistan', '+93', 'AF'], ['Aland Islands', '+358', 'AX'], ['Albania', '+355', 'AL'], ['Algeria', '+213', 'DZ'],
            ['American Samoa', '+1684', 'AS'], ['Andorra', '+376', 'AD'], ['Angola', '+244', 'AO'], ['Anguilla', '+1264', 'AI'],
            ['Antarctica', '+672', 'AQ'], ['Antigua and Barbuda', '+1268', 'AG'], ['Argentina', '+54', 'AR'], ['Armenia', '+374', 'AM'],
            ['Aruba', '+297', 'AW'], ['Australia', '+61', 'AU'], ['Austria', '+43', 'AT'], ['Azerbaijan', '+994', 'AZ'],
            ['Bahamas', '+1242', 'BS'], ['Bahrain', '+973', 'BH'], ['Bangladesh', '+880', 'BD'], ['Barbados', '+1246', 'BB'],
            ['Belarus', '+375', 'BY'], ['Belgium', '+32', 'BE'], ['Belize', '+501', 'BZ'], ['Benin', '+229', 'BJ'],
            ['Bermuda', '+1441', 'BM'], ['Bhutan', '+975', 'BT'], ['Bolivia', '+591', 'BO'], ['Bosnia and Herzegovina', '+387', 'BA'],
            ['Botswana', '+267', 'BW'], ['Brazil', '+55', 'BR'], ['British Indian Ocean Territory', '+246', 'IO'], ['Brunei Darussalam', '+673', 'BN'],
            ['Bulgaria', '+359', 'BG'], ['Burkina Faso', '+226', 'BF'], ['Burundi', '+257', 'BI'], ['Cambodia', '+855', 'KH'],
            ['Cameroon', '+237', 'CM'], ['Canada', '+1', 'CA'], ['Cape Verde', '+238', 'CV'], ['Cayman Islands', '+345', 'KY'],
            ['Central African Republic', '+236', 'CF'], ['Chad', '+235', 'TD'], ['Chile', '+56', 'CL'], ['China', '+86', 'CN'],
            ['Christmas Island', '+61', 'CX'], ['Cocos (Keeling) Islands', '+61', 'CC'], ['Colombia', '+57', 'CO'], ['Comoros', '+269', 'KM'],
            ['Congo', '+242', 'CG'], ['Congo, Democratic Republic', '+243', 'CD'], ['Cook Islands', '+682', 'CK'], ['Costa Rica', '+506', 'CR'],
            ["Cote d'Ivoire", '+225', 'CI'], ['Croatia', '+385', 'HR'], ['Cuba', '+53', 'CU'], ['Cyprus', '+357', 'CY'],
            ['Czech Republic', '+420', 'CZ'], ['Denmark', '+45', 'DK'], ['Djibouti', '+253', 'DJ'], ['Dominica', '+1767', 'DM'],
            ['Dominican Republic', '+1849', 'DO'], ['Ecuador', '+593', 'EC'], ['Egypt', '+20', 'EG'], ['El Salvador', '+503', 'SV'],
            ['Equatorial Guinea', '+240', 'GQ'], ['Eritrea', '+291', 'ER'], ['Estonia', '+372', 'EE'], ['Ethiopia', '+251', 'ET'],
            ['Falkland Islands', '+500', 'FK'], ['Faroe Islands', '+298', 'FO'], ['Fiji', '+679', 'FJ'], ['Finland', '+358', 'FI'],
            ['France', '+33', 'FR'], ['French Guiana', '+594', 'GF'], ['French Polynesia', '+689', 'PF'], ['Gabon', '+241', 'GA'],
            ['Gambia', '+220', 'GM'], ['Georgia', '+995', 'GE'], ['Germany', '+49', 'DE'], ['Ghana', '+233', 'GH'],
            ['Gibraltar', '+350', 'GI'], ['Greece', '+30', 'GR'], ['Greenland', '+299', 'GL'], ['Grenada', '+1473', 'GD'],
            ['Guadeloupe', '+590', 'GP'], ['Guam', '+1671', 'GU'], ['Guatemala', '+502', 'GT'], ['Guernsey', '+44', 'GG'],
            ['Guinea', '+224', 'GN'], ['Guinea-Bissau', '+245', 'GW'], ['Guyana', '+595', 'GY'], ['Haiti', '+509', 'HT'],
            ['Holy See (Vatican City)', '+379', 'VA'], ['Honduras', '+504', 'HN'], ['Hong Kong', '+852', 'HK'], ['Hungary', '+36', 'HU'],
            ['Iceland', '+354', 'IS'], ['India', '+91', 'IN'], ['Indonesia', '+62', 'ID'], ['Iran', '+98', 'IR'],
            ['Iraq', '+964', 'IQ'], ['Ireland', '+353', 'IE'], ['Isle of Man', '+44', 'IM'], ['Israel', '+972', 'IL'],
            ['Italy', '+39', 'IT'], ['Jamaica', '+1876', 'JM'], ['Japan', '+81', 'JP'], ['Jersey', '+44', 'JE'],
            ['Jordan', '+962', 'JO'], ['Kazakhstan', '+77', 'KZ'], ['Kenya', '+254', 'KE'], ['Kiribati', '+686', 'KI'],
            ['Korea, North', '+850', 'KP'], ['Korea, South', '+82', 'KR'], ['Kuwait', '+965', 'KW'], ['Kyrgyzstan', '+996', 'KG'],
            ['Laos', '+856', 'LA'], ['Latvia', '+371', 'LV'], ['Lebanon', '+961', 'LB'], ['Lesotho', '+266', 'LS'],
            ['Liberia', '+231', 'LR'], ['Libya', '+218', 'LY'], ['Liechtenstein', '+423', 'LI'], ['Lithuania', '+370', 'LT'],
            ['Luxembourg', '+352', 'LU'], ['Macao', '+853', 'MO'], ['Macedonia', '+389', 'MK'], ['Madagascar', '+261', 'MG'],
            ['Malawi', '+265', 'MW'], ['Malaysia', '+60', 'MY'], ['Maldives', '+960', 'MV'], ['Mali', '+223', 'ML'],
            ['Malta', '+356', 'MT'], ['Marshall Islands', '+692', 'MH'], ['Martinique', '+596', 'MQ'], ['Mauritania', '+222', 'MR'],
            ['Mauritius', '+230', 'MU'], ['Mayotte', '+262', 'YT'], ['Mexico', '+52', 'MX'], ['Micronesia', '+691', 'FM'],
            ['Moldova', '+373', 'MD'], ['Monaco', '+377', 'MC'], ['Mongolia', '+976', 'MN'], ['Montenegro', '+382', 'ME'],
            ['Montserrat', '+1664', 'MS'], ['Morocco', '+212', 'MA'], ['Mozambique', '+258', 'MZ'], ['Myanmar', '+95', 'MM'],
            ['Namibia', '+264', 'NA'], ['Nauru', '+674', 'NR'], ['Nepal', '+977', 'NP'], ['Netherlands', '+31', 'NL'],
            ['Netherlands Antilles', '+599', 'AN'], ['New Caledonia', '+687', 'NC'], ['New Zealand', '+64', 'NZ'], ['Nicaragua', '+505', 'NI'],
            ['Niger', '+227', 'NE'], ['Nigeria', '+234', 'NG'], ['Niue', '+683', 'NU'], ['Norfolk Island', '+672', 'NF'],
            ['Northern Mariana Islands', '+1670', 'MP'], ['Norway', '+47', 'NO'], ['Oman', '+968', 'OM'], ['Pakistan', '+92', 'PK'],
            ['Palau', '+680', 'PW'], ['Palestinian Territory', '+970', 'PS'], ['Panama', '+507', 'PA'], ['Papua New Guinea', '+675', 'PG'],
            ['Paraguay', '+595', 'PY'], ['Peru', '+51', 'PE'], ['Philippines', '+63', 'PH'], ['Pitcairn', '+872', 'PN'],
            ['Poland', '+48', 'PL'], ['Portugal', '+351', 'PT'], ['Puerto Rico', '+1939', 'PR'], ['Qatar', '+974', 'QA'],
            ['Romania', '+40', 'RO'], ['Russia', '+7', 'RU'], ['Rwanda', '+250', 'RW'], ['Reunion', '+262', 'RE'],
            ['Saint Barthelemy', '+590', 'BL'], ['Saint Helena', '+290', 'SH'], ['Saint Kitts and Nevis', '+1869', 'KN'], ['Saint Lucia', '+1758', 'LC'],
            ['Saint Martin', '+590', 'MF'], ['Saint Pierre and Miquelon', '+508', 'PM'], ['Saint Vincent and the Grenadines', '+1784', 'VC'], ['Samoa', '+685', 'WS'],
            ['San Marino', '+378', 'SM'], ['Sao Tome and Principe', '+239', 'ST'], ['Saudi Arabia', '+966', 'SA'], ['Senegal', '+221', 'SN'],
            ['Serbia', '+381', 'RS'], ['Seychelles', '+248', 'SC'], ['Sierra Leone', '+232', 'SL'], ['Singapore', '+65', 'SG'],
            ['Slovakia', '+421', 'SK'], ['Slovenia', '+386', 'SI'], ['Solomon Islands', '+677', 'SB'], ['Somalia', '+252', 'SO'],
            ['South Africa', '+27', 'ZA'], ['South Sudan', '+211', 'SS'], ['South Georgia', '+500', 'GS'], ['Spain', '+34', 'ES'],
            ['Sri Lanka', '+94', 'LK'], ['Sudan', '+249', 'SD'], ['Suriname', '+597', 'SR'], ['Svalbard and Jan Mayen', '+47', 'SJ'],
            ['Swaziland', '+268', 'SZ'], ['Sweden', '+46', 'SE'], ['Switzerland', '+41', 'CH'], ['Syria', '+963', 'SY'],
            ['Taiwan', '+886', 'TW'], ['Tajikistan', '+992', 'TJ'], ['Tanzania', '+255', 'TZ'], ['Thailand', '+66', 'TH'],
            ['Timor-Leste', '+670', 'TL'], ['Togo', '+228', 'TG'], ['Tokelau', '+690', 'TK'], ['Tonga', '+676', 'TO'],
            ['Trinidad and Tobago', '+1868', 'TT'], ['Tunisia', '+216', 'TN'], ['Turkey', '+90', 'TR'], ['Turkmenistan', '+993', 'TM'],
            ['Turks and Caicos Islands', '+1649', 'TC'], ['Tuvalu', '+688', 'TV'], ['Uganda', '+256', 'UG'], ['Ukraine', '+380', 'UA'],
            ['United Arab Emirates', '+971', 'AE'], ['United Kingdom', '+44', 'GB'], ['United States', '+1', 'US'], ['Uruguay', '+598', 'UY'],
            ['Uzbekistan', '+998', 'UZ'], ['Vanuatu', '+678', 'VU'], ['Venezuela', '+58', 'VE'], ['Vietnam', '+84', 'VN'],
            ['Virgin Islands, British', '+1284', 'VG'], ['Virgin Islands, U.S.', '+1340', 'VI'], ['Wallis and Futuna', '+681', 'WF'], ['Yemen', '+967', 'YE'],
            ['Zambia', '+260', 'ZM'], ['Zimbabwe', '+263', 'ZW'],
        ];
        $list = [];
        foreach ($raw as $r) {
            $iso = $r[2];
            $flag = '';
            if (strlen($iso) === 2 && ctype_alpha($iso)) {
                $flag = mb_chr(0x1F1E6 + ord($iso[0]) - 65, 'UTF-8') . mb_chr(0x1F1E6 + ord($iso[1]) - 65, 'UTF-8');
            }
            $list[] = ['name' => $r[0], 'dial' => str_replace(' ', '', $r[1]), 'iso' => $iso, 'flag' => $flag];
        }
        return $list;
    }

    /**
     * Safely store an uploaded file under storage/app/public/<subdir>/.
     * $subdir is relative to storage/app/public (e.g. 'admin-assets/images/category' or 'item').
     * Creates the folder if it's missing (a common cause of 500s on FTP-deployed servers) and
     * throws a clear, catchable message on permission problems instead of a raw 500.
     * Returns the generated filename.
     */
    public static function store_upload($file, string $subdir, string $prefix = 'img')
    {
        $dir = storage_path('app/public/' . trim($subdir, '/') . '/');
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
        if (!is_dir($dir) || !is_writable($dir)) {
            throw new \RuntimeException('Upload folder is not writable: ' . $dir . ' — run "php artisan storage:link" and set storage permissions (chmod -R 775 storage).');
        }
        $name = $prefix . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move($dir, $name);
        return $name;
    }
    // get language list in athu pages.
    public static function currencyinfo($vendor_id)
    {
        if (Cookie::get('code') == null) {
            $currency = CurrencySettings::where('code', helper::appdata($vendor_id)->default_currency)->first();
            session()->put('currency', $currency->currency);
        } else {

            $currency = CurrencySettings::where('code', Cookie::get('code'))->first();
            if (empty($currency)) {
                $currency = CurrencySettings::where('code', helper::appdata($vendor_id)->default_currency)->first();
            }
            session()->put('currency', $currency->currency);
        }
        return $currency;
    }
}
