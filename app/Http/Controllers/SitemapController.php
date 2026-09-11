<?php

namespace App\Http\Controllers;

use App\Helpers\helper;
use App\Helpers\Systems;
use App\Http\Controllers\landing\HomeController as LandingHomeController;
use App\Models\Blog;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;

/**
 * /sitemap.xml for Order Click: the public platform pages, every blog post and every store
 * that is currently visible in the Marketplace. Listed in the domain's robots.txt.
 */
class SitemapController extends Controller
{
    /** Public platform pages and how often they change. */
    private const PAGES = [
        ''                => ['daily',   '1.0'],
        'marketplace'     => ['daily',   '0.9'],
        'register/1'      => ['monthly', '0.8'],
        'blog_list'       => ['weekly',  '0.7'],
        'about_us'        => ['monthly', '0.5'],
        'faqs'            => ['monthly', '0.5'],
        'privacy_policy'  => ['yearly',  '0.3'],
        'terms_condition' => ['yearly',  '0.3'],
        'refund_policy'   => ['yearly',  '0.3'],
    ];

    public function index()
    {
        // Rebuilt at most hourly: crawlers hit this often and a new store can wait an hour.
        $xml = Cache::remember('orderclick.sitemap.xml', 3600, fn() => $this->build());

        return response($xml, 200)->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    private function build(): string
    {
        $urls = [];

        foreach (self::PAGES as $path => [$freq, $priority]) {
            $urls[] = $this->entry(URL::to($path), null, $freq, $priority);
        }

        foreach (Blog::where('vendor_id', 1)->orderBy('reorder_id')->get(['id', 'updated_at']) as $blog) {
            $urls[] = $this->entry(URL::to('blog_details-' . $blog->id), $blog->updated_at, 'monthly', '0.6');
        }

        // Exactly the stores the Marketplace shows. No trailing slash: the server 301-redirects
        // "slug/" to "slug", and a sitemap should list the final URL.
        $stores = LandingHomeController::marketplaceVendors()
            ->whereNotNull('users.slug')->where('users.slug', '!=', '')
            ->get();
        foreach ($stores as $store) {
            if ($this->storeOpens($store)) {
                $urls[] = $this->entry(URL::to($store->slug), $store->updated_at, 'weekly', '0.8');
            }
        }

        return '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
            . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n"
            . implode("\n", $urls) . "\n"
            . '</urlset>' . "\n";
    }

    /**
     * The same gates FrontMiddleware applies before showing a storefront. A store that fails one
     * answers with a "not available" page (HTTP 200), which search engines treat as a soft 404,
     * so it must not be submitted.
     */
    private function storeOpens(User $store): bool
    {
        if ((int) (helper::otherappdata($store->id)->maintenance_on_off ?? 2) === 1) {
            return false;
        }
        if (!Systems::isLive($store) && Systems::hasPaid($store)) {
            return false; // paid, but the website has not been activated yet
        }
        $plan = json_decode(json_encode(helper::checkplan($store->id, '3')));

        return (int) (@$plan->original->status ?? 1) !== 2; // 2 = no plan / plan expired
    }

    private function entry(string $loc, $lastmod, string $freq, string $priority): string
    {
        return "  <url>\n"
            . '    <loc>' . htmlspecialchars($loc, ENT_XML1 | ENT_QUOTES, 'UTF-8') . "</loc>\n"
            . ($lastmod ? '    <lastmod>' . $lastmod->toAtomString() . "</lastmod>\n" : '')
            . '    <changefreq>' . $freq . "</changefreq>\n"
            . '    <priority>' . $priority . "</priority>\n"
            . '  </url>';
    }
}
