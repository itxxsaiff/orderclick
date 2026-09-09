<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;

use App\Models\Item;
use App\Helpers\helper;

use Illuminate\Http\Request;

use App\Models\StoreCategory;
use App\Models\Activity;
use App\Helpers\Systems;

use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class StoreCategoryController extends Controller

{

    public function index(Request $request)

    {

        if(Auth::user()->type == 4)

        {

            $vendor_id = Auth::user()->vendor_id;

        }else{

            $vendor_id = Auth::user()->id;

        }

        $tab = $request->get('tab', 'all');

        $query = StoreCategory::where('is_deleted', 2);

        if (Systems::isValid($tab)) {
            $query->where(function ($q) use ($tab) {
                $q->where('system', $tab);
                // Categories created before the system column existed are Orders & Stores.
                if ($tab === Systems::ORDERS) {
                    $q->orWhereNull('system')->orWhere('system', '');
                }
            });
        }

        if ($search = trim((string) $request->get('q'))) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        $allcategories = $query->orderBy('reorder_id')->get();

        // Counts for the filter pills.
        $tabCounts = [];
        foreach (array_merge(['all'], Systems::keys()) as $key) {
            $c = StoreCategory::where('is_deleted', 2);
            if ($key !== 'all') {
                $c->where(function ($q) use ($key) {
                    $q->where('system', $key);
                    if ($key === Systems::ORDERS) $q->orWhereNull('system')->orWhere('system', '');
                });
            }
            $tabCounts[$key] = $c->count();
        }

        return view('admin.store_categories.index', compact("allcategories", "tab", "tabCounts"));

    }

    public function add_category(Request $request)

    {

        return view('admin.store_categories.add', ['activities' => Activity::orderBy('reorder_id')->get()]);

    }


    /**
     * System + the activity this category represents. The activity link is what lets registration
     * assign a category automatically, so it is validated against the chosen system.
     */
    private function applyScope(StoreCategory $category, Request $request): void
    {
        $category->system = Systems::normalise($request->system);
        $category->is_other = (int) $request->is_other === 1 ? 1 : 2;

        $activityId = $request->activity_id ?: null;
        $category->activity_id = ($activityId && Systems::activityBelongsTo($activityId, $category->system))
            ? $activityId
            : null;
    }

    public function save_category(Request $request)

    {
        $validator = Validator::make($request->all(), [
            'category_image' => 'nullable|image|max:' . helper::imagesize() . '|' . helper::imageext(),
        ], [
            'category_image.max' => trans('messages.image_size_message'),
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', trans('messages.image_size_message') . ' ' . helper::appdata('')->image_size . ' ' . 'MB')->withInput();
        }

        $savecategory = new StoreCategory();

        $savecategory->name = $request->category_name;

        $this->applyScope($savecategory, $request);

        if ($request->hasFile('category_image')) {
            $image = 'category-' . uniqid() . '.' . $request->category_image->getClientOriginalExtension();
            $request->file('category_image')->move(storage_path('app/public/admin-assets/images/category/'), $image);
            $savecategory->image = $image;
        }

        $savecategory->save();

        return redirect('admin/store_categories/')->with('success', trans('messages.success'));

    }

    public function edit_category(Request $request)

    {

        $editcategory = StoreCategory::where('id', $request->id)->first();

        $activities = Activity::orderBy('reorder_id')->get();

        return view('admin.store_categories.edit', compact("editcategory", "activities"));

    }

    public function update_category(Request $request)

    {
        $validator = Validator::make($request->all(), [
            'category_image' => 'nullable|image|max:' . helper::imagesize() . '|' . helper::imageext(),
        ], [
            'category_image.max' => trans('messages.image_size_message'),
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', trans('messages.image_size_message') . ' ' . helper::appdata('')->image_size . ' ' . 'MB')->withInput();
        }

        $editcategory = StoreCategory::where('id', $request->id)->first();

        $editcategory->name = $request->category_name;

        $this->applyScope($editcategory, $request);

        if ($request->hasFile('category_image')) {
            if (!empty($editcategory->image) && file_exists(storage_path('app/public/admin-assets/images/category/' . $editcategory->image))) {
                unlink(storage_path('app/public/admin-assets/images/category/' . $editcategory->image));
            }

            $image = 'category-' . uniqid() . '.' . $request->category_image->getClientOriginalExtension();
            $request->file('category_image')->move(storage_path('app/public/admin-assets/images/category/'), $image);
            $editcategory->image = $image;
        }

        $editcategory->update();

        return redirect('admin/store_categories')->with('success', trans('messages.success'));

    }

    public function change_status(Request $request)

    {

        StoreCategory::where('id', $request->id)->update(['is_available' => $request->status]);

        return redirect('admin/store_categories')->with('success', trans('messages.success'));

    }

    public function delete_category(Request $request)

    {

        $checkcategory = StoreCategory::where('id', $request->id)->first();

        if (!empty($checkcategory)) {
            if (!empty($checkcategory->image) && file_exists(storage_path('app/public/admin-assets/images/category/' . $checkcategory->image))) {
                unlink(storage_path('app/public/admin-assets/images/category/' . $checkcategory->image));
            }

            $checkcategory->is_deleted = 1;

            $checkcategory->save();

            return redirect('admin/store_categories')->with('success', trans('messages.success'));

        } else {

            return redirect()->back()->with('error', trans('messages.wrong'));

        }

    }

    public function reorder_category(Request $request)

    {

       

        $getcategory = StoreCategory::get();

        foreach ($getcategory as $category) {

            foreach ($request->order as $order) {

               $category = StoreCategory::where('id',$order['id'])->first();

               $category->reorder_id = $order['position'];

               $category->save();

            }

        }

        return response()->json(['status' => 1,'msg' => trans('messages.success')], 200);

    }

}
