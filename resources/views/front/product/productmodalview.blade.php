<div class="modal-content rounded-4">
    <div class="modal-header justify-content-between p-3">
        <p class="title fs-5 text-dark color-changer fw-600" id="viewitem_name">{{ $getitem->item_name }}</p>
         <button type="button" class="bg-transparent border-0 m-0" data-bs-dismiss="modal"
                        aria-label="Close">
                        <i class="fa-regular fa-xmark color-changer fs-4"></i>
                    </button>
    </div>
    <div class="modal-body pb-0">
        @if (!request()->is('admin/pos/item-details'))
            <div id="carouselExampleIndicators" class="carousel slide position-relative">
                <div class="carousel-indicators" id="image_buttons">
                    @foreach ($itemimages as $key => $image)
                        <button type="button" data-bs-target="#carouselExampleIndicators"
                            data-bs-slide-to="{{ $key }}" class="{{ $key == 0 ? 'active' : '' }}"
                            aria-current="true" aria-label="Slide {{ $key }}"></button>
                    @endforeach
                </div>
                <div class="carousel-inner card-modal-iages" id="item_images">
                    @if (count($itemimages) > 0)
                        @foreach ($itemimages as $key => $image)
                            <div class="carousel-item {{ $key == 0 ? 'active' : '' }} " name="image{{ $key }}">
                                <img class="img-fluid w-100" src="{{ helper::image_path($image->image) }}">
                            </div>
                            @if ($key == 0)
                                <input type="hidden" name="item_image" id="overview_item_image" value="{{ @$image->image }}">
                            @endif
                        @endforeach
                    @else
                        <div class="carousel-item active" name="image0">
                            <img class="img-fluid w-100" src="{{ helper::food_image($getitem->item_name, $getitem->id) }}">
                        </div>
                        <input type="hidden" name="item_image" id="overview_item_image" value="">
                    @endif
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators"
                    data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">{{ trans('labels.previous') }}</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators"
                    data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">{{ trans('labels.next') }}</span>
                </button>
            </div>
        @endif
        @php
            if (
                $getitem->top_deals == 1 &&
                helper::top_deals(@$vdata) != null &&
                !request()->is('admin/pos/item-details')
            ) {
                if (@helper::top_deals($vdata)->offer_type == 1) {
                    if ($getitem['variation']->count() > 0) {
                        if ($getitem['variation'][0]->price > @helper::top_deals($vdata)->offer_amount) {
                            $price = $getitem['variation'][0]->price - @helper::top_deals($vdata)->offer_amount;
                        } else {
                            $price = $getitem['variation'][0]->price;
                        }
                    } else {
                        if ($getitem->item_price > @helper::top_deals($vdata)->offer_amount) {
                            $price = $getitem->item_price - @helper::top_deals($vdata)->offer_amount;
                        } else {
                            $price = $getitem->item_price;
                        }
                    }
                } else {
                    if ($getitem['variation']->count() > 0) {
                        $price =
                            $getitem['variation'][0]->price -
                            $getitem['variation'][0]->price * (@helper::top_deals($vdata)->offer_amount / 100);
                    } else {
                        $price =
                            $getitem->item_price -
                            $getitem->item_price * (@helper::top_deals($vdata)->offer_amount / 100);
                    }
                }
                if ($getitem['variation']->count() > 0) {
                    $original_price = $getitem['variation'][0]->price;
                } else {
                    $original_price = $getitem->item_price;
                }
                $off = $original_price > 0 ? round(100 - ($price * 100) / $original_price) : 0;
            } else {
                if ($getitem['variation']->count() > 0) {
                    $price = $getitem['variation'][0]->price;
                    $original_price = $getitem['variation'][0]->original_price;
                } else {
                    $price = $getitem->item_price;
                    $original_price = $getitem->item_original_price;
                }
                $off = $original_price > 0 ? round(100 - ($price * 100) / $original_price) : 0;
            }
        @endphp
        <div class="{{ !request()->is('admin/pos/item-details') ? 'mt-4' : '' }}">
            @if ($off > 0)
                <span class="badge text-bg-secondary fs-7 mb-2" id="offer">{{ $off }}%
                    {{ trans('labels.off') }}</span>
            @endif
            @if ($getitem->is_available != 2 || $getitem->is_deleted == 1)
                <div class="products-price d-flex align-items-center justify-content-between">
                    <p id="laodertext" class="d-none laodertext"></p>
                    <div class="d-flex gap-2 align-items-center modal-product-detail-price">
                        <span class="price fs-6 text-dark color-changer fw-600" id="detail_item_price">
                            {{ helper::currency_formate($price, $getitem->vendor_id) }}
                        </span>
                        @if ($original_price > $price)
                            <del id="detail_original_price" class="text-muted">
                                {{ $original_price > 0 ? helper::currency_formate($original_price, $getitem->vendor_id) : '' }}
                            </del>
                        @endif
                    </div>
                    @if (!request()->is('admin/pos/item-details'))
                        @if (App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first() != null &&
                                App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first()->activated == 1)
                            @if (App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first() != null &&
                                    App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first()->activated == 1)
                                @if (helper::appdata($getitem->vendor_id)->checkout_login_required == 1)
                                    <div class="bg-light bg-change-mode border rounded-2">
                                        <div class="d-flex p-1 gap-2 align-items-center">
                                            <i class="fa-solid fa-star text-warning fs-7"></i>
                                            <p class="fs-7 color-changer cursor-pointer">
                                                {{ number_format($getitem->avg_ratting, 1) }}
                                            </p>
                                        </div>
                                    </div>
                                @endif
                            @endif
                        @endif
                    @endif
                </div>
            @endif
            @if ($getitem->is_available != 2 || $getitem->is_deleted == 1)
                <p id="tax" class="responcive-tax text-left">
                    <span class="text-muted fs-7">
                        @if ($getitem->tax != null && $getitem->tax != '')
                            <span class="text-danger fs-7"> {{ trans('labels.exclusive_taxes') }}</span>
                        @else
                            <span class="text-success fs-7"> {{ trans('labels.inclusive_taxes') }}</span>
                        @endif
                    </span>
                </p>
            @endif
            @if (!request()->is('admin/pos/item-details'))
                @if (App\Models\SystemAddons::where('unique_identifier', 'fake_view')->first() != null &&
                        App\Models\SystemAddons::where('unique_identifier', 'fake_view')->first()->activated == 1)
                    @if (helper::appdata($storeinfo->id)->product_fake_view == 1)
                        @php
                            $var = ['{eye}', '{count}'];
                            $newvar = [
                                "<i class='fa-solid fa-eye'></i>",
                                rand(
                                    helper::appdata($storeinfo->id)->min_view_count,
                                    helper::appdata($storeinfo->id)->max_view_count,
                                ),
                            ];

                            $fake_view = str_replace($var, $newvar, helper::appdata($storeinfo->id)->fake_view_message);
                        @endphp
                        <div class="border-bottom pb-3 mt-2">
                            <div class="d-flex gap-1 align-items-center blink_me">
                                <p class="fw-600 text-success m-0">{!! $fake_view !!}</p>
                            </div>
                        </div>
                    @endif
                @endif
            @endif
            @if ($getitem->has_variants == 2)
                @if ($getitem->is_available == 2 || $getitem->is_deleted == 1)
                    <h3 class="text-danger">{{ trans('labels.not_available') }}</h3>
                @endif
            @else
                <h3 class="text-danger" id="detail_not_available_text"></h3>
            @endif
            <div class="border-bottom py-2 {{ $getitem->stock_management == 1 ? 'd-block' : 'd-none' }} {{ $getitem->is_available == 1 ? 'd-block' : 'd-none' }}"
                id="sku_stock">
                <div class="meta-content rounded-2 bg-light bg-changer p-2">
                    @if ($getitem->has_variants == 2 && $getitem->stock_management == 1)
                        <div class="sku-wrapper product_meta" id="stock">
                            <span class="fs-7"><span class="fw-500 color-changer">{{ trans('labels.stock') }}:</span>
                            </span>
                            @if ($getitem->qty > 0)
                                <span class="text-success fw-500 fs-7">{{ $getitem->qty }}
                                    {{ trans('labels.in_stock') }}</span>
                            @else
                                <span class="text-danger fw-500 fs-7">{{ trans('labels.out_of_stock') }}</span>
                            @endif
                        </div>
                    @elseif ($getitem->has_variants == 1)
                        <div class="sku-wrapper product_meta" id="stock">
                            <span class="fs-7 color-changer"><span class="fw-500">{{ trans('labels.stock') }}:
                                </span></span>
                            <span class="fs-7 color-changer fw-500" id="detail_out_of_stock"></span>
                        </div>
                    @endif
                </div>
            </div>
            @if ($getitem->has_variants == 1 && $getitem->variants_json != null)
                <p class="title pb-1 variants m-0 color-changer" id="variants_title">{{ trans('labels.variants') }}</p>
                <div class="product-variations-wrapper">
                    <div class="size-variation modal-variation" id="variation">
                        @for ($i = 0; $i < count($getitem->variants_json); $i++)
                            <label class="fw-500 mt-2 form-label"
                                for="">{{ $getitem->variants_json[$i]['variant_name'] }}</label><br>
                            <div class="d-flex flex-wrap gap-2 border-bottom py-3">
                                @for ($t = 0; $t < count($getitem->variants_json[$i]['variant_options']); $t++)
                                    <label
                                        class="checkbox-inline fs-15 fw-500 check{{ str_replace(' ', '_', $getitem->variants_json[$i]['variant_name']) }} {{ $t == 0 ? 'active' : '' }}"
                                        id="check_{{ str_replace(' ', '_', $getitem->variants_json[$i]['variant_name']) }}-{{ str_replace(' ', '_', $getitem->variants_json[$i]['variant_options'][$t]) }}-{{ $getitem->id }}"
                                        for="{{ str_replace(' ', '_', $getitem->variants_json[$i]['variant_name']) }}-{{ str_replace(' ', '_', $getitem->variants_json[$i]['variant_options'][$t]) }}-{{ $getitem->id }}">
                                        <input type="checkbox" class="" name="skills"
                                            {{ $t == 0 ? 'checked' : '' }}
                                            value="{{ $getitem->variants_json[$i]['variant_options'][$t] }}"
                                            id="{{ str_replace(' ', '_', $getitem->variants_json[$i]['variant_name']) }}-{{ str_replace(' ', '_', $getitem->variants_json[$i]['variant_options'][$t]) }}-{{ $getitem->id }}">
                                        {{ $getitem->variants_json[$i]['variant_options'][$t] }}
                                    </label>
                                @endfor
                            </div>
                        @endfor
                    </div>
                </div>
            @endif

            @if (count($getitem['extras']) > 0)
                <div class="woo_pr_color flex_inline_center my-3 border-bottom pb-3">
                    <div class="woo_colors_list text-left">
                        <span id="extras">
                            <h6 class="extra-title fw-500 color-changer text-dark">{{ trans('labels.extras') }}</h6>
                            <ul class="list-unstyled extra-food mt-2">
                                <div id="pricelist">
                                    @foreach ($getitem['extras'] as $key => $extras)
                                        <li class="mb-2">
                                            <div class="form-check p-0 gap-2 d-flex align-items-center">
                                                <input class="form-check-input m-0 Checkbox" type="checkbox"
                                                    name="addons[]" extras_name="{{ $extras->name }}"
                                                    value="{{ $extras->id }}" price="{{ $extras->price }}"
                                                    id="extras_{{ $extras->id }}_{{ $getitem['id'] }}">
                                                <label
                                                    class="form-check-label w-100 m-0 justify-content-between d-flex"
                                                    for="extras_{{ $extras->id }}_{{ $getitem['id'] }}">
                                                    <span class="fs-7 p-0">{{ $extras->name }}</span>
                                                    <span
                                                        class="fs-7 p-0">{{ helper::currency_formate($extras->price, $getitem->vendor_id) }}</span>
                                                </label>
                                            </div>
                                        </li>
                                    @endforeach
                                </div>
                            </ul>
                        </span>
                    </div>
                </div>
            @endif
        </div>
        <input type="hidden" name="vendor" id="overview_vendor" value="{{ $getitem->vendor_id }}">
        <input type="hidden" name="item_id" id="overview_item_id" value="{{ $getitem->id }}">
        <input type="hidden" name="item_name" id="overview_item_name" value="{{ $getitem->item_name }}">
        <input type="hidden" name="item_image" id="overview_item_image"
            value="{{ @$getitem['item_image']->image }}">
        <input type="hidden" name="item_min_order" id="item_min_order" value="{{ $getitem->min_order }}">
        <input type="hidden" name="item_max_order" id="item_max_order" value="{{ $getitem->max_order }}">
        <input type="hidden" name="item_price" id="overview_item_price" value="{{ $getitem->item_price }}">
        <input type="hidden" name="item_original_price" id="overview_item_original_price"
            value ="{{ $original_price }}">
        <input type="hidden" name="tax" id="item_tax" value="{{ $getitem->tax }}">
        <input type="hidden" name="variants_name" id="variants_name">
        <input type="hidden" name="stock_management" id="stock_management"
            value="{{ $getitem->stock_management }}">
        @if (!request()->is('admin/pos/item-details'))
            <input type="hidden" id="getProductsVariantQuantityurl"
                value="{{ url('/get-products-variant-quantity') }}">
            <input type="hidden" id="addtocarturl" value="{{ url('/add-to-cart') }}">
        @else
            <input type="hidden" id="getProductsVariantQuantityurl"
                value="{{ url('admin/pos/get-products-variant-quantity') }}">
            <input type="hidden" id="addtocarturl" value="{{ url('/admin/pos/addtocart') }}">
        @endif
    </div>
    <div class="modal-footer pt-0 p-3 border-0 d-block">
        <div class="col-12 m-0 mb-4">
            <div class="row flex-wrap justify-content-between align-items-center g-2" id="detail_plus-minus">
                <div class="col-md-4 {{ !request()->is('admin/pos/item-details') ? ' col-12' : ' col-6' }}">
                    <div
                        class="input-group d-flex qty-input2 small w-100 responsive-margin m-0 rounded-2 hight-modal-btn align-items-center">
                        <button class="btn p-0 change-qty-2" id="minus" data-type="minus"
                            data-item_id="{{ $getitem->id }}"
                            onclick="changeqty($(this).attr('data-item_id'),'minus')" value="minus value"
                            href="javascript:void(0)" aria-label="Previous">
                            <i class="fa fa-minus"></i>
                        </button>
                        <input type="text" class="border bg-transparent color-changer text-center" name="number" value="1"
                            id="item_qty" readonly="">
                        <button class="btn p-0 change-qty-2" id="plus" data-item_id="{{ $getitem->id }}"
                            onclick="changeqty($(this).attr('data-item_id'),'plus')" data-type="plus"
                            value="plus value" href="javascript:void(0)" aria-label="Next">
                            <i class="fa fa-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="{{ !request()->is('admin/pos/item-details') ? 'col-md-4' : 'col-md-8' }} col-6">
                    <button
                        class="btn-primary btn rounded-3 w-100 text-center fs-15 add-btn {{ $getitem->is_available == 1 ? 'd-block' : 'd-none' }} add_to_cartbtn-{{ $getitem->id }}"
                        href="javascript:void(0)"
                        {{ $getitem->stock_management == 1 ? ($getitem->qty <= 0 ? 'disabled' : '') : '' }}
                        @if (!request()->is('admin/pos/item-details')) onclick="addtocart('0')" @else onclick="posaddtocart()" @endif>{{ trans('labels.addcart') }}
                    </button>
                </div>
                @if (!request()->is('admin/pos/item-details'))
                    <div class="col-md-4 col-6">
                        <button
                            class="btn-outline-secondary rounded-3 w-100 btn text-center fs-15 buynow add-btn {{ $getitem->is_available == 1 ? 'd-block' : 'd-none' }} buynowbtn-{{ $getitem->id }}"
                            href="javascript:void(0)"
                            {{ $getitem->stock_management == 1 ? ($getitem->qty <= 0 ? 'disabled' : '') : '' }}
                            onclick="addtocart('1')">{{ trans('labels.buy_now') }}
                        </button>
                    </div>
                @endif
            </div>
        </div>
        @if (!request()->is('admin/pos/item-details'))
            <div class="col-12 m-0">
                <div
                    class="d-flex gap-sm-2 gap-3 flex-wrap justify-content-between align-items-center w-100 pt-3 mt-2 border-top">
                    @if (Auth::user() && Auth::user()->type == 3)
                        <div class="fs-7 d-flex gap-2 align-items-center">
                            @if ($getitem->is_favorite == 1)
                                <a href="javascript:void(0)" class="btn-sm cursor-pointer"
                                    onclick="managefavorite('{{ $storeinfo->id }}','{{ $getitem->id }}',0,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')">
                                    <div class="d-flex align-items-center color-changer justify-content-center gap-2">
                                        <i class="fa-solid fa-heart btn-Wishlist"></i>
                                        <span class="fs-7">{{ trans('labels.remove_wishlist') }}</span>
                                    </div>
                                </a>
                            @else
                                <a href="javascript:void(0)" class="btn-sm cursor-pointer"
                                    onclick="managefavorite('{{ $storeinfo->id }}','{{ $getitem->id }}',1,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')">
                                    <div class="d-flex align-items-center color-changer justify-content-center gap-2">
                                        <i class="fa-regular fa-heart btn-Wishlist"></i>
                                        <span class="fs-7">{{ trans('labels.add_wishlist') }}</span>
                                    </div>
                                </a>
                            @endif
                        </div>
                    @else
                        <a href="{{ URL::to($storeinfo->slug . '/login') }}" class="btn-sm cursor-pointer">
                            <div class="d-flex align-items-center color-changer justify-content-center gap-2">
                                <i class="fa-regular fa-heart btn-Wishlist"></i>
                                <span class="fs-7">{{ trans('labels.add_wishlist') }}</span>
                            </div>
                        </a>
                    @endif
                    <div class="d-flex flex-wrap align-items-center justify-content-center gap-2">
                        @if ($getitem->video_url != null)
                            <a href="{{ $getitem->video_url }}" tooltip="Video"
                                class="rounded-circle prod-social m-0" id="btn-video" target="_blank">
                                <i class="fa-solid fa-video fs-7"></i>
                            </a>
                        @endif
                        @if (App\Models\SystemAddons::where('unique_identifier', 'whatsapp_message')->first() != null &&
                                App\Models\SystemAddons::where('unique_identifier', 'whatsapp_message')->first()->activated == 1)
                            <a href="https://api.whatsapp.com/send?phone={{ helper::appdata($getitem->vendor_id)->contact }}&amp;text=I am interested for this item :{{ $getitem->item_name }}"
                                tooltip="Whatsapp" class="rounded-circle prod-social m-0" id="enquiries"
                                target="_blank">
                                <i class="fa-brands fa-whatsapp fs-7"></i>
                            </a>
                        @endif
                        @if (helper::appdata($storeinfo->id)->google_review != null)
                            <a href="{{ helper::appdata($storeinfo->id)->google_review }}" target="_blank"
                                tooltip="Review" class="rounded-circle prod-social m-0">
                                <i class="fa-regular fa-star fs-7"></i></a>
                        @endif

                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
<script>
    var not_available = "{{ trans('labels.not_available') }}";
    var out_stock = "{{ trans('labels.out_of_stock') }}";
    var in_stock = "{{ trans('labels.in_stock') }}";
</script>
<script>
    $(document).ready(function($) {
        var selected = [];
        $('.modal-variation input:checked').each(function() {
            $("#variation [id='" + 'check_' + this.id + "']").addClass('active');
            selected.push($(this).attr('value'));
        });

        if (selected != "" && selected != null) {

            set_variant_price(selected);
        }

    });
    $('#variation input:checkbox').click(function() {
        var selected = [];
        var divselected = [];
        const myArray = this.id.split("-");

        var id = this.id;
        $('#variation .check' + myArray[0] + ' input:checked').each(function() {
            divselected.push($(this).attr('value'));
        });
        if (divselected.length == 0) {
            $(this).prop('checked', true);
        }


        $('#variation .check' + myArray[0] + ' input:checkbox').not(this).prop('checked', false);
        $('#variation .check' + myArray[0]).removeClass('active');
        $("#variation [id='" + 'check_' + this.id + "']").addClass('active');
        $('.modal-variation input:checked').each(function() {
            selected.push($(this).attr('value'));
        });
        if (selected != "" && selected != null) {
            $('.modal-product-detail-price').addClass('d-none');
            $('#laodertext').removeClass('d-none');
            $('#laodertext').html(
                '<span class="loader"></span>'
            );
            set_variant_price(selected);
        }
    });

    function set_variant_price(variants) {
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: $('#getProductsVariantQuantityurl').val(),
            data: {
                name: variants,
                item_id: $('#overview_item_id').val(),
                vendor_id: $('#overview_vendor').val(),
            },
            success: function(data) {
                if (data.status == 1) {
                    setTimeout(function() {
                        $('#laodertext').html('');
                    }, 4000);
                    var off = ((1 - (data.price / data.original_price)) * 100).toFixed(1);
                    $('#laodertext').addClass('d-none');
                    $('.modal-product-detail-price').removeClass('d-none');
                    $('#variants_name').val(variants);
                    $('#detail_item_price').text(currency_formate(parseFloat(data.price)));
                    $('#overview_item_price').val(data.price);
                    $('#offer').removeClass('d-none');
                    if (parseFloat(data.original_price) > parseFloat(data.price)) {
                        $('#detail_original_price').text(currency_formate(parseFloat(data.original_price)));
                        $('#offer').text($.number(off, 0) + '% ' + '{{ trans('labels.off') }}');
                    } else {
                        $('#detail_original_price').text('');
                        $('#offer').text('');
                    }
                    $('#overview_item_original_price').val(data.original_price);
                    $('#stock_management').val(data.stock_management);
                    $('#item_min_order').val(data.min_order);
                    $('#item_max_order').val(data.max_order);
                    if (data.is_available == 2) {
                        $('#offer').addClass('d-none');
                        $('#detail_not_available_text').html(not_available);
                        $('.add-btn').attr('disabled', true);
                        $('.add-btn').addClass('d-none');
                        $('#detail_item_price').addClass('d-none');
                        $('#detail_original_price').addClass('d-none');
                        $('#sku_stock').addClass('d-none');
                        $('#detail_plus-minus').addClass('d-none');
                        $('#tax').addClass('d-none');
                        $('#stock').addClass('d-none');

                    } else {
                        $('#offer').removeClass('d-none');
                        $('#detail_not_available_text').html('');
                        $('.add-btn').attr('disabled', false);
                        $('.add-btn').removeClass('d-none');
                        $('#detail_item_price').removeClass('d-none');
                        $('#detail_original_price').removeClass('d-none');
                        $('#detail_plus-minus').removeClass('d-none');
                        $('#sku_stock').addClass('d-none');
                        $('#tax').removeClass('d-none');
                        $('#stock').addClass('d-none');
                        if (data.stock_management == 1) {
                            $('#stock').removeClass('d-none');
                            $('#sku_stock').removeClass('d-none');
                            $('#detail_out_of_stock').removeClass('d-none');
                            if (data.quantity > 0) {
                                $('.add-btn').attr('disabled', false);
                                $('#detail_out_of_stock').removeClass('text-danger');
                                $('#detail_out_of_stock').addClass('text-success');
                                $('#detail_out_of_stock').html('' + data.quantity +
                                    ' {{ trans('labels.in_stock') }}');
                            } else {
                                $('.add-btn').attr('disabled', true);
                                $('#detail_out_of_stock').removeClass('text-dark');
                                $('#detail_out_of_stock').addClass('text-danger');
                                $('#detail_out_of_stock').html(out_stock);
                            }
                        } else {
                            $('#detail_out_of_stock').addClass('d-none');
                        }

                    }
                }

            }
        });
    }

    function changeqty(item_id, type) {
        var qtys = parseInt($('#item_qty').val());
        if (type == "minus") {
            qty = qtys - 1;
        } else {
            qty = qtys + 1;
        }
        if (qty >= "1") {
            $('.change-qty-2').prop('disabled', true);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ URL::to('/changeqty') }}",
                data: {
                    item_id: item_id,
                    type: type,
                    qty: qty,
                    vendor_id: $('#overview_vendor').val(),
                    variants_name: $('#variants_name').val(),
                    stock_management: $('#stock_management').val(),
                },
                method: 'POST',
                success: function(response) {
                    if (response.status == 1) {
                        $('#item_qty').val(response.qty);
                        $('.change-qty-2').prop('disabled', false);
                    } else {
                        $('.change-qty-2').prop('disabled', false);
                        toastr.error(response.message);
                    }
                },
                error: function(error) {
                    $('.change-qty-2').prop('disabled', false);
                    toastr.error(wrong);
                }
            });
        }

    }
</script>
