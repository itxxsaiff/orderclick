<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BookingService;
use Illuminate\Support\Facades\Auth;

class BookingServiceController extends Controller
{
    private function tenantId()
    {
        return Auth::user()->type == 4 ? Auth::user()->vendor_id : Auth::user()->id;
    }

    public function index()
    {
        $services = BookingService::where('vendor_id', $this->tenantId())
            ->orderBy('reorder_id')->orderByDesc('id')->get();
        return view('admin.booking.services', compact('services'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required',
            'price' => 'nullable|numeric',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $service = new BookingService;
        $service->vendor_id = $this->tenantId();
        $this->fill($service, $request);
        $service->save();

        return redirect()->back()->with('success', trans('messages.success'));
    }

    public function update(Request $request)
    {
        $service = BookingService::where('id', $request->id)->where('vendor_id', $this->tenantId())->first();
        if (empty($service)) {
            return redirect()->back()->with('error', trans('messages.wrong'));
        }
        $request->validate([
            'name'  => 'required',
            'price' => 'nullable|numeric',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);
        $this->fill($service, $request);
        $service->save();

        return redirect()->back()->with('success', trans('messages.success'));
    }

    public function updatestatus(Request $request)
    {
        $service = BookingService::where('id', $request->id)->where('vendor_id', $this->tenantId())->first();
        if (empty($service)) {
            return redirect()->back()->with('error', trans('messages.wrong'));
        }
        $service->is_available = $service->is_available == 1 ? 0 : 1;
        $service->save();
        return redirect()->back()->with('success', trans('messages.success'));
    }

    public function destroy(Request $request)
    {
        $service = BookingService::where('id', $request->id)->where('vendor_id', $this->tenantId())->first();
        if (empty($service)) {
            return redirect()->back()->with('error', trans('messages.wrong'));
        }
        $this->deleteImage($service->image);
        $service->delete();
        return redirect()->back()->with('success', trans('messages.success'));
    }

    private function fill(BookingService $service, Request $request): void
    {
        $service->name        = $request->name;
        $service->category    = $request->category;
        $service->price       = $request->price ?: 0;
        $service->duration    = $request->duration;
        $service->description = $request->description;
        $service->reorder_id  = $request->reorder_id ?: 0;
        $service->is_available = $request->has('is_available') ? 1 : 0;

        if ($request->hasFile('image')) {
            $this->deleteImage($service->image);
            $ext = $request->image->getClientOriginalExtension();
            $name = 'item-svc-' . uniqid() . '.' . $ext;
            $request->image->move(env('ASSETSPATHURL') . 'item/', $name);
            $service->image = $name;
        }
    }

    private function deleteImage($image): void
    {
        if (!empty($image) && file_exists(env('ASSETSPATHURL') . 'item/' . $image)) {
            @unlink(env('ASSETSPATHURL') . 'item/' . $image);
        }
    }
}
