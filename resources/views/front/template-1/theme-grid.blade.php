@foreach ($getcategory as $key => $category)

    @php
        $check_cat_count = 0;
    @endphp
    @foreach ($getitem as $item)
        @if ($category->id == $item->cat_id)
            @php
                $check_cat_count++;
            @endphp
        @endif
    @endforeach
    @if ($check_cat_count > 0)
        <div class="specs theme1grid @if ($check_cat_count > 0 && $key != 0) card-none @endif" id="specs-{{ $category->id }}">
            <div class="row g-3 products-img">
                @if (!$getcategory->isEmpty())
                    @php $i = 0; @endphp
                    @foreach ($getitem as $item)
                        @if ($category->id == $item->cat_id)
                            @php
                                if ($item->top_deals == 1 && helper::top_deals($vdata) != null) {
                                    if (@helper::top_deals($vdata)->offer_type == 1) {
                                        if ($item['variation']->count() > 0) {
                                            if (
                                                $item['variation'][0]->price >
                                                @helper::top_deals($vdata)->offer_amount
                                            ) {
                                                $price =
                                                    $item['variation'][0]->price -
                                                    @helper::top_deals($vdata)->offer_amount;
                                            } else {
                                                $price = $item['variation'][0]->price;
                                            }
                                        } else {
                                            if ($item->item_price > @helper::top_deals($vdata)->offer_amount) {
                                                $price =
                                                    $item->item_price -
                                                    @helper::top_deals($vdata)->offer_amount;
                                            } else {
                                                $price = $item->item_price;
                                            }
                                        }
                                    } else {
                                        if ($item['variation']->count() > 0) {
                                            $price =
                                                $item['variation'][0]->price -
                                                $item['variation'][0]->price *
                                                    (@helper::top_deals($vdata)->offer_amount / 100);
                                        } else {
                                            $price =
                                                $item->item_price -
                                                $item->item_price *
                                                    (@helper::top_deals($vdata)->offer_amount / 100);
                                        }
                                    }
                                    if ($item['variation']->count() > 0) {
                                        $original_price = $item['variation'][0]->price;
                                    } else {
                                        $original_price = $item->item_price;
                                    }
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
                            <div class="col-6 col-lg-3">
                                <div class="card h-100 position-relative rounded">
                                    <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}">
                                        <div class="overflow-hidden theme1grid_image p-2">
                                            <img src="{{ helper::image_path(@$item['item_image']->image) }}"
                                                alt="" class="rounded">
                                            @if ($off > 0)
                                                <span
                                                    class="offer-text rounded fw-500 text-bg-secondary fs-8">{{ $off }}%
                                                    {{ trans('labels.off') }}</span>
                                            @endif
                                        </div>
                                    </a>
                                    <div class="card-body p-2 p-md-3 pb-sm-0 ">
                                        @if (Auth::user() && Auth::user()->type == 3)
                                            <div class="favorite-icon set-fav1-{{ $item->id }}">
                                                @if ($item->is_favorite == 1)
                                                    <a href="javascript:void(0)"
                                                        onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',0,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')"><i
                                                            class="fa-solid fa-heart"></i></a>
                                                @else
                                                    <a href="javascript:void(0)"
                                                        onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',1,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')">
                                                        <i class="fa-regular fa-heart"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        @endif
                                        @if (App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first() != null &&
                                                App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first()->activated == 1)
                                            @if (App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first() != null &&
                                                    App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first()->activated == 1)
                                                @if (helper::appdata($storeinfo->id)->checkout_login_required == 1)
                                                    <a class="fs-8 d-flex gap-1 align-items-center mb-1"
                                                        onclick="showreviews('{{ $item->id }}')" role="button"
                                                        aria-controls="offcanvasExample">
                                                        <i class="fa-solid fa-star text-warning"></i>
                                                        <p class="cursor-pointer color-changer fw-600 fs-8">
                                                            {{ number_format($item->avg_ratting, 1) }}</p>
                                                    </a>
                                                @endif
                                            @endif
                                        @endif
                                        <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}"
                                            class="title pb-1 color-changer">
                                            {{ $item->item_name }}
                                        </a>
                                    </div>
                                    <div class="card-footer bg-transparent border-0 p-2 pt-md-0 p-md-3 pt-0">
                                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                                            <div class="mb-2 mb-md-0">
                                                <div class="d-flex gap-1 flex-wrap align-items-center">
                                                    <p class="price color-changer">
                                                        {{ helper::currency_formate($price, @$storeinfo->id) }}
                                                    </p>
                                                    @if ($item->item_original_price != null)
                                                        @if ($original_price > $price)
                                                            <del class="text-muted">{{ helper::currency_formate($original_price, @$storeinfo->id) }}</del>
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>
                                            <div
                                                class="d-flex col-sm-auto col-12 justify-content-end align-items-center">
                                                <button
                                                    class="btn-primary w-100 px-3 py-2 d-flex gap-2 fw-500 align-items-center justify-content-center fs-7 btn m-0"
                                                    type="button"
                                                    onclick="showitems('{{ $item->id }}','{{ $item_name }}','{{ $item->item_price }}')">
                                                    <div class="addcartbtn-{{ $item->id }}">
                                                        <i class="fa-regular fa-plus"></i>
                                                        {{ trans('labels.add_to_cart') }}
                                                    </div>
                                                    <div class="load showload-{{ $item->id }}"
                                                        style="display:none">
                                                    </div>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @php $i += 1; @endphp
                        @endif
                    @endforeach
                @endif
            </div>
        </div>
    @endif
@endforeach
