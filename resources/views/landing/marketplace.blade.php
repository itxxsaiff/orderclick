@extends('landing.layout.default')

@section('styles')
    <style>
        .mk { --a: #1f9d55; --ink:#141b17; --muted:#6a756c; --line:#e6ece5; --bg:#f5f8f5;
              --dark:#0d1512; --dark2:#132019; }
        .mk * { box-sizing: border-box; }
        .mk { background: var(--bg); color: var(--ink); font-family: 'Poppins', system-ui, sans-serif; }
        .mk .wrap { max-width: 1220px; margin: 0 auto; padding: 0 22px; }
        .mk a { text-decoration:none; }
        .mk button { font-family: inherit; }

        /* ===== HERO (bold dark) ===== */
        .mk-hero { position:relative; color:#fff; overflow:hidden; background:
            linear-gradient(135deg, rgba(11,18,15,.94) 0%, rgba(14,24,19,.86) 45%, rgba(16,28,22,.70) 100%),
            radial-gradient(70% 120% at 92% -10%, color-mix(in srgb, var(--a) 45%, transparent), transparent 60%),
            url('https://images.unsplash.com/photo-1550989460-0adf9ea622e2?auto=format&fit=crop&w=1920&q=75') center/cover no-repeat;
            background-color: var(--dark); }
        .mk-hero::after { content:''; position:absolute; inset:0; background-image:radial-gradient(rgba(255,255,255,.05) 1px, transparent 1px); background-size:22px 22px; opacity:.4; pointer-events:none; }
        .mk-hero__in { position:relative; z-index:2; display:grid; grid-template-columns:1.05fr .95fr; gap:48px; align-items:center; min-height:600px; padding:92px 0 104px; }
        .mk-eyebrow { display:inline-flex; align-items:center; gap:9px; background:rgba(255,255,255,.1); color:#eafff2; border:1px solid rgba(255,255,255,.14); padding:7px 14px; border-radius:30px; font-size:12.5px; font-weight:600; letter-spacing:.03em; margin-bottom:20px; }
        .mk-eyebrow i { color: color-mix(in srgb, var(--a) 55%, #fff); }
        .mk-hero h1 { font-size:clamp(34px,5vw,58px); font-weight:840; line-height:1.05; letter-spacing:-.02em; margin:0 0 18px; }
        .mk-hero h1 .hl { color: color-mix(in srgb, var(--a) 60%, #fff); }
        .mk-hero p.lead { font-size:16.5px; color:#c4cfc8; max-width:30em; margin:0 0 26px; line-height:1.6; }
        /* hero search */
        .mk-hsearch { background:rgba(255,255,255,.96); border-radius:16px; padding:10px; display:grid; grid-template-columns:1.5fr 1fr auto; gap:8px; box-shadow:0 30px 60px -34px rgba(0,0,0,.6); }
        .mk-hfld { display:flex; align-items:center; gap:8px; background:#f4f7f4; border:1px solid var(--line); border-radius:11px; padding:0 12px; height:50px; }
        .mk-hfld i { color:var(--a); font-size:14px; }
        .mk-hfld input, .mk-hfld select { border:0; background:transparent; outline:none; width:100%; font-size:14.5px; color:var(--ink); font-family:inherit; }
        .mk-hbtn { height:50px; border:0; border-radius:11px; background:var(--a); color:#fff; font-weight:650; font-size:14.5px; padding:0 22px; cursor:pointer; display:inline-flex; align-items:center; gap:8px; white-space:nowrap; }
        .mk-hbtn:hover { filter:brightness(.93); }
        .mk-trust { display:flex; gap:22px; margin-top:22px; flex-wrap:wrap; }
        .mk-trust div { font-size:13px; color:#b7c3bb; display:flex; align-items:center; gap:8px; }
        .mk-trust i { color: color-mix(in srgb, var(--a) 55%, #fff); }
        /* hero category tiles */
        .mk-htiles { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
        .mk-htile { background:rgba(255,255,255,.07); border:1px solid rgba(255,255,255,.12); border-radius:16px; padding:18px; display:flex; align-items:center; gap:13px; transition:.16s; }
        .mk-htile:hover { background:rgba(255,255,255,.13); transform:translateY(-3px); }
        .mk-htile .ic { width:46px; height:46px; border-radius:13px; background: color-mix(in srgb, var(--a) 85%, #fff); color:#fff; display:flex; align-items:center; justify-content:center; font-size:19px; flex:none; }
        .mk-htile b { color:#fff; font-size:14.5px; display:block; }
        .mk-htile span { color:#aab6ae; font-size:12px; }
        @media (max-width:900px){ .mk-hero__in { grid-template-columns:1fr; gap:32px; } .mk-htiles { grid-template-columns:1fr 1fr; } }
        @media (max-width:560px){ .mk-hsearch { grid-template-columns:1fr; } }
        /* Grid and flex children are min-width:auto by default, so they refuse to shrink below
           their content: on a 320-360px phone the hero column stayed 390px wide and the hero's
           overflow:hidden simply cut the text off. Letting them shrink fixes the whole page. */
        .mk-hero__in > *, .mk-hsearch > *, .mk-hfld, .mk-htile, .mk-htile > * { min-width: 0; }
        .mk-hfld { overflow: hidden; }
        .mk-hero h1, .mk-hero p.lead, .mk-htile b, .mk-htile span { overflow-wrap: anywhere; }
        @media (max-width:430px){
            .mk .wrap { padding: 22px 16px; }
            .mk-hero__in { min-height: 0; padding: 64px 0 72px; gap: 24px; }
            .mk-hero h1 { font-size: 30px; }
            .mk-hero p.lead { font-size: 15px; }
            .mk-htiles { grid-template-columns: 1fr; }
            .mk-htile { padding: 14px; }
            .mk-trust { gap: 14px; }
        }

        /* ===== features strip ===== */
        .mk-feat { position:relative; z-index:3; margin-top:-38px; }
        .mk-feat__box { background:#fff; border:1px solid var(--line); border-radius:18px; box-shadow:0 26px 54px -40px rgba(20,40,28,.5); display:grid; grid-template-columns:repeat(5,1fr); gap:8px; padding:22px; }
        .mk-feat__item { display:flex; align-items:center; gap:12px; padding:6px 10px; }
        .mk-feat__item .ic { width:44px; height:44px; border-radius:12px; background: color-mix(in srgb, var(--a) 11%, #fff); color:var(--a); display:flex; align-items:center; justify-content:center; font-size:18px; flex:none; }
        .mk-feat__item b { font-size:14px; display:block; color:var(--ink); }
        .mk-feat__item span { font-size:12px; color:var(--muted); }
        @media (max-width:900px){ .mk-feat__box { grid-template-columns:1fr 1fr; } }
        @media (max-width:520px){ .mk-feat__box { grid-template-columns:1fr; } }

        /* ===== filter pills ===== */
        .mk-filters { display:flex; flex-wrap:wrap; gap:9px; margin:34px 0 4px; }
        .mk-pill { display:inline-flex; align-items:center; gap:7px; background:#fff; border:1.5px solid var(--line); color:var(--ink); border-radius:30px; padding:9px 17px; font-weight:600; font-size:13.5px; cursor:pointer; transition:.15s; }
        .mk-pill:hover { border-color:var(--a); color:var(--a); }
        .mk-pill.active { background:var(--a); border-color:var(--a); color:#fff; }

        /* ===== offers banner ===== */
        .mk-offer { margin:26px 0 4px; background:linear-gradient(120deg, var(--a), color-mix(in srgb, var(--a) 60%, #000)); border-radius:20px; padding:30px 34px; color:#fff; display:flex; align-items:center; justify-content:space-between; gap:20px; flex-wrap:wrap; overflow:hidden; position:relative; }
        .mk-offer h3 { margin:0 0 6px; font-size:23px; font-weight:800; }
        .mk-offer p { margin:0; opacity:.92; font-size:14.5px; }
        .mk-offer .code { background:rgba(255,255,255,.16); border:1px dashed rgba(255,255,255,.5); border-radius:12px; padding:12px 20px; font-weight:800; letter-spacing:.08em; font-size:18px; }

        /* ===== sections & cards ===== */
        .mk-sec { padding:32px 0 6px; }
        .mk-sec__head { display:flex; align-items:center; justify-content:space-between; margin-bottom:16px; }
        .mk-sec__head h2 { font-size:21px; font-weight:750; margin:0; letter-spacing:-.01em; display:flex; align-items:center; gap:10px; }
        .mk-sec__head .cnt { color:var(--muted); font-size:13.5px; }
        .mk-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:18px; }
        @media (max-width:1080px){ .mk-grid { grid-template-columns:repeat(3,1fr);} }
        @media (max-width:820px){ .mk-grid { grid-template-columns:repeat(2,1fr);} }
        @media (max-width:520px){ .mk-grid { grid-template-columns:1fr;} }
        .mk-scroll { display:grid; grid-auto-flow:column; grid-auto-columns:minmax(262px,282px); gap:18px; overflow-x:auto; padding-bottom:8px; scrollbar-width:none; }
        .mk-scroll::-webkit-scrollbar { display:none; }

        .mk-card { display:flex; flex-direction:column; background:#fff; border:1px solid var(--line); border-radius:18px; overflow:hidden; transition:transform .16s, box-shadow .16s; color:var(--ink); }
        .mk-card:hover { transform:translateY(-4px); box-shadow:0 26px 50px -34px rgba(20,40,28,.45); }
        .mk-cover { position:relative; aspect-ratio:16/9; background:#eef2ec; }
        .mk-cover > img { width:100%; height:100%; object-fit:cover; }
        .mk-badges { position:absolute; top:10px; left:10px; display:flex; gap:6px; flex-wrap:wrap; }
        .mk-badge { display:inline-flex; align-items:center; gap:5px; font-size:11px; font-weight:700; padding:4px 9px; border-radius:20px; background:#fff; color:var(--ink); box-shadow:0 6px 14px -8px rgba(0,0,0,.4); }
        .mk-badge--open { color:#12a150; } .mk-badge--open i { font-size:7px; }
        .mk-badge--del { color:#2563eb; }
        .mk-badge--book { color:#8b5cf6; }
        .mk-logo { position:absolute; bottom:-18px; left:16px; width:52px; height:52px; border-radius:14px; background:#fff; border:1px solid var(--line); overflow:hidden; box-shadow:0 10px 20px -12px rgba(0,0,0,.4); }
        .mk-logo img { width:100%; height:100%; object-fit:cover; }
        .mk-body { padding:24px 16px 16px; display:flex; flex-direction:column; flex:1; }
        .mk-row { display:flex; align-items:flex-start; justify-content:space-between; gap:10px; }
        .mk-name { font-size:16px; font-weight:700; margin:0; line-height:1.3; }
        .mk-rate { flex:none; display:inline-flex; align-items:center; gap:4px; font-size:13.5px; font-weight:700; color:#e8a400; }
        .mk-rate i { font-size:12px; }
        .mk-meta { display:flex; align-items:center; gap:6px; flex-wrap:wrap; color:var(--muted); font-size:12.5px; margin:7px 0 0; }
        .mk-meta i { font-size:11px; }
        .mk-dot { opacity:.6; }
        .mk-desc { font-size:13px; color:var(--muted); line-height:1.5; margin:10px 0 12px; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
        .mk-status { display:flex; align-items:center; gap:16px; flex-wrap:wrap; margin:0 0 14px; font-size:12.5px; color:var(--muted); font-weight:500; }
        .mk-status span { display:inline-flex; align-items:center; gap:6px; }
        .mk-status i { font-size:12px; opacity:.85; }
        .mk-status .on { color:#12a150; font-weight:600; }
        .mk-status .on i { font-size:7px; opacity:1; }
        .mk-open-btn { margin-top:auto; display:inline-flex; align-items:center; justify-content:center; gap:8px; height:42px; border-radius:11px; background: color-mix(in srgb, var(--a) 10%, #fff); color:var(--a); font-weight:650; font-size:14px; transition:.15s; }
        .mk-card:hover .mk-open-btn { background:var(--a); color:#fff; }
        .mk-empty { text-align:center; padding:70px 20px; color:var(--muted); }
        .mk-empty i { font-size:46px; color: color-mix(in srgb, var(--a) 40%, #ccc); }
        .mk-foot-space { height:56px; }
    </style>
@endsection

@section('content')
    @php
        $mkAr = app()->getLocale() === 'ar';
        // Cities now arrive as auto-geocoded names, so key the list by the name itself.
        $cityNames = $cities->pluck('name');
        $catIcons = [
            'restaurant' => 'fa-utensils', 'food' => 'fa-utensils', 'cafe' => 'fa-mug-saucer',
            'grocery' => 'fa-basket-shopping', 'pharmacy' => 'fa-prescription-bottle-medical',
            'electronic' => 'fa-laptop', 'fashion' => 'fa-shirt', 'cloth' => 'fa-shirt',
            'beauty' => 'fa-spa', 'salon' => 'fa-scissors', 'flower' => 'fa-seedling',
            'gift' => 'fa-gift', 'book' => 'fa-book', 'clinic' => 'fa-stethoscope', 'gym' => 'fa-dumbbell',
            'hotel' => 'fa-bell-concierge', 'bakery' => 'fa-bread-slice', 'dairy' => 'fa-cheese', 'fruit' => 'fa-apple-whole',
        ];
        $mkIcon = function ($name) use ($catIcons) {
            $n = strtolower((string) $name);
            foreach ($catIcons as $k => $ic) { if (str_contains($n, $k)) return $ic; }
            return 'fa-store';
        };
        $hasFilter = $q !== '' || $store !== '' || $filter !== '';
        $base = URL::to('marketplace');
        $heroTiles = $categories->take(5);
    @endphp

    <div class="mk">
        {{-- ===== HERO ===== --}}
        <section class="mk-hero">
            <div class="wrap mk-hero__in">
                <div>
                    <h1>{{ $mkAr ? 'كل ما تحتاجه،' : 'Everything You Need,' }}<br><span class="hl">{{ $mkAr ? 'في مكان واحد' : 'In One Place' }}</span></h1>
                    <p class="lead">{{ $mkAr ? 'اكتشف أفضل المتاجر والمطاعم والخدمات القريبة منك — اطلب أو احجز وأكمل عبر واتساب.' : 'Discover the best stores, restaurants and services near you — order or book and finish on WhatsApp.' }}</p>

                    <form method="get" action="{{ $base }}" id="mkForm">
                        <input type="hidden" name="filter" id="mkFilter" value="{{ $filter }}">
                        <input type="hidden" name="lat" id="mkLat" value="{{ request('lat') }}">
                        <input type="hidden" name="lng" id="mkLng" value="{{ request('lng') }}">
                        <div class="mk-hsearch">
                            <div class="mk-hfld">
                                <i class="fa-solid fa-magnifying-glass"></i>
                                <input type="text" name="q" value="{{ $q }}" placeholder="{{ $mkAr ? 'ابحث عن متجر أو خدمة...' : 'Search stores, services...' }}">
                            </div>
                            <div class="mk-hfld">
                                <i class="fa-solid fa-earth-americas"></i>
                                <select name="country" id="mkCountry" onchange="document.getElementById('mkForm').submit()">
                                    <option value="">{{ $mkAr ? 'كل الدول' : 'All Countries' }}</option>
                                    @foreach ($countries as $co)
                                        <option value="{{ $co }}" {{ $country === $co ? 'selected' : '' }}>{{ $co }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="mk-hbtn"><i class="fa-solid fa-magnifying-glass"></i> {{ $mkAr ? 'بحث' : 'Search' }}</button>
                        </div>
                    </form>

                    <div class="mk-trust">
                        <div><i class="fa-solid fa-store"></i> {{ $totalStores }}+ {{ $mkAr ? 'متجر' : 'stores' }}</div>
                        <div><i class="fa-brands fa-whatsapp"></i> {{ $mkAr ? 'طلب عبر واتساب' : 'Order on WhatsApp' }}</div>
                        <div><i class="fa-solid fa-bolt"></i> {{ $mkAr ? 'سريع وسهل' : 'Fast & easy' }}</div>
                    </div>
                </div>

                {{-- hero category tiles --}}
                <div class="mk-htiles">
                    @foreach ($heroTiles as $ca)
                        <a href="{{ $base }}?store={{ urlencode($ca->name) }}" class="mk-htile">
                            <span class="ic"><i class="fa-solid {{ $mkIcon($ca->name) }}"></i></span>
                            <div><b>{{ $ca->name }}</b><span>{{ $mkAr ? 'تصفّح المتاجر' : 'Browse stores' }}</span></div>
                        </a>
                    @endforeach
                    <a href="#mk-all" class="mk-htile">
                        <span class="ic"><i class="fa-solid fa-ellipsis"></i></span>
                        <div><b>{{ $mkAr ? 'المزيد' : 'More' }}</b><span>{{ $mkAr ? 'كل الفئات' : 'Explore all' }}</span></div>
                    </a>
                </div>
            </div>
        </section>

        {{-- ===== FEATURES STRIP ===== --}}
        <div class="wrap mk-feat">
            <div class="mk-feat__box">
                @php $feats = [
                    ['fa-shapes', $mkAr ? 'تشكيلة واسعة' : 'Wide Selection', $totalStores.'+ '.($mkAr?'متجر':'stores')],
                    ['fa-truck-fast', $mkAr ? 'توصيل سريع' : 'Fast Delivery', $mkAr ? '٣٠–٦٠ دقيقة' : '30–60 min'],
                    ['fa-lock', $mkAr ? 'دفع آمن' : 'Secure Payment', $mkAr ? '١٠٠٪ آمن' : '100% safe'],
                    ['fa-tags', $mkAr ? 'أفضل الأسعار' : 'Best Prices', $mkAr ? 'كل يوم' : 'Everyday'],
                    ['fa-headset', $mkAr ? 'دعم متواصل' : '24/7 Support', $mkAr ? 'نحن هنا لك' : "We're here for you"],
                ]; @endphp
                @foreach ($feats as $f)
                    <div class="mk-feat__item">
                        <span class="ic"><i class="fa-solid {{ $f[0] }}"></i></span>
                        <div><b>{{ $f[1] }}</b><span>{{ $f[2] }}</span></div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="wrap">
            {{-- ===== filter pills ===== --}}
            <div class="mk-filters">
                @php $pills = [
                    '' => [$mkAr ? 'الكل' : 'All Stores', 'fa-store'],
                    'near' => [$mkAr ? 'بالقرب مني' : 'Near Me', 'fa-location-crosshairs'],
                    'open' => [$mkAr ? 'مفتوح الآن' : 'Open Now', 'fa-clock'],
                    'top' => [$mkAr ? 'الأعلى تقييماً' : 'Top Rated', 'fa-star'],
                    'featured' => [$mkAr ? 'مميّزة' : 'Featured', 'fa-award'],
                    'new' => [$mkAr ? 'جديدة' : 'New Stores', 'fa-bolt'],
                ]; @endphp
                @foreach ($pills as $key => $p)
                    <button type="button" class="mk-pill {{ $filter === $key ? 'active' : '' }}" data-filter="{{ $key }}">
                        <i class="fa-solid {{ $p[1] }}"></i> {{ $p[0] }}
                    </button>
                @endforeach
            </div>

            {{-- ===== discovery sections (only when not filtering) ===== --}}
            @if (!$hasFilter)
                <div class="mk-offer">
                    <div>
                        <h3>{{ $mkAr ? 'عروض حصرية لك!' : 'Exclusive Offers Just for You!' }}</h3>
                        <p>{{ $mkAr ? 'استمتع بأفضل الخصومات من متاجرك المفضّلة.' : 'Enjoy amazing deals and discounts from your favourite stores.' }}</p>
                    </div>
                    <span class="code">WELCOME</span>
                </div>

                @if (count($featured) > 0)
                    <div class="mk-sec">
                        <div class="mk-sec__head"><h2><i class="fa-solid fa-award" style="color:var(--a)"></i> {{ $mkAr ? 'متاجر مميّزة' : 'Featured Stores' }}</h2></div>
                        <div class="mk-scroll">@foreach ($featured as $store)@include('landing.partials.store_card')@endforeach</div>
                    </div>
                @endif
                @if (count($popular) > 0)
                    <div class="mk-sec">
                        <div class="mk-sec__head"><h2><i class="fa-solid fa-fire" style="color:#f97316"></i> {{ $mkAr ? 'الأكثر رواجاً' : 'Popular Stores' }}</h2></div>
                        <div class="mk-scroll">@foreach ($popular as $store)@include('landing.partials.store_card')@endforeach</div>
                    </div>
                @endif
                @if (count($latest) > 0)
                    <div class="mk-sec">
                        <div class="mk-sec__head"><h2><i class="fa-solid fa-bolt" style="color:#3b82f6"></i> {{ $mkAr ? 'أحدث المتاجر' : 'Latest Stores' }}</h2></div>
                        <div class="mk-scroll">@foreach ($latest as $store)@include('landing.partials.store_card')@endforeach</div>
                    </div>
                @endif
            @endif

            {{-- ===== all stores grid ===== --}}
            <div class="mk-sec" id="mk-all">
                <div class="mk-sec__head">
                    <h2>{{ $hasFilter ? ($mkAr ? 'النتائج' : 'Results') : ($mkAr ? 'كل المتاجر' : 'All Stores') }}</h2>
                    <span class="cnt">{{ count($stores) }} {{ $mkAr ? 'متجر' : 'stores' }}</span>
                </div>
                @if (count($stores) > 0)
                    <div class="mk-grid">@foreach ($stores as $store)@include('landing.partials.store_card')@endforeach</div>
                @else
                    <div class="mk-empty">
                        <i class="fa-solid fa-store-slash"></i>
                        <h3 style="margin:14px 0 6px;">{{ $mkAr ? 'لا توجد متاجر مطابقة' : 'No stores match your search' }}</h3>
                        <p>{{ $mkAr ? 'جرّب تغيير الدولة أو الفئة أو الفلتر.' : 'Try changing the country, category or filter.' }}</p>
                    </div>
                @endif
            </div>
            <div class="mk-foot-space"></div>
        </div>
    </div>

    <script>
        (function () {
            var form = document.getElementById('mkForm');
            var filterInput = document.getElementById('mkFilter');
            document.querySelectorAll('.mk-pill').forEach(function (b) {
                b.addEventListener('click', function () {
                    var f = b.getAttribute('data-filter');
                    filterInput.value = f;
                    if (f === 'near' && navigator.geolocation) {
                        b.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> ...';
                        navigator.geolocation.getCurrentPosition(function (pos) {
                            document.getElementById('mkLat').value = pos.coords.latitude;
                            document.getElementById('mkLng').value = pos.coords.longitude;
                            form.submit();
                        }, function () { form.submit(); }, { timeout: 8000 });
                        return;
                    }
                    form.submit();
                });
            });
        })();
    </script>
@endsection
