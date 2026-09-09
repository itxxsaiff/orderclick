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
            </div>
            <div>
                <h5>{{ $ocIsAr ? 'روابط' : 'Links' }}</h5>
                <ul>
                    <li><a href="{{ URL::to('/#home') }}">{{ trans('landing.home') }}</a></li>
                    <li><a href="{{ URL::to('/#categories') }}">{{ $ocIsAr ? 'الفئات' : 'Categories' }}</a></li>
                    <li><a href="{{ URL::to('/#pricing') }}">{{ trans('landing.pricing_plan') }}</a></li>
                    <li><a href="{{ URL::to('stores') }}">{{ trans('landing.our_stores') }}</a></li>
                </ul>
            </div>
            <div>
                <h5>{{ $ocIsAr ? 'صفحات' : 'Pages' }}</h5>
                <ul>
                    <li><a href="{{ URL::to('blog_list') }}">{{ trans('landing.blogs') }}</a></li>
                    <li><a href="{{ URL::to('about_us') }}">{{ $ocIsAr ? 'من نحن' : 'About us' }}</a></li>
                    <li><a href="{{ URL::to('privacy_policy') }}">{{ $ocIsAr ? 'سياسة الخصوصية' : 'Privacy Policy' }}</a></li>
                    <li><a href="{{ URL::to('terms_condition') }}">{{ $ocIsAr ? 'الشروط والأحكام' : 'Terms & Conditions' }}</a></li>
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
        <div class="barbottom">© {{ date('Y') }} {{ helper::appdata('')->website_title }}. {{ $ocIsAr ? 'جميع الحقوق محفوظة.' : 'All rights reserved.' }}</div>
    </div>
</footer>
