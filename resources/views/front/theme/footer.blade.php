<!-- Footer Section Start -->
<footer class="mt-25 mb-lg-0 mb-5 pb-lg-0 bg-changer pb-3 d-none d-lg-block">
    <div class="container">
        <div class="row pt-5">
            <div class="col-lg-4 col-md-12 col-12 pb-md-4 pb-lg-0 mb-md-4 mb-lg-0">
                <script>
                    document.addEventListener("DOMContentLoaded", function(event) {
                        if (localStorage.getItem('theme') === 'dark') {
                            var logo = "{{ helper::image_path(helper::appdata($vdata)->darklogo) }}";
                        } else {
                            var logo = "{{ helper::image_path(helper::appdata($vdata)->logo) }}";
                        }
                        $('#footerlogoimage').attr('src', logo);
                    });
                    
                </script>
                <a href="{{ URL::to(@$storeinfo->slug) }}" class="footer-logo text-white">
                    <img src="" alt="" id="footerlogoimage">
                </a>
                <p class="footersubtitle"> {{ helper::appdata($vdata)->description }}</p>
                @if (@helper::app_settings($vdata)->mobile_app_on_off == 1)
                <div class="my-4 d-flex gap-2 app_download_img">
                    <a class="border p-1 rounded-2 border-light"
                        href="{{ @helper::app_settings($vdata)->android_link }}" target="_blank">
                        <img src="{{ url('storage/app/public/web-assets/iamges/svg/google-play.svg') }}"
                            width="140" height="37" alt="">
                    </a>
                    <a class="border p-1 rounded-2 border-light"
                        href="{{ @helper::app_settings($vdata)->ios_link }}" target="_blank">
                        <img src="{{ url('storage/app/public/web-assets/iamges/svg/app-store.svg') }}"
                            width="140" height="37" alt="">
                    </a>
                </div>
                @endif
            </div>
            <hr class="w-100 clearfix d-md-none" />
            <div class="col-lg-8 col-md-12 col-12">
                <div class="row g-4 justify-content-lg-end justify-content-md-between">
                    <div class="col-lg-4 col-md-6 col-6">
                        <h5 class="footer-title">{{ trans('labels.links') }}</h5>
                        <ul class="footer-right-side">
                            <li>
                                <a href="{{ URL::to(@$storeinfo->slug) }}" class="mb-3">
                                    {{ trans('labels.home') }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ URL::to(@$storeinfo->slug . '/contact') }}" class="mb-3">
                                    {{ trans('labels.contact_us') }}
                                </a>
                            </li>
                            @if (App\Models\SystemAddons::where('unique_identifier', 'blog')->first() != null &&
                            App\Models\SystemAddons::where('unique_identifier', 'blog')->first()->activated == 1)
                            @php

                            if (helper::vendordata(@$vdata)->allow_without_subscription == 1) {
                            $blog = 1;
                            } else {
                            $blog = @helper::get_plan($vdata)->blogs;
                            }
                            @endphp
                            @if ($blog == 1)
                            <li>
                                <a href="{{ URL::to(@$storeinfo->slug . '/blog-list') }}" class="mb-3">
                                    {{ trans('labels.blogs') }}
                                </a>
                            </li>
                            @endif
                            @endif
                        </ul>
                    </div>
                    <div class="col-lg-4 col-md-6 col-6">
                        <h5 class="footer-title">{{ trans('labels.other_pages') }}</h5>
                        <ul class="footer-right-side">
                            <li>
                                <a href="{{ URL::to(@$storeinfo->slug . '/aboutus') }}" class="mb-3">
                                    {{ trans('labels.about_us') }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ URL::to(@$storeinfo->slug . '/faqshow') }}" class="mb-3">
                                    {{ trans('labels.faqs') }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ URL::to(@$storeinfo->slug . '/terms_condition') }}" class="mb-3">
                                    {{ trans('labels.terms') }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ URL::to(@$storeinfo->slug . '/privacypolicy') }}" class="mb-3">
                                    {{ trans('labels.privacy_policy') }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ URL::to(@$storeinfo->slug . '/refundprivacypolicy') }}" class="mb-3">
                                    {{ trans('labels.refund_policy') }}
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="col-lg-4 col-md-6 col-12">
                        <h5 class="footer-title">{{ trans('labels.infromation') }}</h5>
                        <ul class="footer-right-side">
                            <li class="d-flex gap-2 align-items-center">
                                <i class="fa-solid fs-15 fa-location-dot"></i>
                                <span>
                                    <a href="https://www.google.com/maps/place/323/{{ helper::appdata($storeinfo->id)->address }}"
                                        class="">{{ helper::appdata($storeinfo->id)->address }}</a>
                                </span>
                            </li>
                            <li class="d-flex gap-2 align-items-center">
                                <i class="fa-solid fs-15 fa-headphones"></i>
                                <span class=""> <a
                                        href="tel:{{ helper::appdata($storeinfo->id)->contact }}">{{ helper::appdata($storeinfo->id)->contact }}</a>
                                </span>
                            </li>
                            <li class="d-flex gap-2 align-items-center">
                                <i class="fa-regular fs-15 fa-envelope"></i>
                                <span class="">
                                    <a href="mailto:{{ helper::appdata($storeinfo->id)->email }}">
                                        {{ helper::appdata($storeinfo->id)->email }}</a>
                                </span>
                            </li>
                            <li class="d-flex gap-2 align-items-center">
                                <i class="fa-regular fs-15 fa-circle-question"></i>
                                <span class="">
                                    <a href="#" data-bs-toggle="modal"
                                        data-bs-target="#examplehours">{{ trans('labels.hours') }}</a>
                                </span>
                            </li>
                        </ul>

                    </div>
                </div>
            </div>
        </div>
        <div class="row g-4 align-items-end justify-content-between mt-0 mt-md-4">
            <!-- Payment card -->
            <div class="col-sm-7 col-md-6 col-lg-4">
                <h5 class="mb-2 text-white fw-bold ">{{ trans('labels.payment_methods') }} &amp;
                    {{ trans('labels.security') }}
                </h5>
                <ul class="list-inline mb-0 mt-3 d-flex flex-wrap gap-2">
                    @foreach (helper::getallpayment($vdata) as $payment)
                    <li class="list-inline-item m-0">
                        <img src="{{ helper::image_path($payment->image) }}" class="h-30px" alt="">
                    </li>
                    @endforeach
                </ul>
            </div>

            <!-- Social media icon -->
            @if (count(helper::getsociallinks($vdata)) > 0)
            <div class="col-sm-5 col-md-6 col-lg-3 social-media text-sm-end">
                <h5 class="mb-2 fw-bold text-white mb-2 fw-bold text-white d-flex justify-content-sm-end">
                    {{ trans('labels.follow_us') }}
                </h5>
                <ul class="list-inline mb-0 mt-3  d-flex flex-wrap gap-2 justify-content-sm-end">
                    @foreach (@helper::getsociallinks($vdata) as $links)
                    <li class="list-inline-item m-0">
                        <a class="btn-social mb-0 fb" role="button"
                            href="{{ $links->link }}">{!! $links->icon !!}</a>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>
        <hr class="my-3">
        <div class="d-flex align-items-center justify-content-center pb-3">
            <p class="fs-7 md-mb-0 lg-mb-0 xl-mb-0 text-center">
                {{ helper::appdata($storeinfo->id)->copyright }}
            </p>
        </div>
    </div>
</footer>

<div class="offcanvas {{ session()->get('direction') == 2 ? 'offcanvas-end' : 'offcanvas-start' }}" tabindex="-1"
    id="footersiderbar" aria-labelledby="footersiderbar">
    <div class="offcanvas-header justify-content-between border-bottom">
        <script>
             document.addEventListener("DOMContentLoaded", function(event) {
                if (localStorage.getItem('theme') === 'dark') {
                    var logo = "{{ helper::image_path(helper::appdata($storeinfo->id)->darklogo) }}";
                } else {
                    var logo = "{{ helper::image_path(helper::appdata($storeinfo->id)->logo) }}";
                }
                $('#footerlogoimage').attr('src', logo);
            });
        </script>
        <a href="{{ URL::to(@$storeinfo->slug) }}" class="footer-logo text-white">
            <img src="" alt="" class="m-0" id="footerlogoimage">
        </a>
        <button type="button" class="bg-transparent border-0 m-0" data-bs-dismiss="offcanvas" aria-label="Close">
            <i class="fa-regular fa-xmark color-changer fs-4"></i>
        </button>
    </div>
    <div class="offcanvas-body">
        <h5 class="text-dark text-capitalize border-bottom pb-3 m-0 fw-600 color-changer">
            {{ trans('labels.links') }}
        </h5>
        <ul class="list-group list-add list-group-flush border-bottom">
            <li class="list-group-item px-0 py-3 {{ session()->get('direction') == 2 ? 'pe-3' : 'ps-3' }}">
                <a class="fs-7 fw-500 d-flex gap-2 align-items-center color-changer" href="{{ URL::to(@$storeinfo->slug) }}">
                    <i class="fa-solid fa-circle-dot fs-7"></i>
                    {{ trans('labels.home') }}
                </a>
            </li>
            <li class="list-group-item px-0 py-3 {{ session()->get('direction') == 2 ? 'pe-3' : 'ps-3' }}">
                <a class="fs-7 fw-500 d-flex gap-2 align-items-center color-changer"
                    href="{{ URL::to(@$storeinfo->slug . '/contact') }}">
                    <i class="fa-solid fa-circle-dot fs-7"></i>
                    {{ trans('labels.contact_us') }}
                </a>
            </li>
            @if (App\Models\SystemAddons::where('unique_identifier', 'blog')->first() != null &&
            App\Models\SystemAddons::where('unique_identifier', 'blog')->first()->activated == 1)
            @php

            if (helper::vendordata(@$vdata)->allow_without_subscription == 1) {
            $blog = 1;
            } else {
            $blog = @helper::get_plan($storeinfo->id)->blogs;
            }
            @endphp
            @if ($blog == 1)
            <li class="list-group-item px-0 py-3 {{ session()->get('direction') == 2 ? 'pe-3' : 'ps-3' }}">
                <a class="fs-7 fw-500 d-flex gap-2 align-items-center color-changer"
                    href="{{ URL::to(@$storeinfo->slug . '/blog-list') }}">
                    <i class="fa-solid fa-circle-dot fs-7"></i>
                    {{ trans('labels.blogs') }}
                </a>
            </li>
            @endif
            @endif
        </ul>
        <h5 class="text-dark text-capitalize border-bottom py-3 m-0 fw-600 color-changer">
            {{ trans('labels.other_pages') }}
        </h5>
        <ul class="list-group list-add list-group-flush border-bottom">
            <li class="list-group-item px-0 py-3 {{ session()->get('direction') == 2 ? 'pe-3' : 'ps-3' }}">
                <a class="fs-7 fw-500 d-flex gap-2 align-items-center color-changer"
                    href="{{ URL::to(@$storeinfo->slug . '/aboutus') }}">
                    <i class="fa-solid fa-circle-dot fs-7"></i>
                    {{ trans('labels.about_us') }}
                </a>
            </li>
            <li class="list-group-item px-0 py-3 {{ session()->get('direction') == 2 ? 'pe-3' : 'ps-3' }}">
                <a class="fs-7 fw-500 d-flex gap-2 align-items-center color-changer"
                    href="{{ URL::to(@$storeinfo->slug . '/faqshow') }}">
                    <i class="fa-solid fa-circle-dot fs-7"></i>
                    {{ trans('labels.faqs') }}
                </a>
            </li>
            <li class="list-group-item px-0 py-3 {{ session()->get('direction') == 2 ? 'pe-3' : 'ps-3' }}">
                <a class="fs-7 fw-500 d-flex gap-2 align-items-center color-changer"
                    href="{{ URL::to(@$storeinfo->slug . '/terms_condition') }}">
                    <i class="fa-solid fa-circle-dot fs-7"></i>
                    {{ trans('labels.terms') }}
                </a>
            </li>
            <li class="list-group-item px-0 py-3 {{ session()->get('direction') == 2 ? 'pe-3' : 'ps-3' }}">
                <a class="fs-7 fw-500 d-flex gap-2 align-items-center color-changer"
                    href="{{ URL::to(@$storeinfo->slug . '/privacypolicy') }}">
                    <i class="fa-solid fa-circle-dot fs-7"></i>
                    {{ trans('labels.privacy_policy') }}
                </a>
            </li>
            <li class="list-group-item px-0 py-3 {{ session()->get('direction') == 2 ? 'pe-3' : 'ps-3' }}">
                <a class="fs-7 fw-500 d-flex gap-2 align-items-center color-changer"
                    href="{{ URL::to(@$storeinfo->slug . '/refundprivacypolicy') }}">
                    <i class="fa-solid fa-circle-dot fs-7"></i>
                    {{ trans('labels.refund_policy') }}
                </a>
            </li>
        </ul>
        <h5 class="text-dark text-capitalize py-3 color-changer m-0 fw-600">{{ trans('labels.infromation') }}</h5>
        <ul class="list-add">
            <li class="py-2">
                <a href="https://www.google.com/maps/place/323/{{ helper::appdata($storeinfo->id)->address }}"
                    class="fs-7 fw-500 d-flex gap-2 align-items-center color-changer">
                    <i class="fa-regular fa-house fs-7"></i>
                    {{ helper::appdata($storeinfo->id)->address }}
                </a>
            </li>
            <li class="py-2">
                <a class="fs-7 fw-500 d-flex gap-2 align-items-center color-changer"
                    href="tel:{{ helper::appdata($storeinfo->id)->contact }}">
                    <i class="fa-solid fa-phone fs-7"></i>
                    {{ helper::appdata($storeinfo->id)->contact }}
                </a>
            </li>
            <li class="py-2">
                <a class="fs-7 fw-500 d-flex gap-2 align-items-center color-changer"
                    href="mailto:{{ helper::appdata($storeinfo->id)->email }}">
                    <i class="fa-solid fa-envelope fs-7"></i>
                    {{ helper::appdata($storeinfo->id)->email }}
                </a>
            </li>
            <li class="py-2">
                <a class="fs-7 fw-500 d-flex gap-2 align-items-center color-changer" href="#" data-bs-toggle="modal"
                    data-bs-target="#examplehours" data-bs-whatever="@mdo">
                    <i class="fa-solid fs-7 fa-circle-question"></i>
                    {{ trans('labels.hours') }}
                </a>
            </li>
        </ul>
        <!-- Social media icon -->
        @if (count(helper::getsociallinks($storeinfo->id)) > 0)
        <div class="social-media">
            <h5 class="text-dark text-capitalize pt-3 color-changer border-top mt-2 m-0 fw-600">
                {{ trans('labels.follow_us') }}
            </h5>
            <ul class="d-flex flex-wrap gap-3 mt-3 mb-0">
                @foreach (@helper::getsociallinks($storeinfo->id) as $links)
                <li class="border border-primary border-1 rounded-circle">
                    <a class="btn-social mb-0 fb" role="button"
                        href="{{ $links->link }}">{!! $links->icon !!}</a>
                </li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>
    <div class="offcanvas-footer bg-dark border-top">
        <p class="m-0 fs-7 text-center text-light fw-500 px-2 py-2">
            {{ helper::appdata($storeinfo->id)->copyright }}
        </p>
    </div>
</div>