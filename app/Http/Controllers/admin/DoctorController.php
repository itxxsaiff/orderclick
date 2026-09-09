<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Doctor;
use Illuminate\Support\Facades\Auth;

class DoctorController extends Controller
{
    private function tenantId()
    {
        return Auth::user()->type == 4 ? Auth::user()->vendor_id : Auth::user()->id;
    }

    public function index()
    {
        $doctors = Doctor::where('vendor_id', $this->tenantId())
            ->orderBy('reorder_id')->orderByDesc('id')->get();
        return view('admin.otherpages.doctors', compact('doctors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required',
            'fee'   => 'nullable|numeric',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);
        $doctor = new Doctor;
        $doctor->vendor_id = $this->tenantId();
        $this->fill($doctor, $request);
        $doctor->save();
        return redirect()->back()->with('success', trans('messages.success'));
    }

    public function update(Request $request)
    {
        $doctor = Doctor::where('id', $request->id)->where('vendor_id', $this->tenantId())->first();
        if (empty($doctor)) {
            return redirect()->back()->with('error', trans('messages.wrong'));
        }
        $request->validate([
            'name'  => 'required',
            'fee'   => 'nullable|numeric',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);
        $this->fill($doctor, $request);
        $doctor->save();
        return redirect()->back()->with('success', trans('messages.success'));
    }

    public function updatestatus(Request $request)
    {
        $doctor = Doctor::where('id', $request->id)->where('vendor_id', $this->tenantId())->first();
        if (empty($doctor)) {
            return redirect()->back()->with('error', trans('messages.wrong'));
        }
        $doctor->is_available = $doctor->is_available == 1 ? 0 : 1;
        $doctor->save();
        return redirect()->back()->with('success', trans('messages.success'));
    }

    public function destroy(Request $request)
    {
        $doctor = Doctor::where('id', $request->id)->where('vendor_id', $this->tenantId())->first();
        if (empty($doctor)) {
            return redirect()->back()->with('error', trans('messages.wrong'));
        }
        $this->deleteImage($doctor->image);
        $doctor->delete();
        return redirect()->back()->with('success', trans('messages.success'));
    }

    private function fill(Doctor $doctor, Request $request): void
    {
        $doctor->name          = $request->name;
        $doctor->specialty     = $request->specialty;
        $doctor->qualification = $request->qualification;
        $doctor->experience    = $request->experience;
        $doctor->fee           = $request->fee ?: 0;
        $doctor->languages     = $request->languages;
        $doctor->about         = $request->about;
        $doctor->reorder_id    = $request->reorder_id ?: 0;
        $doctor->is_available  = $request->has('is_available') ? 1 : 0;

        if ($request->hasFile('image')) {
            $this->deleteImage($doctor->image);
            $ext = $request->image->getClientOriginalExtension();
            $name = 'item-doctor-' . uniqid() . '.' . $ext;
            $request->image->move(env('ASSETSPATHURL') . 'item/', $name);
            $doctor->image = $name;
        }
    }

    private function deleteImage($image): void
    {
        if (!empty($image) && file_exists(env('ASSETSPATHURL') . 'item/' . $image)) {
            @unlink(env('ASSETSPATHURL') . 'item/' . $image);
        }
    }
}
