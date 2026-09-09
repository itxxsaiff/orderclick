<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\helper;
use App\Helpers\whatsapp_helper;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\CustomStatus;
use App\Models\Item;
use App\Models\SystemAddons;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Variants;
use Illuminate\Support\Facades\Auth;
use Config;
use PDF;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::user()->type == 4) {
            $vendor_id = Auth::user()->vendor_id;
        } else {
            $vendor_id = Auth::user()->id;
        }
        $getorders = Order::where('vendor_id', $vendor_id);
        if ($request->has('status') && $request->status != "") {
            if ($request->status == "processing") {
                $getorders = $getorders->whereIn('status_type', array(1, 2));
            }
            if ($request->status == "cancelled") {
                $getorders = $getorders->where('status_type', 4);
            }

            if ($request->status == "delivered") {
                $getorders = $getorders->where('status_type', 3);
            }
        }
        $totalorders = Order::where('vendor_id', $vendor_id)->count();
        $totalprocessing = Order::whereIn('status_type', array(1, 2))->where('vendor_id', $vendor_id)->count();
        $totalrevenue = Order::where('vendor_id', $vendor_id)->where('status_type', 3)->where('payment_status', 2)->sum('grand_total');
        $totalcompleted = Order::where('status_type', 3)->where('vendor_id', $vendor_id)->count();
        $totalcancelled = Order::where('status_type', 4)->where('vendor_id', $vendor_id)->count();
        if (!empty($request->customer_id) && !empty($request->startdate) && !empty($request->enddate)) {
            $totalorders = Order::where('vendor_id', $vendor_id)->whereBetween('created_at', [$request->startdate, $request->enddate])->where('user_id', $request->customer_id)->count();
            $getorders = $getorders->whereBetween('created_at', [$request->startdate, $request->enddate])->where('user_id', $request->customer_id);
            $totalprocessing = Order::whereIn('status_type', array(1, 2))->where('vendor_id', $vendor_id)->whereBetween('created_at', [$request->startdate, $request->enddate])->where('user_id', $request->customer_id)->count();
            $totalrevenue = Order::where('status_type', 3)->where('vendor_id', $vendor_id)->where('payment_status', 2)->whereBetween('created_at', [$request->startdate, $request->enddate])->where('user_id', $request->customer_id)->sum('grand_total');
            $totalcompleted = Order::where('status_type', 3)->where('vendor_id', $vendor_id)->whereBetween('created_at', [$request->startdate, $request->enddate])->where('user_id', $request->customer_id)->count();
            $totalcancelled = Order::where('status_type', 4)->where('vendor_id', $vendor_id)->whereBetween('created_at', [$request->startdate, $request->enddate])->where('user_id', $request->customer_id)->count();
        } elseif (!empty($request->startdate) && !empty($request->enddate)) {
            $totalorders = Order::where('vendor_id', $vendor_id)->whereBetween('created_at', [$request->startdate, $request->enddate])->count();
            $getorders = $getorders->whereBetween('created_at', [$request->startdate, $request->enddate]);
            $totalprocessing = Order::whereIn('status_type', array(1, 2))->where('vendor_id', $vendor_id)->whereBetween('created_at', [$request->startdate, $request->enddate])->count();
            $totalrevenue = Order::where('status_type', 3)->where('vendor_id', $vendor_id)->where('payment_status', 2)->whereBetween('created_at', [$request->startdate, $request->enddate])->sum('grand_total');
            $totalcompleted = Order::where('status_type', 3)->where('vendor_id', $vendor_id)->whereBetween('created_at', [$request->startdate, $request->enddate])->count();
            $totalcancelled = Order::where('status_type', 4)->where('vendor_id', $vendor_id)->whereBetween('created_at', [$request->startdate, $request->enddate])->count();
        }
        $getorders = $getorders->orderByDesc('id')->get();
        $getcustomerslist = User::where('type', 3)->where('is_deleted', 2)->where('vendor_id', $vendor_id)->orderByDesc('id')->get();
        return view('admin.orders.index', compact('getorders', 'totalorders', 'totalprocessing', 'totalcompleted', 'totalcancelled', 'totalrevenue', 'getcustomerslist'));
    }
    public function update(Request $request)
    {
        try {
            if (Auth::user()->type == 4) {
                $vendor_id = Auth::user()->vendor_id;
            } else {
                $vendor_id = Auth::user()->id;
            }
            $orderdata = Order::where('id', $request->id)->where('vendor_id', $vendor_id)->first();
            $orderdetail = OrderDetails::where('order_id', $orderdata->id)->get();
            if (empty($orderdata) || !in_array($request->type, [2, 3, 4])) {
                abort(404);
            }
            $title = "";
            $message_text = "";
            if ($request->type == "2") {
                $title = @helper::gettype($request->status, $request->type, $orderdata->order_type, $orderdata->vendor_id)->name;
                $message_text = 'Your Order ' . $orderdata->order_number . ' has been ' . $title . ' by Admin';
            }
            if ($request->type == "3") {
                $title = @helper::gettype($request->status, $request->type, $orderdata->order_type, $orderdata->vendor_id)->name;
                $message_text = 'Your Order ' . $orderdata->order_number . ' has been successfully delivered.';
            }
            if ($request->type == "4") {
                if ($orderdata->payment_type == 16) {
                    $walletuser = User::where('id', $orderdata->user_id)->first();
                    $walletuser->wallet += $orderdata->grand_total;
                    $walletuser->save();

                    $transaction = new Transaction();
                    $transaction->vendor_id = $orderdata->vendor_id;
                    $transaction->user_id = $orderdata->user_id;
                    $transaction->order_id = $orderdata->id;
                    $transaction->transaction_type = 3;
                    $transaction->amount = $orderdata->grand_total;
                    $transaction->order_number = $orderdata->order_number;
                    $transaction->save();
                }
                $title = @helper::gettype($request->status, $request->type, $orderdata->order_type, $orderdata->vendor_id)->name;
                $message_text = 'Order ' . $orderdata->order_number . ' has been cancelled by Admin.';
            }
            $vendor = User::select('id', 'name')->where('id', $orderdata->vendor_id)->first();

            $defaultsatus = CustomStatus::where('vendor_id', $orderdata->vendor_id)->where('order_type', $orderdata->order_type)->where('type', $request->type)->where('id', $request->status)->where('is_available', 1)->where('is_deleted', 2)->first();

            if (empty($defaultsatus) && $defaultsatus == null) {
                return redirect()->back()->with('error', trans('messages.wrong'));
            } else {
                $emaildata = helper::emailconfigration($orderdata->vendor_id);
                Config::set('mail', $emaildata);
                helper::order_status_email($orderdata->customer_email, $orderdata->customer_name, $title, $message_text, $vendor);
                if ($orderdata->payment_type == 6 && $request->type == 3) {
                    $orderdata->payment_status = 2;
                }
                if (SystemAddons::where('unique_identifier', 'whatsapp_message')->first() != null && SystemAddons::where('unique_identifier', 'whatsapp_message')->first()->activated == 1) {
                    if (@whatsapp_helper::whatsapp_message_config($orderdata->vendor_id)->status_change == 1) {
                        whatsapp_helper::orderstatusupdatemessage($orderdata->order_number, $message_text, $orderdata->vendor_id);
                    }
                }

                $orderdata->status = $defaultsatus->id;
                $orderdata->status_type = $defaultsatus->type;
                $orderdata->save();

                if ($request->type == "4") {
                    foreach ($orderdetail as $order) {
                        if ($order->variants_id != null && $order->variants_id != "") {
                            $item = Variants::where('id', $order->variants_id)->where('item_id', $order->item_id)->first();
                        } else {
                            $item = Item::where('id', $order->item_id)->where('vendor_id', $orderdata->vendor_id)->first();
                        }
                        $item->qty = $item->qty + $order->qty;
                        $item->update();
                    }
                }
                return redirect()->back()->with('success', trans('messages.success'));
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', trans('messages.wrong'));
        }
    }
    public function invoice(Request $request)
    {
        if (Auth::user()->type == 4) {
            $vendor_id = Auth::user()->vendor_id;
        } else {
            $vendor_id = Auth::user()->id;
        }
        $getorderdata = Order::with('tableqr')->where('order_number', $request->order_number)->where('vendor_id', $vendor_id)->first();
        if (empty($getorderdata)) {
            abort(404);
        }
        $ordersdetails = OrderDetails::where('order_id', $getorderdata->id)->get();
        return view('admin.orders.invoice', compact('getorderdata', 'ordersdetails'));
    }
    public function print(Request $request)
    {
        if (Auth::user()->type == 4) {
            $vendor_id = Auth::user()->vendor_id;
        } else {
            $vendor_id = Auth::user()->id;
        }
        $getorderdata = Order::where('order_number', $request->order_number)->where('vendor_id', $vendor_id)->first();

        if (empty($getorderdata)) {
            abort(404);
        }
        $ordersdetails = OrderDetails::where('order_id', $getorderdata->id)->get();
        return view('admin.orders.print', compact('getorderdata', 'ordersdetails'));
    }
    public function customerinfo(Request $request)
    {
        if (Auth::user()->type == 4) {
            $vendor_id = Auth::user()->vendor_id;
        } else {
            $vendor_id = Auth::user()->id;
        }

        $customerinfo = Order::where('order_number', $request->order_id)->where('vendor_id', $vendor_id)->first();

        if ($request->edit_type == "customer_info") {
            $customerinfo->customer_name = $request->customer_name;
            $customerinfo->mobile = $request->customer_mobile;
            $customerinfo->customer_email = $request->customer_email;
        }
        if ($request->edit_type == "delivery_info") {
            $customerinfo->address = $request->customer_address;
            $customerinfo->building = $request->customer_building;
            $customerinfo->landmark = $request->customer_landmark;
            $customerinfo->pincode = $request->customer_pincode;
        }
        $customerinfo->update();
        return redirect()->back()->with('success', trans('messages.success'));
    }

    public function vendor_note(Request $request)
    {

        if (Auth::user()->type == 4) {
            $vendor_id = Auth::user()->vendor_id;
        } else {
            $vendor_id = Auth::user()->id;
        }

        $updatenote = Order::where('order_number', $request->order_id)->where('vendor_id', $vendor_id)->first();

        $updatenote->vendor_note = $request->vendor_note;
        $updatenote->update();
        return redirect()->back()->with('success', trans('messages.success'));
    }
    public function payment_status(Request $request)
    {
        if ($request->ramin_amount > 0) {
            return redirect()->back()->with('error', trans('messages.amount_validation_msg'));
        }

        if (Auth::user()->type == 4) {
            $vendor_id = Auth::user()->vendor_id;
        } else {
            $vendor_id = Auth::user()->id;
        }
        $order = Order::where('order_number', $request->order_number)->where('vendor_id', $vendor_id)->first();
        if ($order->order_type == 4) {
            $order->payment_type = $request->payment_type;
        }
        $order->payment_status = 2;
        $order->update();
        return redirect()->back()->with('success', trans('messages.success'));
    }
    public function generatepdf(Request $request)
    {
        if (Auth::user()->type == 4) {
            $vendor_id = Auth::user()->vendor_id;
        } else {
            $vendor_id = Auth::user()->id;
        }
        $getorderdata = Order::where('order_number', $request->order_number)->where('vendor_id', $vendor_id)->first();
        $ordersdetails = OrderDetails::where('order_id', $getorderdata->id)->get();
        $pdf = PDF::loadView('admin.orders.invoicepdf', ['getorderdata' => $getorderdata, 'ordersdetails' => $ordersdetails]);
        return $pdf->download('orderinvoice.pdf');
    }
}
