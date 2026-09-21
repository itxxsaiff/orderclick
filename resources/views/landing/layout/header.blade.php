@php
    $ocRegisterUrl = env('Environment') == 'sendbox'
        ? URL::to('/admin')
        : (helper::appdata('')->vendor_register == 1 ? URL::to('/admin/register') : URL::to('/admin'));
    $ocIsAr = app()->getLocale() === 'ar';
@endphp
<nav class="ocl ocl-nav">
    <div class="wrap ocl-bar">
        <button class="burger" data-bs-toggle="offcanvas" data-bs-target="#oclMobileMenu" aria-controls="oclMobileMenu" aria-label="Menu"><i class="fa-solid fa-bars"></i></button>
        <a href="{{ URL::to('/') }}" class="logo"><img src="{{ helper::image_path(helper::appdata('')->logo) }}" alt="logo"></a>
        <div class="links">
            <a href="{{ URL::to('/#categories') }}">{{ trans('landing.categories') }}</a>
            <a href="{{ URL::to('/#how') }}">{{ trans('landing.how_it_works') }}</a>
            @if (App\Models\SystemAddons::where('unique_identifier', 'subscription')->first() != null &&
                    App\Models\SystemAddons::where('unique_identifier', 'subscription')->first()->activated == 1)
                <a href="{{ URL::to('/#pricing') }}">{{ trans('landing.pricing_plan') }}</a>
            @endif
            <a href="{{ URL::to('marketplace') }}">{{ trans('landing.marketplace') }}</a>
            @if (App\Models\SystemAddons::where('unique_identifier', 'blog')->first() != null &&
                    App\Models\SystemAddons::where('unique_identifier', 'blog')->first()->activated == 1)
                <a href="{{ URL::to('blog_list') }}">{{ trans('landing.blogs') }}</a>
            @endif
            <a href="{{ URL::to('/#contact') }}">{{ trans('landing.contact_us') }}</a>
        </div>
        <div class="cta">
            @if (App\Models\SystemAddons::where('unique_identifier', 'language')->first() != null &&
                    App\Models\SystemAddons::where('unique_identifier', 'language')->first()->activated == 1)
                <div class="lang dropdown">
                    <a href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="{{ helper::image_path(session()->get('flag')) }}" alt="lang">
                    </a>
                    <ul class="dropdown-menu shadow border-0 p-0 mt-2 {{ session()->get('direction') == 2 ? 'min-dropdown-rtl' : 'min-dropdown-ltr' }}">
                        @foreach (helper::listoflanguage() as $languagelist)
                            <li><a class="dropdown-item p-2 gap-2 fs-8 d-flex align-items-center" href="{{ URL::to('/lang/change?lang=' . $languagelist->code) }}">
                                <img src="{{ helper::image_path($languagelist->image) }}" alt="" class="language-items-img" style="width:20px;height:20px;border-radius:50%;">
                                <span>{{ $languagelist->name }}</span>
                            </a></li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <a href="{{ URL::to('/admin') }}" class="ocl-btn ocl-btn--ghost">{{ trans('landing.login') }}</a>
            <a href="{{ $ocRegisterUrl }}" class="ocl-btn ocl-btn--primary">{{ trans('landing.get_started') }}</a>
        </div>
    </div>
</nav>

{{-- Redesigned mobile menu — matches the clean landing theme; links mirror the desktop nav. --}}
@php
    $ocSubOn = App\Models\SystemAddons::where('unique_identifier', 'subscription')->where('activated', 1)->exists();
    $ocBlogOn = App\Models\SystemAddons::where('unique_identifier', 'blog')->where('activated', 1)->exists();
    $ocLangOn = App\Models\SystemAddons::where('unique_identifier', 'language')->where('activated', 1)->exists();
@endphp
<div class="ocl offcanvas offcanvas-{{ session()->get('direction') == 2 ? 'end' : 'start' }} ocl-mnav"
    tabindex="-1" id="oclMobileMenu" aria-labelledby="oclMobileMenuLabel">
    <div class="ocl-mnav__head">
        <a href="{{ URL::to('/') }}" class="ocl-mnav__logo"><img src="{{ helper::image_path(helper::appdata('')->logo) }}" alt="logo"></a>
        <button type="button" class="ocl-mnav__close" data-bs-dismiss="offcanvas" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="ocl-mnav__body">
        <nav class="ocl-mnav__links">
            <a href="{{ URL::to('/#categories') }}"><span class="ic"><i class="fa-solid fa-grip"></i></span>{{ trans('landing.categories') }}<i class="fa-solid fa-chevron-{{ $ocIsAr ? 'left' : 'right' }} arw"></i></a>
            <a href="{{ URL::to('/#how') }}"><span class="ic"><i class="fa-solid fa-wand-magic-sparkles"></i></span>{{ trans('landing.how_it_works') }}<i class="fa-solid fa-chevron-{{ $ocIsAr ? 'left' : 'right' }} arw"></i></a>
            @if ($ocSubOn)
                <a href="{{ URL::to('/#pricing') }}"><span class="ic"><i class="fa-solid fa-tag"></i></span>{{ trans('landing.pricing_plan') }}<i class="fa-solid fa-chevron-{{ $ocIsAr ? 'left' : 'right' }} arw"></i></a>
            @endif
            <a href="{{ URL::to('marketplace') }}"><span class="ic"><i class="fa-solid fa-store"></i></span>{{ trans('landing.marketplace') }}<i class="fa-solid fa-chevron-{{ $ocIsAr ? 'left' : 'right' }} arw"></i></a>
            @if ($ocBlogOn)
                <a href="{{ URL::to('blog_list') }}"><span class="ic"><i class="fa-solid fa-newspaper"></i></span>{{ trans('landing.blogs') }}<i class="fa-solid fa-chevron-{{ $ocIsAr ? 'left' : 'right' }} arw"></i></a>
            @endif
            <a href="{{ URL::to('/#contact') }}"><span class="ic"><i class="fa-solid fa-headset"></i></span>{{ trans('landing.contact_us') }}<i class="fa-solid fa-chevron-{{ $ocIsAr ? 'left' : 'right' }} arw"></i></a>
            <a href="{{ URL::to('marketplace#find-store') }}"><span class="ic"><i class="fa-solid fa-magnifying-glass"></i></span>{{ trans('landing.search_store') }}<i class="fa-solid fa-chevron-{{ $ocIsAr ? 'left' : 'right' }} arw"></i></a>
        </nav>

        @if ($ocLangOn)
            <div class="ocl-mnav__lang">
                <span class="ocl-mnav__lbl">{{ trans('landing.language') }}</span>
                <div class="ocl-mnav__flags">
                    @foreach (helper::listoflanguage() as $languagelist)
                        <a href="{{ URL::to('/lang/change?lang=' . $languagelist->code) }}" class="{{ session()->get('flag') == $languagelist->image ? 'on' : '' }}">
                            <img src="{{ helper::image_path($languagelist->image) }}" alt="{{ $languagelist->name }}">
                            <span>{{ $languagelist->name }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
    <div class="ocl-mnav__foot">
        <a href="{{ URL::to('/admin') }}" class="ocl-btn ocl-btn--ghost">{{ trans('landing.login') }}</a>
        <a href="{{ $ocRegisterUrl }}" class="ocl-btn ocl-btn--primary">{{ trans('landing.get_started') }}</a>
    </div>
</div>

{{-- Mobile menu actions. Bootstrap's own data-bs-dismiss on an <a> calls preventDefault()
     and picks its target from the href hash, so in-page links did nothing at all.
     We close the drawer ourselves, then scroll (or open the search modal) once it is hidden.
     Bootstrap's bundle loads below this markup, so everything is resolved at click time. --}}
<script>
    document.addEventListener('click', function (e) {
        var menu = document.getElementById('oclMobileMenu');
        if (!menu || !window.bootstrap) return;

        var link = e.target.closest ? e.target.closest('a') : null;
        if (!link || !menu.contains(link)) return;

        var modal = link.getAttribute('data-oc-modal');
        var hash = '';
        if (!modal) {
            try {
                var u = new URL(link.href, location.href);
                if (u.hash && u.pathname === location.pathname && u.search === location.search) {
                    hash = u.hash;
                }
            } catch (err) {}
        }
        // Links to another page navigate normally; the drawer goes with the old page.
        if (!modal && !hash) return;

        e.preventDefault();
        var target = document.querySelector(modal || hash);
        var run = function () {
            menu.removeEventListener('hidden.bs.offcanvas', run);
            if (!target) return;
            if (modal) {
                bootstrap.Modal.getOrCreateInstance(target).show();
                return;
            }
            var nav = document.querySelector('.ocl-nav');
            var top = target.getBoundingClientRect().top + window.pageYOffset - (nav ? nav.offsetHeight : 0);
            window.scrollTo({ top: top > 0 ? top : 0, behavior: 'smooth' });
            if (history.replaceState) history.replaceState(null, '', hash);
        };
        menu.addEventListener('hidden.bs.offcanvas', run);
        bootstrap.Offcanvas.getOrCreateInstance(menu).hide();
    });
</script>

@include('cookie-consent::index')
