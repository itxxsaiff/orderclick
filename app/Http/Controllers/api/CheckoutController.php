<?php
   
namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\api\BaseController as BaseController;
use App\Models\Coupons;
use App\Models\DeliveryArea;
use App\Models\Timing;
use App\Models\SystemAddons;
use App\Models\Payment;
use App\Models\TableQR;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\User;
use App\Http\Resources\Coupons as CouponsResource;
use App\Http\Resources\DeliveryArea as DeliveryAreaResource;
use App\Http\Resources\Payment as PaymentResource;
use App\Http\Resources\Table as TableResource;
use App\Http\Resources\Order as OrderResource;
use App\Http\Resources\OrderDetails as OrderDetailsResource;
use App\Helpers\helper;
use App\Helpers\whatsapp_helper;
use Carbon\Carbon;
use DateTime;
use DateInterval;

class CheckoutController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getcoupons(Request $request)
    {
        if ($request->vendor_id == "") {
            return $this->sendError(trans('messages.vendor_id_required'));
        }

        $dt = date('Y-m-d');
    
        $coupons = Coupons::where('active_from', '<=', $dt)
                ->where('active_to', '>=', $dt)->
                where('vendor_id', $request->vendor_id)->orderBy('id', 'ASC')->get();

        return $this->sendResponse(CouponsResource::collection($coupons), trans('messages.success'));
    }

    public function deliveryarea(Request $request)
    {
        if ($request->vendor_id == "") {
            return $this->sendError(trans('messages.vendor_id_required'));
        }
    
        $deliveryarea = DeliveryArea::where('vendor_id', $request->vendor_id)->get();

        return $this->sendResponse(DeliveryAreaResource::collection($deliveryarea), trans('messages.success'));
    }

    public function timeslot(Request $request)
    {
        try {
            if ($request->vendor_id == "") {
                return $this->sendError(trans('messages.vendor_id_required'));
            }

            $timezone = helper::appdata($request->vendor_id);
         
            $slots = [];
            date_default_timezone_set($timezone->timezone);
            
            if ($request->date != "" || $request->date != null) {
                $day = date('l', strtotime($request->date));
                
                $minute = "";
                $time = Timing::where('vendor_id', $request->vendor_id)->where('day', $day)->first();
                if ($time->is_always_close == 1) {
                    $slots = "1";
                } else {
                    if (helper::appdata($request->vendor_id)->interval_type == 2) {
                        $minute = (float)helper::appdata($request->vendor_id)->interval_time * 60;
                    }
                    if (helper::appdata($request->vendor_id)->interval_type == 1) {
                        $minute = helper::appdata($request->vendor_id)->interval_time;
                    }
                    $duration = $minute;
                    $cleanup = 0;
                    $start = $time->open_time;
                    $break_start = $time->break_start; // break start
                    $break_end   = $time->break_end; // break end
                    $end = $time->close_time;
                    $firsthalf = self::firsthalf($duration, $cleanup, $start, $break_start);
                    $secondhalf = self::secondhalf($duration, $cleanup, $break_end, $end);
                    $period = array_merge($firsthalf, $secondhalf);
                    $currenttime = Carbon::now()->format('h:i a');
                    $current_date = Carbon::now()->format('Y-m-d');
                    
                    foreach ($period as $item) {
                        if ($request->date == $current_date) {
                            $slottime = explode('-', $item);
                            if (strtotime($slottime[0]) <= strtotime($currenttime)) {
                                $status = "";
                            } else {
                                $status = "active";
                            }
                        } else {
                            $status = "active";
                        }
                        $slots[] = array(
                            'slot' =>  $item,
                            'status' => $status,
                        );
                    }
                }
            }
            return $this->sendResponse($slots, trans('messages.success'));
        } catch (\Throwable $th) {
            return $this->sendError(trans('messages.wrong'));
        }
    }
    function firsthalf($duration, $cleanup, $start, $break_start)
    {
        $start = new DateTime($start);
        $break_start  = new DateTime($break_start);
        $interval = new DateInterval('PT' . $duration . 'M');
        $cleanupinterval = new DateInterval('PT' . $cleanup . 'M');
        $slots = array();
        for ($intStart = $start; $intStart < $break_start; $intStart->add($interval)->add($cleanupinterval)) {
            $endperiod = clone $intStart;
            $endperiod->add($interval);
            if (strtotime($break_start->format('h:i A')) < strtotime($endperiod->format('h:i A')) && strtotime($endperiod->format('h:i A')) < strtotime($break_start->format('h:i A'))) {
                $endperiod = $break_start;
                $slots[] = $intStart->format('h:i A') . ' - ' . $endperiod->format('h:i A');
                $intStart = $break_start;
                $endperiod = $break_start;
                $intStart->sub($interval);
            }
            $slots[] = $intStart->format('h:i A') . ' - ' . $endperiod->format('h:i A');
        }
        return $slots;
    }
    function secondhalf($duration, $cleanup, $break_end, $end)
    {
        $break_end = new DateTime($break_end);
        $end  = new DateTime($end);
        $interval = new DateInterval('PT' . $duration . 'M');
        $cleanupinterval = new DateInterval('PT' . $cleanup . 'M');
        $slots = array();
        for ($intStart = $break_end; $intStart < $end; $intStart->add($interval)->add($cleanupinterval)) {
            $endperiod = clone $intStart;
            $endperiod->add($interval);
            if (strtotime($end->format('h:i A')) < strtotime($endperiod->format('h:i A')) && strtotime($endperiod->format('h:i A')) < strtotime($break_end->format('h:i A'))) {
                $endperiod = $end;
                $slots[] = $intStart->format('h:i A') . ' - ' . $endperiod->format('h:i A');
                $intStart = $end;
                $endperiod = $end;
                $intStart->sub($interval);
            }
            $slots[] = $intStart->format('h:i A') . ' - ' . $endperiod->format('h:i A');
        }
        return $slots;
    }

    public function paymentlist(Request $request)
    {
        if ($request->vendor_id == "") {
            return $this->sendError(trans('messages.vendor_id_required'));
        }
    
        $paymentlist = Payment::where('is_available', '1')
            ->where('vendor_id', $request->vendor_id)
            ->where('is_activate', 1)
            ->get()
            ->filter(function ($payment) {
                if ($payment->isIncludedGateway()) {
                    return true;
                }

                return SystemAddons::where('unique_identifier', $payment->unique_identifier)
                    ->where('activated', 1)
                    ->exists();
            })
            ->values();
        
        return $this->sendResponse(PaymentResource::collection($paymentlist), trans('messages.success'));
    }

    public function tablelist(Request $request)
    {
        if ($request->vendor_id == "") {
            return $this->sendError(trans('messages.vendor_id_required'));
        }
    
        $tableqrs = TableQR::where('vendor_id', $request->vendor_id)->orderBy('id', 'ASC')->get();
        
        return $this->sendResponse(TableResource::collection($tableqrs), trans('messages.success'));
    }

    public function placeorder(Request $request)
    {
        if ($request->vendor_id == "") {
            return $this->sendError(trans('messages.vendor_id_required'));
        }
        if ($request->order_type == "") {
            return response()->json(["status" => 0, "message" => trans('messages.order_type_required')], 400);
        }
        if ($request->order_type == 1) {
            if ($request->delivery_date == "") {
                return response()->json(["status" => 0, "message" => trans('messages.delivery_date_required')], 400);
            }
            if ($request->delivery_time == "") {
                return response()->json(["status" => 0, "message" => trans('messages.delivery_time_required')], 400);
            }
            if ($request->delivery_area == "") {
                return response()->json(["status" => 0, "message" => trans('messages.delivery_area_required')], 400);
            }
            if ($request->delivery_charge == "") {
                return response()->json(["status" => 0, "message" => trans('messages.delivery_charge_required')], 400);
            }
            if ($request->address == "") {
                return response()->json(["status" => 0, "message" => trans('messages.address_required')], 400);
            }
        }
        if ($request->order_type == 2) {
            if ($request->delivery_date == "") {
                return response()->json(["status" => 0, "message" => trans('messages.pickup_date_required')], 400);
            }
            if ($request->delivery_time == "") {
                return response()->json(["status" => 0, "message" => trans('messages.pickup_time_required')], 400);
            }
        }
        if ($request->order_type == 3) {
            if ($request->table == "") {
                return response()->json(["status" => 0, "message" => trans('messages.table_required')], 400);
            }
        }
        if ($request->customer_name == "") {
            return response()->json(["status" => 0, "message" => trans('messages.customer_name_required')], 400);
        }
        if ($request->customer_email == "") {
            return response()->json(["status" => 0, "message" => trans('messages.customer_email_required')], 400);
        }
        if ($request->customer_mobile == "") {
            return response()->json(["status" => 0, "message" => trans('messages.customer_mobile_required')], 400);
        }
        if ($request->payment_type == "") {
            return response()->json(["status" => 0, "message" => trans('messages.payment_type_required')], 400);
        }
        if ($request->grand_total == "") {
            return response()->json(["status" => 0, "message" => trans('messages.grand_total_required')], 400);
        }
        if ($request->sub_total == "") {
            return response()->json(["status" => 0, "message" => trans('messages.sub_total_required')], 400);
        }
        if ($request->tax == "") {
            return response()->json(["status" => 0, "message" => trans('messages.tax_required')], 400);
        }

        $payment_id ="";
        $payment_id = $request->payment_id;
        if ($request->payment_type == "stripe" || (string) $request->payment_type === (string) Payment::TYPE_STRIPE) {

            $getstripe = Payment::select('environment', 'secret_key', 'currency')
                ->where('payment_type', Payment::TYPE_STRIPE)
                ->where('vendor_id', $request->vendor_id)
                ->where('is_available', 1)
                ->where('is_activate', 1)
                ->first();

            if (empty($getstripe) || empty($getstripe->secret_key)) {
                return response()->json(["status" => 0, "message" => trans('messages.unable_to_complete_payment')], 400);
            }

            $stripe = new \Stripe\StripeClient($getstripe->secret_key);
            $gettoken = $stripe->tokens->create([
                'card' => [
                    'number' => $request->card_number,
                    'exp_month' => $request->card_exp_month,
                    'exp_year' => $request->card_exp_year,
                    'cvc' => $request->card_cvc,
                ],
            ]);
            Stripe\Stripe::setApiKey($getstripe->secret_key);
            $payment = Stripe\Charge::create([
                "amount" => (int) round($request->grand_total * 100),
                "currency" => $getstripe->currency,
                "source" => $gettoken->id,
                "description" => "Restro-SaaS-OrderPayment",
            ]);
            $payment_id = $payment->id;
        }
        $orderresponse = helper::createorder($request->vendor_id,$request->user_id, $request->session_id,$request->payment_type, $payment_id, $request->customer_email, $request->customer_name, $request->customer_mobile, $request->stripeToken, $request->grand_total, $request->delivery_charge, $request->address, $request->building, $request->landmark, $request->postal_code, $request->discount_amount, $request->offer_type, $request->sub_total, $request->tax, $request->delivery_time, $request->delivery_date, $request->delivery_area, $request->couponcode, $request->order_type, $request->notes,$request->table);
        
        $vendordata = User::where('id', $request->vendor_id)->first();
    
        $data = json_decode(json_encode($orderresponse), true);
        
        if(@$orderresponse == -1)
        {
            return $this->sendError(trans('messages.cart_empty'));
        }
        if($request->offer_type == "promocode") {
            if($request->couponcode != null)
            {
                $promocode = Coupons::where('code',$request->couponcode)->where('vendor_id',$request->vendor_id)->first();
                $promocode->limit = $promocode->limit - 1;
                $promocode->save();
            }
        }
    
        if ($orderresponse != "") {
            $whmessage = whatsapp_helper::whatsappmessage($orderresponse, $vendordata->slug, $vendordata);
            $whatsapp_number = helper::appdata($vendordata->id)->contact;
            

            $responsedata = [
                'order_number' =>  $orderresponse,
                'whmessage' =>  $whmessage,
                'whatsapp_number' =>  $whatsapp_number,
            ];
            
            return $this->sendResponse($responsedata, trans('messages.success'));
        } else {
            return response()->json(["status" => 0, "message" => $data['original']['message']], 200);
        }
   
    }

    public function orderhistory(Request $request)
    {
        if ($request->vendor_id == "") {
            return $this->sendError(trans('messages.vendor_id_required'));
        }
        if ($request->user_id == "") {
            return $this->sendError(trans('messages.user_id_required'));
        }
    
        $orders = Order::where('vendor_id', $request->vendor_id)->where('user_id', $request->user_id)->orderBy('id', 'DESC')->get();
        
        return $this->sendResponse(OrderResource::collection($orders), trans('messages.success'));
    }

    public function orderdetails(Request $request)
    {
        if ($request->order_number == "") {
            return $this->sendError(trans('messages.order_number_required'));
        }

        $orderdata = Order::with('tableqr')->select('orders.id','orders.order_number', \DB::raw('DATE_FORMAT(orders.created_at, "%d %M %Y") as date'), 'orders.address', 'orders.building', 'orders.landmark', 'orders.pincode', 'orders.order_type', 'orders.discount_amount', 'orders.order_number', 'orders.status', 'orders.order_notes', 'orders.tax', 'orders.delivery_charge', 'orders.couponcode', 'orders.offer_type', 'orders.sub_total', 'orders.grand_total', 'orders.customer_name', 'orders.customer_email', 'orders.mobile', 'orders.table_id','orders.payment_type')->where('orders.order_number', $request->order_number)->first();
        $orderdetails = OrderDetails::where('order_details.order_id', $orderdata->id)->get();

        $data = [
            'orderdata' =>  $orderdata,
            'orderdetails' =>  OrderDetailsResource::collection($orderdetails),
        ];
        
        return $this->sendResponse($data, trans('messages.success'));
    }

    public function cancelorder(Request $request)
    {
        if ($request->order_number == "") {
            return $this->sendError(trans('messages.order_number_required'));
        }

        Order::where('order_number', $request->order_number)->update(['status' => "4"]);
        $orderdata = Order::where('order_number', $request->order_number)->first();
        $emaildata = User::select('id', 'name', 'slug', 'email', 'mobile','token')->where('id', $orderdata->vendor_id)->first();
        $title = trans('labels.order_update');
        $body = "#".$request->order_number." has been cancelled";
        helper::push_notification($emaildata->token,$title,$body,"order",$orderdata->id);
        return $this->sendSuccess(trans('messages.success'));
    }
}
