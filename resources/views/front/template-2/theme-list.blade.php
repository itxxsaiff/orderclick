@foreach ($getcategory as $key => $category)
    @php $check_cat_count = 0; @endphp
    @foreach ($getitem as $item)
        @if ($category->id == $item->cat_id) @php $check_cat_count++; @endphp @endif
    @endforeach
    @if ($check_cat_count > 0)
        <div class="specs @if ($key != 0) card-none @endif" id="specs-{{ $category->id }}">
            <div class="row g-4">
                @foreach ($getitem as $item)
                    @if ($category->id == $item->cat_id)
                        @php
                            if ($item->top_deals == 1 && helper::top_deals($vdata) != null) {
                                if (@helper::top_deals($vdata)->offer_type == 1) {
                                    if ($item['variation']->count() > 0) {
                                        $price = $item['variation'][0]->price > @helper::top_deals($vdata)->offer_amount
                                            ? $item['variation'][0]->price - @helper::top_deals($vdata)->offer_amount
                                            : $item['variation'][0]->price;
                                    } else {
                                        $price = $item->item_price > @helper::top_deals($vdata)->offer_amount
                                            ? $item->item_price - @helper::top_deals($vdata)->offer_amount
                                            : $item->item_price;
                                    }
                                } else {
                                    if ($item['variation']->count() > 0) {
                                        $price = $item['variation'][0]->price - $item['variation'][0]->price * (@helper::top_deals($vdata)->offer_amount / 100);
                                    } else {
                                        $price = $item->item_price - $item->item_price * (@helper::top_deals($vdata)->offer_amount / 100);
                                    }
                                }
                                $original_price = $item['variation']->count() > 0 ? $item['variation'][0]->price : $item->item_price;
                                $off = $original_price > 0 ? round(100 - ($price * 100) / $original_price) : 0;
                            } else {
                                if ($item['variation']->count() > 0) {
                                    $price = $item['variation'][0]->price;
                                    $original_price = $item['variation'][0]->original_price;
                                } else {
                                    $price = $item->item_price;
                                    $original_price = $item->item_original_price;
                                }
                                $off = $original_price > 0 ? round(100 - ($price * 100) / $original_price) : 0;
                            }
                            $item_name = str_replace("'", "\'", $item->item_name);
                        @endphp
                        <div class="col-md-6">
                            <div class="ct-card ct-list-card">
                                <div class="ct-card__media">
                                    @if ($off > 0)
                                        <span class="ct-badge">{{ $off }}% {{ trans('labels.off') }}</span>
                                    @endif
                                    <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}">
                                        <img src="{{ helper::image_path(@$item['item_image']->image) }}" alt="{{ $item->item_name }}">
                                    </a>
                                </div>
                                <div class="ct-card__body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        @if (App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first() != null &&
                                                App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first()->activated == 1)
                                            @if (App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first() != null &&
                                                    App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first()->activated == 1)
                                                @if (helper::appdata($storeinfo->id)->checkout_login_required == 1)
                                                    <a class="ct-card__rating" onclick="showreviews('{{ $item->id }}')" role="button">
                                                        <i class="fa-solid fa-star"></i>
                                                        <span>{{ number_format($item->avg_ratting, 1) }}</span>
                                                    </a>
                                                @endif
                                            @endif
                                        @endif
                                        @if (Auth::user() && Auth::user()->type == 3)
                                            <div class="set-fav1-{{ $item->id }}" style="margin-left:auto">
                                                @if ($item->is_favorite == 1)
                                                    <a href="javascript:void(0)" style="color:var(--ct-accent)" onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',0,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')"><i class="fa-solid fa-heart"></i></a>
                                                @else
                                                    <a href="javascript:void(0)" style="color:var(--ct-accent)" onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',1,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')"><i class="fa-regular fa-heart"></i></a>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                    <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}" class="ct-card__title">{{ $item->item_name }}</a>
                                    <div class="ct-card__price">
                                        <span class="now">{{ helper::currency_formate($price, @$storeinfo->id) }}</span>
                                        @if ($item->item_original_price != null && $original_price > $price)
                                            <del>{{ helper::currency_formate($original_price, @$storeinfo->id) }}</del>
                                        @endif
                                    </div>
                                    <div class="ct-card__foot">
                                        <button type="button" class="ct-add" onclick="showitems('{{ $item->id }}','{{ $item_name }}','{{ $item->item_price }}')">
                                            <div class="addcartbtn-{{ $item->id }}">
                                                <i class="fa-regular fa-plus"></i> {{ trans('labels.add_to_cart') }}
                                            </div>
                                            <div class="load showload-{{ $item->id }}" style="display:none"></div>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    @endif
@endforeach
