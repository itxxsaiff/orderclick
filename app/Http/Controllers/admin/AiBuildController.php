<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\BookingService;
use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use App\Models\Settings;
use App\Services\AiAssistant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AiBuildController extends Controller
{
    private function vendorId()
    {
        return Auth::user()->type == 4 ? Auth::user()->vendor_id : Auth::user()->id;
    }

    /** The "AI is setting up your store" page shown right after registration. */
    public function setup()
    {
        $vid = $this->vendorId();
        $settings = Settings::where('vendor_id', $vid)->first();
        // The assistant is a permanent tool, not a one-time setup step: a merchant who already
        // has products must still be able to open it and add more. Only a missing AI key sends
        // them back (previously an existing catalogue bounced them to the dashboard, so the
        // "Open AI Assistant" button looked dead).
        if (!AiAssistant::enabled()) {
            return redirect('admin/dashboard');
        }
        $storeName = \App\Services\StoreKnowledge::businessName($vid);
        $businessType = ($settings && $settings->business_type) ? $settings->business_type : 'food';
        [$ssSystem] = $this->businessContext($vid, $settings);
        return view('admin.auth.store_setup', compact('storeName', 'businessType', 'ssSystem'));
    }

    /**
     * AJAX: read an uploaded menu/product file (image or PDF) and return extracted offerings.
     * Judges relevance + clarity so the UI can guide the merchant back to manual entry when needed.
     */
    public function extract(Request $request, AiAssistant $ai)
    {
        if (!AiAssistant::enabled()) {
            return response()->json(['success' => false, 'error' => 'AI is not configured.'], 422);
        }
        $validator = \Validator::make($request->all(), [
            'file' => 'required|file|mimes:jpg,jpeg,png,webp,pdf|max:10240', // 10 MB
        ], [
            'file.mimes' => 'Please upload an image (JPG, PNG, WEBP) or a PDF.',
            'file.max' => 'The file is too large. Please keep it under 10 MB.',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->errors()->first('file')], 200);
        }

        $vid = $this->vendorId();
        $settings = Settings::where('vendor_id', $vid)->first();
        [, $businessType] = $this->businessContext($vid, $settings);
        $lang = $this->contentLanguage();

        try {
            $file = $request->file('file');
            $mime = $file->getMimeType() ?: 'application/octet-stream';
            $dataUrl = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($file->getRealPath()));
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'error' => 'Could not read the uploaded file. Please try again.'], 200);
        }

        $res = $ai->extractFromFile($dataUrl, $mime, $businessType, $lang);
        if (!$res['success']) {
            \Log::error('AI extract failed for vendor ' . $vid . ': ' . $res['error']);
            return response()->json(['success' => false, 'error' => $res['error']], 200);
        }

        $d = $res['data'];
        if (empty($d['relevant'])) {
            return response()->json([
                'success' => false, 'kind' => 'irrelevant',
                'error' => trim((string) ($d['reason'] ?? '')) ?: 'This file does not look like a menu or product list.',
            ], 200);
        }
        if (array_key_exists('clear', $d) && !$d['clear']) {
            return response()->json([
                'success' => false, 'kind' => 'unclear',
                'error' => trim((string) ($d['reason'] ?? '')) ?: 'The image is not clear enough to read. Please upload a sharper photo.',
            ], 200);
        }

        $offerings = trim((string) ($d['offerings'] ?? ''));
        if ($offerings === '') {
            return response()->json([
                'success' => false, 'kind' => 'empty',
                'error' => 'We could not find any items in that file. Try another photo, or type your items below.',
            ], 200);
        }

        return response()->json([
            'success' => true,
            'offerings' => $offerings,
            'categories' => trim((string) ($d['categories'] ?? '')),
        ]);
    }

    /** The language AI content is written in: the language the merchant is using the panel in. */
    private function contentLanguage(): string
    {
        $names = ['ar' => 'Arabic', 'en' => 'English', 'de' => 'German', 'fr' => 'French', 'es' => 'Spanish', 'tr' => 'Turkish', 'ur' => 'Urdu'];

        return $names[app()->getLocale()] ?? 'English';
    }

    /** System + a plain description of the business, for the AI. */
    private function businessContext($vid, $settings): array
    {
        $vendor = User::find($vid);
        $businessType = optional($settings)->business_type ?: 'food';
        // Same rule the storefront uses to decide what it shows (cart, booking or service requests),
        // so the AI always creates what the website will display.
        $system = \App\Services\StoreKnowledge::flow($vid);
        $about = trim(implode(' / ', array_filter([
            optional(\App\Models\Activity::find(optional($vendor)->activity_id))->name,
            optional(\App\Models\Specialization::find(optional($vendor)->specialization_id))->name,
            optional(\App\Models\StoreCategory::find(optional($vendor)->store_id))->name,
        ]))) ?: $businessType;

        return [$system, $about];
    }

    /**
     * AJAX: build the store in small steps, driven by the page so that no request is long enough
     * to time out (a big price list used to make one huge AI call that failed):
     *   stage=plan   description, About text, colour and the category list
     *   stage=items  the items of one category (called once per category)
     *   stage=design the website look, when the store has none yet
     */
    public function build(Request $request, AiAssistant $ai)
    {
        @set_time_limit(180);
        $vid = $this->vendorId();
        $settings = Settings::where('vendor_id', $vid)->first();
        if (empty($settings)) {
            return response()->json(['success' => false, 'error' => 'Store not found.'], 422);
        }
        $storeName = \App\Services\StoreKnowledge::businessName($vid);
        $offerings = mb_substr(trim((string) $request->input('offerings', '')), 0, 6000);
        $lang = $this->contentLanguage();
        [$system, $about] = $this->businessContext($vid, $settings);
        $stage = (string) $request->input('stage', 'plan');

        if ($stage === 'items') {
            $category = mb_substr(trim((string) $request->input('category', '')), 0, 60);
            $all = array_values(array_filter(array_map(fn($c) => mb_substr(trim((string) $c), 0, 60), (array) $request->input('all_categories', []))));
            if ($category === '') {
                return response()->json(['success' => false, 'error' => 'Missing category.'], 422);
            }
            $res = $ai->buildItems($system, $about, $storeName, $category, $all, $offerings, $lang, $request->boolean('assigned'));
            if (!$res['success']) {
                return response()->json(['success' => false, 'error' => $res['error']], 422);
            }
            $items = array_map(fn($it) => (is_array($it) ? $it : []) + ['category' => $category], $res['items']);
            foreach ($items as &$it) {
                $it['category'] = $category;
            }
            unset($it);
            // Booking stores keep a Services catalogue (sessions with a duration); service and
            // product stores keep categories + items (services show a "Request" button).
            $count = $system === \App\Helpers\Systems::BOOKING
                ? $this->saveBookingServices($vid, $items)
                : $this->saveProducts($vid, [$category], $items);

            return response()->json(['success' => true, 'count' => $count]);
        }

        if ($stage === 'design') {
            // The whole website look in the same run — only when the store has no design yet, so a
            // merchant who already designed their site never loses it.
            if (empty($settings->ai_design)) {
                $design = \App\Services\StoreDesign::generate($vid, ['describe' => mb_substr($offerings, 0, 700)]);
                if (!empty($design['success'])) {
                    $settings->ai_design = json_encode($design['design'], JSON_UNESCAPED_UNICODE);
                    $settings->save();
                }
            }

            return response()->json(['success' => true, 'redirect' => url('admin/dashboard')]);
        }

        // stage=plan
        $categories = mb_substr(trim((string) $request->input('categories', '')), 0, 500);
        $whatsapp = trim((string) $request->input('whatsapp', ''));
        $prefix = trim((string) $request->input('prefix', ''));

        $res = $ai->planStore($system, $about, $storeName, $offerings, $categories, $lang);
        if (!$res['success']) {
            return response()->json(['success' => false, 'error' => $res['error']], 422);
        }
        $data = $res['data'];

        if (!empty($data['description'])) {
            $settings->description = mb_substr(trim((string) $data['description']), 0, 600);
        }
        if (!empty($data['primary_color']) && preg_match('/^#[0-9a-fA-F]{6}$/', $data['primary_color'])) {
            $settings->primary_color = $data['primary_color'];
        }
        // Merchant-provided contact + order settings (direct, not AI).
        if ($whatsapp !== '') {
            $settings->whatsapp_number = preg_replace('/[^0-9+]/', '', $whatsapp);
            if (empty($settings->contact)) {
                $settings->contact = $settings->whatsapp_number;
            }
        }
        if ($prefix !== '') {
            $settings->order_prefix = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $prefix));
        }
        $settings->save();

        // About page text, unless the merchant already wrote one.
        $aboutText = trim((string) ($data['about'] ?? ''));
        if ($aboutText !== '') {
            $page = \App\Models\About::where('vendor_id', $vid)->first();
            if (!$page || trim(strip_tags((string) $page->about_content)) === '') {
                $page = $page ?: new \App\Models\About();
                $page->vendor_id = $vid;
                $page->about_content = '<p>' . nl2br(e(mb_substr($aboutText, 0, 2000))) . '</p>';
                $page->save();
            }
        }

        return response()->json(['success' => true, 'categories' => $data['categories'], 'groups' => $data['groups'] ?? null, 'system' => $system]);
    }


    private function saveProducts($vid, array $categories, array $items): int
    {
        // Build category name → id map. The assistant can be run again later, so an existing
        // category with the same name is reused instead of being duplicated.
        $catMap = [];
        foreach (Category::where('vendor_id', $vid)->where('is_deleted', 2)->get() as $existing) {
            $catMap[strtolower(trim((string) $existing->name))] = $existing->id;
        }
        $reorder = (int) Category::where('vendor_id', $vid)->max('reorder_id') + 1;
        $names = !empty($categories) ? $categories : collect($items)->pluck('category')->filter()->unique()->values()->all();
        foreach ($names as $name) {
            $name = trim((string) $name);
            if ($name === '' || isset($catMap[strtolower($name)])) continue;
            $c = new Category;
            $c->vendor_id = $vid;
            $c->name = Str::limit($name, 60, '');
            $c->slug = Str::slug($name) . '-' . $vid . '-' . $reorder;
            $c->is_available = 1;
            $c->is_deleted = 2;
            $c->reorder_id = $reorder++;
            $c->save();
            $catMap[strtolower($name)] = $c->id;
        }
        $firstCat = $catMap ? array_values($catMap)[0] : null;

        $n = 0;
        foreach ($items as $i => $it) {
            $itemName = trim((string) ($it['name'] ?? ''));
            if ($itemName === '') continue;
            $catId = $catMap[strtolower(trim((string) ($it['category'] ?? '')))] ?? $firstCat;
            if (!$catId) continue;
            // Running the assistant twice should not duplicate what is already on the menu.
            if (Item::where('vendor_id', $vid)->where('cat_id', $catId)->where('item_name', Str::limit($itemName, 100, ''))->exists()) {
                continue;
            }
            $item = new Item;
            $item->vendor_id = $vid;
            $item->cat_id = $catId;
            $item->item_name = Str::limit($itemName, 100, '');
            $item->description = trim((string) ($it['description'] ?? ''));
            $item->item_price = is_numeric($it['price'] ?? null) ? $it['price'] : 0;
            $item->slug = Str::slug($itemName) . '-' . $vid . '-' . ($i + 1);
            $item->is_available = 1;
            $item->reorder_id = $i + 1;
            $item->has_variants = 0;
            $item->has_extras = 0;
            $item->avg_ratting = 0;
            $item->save();
            $n++;
        }
        return $n;
    }

    private function saveBookingServices($vid, array $items): int
    {
        $n = 0;
        foreach ($items as $i => $it) {
            $name = trim((string) ($it['name'] ?? ''));
            if ($name === '') continue;
            // Running the assistant again should not duplicate services that already exist.
            if (BookingService::where('vendor_id', $vid)->where('name', Str::limit($name, 120, ''))->exists()) {
                continue;
            }
            $s = new BookingService;
            $s->vendor_id = $vid;
            $s->name = Str::limit($name, 120, '');
            $s->category = trim((string) ($it['category'] ?? ''));
            $s->price = is_numeric($it['price'] ?? null) ? $it['price'] : 0;
            $s->duration = trim((string) ($it['duration'] ?? ''));
            $s->description = trim((string) ($it['description'] ?? ''));
            $s->is_available = 1;
            $s->reorder_id = $i + 1;
            $s->save();
            $n++;
        }
        return $n;
    }
}
