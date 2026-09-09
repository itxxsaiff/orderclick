<?php

namespace App\Http\Controllers\admin;

use App\Helpers\helper;
use App\Http\Controllers\Controller;
use App\Models\Features;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FeaturesController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::user()->type == 4) {
            $vendor_id = Auth::user()->vendor_id;
        } else {
            $vendor_id = Auth::user()->id;
        }
        $features = Features::where('vendor_id', $vendor_id)->orderBy('reorder_id')->orderBy('id')->get();
        return view('admin.features.index', compact('features'));
    }
    public function add(Request $request)
    {
        $vendor_id = Auth::user()->type == 4 ? Auth::user()->vendor_id : Auth::user()->id;
        // Default Display Order = next in sequence.
        $nextOrder = (int) Features::where('vendor_id', $vendor_id)->max('reorder_id') + 1;

        return view('admin.features.add', compact('nextOrder'));
    }

    /** Applies To + Display Order, shared by save and update. */
    private function applyScope(Features $feature, Request $request): void
    {
        $feature->applies_to = array_key_exists((string) $request->applies_to, Features::appliesToOptions())
            ? $request->applies_to
            : 'all';

        if (is_numeric($request->reorder_id) && (int) $request->reorder_id > 0) {
            $feature->reorder_id = (int) $request->reorder_id;
        }
    }
    public function save(Request $request)
    {
        if (Auth::user()->type == 4) {
            $vendor_id = Auth::user()->vendor_id;
        } else {
            $vendor_id = Auth::user()->id;
        }
        if (env('Environment') == 'sendbox') {
            return $this->sendError("This operation was not performed due to demo mode");
        }
        $validator = Validator::make($request->all(), [
            'image' => 'image|max:' . helper::imagesize() . '|' . helper::imageext(),
        ], [
            'image.max' => trans('messages.image_size_message'),
        ]);
        if ($validator->fails()) {
            return redirect()->back()->with('error', trans('messages.image_size_message') . ' ' . helper::appdata('')->image_size . ' ' . 'MB');
        }
        $features = new Features();
        $features->vendor_id = $vendor_id;
        $features->title = $request->title;
        $features->description = $request->description;
        $this->applyScope($features, $request);
        // hasFile(), not has() — an empty file input still satisfies has().
        if ($request->hasFile('image')) {
            $features->image = helper::store_upload($request->file('image'), 'admin-assets/images/feature', 'feature');
        }
        if (empty($features->reorder_id)) {
            $features->reorder_id = (int) Features::where('vendor_id', $vendor_id)->max('reorder_id') + 1;
        }
        // The image is optional; the column is NOT NULL, so store an empty string and let the
        // views fall back to a default icon.
        $features->image = $features->image ?: '';
        $features->save();
        return redirect('admin/features')->with('success', trans('messages.success'));
    }
    public function edit(Request $request)
    {
        $editfeature = Features::where('id', $request->id)->first();
        return view('admin.features.edit', compact('editfeature'));
    }
    public function update(Request $request)
    {
        if (Auth::user()->type == 4) {
            $vendor_id = Auth::user()->vendor_id;
        } else {
            $vendor_id = Auth::user()->id;
        }
        if (env('Environment') == 'sendbox') {
            return $this->sendError("This operation was not performed due to demo mode");
        }
        $validator = Validator::make($request->all(), [
            'image' => 'image|max:' . helper::imagesize() . '|' . helper::imageext(),
        ], [
            'image.max' => trans('messages.image_size_message'),
        ]);
        if ($validator->fails()) {
            return redirect()->back()->with('error', trans('messages.image_size_message') . ' ' . helper::appdata('')->image_size . ' ' . 'MB');
        }
        $editfeature = Features::where('id', $request->id)->first();
        $editfeature->vendor_id = $vendor_id;
        $editfeature->title = $request->title;
        $editfeature->description = $request->description;
        $this->applyScope($editfeature, $request);
        if ($request->hasFile('image')) {
            if ($editfeature->image && file_exists(storage_path('app/public/admin-assets/images/feature/' . $editfeature->image))) {
                unlink(storage_path('app/public/admin-assets/images/feature/' . $editfeature->image));
            }
            $editfeature->image = helper::store_upload($request->file('image'), 'admin-assets/images/feature', 'feature');
        }
        $editfeature->update();
        return redirect('admin/features')->with('success', trans('messages.success'));
    }
    public function delete(Request $request)
    {
        $feature = Features::where('id', $request->id)->first();
        if (file_exists(storage_path('app/public/admin-assets/images/feature/' . $feature->image))) {
            unlink(storage_path('app/public/admin-assets/images/feature/' . $feature->image));
        }
        $feature->delete();
        return redirect()->back()->with('success', trans('messages.success'));
    }
    public function reorder_features(Request $request)
    {
        if (Auth::user()->type == 4) {
            $vendor_id = Auth::user()->vendor_id;
        } else {
            $vendor_id = Auth::user()->id;
        }
        $getfeatures = Features::where('vendor_id', $vendor_id)->get();
        foreach ($getfeatures as $features) {
            foreach ($request->order as $order) {
                $features = Features::where('id', $order['id'])->first();
                $features->reorder_id = $order['position'];
                $features->save();
            }
        }
        return response()->json(['status' => 1, 'msg' => trans('messages.success')], 200);
    }
}
