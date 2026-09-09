<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    private function tenantId()
    {
        return Auth::user()->type == 4 ? Auth::user()->vendor_id : Auth::user()->id;
    }

    public function index()
    {
        $vendor_id = $this->tenantId();
        $bookings = Booking::where('vendor_id', $vendor_id)->orderByDesc('id')->get();
        // mark new bookings as read
        Booking::where('vendor_id', $vendor_id)->where('is_notification', 1)->update(['is_notification' => 2]);
        return view('admin.booking.index', compact('bookings'));
    }

    public function updatestatus(Request $request)
    {
        $booking = Booking::where('id', $request->id)->where('vendor_id', $this->tenantId())->first();
        if (empty($booking)) {
            return redirect()->back()->with('error', trans('messages.wrong'));
        }
        $booking->status = $request->status;
        $booking->save();
        return redirect()->back()->with('success', trans('messages.success'));
    }
}
