<?php
   
namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\api\BaseController as BaseController;
use App\Models\Cart;
use App\Http\Resources\Cart as CartResource;
   
class CartController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function addtocart(Request $request)
    {
        $user_id = "";
        if ($request->user_id != "") {
            $user_id = $request->user_id;
        }
        if ($request->vendor_id == "") {
            return $this->sendError(trans('messages.vendor_id_required'));
        }
        
        $totalprice = 0;
        $cart = new Cart;
        $cart->user_id = $user_id;
        $cart->session_id = $request->session_id;
        $cart->vendor_id = $request->vendor_id;
        $cart->item_id = $request->item_id;
        $cart->item_name = $request->item_name;
        $cart->item_image = $request->item_image;
        $cart->item_price = $request->item_price;
        $cart->tax = $request->tax;
        $cart->extras_id = $request->extras_id;
        $cart->extras_name = $request->extras_name;
        $cart->extras_price = $request->extras_price;
        $cart->qty = $request->qty;
        if($request->extras_price != null)
        {
            $extraprices =  explode(',',$request->extras_price);
            foreach($extraprices as $price)
            {
                $totalprice += $price;
            }
        }
        if($request->variants_price != null)
        {
            $totalprice += $request->variants_price;
            $cart->price = $totalprice;
        }
        else
        {
            $cart->price = $request->item_price + $totalprice;
        }
        
        $cart->variants_id = $request->variants_id;
        $cart->variants_name = $request->variants_name;
        $cart->variants_price = $request->variants_price;
        $cart->save();
        
        return $this->sendResponse($cart, trans('messages.success'));
    }

    public function getcart(Request $request)
    {
        $user_id = "";
        if ($request->user_id != "") {
            $user_id = $request->user_id;
        }
        if ($request->vendor_id == "") {
            return $this->sendError(trans('messages.vendor_id_required'));       
        }
        $cartitems = Cart::where('vendor_id', $request->vendor_id);
        if($user_id != "") {
            $cartitems->where('user_id', $request->user_id);
        } else {
            $cartitems->where('session_id', $request->session_id);
        }
        $cartdata = $cartitems->get();
    
        $data = [
            'cartdata' =>  CartResource::collection($cartdata)
        ];

        return $this->sendResponse($data, trans('messages.success'));
    }

    public function qtyupdate(Request $request)
    {
        if ($request->cart_id == "") {
            return $this->sendError(trans('messages.cart_id_required'));       
        }
        if ($request->qty == "") {
            return $this->sendError(trans('messages.qty_required'));       
        }

        $update = Cart::where('id', $request->cart_id)->update(['qty' => $request-> qty]);

        return $this->sendResponse($update, trans('messages.success'));
    }

    public function deletecartitem(Request $request)
    {
        if ($request->cart_id == "") {
            return $this->sendError(trans('messages.cart_id_required'));       
        }

        $cart = Cart::where('id', $request->cart_id)->delete();

        return $this->sendSuccess(trans('messages.success'));
    }
}