<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Settings;
use App\Models\Works;
use App\Helpers\helper;
use Illuminate\Support\Facades\Auth;

class WorksController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::user()->type == 4) {
            $vendor_id = Auth::user()->vendor_id;
        } else {
            $vendor_id = Auth::user()->id;
        }

        $content = Settings::where('vendor_id', $vendor_id)->first();
        $allworkcontent = Works::where('vendor_id', $vendor_id)->orderBy('reorder_id')->get();
        return view('admin.how_work.index', compact('content', 'allworkcontent'));
    }
    public function savecontent(Request $request)
    {
        if (Auth::user()->type == 4) {
            $vendor_id = Auth::user()->vendor_id;
        } else {
            $vendor_id = Auth::user()->id;
        }

        $newcontent = Settings::where('vendor_id', $vendor_id)->first();
        $newcontent->work_title = $request->title;
        $newcontent->work_subtitle = $request->subtitle;
        // Arabic is optional — a blank value makes the landing page fall back to the English one.
        $newcontent->work_title_ar = $request->title_ar;
        $newcontent->work_subtitle_ar = $request->subtitle_ar;
        $newcontent->save();
        return redirect('admin/how_works')->with('success', trans('messages.success'));
    }
    public function add()
    {
        $vendor_id = Auth::user()->type == 4 ? Auth::user()->vendor_id : Auth::user()->id;
        // Default Display Order = next in sequence, so steps stay 1-2-3 without thinking about it.
        $nextOrder = (int) Works::where('vendor_id', $vendor_id)->max('reorder_id') + 1;

        return view('admin.how_work.add', compact('nextOrder'));
    }
    public function save(Request $request)
    {
        if (Auth::user()->type == 4) {
            $vendor_id = Auth::user()->vendor_id;
        } else {
            $vendor_id = Auth::user()->id;
        }

        $newwork = new Works();
        $newwork->vendor_id = $vendor_id;
        $newwork->title = $request->title;
        $newwork->sub_title = $request->subtitle;
        $newwork->title_ar = $request->title_ar;
        $newwork->sub_title_ar = $request->subtitle_ar;
        // Display Order keeps the steps in their 1-2-3 sequence; default to the end of the list.
        $newwork->reorder_id = is_numeric($request->reorder_id) && (int) $request->reorder_id > 0
            ? (int) $request->reorder_id
            : (int) Works::where('vendor_id', $vendor_id)->max('reorder_id') + 1;
        // hasFile(), not has() — an empty file input still satisfies has().
        if ($request->hasFile('image')) {
            $newwork->image = helper::store_upload($request->file('image'), 'landing/images/png', 'work');
        }
        $newwork->save();
        return redirect('admin/how_works')->with('success', trans('messages.success'));
    }
    public function edit(Request $request)
    {
        if (Auth::user()->type == 4) {
            $vendor_id = Auth::user()->vendor_id;
        } else {
            $vendor_id = Auth::user()->id;
        }

        $editwork = Works::where('id', $request->id)->where('vendor_id', $vendor_id)->first();
        return view('admin.how_work.edit', compact('editwork'));
    }
    public function update(Request $request)
    {
        if (Auth::user()->type == 4) {
            $vendor_id = Auth::user()->vendor_id;
        } else {
            $vendor_id = Auth::user()->id;
        }

        $editwork = Works::where('id', $request->id)->where('vendor_id', $vendor_id)->first();
        $editwork->title = $request->title;
        $editwork->sub_title = $request->subtitle;
        $editwork->title_ar = $request->title_ar;
        $editwork->sub_title_ar = $request->subtitle_ar;
        if (is_numeric($request->reorder_id) && (int) $request->reorder_id > 0) {
            $editwork->reorder_id = (int) $request->reorder_id;
        }
        if ($request->hasFile('image')) {
            if ($editwork->image && file_exists(storage_path('app/public/landing/images/png/' . $editwork->image))) {
                unlink(storage_path('app/public/landing/images/png/' . $editwork->image));
            }
            $editwork->image = helper::store_upload($request->file('image'), 'landing/images/png', 'work');
        }
        $editwork->update();
        return redirect('admin/how_works')->with('success', trans('messages.success'));
    }
    public function delete(Request $request)
    {
        if (Auth::user()->type == 4) {
            $vendor_id = Auth::user()->vendor_id;
        } else {
            $vendor_id = Auth::user()->id;
        }

        $deletework = Works::where('id', $request->id)->where('vendor_id', $vendor_id)->first();
        if ($deletework->image && file_exists(storage_path('app/public/landing/images/png/' . $deletework->image))) {
            unlink(storage_path('app/public/landing/images/png/' . $deletework->image));
        }
        $deletework->delete();
        return redirect('admin/how_works')->with('success', trans('messages.success'));
    }

    public function reorder_status(Request $request)
    {
        if (Auth::user()->type == 4) {
            $vendor_id = Auth::user()->vendor_id;
        } else {
            $vendor_id = Auth::user()->id;
        }

        $getstatus = Works::where('vendor_id', $vendor_id)->get();
        foreach ($getstatus as $status) {
            foreach ($request->order as $order) {
                $status = Works::where('id', $order['id'])->first();
                $status->reorder_id = $order['position'];
                $status->save();
            }
        }
        return response()->json(['status' => 1, 'msg' => trans('messages.success')], 200);
    }
}
