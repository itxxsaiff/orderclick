@php
    $ocIsAr = app()->getLocale() === 'ar';
    $ocRegisterUrl = env('Environment') == 'sendbox'
        ? URL::to('/admin')
        : (helper::appdata('')->vendor_register == 1 ? URL::to('/admin/register') : URL::to('/admin'));
@endphp
<footer class="ocl ocl-foot">
    <div class="wrap">
        <div class="top">
            <div>
                <img src="{{ helper::image_path(helper::appdata('')->logo) }}" alt="logo">
                <p>{{ \Illuminate\Support\Str::limit(helper::appdata('')->meta_description ?: helper::appdata('')->website_title, 160) }}</p>
                {{-- Social links: managed in Admin > Settings > Social Links. Only the icon class is
                     taken from the stored icon markup, so nothing else from it reaches the page. --}}
                @php $ocSocials = \App\Models\SocialLinks::where('vendor_id', 1)->whereNotNull('link')->where('link', '!=', '')->get(); @endphp
                @if ($ocSocials->count())
                    <div class="ocl-socials">
                        @foreach ($ocSocials as $soc)
                            @php preg_match('/class="([a-z0-9 \-]+)"/i', (string) $soc->icon, $ocIc); @endphp
                            <a href="{{ $soc->link }}" target="_blank" rel="noopener" aria-label="{{ trim(str_replace(['fa-brands', 'fa-'], '', $ocIc[1] ?? 'link')) }}">
                                <i class="{{ $ocIc[1] ?? 'fa-solid fa-link' }}"></i>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
            <div>
                <h5>{{ trans('landing.links') }}</h5>
                <ul>
                    <li><a href="{{ URL::to('/#home') }}">{{ trans('landing.home') }}</a></li>
                    <li><a href="{{ URL::to('/#categories') }}">{{ trans('landing.categories') }}</a></li>
                    <li><a href="{{ URL::to('/#pricing') }}">{{ trans('landing.pricing_plan') }}</a></li>
                    <li><a href="{{ URL::to('stores') }}">{{ trans('landing.our_stores') }}</a></li>
                </ul>
            </div>
            <div>
                <h5>{{ trans('landing.pages') }}</h5>
                <ul>
                    <li><a href="{{ URL::to('blog_list') }}">{{ trans('landing.blogs') }}</a></li>
                    <li><a href="{{ URL::to('about_us') }}">{{ trans('landing.about_us_2') }}</a></li>
                    <li><a href="{{ URL::to('privacy_policy') }}">{{ trans('landing.privacy_policy') }}</a></li>
                    <li><a href="{{ URL::to('terms_condition') }}">{{ trans('landing.terms_conditions') }}</a></li>
                </ul>
            </div>
            <div>
                <h5>{{ trans('landing.contact_us') }}</h5>
                <ul>
                    <li><a href="mailto:{{ helper::appdata('')->email }}">{{ helper::appdata('')->email }}</a></li>
                    <li><a href="tel:{{ helper::appdata('')->contact }}">{{ helper::appdata('')->contact }}</a></li>
                    <li><a href="{{ $ocRegisterUrl }}">{{ trans('landing.get_started') }}</a></li>
                </ul>
            </div>
        </div>
        <div class="barbottom">© {{ date('Y') }} {{ helper::appdata('')->website_title }}. {{ trans('landing.all_rights_reserved') }}</div>
    </div>
</footer>
