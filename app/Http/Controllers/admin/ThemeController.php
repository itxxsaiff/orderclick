<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Theme;
use App\Models\Activity;
use App\Helpers\Systems;
use App\Helpers\helper;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class ThemeController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'all');

        $query = Theme::where('vendor_id', 1);

        if (Systems::isValid($tab)) {
            $query->where(function ($q) use ($tab) {
                $q->where('system', $tab);
                // Templates added before the system column existed count as Orders & Stores.
                if ($tab === Systems::ORDERS) {
                    $q->orWhereNull('system')->orWhere('system', '');
                }
            });
        }

        if ($search = trim((string) $request->get('q'))) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        $themes = $query->orderBy('reorder_id')->get();

        $tabCounts = [];
        foreach (array_merge(['all'], Systems::keys()) as $key) {
            $c = Theme::where('vendor_id', 1);
            if ($key !== 'all') {
                $c->where(function ($q) use ($key) {
                    $q->where('system', $key);
                    if ($key === Systems::ORDERS) $q->orWhereNull('system')->orWhere('system', '');
                });
            }
            $tabCounts[$key] = $c->count();
        }

        return view('admin.theme.index', compact('themes', 'tab', 'tabCounts'));
    }

    public function add()
    {
        return view('admin.theme.add', ['activities' => Activity::orderBy('reorder_id')->get()]);
    }

    public function edit(Request $request)
    {
        $theme = Theme::where('vendor_id', 1)->where('id', $request->id)->first();
        $activities = Activity::orderBy('reorder_id')->get();

        return view('admin.theme.edit', compact('theme', 'activities'));
    }

    /**
     * System, applicable activities and the storefront template number.
     * Activities are validated against the chosen system so a template can never be tagged with
     * an activity from a system it does not belong to.
     */
    private function applyScope($theme, Request $request): void
    {
        $theme->system = Systems::normalise($request->system);

        $ids = array_filter((array) $request->activity_ids);
        if (!empty($ids)) {
            $ids = Activity::whereIn('id', $ids)
                ->where('system', $theme->system)
                ->pluck('id')->map(fn($i) => (string) $i)->all();
        }
        // Empty = every activity in this system ("All Activities").
        $theme->activity_ids = empty($ids) ? null : implode('|', $ids);

        if (is_numeric($request->template) && (int) $request->template > 0) {
            $theme->template = (int) $request->template;
        }
    }
    public function update(Request $request)
    {
        $edittheme = Theme::where('id', $request->id)->first();
        $edittheme->name = $request->name;
        $edittheme->vendor_id = Auth::user()->id;
        $this->applyScope($edittheme, $request);
        if ($request->hasfile('image')) {
            $validator = Validator::make($request->all(), [
                'image' => 'image|max:' . helper::imagesize() . '|' . helper::imageext(),
            ], [
                "image.image" => trans('messages.enter_image_file'),
                'image.max' => trans('messages.image_size_message'),
            ]);
            if ($validator->fails()) {
                return redirect()->back()->with('error', trans('messages.image_size_message') . ' ' . helper::appdata('')->image_size . ' ' . 'MB');
            }
            if (file_exists(storage_path('app/public/admin-assets/images/theme/' . $edittheme->theme))) {
                @unlink(storage_path('app/public/admin-assets/images/theme/' . $edittheme->theme));
            }
            $theme = 'theme-' . uniqid() . '.' . $request->image->getClientOriginalExtension();
            $request->file('image')->move(storage_path('app/public/admin-assets/images/theme/'), $theme);
            $edittheme->image = $theme;
        }
        $edittheme->save();
        return redirect('admin/themes')->with('success', trans('messages.success'));
    }
    public function save(Request $request)
    {
        $newtheme = new Theme();
        $newtheme->name = $request->name;
        $newtheme->vendor_id = Auth::user()->id;
        $this->applyScope($newtheme, $request);
        if ($request->hasfile('image')) {
            $validator = Validator::make($request->all(), [
                'image' => 'image|max:' . helper::imagesize() . '|' . helper::imageext(),
            ], [
                "image.image" => trans('messages.enter_image_file'),
                'image.max' => trans('messages.image_size_message'),
            ]);
            if ($validator->fails()) {
                return redirect()->back()->with('error', trans('messages.image_size_message') . ' ' . helper::appdata('')->image_size . ' ' . 'MB');
            }
            $theme = 'theme-' . uniqid() . '.' . $request->image->getClientOriginalExtension();
            $request->file('image')->move(storage_path('app/public/admin-assets/images/theme/'), $theme);
            $newtheme->image = $theme;
        }
        // reorder_id is NOT NULL with no default — without this, adding a template fails.
        $newtheme->reorder_id = (int) Theme::where('vendor_id', 1)->max('reorder_id') + 1;
        // The screenshot is uploaded later from the admin panel, so the column must not be null.
        $newtheme->image = $newtheme->image ?: '';
        $newtheme->save();
        return redirect('admin/themes')->with('success', trans('messages.success'));
    }
    public function delete(Request $request)
    {
        $theme = Theme::where('id', $request->id)->first();
        if (file_exists(storage_path('app/public/admin-assets/images/theme/' . $theme->image))) {
            unlink(storage_path('app/public/admin-assets/images/theme/' . $theme->image));
        }
        $theme->delete();
        return redirect('admin/themes')->with('success', trans('messages.success'));
    }
    public function reorder_theme(Request $request)
    {
        if (Auth::user()->type == 4) {
            $vendor_id = Auth::user()->vendor_id;
        } else {
            $vendor_id = Auth::user()->id;
        }
        $gettheme = Theme::where('vendor_id', $vendor_id)->get();
        foreach ($gettheme as $theme) {
            foreach ($request->order as $order) {
                $theme = Theme::where('id', $order['id'])->first();
                $theme->reorder_id = $order['position'];
                $theme->save();
            }
        }
        return response()->json(['status' => 1, 'msg' => trans('messages.success')], 200);
    }
}
