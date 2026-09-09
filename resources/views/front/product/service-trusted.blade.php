@if (request()->is($storeinfo->slug . '/details-*'))
    @if (App\Models\SystemAddons::where('unique_identifier', 'trusted_badges')->first() != null &&
            App\Models\SystemAddons::where('unique_identifier', 'trusted_badges')->first()->activated == 1)
        <div class="col-12 my-3 p-3 border-top">
            <div class="row g-3 product-detile">
                @if (@helper::otherappdata($storeinfo->id)->trusted_badge_image_1)
                    <div class="col-lg-3 col-6">
                        <div class="service-content">
                            <img src="{{ helper::image_path(@helper::otherappdata($storeinfo->id)->trusted_badge_image_1) }}"
                                alt="">
                        </div>
                    </div>
                @endif
                @if (@helper::otherappdata($storeinfo->id)->trusted_badge_image_2)
                    <div class="col-lg-3 col-6">
                        <div class="service-content">
                            <img src="{{ helper::image_path(@helper::otherappdata($storeinfo->id)->trusted_badge_image_2) }}"
                                alt="">
                        </div>
                    </div>
                @endif
                @if (@helper::otherappdata($storeinfo->id)->trusted_badge_image_3)
                    <div class="col-lg-3 col-6">
                        <div class="service-content">
                            <img src="{{ helper::image_path(@helper::otherappdata($storeinfo->id)->trusted_badge_image_3) }}"
                                alt="">
                        </div>
                    </div>
                @endif
                @if (@helper::otherappdata($storeinfo->id)->trusted_badge_image_4)
                    <div class="col-lg-3 col-6">
                        <div class="service-content">
                            <img src="{{ helper::image_path(@helper::otherappdata($storeinfo->id)->trusted_badge_image_4) }}"
                                alt="">
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif
@endif

@if (App\Models\SystemAddons::where('unique_identifier', 'safe_secure_checkout')->first() != null &&
        App\Models\SystemAddons::where('unique_identifier', 'safe_secure_checkout')->first()->activated == 1)
    @if (@helper::otherappdata($storeinfo->id)->safe_secure_checkout_payment_selection)
        <div
            class="col-12 py-4 p-3 bg-changer rounded-3 sevirce-trued {{ request()->is($storeinfo->slug . '/details-*') ? '' : 'my-3' }}">
            <div class="d-flex mb-2 pb-1 flex-wrap gap-2 justify-content-center aling-items-center">
                @foreach (helper::getallpayment($storeinfo->id) as $stpayment)
                    @if (
                        @in_array(
                            $stpayment->payment_type,
                            explode(',', helper::otherappdata($storeinfo->id)->safe_secure_checkout_payment_selection)))
                        <div class="sevirce-tru">
                            <div class="img">
                                <img class="border rounded-2" src="{{ helper::image_path($stpayment->image) }}"
                                    alt="">
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
            <h6 class="fs-15 text-center fw-normal"
                style="color: {{ @helper::otherappdata($storeinfo->id)->safe_secure_checkout_text_color }}">
                {{ @helper::otherappdata($storeinfo->id)->safe_secure_checkout_text }}
            </h6>
        </div>
    @endif
@endif
