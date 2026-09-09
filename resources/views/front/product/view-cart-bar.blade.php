<div class="view-cart-bar-2">
    <section class="view-cart-bar d-none">
        <div class="container">
            <div class="row g-2 align-items-center">
                <div class="col-xl-6 col-lg-5 col-md-6">
                    <div class="d-flex gap-3 align-items-center">
                        <div class="product-img">
                            <img src="{{ helper::image_path($image->image) }}" class="rounded">
                        </div>
                        <div>
                            <h5 class="text-dark color-changer line-2 fw-600 my-1">
                                {{ $getitem->item_name }}
                            </h5>
                            <div class="d-flex gap-1 flex-wrap align-items-center">
                                <p class="pro-text color-changer pricing fs-6 fw-600 details_item_price">
                                    {{ helper::currency_formate($price, $getitem->vendor_id) }}
                                </p>
                                @if ($original_price > $price)
                                    <del
                                        class="card-text pro-org-value fs-8 text-muted pricing mb-0 details_original_price">
                                        {{ helper::currency_formate($original_price, $getitem->vendor_id) }}
                                    </del>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-6 col-lg-7 col-md-6">
                    <div class="row g-2 justify-content-end">
                        <div class="col-lg-3 col-md-4 col-12">
                            <div class="input-group qty-input2 col-md-auto col-12 responsive-margin m-0 rounded-2">
                                <button class="btn p-0 change-qty-1" id="minus"
                                    onclick="detailchangeqty('{{ $getitem->id }}','minus')" value="minus value">
                                    <i class="fa fa-minus"></i>
                                </button>
                                <input type="text" class="border-0 color-changer bg-transparent text-center detail_item_qty" value="1"
                                    readonly="">
                                <button class="btn p-0 change-qty-1" id="plus"
                                    onclick="detailchangeqty('{{ $getitem->id }}','plus')" value="plus value">
                                    <i class="fa fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-4 col-12">
                            <button class="btn btn-store m-0 add-details-btn px-0 w-100 addtocart h-100"
                                onclick="detailaddtocart('0')"
                                {{ $getitem->stock_management == 1 ? ($getitem->qty <= 0 ? 'disabled' : '') : '' }}>
                                <span class="px-1 fs-7">{{ trans('labels.addcart') }}</span>
                            </button>
                        </div>
                        <div class="col-lg-3 col-md-4 col-12">
                            <button class="btn btn-store-outline m-0 px-0 add-details-btn w-100 buynow h-100"
                                onclick="detailaddtocart('1')"
                                {{ $getitem->stock_management == 1 ? ($getitem->qty <= 0 ? 'disabled' : '') : '' }}>
                                <span class="px-1 fs-7">{{ trans('labels.buy_now') }}</span>
                            </button>
                        </div>
                        <div class="col-auto">
                            <button class="border border-black rounded bg-transparent color-changer add-details-btn m-0 close-btn-view" id="close-btn-view">
                                <i class="fa-regular fa-xmark fs-4"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
