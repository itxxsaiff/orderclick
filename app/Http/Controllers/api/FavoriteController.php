<?php
   
namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\api\BaseController as BaseController;
use App\Models\Favorite;
use App\Models\Item;
use App\Http\Resources\Item as ItemResource;
use Illuminate\Support\Facades\DB;
   
class FavoriteController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function managefavorite(Request $request)
    {

        if ($request->user_id != "") {
            $user_id = $request->user_id;
        }
        if ($request->vendor_id == "") {
            return $this->sendError(trans('messages.vendor_id_required'));
        }
        if ($request->item_id == "") {
            return $this->sendError(trans('messages.item_id_required'));
        }
        if ($request->type == "") {
            return $this->sendError(trans('messages.type_required'));
        }

        $checkfavorite = Favorite::where('user_id',$user_id)->where('item_id',$request->item_id)->first();
        try {
            if($request->type == 1){
                $favorite = new Favorite();
                $favorite->user_id = $user_id;
                $favorite->item_id = $request->item_id;
                $favorite->vendor_id = $request->vendor_id;
                $favorite->save();
            }
            if($request->type == 0){
                $checkfavorite->delete();
            }
            return $this->sendSuccess(trans('messages.success'));
        } catch (\Throwable $th) {
            return $this->sendError(trans('messages.wrong'));
        }
    }

    public function favoritelist(Request $request)
    {

        if ($request->user_id != "") {
            $user_id = $request->user_id;
        }
        if ($request->vendor_id == "") {
            return $this->sendError(trans('messages.vendor_id_required'));
        }

        try {
            $items = Item::with(['variation', 'extras','item_image'])->select('items.*',DB::raw('(case when favorite.item_id is null then 0 else 1 end) as is_favorite'))
            ->leftJoin('favorite', function($query) use($user_id) {
                $query->on('favorite.item_id','=','items.id')
                ->where('favorite.user_id', '=', $user_id);
            })
            ->groupBy('items.id','favorite.item_id')
            ->where('items.is_available','1')
            ->where('favorite.user_id',$user_id)
            ->where('favorite.vendor_id',$request->vendor_id)
            ->orderByDesc('favorite.id')->get();
            
            $data = [
                'items' => ItemResource::collection($items),
            ];

            return $this->sendResponse($data, trans('messages.success'));
        } catch (\Throwable $th) {
            dd($th);
            return $this->sendError(trans('messages.wrong'));
        }
    }
}