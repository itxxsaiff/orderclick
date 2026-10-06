<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Pagination\Paginator;
use App\Helpers\helper;
use App\Models\Settings;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();

        // Restaurant / Café custom theme (template-3 & template-4): compute store branding
        // once and cascade it to the layout, partials and section content. Needed because
        // Blade captures @section content before the layout's own @php runs.
        $tplPages = [];
        foreach (['3', '4', '5', '6', '7', '8', '9', '10', '20'] as $tpl) {
            foreach (['index', 'booking_index', 'category', 'cart', 'checkout', 'contact', 'about', 'faq', 'terms', 'privacy', 'refund', 'productdetail', 'success', 'trackorder', 'listing', 'bookingsuccess'] as $page) {
                $tplPages[] = "front.template-$tpl.$page";
            }
        }
        View::composer($tplPages, function ($view) {
            $data = $view->getData();
            $storeinfo = $data['storeinfo'] ?? null;
            if (!$storeinfo) {
                return;
            }
            // Storefront $storeinfo is a User (id == vendor id); on custom domains it is a
            // Settings row (use its vendor_id). Normalise to the vendor id.
            $vid = $storeinfo->id ?? ($storeinfo->vendor_id ?? null);
            $app = Settings::where('vendor_id', $vid)->first();
            $phone = optional($app)->whatsapp_number ?: optional($app)->contact;
            // Never surface the platform's default website title as the store name — fall back
            // to the store's own name so the header/footer/tab show the real brand (or just the logo).
            $rawTitle = trim((string) optional($app)->website_title);
            $isPlatformTitle = $rawTitle === '' || stripos($rawTitle, 'Multi-Business Ordering Platform') !== false;
            $storeName = $isPlatformTitle ? trim((string) ($storeinfo->name ?? '')) : $rawTitle;
            // active nav state keyed by page suffix (works for any template-N)
            $pageKey = \Illuminate\Support\Str::afterLast($view->getName(), '.');
            $activeMap = [
                'category' => 'menu', 'productdetail' => 'menu', 'listing' => 'book', 'bookingsuccess' => 'book',
                'cart' => 'cart', 'checkout' => 'cart',
                'contact' => 'contact', 'about' => 'about', 'faq' => 'faqs',
            ];
            // Each custom theme loads its own asset folder.
            $tplFolder = 'restaurant';
            if (\Illuminate\Support\Str::contains($view->getName(), 'template-6')) {
                $tplFolder = 'retail';
            } elseif (\Illuminate\Support\Str::contains($view->getName(), 'template-5')) {
                $tplFolder = 'grocery';
            } elseif (\Illuminate\Support\Str::contains($view->getName(), 'template-7')) {
                $tplFolder = 'pharmacy';
            } elseif (\Illuminate\Support\Str::contains($view->getName(), 'template-8')) {
                $tplFolder = 'booking';
            } elseif (\Illuminate\Support\Str::contains($view->getName(), 'template-10')) {
                $tplFolder = 'salon';
            } elseif (\Illuminate\Support\Str::contains($view->getName(), 'template-9')) {
                $tplFolder = 'clinic';
            }
            // AI design engine (template-20): the store's design plan, its flow and navigation.
            if (\Illuminate\Support\Str::contains($view->getName(), 'template-20')) {
                $design = \App\Services\StoreDesign::for($vid, \App\Services\StoreDesign::previewing($vid));
                $flow = $design['flow'];
                $slug = $storeinfo->slug ?? '';
                $shopLabel = in_array(optional($app)->business_type, ['food', 'cafe'], true) ? trans('labels.eng_nav_menu') : trans('labels.eng_nav_shop');
                $main = [
                    'orders'  => ['key' => 'menu', 'label' => $shopLabel, 'url' => url($slug . '/categories')],
                    'booking' => ['key' => 'book', 'label' => trans('labels.eng_nav_book'), 'url' => url($slug . '/booking')],
                    'service' => ['key' => 'service', 'label' => trans('labels.eng_nav_services'), 'url' => url($slug . '/service')],
                ][$flow];
                $view->with([
                    'tDesign' => $design,
                    'tFlow'   => $flow,
                    'tNav'    => [
                        ['key' => 'home', 'label' => __('Home'), 'url' => url($slug)],
                        $main,
                        ['key' => 'about', 'label' => __('About us'), 'url' => url($slug . '/aboutus')],
                        ['key' => 'contact', 'label' => __('Contact'), 'url' => url($slug . '/contact')],
                        ['key' => 'faqs', 'label' => __('FAQs'), 'url' => url($slug . '/faqshow')],
                    ],
                    'tCta'    => [
                        'orders'  => ['label' => trans('labels.eng_cta_order'), 'url' => url($slug . '/categories')],
                        'booking' => ['label' => trans('labels.eng_cta_book'), 'url' => url($slug . '/booking')],
                        'service' => ['label' => trans('labels.eng_cta_request'), 'url' => url($slug . '/service')],
                    ][$flow],
                ]);
                $tplFolder = 'restaurant';
            }
            $view->with([
                'tApp'    => $app,
                'tName'   => $storeName,
                'tLogo'   => optional($app)->logo,
                'tDesc'   => optional($app)->description,
                'tPhone'  => $phone,
                'tWa'     => preg_replace('/[^0-9]/', '', (string) $phone),
                'tEmail'  => optional($app)->email,
                'tAddr'   => optional($app)->address,
                'tSlug'   => $storeinfo->slug ?? '',
                'tBase'   => url($storeinfo->slug ?? ''),
                'tCount'  => helper::getcartcount($vid, optional(Auth::user())->id),
                'tCss'    => env('ASSETSPATHURL') . 'web-assets/templates/' . $tplFolder . '/',
                'tCats'   => \App\Models\Category::where('vendor_id', $vid)->where('is_available', '1')->where('is_deleted', '2')->orderBy('reorder_id')->get(['id', 'name', 'image']),
                'tActive' => $activeMap[$pageKey] ?? 'home',
            ]);
        });
    }
}
