<?php
   
namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\api\BaseController as BaseController;
use App\Models\Category;
use App\Models\Item;
use App\Models\Banner;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\Banner as BannerResource;
use App\Http\Resources\Category as CategoryResource;
use App\Http\Resources\Item as ItemResource;
use App\Helpers\helper;
use Session;
   
class ProductController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function home(Request $request)
    {        
        if ($request->vendor_id == "") {
            return $this->sendError(trans('messages.vendor_id_required'));
        }

        $user_id = $request->user_id;
        
        $category = Category::where('vendor_id', $request->vendor_id)->where('is_available', '=', '1')->where('is_deleted', '2')->orderBy('reorder_id', 'ASC')->get();

        $items = Item::with(['variation', 'extras','item_image'])->select('items.*',DB::raw('(case when favorite.item_id is null then 0 else 1 end) as is_favorite'))
            ->leftJoin('favorite', function($query) use($user_id) {
                $query->on('favorite.item_id','=','items.id')
                ->where('favorite.user_id', '=', $user_id);
            })->where('items.vendor_id', $request->vendor_id)->where('is_available', '1')->orderBy('reorder_id', 'ASC')->get();

        $banner = Banner::where('vendor_id', $request->vendor_id)->orderBy('id', 'ASC')->get();

        $data = [
            'session' =>  Session::getId(),
            'currency' =>  helper::appdata($request->vendor_id)->currency,
            'currency_position' =>  helper::appdata($request->vendor_id)->currency_position,
            'banner' =>  BannerResource::collection($banner),
            'category' =>  CategoryResource::collection($category),
            'items' => ItemResource::collection($items),
            'primary_color' => @helper::appdata($request->vendor_id)->primary_color,
            'secondary_color' => @helper::appdata($request->vendor_id)->secondary_color,
        ];
        
        return $this->sendResponse($data, trans('messages.success'));
    }

    public function searchproduct(Request $request)
    {
        $user_id = "";
        if ($request->user_id != "") {
            $user_id = $request->user_id;
        }
        if ($request->vendor_id == "") {
            return $this->sendError(trans('messages.vendor_id_required'));       
        }

        if($request->has('search') && $request->search != ""){
            $items = Item::with(['variation', 'extras'])->select('items.*',DB::raw('(case when favorite.item_id is null then 0 else 1 end) as is_favorite'))
            ->leftJoin('favorite', function($query) use($user_id) {
                $query->on('favorite.item_id','=','items.id')
                ->where('favorite.user_id', '=', $user_id);
            })->where('items.vendor_id', $request->vendor_id)->where('is_available', '1')->where('items.item_name','LIKE','%'.$request->search.'%')->orderBy('id', 'ASC')->get();
        }

        $data = [
            'items' => ItemResource::collection($items)
        ];
        
        return $this->sendResponse($data, trans('messages.success'));
    }
}