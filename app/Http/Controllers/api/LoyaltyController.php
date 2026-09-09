<?php
   
namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\api\BaseController as BaseController;
use App\Helpers\loyaltyhelper;

class LoyaltyController extends BaseController
{
    public function getloyaltypoints(Request $request)
    {
        if ($request->vendor_id == "") {
            return $this->sendError(trans('messages.vendor_id_required'));       
        }
        if ($request->user_id == "") {
            return $this->sendError(trans('messages.user_id_required'));       
        }
    
        $data = [
            'available_point' =>  @loyaltyhelper::availablepoints($request->user_id,$request->vendor_id),
            'per_coin_amount' =>  @loyaltyhelper::getloyaltydata($request->vendor_id)->per_coin_amount,
        ];

        return $this->sendResponse($data, trans('messages.success'));
    }

    public function loyaltyhistory(Request $request)
    {
        if ($request->vendor_id == "") {
            return $this->sendError(trans('messages.vendor_id_required'));       
        }
        if ($request->user_id == "") {
            return $this->sendError(trans('messages.user_id_required'));       
        }
    
        $data = [
            'available_point' =>  loyaltyhelper::getloyaltyhistory($request->vendor_id,$request->user_id)
        ];

        return $this->sendResponse($data, trans('messages.success'));
    }
}