@extends('front.theme.default')
@section('content')
    {{-- Classic-theme polish for the product details page. Scoped to details-only
         selectors (.view-product / .pro-title / .breadcrumb-sec) so it harmonises
         with the storefront home without altering any structure or JS hooks. --}}
    <style>
        .breadcrumb-sec { background: #faf7f1 !important; border-bottom: 1px solid #e7ded0; }
        .view-product .card-bg { background: #fff; border: 1px solid #e7ded0 !important; border-radius: 6px !important; }
        .view-product .sp-wrap { display: flex; align-items: center; justify-content: center; background: #f6f1e8; min-height: 340px; padding: 18px; }
        .view-product .sp-wrap img { width: 100%; max-height: 470px; object-fit: contain; border-radius: 4px; }
        .view-product .pro-title { font-family: 'Playfair Display', Georgia, 'Times New Roman', serif; font-weight: 700; letter-spacing: .2px; }
        .view-product .details_item_price.pricing { color: #b8860b !important; }
        #pills-tab .nav-link { border-radius: 3px; }
        #pills-tabContent .card,
        #review-tab + .tab-content .card,
        .sevirce-trued { border: 1px solid #e7ded0 !important; border-radius: 6px !important; box-shadow: none !important; }
        .top_related_products, .section-heading h4 { font-family: 'Playfair Display', Georgia, serif; }
    </style>
    <section class="breadcrumb-sec bg-change-mode">
        <div class="container">
            <nav>
                <ol class="breadcrumb d-flex m-0 text-capitalize">
                    <li class="breadcrumb-item">
                        <a href="{{ URL::to(@$storeinfo->slug) }}" class="text-dark color-changer">{{ trans('labels.home') }}</a>
                    </li>
                    <li
                        class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-item-right' : 'breadcrumb-item-left' }}">
                        {{ trans('labels.item_details') }}
                    </li>
                </ol>
            </nav>
        </div>
    </section>

    <section class="mt-5">
        <div class="container">
            <div class="row g-4 g-md-5 view-product">
                <div class="col-md-5 mb-sm-5 mb-3">
                    <div class="card card-bg h-100 overflow-hidden rounded-0 border-0 position-relative">
                        <!-- new big-view -->
                        <div class="sp-wrap">
                            @if (count($itemimages) > 0)
                                @foreach ($itemimages as $key => $image)
                                    <a href="{{ helper::image_path($image->image) }}">
                                        <img src="{{ helper::image_path($image->image) }}" alt="">
                                    </a>
                                @endforeach
                            @else
                                <a href="{{ helper::food_image($getitem->item_name, $getitem->id) }}">
                                    <img src="{{ helper::food_image($getitem->item_name, $getitem->id) }}" alt="">
                                </a>
                            @endif
                        </div>
                    </div>
                    <!-- new big-view -->
                </div>
                @php
                    if ($getitem->top_deals == 1 && helper::top_deals($vdata) != null) {
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
                <div class="col-md-7">
                    <div class="card-body p-0 text-left">
                        @if ($off > 0)
                            <span class="badge text-bg-primary border fs-7 p-2 mb-2" id="details_offer">{{ $off }}%
                                {{ trans('labels.off') }}</span>
                        @endif

                        <p class="pro-title fs-4 color-changer fw-600 mb-2">{{ $getitem->item_name }}</p>
                        <div class="d-flex align-items-center justify-content-between mb-0">
                            <p id="detail_laodertext" class="d-none laodertext"></p>
                            <div class="d-flex flex-wrap gap-2 align-items-center product-detail-price">
                                <p class="pro-text color-changer pricing details_item_price">
                                    {{ helper::currency_formate($price, $getitem->vendor_id) }}
                                </p>
                                @if ($original_price > $price)
                                    <del class="card-text pro-org-value text-muted pricing mb-0 details_original_price">
                                        {{ helper::currency_formate($original_price, $getitem->vendor_id) }}
                                    </del>
                                @endif
                            </div>

                            <!-- rating star -->
                            @if (App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first() != null &&
                                    App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first()->activated == 1)
                                @if (App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first() != null &&
                                        App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first()->activated == 1)
                                    @if (helper::appdata($getitem->vendor_id)->checkout_login_required == 1)
                                        <a href="javascript::void(0)" onclick="showreviews('{{ $getitem->id }}')"
                                            class="cursor-pointer">
                                            <div class="d-flex bg-gray px-2 py-1 rounded-2 align-items-center p-0 m-0"
                                                tooltip="View">
                                                <div id="ratting-div" class="fs-7 fw-semibold">
                                                    <p class="px-1 avg-ratting color-changer cursor-pointer">
                                                        <i class="fa-solid fa-star text-warning fs-7"></i>
                                                        {{ number_format($getitem->avg_ratting, 1) }}
                                                    </p>
                                                </div>
                                            </div>
                                        </a>
                                    @endif
                                @endif
                            @endif
                        </div>

                        <p id="detail_tax" class="responcive-tax text-left mb-1">
                            @if ($getitem->tax != null && $getitem->tax != '')
                                <span class="text-danger fs-7"> {{ trans('labels.exclusive_taxes') }}</span>
                            @else
                                <span class="text-success fs-7"> {{ trans('labels.inclusive_taxes') }}</span>
                            @endif
                        </p>

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

                                    $fake_view = str_replace(
                                        $var,
                                        $newvar,
                                        helper::appdata($storeinfo->id)->fake_view_message,
                                    );
                                @endphp
                                <div class="border-bottom pb-3">
                                    <div class="d-flex gap-1 align-items-center blink_me">
                                        <p class="fw-600 text-success">{!! $fake_view !!}</p>
                                    </div>
                                </div>
                            @endif
                        @endif

                        <div class="border-bottom pb-3 {{ $getitem->stock_management == 1 ? 'd-block' : 'd-none' }} {{ $getitem->is_available == 1 ? 'd-block' : 'd-none' }}"
                            id="detail_sku_stock">
                            <div class="meta-content bg-secondary-subtle bg-changer p-3 mt-3 rounded-2">
                                @if ($getitem->has_variants == 2 && $getitem->stock_management == 1)
                                    <div class="sku-wrapper product_meta py-1" id="detail_stock">
                                        <span class="fs-7 color-changer fw-semibold">
                                            {{ trans('labels.stock') }}:
                                        </span>
                                        @if ($getitem->qty > 0)
                                            <span class="text-success fs-7">{{ $getitem->qty }}
                                                {{ trans('labels.in_stock') }}</span>
                                        @else
                                            <span class="text-danger fs-7">{{ trans('labels.out_of_stock') }}</span>
                                        @endif
                                    </div>
                                @elseif ($getitem->has_variants == 1)
                                    <div class="sku-wrapper product_meta py-1" id="detail_stock">
                                        <span class="fs-7 color-changer fw-semibold">
                                            {{ trans('labels.stock') }}:
                                        </span>
                                        <span class="fs-7 fw-500" id="details_out_of_stock"></span>
                                    </div>
                                @endif
                                @if (helper::otherappdata(@helper::vendor_data()->id)->estimated_delivery_on_off == 1)
                                    <div class="sku-wrapper product_meta py-1">

                                        <span class="fs-7 color-changer fw-semibold">{{ trans('labels.estimated_delivery') }}
                                            :</span>
                                        <span class="text-muted fs-7">
                                            {{ helper::otherappdata(@helper::vendor_data()->id)->days_of_estimated_delivery }}
                                            {{ trans('labels.days') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                        @if ($getitem->has_variants == 1 && $getitem->variants_json != null)
                            <p class="title pb-1 mt-2 variants m-0" id="variants_title">{{ trans('labels.variants') }}</p>
                            <div class="product-variations-wrapper">
                                <div class="size-variation detail-variation" id="detail_variation">
                                    @for ($i = 0; $i < count($getitem->variants_json); $i++)
                                        <p class="fw-500 mt-2 color-changer" for="">
                                            {{ $getitem->variants_json[$i]['variant_name'] }}</p>
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
                                        <h6 class="extra-title color-changer fw-500 text-dark">{{ trans('labels.extras') }}</h6>
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
                        @if ($getitem->top_deals == 1 && helper::top_deals($vdata) != null)
                            <div id="eapps-countdown-timer-1"
                                class="countdown rounded eapps-countdown-timer-align-center  eapps-countdown-timer-finish-button-show   eapps-countdown-timer-style-combined eapps-countdown-timer-style-blocks eapps-countdown-timer-position-bar eapps-countdown-timer-area-clickable eapps-countdown-timer-has-background">
                                <div class="eapps-countdown-timer-container">
                                    <div class="eapps-countdown-timer-inner col-12 ">
                                        <div class="d-flex flex-column gap-2 align-items-sm-start align-items-center">
                                            <div class="eapps-countdown-timer-header">
                                                <div class="eapps-countdown-timer-header-title">
                                                    <div class="text-dark color-changer col-12 d-flex gap-2 align-items-center">
                                                        <i class="fa-regular fa-clock fs-6"></i>
                                                        <div class="line-2 fw-bolder">Hurry up!</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div
                                                class="eapps-countdown-timer-item-container eapps-countdown-timer-item-details mt-3 mt-sm-0">
                                                <div id="countdown"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="col-12 mt-3 pb-3 border-bottom">
                            <div class="row g-2 g-sm-3 " id="detail_plus-minus">
                                <div class="col-xl-3 col-6">
                                    <div class="input-group qty-input2 col-md-auto col-12 responsive-margin m-0 rounded-2">
                                        <button class="btn p-0 change-qty-1" id="minus"
                                            onclick="detailchangeqty('{{ $getitem->id }}','minus')" value="minus value">
                                            <i class="fa fa-minus"></i>
                                        </button>
                                        <input type="text" class="border-0 bg-transparent color-changer text-center detail_item_qty" value="1"
                                            readonly="">
                                        <button class="btn p-0 change-qty-1" id="plus"
                                            onclick="detailchangeqty('{{ $getitem->id }}','plus')" value="plus value">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                @if (App\Models\SystemAddons::where('unique_identifier', 'whatsapp_message')->first() != null &&
                                        App\Models\SystemAddons::where('unique_identifier', 'whatsapp_message')->first()->activated == 1)
                                    <div class="col-xl-3 col-6">
                                        <a href="https://api.whatsapp.com/send?phone={{ helper::appdata($getitem->vendor_id)->contact }}&amp;text=I am interested for this item :{{ $getitem->item_name }}"
                                            target="_blank" class="btn py-2 btn-danger btn-enquir rounded-2 w-100">
                                            <span class="px-1 fs-7 d-flex align-items-center gap-1">
                                                <i class="fa-brands fa-whatsapp"></i>
                                                {{ trans('labels.enquiries') }}
                                            </span>
                                        </a>
                                    </div>
                                @endif
                                <div class="col-xl-6 col-6">
                                    <button class="btn btn-store m-0 add-details-btn px-0 w-100 addtocart h-100"
                                        onclick="detailaddtocart('0')"
                                        {{ $getitem->stock_management == 1 ? ($getitem->qty <= 0 ? 'disabled' : '') : '' }}>
                                        <span class="px-1 fs-7">{{ trans('labels.addcart') }}</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex flex-wrap gap-sm-2 gap-3 justify-content-end w-100 my-3">
                            <div>
                                <div class="d-flex align-items-center justify-content-center gap-2">
                                    @if ($getitem->video_url != null)
                                        <a href="{{ $getitem->video_url }}" class="rounded-circle prod-social m-0"
                                            tooltip="Video" target="_blank">
                                            <i class="fa-solid fa-video fs-7"></i>
                                        </a>
                                    @endif
                                    @if (helper::appdata($storeinfo->id)->google_review != null)
                                        <a href="{{ helper::appdata($storeinfo->id)->google_review }}" target="_blank"
                                            tooltip="Review" class="rounded-circle prod-social fs-7">
                                            <i class="fa-regular fa-star"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @include('front.product.service-trusted')
                    </div>
                </div>
                <div class="product-view mt-3" id="review-tab">
                    <ul class="nav nav-pills py-3 border-bottom border-top gap-3" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" href="javascript:void(0)" data-bs-toggle="pill"
                                data-bs-target="#description" aria-selected="false"
                                role="tab">{{ trans('labels.description') }}</a>
                        </li>
                        @if (App\Models\SystemAddons::where('unique_identifier', 'product_inquiry')->first() != null &&
                                App\Models\SystemAddons::where('unique_identifier', 'product_inquiry')->first()->activated == 1)
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" href="javascript:void(0)" data-bs-toggle="pill"
                                    data-bs-target="#pills-product_inquiry" aria-selected="false"
                                    role="tab">{{ trans('labels.product_inquiry') }}</a>
                            </li>
                        @endif
                        @if (App\Models\SystemAddons::where('unique_identifier', 'question_answer')->first() != null &&
                                App\Models\SystemAddons::where('unique_identifier', 'question_answer')->first()->activated == 1)
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" href="javascript:void(0)" data-bs-toggle="pill"
                                    data-bs-target="#pills-question_answer" aria-selected="false"
                                    role="tab">{{ trans('labels.question_answer') }}</a>
                            </li>
                        @endif
                    </ul>
                </div>
                <div class="tab-content mt-3" id="pills-tabContent">
                    <div class="tab-pane fade active show" id="description" role="tabpanel"
                        aria-labelledby="description-tab">
                        <div class="card sevirce-trued">
                            <div class="card-body cms-section">
                                <p class="m-0">
                                    {!! $getitem->description !!}
                                </p>
                            </div>
                        </div>
                    </div>
                    @if (App\Models\SystemAddons::where('unique_identifier', 'product_inquiry')->first() != null &&
                            App\Models\SystemAddons::where('unique_identifier', 'product_inquiry')->first()->activated == 1)
                        <div class="tab-pane fade show" id="pills-product_inquiry" role="tabpanel"
                            aria-labelledby="pills-product_inquiry-tab">
                            <div class="card sevirce-trued">
                                <div class="card-body">
                                    <form action="{{ URL::to('product_inquiry') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $getitem->id }}">
                                        <input type="hidden" name="vendor_id" value="{{ $getitem->vendor_id }}">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label for="first_name" class="form-label d-flex gap-1">
                                                    {{ trans('labels.first_name') }}
                                                    <div aria-hidden="true" class="text-danger">*</div>
                                                </label>
                                                <input type="text" class="form-control fs-7 input-h" id="first_name"
                                                    name="first_name" placeholder="{{ trans('labels.first_name') }}"
                                                    required>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="last_name" class="form-label d-flex gap-1">
                                                    {{ trans('labels.last_name') }}
                                                    <div aria-hidden="true" class="text-danger">*</div>
                                                </label>
                                                <input type="text" class="form-control fs-7 input-h" name="last_name"
                                                    placeholder="{{ trans('labels.last_name') }}" id="last_name"
                                                    required>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="email" class="form-label d-flex gap-1">
                                                    {{ trans('labels.email') }}
                                                    <div aria-hidden="true" class="text-danger">*</div>
                                                </label>
                                                <input type="email" class="form-control fs-7 input-h" id="email"
                                                    name="email" placeholder="{{ trans('labels.email') }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="mobile" class="form-label d-flex gap-1">
                                                    {{ trans('labels.mobile') }}
                                                    <div aria-hidden="true" class="text-danger">*</div>
                                                </label>
                                                <input type="text" class="form-control fs-7 input-h number"
                                                    name="mobile" placeholder="{{ trans('labels.mobile') }}"
                                                    id="mobile" required>
                                            </div>
                                            <div class="col-12">
                                                <label for="message" class="form-label d-flex gap-1">
                                                    {{ trans('labels.comment') }}
                                                    <div aria-hidden="true" class="text-danger">*</div>
                                                </label>
                                                <p class="fs-8 mb-1 color-changer">{{ trans('labels.note') }}
                                                    {{ trans('messages.product_inquiry_note') }}</p>
                                                <textarea class="form-control fs-7 m-0" id="message" placeholder="{{ trans('labels.textarea') }}" name="message"
                                                    rows="3" required></textarea>
                                            </div>
                                            <div class="col-12">
                                                <button type="submit"
                                                    class="btn btn-secondary py-2 px-5 fs-15 fw-500 m-0">
                                                    {{ trans('labels.submit') }}
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif
                    @if (App\Models\SystemAddons::where('unique_identifier', 'question_answer')->first() != null &&
                            App\Models\SystemAddons::where('unique_identifier', 'question_answer')->first()->activated == 1)
                        <div class="tab-pane fade" id="pills-question_answer" role="tabpanel"
                            aria-labelledby="pills-product_inquiry-tab">
                            <div class="card sevirce-trued">
                                <div class="card-body">
                                    <div
                                        class="d-flex align-items-center justify-content-between gap-2 mb-2 pb-2 border-bottom">
                                        <p class="fs-7 line-1 color-changer">
                                            {{ trans('labels.have_doubts_regarding_this_product') }}</p>
                                        <div class="col-auto">
                                            <a type="button" class="w-100 fw-600 color-changer text-dark rounded-0 p-0"
                                                data-bs-toggle="modal"
                                                data-bs-target="#question_answer">{{ trans('labels.post_your_question') }}</a>
                                        </div>
                                    </div>
                                    @if (count($question_answer) > 0)
                                        @foreach ($question_answer as $item)
                                            <div class="border-bottom p-2">
                                                <h6 class="fs-7 fw-600 line-2 color-changer">{{ $item->question }}
                                                </h6>
                                                <p class="fs-13  text-muted">{{ $item->answer }}
                                                </p>
                                            </div>
                                        @endforeach
                                    @else
                                        @include('front.nodata')
                                    @endif

                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>


        <input type="hidden" name="vendor" id="detail_overview_vendor" value="{{ $getitem->vendor_id }}">
        <input type="hidden" name="item_id" id="detail_overview_item_id" value="{{ $getitem->id }}">
        <input type="hidden" name="item_name" id="detail_overview_item_name" value="{{ $getitem->item_name }}">
        <input type="hidden" name="item_image" id="detail_overview_item_image"
            value="{{ @$getitem['item_image']->image }}">
        <input type="hidden" name="detail_item_min_order" id="detail_item_min_order"
            value="{{ $getitem->min_order }}">
        <input type="hidden" name="detail_item_max_order" id="detail_item_max_order"
            value="{{ $getitem->max_order }}">
        <input type="hidden" name="item_price" id="detail_overview_item_price" value="{{ $getitem->item_price }}">
        <input type="hidden" name="item_original_price" id="detail_overview_item_original_price"
            value ="{{ $original_price }}">
        <input type="hidden" name="detail_tax" id="detail_item_tax" value="{{ $getitem->tax }}">
        <input type="hidden" name="detail_variants_name" id="detail_variants_name">
        <input type="hidden" name="stock_management" id="detail_stock_management"
            value="{{ $getitem->stock_management }}">
        <input type="hidden" id="addtocarturl" value="{{ url('/add-to-cart') }}">
    </section>
    @if (count($getrelateditems) > 0)
        <section class="mt-sm-3 mb-sm-5 mt-3 mb-3">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div
                            class="section-heading d-flex flex-wrap gap-2 align-items-center justify-content-between py-4 border-top">
                            <h4 class="text-dark color-changer text-truncate fw-600">{{ trans('labels.top_related_products') }}</h4>
                        </div>
                    </div>
                </div>
                @if (helper::appdata($storeinfo->id)->template == 1)
                    <div class="row g-2 g-md-3 ">
                        @foreach ($getrelateditems as $item)
                            @php
                                if ($item->top_deals == 1 && helper::top_deals($vdata) != null) {
                                    if (@helper::top_deals($vdata)->offer_type == 1) {
                                        if ($item['variation']->count() > 0) {
                                            if (
                                                $item['variation'][0]->price > @helper::top_deals($vdata)->offer_amount
                                            ) {
                                                $price =
                                                    $item['variation'][0]->price -
                                                    @helper::top_deals($vdata)->offer_amount;
                                            } else {
                                                $price = $item['variation'][0]->price;
                                            }
                                        } else {
                                            if ($item->item_price > @helper::top_deals($vdata)->offer_amount) {
                                                $price = $item->item_price - @helper::top_deals($vdata)->offer_amount;
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
                                                $item->item_price * (@helper::top_deals($vdata)->offer_amount / 100);
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
                                            <img src="{{ (!empty(@$item['item_image']->image) ? helper::image_path($item['item_image']->image) : helper::food_image($item->item_name, $item->id)) }}"
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
                                            <div class="d-flex col-sm-auto col-12 justify-content-end align-items-center">
                                                <button
                                                    class="btn-primary w-100 px-3 py-2 d-flex gap-2 fw-500 align-items-center justify-content-center fs-7 btn m-0"
                                                    type="button"
                                                    onclick="showitems('{{ $item->id }}','{{ $item_name }}','{{ $item->item_price }}')">
                                                    <div class="addcartbtn-{{ $item->id }}">
                                                        <i class="fa-regular fa-plus"></i>
                                                        {{ trans('labels.add_to_cart') }}
                                                    </div>
                                                    <div class="load showload-{{ $item->id }}" style="display:none">
                                                    </div>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @elseif (helper::appdata($storeinfo->id)->template == 2)
                    <div class="row g-3 ">
                        @foreach ($getrelateditems as $item)
                            @php
                                if ($item->top_deals == 1 && helper::top_deals($vdata) != null) {
                                    if (@helper::top_deals($vdata)->offer_type == 1) {
                                        if ($item['variation']->count() > 0) {
                                            if (
                                                $item['variation'][0]->price > @helper::top_deals($vdata)->offer_amount
                                            ) {
                                                $price =
                                                    $item['variation'][0]->price -
                                                    @helper::top_deals($vdata)->offer_amount;
                                            } else {
                                                $price = $item['variation'][0]->price;
                                            }
                                        } else {
                                            if ($item->item_price > @helper::top_deals($vdata)->offer_amount) {
                                                $price = $item->item_price - @helper::top_deals($vdata)->offer_amount;
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
                                                $item->item_price * (@helper::top_deals($vdata)->offer_amount / 100);
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
                            <div class="col-sm-6 col-md-6 col-lg-4 col-xl-3">
                                <div class="card border-0 rounded theme-2-products-card h-100">
                                    <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}">
                                        <div class="theme_2_grid_img overflow-hidden">
                                            <img src="{{ (!empty(@$item['item_image']->image) ? helper::image_path($item['item_image']->image) : helper::food_image($item->item_name, $item->id)) }}"
                                                class="img-fluid" alt="...">
                                            @if ($off > 0)
                                                <span
                                                    class="offer-text rounded fw-500 text-bg-primary fs-8">{{ $off }}%
                                                    {{ trans('labels.off') }}</span>
                                            @endif
                                        </div>
                                    </a>
                                    <div class="card-body px-2 px-md-3 pb-0">
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
                                            class="title color-changer">
                                            {{ $item->item_name }}</a>
                                    </div>
                                    <div class="card-footer px-2 px-md-3 pt-0">
                                        <div class="d-flex justify-content-between align-items-center gap-2">
                                            <div class="mb-2 mb-md-0">
                                                <div class="products-price d-flex gap-1 align-items-center">
                                                    <span
                                                        class="price color-changer">{{ helper::currency_formate($price, @$storeinfo->id) }}</span>
                                                    @if ($item->item_original_price != null)
                                                        @if ($original_price > $price)
                                                            <del class="text-muted">{{ helper::currency_formate($original_price, @$storeinfo->id) }}</del>
                                                        @endif
                                                    @endif

                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-end mb-2 mb-md-0">
                                                <div class="d-flex gap-2 align-items-center justify-content-between">
                                                    <div class="load showload-{{ $item->id }}" style="display:none">
                                                    </div>
                                                    <button
                                                        class="theme-2-product-icon m-0 btn border-0 addcartbtn-{{ $item->id }}"
                                                        type="button"
                                                        onclick="showitems('{{ $item->id }}','{{ $item_name }}','{{ $item->item_price }}')">
                                                        <i class="fa-solid fa-cart-shopping"></i>
                                                    </button>

                                                    @if (Auth::user() && Auth::user()->type == 3)
                                                        <div class="set-fav1-{{ $item->id }}">
                                                            @if ($item->is_favorite == 1)
                                                                <a class="theme-2-product-icon" href="javascript:void(0)"
                                                                    onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',0,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')">
                                                                    <i class="fa-solid fa-heart"></i>
                                                                </a>
                                                            @else
                                                                <a class="theme-2-product-icon" href="javascript:void(0)"
                                                                    onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',1,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')">
                                                                    <i class="fa-regular fa-heart"></i>
                                                                </a>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @elseif (helper::appdata($storeinfo->id)->template == 3)
                    <div class="row row-cols-xl-5 row-cols-lg-4 row-cols-md-3 row-cols-sm-2 row-cols-2 g-3 ">
                        @foreach ($getrelateditems as $item)
                            @php
                                if ($item->top_deals == 1 && helper::top_deals($vdata) != null) {
                                    if (@helper::top_deals($vdata)->offer_type == 1) {
                                        if ($item['variation']->count() > 0) {
                                            if (
                                                $item['variation'][0]->price > @helper::top_deals($vdata)->offer_amount
                                            ) {
                                                $price =
                                                    $item['variation'][0]->price -
                                                    @helper::top_deals($vdata)->offer_amount;
                                            } else {
                                                $price = $item['variation'][0]->price;
                                            }
                                        } else {
                                            if ($item->item_price > @helper::top_deals($vdata)->offer_amount) {
                                                $price = $item->item_price - @helper::top_deals($vdata)->offer_amount;
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
                                                $item->item_price * (@helper::top_deals($vdata)->offer_amount / 100);
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
                            <div class="col">
                                <div class="card thme3girdproduct h-100">
                                    <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}">
                                        <img src="{{ (!empty(@$item['item_image']->image) ? helper::image_path($item['item_image']->image) : helper::food_image($item->item_name, $item->id)) }}"
                                            class="card-img-top" alt="...">
                                        @if ($off > 0)
                                            <span
                                                class="offer-text rounded fw-500 text-bg-primary fs-8">{{ $off }}%
                                                {{ trans('labels.off') }}</span>
                                        @endif
                                    </a>
                                    <div class="card-body px-2 px-md-2 pb-md-2 py-0">
                                        <div class="text-section">
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
                                                class="title color-changer pb-1">
                                                {{ $item->item_name }}</a>
                                        </div>
                                    </div>
                                    <div class="card-footer px-2 px-md-2 pb-2 py-0">
                                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                                            <div class="products-price d-flex flex-wrap align-items-center gap-1">
                                                <span
                                                    class="price color-changer">{{ helper::currency_formate($price, @$storeinfo->id) }}</span>
                                                @if ($item->item_original_price != null)
                                                    @if ($original_price > $price)
                                                        <del class="text-muted">{{ helper::currency_formate($original_price, @$storeinfo->id) }}</del>
                                                    @endif
                                                @endif
                                            </div>
                                            <div class="d-flex justify-content-end">

                                                <div class="load showload-{{ $item->id }}" style="display:none">
                                                </div>
                                                <a class="theme-3-product-icon m-0 addcartbtn-{{ $item->id }}"
                                                    href="javascript:void(0)"
                                                    onclick="showitems('{{ $item->id }}','{{ $item_name }}','{{ $item->item_price }}')">
                                                    <i class="fa-solid fa-plus"></i>
                                                </a>

                                                @if (Auth::user() && Auth::user()->type == 3)
                                                    <div class="favorite-icon set-fav1-{{ $item->id }}">
                                                        @if ($item->is_favorite == 1)
                                                            <a href="javascript:void(0)"
                                                                onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',0,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')">
                                                                <i class="fa-solid fa-heart"></i>
                                                            </a>
                                                        @else
                                                            <a href="javascript:void(0)"
                                                                onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',1,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')">
                                                                <i class="fa-regular fa-heart"></i>
                                                            </a>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @elseif (helper::appdata($storeinfo->id)->template == 4)
                    <div class="row g-3 theme-4-gridproduct-card ">
                        @foreach ($getrelateditems as $item)
                            @php
                                if ($item->top_deals == 1 && helper::top_deals($vdata) != null) {
                                    if (@helper::top_deals($vdata)->offer_type == 1) {
                                        if ($item['variation']->count() > 0) {
                                            if (
                                                $item['variation'][0]->price > @helper::top_deals($vdata)->offer_amount
                                            ) {
                                                $price =
                                                    $item['variation'][0]->price -
                                                    @helper::top_deals($vdata)->offer_amount;
                                            } else {
                                                $price = $item['variation'][0]->price;
                                            }
                                        } else {
                                            if ($item->item_price > @helper::top_deals($vdata)->offer_amount) {
                                                $price = $item->item_price - @helper::top_deals($vdata)->offer_amount;
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
                                                $item->item_price * (@helper::top_deals($vdata)->offer_amount / 100);
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
                            <div class="col-6 col-md-4 col-lg-4 col-xl-3">
                                <div class="card card-bg border-0 h-100">
                                    <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}">
                                        <img src="{{ (!empty(@$item['item_image']->image) ? helper::image_path($item['item_image']->image) : helper::food_image($item->item_name, $item->id)) }}"
                                            class="card-img-top" alt="...">
                                    </a>
                                    @if ($off > 0)
                                        <span
                                            class="offer-text rounded fw-500 text-bg-secondary fs-8">{{ $off }}%
                                            {{ trans('labels.off') }}</span>
                                    @endif
                                    <div class="card-body">
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
                                            class="title color-changer pb-1">
                                            {{ $item->item_name }}</a>
                                    </div>
                                    <div class="card-footer px-0 pt-0">
                                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                                            <div class="products-price d-flex flex-wrap align-items-center gap-1">
                                                <span
                                                    class="price color-changer">{{ helper::currency_formate($price, @$storeinfo->id) }}</span>
                                                @if ($item->item_original_price != null)
                                                    @if ($original_price > $price)
                                                        <del class="text-muted">{{ helper::currency_formate($original_price, @$storeinfo->id) }}</del>
                                                    @endif
                                                @endif
                                            </div>
                                            <div class="">
                                                <div class="load showload-{{ $item->id }}" style="display:none">
                                                </div>
                                                <a type="button" class="addcartbtn-{{ $item->id }} color-changer"
                                                    onclick="showitems('{{ $item->id }}','{{ $item_name }}','{{ $item->item_price }}')">
                                                    <i class="fa-solid fa-circle-plus fs-5"></i>
                                                </a>

                                                @if (Auth::user() && Auth::user()->type == 3)
                                                    <div class="favorite-icon set-fav1-{{ $item->id }}">
                                                        @if ($item->is_favorite == 1)
                                                            <a href="javascript:void(0)" class="text-secondary"
                                                                onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',0,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')"><i
                                                                    class="fa-solid fa-heart"></i></a>
                                                        @else
                                                            <a href="javascript:void(0)" class="text-secondary"
                                                                onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',1,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')"><i
                                                                    class="fa-regular fa-heart"></i></a>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @elseif (helper::appdata($storeinfo->id)->template == 5)
                    <div class="row row-cols-xl-5 row-cols-lg-4 row-cols-md-3 row-cols-sm-2 row-cols-2 g-3 ">
                        @foreach ($getrelateditems as $item)
                            @php
                                if ($item->top_deals == 1 && helper::top_deals($vdata) != null) {
                                    if (@helper::top_deals($vdata)->offer_type == 1) {
                                        if ($item['variation']->count() > 0) {
                                            if (
                                                $item['variation'][0]->price > @helper::top_deals($vdata)->offer_amount
                                            ) {
                                                $price =
                                                    $item['variation'][0]->price -
                                                    @helper::top_deals($vdata)->offer_amount;
                                            } else {
                                                $price = $item['variation'][0]->price;
                                            }
                                        } else {
                                            if ($item->item_price > @helper::top_deals($vdata)->offer_amount) {
                                                $price = $item->item_price - @helper::top_deals($vdata)->offer_amount;
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
                                                $item->item_price * (@helper::top_deals($vdata)->offer_amount / 100);
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
                            <div class="col">
                                <div class="card thme3girdproduct h-100">
                                    <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}">
                                        <img src="{{ (!empty(@$item['item_image']->image) ? helper::image_path($item['item_image']->image) : helper::food_image($item->item_name, $item->id)) }}"
                                            class="card-img-top" alt="...">
                                    </a>
                                    @if ($off > 0)
                                        <span class="offer-text rounded fw-500 text-bg-primary fs-8">{{ $off }}%
                                            {{ trans('labels.off') }}</span>
                                    @endif
                                    <div class="card-body px-2 px-md-2 pb-md-2 py-0">
                                        <div class="text-section">
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
                                                class="title color-changer pb-1">
                                                {{ $item->item_name }}</a>
                                        </div>
                                    </div>
                                    <div class="card-footer px-2 px-md-2 pb-2 py-0">
                                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                                            <div class="">
                                                <div class="products-price d-flex flex-wrap align-items-center gap-1">
                                                    <span
                                                        class="price color-changer">{{ helper::currency_formate($price, @$storeinfo->id) }}</span>
                                                    @if ($item->item_original_price != null)
                                                        @if ($original_price > $price)
                                                            <del class="text-muted">{{ helper::currency_formate($original_price, @$storeinfo->id) }}</del>
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>
                                            <div class=" d-flex justify-content-end">

                                                <div class="load showload-{{ $item->id }}" style="display:none">
                                                </div>
                                                <a class="theme-3-product-icon m-0 addcartbtn-{{ $item->id }}"
                                                    href="javascript:void(0)"
                                                    onclick="showitems('{{ $item->id }}','{{ $item_name }}','{{ $item->item_price }}')">
                                                    <i class="fa-solid fa-plus"></i>
                                                </a>

                                                @if (Auth::user() && Auth::user()->type == 3)
                                                    <div class="favorite-icon set-fav1-{{ $item->id }}">
                                                        @if ($item->is_favorite == 1)
                                                            <a href="javascript:void(0)" class="text-secondary"
                                                                onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',0,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')"><i
                                                                    class="fa-solid fa-heart"></i></a>
                                                        @else
                                                            <a href="javascript:void(0)" class="text-secondary"
                                                                onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',1,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')"><i
                                                                    class="fa-regular fa-heart"></i></a>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @elseif (helper::appdata($storeinfo->id)->template == 6)
                    <div
                        class="row row-cols-xl-5 row-cols-lg-4 row-cols-md-3 row-cols-sm-2 row-cols-2 g-3 pt-4 theme-6-margin-top">
                        @foreach ($getrelateditems as $item)
                            @php
                                if ($item->top_deals == 1 && helper::top_deals($vdata) != null) {
                                    if (@helper::top_deals($vdata)->offer_type == 1) {
                                        if ($item['variation']->count() > 0) {
                                            if (
                                                $item['variation'][0]->price > @helper::top_deals($vdata)->offer_amount
                                            ) {
                                                $price =
                                                    $item['variation'][0]->price -
                                                    @helper::top_deals($vdata)->offer_amount;
                                            } else {
                                                $price = $item['variation'][0]->price;
                                            }
                                        } else {
                                            if ($item->item_price > @helper::top_deals($vdata)->offer_amount) {
                                                $price = $item->item_price - @helper::top_deals($vdata)->offer_amount;
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
                                                $item->item_price * (@helper::top_deals($vdata)->offer_amount / 100);
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
                            <div class="col products-img">
                                <div class="card h-100 border-0 bg-light position-relative rounded-none">
                                    <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}">
                                        <div class="overflow-hidden theme6grid_image">
                                            <img src="{{ (!empty(@$item['item_image']->image) ? helper::image_path($item['item_image']->image) : helper::food_image($item->item_name, $item->id)) }}"
                                                alt="" class="p-2 p-md-3">
                                        </div>
                                    </a>
                                    <div class="card-body px-sm-3 px-2 py-0">
                                        @if ($off > 0)
                                            <span
                                                class="offer-text rounded fw-500 text-bg-secondary fs-8">{{ $off }}%
                                                {{ trans('labels.off') }}</span>
                                        @endif
                                        @if (Auth::user() && Auth::user()->type == 3)
                                            <div class="favorite-icon set-fav1-{{ $item->id }}">
                                                @if ($item->is_favorite == 1)
                                                    <a href="javascript:void(0)"
                                                        onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',0,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')">
                                                        <i class="fa-solid fa-heart"></i>
                                                    </a>
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
                                            class="title pb-1 color-changer hover-white line-limit-1">
                                            {{ $item->item_name }}</a>
                                    </div>
                                    <div class="card-footer bg-transparent border-0 px-sm-3 px-2 py-2">
                                        <div class="d-flex justify-content-between align-items-center gap-2 flex-wrap">
                                            <div class="d-flex flex-wrap gap-1 align-items-center">
                                                <p class="price color-changer">
                                                    {{ helper::currency_formate($price, @$storeinfo->id) }}
                                                </p>
                                                @if ($item->item_original_price != null)
                                                    @if ($original_price > $price)
                                                        <del
                                                            class="hover-white">{{ helper::currency_formate($original_price, @$storeinfo->id) }}</del>
                                                    @endif
                                                @endif
                                            </div>
                                            <div class="d-flex justify-content-end">
                                                <div class="load showload-{{ $item->id }}" style="display:none">
                                                </div>
                                                <button
                                                    class="btn-primary d-flex justify-content-center align-items-center btn m-0 product-cart-icon rounded-0 addcartbtn-{{ $item->id }}"
                                                    type="button"
                                                    onclick="showitems('{{ $item->id }}','{{ $item_name }}','{{ $item->item_price }}')">
                                                    <i class="fa-solid fa-cart-shopping"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @elseif (helper::appdata($storeinfo->id)->template == 7)
                    <div class="row g-3  theme-7-margin-top">
                        @foreach ($getrelateditems as $item)
                            @php
                                if ($item->top_deals == 1 && helper::top_deals($vdata) != null) {
                                    if (@helper::top_deals($vdata)->offer_type == 1) {
                                        if ($item['variation']->count() > 0) {
                                            if (
                                                $item['variation'][0]->price > @helper::top_deals($vdata)->offer_amount
                                            ) {
                                                $price =
                                                    $item['variation'][0]->price -
                                                    @helper::top_deals($vdata)->offer_amount;
                                            } else {
                                                $price = $item['variation'][0]->price;
                                            }
                                        } else {
                                            if ($item->item_price > @helper::top_deals($vdata)->offer_amount) {
                                                $price = $item->item_price - @helper::top_deals($vdata)->offer_amount;
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
                                                $item->item_price * (@helper::top_deals($vdata)->offer_amount / 100);
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
                            <div class="col-6 col-lg-4 col-xl-3">
                                <div class="card h-100 border-0 bg-light position-relative rounded">
                                    <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}">
                                        <div class="overflow-hidden theme7grid_image">
                                            <img src="{{ (!empty(@$item['item_image']->image) ? helper::image_path($item['item_image']->image) : helper::food_image($item->item_name, $item->id)) }}"
                                                alt="" class="p-2 p-md-3">
                                        </div>
                                    </a>
                                    <div class="card-body p-2 p-md-3 pb-0 pb-md-3 text-center">
                                        <div class="d-flex justify-content-between flex-wrap align-items-center mb-2">
                                            @if ($off > 0)
                                                <span
                                                    class="p-1 px-2 rounded fw-500 text-bg-primary fs-8">{{ $off }}%
                                                    {{ trans('labels.off') }}</span>
                                            @endif
                                            @if (App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first() != null &&
                                                    App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first()->activated == 1)
                                                @if (App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first() != null &&
                                                        App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first()->activated == 1)
                                                    @if (helper::appdata($storeinfo->id)->checkout_login_required == 1)
                                                        <a class="fs-8 d-flex gap-1 align-items-center"
                                                            onclick="showreviews('{{ $item->id }}')" role="button"
                                                            aria-controls="offcanvasExample">
                                                            <i class="fa-solid fa-star text-warning"></i>
                                                            <p class="cursor-pointer color-changer fw-600 fs-8">
                                                                {{ number_format($item->avg_ratting, 1) }}</p>
                                                        </a>
                                                    @endif
                                                @endif
                                            @endif
                                        </div>
                                        <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}"
                                            class="title pb-1 color-changer hover-white">
                                            {{ $item->item_name }}</a>
                                    </div>
                                    <div class="card-footer bg-transparent border-0 pb-3 pt-0">
                                        <div class="row justify-content-center align-items-center gx-0">
                                            <div class="d-flex flex-wrap gap-1 align-items-center justify-content-center">
                                                <p class="price color-changer">
                                                    {{ helper::currency_formate($price, @$storeinfo->id) }}
                                                </p>
                                                @if ($item->item_original_price != null)
                                                    @if ($original_price > $price)
                                                        <del
                                                            class="hover-white">{{ helper::currency_formate($original_price, @$storeinfo->id) }}</del>
                                                    @endif
                                                @endif
                                            </div>
                                            <div class="">
                                                <ul class="card-option d-flex gap-sm-2 gap-1">
                                                    <li tooltip="{{ trans('labels.addcart') }}"
                                                        onclick="showitems('{{ $item->id }}','{{ $item_name }}','{{ $item->item_price }}')"
                                                        class="m-0 p-0 cursor-pointer">
                                                        <div class="load showload-{{ $item->id }}"
                                                            style="display:none"></div>
                                                        <button
                                                            class="product-cart-icon p-0 addcartbtn-{{ $item->id }}"
                                                            type="button">
                                                            <i class="fa-solid fa-cart-shopping fs-7"></i>
                                                        </button>

                                                    </li>
                                                    @if (Auth::user() && Auth::user()->type == 3)
                                                        <li tooltip="Wishlist" class="m-0 p-0">
                                                            <div
                                                                class="product-cart-icon p-0 d-flex justify-content-center align-items-center set-fav1-{{ $item->id }}">
                                                                @if ($item->is_favorite == 1)
                                                                    <a href="javascript:void(0)"
                                                                        onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',0,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')">
                                                                        <i class="fa-solid fa-heart"></i>
                                                                    </a>
                                                                @else
                                                                    <a href="javascript:void(0)"
                                                                        onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',1,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')">
                                                                        <i class="fa-regular fa-heart"></i>
                                                                    </a>
                                                                @endif
                                                            </div>
                                                        </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @elseif (helper::appdata($storeinfo->id)->template == 8)
                    <div class="row g-3  theme-8-margin-top">
                        @foreach ($getrelateditems as $item)
                            @php
                                if ($item->top_deals == 1 && helper::top_deals($vdata) != null) {
                                    if (@helper::top_deals($vdata)->offer_type == 1) {
                                        if ($item['variation']->count() > 0) {
                                            if (
                                                $item['variation'][0]->price > @helper::top_deals($vdata)->offer_amount
                                            ) {
                                                $price =
                                                    $item['variation'][0]->price -
                                                    @helper::top_deals($vdata)->offer_amount;
                                            } else {
                                                $price = $item['variation'][0]->price;
                                            }
                                        } else {
                                            if ($item->item_price > @helper::top_deals($vdata)->offer_amount) {
                                                $price = $item->item_price - @helper::top_deals($vdata)->offer_amount;
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
                                                $item->item_price * (@helper::top_deals($vdata)->offer_amount / 100);
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
                            <div class="col-6 col-lg-4 col-xl-3 products-img">
                                <div class="card h-100 border-0 position-relative">
                                    <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}">
                                        <div class="overflow-hidden">
                                            <img src="{{ (!empty(@$item['item_image']->image) ? helper::image_path($item['item_image']->image) : helper::food_image($item->item_name, $item->id)) }}"
                                                alt="" class="p-2 p-md-3">
                                        </div>
                                    </a>
                                    <div class="card-body p-2 p-md-3 pb-0 pb-md-3 text-center">
                                        <div class="d-flex justify-content-between flex-wrap align-items-center mb-2">
                                            @if ($off > 0)
                                                <span
                                                    class="p-1 px-2 rounded fw-500 text-bg-primary fs-8">{{ $off }}%
                                                    {{ trans('labels.off') }}</span>
                                            @endif
                                            @if (App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first() != null &&
                                                    App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first()->activated == 1)
                                                @if (App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first() != null &&
                                                        App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first()->activated == 1)
                                                    @if (helper::appdata($storeinfo->id)->checkout_login_required == 1)
                                                        <a class="fs-8 d-flex gap-1 align-items-center"
                                                            onclick="showreviews('{{ $item->id }}')" role="button"
                                                            aria-controls="offcanvasExample">
                                                            <i class="fa-solid fa-star text-warning"></i>
                                                            <p class="cursor-pointer color-changer fw-600 fs-8">
                                                                {{ number_format($item->avg_ratting, 1) }}</p>
                                                        </a>
                                                    @endif
                                                @endif
                                            @endif
                                        </div>
                                        <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}"
                                            class="title pb-1 color-changer hover-white">
                                            {{ $item->item_name }}</a>
                                    </div>
                                    <div class="card-footer bg-transparent border-0 pb-3 pt-0">
                                        <div class="row justify-content-center align-items-center gx-0">
                                            <div class="d-flex flex-wrap align-items-center justify-content-center gap-1">
                                                <p class="price color-changer">
                                                    {{ helper::currency_formate($price, @$storeinfo->id) }}
                                                </p>
                                                @if ($item->item_original_price != null)
                                                    @if ($original_price > $price)
                                                        <del
                                                            class="hover-white text-muted">{{ helper::currency_formate($original_price, @$storeinfo->id) }}</del>
                                                    @endif
                                                @endif
                                            </div>
                                            <div class=" text-center">
                                                <ul class="card-option d-flex justify-content-center">
                                                    <li tooltip="{{ trans('labels.addcart') }}"
                                                        onclick="showitems('{{ $item->id }}','{{ $item_name }}','{{ $item->item_price }}')"
                                                        class="m-0 p-0 cursor-pointer">
                                                        <div class="load showload-{{ $item->id }}"
                                                            style="display:none"></div>
                                                        <button
                                                            class="product-cart-icon p-0 addcartbtn-{{ $item->id }}"
                                                            type="button">
                                                            <i class="fa-solid fa-cart-shopping"></i>
                                                        </button>
                                                    </li>
                                                    @if (Auth::user() && Auth::user()->type == 3)
                                                        <li tooltip="Wishlist" class="m-0 p-0">
                                                            <div class="set-fav-38">
                                                                <div
                                                                    class="product-cart-icon p-0 d-flex align-items-center justify-content-center set-fav1-{{ $item->id }}">
                                                                    @if ($item->is_favorite == 1)
                                                                        <a href="javascript:void(0)" class="d-flex"
                                                                            onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',0,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')">
                                                                            <i class="fa-solid fa-heart"></i>
                                                                        </a>
                                                                    @else
                                                                        <a href="javascript:void(0)" class="d-flex"
                                                                            onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',1,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')">
                                                                            <i class="fa-regular fa-heart"></i>
                                                                        </a>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @elseif (helper::appdata($storeinfo->id)->template == 9)
                    <div
                        class="row row-cols-xl-5 row-cols-lg-4 row-cols-md-3 row-cols-sm-2 row-cols-2 g-3  theme-9-margin-top">
                        @foreach ($getrelateditems as $item)
                            @php
                                if ($item->top_deals == 1 && helper::top_deals($vdata) != null) {
                                    if (@helper::top_deals($vdata)->offer_type == 1) {
                                        if ($item['variation']->count() > 0) {
                                            if (
                                                $item['variation'][0]->price > @helper::top_deals($vdata)->offer_amount
                                            ) {
                                                $price =
                                                    $item['variation'][0]->price -
                                                    @helper::top_deals($vdata)->offer_amount;
                                            } else {
                                                $price = $item['variation'][0]->price;
                                            }
                                        } else {
                                            if ($item->item_price > @helper::top_deals($vdata)->offer_amount) {
                                                $price = $item->item_price - @helper::top_deals($vdata)->offer_amount;
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
                                                $item->item_price * (@helper::top_deals($vdata)->offer_amount / 100);
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
                            @endphp
                            <div class="col">
                                <div class="card card-bg thme9categories dark h-100 border-0">
                                    <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}">
                                        <img src="{{ (!empty(@$item['item_image']->image) ? helper::image_path($item['item_image']->image) : helper::food_image($item->item_name, $item->id)) }}"
                                            class="card-img-top m-0 img-hight-fiexed" alt="...">
                                        @if ($off > 0)
                                            <span
                                                class="offer-text rounded fw-500 text-bg-secondary fs-8">{{ $off }}%
                                                {{ trans('labels.off') }}</span>
                                        @endif
                                    </a>
                                    <div class="card-body px-0 pb-1">
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
                                        <div class="text-section">
                                            <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}"
                                                class="title fs-6 color-changer pb-1 hover-white">
                                                {{ $item->item_name }}</a>
                                        </div>
                                    </div>
                                    @if (Auth::user() && Auth::user()->type == 3)
                                        <div class="theme-9-favorite-icon grid-icon p-1 set-fav1-{{ $item->id }}">
                                            @if ($item->is_favorite == 1)
                                                <a href="javascript:void(0)"
                                                    onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',0,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')">
                                                    <i class="fa-solid fa-heart"></i>
                                                </a>
                                            @else
                                                <a href="javascript:void(0)"
                                                    onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',1,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')">
                                                    <i class="fa-regular fa-heart"></i>
                                                </a>
                                            @endif
                                        </div>
                                    @endif
                                    <div
                                        class="card-footer d-sm-flex align-items-center justify-content-between bg-transparent border-0 p-0">
                                        <div class="products-price d-flex align-items-center gap-1 flex-wrap">
                                            <span class="price color-changer">
                                                {{ helper::currency_formate($price, @$storeinfo->id) }}</span>
                                            @if ($item->item_original_price != null)
                                                @if ($original_price > $price)
                                                    <del
                                                        class="hover-white text-muted">{{ helper::currency_formate($original_price, @$storeinfo->id) }}</del>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @elseif (helper::appdata($storeinfo->id)->template == 10)
                    <div class="row g-3 ">
                        @foreach ($getrelateditems as $item)
                            @php
                                if ($item->top_deals == 1 && helper::top_deals($vdata) != null) {
                                    if (@helper::top_deals($vdata)->offer_type == 1) {
                                        if ($item['variation']->count() > 0) {
                                            if (
                                                $item['variation'][0]->price > @helper::top_deals($vdata)->offer_amount
                                            ) {
                                                $price =
                                                    $item['variation'][0]->price -
                                                    @helper::top_deals($vdata)->offer_amount;
                                            } else {
                                                $price = $item['variation'][0]->price;
                                            }
                                        } else {
                                            if ($item->item_price > @helper::top_deals($vdata)->offer_amount) {
                                                $price = $item->item_price - @helper::top_deals($vdata)->offer_amount;
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
                                                $item->item_price * (@helper::top_deals($vdata)->offer_amount / 100);
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
                            <div class="col-6 col-lg-4 col-xl-3">
                                <div class="card h-100 border-secondary bg-transparent position-relative">
                                    <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}">
                                        <div class="overflow-hidden">
                                            <img src="{{ (!empty(@$item['item_image']->image) ? helper::image_path($item['item_image']->image) : helper::food_image($item->item_name, $item->id)) }}"
                                                alt="" class="p-2 p-md-3 theme-10-product-img">
                                        </div>
                                        @if ($off > 0)
                                            <span
                                                class="offer-text rounded fw-500 text-bg-secondary fs-8">{{ $off }}%
                                                {{ trans('labels.off') }}</span>
                                        @endif
                                    </a>
                                    <div class="card-body px-2 px-md-3 py-0 py-md-0">
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
                                            class="title mb-2 color-changer hover-white">
                                            {{ $item->item_name }}</a>
                                        <div class="d-flex flex-wrap gap-1 align-items-center">
                                            <p class="price color-changer">
                                                {{ helper::currency_formate($price, @$storeinfo->id) }}
                                            </p>

                                            @if ($item->item_original_price != null)
                                                @if ($original_price > $price)
                                                    <del
                                                        class="hover-white">{{ helper::currency_formate($original_price, @$storeinfo->id) }}</del>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                    <div class="card-footer bg-transparent border-0 px-2 px-md-3 pb-3 pt-0">
                                        <div>
                                            <div class="text-center mt-3">
                                                <ul class="mb-0">
                                                    <li>
                                                        <div class="theme-10-load">
                                                            <div class="load-2 showload-{{ $item->id }} "
                                                                style="display:none"></div>
                                                        </div>
                                                        <button
                                                            class="theme-10-product-cart-icon addcartbtn-{{ $item->id }}"
                                                            type="button"
                                                            onclick="showitems('{{ $item->id }}','{{ $item_name }}','{{ $item->item_price }}')">
                                                            {{ trans('labels.addcart') }}
                                                        </button>
                                                    </li>
                                                    @if (Auth::user() && Auth::user()->type == 3)
                                                        <li>
                                                            <div class="set-fav-38 theme-10-heart-option">
                                                                <div
                                                                    class="theme-10-product-heart-icon set-fav1-{{ $item->id }}">
                                                                    @if ($item->is_favorite == 1)
                                                                        <a href="javascript:void(0)"
                                                                            onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',0,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')">
                                                                            <i class="fa-solid fa-heart"></i>
                                                                        </a>
                                                                    @else
                                                                        <a href="javascript:void(0)"
                                                                            onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',1,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')">
                                                                            <i class="fa-regular fa-heart"></i>
                                                                        </a>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @elseif (helper::appdata($storeinfo->id)->template == 11)
                    <div class="row row-cols-xl-5 row-cols-lg-4 row-cols-md-3 row-cols-sm-2 row-cols-2 g-3 ">
                        @foreach ($getrelateditems as $item)
                            @php
                                if ($item->top_deals == 1 && helper::top_deals($vdata) != null) {
                                    if (@helper::top_deals($vdata)->offer_type == 1) {
                                        if ($item['variation']->count() > 0) {
                                            if (
                                                $item['variation'][0]->price > @helper::top_deals($vdata)->offer_amount
                                            ) {
                                                $price =
                                                    $item['variation'][0]->price -
                                                    @helper::top_deals($vdata)->offer_amount;
                                            } else {
                                                $price = $item['variation'][0]->price;
                                            }
                                        } else {
                                            if ($item->item_price > @helper::top_deals($vdata)->offer_amount) {
                                                $price = $item->item_price - @helper::top_deals($vdata)->offer_amount;
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
                                                $item->item_price * (@helper::top_deals($vdata)->offer_amount / 100);
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
                            <div class="col theme-11">
                                <div class="product-grid text-center card h-100 rounded-3">
                                    <div class="product-image position-relative">
                                        <a class="image">
                                            <img class="pic-1"
                                                src="{{ @helper::image_path($item['multi_image'][0]->image) }}"
                                                class="img-fluid" alt="...">
                                            <img class="pic-2"
                                                src="{{ @$item['multi_image']->count() > 1 ? @helper::image_path($item['multi_image'][1]->image) : @helper::image_path($item['multi_image'][0]->image) }}">
                                        </a>
                                        <span class="product-discount-label">
                                            @if (App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first() != null &&
                                                    App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first()->activated == 1)
                                                @if (App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first() != null &&
                                                        App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first()->activated == 1)
                                                    @if (helper::appdata($storeinfo->id)->checkout_login_required == 1)
                                                        <a class="fs-8 d-flex gap-1 align-items-center p-1"
                                                            onclick="showreviews('{{ $item->id }}')" role="button"
                                                            aria-controls="offcanvasExample">
                                                            <i class="fa-solid fa-star text-warning"></i>
                                                            <p class="cursor-pointer text-white fw-600 fs-8">
                                                                {{ number_format($item->avg_ratting, 1) }}</p>
                                                        </a>
                                                    @endif
                                                @endif
                                            @endif
                                        </span>
                                        @if ($off > 0)
                                            <span class="theme-11-ribbon">
                                                <h3>{{ $off }}% {{ trans('labels.off') }}</h3>
                                            </span>
                                        @endif
                                        <ul class="product-links d-flex gap-1 justify-content-center">
                                            @if (Auth::user() && Auth::user()->type == 3)
                                                <li class="set-fav1-{{ $item->id }}">
                                                    @if ($item->is_favorite == 1)
                                                        <a class="theme-2-product-icon" href="javascript:void(0)"
                                                            onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',0,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')">
                                                            <i class="fa-solid fa-heart"></i>
                                                        </a>
                                                    @else
                                                        <a class="theme-2-product-icon" href="javascript:void(0)"
                                                            onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',1,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')">
                                                            <i class="fa-regular fa-heart"></i>
                                                        </a>
                                                    @endif
                                                </li>
                                            @endif
                                            <li>
                                                <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}">
                                                    <i class="fa-regular fa-eye"></i>
                                                </a>
                                            </li>
                                            <li>
                                                <a type="button"
                                                    onclick="showitems('{{ $item->id }}','{{ $item_name }}','{{ $item->item_price }}')">
                                                    <div class="load showload-{{ $item->id }}" style="display:none">
                                                    </div>
                                                    <i
                                                        class="fa-solid fa-cart-shopping addcartbtn-{{ $item->id }}"></i>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="card-body product-content pb-0 p-2">
                                        <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}"
                                            class="fs-7 fw-500 color-changer line-2">
                                            {{ $item->item_name }}
                                        </a>
                                    </div>
                                    <div class="card-footer p-2">
                                        <div
                                            class="products-price d-flex flex-wrap gap-1 justify-content-center align-items-center">
                                            <span class="fs-15 fw-600 text-primary color-changer">
                                                {{ helper::currency_formate($price, @$storeinfo->id) }}
                                            </span>
                                            @if ($item->item_original_price != null)
                                                @if ($original_price > $price)
                                                    <del>{{ helper::currency_formate($original_price, @$storeinfo->id) }}</del>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @elseif (helper::appdata($storeinfo->id)->template == 12)
                    <div class="row g-3 row-cols-xl-5 row-cols-lg-4 row-cols-md-3 row-cols-sm-2 row-cols-2 ">
                        @foreach ($getrelateditems as $item)
                            @php
                                if ($item->top_deals == 1 && helper::top_deals($vdata) != null) {
                                    if (@helper::top_deals($vdata)->offer_type == 1) {
                                        if ($item['variation']->count() > 0) {
                                            if (
                                                $item['variation'][0]->price > @helper::top_deals($vdata)->offer_amount
                                            ) {
                                                $price =
                                                    $item['variation'][0]->price -
                                                    @helper::top_deals($vdata)->offer_amount;
                                            } else {
                                                $price = $item['variation'][0]->price;
                                            }
                                        } else {
                                            if ($item->item_price > @helper::top_deals($vdata)->offer_amount) {
                                                $price = $item->item_price - @helper::top_deals($vdata)->offer_amount;
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
                                                $item->item_price * (@helper::top_deals($vdata)->offer_amount / 100);
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
                            <div class="col theme-12">
                                <div class="product-grid card shadow border-0 rounded-5 overflow-visible h-100">
                                    <div class="product-image">
                                        <a class="image">
                                            <img class="pic-1"
                                                src="{{ @helper::image_path($item['multi_image'][0]->image) }}"
                                                class="img-fluid" alt="...">
                                            <img class="pic-2"
                                                src="{{ @$item['multi_image']->count() > 1 ? @helper::image_path($item['multi_image'][1]->image) : @helper::image_path($item['multi_image'][0]->image) }}">
                                        </a>
                                        @if ($off > 0)
                                            <span class="product-hot-label">
                                                {{ $off }}% {{ trans('labels.off') }}
                                            </span>
                                        @endif
                                        <span class="product-sale-label rounded-5">
                                            @if (App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first() != null &&
                                                    App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first()->activated == 1)
                                                @if (App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first() != null &&
                                                        App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first()->activated == 1)
                                                    @if (helper::appdata($storeinfo->id)->checkout_login_required == 1)
                                                        <a class="fs-8 text-white d-flex gap-1 align-items-center"
                                                            onclick="showreviews('{{ $item->id }}')" role="button"
                                                            aria-controls="offcanvasExample">
                                                            <i class="fa-solid fa-star text-warning"></i>
                                                            <p class="cursor-pointer fw-600 fs-8">
                                                                {{ number_format($item->avg_ratting, 1) }}</p>
                                                        </a>
                                                    @endif
                                                @endif
                                            @endif
                                        </span>
                                        <ul class="product-links">
                                            @if (Auth::user() && Auth::user()->type == 3)
                                                <li class="set-fav1-{{ $item->id }}">
                                                    @if ($item->is_favorite == 1)
                                                        <a class="theme-2-product-icon shadow" href="javascript:void(0)"
                                                            onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',0,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')">
                                                            <i class="fa-solid fa-heart"></i>
                                                        </a>
                                                    @else
                                                        <a class="theme-2-product-icon shadow" href="javascript:void(0)"
                                                            onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',1,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')">
                                                            <i class="fa-regular fa-heart"></i>
                                                        </a>
                                                    @endif
                                                </li>
                                            @endif
                                            <li>
                                                <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}"
                                                    class="shadow">
                                                    <i class="fa-regular fa-eye"></i>
                                                </a>
                                            </li>
                                            <li>
                                                <a type="button" class="shadow"
                                                    onclick="showitems('{{ $item->id }}','{{ $item_name }}','{{ $item->item_price }}')">
                                                    <div class="load showload-{{ $item->id }}" style="display:none">
                                                    </div>
                                                    <i
                                                        class="fa-solid fa-cart-shopping addcartbtn-{{ $item->id }}"></i>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="card-body product-content pb-0 p-3">
                                        <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}"
                                            class="fs-7 color-changer fw-500 line-2">
                                            {{ $item->item_name }}
                                        </a>
                                    </div>
                                    <div class="card-footer p-2">
                                        <div class="d-flex flex-wrap gap-1 justify-content-center align-items-center">
                                            <span class="fs-15 fw-600 color-changer text-primary">
                                                {{ helper::currency_formate($price, @$storeinfo->id) }}
                                            </span>
                                            @if ($item->item_original_price != null)
                                                @if ($original_price > $price)
                                                    <del>{{ helper::currency_formate($original_price, @$storeinfo->id) }}</del>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @elseif (helper::appdata($storeinfo->id)->template == 13)
                    <div class="row g-3  row-cols-xl-5 row-cols-lg-4 row-cols-md-3 row-cols-sm-2 row-cols-2 products-img">
                        @foreach ($getrelateditems as $item)
                            @php
                                if ($item->top_deals == 1 && helper::top_deals($vdata) != null) {
                                    if (@helper::top_deals($vdata)->offer_type == 1) {
                                        if ($item['variation']->count() > 0) {
                                            if (
                                                $item['variation'][0]->price > @helper::top_deals($vdata)->offer_amount
                                            ) {
                                                $price =
                                                    $item['variation'][0]->price -
                                                    @helper::top_deals($vdata)->offer_amount;
                                            } else {
                                                $price = $item['variation'][0]->price;
                                            }
                                        } else {
                                            if ($item->item_price > @helper::top_deals($vdata)->offer_amount) {
                                                $price = $item->item_price - @helper::top_deals($vdata)->offer_amount;
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
                                                $item->item_price * (@helper::top_deals($vdata)->offer_amount / 100);
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
                            <div class="col theme-13">
                                <div class="product-grid card border-0 h-100">
                                    <div class="product-image">
                                        <a class="image">
                                            <img class="pic-1"
                                                src="{{ @helper::image_path($item['multi_image'][0]->image) }}"
                                                class="img-fluid" alt="...">
                                            <img class="pic-2"
                                                src="{{ @$item['multi_image']->count() > 1 ? @helper::image_path($item['multi_image'][1]->image) : @helper::image_path($item['multi_image'][0]->image) }}">
                                        </a>
                                        @if ($off > 0)
                                            <span class="product-sale-label">
                                                {{ $off }}% {{ trans('labels.off') }}
                                            </span>
                                        @endif
                                        <ul class="social">
                                            <li>
                                                <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}"
                                                    class="shadow">
                                                    <i class="fa-regular fa-eye"></i>
                                                </a>
                                            </li>
                                            @if (Auth::user() && Auth::user()->type == 3)
                                                <li class="set-fav1-{{ $item->id }}">
                                                    @if ($item->is_favorite == 1)
                                                        <a href="javascript:void(0)"
                                                            onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',0,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')">
                                                            <i class="fa-solid fa-heart"></i>
                                                        </a>
                                                    @else
                                                        <a href="javascript:void(0)"
                                                            onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',1,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')">
                                                            <i class="fa-regular fa-heart"></i>
                                                        </a>
                                                    @endif
                                                </li>
                                            @endif
                                        </ul>
                                        <div
                                            class="product-rating flex-wrap d-flex justify-content-between gap-1 align-items-center">
                                            @if (App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first() != null &&
                                                    App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first()->activated == 1)
                                                @if (App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first() != null &&
                                                        App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first()->activated == 1)
                                                    @if (helper::appdata($storeinfo->id)->checkout_login_required == 1)
                                                        <a class="fs-8 text-dark d-flex gap-1 align-items-center"
                                                            onclick="showreviews('{{ $item->id }}')" role="button"
                                                            aria-controls="offcanvasExample">
                                                            <i class="fa-solid fa-star text-warning"></i>
                                                            <p class="cursor-pointer fw-600 fs-8">
                                                                {{ number_format($item->avg_ratting, 1) }}</p>
                                                        </a>
                                                    @endif
                                                @endif
                                            @endif
                                            <a class="add-to-cart cursor-pointer"
                                                onclick="showitems('{{ $item->id }}','{{ $item_name }}','{{ $item->item_price }}')">
                                                <div class="load showload-{{ $item->id }}" style="display:none">
                                                </div>
                                                <span
                                                    class="addcartbtn-{{ $item->id }}">{{ trans('labels.addcart') }}</span>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="card-body bg-change-mode product-content p-3">
                                        <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}"
                                            class="fs-7 fw-500 color-changer line-2">
                                            {{ $item->item_name }}
                                        </a>
                                    </div>
                                    <div class="card-footer bg-change-mode p-2">
                                        <div
                                            class="products-price d-flex flex-wrap gap-1 justify-content-center align-items-center">
                                            <span class="fs-15 fw-600 color-changer text-primary">
                                                {{ helper::currency_formate($price, @$storeinfo->id) }}
                                            </span>
                                            @if ($item->item_original_price != null)
                                                @if ($original_price > $price)
                                                    <del>{{ helper::currency_formate($original_price, @$storeinfo->id) }}</del>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @elseif (helper::appdata($storeinfo->id)->template == 14)
                    <div class="row row-cols-xl-5 row-cols-lg-4 row-cols-md-3 row-cols-sm-2 row-cols-2 g-3 ">
                        @foreach ($getrelateditems as $item)
                            @php
                                if ($item->top_deals == 1 && helper::top_deals($vdata) != null) {
                                    if (@helper::top_deals($vdata)->offer_type == 1) {
                                        if ($item['variation']->count() > 0) {
                                            if (
                                                $item['variation'][0]->price > @helper::top_deals($vdata)->offer_amount
                                            ) {
                                                $price =
                                                    $item['variation'][0]->price -
                                                    @helper::top_deals($vdata)->offer_amount;
                                            } else {
                                                $price = $item['variation'][0]->price;
                                            }
                                        } else {
                                            if ($item->item_price > @helper::top_deals($vdata)->offer_amount) {
                                                $price = $item->item_price - @helper::top_deals($vdata)->offer_amount;
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
                                                $item->item_price * (@helper::top_deals($vdata)->offer_amount / 100);
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
                            <div class="col theme-14">
                                <div class="product-grid card h-100">
                                    <div class="product-image">
                                        <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}"
                                            class="image">
                                            <img src="{{ (!empty(@$item['item_image']->image) ? helper::image_path($item['item_image']->image) : helper::food_image($item->item_name, $item->id)) }}"
                                                class="card-img-top" alt="...">
                                        </a>
                                        @if (Auth::user() && Auth::user()->type == 3)
                                            <div class="product-like-icon set-fav1-{{ $item->id }}">
                                                @if ($item->is_favorite == 1)
                                                    <a href="javascript:void(0)" class="text-danger"
                                                        onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',0,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')">
                                                        <i class="fa-solid fa-heart text-danger"></i>
                                                    </a>
                                                @else
                                                    <a href="javascript:void(0)" class="text-danger"
                                                        onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',1,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')">
                                                        <i class="fa-regular fa-heart text-danger"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                    <div class="card-body product-content pb-0 p-3">
                                        <div class="d-flex align-items-center mb-2 justify-content-between gap-1">
                                            @if ($off > 0)
                                                <span
                                                    class="px-2 py-1 text-capitalize rounded bg-primary fw-600 text-white fs-11">
                                                    {{ $off }}% {{ trans('labels.off') }}
                                                </span>
                                            @endif
                                            @if (App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first() != null &&
                                                    App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first()->activated == 1)
                                                @if (App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first() != null &&
                                                        App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first()->activated == 1)
                                                    @if (helper::appdata($storeinfo->id)->checkout_login_required == 1)
                                                        <a class="fs-8 text-dark d-flex gap-1 align-items-center"
                                                            onclick="showreviews('{{ $item->id }}')" role="button"
                                                            aria-controls="offcanvasExample">
                                                            <i class="fa-solid fa-star text-warning"></i>
                                                            <p class="cursor-pointer color-changer fw-600 fs-8">
                                                                {{ number_format($item->avg_ratting, 1) }}</p>
                                                        </a>
                                                    @endif
                                                @endif
                                            @endif
                                        </div>
                                        <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}"
                                            class="fs-7 color-changer fw-600 line-2">
                                            {{ $item->item_name }}
                                        </a>
                                    </div>
                                    <div class="card-footer p-3">
                                        <div class="d-flex flex-wrap gap-1 align-items-center justify-content-between">
                                            <div class="products-price d-flex flex-wrap gap-1 align-items-center">
                                                <span class="fs-15 fw-600 color-changer text-primary">
                                                    {{ helper::currency_formate($price, @$storeinfo->id) }}
                                                </span>
                                                @if ($item->item_original_price != null)
                                                    @if ($original_price > $price)
                                                        <del>{{ helper::currency_formate($original_price, @$storeinfo->id) }}</del>
                                                    @endif
                                                @endif
                                            </div>
                                            <div class="load showload-{{ $item->id }}" style="display:none">
                                            </div>
                                            <a class="add-to-cart addcartbtn-{{ $item->id }}"
                                                href="javascript:void(0)"
                                                onclick="showitems('{{ $item->id }}','{{ $item_name }}','{{ $item->item_price }}')">
                                                <i class="fas fa-cart-plus"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @elseif (helper::appdata($storeinfo->id)->template == 15)
                    <div class="row g-3  row-cols-xl-5 row-cols-lg-4 row-cols-md-3 row-cols-sm-2 row-cols-2">
                        @foreach ($getrelateditems as $item)
                            @php
                                if ($item->top_deals == 1 && helper::top_deals($vdata) != null) {
                                    if (@helper::top_deals($vdata)->offer_type == 1) {
                                        if ($item['variation']->count() > 0) {
                                            if (
                                                $item['variation'][0]->price > @helper::top_deals($vdata)->offer_amount
                                            ) {
                                                $price =
                                                    $item['variation'][0]->price -
                                                    @helper::top_deals($vdata)->offer_amount;
                                            } else {
                                                $price = $item['variation'][0]->price;
                                            }
                                        } else {
                                            if ($item->item_price > @helper::top_deals($vdata)->offer_amount) {
                                                $price = $item->item_price - @helper::top_deals($vdata)->offer_amount;
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
                                                $item->item_price * (@helper::top_deals($vdata)->offer_amount / 100);
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
                            <div class="col theme-15">
                                <div class="product-grid card-bg card border-0 h-100">
                                    <div class="product-image">
                                        <a class="image">
                                            <img class="pic-1"
                                                src="{{ @helper::image_path($item['multi_image'][0]->image) }}"
                                                class="img-fluid" alt="...">
                                            <img class="pic-2"
                                                src="{{ @$item['multi_image']->count() > 1 ? @helper::image_path($item['multi_image'][1]->image) : @helper::image_path($item['multi_image'][0]->image) }}">
                                        </a>
                                        @if ($off > 0)
                                            <span class="product-discount-label">
                                                {{ $off }}% {{ trans('labels.off') }}
                                            </span>
                                        @endif
                                        <ul class="social">
                                            @if (Auth::user() && Auth::user()->type == 3)
                                                <li class="set-fav1-{{ $item->id }}">
                                                    @if ($item->is_favorite == 1)
                                                        <a href="javascript:void(0)"
                                                            onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',0,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')">
                                                            <i class="fa-solid fa-heart"></i>
                                                        </a>
                                                    @else
                                                        <a href="javascript:void(0)"
                                                            onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',1,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')">
                                                            <i class="fa-regular fa-heart"></i>
                                                        </a>
                                                    @endif
                                                </li>
                                            @endif
                                            <li>
                                                <a type="button"
                                                    onclick="showitems('{{ $item->id }}','{{ $item_name }}','{{ $item->item_price }}')">
                                                    <div class="load showload-{{ $item->id }}" style="display:none">
                                                    </div>
                                                    <i
                                                        class="fa-solid fa-cart-shopping addcartbtn-{{ $item->id }}"></i>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}">
                                                    <i class="fa-regular fa-eye"></i>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="card-body product-content">
                                        @if (App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first() != null &&
                                                App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first()->activated == 1)
                                            @if (App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first() != null &&
                                                    App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first()->activated == 1)
                                                @if (helper::appdata($storeinfo->id)->checkout_login_required == 1)
                                                    <a class="fs-8 text-dark mb-2 d-flex gap-1 justify-content-between align-items-center"
                                                        onclick="showreviews('{{ $item->id }}')" role="button"
                                                        aria-controls="offcanvasExample">
                                                        @php
                                                            $count = (float) $item->avg_ratting;
                                                            $fullStars = 0;
                                                            if ($count > 4.5) {
                                                                $fullStars = $count >= 4.75 ? 5 : floor($count);
                                                            } elseif ($count > 3.5) {
                                                                $fullStars = $count >= 3.75 ? 4 : floor($count);
                                                            } elseif ($count > 2.5) {
                                                                $fullStars = $count >= 2.75 ? 3 : floor($count);
                                                            } elseif ($count > 1.5) {
                                                                $fullStars = $count >= 1.75 ? 2 : floor($count);
                                                            } elseif ($count > 0.5) {
                                                                $fullStars = $count >= 0.75 ? 1 : floor($count);
                                                            }
                                                            $hasHalfStar = $count - $fullStars >= 0.5 && $fullStars < 5;
                                                        @endphp
                                                        <ul class="d-flex gap-1 m-0">
                                                            @for ($i = 0; $i < 5; $i++)
                                                                @if ($i < $fullStars)
                                                                    <li class="list-inline-item me-0 fs-8">
                                                                        <i class="fa-solid fa-star text-warning"></i>
                                                                    </li>
                                                                @elseif ($i == $fullStars && $hasHalfStar)
                                                                    <li class="list-inline-item me-0 fs-8">
                                                                        <i
                                                                            class="fa-solid fa-star-half-stroke text-warning"></i>
                                                                    </li>
                                                                @else
                                                                    <li class="list-inline-item me-0 fs-8">
                                                                        <i class="fa-regular fa-star text-warning"></i>
                                                                    </li>
                                                                @endif
                                                            @endfor
                                                        </ul>
                                                        <p class="cursor-pointer color-changer fw-600 fs-8">
                                                            {{ number_format($item->avg_ratting, 1) }}</p>
                                                    </a>
                                                @endif
                                            @endif
                                        @endif
                                        <h3 class="title m-0">
                                            <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}"
                                                class="fs-7 color-changer fw-500 line-2">
                                                {{ $item->item_name }}
                                            </a>
                                        </h3>
                                    </div>
                                    <div class="card-footer p-0">
                                        <div class="d-flex flex-wrap gap-1 align-items-center">
                                            <span class="fs-15 fw-600 color-changer text-primary">
                                                {{ helper::currency_formate($price, @$storeinfo->id) }}
                                            </span>
                                            @if ($item->item_original_price != null)
                                                @if ($original_price > $price)
                                                    <del>{{ helper::currency_formate($original_price, @$storeinfo->id) }}</del>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>
    @endif

    @if (App\Models\SystemAddons::where('unique_identifier', 'sticky_cart_bar')->first() != null &&
            App\Models\SystemAddons::where('unique_identifier', 'sticky_cart_bar')->first()->activated == 1)
        @include('front.product.view-cart-bar')
    @endif

    @include('front.sum_qusction')

@endsection
@section('model')
    <!-- question answer  Modal -->
    <div class="modal fade" id="question_answer" tabindex="-1" aria-labelledby="question_answerLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header justify-content-between">
                    <h1 class="modal-title fs-5 fw-600 m-0 color-changer" id="question_answer">
                        {{ trans('labels.ask_a_question') }}</h1>
                    <button type="button" class="bg-transparent border-0 m-0" data-bs-dismiss="modal"
                        aria-label="Close">
                        <i class="fa-regular fa-xmark fs-4 color-changer"></i>
                    </button>
                </div>
                <form action="{{ URL::to($storeinfo->slug . '/product_question_answer') }}" method="post"
                    class="border-top ">
                    @csrf
                    <input type="hidden" name="question_answer_product_id" value="{{ $getitem->id }}">
                    <input type="hidden" name="question_answer_vendor_id" value="{{ $getitem->vendor_id }}">
                    <div class="modal-body">
                        <div class="d-flex align-items-center gap-2">
                            <div>
                                <img src="{{ !empty(@$getitem['item_image']->image) ? helper::image_path($getitem['item_image']->image) : helper::food_image($getitem->item_name, $getitem->id) }}" alt=""
                                    class="rounded" height="110px" width="110px">
                            </div>
                            <div class="w-100">
                                <h6 class="line-2 fs-15 fw-500 color-changer">
                                    {{ @$getitem->item_name }}
                                </h6>
                                <div class="d-flex flex-wrap gap-1 align-items-center">
                                    <p class="fs-6 fw-500 m-0 color-changer">
                                        {{ helper::currency_formate($price, $storeinfo->id) }}
                                    </p>
                                    <del
                                        class="fw-500 text-muted fs-13">{{ helper::currency_formate($original_price, $storeinfo->id) }}</del>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label for="exampleFormControlTextarea1" class="form-label d-flex gap-1 mt-2">
                                {{ trans('labels.your_question') }}
                                <div aria-hidden="true" class="text-danger">*</div>
                            </label>

                            <textarea class="form-control m-0 fs-7" id="question" name="question" placeholder="Your Questions" rows="3"
                                required=""></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary fs-7 fw-500">{{ trans('labels.submit') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="{{ url(env('ASSETSPATHURL') . 'web-assets/js/smoothproducts.js') }}"></script>
    <script src="{{ url(env('ASSETSPATHURL') . 'web-assets/js/custom/top_deals.js') }}"></script>
    <script>
        // Product Preview
        $('.sp-wrap').smoothproducts();

        var topdeals = 1;

        $(document).ready(function($) {
            var selected = [];
            $('.detail-variation input:checked').each(function() {
                var label = $("#detail_variation label[for='" + $(this).attr('id') + "']").attr('id');
                $("#detail_variation [id='" + 'check_' + this.id + "']").addClass('active');
                selected.push($(this).attr('value'));
            });

            if (selected != "" && selected != null) {

                detail_set_variant_price(selected);
            }

        });
        $('#detail_variation input:checkbox').click(function() {
            var selected = [];
            var divselected = [];
            const myArray = this.id.split("-");

            var id = this.id;
            $('#detail_variation .check' + myArray[0] + ' input:checked').each(function() {
                divselected.push($(this).attr('value'));
            });
            if (divselected.length == 0) {
                $(this).prop('checked', true);
            }

            $('#detail_variation .check' + myArray[0] + ' input:checkbox').not(this).prop('checked', false);
            $('#detail_variation .check' + myArray[0]).removeClass('active');
            $("#detail_variation [id='" + 'check_' + this.id + "']").addClass('active');
            $('.detail-variation input:checked').each(function() {
                selected.push($(this).attr('value'));
            });
            if (selected != "" && selected != null) {
                $('.product-detail-price').addClass('d-none');
                $('#detail_laodertext').removeClass('d-none');
                $('#detail_laodertext').html(
                    '<span class="loader"></span>'
                );
                detail_set_variant_price(selected);
            }
        });

        function detail_set_variant_price(variants) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ URL::to('get-products-variant-quantity') }}",
                data: {
                    name: variants,
                    item_id: $('#detail_overview_item_id').val(),
                    vendor_id: $('#detail_overview_vendor').val(),
                },
                success: function(data) {
                    if (data.status == 1) {
                        setTimeout(function() {
                            $('#detail_laodertext').html('');
                        }, 4000);
                        var off = ((1 - (data.price / data.original_price)) * 100).toFixed(1);
                        $('#detail_laodertext').addClass('d-none');
                        $('.product-detail-price').removeClass('d-none');
                        $('#detail_variants_name').val(variants);
                        $('.details_item_price').text(currency_formate(parseFloat(data.price)));
                        $('#detail_overview_item_price').val(data.price);
                        $('#details_offer').removeClass('d-none');
                        if (parseFloat(data.original_price) > parseFloat(data.price)) {
                            $('.details_original_price').text(currency_formate(parseFloat(data
                                .original_price)));
                            $('#details_offer').text($.number(off, 0) + '% ' + '{{ trans('labels.off') }}');
                        } else {
                            $('.details_original_price').text('');
                            $('#details_offer').text('');
                        }
                        $('#detail_overview_item_original_price').val(data.original_price);
                        $('#detail_stock_management').val(data.stock_management);
                        $('#detail_item_min_order').val(data.min_order);
                        $('#detail_item_max_order').val(data.max_order);
                        if (data.is_available == 2) {
                            $('#details_offer').addClass('d-none');
                            $('#detail_not_available_text').html(not_available);
                            $('.add-details-btn').attr('disabled', true);
                            $('.add-details-btn').addClass('d-none');
                            $('.details_item_price').addClass('d-none');
                            $('.details_original_price').addClass('d-none');
                            $('#detail_sku_stock').addClass('d-none');
                            $('#detail_plus-minus').addClass('d-none');
                            $('#detail_tax').addClass('d-none');
                            $('#detail_stock').addClass('d-none');

                        } else {
                            $('#details_offer').removeClass('d-none');
                            $('#detail_not_available_text').html('');
                            $('.add-details-btn').attr('disabled', false);
                            $('.add-details-btn').removeClass('d-none');
                            $('.details_item_price').removeClass('d-none');
                            $('.details_original_price').removeClass('d-none');
                            $('#detail_plus-minus').removeClass('d-none');
                            $('#detail_sku_stock').addClass('d-none');
                            $('#detail_tax').removeClass('d-none');
                            $('#detail_stock').addClass('d-none');
                            if (data.stock_management == 1) {
                                $('#detail_stock').removeClass('d-none');
                                $('#detail_sku_stock').removeClass('d-none');
                                $('#details_out_of_stock').removeClass('d-none');
                                if (data.quantity > 0) {
                                    $('.add-details-btn').attr('disabled', false);
                                    $('#details_out_of_stock').removeClass('text-danger');
                                    $('#details_out_of_stock').addClass('text-success');
                                    $('#details_out_of_stock').html('' + data.quantity +
                                        ' {{ trans('labels.in_stock') }}');
                                } else {
                                    $('.add-details-btn').attr('disabled', true);
                                    $('#details_out_of_stock').removeClass('text-dark');
                                    $('#details_out_of_stock').addClass('text-danger');
                                    $('#details_out_of_stock').html('{{ trans('labels.out_of_stock') }}');
                                }
                            } else {
                                $('#details_out_of_stock').addClass('d-none');
                            }

                        }
                    }

                }
            });
        }

        function detailchangeqty(item_id, type) {
            var qtys = parseInt($('.detail_item_qty').val());
            if (type == "minus") {
                qty = qtys - 1;
            } else {
                qty = qtys + 1;
            }
            if (qty >= "1") {
                $('.change-qty-1').prop('disabled', true);
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ URL::to('/changeqty') }}",
                    data: {
                        item_id: item_id,
                        type: type,
                        qty: qty,
                        vendor_id: $('#detail_overview_vendor').val(),
                        variants_name: $('#detail_variants_name').val(),
                        stock_management: $('#detail_stock_management').val(),
                    },
                    method: 'POST',
                    success: function(response) {
                        if (response.status == 1) {
                            $('.detail_item_qty').val(response.qty);
                            $('.change-qty-1').prop('disabled', false);
                        } else {
                            $('.change-qty-1').prop('disabled', false);
                            toastr.error(response.message);
                        }
                    },
                    error: function(error) {
                        $('.change-qty-1').prop('disabled', false);
                        toastr.error(wrong);
                    }
                });
            }

        }

        function detailaddtocart(buynow) {
            "use strict";
            if (buynow == 1) {
                $('.buynow').prop("disabled", true);
                $('.buynow').html('<span class="loader"></span>');
            } else {
                $('.addtocart').prop("disabled", true);
                $('.addtocart').html('<span class="loader"></span>');
            }
            var item_id = $('#detail_overview_item_id').val();
            var vendor = $('#detail_overview_vendor').val();
            var item_name = $('#detail_overview_item_name').val();
            var item_image = $('#detail_overview_item_image').val();
            var item_price = $('#detail_overview_item_price').val();
            var item_original_price = $('#detail_overview_item_original_price').val();
            var variants_name = $('#detail_variants_name').val();
            var item_qty = $('.detail_item_qty').val();
            var min_order = $('#detail_item_min_order').val();
            var max_order = $('#detail_item_max_order').val();
            var tax = $('#detail_item_tax').val();
            var stock_management = $('#detail_stock_management').val();
            var extras_id = ($('.Checkbox:checked').map(function() {
                return this.value;
            }).get().join('| '));
            var extras_name = ($('.Checkbox:checked').map(function() {
                return $(this).attr('extras_name');
            }).get().join('| '));
            var extras_price = ($('.Checkbox:checked').map(function() {
                return $(this).attr('price');
            }).get().join('| '));

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: $('#addtocarturl').val(),
                data: {
                    vendor_id: vendor,
                    item_id: item_id,
                    item_name: item_name,
                    item_image: item_image,
                    item_price: item_price,
                    item_original_price: item_original_price,
                    tax: tax,
                    variants_name: variants_name,
                    extras_id: extras_id,
                    extras_name: extras_name,
                    extras_price: extras_price,
                    qty: item_qty,
                    min_order: min_order,
                    max_order: max_order,
                    stock_management: stock_management,
                    buynow: buynow,
                    tax: tax,
                },
                method: 'POST', //Post method,
                success: function(response) {
                    if (response.status == 1) {
                        $('#cartcount').html(response.totalcart);
                        $('#cartcount_mobile').html(response.totalcart);
                        if (response.buynow == 1) {
                            window.location.href = response.checkouturl;
                        } else {
                            location.reload();
                        }
                    } else {
                        if (response.buynow == 1) {
                            $('.buynow').prop("disabled", false);
                            $('.buynow').html('Buy now');
                        } else {
                            $('.addtocart').prop("disabled", false);
                            $('.addtocart').html('Add to Cart');
                        }
                        $('#additems').modal('hide');
                        toastr.error(response.message);
                    }
                },
                error: function(response) {
                    if (response.buynow == 1) {
                        $('.buynow').prop("disabled", false);
                        $('.buynow').html('Buy now');
                    } else {
                        $('.addtocart').prop("disabled", false);
                        $('.addtocart').html('Add to Cart');
                    }
                    $('#additems').modal('hide');
                    toastr.error(wrong);
                }
            })
        };
    </script>
@endsection
