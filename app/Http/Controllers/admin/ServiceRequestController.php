<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceRequest;
use Illuminate\Support\Facades\Auth;

class ServiceRequestController extends Controller
{
    private function tenantId()
    {
        return Auth::user()->type == 4 ? Auth::user()->vendor_id : Auth::user()->id;
    }

    public function index()
    {
        $vendor_id = $this->tenantId();
        $requests = ServiceRequest::where('vendor_id', $vendor_id)->orderByDesc('id')->get();
        // mark new requests as read
        ServiceRequest::where('vendor_id', $vendor_id)->where('is_notification', 1)->update(['is_notification' => 2]);
        return view('admin.servicerequest.index', compact('requests'));
    }

    public function updatestatus(Request $request)
    {
        $sr = ServiceRequest::where('id', $request->id)->where('vendor_id', $this->tenantId())->first();
        if (empty($sr)) {
            return redirect()->back()->with('error', trans('messages.wrong'));
        }
        $sr->status = $request->status;
        $sr->save();
        return redirect()->back()->with('success', trans('messages.success'));
    }
}
