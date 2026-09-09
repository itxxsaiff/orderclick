@extends('admin.layout.default')
@php
    if (Auth::user()->type == 4) {
        $vendor_id = Auth::user()->vendor_id;
    } else {
        $vendor_id = Auth::user()->id;
    }

@endphp
@section('content')
    <div class="row justify-content-between align-items-center mb-3">
        <div class="col-12">
            <h5 class="pages-title color-changer fs-2">{{ trans('labels.edit') }}</h5>
            @include('admin.layout.breadcrumb')
        </div>
    </div>

    <div class="col-md-12 mb-7 mt-3">
        <div class="card border-0 box-shadow">
            <div class="card-body">
                @if (!empty($getproductdata))
                    <form action="{{ URL::to('admin/products/update-' . $getproductdata->slug) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="col-12">
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label class="form-label">{{ trans('labels.category') }} <span class="text-danger"> *
                                        </span></label>
                                    <select class="form-select" name="category" id="editcat_id" required>
                                        <option value="">{{ trans('labels.select') }}</option>
                                        @foreach ($getcategorylist as $catdata)
                                            <option value="{{ $catdata->id }}" data-id="{{ $catdata->id }}"
                                                {{ $getproductdata->cat_id == $catdata->id ? 'selected' : '' }}>
                                                {{ $catdata->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="form-label">{{ trans('labels.name') }} <span class="text-danger"> *
                                        </span></label>
                                    <input type="text" class="form-control" name="product_name"
                                        value="{{ $getproductdata->item_name }}" placeholder="{{ trans('labels.name') }}"
                                        required>
                                    @error('product_name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div
                                    class="col-md-6 form-group add-extra-class {{ session()->get('direction') == 2 ? 'rtl' : '' }}">
                                    <label class="form-label">{{ trans('labels.tax') }}</label>
                                    <select name="tax[]" class="form-control selectpicker" multiple
                                        data-live-search="true">
                                        @if (!empty($gettaxlist))
                                            @foreach ($gettaxlist as $tax)
                                                <option value="{{ $tax->id }}"
                                                    {{ in_array($tax->id, explode('|', $getproductdata->tax)) ? 'selected' : '' }}>
                                                    {{ $tax->name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="form-label">{{ trans('labels.video_url') }}</label>
                                    <input type="text" class="form-control" name="video_url"
                                        value="{{ $getproductdata->video_url }}"
                                        placeholder="{{ trans('labels.video_url') }}">
                                </div>
                                <div class="col-sm-12 form-group">
                                    <label class="form-label">{{ trans('labels.description') }}
                                        <span class="text-danger">*</span></label>
                                    <textarea name="description" class="form-control" rows="5" id="ckeditor"
                                        placeholder="{{ trans('labels.description') }}" required>{{ $getproductdata->description }}</textarea>
                                    @error('description')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-12 d-flex flex-wrap justify-content-between align-items-center">
                                    <div class="form-group">
                                        <label for="has_extras"
                                            class="form-label">{{ trans('labels.product_has_extras') }}</label>
                                        <div class="col-md-12">
                                            <div class="form-check-inline">
                                                <input class="form-check-input me-0 has_extras" type="radio"
                                                    name="has_extras" id="extras_no" value="2"
                                                    {{ count($getproductdata['extras']) > 0 ? '' : 'checked' }}>
                                                <label class="form-check-label"
                                                    for="extras_no">{{ trans('labels.no') }}</label>
                                            </div>
                                            <div class="form-check-inline">
                                                <input class="form-check-input me-0 has_extras" type="radio"
                                                    name="has_extras" id="extras_yes" value="1"
                                                    {{ count($getproductdata['extras']) > 0 ? 'checked' : '' }}>
                                                <label class="form-check-label"
                                                    for="extras_yes">{{ trans('labels.yes') }}</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-center align-items-center gap-2 col-sm-auto col-12">
                                        @if (count($globalextras) > 0)
                                            <div class="col-sm-auto col-10">
                                                <button
                                                    class="btn btn-secondary w-100 fs-7 btn-sm px-sm-4 rounded-start-5 rounded-end-5"
                                                    type="button" id="globalextra" onclick="global_extras()"><i
                                                        class="fa-sharp fa-solid fa-plus"></i>
                                                    {{ trans('labels.add_global_extras') }}</button>
                                            </div>
                                        @endif
                                        <div class="col-auto">
                                            <button class="btn btn-dark hov btn-sm rounded-5" id="add_extras" type="button"
                                                onclick="more_editextras_fields('{{ trans('labels.name') }}','{{ trans('labels.price') }}')">
                                                <i class="fa-sharp fa-solid fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="" id="extras">

                                    @foreach ($getproductdata['extras'] as $key => $extras)
                                        <div class="row pe-0">
                                            <input type="hidden" class="form-control" name="extras_id[]"
                                                value="{{ $extras->id }}">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    @if ($key == 0)
                                                        <label class="form-label">{{ trans('labels.name') }} <span
                                                                class="text-danger">
                                                                * </span></label>
                                                    @endif
                                                    <input type="text" class="form-control extras_name"
                                                        name="extras_name[]" value="{{ $extras->name }}"
                                                        placeholder="{{ trans('labels.name') }}" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    @if ($key == 0)
                                                        <label class="form-label">{{ trans('labels.price') }}
                                                            <span class="text-danger">
                                                                * </span></label>
                                                    @endif
                                                    <div class="d-flex gap-2">
                                                        <input type="text"
                                                            class="form-control numbers_only extras_price"
                                                            name="extras_price[]" value="{{ $extras->price }}"
                                                            placeholder="{{ trans('labels.price') }}" required>
                                                        <button class="btn btn-danger hov btn-sm rounded-5" type="button"
                                                            @if (env('Environment') == 'sendbox') onclick="myFunction()" @else onclick="deletedata('{{ URL::to('admin/products/delete/extras-' . $extras->id) }}')" @endif><i
                                                                class="fa fa-trash" aria-hidden="true"></i> </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <span class="hiddenextrascount d-none">{{ $key }}</span>
                                    @endforeach
                                    <div id="global-extras"></div>
                                    <div id="more_editextras_fields"></div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="has_variants"
                                            class="form-label">{{ trans('labels.product_has_variation') }}</label>
                                        <div class="col-md-12">

                                            <div class="form-check-inline">
                                                <input class="form-check-input me-0 has_variants" type="radio"
                                                    name="has_variants" id="no" value="2" checked
                                                    @if ($getproductdata->has_variants == 2) checked @endif>
                                                <label class="form-check-label"
                                                    for="no">{{ trans('labels.no') }}</label>
                                            </div>
                                            <div class="form-check-inline">
                                                <input class="form-check-input me-0 has_variants" type="radio"
                                                    name="has_variants" id="yes" value="1"
                                                    @if ($getproductdata->has_variants == 1) checked @endif>
                                                <label class="form-check-label"
                                                    for="yes">{{ trans('labels.yes') }}</label>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                @if ($getproductdata->has_variants == 1 && count(@$getproductdata['variation']) > 0)
                                    <div
                                        class="col-md-6 form-group m-0 mt-2 d-flex gap-2 align-items-center justify-content-end">
                                        <button class="btn btn-dark hov btn-sm rounded-5 get-variants" type="button"
                                            dataa-url="{{ URL::to('admin/products/variants/edit', $getproductdata->id) }}">
                                            <i class="fa-sharp fa-solid fa-plus"></i>
                                        </button>
                                    </div>
                                @else
                                    <div
                                        class="col-md-6 form-group m-0 mt-2 d-flex gap-2 align-items-center justify-content-end">
                                        <button class="btn btn-dark hov btn-sm rounded-5" type="button"
                                            id="btn_addvariants" onclick="addvariantModal()">
                                            <i class="fa-sharp fa-solid fa-plus"></i>
                                        </button>
                                    </div>
                                @endif
                                <div class="col-12">
                                    <div class="row @if ($getproductdata->has_variants == 1) dn @endif" id="price_row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">{{ trans('labels.original_price') }} <span
                                                        class="text-danger">
                                                        * </span></label>
                                                <input type="text" step="any" class="form-control numbers_only"
                                                    name="original_price"
                                                    value="{{ $getproductdata->has_variants == 1 ? '' : ($getproductdata->item_original_price > 0 ? $getproductdata->item_original_price : 0) }}"
                                                    placeholder="{{ trans('labels.original_price') }}"
                                                    id="original_price">

                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">{{ trans('labels.selling_price') }} <span
                                                        class="text-danger">
                                                        * </span></label>
                                                <input type="text" step="any" class="form-control numbers_only"
                                                    name="price"
                                                    value="{{ $getproductdata->has_variants == 1 ? '' : $getproductdata->item_price }}"
                                                    placeholder="{{ trans('labels.selling_price') }}" id="price">

                                            </div>
                                        </div>
                                        <div class="col-12 d-flex align-items-center justify-content-between">
                                            <div class="form-group">
                                                <label for="has_stock"
                                                    class="form-label">{{ trans('labels.stock_management') }}</label>
                                                <div class="col-md-12">
                                                    <div class="form-check-inline">
                                                        <input class="form-check-input me-0 has_stock" type="radio"
                                                            name="has_stock" id="stock_no" value="2" checked
                                                            @if ($getproductdata->stock_management == 2) checked @endif>
                                                        <label class="form-check-label"
                                                            for="stock_no">{{ trans('labels.no') }}</label>
                                                    </div>
                                                    <div class="form-check-inline">
                                                        <input class="form-check-input me-0 has_stock" type="radio"
                                                            name="has_stock" id="stock_yes" value="1"
                                                            @if ($getproductdata->stock_management == 1) checked @endif>
                                                        <label class="form-check-label"
                                                            for="stock_yes">{{ trans('labels.yes') }}</label>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3" id="block_stock_qty">
                                            <div class="form-group">
                                                <label class="form-label">{{ trans('labels.stock_qty') }} <span
                                                        class="text-danger"> * </span></label>
                                                <input type="text" class="form-control numbers_only" name="qty"
                                                    onkeypress="allowNumbersOnly(event)"
                                                    value="{{ $getproductdata->has_variants == 1 ? '' : $getproductdata->qty }}"
                                                    placeholder="{{ trans('labels.stock_qty') }}" id="qty">
                                            </div>
                                        </div>
                                        <div class="col-md-3" id="block_min_order">
                                            <div class="form-group">
                                                <label class="form-label">{{ trans('labels.min_order_qty') }} <span
                                                        class="text-danger"> * </span></label>
                                                <input type="text" class="form-control numbers_only" name="min_order"
                                                    onkeypress="allowNumbersOnly(event)"
                                                    value="{{ $getproductdata->has_variants == 1 ? '' : ($getproductdata->min_order > 0 ? $getproductdata->min_order : 0) }}"
                                                    placeholder="{{ trans('labels.min_order_qty') }}" id="min_order">

                                            </div>
                                        </div>
                                        <div class="col-md-3" id="block_max_order">
                                            <div class="form-group">
                                                <label class="form-label">{{ trans('labels.max_order_qty') }} <span
                                                        class="text-danger"> * </span></label>
                                                <input type="text" class="form-control numbers_only" name="max_order"
                                                    onkeypress="allowNumbersOnly(event)"
                                                    value="{{ $getproductdata->has_variants == 1 ? '' : ($getproductdata->max_order > 0 ? $getproductdata->max_order : 0) }}"
                                                    placeholder="{{ trans('labels.max_order_qty') }}" id="max_order">

                                            </div>
                                        </div>
                                        <div class="col-md-3" id="block_product_low_qty_warning">
                                            <div class="form-group">
                                                <label class="form-label">{{ trans('labels.product_low_qty_warning') }}
                                                    <span class="text-danger"> * </span></label>
                                                <input type="text" class="form-control" name="low_qty" id="low_qty"
                                                    onkeypress="allowNumbersOnly(event)"
                                                    value="{{ $getproductdata->low_qty }}"
                                                    placeholder="{{ trans('labels.product_low_qty_warning') }}">

                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="@if ($getproductdata->has_variants == 2) dn @endif" id="variations">
                                    <div class="card my-3 {{ count($productVariantArrays) > 0 ? 'd-flex' : 'd-none' }}"
                                        id="variant_card">
                                        <div class="card-header border-bottom">
                                            <div class="row flex-grow-1">
                                                <div class="col-md d-flex align-items-center">
                                                    <h5 class="card-header-title color-changer">
                                                        {{ trans('labels.product') }}
                                                        {{ trans('labels.variants') }}
                                                    </h5>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row form-group">
                                                <div class="table-responsive">
                                                    <input type="hidden" id="hiddenVariantOptions"
                                                        name="hiddenVariantOptions"
                                                        value="{{ $getproductdata->variants_json == null ? '{}' : $getproductdata->variants_json }}">
                                                    <div class="variant-table">
                                                        <table class="table table-bordered" id='tblvariants'>
                                                            <thead>
                                                                <tr class="text-center">
                                                                    @if (isset($product_variant_names))
                                                                        @foreach ($product_variant_names as $variant)
                                                                            <th class="fs-15 fw-500">
                                                                                <span>{{ ucwords($variant) }}</span>
                                                                            </th>
                                                                        @endforeach
                                                                    @endif
                                                                    <th class="fs-15 fw-500">
                                                                        <span>{{ trans('labels.original_price') }}</span>
                                                                    </th>
                                                                    <th class="fs-15 fw-500">
                                                                        <span>{{ trans('labels.selling_price') }}</span>
                                                                    </th>
                                                                    <th class="fs-15 fw-500">
                                                                        <span>{{ trans('labels.stock_qty') }}</span>
                                                                    </th>
                                                                    <th class="fs-15 fw-500">
                                                                        <span>{{ trans('labels.min_order_qty') }}</span>
                                                                    </th>
                                                                    <th class="fs-15 fw-500">
                                                                        <span>{{ trans('labels.max_order_qty') }}</span>
                                                                    </th>
                                                                    <th class="fs-15 fw-500">
                                                                        <span>{{ trans('labels.product_low_qty_warning') }}</span>
                                                                    </th>
                                                                    <th class="fs-15 fw-500">
                                                                        <span>{{ trans('labels.stock_management') }}</span>
                                                                    </th>
                                                                    <th class="fs-15 fw-500">
                                                                        <span>{{ trans('labels.is_available') }}</span>
                                                                    </th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>

                                                                @if (isset($productVariantArrays))
                                                                    @foreach ($productVariantArrays as $counter => $productVariant)
                                                                        <tr class="fs-7 align-middle"
                                                                            data-id="{{ $productVariant['product_variants']['id'] }}">
                                                                            @foreach (explode('|', $productVariant['product_variants']['name']) as $key => $values)
                                                                                <td>
                                                                                    <input type="text"
                                                                                        name="variants[{{ $productVariant['product_variants']['id'] }}][variants][{{ $key }}][]"
                                                                                        autocomplete="off"
                                                                                        spellcheck="false"
                                                                                        class="form-control"
                                                                                        value="{{ $values }}"
                                                                                        readonly>
                                                                                </td>
                                                                            @endforeach
                                                                            <td>
                                                                                <input type="number"
                                                                                    name="variants[{{ $productVariant['product_variants']['id'] }}][original_price]"
                                                                                    autocomplete="off" spellcheck="false"
                                                                                    placeholder="{{ trans('labels.original_price') }}"
                                                                                    class="form-control voriginal_price_{{ $counter }}"
                                                                                    value="{{ $productVariant['product_variants']['original_price'] }}"
                                                                                    onclick="originalpricevalidation('{{ $counter }}')"
                                                                                    id="voriginal_price_{{ $counter }}"
                                                                                    required>
                                                                            </td>
                                                                            <td>
                                                                                <input type="number"
                                                                                    name="variants[{{ $productVariant['product_variants']['id'] }}][price]"
                                                                                    autocomplete="off" spellcheck="false"
                                                                                    placeholder="{{ trans('labels.selling_price') }}"
                                                                                    class="form-control vprice_{{ $counter }}"
                                                                                    value="{{ $productVariant['product_variants']['price'] }}"
                                                                                    onclick="sellingpricevalidation('{{ $counter }}')"
                                                                                    id="vprice_{{ $counter }}"
                                                                                    required>
                                                                            </td>

                                                                            <td>
                                                                                <input type="number"
                                                                                    name="variants[{{ $productVariant['product_variants']['id'] }}][qty]"
                                                                                    autocomplete="off" spellcheck="false"
                                                                                    placeholder="{{ trans('labels.stock_qty') }}"
                                                                                    class="form-control vqty_{{ $counter }}"
                                                                                    value="{{ $productVariant['product_variants']['qty'] }}"
                                                                                    id="vqty_{{ $counter }}">
                                                                            </td>
                                                                            <td>
                                                                                <input type="number"
                                                                                    name="variants[{{ $productVariant['product_variants']['id'] }}][min_order]"
                                                                                    autocomplete="off" spellcheck="false"
                                                                                    placeholder="{{ trans('labels.min_order_qty') }}"
                                                                                    class="form-control vmin_order_{{ $counter }}"
                                                                                    value="{{ $productVariant['product_variants']['min_order'] }}"
                                                                                    id="vmin_order_{{ $counter }}">
                                                                            </td>
                                                                            <td>
                                                                                <input type="number"
                                                                                    name="variants[{{ $productVariant['product_variants']['id'] }}][max_order]"
                                                                                    autocomplete="off" spellcheck="false"
                                                                                    placeholder="{{ trans('labels.max_order_qty') }}"
                                                                                    class="form-control vmax_order_{{ $counter }}"
                                                                                    value="{{ $productVariant['product_variants']['max_order'] }}"
                                                                                    id="vmax_order_{{ $counter }}">
                                                                            </td>
                                                                            <td>
                                                                                <input type="number"
                                                                                    name="variants[{{ $productVariant['product_variants']['id'] }}][low_qty]"
                                                                                    autocomplete="off" spellcheck="false"
                                                                                    placeholder="{{ trans('labels.product_low_qty_warning') }}"
                                                                                    class="form-control vlow_qty_{{ $counter }}"
                                                                                    value="{{ $productVariant['product_variants']['low_qty'] }}"
                                                                                    id="vlow_qty_{{ $counter }}">
                                                                            </td>
                                                                            <td class="text-center">
                                                                                <input
                                                                                    class="form-check-input  vstock_management_{{ $counter }}"
                                                                                    type="checkbox" value="1"
                                                                                    {{ $productVariant['product_variants']['stock_management'] == 1 ? 'checked' : '' }}
                                                                                    onclick="edit_stock_management(this.id)"
                                                                                    name="variants[{{ $productVariant['product_variants']['id'] }}][stock_management]"
                                                                                    id="vstockmanagement_{{ $counter }}">

                                                                            </td>
                                                                            <td class="text-center">
                                                                                <input
                                                                                    class="form-check-input  vis_available_{{ $counter }} product_available"
                                                                                    type="checkbox" value="1"
                                                                                    onclick="edit_checkavailable(this.id)"
                                                                                    {{ $productVariant['product_variants']['is_available'] == 1 ? 'checked' : '' }}
                                                                                    name="variants[{{ $productVariant['product_variants']['id'] }}][is_available]"
                                                                                    id="{{ $counter }}">
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                @endif
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group m-0 mt-2 d-flex gap-2 justify-content-end">
                                <a href="{{ URL::to('admin/products') }}"
                                    class="btn btn-danger px-4 rounded-start-5 rounded-end-5">{{ trans('labels.cancel') }}</a>
                                <button
                                    class="btn btn-secondary px-4 rounded-start-5 rounded-end-5 {{ Auth::user()->type == 4 ? (helper::check_access('role_products', Auth::user()->role_id, $vendor_id, 'edit') == 1 ? '' : 'd-none') : '' }}"
                                    @if (env('Environment') == 'sendbox') type="button" onclick="myFunction()" @else type="submit" @endif>
                                    {{ trans('labels.save') }}
                                </button>
                            </div>
                        </div>
                    </form>
                    <button class="btn btn-secondary px-4 rounded-start-5 rounded-end-5 my-3" type="button"
                        data-bs-toggle="modal" data-bs-target="#AddProduct"><i class="fa-sharp fa-solid fa-plus"></i>
                        {{ trans('labels.add') }} {{ trans('labels.image') }}</button>
                    <div class="row row-cols-1 row-cols-md-3 row-cols-lg-3 row-cols-xl-5 g-3 sort_menu"
                        data-url="{{ url('admin/products/reorder_image-' . $getproductdata->id) }}" id="carddetails">
                        @if (count($getproductimage) > 0)
                            @foreach ($getproductimage as $productimage)
                                <div class="col" data-id="{{ $productimage->id }}">
                                    <div class="card p-2 handle">
                                        <div class="overflow-hidden">
                                            <img src="{{ helper::image_path($productimage->image) }}"
                                                class="img-fluid product-image rounded-3">
                                        </div>
                                        <div class="d-flex justify-content-center align-items-center mt-3 gap-2">
                                            <a tooltip="{{ trans('labels.move') }}"
                                                class="btn btn-sm btn-secondary btn-size"><i
                                                    class="fa-light fa-up-down-left-right"></i></a>
                                            <a href="javascript:void(0)" class="btn btn-sm btn-info btn-size"
                                                tooltip="Edit"
                                                onclick="imageview('{{ $productimage->id }}','{{ $productimage->image }}')"><i
                                                    class="fa-regular fa-pen-to-square"></i></a>

                                            @if (count($getproductimage) > 1)
                                                <a href="javascript:void(0)" class="btn btn-sm btn-danger btn-size"
                                                    tooltip="Delete"
                                                    @if (env('Environment') == 'sendbox') type="button" onclick="myFunction()" @else onClick="deleteItemImage('{{ $productimage->id }}','{{ $getproductdata->id }}','{{ URL::to('admin/products/destroyimage') }}')" @endif>
                                                    <i class="fa-solid fa-trash" aria-hidden="true"></i> </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                @else
                    @include('admin.layout.no_data')
                @endif
            </div>
        </div>
    </div>

    <!-- Add Item Image -->
    <div class="modal fade" id="AddProduct" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form method="post" action="{{ URL::to('admin/products/storeimages') }}" name="addproduct"
                class="addproduct" id="addproduct" enctype="multipart/form-data">
                @csrf
                <span id="msg"></span>
                <input type="hidden" id="storeimagesurl" value="">
                <input type="hidden" name="itemid" id="itemid" value="{{ $getproductdata->id }}">
                <div class="modal-content">
                    <div class="modal-header justify-content-between">
                        <h5 class="modal-title text-dark color-changer" id="exampleModalLabel">{{ trans('labels.image') }} add</h5>
                        <button type="button" class="bg-transparent border-0 m-0" data-bs-dismiss="modal"
                            aria-label="Close">
                            <i class="fa-regular fa-xmark color-changer fs-4"></i>
                        </button>
                    </div>
                    <span id="iiemsg"></span>
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="form-label">{{ trans('labels.image') }} <span
                                    class="text-danger">*</span></label>
                            <input type="file" multiple="true" class="form-control" name="file[]" id="file"
                                accept="image/*" required="">
                        </div>
                        <div class="gallery"></div>
                    </div>
                    <div class="modal-footer gap-2">
                        <button type="button" class="btn btn-danger px-4 rounded-start-5 rounded-end-5 m-0"
                            data-bs-dismiss="modal">{{ trans('labels.close') }}</button>
                        <button
                            class="btn btn-secondary m-0 px-4 rounded-start-5 rounded-end-5  {{ Auth::user()->type == 4 ? (helper::check_access('role_products', Auth::user()->role_id, $vendor_id, 'edit') == 1 ? '' : 'd-none') : '' }}"
                            @if (env('Environment') == 'sendbox') type="button" onclick="myFunction()" @else type="submit" @endif>{{ trans('labels.save') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    {{-- EDIT PRODUCT IMAGE MODAL --}}
    <div class="modal modal-fade-transform" id="editModal" tabindex="-1" aria-labelledby="editModallable"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header justify-content-between">
                    <h5 class="modal-title text-dark color-changer" id="editModallable">{{ trans('labels.image') }} <span
                            class="text-danger">
                            * </span></h5>
                    <button type="button" class="bg-transparent border-0 m-0" data-bs-dismiss="modal"
                            aria-label="Close">
                            <i class="fa-regular fa-xmark color-changer fs-4"></i>
                        </button>
                </div>
                <form action=" {{ URL::to('admin/products/updateimage') }}" method="post"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" id="item_id" name="id">
                        <input type="hidden" id="img_name" name="image">
                        <input type="file" name="product_image" class="form-control" id="" required>
                        @error('product_image')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="modal-footer">
                        <button type="submit"
                            class="btn btn-secondary px-4 m-0 rounded-start-5 rounded-end-5 {{ Auth::user()->type == 4 ? (helper::check_access('role_products', Auth::user()->role_id, $vendor_id, 'edit') == 1 ? '' : 'd-none') : '' }}">{{ trans('labels.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal modal-fade-transform" id="addvariantModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-inner lg-dialog" role="document">
            <div class="modal-content">
                <div class="popup-content">
                    <div class="modal-header justify-content-between popup-header align-items-center">
                        <div class="modal-title text-dark color-changer">
                            <h5 class="mb-0" id="modelCommanModelLabel">{{ trans('labels.add_variants') }}</h5>
                        </div>
                        <button type="button" class="bg-transparent border-0 m-0" data-bs-dismiss="modal"
                            aria-label="Close">
                            <i class="fa-regular fa-xmark color-changer fs-4"></i>
                        </button>
                    </div>
                    <div class="modal-body px-0 pt-3 p-0">
                        <form method="POST"
                            action="{{ URL::to('admin/products/get-product-variants-possibilities') }}">
                            @csrf
                            <div class="px-3 mb-3">
                                <div class="form-group">
                                    <label for="variant_name" class="form-label">{{ trans('labels.variant_name') }}</label>
                                    <input class="form-control" name="variant_name" type="text" id="variant_name"
                                        onkeyup="this.value = this.value.replace(/[^a-zA-Z0-9]/g, '')"
                                        placeholder="{{ 'Variant Name, i.e Size, Color etc' }}">
                                </div>
                                <div class="form-group">
                                    <label for="variant_options" class="form-label">{{ trans('labels.variant_options') }}</label>
                                    <input class="form-control" name="variant_options" type="text"
                                        id="variant_options"
                                        placeholder="{{ 'Variant Options separated by|pipe symbol, i.e Black|Blue|Red' }}">
                                </div>
                            </div>
                            <div class="modal-footer p-3">
                                <div class="form-group m-0 gap-2 d-flex justify-content-end">
                                    <input type="button" value="{{ trans('labels.cancel') }}"
                                        class="btn btn-danger px-4 rounded-start-5 rounded-end-5" data-bs-dismiss="modal">
                                    <input type="button" value="{{ trans('labels.add_variants') }}"
                                        class="add-variants btn btn-secondary px-4 rounded-start-5 rounded-end-5">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="modal modal-fade-transform" id="commonModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-inner lg-dialog" role="document">
            <div class="modal-content">
                <div class="popup-content">
                    <div class="modal-header justify-content-between popup-header align-items-center">
                        <h5 class="modal-title color-changer text-dark" id="modelCommanModelLabel">
                            {{ trans('labels.add_variants') }}</h5>
                        <button type="button" class="bg-transparent border-0 m-0" data-bs-dismiss="modal"
                            aria-label="Close">
                            <i class="fa-regular fa-xmark color-changer fs-4"></i>
                        </button>
                    </div>
                    <div class="modal-body px-0 pb-0">
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        var extrasurl = "{{ URL::to('admin/editgetextras-' . $getproductdata->id) }}";
        var placehodername = "{{ trans('labels.name') }}";
        var placeholderprice = "{{ trans('labels.price') }}";
        var page = "edit";
        var vendor_id = "{{ Auth::user()->id }}";
    </script>
    <script>
        $(document).on('click', '.get-variants', function(e) {
            $("#commonModal .modal-title").html('{{ __('Add Variants ') }}');
            $("#commonModal .modal-dialog").addClass('modal-md');
            $("#commonModal").modal('show');
            var data_url = $(this).attr('dataa-url');

            $.get(data_url, {}, function(data) {
                $('#commonModal .modal-body').html(data);
            });
        });

        $(document).on('click', '.add-variants', function(e) {
            e.preventDefault();
            var form = $(this).parents('form');
            var variantNameEle = $('#variant_name');
            var variantOptionsEle = $('#variant_options');
            var isValid = true;
            var hiddenVariantOptions = $('#hiddenVariantOptions').val();

            if (variantNameEle.val() == '') {
                variantNameEle.focus();
                isValid = false;
            } else if (variantOptionsEle.val() == '') {
                variantOptionsEle.focus();
                isValid = false;
            }

            if (isValid) {
                $.ajax({
                    url: form.attr('action'),
                    datType: 'json',
                    data: {
                        variant_name: variantNameEle.val(),
                        variant_options: variantOptionsEle.val(),
                        hiddenVariantOptions: hiddenVariantOptions
                    },
                    success: function(data) {
                        if (data.message != "" && data.message != null) {
                            toastr.error(data.message);
                        }
                        $('#hiddenVariantOptions').val(data.hiddenVariantOptions);
                        $('.variant-table').html(data.varitantHTML);
                        $('#variant_card').removeClass('d-none');
                        $("#commonModal").modal('hide');
                    }
                })
            }
        });
    </script>
    <script>
        function validation(value, id) {
            if (value.includes('@')) {
                newval = value.replaceAll('@', '');
                $('#' + id).val(newval);
            }
            if (value.includes('\\')) {
                newval = value.replaceAll('\\', '');
                $('#' + id).val(newval);
            }
        }
    </script>
    <script>
        $(document).ready(function() {

            $('.sort_menu').sortable({
                handle: '.handle',
                cursor: 'move',
                placeholder: 'highlight',
                axis: "x,y",

                update: function(e, ui) {
                    var sortData = $('.sort_menu').sortable('toArray', {
                        attribute: 'data-id'
                    })
                    updateToDatabase(sortData.join('|'))
                }
            })

            function updateToDatabase(idString) {

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    dataType: "json",
                    url: $('#carddetails').attr('data-url'),
                    data: {
                        ids: idString,
                    },
                    success: function(response) {
                        if (response.status == 1) {
                            toastr.success(response.msg);
                        } else {
                            toastr.success(wrong);
                        }
                    }
                });
            }

        })
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.12.1/ckeditor.js"></script>
    <script type="text/javascript">
        CKEDITOR.replace('ckeditor');
    </script>
    <script src="{{ url(env('ASSETSPATHURL') . 'admin-assets/js/product.js') }}"></script>
@endsection
