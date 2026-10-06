<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Settings;
use App\Models\User;
use App\Services\AiAssistant;
use App\Services\StoreDesign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

/**
 * The merchant's AI store designer: describe the store → the AI drafts a design plan → preview
 * it on the real store → refine or fine-tune → publish. Everything is saved as a draft until the
 * merchant publishes, so customers never see a half-finished design.
 */
class StoreDesignController extends Controller
{
    private function vendorId(): int
    {
        return (int) (Auth::user()->type == 4 ? Auth::user()->vendor_id : Auth::user()->id);
    }

    private function settings(): Settings
    {
        return Settings::where('vendor_id', $this->vendorId())->firstOrFail();
    }

    /** The plan being edited: the draft if there is one, else the published plan. */
    private function working(Settings $settings): array
    {
        $raw = json_decode((string) ($settings->ai_design_draft ?: $settings->ai_design), true);

        return StoreDesign::normalize(is_array($raw) ? $raw : [], $this->vendorId());
    }

    public function index()
    {
        $vid = $this->vendorId();
        $settings = $this->settings();
        $vendor = User::find($vid);

        return view('admin.design.index', [
            'design'    => $this->working($settings),
            'brief'     => json_decode((string) $settings->ai_design_brief, true) ?: [],
            'hasDraft'  => !empty($settings->ai_design_draft),
            'published' => !empty($settings->ai_design),
            'storeUrl'  => URL::to($vendor->slug),
            'aiReady'   => AiAssistant::enabled(),
            'flow'      => StoreDesign::flow($vid),
        ]);
    }

    /** Ask the AI for a new design (or a refinement of the current one). */
    public function generate(Request $request)
    {
        $request->validate([
            'style'    => 'nullable|string|max:80',
            'colors'   => 'nullable|string|max:120',
            'mode'     => 'nullable|in:auto,light,dark',
            'audience' => 'nullable|string|max:160',
            'describe' => 'nullable|string|max:800',
            'refine'   => 'nullable|string|max:400',
        ]);
        $vid = $this->vendorId();
        $settings = $this->settings();

        $brief = $request->only(['style', 'colors', 'mode', 'audience', 'describe']);
        if (($brief['mode'] ?? 'auto') === 'auto') {
            unset($brief['mode']);
        }
        $refining = trim((string) $request->refine) !== '';
        if ($refining) {
            $brief = (json_decode((string) $settings->ai_design_brief, true) ?: []) + ['refine' => $request->refine];
        }

        @set_time_limit(180);
        $result = StoreDesign::generate($vid, $brief, $refining ? $this->working($settings) : null);
        if (empty($result['success'])) {
            return response()->json(['success' => false, 'message' => $result['error'] ?? trans('messages.design_generate_failed')]);
        }

        unset($brief['refine']);
        $settings->ai_design_draft = json_encode($result['design'], JSON_UNESCAPED_UNICODE);
        if (!$refining) {
            $settings->ai_design_brief = json_encode($brief, JSON_UNESCAPED_UNICODE);
        }
        $settings->save();

        return response()->json(['success' => true, 'design' => $result['design']]);
    }

    /** Manual fine-tuning from the panel; merged into the draft and validated like AI output. */
    public function tweak(Request $request)
    {
        $settings = $this->settings();
        $design = $this->working($settings);

        foreach (['brand', 'accent', 'bg', 'surface', 'text'] as $k) {
            if ($request->filled("palette.$k")) {
                $design['palette'][$k] = $request->input("palette.$k");
            }
        }
        foreach (['heading', 'body'] as $k) {
            if ($request->filled("fonts.$k")) {
                $design['fonts'][$k] = $request->input("fonts.$k");
            }
        }
        foreach (array_keys(StoreDesign::OPTIONS) as $k) {
            if ($request->filled($k)) {
                $design[$k] = $request->input($k);
                // A new light/dark choice needs a matching palette, not the old background.
                if ($k === 'mode') {
                    unset($design['palette']['bg'], $design['palette']['surface'], $design['palette']['text']);
                    if ($request->input('mode') === 'dark') {
                        $design['palette'] += ['bg' => '#111316', 'surface' => '#1A1D21', 'text' => '#F3F4F6'];
                    } else {
                        $design['palette'] += ['bg' => '#FAF8F5', 'surface' => '#FFFFFF', 'text' => '#14181B'];
                    }
                }
            }
        }
        if (is_array($request->input('sections'))) {
            $design['sections'] = $request->input('sections');
        }

        $design = StoreDesign::normalize($design, $this->vendorId());
        $settings->ai_design_draft = json_encode($design, JSON_UNESCAPED_UNICODE);
        $settings->save();

        return response()->json(['success' => true, 'design' => $design]);
    }

    public function publish()
    {
        $settings = $this->settings();
        if (!empty($settings->ai_design_draft)) {
            $settings->ai_design = $settings->ai_design_draft;
            $settings->ai_design_draft = null;
            $settings->save();
        }
        StoreDesign::endPreview($this->vendorId());

        return redirect('admin/design')->with('success', trans('messages.design_published'));
    }

    public function discard()
    {
        $settings = $this->settings();
        $settings->ai_design_draft = null;
        $settings->save();
        StoreDesign::endPreview($this->vendorId());

        return redirect('admin/design')->with('success', trans('messages.success'));
    }

    /** Back to the default look for the business type. */
    public function reset()
    {
        $settings = $this->settings();
        $settings->ai_design = null;
        $settings->ai_design_draft = null;
        $settings->save();
        StoreDesign::endPreview($this->vendorId());

        return redirect('admin/design')->with('success', trans('messages.success'));
    }
}
