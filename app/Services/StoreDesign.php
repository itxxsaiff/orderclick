<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Item;
use App\Models\Settings;
use App\Models\User;
use Illuminate\Support\Str;

/**
 * AI store design.
 *
 * A store's look is a DESIGN PLAN (JSON), never markup: palette, fonts, shape, layout variants,
 * section order and the marketing copy. The AI proposes a plan; normalize() keeps only known
 * values, so whatever the model returns can never inject HTML or break the cart, checkout or
 * booking flows. The engine template (front/template-20) renders every store from its plan.
 */
class StoreDesign
{
    /** Google Fonts the engine may load. */
    public const FONTS = [
        'Inter', 'Plus Jakarta Sans', 'Poppins', 'Outfit', 'DM Sans', 'Manrope', 'Sora', 'Rubik',
        'Montserrat', 'Nunito', 'Raleway', 'Space Grotesk', 'Work Sans', 'Lexend',
        'Playfair Display', 'Fraunces', 'Cormorant Garamond', 'Lora', 'DM Serif Display', 'Libre Baskerville',
    ];

    /** Every enum in the plan and its allowed values; the first value is the fallback. */
    public const OPTIONS = [
        'mode'    => ['light', 'dark'],
        'corners' => ['soft', 'sharp', 'round'],
        'buttons' => ['rounded', 'pill', 'square'],
        'shadow'  => ['soft', 'flat', 'elevated'],
        'header'  => ['classic', 'centered', 'minimal', 'bold'],
        'hero'    => ['split', 'centered', 'fullbleed', 'minimal'],
        'card'    => ['classic', 'minimal', 'overlay', 'bordered'],
        'ratio'   => ['square', 'portrait', 'landscape'],
        'cats'    => ['tiles', 'chips', 'circles'],
        'footer'  => ['dark', 'light', 'brand'],
    ];

    /** Home-page sections per flow, in their default order. 'hero' is always first. */
    public const SECTIONS = [
        'orders'  => ['trust', 'categories', 'products', 'offers', 'about', 'steps', 'reviews', 'cta'],
        'service' => ['trust', 'products', 'about', 'steps', 'reviews', 'cta'],
        'booking' => ['trust', 'services', 'team', 'steps', 'about', 'reviews', 'cta'],
    ];

    /** Icons the "trust" items may use (drawn by the engine). */
    public const ICONS = ['bolt', 'lock', 'whatsapp', 'star', 'leaf', 'truck', 'clock', 'shield', 'heart', 'gift', 'tag', 'phone', 'check', 'sparkle', 'map', 'calendar'];

    /** Text the AI writes, per language, with the maximum length of each field. */
    private const COPY_LIMITS = [
        'hero_eyebrow' => 60, 'hero_title' => 80, 'hero_text' => 260, 'hero_cta' => 32,
        'categories_title' => 70, 'categories_text' => 180,
        'products_title' => 70, 'products_text' => 180,
        'services_title' => 70, 'services_text' => 180,
        'team_title' => 70, 'offers_title' => 70,
        'about_title' => 70, 'about_text' => 600,
        'steps_title' => 70, 'reviews_title' => 70,
        'cta_title' => 80, 'cta_text' => 220, 'cta_button' => 32,
        'footer_text' => 220,
    ];

    /** Visual starting point per business type, used until the merchant runs the AI designer. */
    private const PRESETS = [
        'food'     => ['brand' => '#12A150', 'accent' => '#FF6B35', 'bg' => '#FBF6EE', 'heading' => 'Plus Jakarta Sans', 'body' => 'Plus Jakarta Sans'],
        'cafe'     => ['brand' => '#8B5E3C', 'accent' => '#D9A441', 'bg' => '#FAF5EF', 'heading' => 'Fraunces', 'body' => 'Inter'],
        'grocery'  => ['brand' => '#0E9F6E', 'accent' => '#F59E0B', 'bg' => '#F6FAF8', 'heading' => 'Inter', 'body' => 'Inter'],
        'retail'   => ['brand' => '#B4532A', 'accent' => '#1F2937', 'bg' => '#FAF7F2', 'heading' => 'Playfair Display', 'body' => 'Inter'],
        'pharmacy' => ['brand' => '#2563EB', 'accent' => '#8B5CF6', 'bg' => '#F5F8FF', 'heading' => 'Manrope', 'body' => 'Manrope'],
        'booking'  => ['brand' => '#6D28D9', 'accent' => '#D4A017', 'bg' => '#FAF8FC', 'heading' => 'Fraunces', 'body' => 'Inter'],
        'clinic'   => ['brand' => '#1E3A8A', 'accent' => '#F97362', 'bg' => '#F5F8FC', 'heading' => 'Sora', 'body' => 'Rubik'],
        'salon'    => ['brand' => '#5F8D6E', 'accent' => '#C2876A', 'bg' => '#F8F6F1', 'heading' => 'Outfit', 'body' => 'DM Sans'],
        'service'  => ['brand' => '#0F766E', 'accent' => '#F59E0B', 'bg' => '#F6F9F9', 'heading' => 'Manrope', 'body' => 'Manrope'],
    ];

    // ---------------------------------------------------------------------------------------
    // Reading a store's design
    // ---------------------------------------------------------------------------------------

    /** Which home layout the store needs: orders (cart), booking, or service requests. */
    public static function flow($vendorId): string
    {
        return StoreKnowledge::flow($vendorId);
    }

    /**
     * The plan a store renders with: its draft when the owner is previewing, otherwise the
     * published plan, otherwise the preset for its business type.
     */
    public static function for($vendorId, bool $preview = false): array
    {
        $settings = Settings::where('vendor_id', $vendorId)->first();
        $raw = null;
        if ($preview && !empty(optional($settings)->ai_design_draft)) {
            $raw = json_decode($settings->ai_design_draft, true);
        }
        if (!is_array($raw) && !empty(optional($settings)->ai_design)) {
            $raw = json_decode($settings->ai_design, true);
        }

        return self::normalize(is_array($raw) ? $raw : [], $vendorId);
    }

    /**
     * Is the store's owner (or the platform admin) previewing the draft design? Opening the store
     * with ?design_preview=1 starts a preview that lasts while they click around the store; it
     * ends when the draft is published or discarded. Customers never see a draft.
     */
    public static function previewing($vendorId): bool
    {
        $user = auth()->user();
        $owner = $user && ((int) $user->type === 1 || (int) $user->id === (int) $vendorId || (int) $user->vendor_id === (int) $vendorId);
        if (!$owner) {
            return false;
        }
        $key = 'oc_design_preview_' . $vendorId;
        if (request()->boolean('design_preview')) {
            session()->put($key, true);
        }

        return (bool) session($key) && !empty(Settings::where('vendor_id', $vendorId)->value('ai_design_draft'));
    }

    /** End the owner's preview (after publish / discard). */
    public static function endPreview($vendorId): void
    {
        session()->forget('oc_design_preview_' . $vendorId);
    }

    /** The default plan for a store, before any AI design. */
    public static function defaults($vendorId): array
    {
        $type = (string) optional(Settings::where('vendor_id', $vendorId)->first())->business_type;
        $flow = self::flow($vendorId);
        $preset = self::PRESETS[$type] ?? self::PRESETS[$flow === 'booking' ? 'booking' : ($flow === 'service' ? 'service' : 'food')];

        return [
            'version'  => 1,
            'flow'     => $flow,
            'palette'  => ['brand' => $preset['brand'], 'accent' => $preset['accent'], 'bg' => $preset['bg'], 'surface' => '#FFFFFF', 'text' => '#14181B'],
            'fonts'    => ['heading' => $preset['heading'], 'body' => $preset['body']],
            'mode'     => 'light', 'corners' => 'soft', 'buttons' => 'rounded', 'shadow' => 'soft',
            'header'   => 'classic', 'hero' => $flow === 'booking' ? 'fullbleed' : 'split',
            'card'     => 'classic', 'ratio' => 'square', 'cats' => 'tiles', 'footer' => 'dark',
            'sections' => self::SECTIONS[$flow],
            'trust'    => [['icon' => 'bolt'], ['icon' => 'lock'], ['icon' => 'whatsapp'], ['icon' => 'star']],
            'copy'     => [],
        ];
    }

    // ---------------------------------------------------------------------------------------
    // Validation — the only gate between the AI and the page
    // ---------------------------------------------------------------------------------------

    /** Keep only known keys and allowed values; fill everything else from the defaults. */
    public static function normalize(array $in, $vendorId): array
    {
        $d = self::defaults($vendorId);
        $flow = $d['flow'];

        // Palette: valid hex only, then make sure text stays readable.
        foreach (['brand', 'accent', 'bg', 'surface', 'text'] as $k) {
            $v = $in['palette'][$k] ?? null;
            if (is_string($v) && preg_match('/^#?([0-9a-f]{6})$/i', trim($v), $m)) {
                $d['palette'][$k] = '#' . strtoupper($m[1]);
            }
        }
        foreach (self::OPTIONS as $key => $allowed) {
            if (isset($in[$key]) && in_array($in[$key], $allowed, true)) {
                $d[$key] = $in[$key];
            }
        }
        if (($in['mode'] ?? null) === 'dark' && !isset($in['palette']['bg'])) {
            $d['palette']['bg'] = '#111316';
            $d['palette']['surface'] = '#1A1D21';
        }
        $d['palette'] = self::readablePalette($d['palette'], $d['mode']);

        foreach (['heading', 'body'] as $k) {
            $v = $in['fonts'][$k] ?? null;
            if (in_array($v, self::FONTS, true)) {
                $d['fonts'][$k] = $v;
            }
        }

        // Sections: only ones this flow has, no duplicates; keep at least the essentials.
        if (isset($in['sections']) && is_array($in['sections'])) {
            $s = array_values(array_unique(array_filter($in['sections'], fn($x) => in_array($x, self::SECTIONS[$flow], true))));
            $essential = $flow === 'booking' ? 'services' : 'products';
            if (!in_array($essential, $s, true)) {
                array_unshift($s, $essential);
            }
            $d['sections'] = $s;
        }

        if (isset($in['trust']) && is_array($in['trust'])) {
            $trust = [];
            foreach (array_slice($in['trust'], 0, 4) as $t) {
                $trust[] = ['icon' => in_array($t['icon'] ?? null, self::ICONS, true) ? $t['icon'] : 'check'];
            }
            if ($trust) {
                $d['trust'] = $trust;
            }
        }

        // Copy, per language: plain text, length-limited.
        foreach (['en', 'ar'] as $lang) {
            $c = $in['copy'][$lang] ?? null;
            if (!is_array($c)) {
                continue;
            }
            $out = [];
            foreach (self::COPY_LIMITS as $k => $limit) {
                $text = self::clean($c[$k] ?? '', $limit);
                if ($text !== '') {
                    $out[$k] = $text;
                }
            }
            foreach (['trust' => 4, 'steps' => 4] as $list => $max) {
                $items = [];
                foreach (array_slice((array) ($c[$list] ?? []), 0, $max) as $it) {
                    $title = self::clean($it['title'] ?? '', 60);
                    if ($title !== '') {
                        $items[] = ['title' => $title, 'text' => self::clean($it['text'] ?? '', 160)];
                    }
                }
                if ($items) {
                    $out[$list] = $items;
                }
            }
            if ($out) {
                $d['copy'][$lang] = $out;
            }
        }

        return $d;
    }

    private static function clean($text, int $limit): string
    {
        $text = trim(preg_replace('/\s+/u', ' ', strip_tags((string) $text)));

        return Str::limit($text, $limit, '');
    }

    // ---------------------------------------------------------------------------------------
    // Rendering helpers used by the engine template
    // ---------------------------------------------------------------------------------------

    /** Copy in the visitor's language, then English, then the given fallback. */
    public static function text(array $design, string $key, $fallback = '')
    {
        $lang = app()->getLocale();

        return $design['copy'][$lang][$key] ?? $design['copy']['en'][$key] ?? $fallback;
    }

    /** CSS custom properties that restyle the whole engine (the template CSS reads these tokens). */
    public static function cssVars(array $d): string
    {
        $p = $d['palette'];
        $dark = $d['mode'] === 'dark';
        $radius = ['sharp' => [4, 6, 8, 12], 'soft' => [10, 16, 24, 32], 'round' => [16, 24, 32, 40]][$d['corners']];
        $shadow = [
            'flat'     => ['none', '0 1px 0 rgba(0,0,0,.04)', '0 0 0 1px rgba(0,0,0,.06)', '0 0 0 1px rgba(0,0,0,.08)'],
            'soft'     => ['0 1px 2px rgba(16,20,24,.05)', '0 2px 8px rgba(16,20,24,.06)', '0 8px 28px rgba(16,20,24,.08)', '0 24px 60px rgba(16,20,24,.12)'],
            'elevated' => ['0 2px 4px rgba(16,20,24,.08)', '0 6px 16px rgba(16,20,24,.10)', '0 16px 40px rgba(16,20,24,.14)', '0 32px 80px rgba(16,20,24,.20)'],
        ][$d['shadow']];
        $btn = ['rounded' => $radius[1] . 'px', 'pill' => '999px', 'square' => $radius[0] . 'px'][$d['buttons']];

        $vars = [
            '--brand'       => $p['brand'],
            '--brand-dark'  => self::mix($p['brand'], '#000000', .18),
            '--brand-soft'  => $dark ? self::mix($p['brand'], $p['surface'], .78) : self::mix($p['brand'], '#FFFFFF', .88),
            '--accent'      => $p['accent'],
            '--accent-soft' => $dark ? self::mix($p['accent'], $p['surface'], .78) : self::mix($p['accent'], '#FFFFFF', .88),
            '--on-brand'    => self::contrast($p['brand'], '#FFFFFF') >= 3 ? '#FFFFFF' : '#14181B',
            '--bg'          => $p['bg'],
            '--bg-alt'      => self::mix($p['bg'], $dark ? '#FFFFFF' : $p['brand'], $dark ? .05 : .05),
            '--surface'     => $p['surface'],
            '--surface-2'   => self::mix($p['surface'], $p['bg'], .5),
            '--paper'       => $p['surface'],
            '--text'        => $p['text'],
            '--ink'         => $p['text'],
            '--ink-2'       => self::mix($p['text'], $p['bg'], .15),
            '--text-2'      => self::mix($p['text'], $p['bg'], .42),
            '--muted'       => self::mix($p['text'], $p['bg'], .42),
            '--line'        => self::rgba($p['text'], .09),
            '--line-strong' => self::rgba($p['text'], .16),
            '--ring'        => '0 0 0 4px ' . self::rgba($p['brand'], .18),
            '--font'         => '"' . $d['fonts']['body'] . '", "Cairo", -apple-system, "Segoe UI", Roboto, sans-serif',
            '--font-display' => '"' . $d['fonts']['heading'] . '", "Cairo", serif',
            '--r-xs' => $radius[0] . 'px', '--r-sm' => $radius[0] . 'px', '--r' => $radius[1] . 'px', '--r-lg' => $radius[2] . 'px', '--r-xl' => $radius[3] . 'px',
            '--r-btn' => $btn,
            '--sh-xs' => $shadow[0], '--sh-sm' => $shadow[1], '--sh' => $shadow[2], '--sh-lg' => $shadow[3],
        ];

        return implode(';', array_map(fn($k, $v) => $k . ':' . $v, array_keys($vars), $vars));
    }

    /**
     * Fallback picture when the merchant uploaded none: a matching stock food photo for food
     * businesses, the neutral placeholder for everyone else (a random stock photo of the wrong
     * kind of business looks worse than no photo).
     */
    public static function stockImage($name, $seed, $vendorId): string
    {
        static $types = [];
        $types[$vendorId] ??= (string) Settings::where('vendor_id', $vendorId)->value('business_type');

        return in_array($types[$vendorId], ['food', 'cafe', 'grocery', ''], true)
            ? \App\Helpers\helper::food_image($name, $seed)
            : \App\Helpers\helper::image_path('');
    }

    /** Body classes that switch the layout variants in engine.css. */
    public static function bodyClasses(array $d): string
    {
        $c = ['oce', 'oce-' . $d['flow'], 'oce-mode-' . $d['mode']];
        foreach (['header', 'hero', 'card', 'ratio', 'cats', 'footer', 'buttons', 'corners'] as $k) {
            $c[] = 'oce-' . $k . '-' . $d[$k];
        }

        return implode(' ', $c);
    }

    /** Google Fonts stylesheet URL for the plan's fonts (plus Cairo for Arabic). */
    public static function fontUrl(array $d): string
    {
        $families = array_unique([$d['fonts']['heading'], $d['fonts']['body'], 'Cairo']);
        $q = implode('&', array_map(fn($f) => 'family=' . str_replace(' ', '+', $f) . ':wght@400;500;600;700;800', $families));

        return 'https://fonts.googleapis.com/css2?' . $q . '&display=swap';
    }

    // ---------------------------------------------------------------------------------------
    // Asking the AI
    // ---------------------------------------------------------------------------------------

    /**
     * Ask the AI for a design plan from the merchant's answers. $base is the plan to refine
     * (for "make it darker" style requests). Returns ['success' => bool, 'design' => array, 'error' => string].
     */
    public static function generate($vendorId, array $brief, ?array $base = null): array
    {
        if (!AiAssistant::enabled()) {
            return ['success' => false, 'error' => trans('messages.assistant_unavailable')];
        }
        $flow = self::flow($vendorId);
        $vendor = User::find($vendorId);
        $settings = Settings::where('vendor_id', $vendorId)->first();
        $cats = Category::where('vendor_id', $vendorId)->where('is_deleted', 2)->orderBy('reorder_id')->limit(12)->pluck('name')->implode(', ');
        $items = Item::where('vendor_id', $vendorId)->where('is_available', 1)->orderBy('reorder_id')->limit(15)->pluck('item_name')->implode(', ');

        $business = implode("\n", array_filter([
            'Business name: ' . StoreKnowledge::businessName($vendorId),
            'Business type: ' . (optional($settings)->business_type ?: $flow),
            optional($settings)->description ? 'About: ' . self::clean($settings->description, 400) : null,
            $cats ? 'Categories: ' . $cats : null,
            $items ? 'Some products/services: ' . $items : null,
            'Customers complete: ' . ['orders' => 'online orders with a cart', 'booking' => 'bookings / appointments', 'service' => 'service requests'][$flow],
        ]));
        $answers = implode("\n", array_filter([
            !empty($brief['style'])    ? 'Style / mood: ' . self::clean($brief['style'], 80) : null,
            !empty($brief['colors'])   ? 'Colour wishes: ' . self::clean($brief['colors'], 120) : null,
            !empty($brief['mode'])     ? 'Light or dark: ' . self::clean($brief['mode'], 20) : null,
            !empty($brief['audience']) ? 'Target customers: ' . self::clean($brief['audience'], 160) : null,
            !empty($brief['describe']) ? 'In their own words: ' . self::clean($brief['describe'], 800) : null,
        ]));

        $input = "BUSINESS:\n" . $business . "\n\nMERCHANT'S WISHES:\n" . ($answers ?: '(none — choose what suits the business best)');
        if ($base) {
            $input .= "\n\nCURRENT DESIGN (change only what the request asks, keep the rest):\n" . json_encode($base, JSON_UNESCAPED_UNICODE)
                . "\n\nREQUESTED CHANGE:\n" . self::clean($brief['refine'] ?? '', 400);
        }

        $content = [['type' => 'input_text', 'text' => $input]];
        $logo = self::logoDataUrl(optional($settings)->logo);
        if ($logo) {
            $content[] = ['type' => 'input_text', 'text' => 'The business logo is attached — build the palette around its colours unless the merchant asked otherwise.'];
            $content[] = ['type' => 'input_image', 'image_url' => $logo];
        }

        $ai = app(AiAssistant::class);
        $result = $ai->runMultimodal(self::instructions($flow), $content, 4000, config('services.openai.design_model'));
        if (empty($result['success'])) {
            return ['success' => false, 'error' => $result['error'] ?? trans('messages.assistant_unavailable')];
        }
        $plan = $ai->parseJson((string) $result['text']);
        if (!is_array($plan)) {
            return ['success' => false, 'error' => trans('messages.design_generate_failed')];
        }

        return ['success' => true, 'design' => self::normalize($plan, $vendorId)];
    }

    private static function instructions(string $flow): string
    {
        $sections = implode(', ', self::SECTIONS[$flow]);
        $fonts = implode(', ', self::FONTS);
        $icons = implode(', ', self::ICONS);
        $o = self::OPTIONS;
        $opt = fn($k) => implode('|', $o[$k]);

        return <<<TXT
You are a senior brand and web designer. Design the online store of ONE real business on the
Order Click platform. You do not write code: you return a design plan that our store engine
renders. Products, prices, cart, checkout and booking are handled by the engine.

Design well: pick a coherent palette with strong contrast (brand colour usable for buttons with
white or dark text), a heading + body font pairing that fits the business, and layout variants
that suit it (e.g. luxury → serif headings, generous space, dark or cream tones; fast food →
bold colours, energetic; clinic → calm, trustworthy blues). Respect the merchant's wishes.

Write all customer-facing text twice, in English ("en") and Arabic ("ar"), specific to THIS
business — never generic filler, never invented facts (no fake prices, years, awards, delivery
times or ratings). Short, warm, persuasive.

Return ONLY this JSON object:
{
 "palette": {"brand": "#RRGGBB", "accent": "#RRGGBB", "bg": "#RRGGBB", "surface": "#RRGGBB", "text": "#RRGGBB"},
 "fonts": {"heading": "<one of: {$fonts}>", "body": "<one of the same list>"},
 "mode": "{$opt('mode')}", "corners": "{$opt('corners')}", "buttons": "{$opt('buttons')}", "shadow": "{$opt('shadow')}",
 "header": "{$opt('header')}", "hero": "{$opt('hero')}", "card": "{$opt('card')}", "ratio": "{$opt('ratio')}",
 "cats": "{$opt('cats')}", "footer": "{$opt('footer')}",
 "sections": [ordered subset of: {$sections}],
 "trust": [4 x {"icon": "<one of: {$icons}>"}],
 "copy": {
   "en": {"hero_eyebrow": "", "hero_title": "", "hero_text": "", "hero_cta": "",
          "categories_title": "", "categories_text": "", "products_title": "", "products_text": "",
          "services_title": "", "services_text": "", "team_title": "", "offers_title": "",
          "about_title": "", "about_text": "", "steps_title": "", "reviews_title": "",
          "cta_title": "", "cta_text": "", "cta_button": "", "footer_text": "",
          "trust": [4 x {"title": "", "text": ""}], "steps": [3 x {"title": "", "text": ""}]},
   "ar": { same keys, in Arabic }
 }
}
TXT;
    }

    /** The store logo as a data URL for the vision model (skipped when missing or large). */
    private static function logoDataUrl(?string $logo): ?string
    {
        if (empty($logo)) {
            return null;
        }
        foreach (['admin-assets/images/about/logo/', 'admin-assets/images/profile/', 'admin-assets/images/'] as $dir) {
            $path = storage_path('app/public/' . $dir . basename($logo));
            if (is_file($path) && filesize($path) < 3 * 1024 * 1024) {
                $mime = mime_content_type($path);
                if (in_array($mime, ['image/png', 'image/jpeg', 'image/webp', 'image/gif'], true)) {
                    return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($path));
                }
            }
        }

        return null;
    }

    // ---------------------------------------------------------------------------------------
    // Colour maths
    // ---------------------------------------------------------------------------------------

    /** Make text readable on the background (WCAG AA) whatever the AI picked. */
    private static function readablePalette(array $p, string $mode): array
    {
        if (self::contrast($p['text'], $p['bg']) < 4.5 || self::contrast($p['text'], $p['surface']) < 4.5) {
            $p['text'] = self::luminance($p['bg']) > .4 ? '#14181B' : '#F3F4F6';
        }
        if ($mode === 'dark' && self::luminance($p['bg']) > .4) {
            $p['bg'] = '#111316';
            $p['surface'] = '#1A1D21';
            $p['text'] = '#F3F4F6';
        }
        // Buttons and links use the brand colour, so it must stand out from the background:
        // swap in the accent when that works, otherwise shift the brand until it does.
        if (self::contrast($p['brand'], $p['bg']) < 3) {
            if (self::contrast($p['accent'], $p['bg']) >= 3) {
                [$p['brand'], $p['accent']] = [$p['accent'], $p['brand']];
            } else {
                $toward = self::luminance($p['bg']) > .4 ? '#000000' : '#FFFFFF';
                for ($t = .15; $t < 1 && self::contrast($p['brand'], $p['bg']) < 3; $t += .15) {
                    $p['brand'] = self::mix($p['brand'], $toward, .15);
                }
            }
        }

        return $p;
    }

    private static function rgb(string $hex): array
    {
        $hex = ltrim($hex, '#');

        return [hexdec(substr($hex, 0, 2)), hexdec(substr($hex, 2, 2)), hexdec(substr($hex, 4, 2))];
    }

    private static function luminance(string $hex): float
    {
        $c = array_map(function ($v) {
            $v /= 255;
            return $v <= .03928 ? $v / 12.92 : (($v + .055) / 1.055) ** 2.4;
        }, self::rgb($hex));

        return .2126 * $c[0] + .7152 * $c[1] + .0722 * $c[2];
    }

    private static function contrast(string $a, string $b): float
    {
        $l = [self::luminance($a), self::luminance($b)];

        return (max($l) + .05) / (min($l) + .05);
    }

    /** $a mixed toward $b by $t (0..1). */
    private static function mix(string $a, string $b, float $t): string
    {
        [$r1, $g1, $b1] = self::rgb($a);
        [$r2, $g2, $b2] = self::rgb($b);

        return sprintf('#%02X%02X%02X', $r1 + ($r2 - $r1) * $t, $g1 + ($g2 - $g1) * $t, $b1 + ($b2 - $b1) * $t);
    }

    private static function rgba(string $hex, float $alpha): string
    {
        [$r, $g, $b] = self::rgb($hex);

        return "rgba($r,$g,$b,$alpha)";
    }
}
