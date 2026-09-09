<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\BookingService;
use App\Models\Category;
use App\Models\Item;
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
        // Already has content, or AI not configured → straight to dashboard.
        if (!AiAssistant::enabled() || $this->alreadyBuilt($vid)) {
            return redirect('admin/dashboard');
        }
        $storeName = ($settings && $settings->website_title) ? $settings->website_title : Auth::user()->name;
        $businessType = ($settings && $settings->business_type) ? $settings->business_type : 'food';
        return view('admin.auth.store_setup', compact('storeName', 'businessType'));
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
        $businessType = ($settings && $settings->business_type) ? $settings->business_type : 'food';
        $lang = $request->input('lang', 'English');

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

    /** AJAX: generate + save the starter store. */
    public function build(Request $request, AiAssistant $ai)
    {
        $vid = $this->vendorId();
        $settings = Settings::where('vendor_id', $vid)->first();
        if (empty($settings)) {
            return response()->json(['success' => false, 'error' => 'Store not found.'], 422);
        }
        if ($this->alreadyBuilt($vid)) {
            return response()->json(['success' => true, 'text' => 'Already set up.']);
        }

        $storeName = $settings->website_title ?: Auth::user()->name;
        $businessType = $settings->business_type ?: 'food';
        $offerings = trim((string) $request->input('offerings', ''));
        $categories = trim((string) $request->input('categories', ''));
        $whatsapp = trim((string) $request->input('whatsapp', ''));
        $prefix = trim((string) $request->input('prefix', ''));
        $lang = $request->input('lang', 'English');

        $res = $ai->buildStore($businessType, $storeName, $offerings, $lang, $categories);
        if (!$res['success']) {
            return response()->json(['success' => false, 'error' => $res['error']], 422);
        }
        $data = $res['data'];

        // Store description + brand colour.
        if (!empty($data['description'])) {
            $settings->description = $data['description'];
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

        $items = is_array($data['items'] ?? null) ? $data['items'] : [];

        if ($businessType === 'booking') {
            $count = $this->saveBookingServices($vid, $items);
        } elseif (in_array($businessType, ['food', 'grocery', 'retail'], true)) {
            $count = $this->saveProducts($vid, $data['categories'] ?? [], $items);
        } else {
            $count = 0; // service-type has no catalog; description + colour are enough
        }

        return response()->json(['success' => true, 'count' => $count, 'redirect' => url('admin/dashboard')]);
    }

    private function alreadyBuilt($vid): bool
    {
        return Item::where('vendor_id', $vid)->exists()
            || BookingService::where('vendor_id', $vid)->exists()
            || Category::where('vendor_id', $vid)->where('is_deleted', 2)->exists();
    }

    private function saveProducts($vid, array $categories, array $items): int
    {
        // Build category name → id map (create as needed).
        $catMap = [];
        $reorder = 1;
        $names = !empty($categories) ? $categories : collect($items)->pluck('category')->filter()->unique()->values()->all();
        foreach ($names as $name) {
            $name = trim((string) $name);
            if ($name === '') continue;
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
