<!DOCTYPE html>
<html lang="{{ session()->get('locale', app()->getLocale()) }}" dir="{{ session()->get('direction') == 2 ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    @include('partials.google_tag')
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        // Always use THIS store's own settings (appdata() is host/port-sensitive and can return the wrong vendor).
        $store = $storeSettings ?? \App\Models\Settings::where('vendor_id', $vdata)->first();
        $bkAr     = app()->getLocale() === 'ar';
        $bkTitle  = $store->website_title ?: 'Our Store';
        $bkDesc   = $store->description;
        $bkLogo   = $store->logo;
        $bkFav    = $store->favicon;
        $bkPrim   = $store->primary_color ?: '#1f9d55';
        $bkAddr   = $store->address;
        $bkPhone  = $store->whatsapp_number ?: $store->contact;
        $bkWa     = preg_replace('/[^0-9]/', '', (string) $bkPhone);
        $bkEmail  = $store->email;
        $bkMap    = $store->map_link;
        $bkSlug   = $storeinfo->slug ?? '';
        $bkBook   = URL::to($bkSlug . '/booking');
        // Only surface hours if the merchant actually set at least one open day.
        $bkHasHours = isset($timings) && collect($timings)->filter(fn($t) => !$t->is_always_close)->count() > 0;
        $today = strtolower(date('l'));
    @endphp
    <title>{{ $bkTitle }}</title>
    <meta name="description" content="{{ $bkDesc }}">
    <link rel="icon" href="{{ helper::image_path($bkFav) }}" type="image" sizes="16x16">
    <link rel="stylesheet" href="{{ url(env('ASSETSPATHURL') . 'web-assets/font-awesome/css/all.min.css') }}">
    <style>
        :root { --p: {{ $bkPrim }}; --ink: #16211b; --muted: #6a756c; --line: #e9eee7; --bg: #fbfcfb; }
        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body { margin: 0; background: var(--bg); color: var(--ink); font-family: 'Outfit', system-ui, -apple-system, 'Segoe UI', sans-serif; -webkit-font-smoothing: antialiased; }
        a { text-decoration: none; }
        .wrap { max-width: 1160px; margin: 0 auto; padding: 0 22px; }
        .btn { display: inline-flex; align-items: center; justify-content: center; gap: 9px; height: 48px; padding: 0 22px; border-radius: 12px; font-weight: 650; font-size: 15px; border: 0; cursor: pointer; transition: transform .15s, filter .15s, background .15s; white-space: nowrap; }
        .btn-p { background: var(--p); color: #fff; }
        .btn-p:hover { filter: brightness(.92); transform: translateY(-1px); color: #fff; }
        .btn-o { background: #fff; color: var(--ink); border: 1.5px solid var(--line); }
        .btn-o:hover { border-color: var(--p); color: var(--p); }
        .btn-wa { background: #25d366; color: #fff; }
        .btn-wa:hover { filter: brightness(.94); color: #fff; }

        /* ---------- Header ---------- */
        header.nav { position: sticky; top: 0; z-index: 50; background: rgba(255,255,255,.86); backdrop-filter: blur(12px); border-bottom: 1px solid var(--line); }
        .nav-in { display: flex; align-items: center; justify-content: space-between; height: 72px; }
        .brand { display: flex; align-items: center; gap: 12px; }
        .brand img { height: 40px; width: auto; border-radius: 9px; }
        .brand .bname { font-weight: 800; font-size: 18px; letter-spacing: -.01em; color: var(--ink); }
        .nav-links { display: flex; align-items: center; gap: 26px; }
        .nav-links a.link { color: var(--ink); font-weight: 550; font-size: 15px; opacity: .8; }
        .nav-links a.link:hover { opacity: 1; color: var(--p); }
        @media (max-width: 720px) { .nav-links a.link { display: none; } .brand .bname { font-size: 16px; } }

        /* ---------- Hero ---------- */
        .hero { position: relative; overflow: hidden; background:
            radial-gradient(60% 90% at 82% -10%, color-mix(in srgb, var(--p) 18%, transparent), transparent 60%),
            radial-gradient(50% 80% at 6% 110%, color-mix(in srgb, var(--p) 12%, transparent), transparent 55%),
            linear-gradient(180deg, color-mix(in srgb, var(--p) 5%, #fff), #fff); }
        .hero-in { padding: 74px 0 66px; max-width: 760px; }
        .eyebrow { display: inline-flex; align-items: center; gap: 8px; background: color-mix(in srgb, var(--p) 12%, #fff); color: var(--p); font-weight: 700; font-size: 12.5px; letter-spacing: .05em; text-transform: uppercase; padding: 7px 13px; border-radius: 30px; margin-bottom: 20px; }
        .hero h1 { font-size: clamp(32px, 5vw, 52px); line-height: 1.05; font-weight: 830; letter-spacing: -.02em; margin: 0 0 16px; }
        .hero p.lead { font-size: 18px; line-height: 1.6; color: var(--muted); margin: 0 0 28px; max-width: 620px; }
        .hero-cta { display: flex; flex-wrap: wrap; gap: 12px; margin-bottom: 26px; }
        .hero-chips { display: flex; flex-wrap: wrap; gap: 10px; }
        .chip { display: inline-flex; align-items: center; gap: 8px; background: #fff; border: 1px solid var(--line); border-radius: 30px; padding: 8px 15px; font-size: 13.5px; color: var(--ink); font-weight: 550; }
        .chip i { color: var(--p); }

        /* ---------- Sections ---------- */
        section { padding: 64px 0; }
        .sec-head { text-align: center; max-width: 620px; margin: 0 auto 40px; }
        .sec-head .eyebrow { margin-bottom: 14px; }
        .sec-head h2 { font-size: clamp(26px, 3.6vw, 36px); font-weight: 800; letter-spacing: -.02em; margin: 0 0 10px; }
        .sec-head p { color: var(--muted); font-size: 16px; margin: 0; }

        /* category filter */
        .filters { display: flex; flex-wrap: wrap; gap: 10px; justify-content: center; margin-bottom: 34px; }
        .filters button { background: #fff; border: 1.5px solid var(--line); color: var(--ink); border-radius: 30px; padding: 8px 17px; font-weight: 600; font-size: 14px; cursor: pointer; transition: .15s; }
        .filters button.active, .filters button:hover { background: var(--p); border-color: var(--p); color: #fff; }

        /* services grid */
        .grid { display: flex; flex-wrap: wrap; gap: 24px; justify-content: center; }
        .card { flex: 1 1 300px; max-width: 350px; background: #fff; border: 1px solid var(--line); border-radius: 20px; overflow: hidden; display: flex; flex-direction: column; box-shadow: 0 18px 44px -34px rgba(20,40,28,.5); transition: transform .18s, box-shadow .18s; }
        .card:hover { transform: translateY(-5px); box-shadow: 0 30px 60px -34px rgba(20,40,28,.45); }
        .card .ph { aspect-ratio: 16/11; overflow: hidden; position: relative; }
        .card .ph img { width: 100%; height: 100%; object-fit: cover; }
        .card .ph.noimg { display: flex; align-items: center; justify-content: center;
            background: linear-gradient(135deg, color-mix(in srgb, var(--p) 16%, #fff), color-mix(in srgb, var(--p) 6%, #fff)); }
        .card .ph.noimg span { width: 76px; height: 76px; border-radius: 20px; background: #fff; display: flex; align-items: center; justify-content: center; font-size: 30px; font-weight: 800; color: var(--p); box-shadow: 0 10px 24px -12px rgba(0,0,0,.25); }
        .card .body { padding: 18px 18px 20px; display: flex; flex-direction: column; flex: 1; }
        .cat { align-self: flex-start; font-size: 11px; font-weight: 750; letter-spacing: .05em; text-transform: uppercase; color: var(--p); background: color-mix(in srgb, var(--p) 11%, #fff); padding: 4px 10px; border-radius: 20px; margin-bottom: 11px; }
        .card h3 { font-size: 18px; font-weight: 750; margin: 0 0 6px; letter-spacing: -.01em; }
        .card .desc { font-size: 14px; color: var(--muted); line-height: 1.55; margin: 0 0 16px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .card .meta { display: flex; align-items: baseline; justify-content: space-between; margin: auto 0 14px; }
        .card .price { font-weight: 820; font-size: 20px; letter-spacing: -.01em; }
        .card .price small { font-weight: 500; font-size: 12.5px; color: var(--muted); }
        .card .dur { font-size: 13px; color: var(--muted); }
        .card .book { width: 100%; }

        .empty { text-align: center; padding: 60px 20px; color: var(--muted); }
        .empty i { font-size: 46px; color: color-mix(in srgb, var(--p) 40%, #ccc); }

        /* visit / contact */
        .visit { background: color-mix(in srgb, var(--p) 5%, #fff); border-top: 1px solid var(--line); border-bottom: 1px solid var(--line); }
        .visit-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 28px; }
        .vcol h4 { display: flex; align-items: center; gap: 10px; font-size: 16px; font-weight: 750; margin: 0 0 14px; }
        .vcol h4 i { width: 34px; height: 34px; border-radius: 10px; background: color-mix(in srgb, var(--p) 12%, #fff); color: var(--p); display: inline-flex; align-items: center; justify-content: center; font-size: 15px; }
        .vcol p, .vcol a.vlink { color: var(--muted); font-size: 14.5px; line-height: 1.6; margin: 0 0 10px; display: block; }
        .vcol a.vlink { color: var(--p); font-weight: 600; }
        .hours { list-style: none; padding: 0; margin: 0; }
        .hours li { display: flex; justify-content: space-between; gap: 14px; padding: 5px 0; font-size: 14px; color: var(--ink); border-bottom: 1px dashed var(--line); }
        .hours li.today { font-weight: 750; color: var(--p); }
        .hours li .cl { color: #c24; font-weight: 600; }

        /* footer */
        footer.ft { background: var(--ink); color: #cfd8d1; padding: 46px 0 30px; }
        .ft-top { display: flex; flex-wrap: wrap; gap: 24px; justify-content: space-between; align-items: flex-start; padding-bottom: 26px; border-bottom: 1px solid rgba(255,255,255,.1); }
        .ft-brand { max-width: 340px; }
        .ft-brand .fname { color: #fff; font-weight: 800; font-size: 19px; margin-bottom: 10px; }
        .ft-brand p { font-size: 14px; line-height: 1.6; margin: 0; color: #aab4ad; }
        .ft-links { display: flex; gap: 40px; flex-wrap: wrap; }
        .ft-links .col b { color: #fff; font-size: 13px; text-transform: uppercase; letter-spacing: .05em; display: block; margin-bottom: 12px; }
        .ft-links .col a { color: #b9c3bc; font-size: 14px; display: block; margin-bottom: 9px; }
        .ft-links .col a:hover { color: #fff; }
        .ft-bottom { padding-top: 22px; text-align: center; font-size: 13px; color: #8b968e; }
        .ft-bottom a { color: #cfd8d1; }

        @media (max-width: 560px) { .hero-in { padding: 54px 0 46px; } section { padding: 48px 0; } }
    </style>
</head>

<body>
    {{-- ===== Header ===== --}}
    <header class="nav">
        <div class="wrap nav-in">
            <a class="brand" href="#top">
                @if (!empty($bkLogo))<img src="{{ helper::image_path($bkLogo) }}" alt="{{ $bkTitle }}">@endif
                <span class="bname">{{ $bkTitle }}</span>
            </a>
            <nav class="nav-links">
                <a class="link" href="#services">{{ $bkAr ? 'الخدمات' : 'Services' }}</a>
                @if ($bkAddr || $bkHasHours)<a class="link" href="#visit">{{ $bkAr ? 'زورونا' : 'Visit' }}</a>@endif
                <a class="btn btn-p" href="{{ $bkBook }}"><i class="fa-solid fa-calendar-check"></i> {{ $bkAr ? 'احجز الآن' : 'Book Now' }}</a>
            </nav>
        </div>
    </header>

    {{-- ===== Hero ===== --}}
    <div id="top" class="hero">
        <div class="wrap hero-in">
            <span class="eyebrow"><i class="fa-solid fa-calendar-check"></i> {{ $bkAr ? 'الحجوزات مفتوحة' : 'Appointments open' }}</span>
            <h1>{{ $bkTitle }}</h1>
            <p class="lead">{{ $bkDesc ?: ($bkAr ? 'اختر خدمتك، احجز الوقت الذي يناسبك، وأكمل التأكيد عبر واتساب في ثوانٍ.' : 'Pick your service, choose a time that suits you, and confirm on WhatsApp in seconds.') }}</p>
            <div class="hero-cta">
                <a class="btn btn-p" href="#services"><i class="fa-solid fa-calendar-check"></i> {{ $bkAr ? 'احجز موعد' : 'Book an appointment' }}</a>
                @if ($bkWa)
                    <a class="btn btn-wa" href="https://wa.me/{{ $bkWa }}" target="_blank" rel="noopener"><i class="fa-brands fa-whatsapp"></i> {{ $bkAr ? 'تواصل معنا' : 'Chat with us' }}</a>
                @endif
            </div>
            <div class="hero-chips">
                @if ($bkAddr)<span class="chip"><i class="fa-solid fa-location-dot"></i> {{ \Illuminate\Support\Str::limit($bkAddr, 46) }}</span>@endif
                @if ($bkPhone)<span class="chip"><i class="fa-brands fa-whatsapp"></i> {{ $bkPhone }}</span>@endif
                <span class="chip"><i class="fa-solid fa-bolt"></i> {{ $bkAr ? 'تأكيد فوري عبر واتساب' : 'Instant WhatsApp confirmation' }}</span>
            </div>
        </div>
    </div>

    {{-- ===== Services ===== --}}
    <section id="services">
        <div class="wrap">
            <div class="sec-head">
                <span class="eyebrow">{{ $bkAr ? 'خدماتنا' : 'Our Services' }}</span>
                <h2>{{ $bkAr ? 'اختر خدمة واحجز' : 'Choose a service & book' }}</h2>
                <p>{{ $bkAr ? 'كل ما تحتاجه في مكان واحد — اختر ما يناسبك واحجز فوراً.' : 'Everything we offer in one place — pick what you need and book in seconds.' }}</p>
            </div>

            @if (count($services) > 0)
                @if (isset($categories) && count($categories) > 1)
                    <div class="filters">
                        <button class="active" data-cat="__all">{{ $bkAr ? 'الكل' : 'All' }}</button>
                        @foreach ($categories as $c)
                            <button data-cat="{{ \Illuminate\Support\Str::slug($c) }}">{{ $c }}</button>
                        @endforeach
                    </div>
                @endif

                <div class="grid" id="bkGrid">
                    @foreach ($services as $s)
                        <div class="card" data-cat="{{ $s->category ? \Illuminate\Support\Str::slug($s->category) : '' }}">
                            <div class="ph {{ empty($s->image) ? 'noimg' : '' }}">
                                @if (!empty($s->image))
                                    <img src="{{ helper::image_path($s->image) }}" alt="{{ $s->name }}">
                                @else
                                    <span>{{ strtoupper(mb_substr($s->name, 0, 1)) }}</span>
                                @endif
                            </div>
                            <div class="body">
                                @if ($s->category)<span class="cat">{{ $s->category }}</span>@endif
                                <h3>{{ $s->name }}</h3>
                                @if ($s->description)<p class="desc">{{ $s->description }}</p>@endif
                                <div class="meta">
                                    <div class="price">
                                        @if ($s->price > 0)
                                            {{ helper::currency_formate($s->price, $vdata) }}@if ($s->duration)<small> / {{ $s->duration }}</small>@endif
                                        @elseif ($s->duration)
                                            <span class="dur">{{ $s->duration }}</span>
                                        @else
                                            <span class="dur">{{ $bkAr ? 'حسب الطلب' : 'On request' }}</span>
                                        @endif
                                    </div>
                                </div>
                                <a class="btn btn-p book" href="{{ $bkBook }}?service={{ $s->id }}">
                                    <i class="fa-solid fa-calendar-check"></i> {{ $bkAr ? 'احجز الآن' : 'Book Now' }}
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty">
                    <i class="fa-solid fa-calendar-day"></i>
                    <h3 style="margin:14px 0 6px;">{{ $bkAr ? 'لا توجد خدمات متاحة بعد' : 'No services available yet' }}</h3>
                    <p>{{ $bkAr ? 'يرجى العودة قريباً.' : 'Please check back soon.' }}</p>
                </div>
            @endif
        </div>
    </section>

    {{-- ===== Visit / Contact ===== --}}
    @if ($bkAddr || $bkHasHours || $bkPhone || $bkEmail)
        <section id="visit" class="visit">
            <div class="wrap">
                <div class="sec-head">
                    <span class="eyebrow">{{ $bkAr ? 'زورونا' : 'Visit us' }}</span>
                    <h2>{{ $bkAr ? 'أين تجدنا' : 'Where to find us' }}</h2>
                </div>
                <div class="visit-grid">
                    @if ($bkAddr)
                        <div class="vcol">
                            <h4><i class="fa-solid fa-location-dot"></i> {{ $bkAr ? 'الموقع' : 'Location' }}</h4>
                            <p>{{ $bkAddr }}</p>
                            @if ($bkMap)<a class="vlink" href="{{ $bkMap }}" target="_blank" rel="noopener"><i class="fa-solid fa-diamond-turn-right"></i> {{ $bkAr ? 'الاتجاهات' : 'Get directions' }}</a>@endif
                        </div>
                    @endif
                    @if ($bkHasHours)
                        <div class="vcol">
                            <h4><i class="fa-solid fa-clock"></i> {{ $bkAr ? 'ساعات العمل' : 'Opening hours' }}</h4>
                            <ul class="hours">
                                @foreach ($timings as $t)
                                    <li class="{{ strtolower($t->day) === $today ? 'today' : '' }}">
                                        <span>{{ $t->day }}</span>
                                        @if ($t->is_always_close)
                                            <span class="cl">{{ $bkAr ? 'مغلق' : 'Closed' }}</span>
                                        @else
                                            <span>{{ $t->open_time }} – {{ $t->close_time }}</span>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @if ($bkPhone || $bkEmail)
                        <div class="vcol">
                            <h4><i class="fa-solid fa-headset"></i> {{ $bkAr ? 'تواصل معنا' : 'Get in touch' }}</h4>
                            @if ($bkWa)<a class="btn btn-wa" style="margin-bottom:12px;" href="https://wa.me/{{ $bkWa }}" target="_blank" rel="noopener"><i class="fa-brands fa-whatsapp"></i> {{ $bkPhone }}</a>@endif
                            @if ($bkEmail)<a class="vlink" href="mailto:{{ $bkEmail }}"><i class="fa-solid fa-envelope"></i> {{ $bkEmail }}</a>@endif
                        </div>
                    @endif
                </div>
            </div>
        </section>
    @endif

    {{-- ===== Footer ===== --}}
    <footer class="ft">
        <div class="wrap">
            <div class="ft-top">
                <div class="ft-brand">
                    <div class="fname">{{ $bkTitle }}</div>
                    <p>{{ $bkDesc ?: ($bkAr ? 'احجز خدماتك بسهولة وأكمل التأكيد عبر واتساب.' : 'Book our services easily and confirm on WhatsApp.') }}</p>
                </div>
                <div class="ft-links">
                    <div class="col">
                        <b>{{ $bkAr ? 'روابط' : 'Explore' }}</b>
                        <a href="#services">{{ $bkAr ? 'الخدمات' : 'Services' }}</a>
                        @if ($bkAddr || $bkHasHours)<a href="#visit">{{ $bkAr ? 'زورونا' : 'Visit us' }}</a>@endif
                        <a href="{{ $bkBook }}">{{ $bkAr ? 'احجز موعد' : 'Book now' }}</a>
                    </div>
                    @if ($bkPhone || $bkEmail || $bkAddr)
                        <div class="col">
                            <b>{{ $bkAr ? 'تواصل' : 'Contact' }}</b>
                            @if ($bkWa)<a href="https://wa.me/{{ $bkWa }}" target="_blank" rel="noopener">{{ $bkPhone }}</a>@endif
                            @if ($bkEmail)<a href="mailto:{{ $bkEmail }}">{{ $bkEmail }}</a>@endif
                            @if ($bkAddr)<a href="{{ $bkMap ?: '#visit' }}" @if($bkMap) target="_blank" @endif>{{ \Illuminate\Support\Str::limit($bkAddr, 40) }}</a>@endif
                        </div>
                    @endif
                </div>
            </div>
            <div class="ft-bottom">
                &copy; {{ date('Y') }} {{ $bkTitle }} · {{ $bkAr ? 'مدعوم بواسطة' : 'Powered by' }} <a href="{{ url('/') }}">Order Click</a>
            </div>
        </div>
    </footer>

    <script>
        // category filter
        (function () {
            var btns = document.querySelectorAll('.filters button');
            var cards = document.querySelectorAll('#bkGrid .card');
            btns.forEach(function (b) {
                b.addEventListener('click', function () {
                    btns.forEach(function (x) { x.classList.remove('active'); });
                    b.classList.add('active');
                    var cat = b.getAttribute('data-cat');
                    cards.forEach(function (c) {
                        c.style.display = (cat === '__all' || c.getAttribute('data-cat') === cat) ? '' : 'none';
                    });
                });
            });
        })();
    </script>
</body>

</html>
