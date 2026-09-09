<?php

namespace App\Http\Controllers\admin;



use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Models\Transaction;

use App\Models\User;

use App\Models\SystemAddons;

use App\Helpers\helper;
use App\Helpers\Systems;
use App\Helpers\Subscriptions;

use Illuminate\Support\Facades\Auth;

use Config;



class TransactionController extends Controller

{
  /** System / payment-method / free-text filters, applied in SQL. */
  private function applyFilters($query, Request $request)
  {
    if (Systems::isValid($request->system)) {
      $query->where(function ($q) use ($request) {
        $q->where('system', $request->system);
        // Rows created before the system column existed count as Orders & Stores.
        if ($request->system === Systems::ORDERS) {
          $q->orWhereNull('system')->orWhere('system', '');
        }
      });
    }

    if ($request->filled('method')) {
      $query->where('payment_type', $request->method);
    }

    if ($search = trim((string) $request->get('q'))) {
      $like = '%' . $search . '%';
      $vendorIds = User::where('name', 'like', $like)->orWhere('trade_name', 'like', $like)
        ->orWhere('vendor_code', 'like', $like)->pluck('id');
      $query->where(function ($w) use ($like, $vendorIds) {
        $w->where('transaction_number', 'like', $like)
          ->orWhere('plan_name', 'like', $like)
          ->orWhere('payment_id', 'like', $like)
          ->orWhereIn('vendor_id', $vendorIds);
      });
    }

    return $query;
  }

  /**
   * Payment status is partly derived (bank transfer under review, expired term), so it is
   * filtered on the resolved value rather than on the raw status column.
   */
  private function filterByStatus($rows, Request $request)
  {
    if (!$request->filled('status')) {
      return $rows;
    }

    return $rows->filter(fn($t) => Subscriptions::paymentStatus($t)['key'] === $request->status)->values();
  }


  public function index(Request $request)
  {

    if (Auth::user()->type == 4) {
      $vendor_id = Auth::user()->vendor_id;
    } else {
      $vendor_id = Auth::user()->id;
    }

    if (SystemAddons::where('unique_identifier', 'subscription')->first() == null) {

      return redirect()->back()->with(['error' => 'You can not charge your end customers in regular license. Please purchase extended license to charge your end customers']);
    } else {

      $vendors = User::where('type', 2)->get();

      if (Auth::user()->type == 1 || (Auth::user()->type == 4 && Auth::user()->vendor_id == 1)) {

        $transaction = Transaction::with(['vendor_info', 'plan_info'])->where('transaction_type', null)->orderByDesc('id');

        if (!empty($request->vendor)) {

          $transaction = $transaction->where('vendor_id', $request->vendor);
        }

        if (!empty($request->startdate) && !empty($request->enddate)) {

          $transaction =  $transaction->whereBetween('purchase_date', [$request->startdate, $request->enddate]);
        }

        $transaction = $this->applyFilters($transaction, $request)->get();
        $transaction = $this->filterByStatus($transaction, $request);
      }
      if (Auth::user()->type == 2 || (Auth::user()->type == 4 && Auth::user()->vendor_id != 1)) {

        $transaction = Transaction::with("plan_info")->where('vendor_id', $vendor_id)->where('transaction_type', null)->orderByDesc('id');

        if (!empty($request->startdate) && !empty($request->enddate)) {

          $transaction =  $transaction->whereBetween('purchase_date', [$request->startdate, $request->enddate]);
        }

        $transaction = $this->applyFilters($transaction, $request)->get();
        $transaction = $this->filterByStatus($transaction, $request);
      }

      return view('admin.transaction.transaction', compact('transaction', 'vendors'));
    }
  }
  public function status(Request $request)
  {
    $transaction = Transaction::find($request->id);
    if (!empty($transaction)) {
      $vendor = User::find($transaction->vendor_id);
      if ($request->status == 2) {
        $transaction->purchase_date = date("Y-m-d h:i:sa");
        // V2 rule 7: only an already-live store gets an expiry now. A first purchase stays open
        // until the merchant activates their website (Systems::activateWebsite).
        $transaction->expire_date = \App\Helpers\Systems::isLive($vendor)
          ? (helper::get_plan_exp_date($transaction->duration, $transaction->days) ?: null)
          : null;
      }
      $transaction->status = $request->status;
      $transaction->save();
      if ($request->status == 2) {
        \App\Helpers\Systems::markPaid($transaction->vendor_id, $transaction->system);
      }
      $vendorinfo = User::where('id', $transaction->vendor_id)->first();
      if ($request->status == 2) {
        $emaildata = helper::emailconfigration(helper::appdata('')->id);
        Config::set('mail', $emaildata);
        helper::send_subscription_email($vendorinfo->email, $vendorinfo->name, $transaction->plan_name, helper::get_plan_exp_date('', $transaction->days), helper::currency_formate($transaction->amount, ""), helper::getpayment($transaction->payment_type, 1)->payment_name, "-");
      } else {
        $emaildata = helper::emailconfigration(helper::appdata('')->id);
        Config::set('mail', $emaildata);
        helper::subscription_rejected($vendorinfo->email, $vendorinfo->name, $transaction->plan_name, helper::getpayment($transaction->payment_type, 1)->payment_name);
      }
      return redirect('admin/transaction')->with('success', trans('messages.success'));
    }
    abort(404);
  }
}
