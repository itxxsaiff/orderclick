{{-- Shared premium storefront engine. Driven by $cfg set in each template-N wrapper. --}}
@php
    $isAr = app()->getLocale() === 'ar';
    $cfg = array_merge([
        'bg' => '#fdf7ee', 'imgset' => 'food', 'variant' => 'food', 'popular' => true,
        'heroImg' => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=900&q=75',
        'h1a' => 'We serve the', 'h1hl' => $isAr ? 'أشهى الأطباق' : 'taste you love', 'emoji' => '😋',
        'lead' => $isAr ? 'طازج ويُحضّر عند الطلب — تصفّح واطلب في ثوانٍ عبر واتساب.' : 'Fresh and made to order — browse and order in seconds on WhatsApp.',
        'cta' => $isAr ? 'تصفّح' : 'Explore', 'addLabel' => $isAr ? 'أضف' : 'Add', 'itemsLabel' => $isAr ? 'صنف' : 'items',
        'popEyebrow' => $isAr ? 'الأكثر طلباً' : 'Popular picks', 'popTitle' => $isAr ? 'يحبها الجميع' : 'Loved by everyone',
        'menuEyebrow' => $isAr ? 'قائمتنا' : 'Our menu', 'menuTitle' => $isAr ? 'تصفّح الكل' : 'Browse everything', 'menuSub' => $isAr ? 'اختر ما يناسبك وأضفه إلى السلة.' : 'Pick what you like and add it to your cart.',
        'f1' => ['fa-bolt', $isAr ? 'توصيل سريع' : 'Fast delivery', $isAr ? '30–60 دقيقة' : '30–60 min'],
        'f2' => ['fa-lock', $isAr ? 'طلب آمن' : 'Secure order', $isAr ? 'دفع موثوق' : 'Trusted payments'],
        'f3' => ['fa-brands fa-whatsapp', $isAr ? 'عبر واتساب' : 'On WhatsApp', $isAr ? 'بضغطة واحدة' : 'One tap away'],
        'f4' => ['fa-medal', $isAr ? 'جودة ممتازة' : 'Great quality', $isAr ? 'طازج دائماً' : 'Always fresh'],
    ], $cfg ?? []);

    $app     = \App\Models\Settings::where('vendor_id', $storeinfo->id)->first();
    $rtName  = $app->website_title ?: $storeinfo->name;
    $rtBanner= $app->banner;
    $rtDesc  = $app->description;
    $rtPhone = $app->whatsapp_number ?: $app->contact;
    $rtWa    = preg_replace('/[^0-9]/', '', (string) $rtPhone);
    $heroImg = !empty($rtBanner) ? helper::image_path($rtBanner) : $cfg['heroImg'];

    $rtCats = [];
    foreach ($getcategory as $c) {
        $n = 0;
        foreach ($getitem as $it) { if ($it->cat_id == $c->id) { $n++; } }
        if ($n > 0) { $rtCats[] = $c; }
    }
    $popular = collect($getitem)->take(8);

    $rtImg = function ($item, $cat = '') use ($cfg) {
        $up = @$item['item_image']->image;
        return !empty($up) ? helper::image_path($up) : helper::food_image($item->item_name . ' ' . $cat, $item->id, $cfg['imgset']);
    };
    $rtStars = function ($avg) {
        $a = (int) round($avg > 0 ? $avg : 5);
        $out = '';
        for ($i = 1; $i <= 5; $i++) { $out .= '<i class="fa-solid fa-star' . ($i <= $a ? '' : ' off') . '"></i>'; }
        return $out;
    };
@endphp

@if (count($getcategory) > 0 && count($getitem) > 0)
    <style>
        .rt { --p: var(--bs-primary, #ef9a35); --ink:#2a2320; --muted:#8b8178; --cream:{{ $cfg['bg'] }}; --card:#fff; --line:#efe8db; --star:#f5a623; }
        .rt.retail { --line:#eceaf0; --ink:#241f2b; }
        .rt * { box-sizing:border-box; }
        .rt { background:var(--cream); font-family:'Outfit','Poppins',system-ui,sans-serif; color:var(--ink); overflow-x:hidden; }
        .rt .wrap { max-width:1200px; margin:0 auto; padding:0 22px; }
        .rt a { text-decoration:none; }
        .rt-btn { display:inline-flex; align-items:center; justify-content:center; gap:9px; height:52px; padding:0 26px; border-radius:40px; font-weight:700; font-size:15px; cursor:pointer; border:0; transition:.16s; }
        .rt-btn--p { background:var(--p); color:#fff; } .rt-btn--p:hover { filter:brightness(.94); transform:translateY(-2px); color:#fff; }
        .rt-btn--wa { background:#25d366; color:#fff; } .rt-btn--wa:hover { filter:brightness(.95); color:#fff; }

        .rt-hero__in { display:grid; grid-template-columns:1.05fr .95fr; gap:44px; align-items:center; min-height:600px; padding:84px 0 92px; }
        .rt-eyebrow { display:inline-flex; align-items:center; gap:8px; background:color-mix(in srgb, var(--p) 14%, #fff); color:var(--p); font-weight:700; font-size:12.5px; letter-spacing:.05em; text-transform:uppercase; padding:8px 15px; border-radius:30px; margin-bottom:20px; }
        .rt-hero h1 { font-size:clamp(34px,4.6vw,56px); line-height:1.06; font-weight:850; letter-spacing:-.02em; margin:0 0 16px; }
        .rt-hero h1 .hl { color:var(--p); }
        .rt-hero p.lead { font-size:16.5px; line-height:1.7; color:var(--muted); max-width:34em; margin:0 0 28px; }
        .rt-hero .cta { display:flex; flex-wrap:wrap; gap:13px; }
        .rt-hero .chips { display:flex; flex-wrap:wrap; gap:10px; margin-top:26px; }
        .rt-hero .chips a { display:inline-flex; align-items:center; gap:8px; background:#fff; border:1px solid var(--line); border-radius:30px; padding:8px 15px; font-size:13.5px; font-weight:600; color:var(--ink); }
        .rt-hero .chips a:hover { border-color:var(--p); color:var(--p); }
        .rt-hero__art { display:flex; align-items:center; justify-content:center; }
        .rt-hero__disc { width:min(430px,90%); aspect-ratio:1; border-radius:50%; overflow:hidden; border:14px solid #fff; box-shadow:0 50px 90px -50px rgba(80,60,20,.55); background:#eee; }
        .rt.retail .rt-hero__disc { border-radius:32px; aspect-ratio:4/5; width:min(400px,90%); }
        .rt-hero__disc img { width:100%; height:100%; object-fit:cover; }
        @media (max-width:900px){ .rt-hero__in { grid-template-columns:1fr; text-align:center; min-height:auto; padding:56px 0 40px; } .rt-hero .cta,.rt-hero .chips { justify-content:center; } .rt-hero p.lead { margin-inline:auto; } .rt-hero__art { display:none; } }

        .rt-feat { background:#fff; border-top:1px solid var(--line); border-bottom:1px solid var(--line); }
        .rt-feat__in { display:grid; grid-template-columns:repeat(4,1fr); gap:24px; padding:40px 0; }
        .rt-feat .f { display:flex; align-items:center; gap:13px; }
        .rt-feat .f i { width:46px; height:46px; border-radius:14px; background:color-mix(in srgb, var(--p) 12%, #fff); color:var(--p); display:flex; align-items:center; justify-content:center; font-size:18px; flex:none; }
        .rt-feat .f b { display:block; font-size:14.5px; } .rt-feat .f span { font-size:12.5px; color:var(--muted); }
        @media (max-width:820px){ .rt-feat__in { grid-template-columns:1fr 1fr; } }

        .rt-sec { padding:56px 0 20px; }
        .rt-head { text-align:center; max-width:600px; margin:0 auto 34px; }
        .rt-head .rt-eyebrow { margin-bottom:12px; }
        .rt-head h2 { font-size:clamp(26px,3.6vw,38px); font-weight:820; letter-spacing:-.02em; margin:0 0 8px; }
        .rt-head p { color:var(--muted); font-size:15.5px; margin:0; }

        .rt-scroll { display:grid; grid-auto-flow:column; grid-auto-columns:minmax(240px,262px); gap:22px; overflow-x:auto; padding:6px 2px 16px; scrollbar-width:none; }
        .rt-scroll::-webkit-scrollbar { display:none; }

        .rt-nav { position:sticky; top:0; z-index:30; background:color-mix(in srgb, var(--cream) 92%, transparent); backdrop-filter:blur(10px); border-bottom:1px solid var(--line); }
        .rt-nav__in { display:flex; gap:10px; overflow-x:auto; padding:15px 0; scrollbar-width:none; }
        .rt-nav__in::-webkit-scrollbar { display:none; }
        .rt-chip { flex:none; display:inline-flex; align-items:center; gap:8px; padding:10px 20px; border-radius:40px; border:1.5px solid var(--line); background:#fff; color:var(--ink); font-weight:650; font-size:14px; cursor:pointer; transition:.15s; white-space:nowrap; }
        .rt-chip:hover { border-color:var(--p); color:var(--p); }
        .rt-chip.active { background:var(--p); border-color:var(--p); color:#fff; }

        .rt-menu { padding:36px 0 64px; }
        .rt-cat { padding-top:30px; scroll-margin-top:84px; }
        .rt-cat__h { display:flex; align-items:baseline; gap:12px; margin:0 0 20px; }
        .rt-cat__h h2 { font-size:24px; font-weight:800; margin:0; }
        .rt-cat__h .n { color:var(--muted); font-size:14px; }
        .rt-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:22px; }
        .rt.dense .rt-grid { grid-template-columns:repeat(5,1fr); gap:16px; }
        @media (max-width:1080px){ .rt-grid { grid-template-columns:repeat(3,1fr);} .rt.dense .rt-grid { grid-template-columns:repeat(4,1fr);} }
        @media (max-width:820px){ .rt-grid { grid-template-columns:repeat(2,1fr);} .rt.dense .rt-grid { grid-template-columns:repeat(3,1fr);} }
        @media (max-width:520px){ .rt-grid,.rt.dense .rt-grid { grid-template-columns:1fr 1fr;} }

        .rt-dish { background:var(--card); border:1px solid var(--line); border-radius:24px; padding:16px 16px 18px; text-align:center; transition:transform .18s, box-shadow .18s; display:flex; flex-direction:column; }
        .rt.dense .rt-dish { border-radius:18px; padding:12px; }
        .rt-dish:hover { transform:translateY(-6px); box-shadow:0 34px 60px -38px rgba(80,60,20,.5); }
        .rt-dish__img { position:relative; aspect-ratio:1/1; border-radius:18px; overflow:hidden; background:#f4ede1; margin-bottom:14px; }
        .rt.retail .rt-dish__img { aspect-ratio:3/4; }
        .rt.dense .rt-dish__img { border-radius:12px; margin-bottom:10px; }
        .rt-dish__img img { width:100%; height:100%; object-fit:cover; transition:transform .4s; }
        .rt-dish:hover .rt-dish__img img { transform:scale(1.07); }
        .rt-dish__off { position:absolute; top:10px; left:10px; background:var(--p); color:#fff; font-size:11px; font-weight:700; padding:4px 9px; border-radius:8px; }
        .rt-dish h3 { font-size:17px; font-weight:750; margin:0 0 6px; }
        .rt.dense .rt-dish h3 { font-size:14.5px; }
        .rt-stars { color:var(--star); font-size:12.5px; letter-spacing:1px; margin-bottom:8px; }
        .rt-stars .off { color:#e6ddcd; }
        .rt-dish .desc { font-size:12.8px; color:var(--muted); line-height:1.5; margin:0 0 15px; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; min-height:38px; }
        .rt.dense .rt-dish .desc { display:none; }
        .rt-dish__foot { margin-top:auto; display:flex; align-items:center; justify-content:space-between; gap:10px; }
        .rt-price { font-weight:850; font-size:18px; } .rt.dense .rt-price { font-size:15.5px; }
        .rt-price del { font-weight:500; font-size:12.5px; color:var(--muted); margin-inline-start:5px; }
        .rt-add { display:inline-flex; align-items:center; gap:7px; height:40px; padding:0 16px; border-radius:30px; background:var(--p); color:#fff; font-weight:700; font-size:13px; border:0; cursor:pointer; transition:.15s; }
        .rt.dense .rt-add { height:36px; padding:0 12px; font-size:12px; }
        .rt-add:hover { filter:brightness(.92); }

        .rt-tst { background:#fff; border-top:1px solid var(--line); padding:56px 0; }
        .rt-tst__grid { display:grid; grid-template-columns:repeat(3,1fr); gap:22px; }
        @media (max-width:820px){ .rt-tst__grid { grid-template-columns:1fr; } }
        .rt-tcard { background:var(--cream); border:1px solid var(--line); border-radius:20px; padding:24px; }
        .rt-tcard .q { color:var(--muted); font-size:14.5px; line-height:1.7; margin:0 0 16px; }
        .rt-tcard .who { display:flex; align-items:center; gap:11px; }
        .rt-tcard .who img { width:44px; height:44px; border-radius:50%; object-fit:cover; }
        .rt-tcard .who b { font-size:14.5px; display:block; } .rt-tcard .who span { font-size:12.5px; color:var(--star); }

        .rt-sub { padding:20px 0 66px; }
        .rt-sub__box { background:linear-gradient(120deg, var(--p), color-mix(in srgb, var(--p) 55%, #000)); border-radius:26px; padding:44px 34px; color:#fff; text-align:center; }
        .rt-sub__box h3 { font-size:27px; font-weight:820; margin:0 0 8px; }
        .rt-sub__box p { margin:0 0 22px; opacity:.92; }
        .rt-sub__form { display:flex; gap:10px; max-width:470px; margin:0 auto; }
        .rt-sub__form input { flex:1; height:54px; border:0; border-radius:40px; padding:0 20px; font-size:15px; }
        .rt-sub__form button { height:54px; padding:0 26px; border:0; border-radius:40px; background:#2a2320; color:#fff; font-weight:700; cursor:pointer; }
        @media (max-width:520px){ .rt-sub__form { flex-direction:column; } }
    </style>

    <div class="rt {{ !empty($cfg['dense']) ? 'dense' : '' }} {{ $cfg['variant'] === 'retail' ? 'retail' : '' }}">
        {{-- HERO --}}
        <section class="rt-hero">
            <div class="wrap rt-hero__in">
                <div>
                    <h1>{{ $isAr ? '' : $cfg['h1a'] }} <span class="hl">{{ $cfg['h1hl'] }}</span> {{ $cfg['emoji'] }}</h1>
                    <p class="lead">{{ $rtDesc ?: $cfg['lead'] }}</p>
                    <div class="cta">
                        <a href="#rt-menu" class="rt-btn rt-btn--p"><i class="fa-solid {{ $cfg['variant'] === 'retail' ? 'fa-bag-shopping' : ($cfg['variant'] === 'grocery' ? 'fa-basket-shopping' : 'fa-utensils') }}"></i> {{ $cfg['cta'] }}</a>
                        @if ($rtWa)<a href="https://wa.me/{{ $rtWa }}" target="_blank" rel="noopener" class="rt-btn rt-btn--wa"><i class="fa-brands fa-whatsapp"></i> {{ $isAr ? 'اطلب عبر واتساب' : 'Order on WhatsApp' }}</a>@endif
                    </div>
                    <div class="chips">
                        @foreach (array_slice($rtCats, 0, 4) as $c)<a href="#rt-cat-{{ $c->id }}">{{ $c->name }}</a>@endforeach
                    </div>
                </div>
                <div class="rt-hero__art"><div class="rt-hero__disc"><img src="{{ $heroImg }}" alt="{{ $rtName }}"></div></div>
            </div>
        </section>

        {{-- FEATURES --}}
        <div class="rt-feat">
            <div class="wrap rt-feat__in">
                @foreach ([$cfg['f1'], $cfg['f2'], $cfg['f3'], $cfg['f4']] as $f)
                    <div class="f"><i class="{{ str_contains($f[0], 'fa-brands') ? $f[0] : 'fa-solid ' . $f[0] }}"></i><div><b>{{ $f[1] }}</b><span>{{ $f[2] }}</span></div></div>
                @endforeach
            </div>
        </div>

        {{-- POPULAR --}}
        @if (!empty($cfg['popular']) && count($popular) > 3)
            <section class="rt-sec">
                <div class="wrap">
                    <div class="rt-head"><span class="rt-eyebrow">{{ $cfg['popEyebrow'] }}</span><h2>{{ $cfg['popTitle'] }}</h2></div>
                    <div class="rt-scroll">
                        @foreach ($popular as $item)
                            @php $hasVar = isset($item['variation']) && $item['variation']->count() > 0; $price = $hasVar ? $item['variation'][0]->price : $item->item_price; $iname = str_replace("'", "\\'", $item->item_name); @endphp
                            <div class="rt-dish">
                                <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}" class="rt-dish__img"><img src="{{ $rtImg($item) }}" alt="{{ $item->item_name }}" loading="lazy"></a>
                                <h3>{{ \Illuminate\Support\Str::limit($item->item_name, 22) }}</h3>
                                <div class="rt-stars">{!! $rtStars(@$item->avg_ratting) !!}</div>
                                <div class="rt-dish__foot">
                                    <span class="rt-price">{{ helper::currency_formate($price, @$storeinfo->id) }}</span>
                                    <button type="button" class="rt-add" onclick="showitems('{{ $item->id }}','{{ $iname }}','{{ $item->item_price }}')"><span class="addcartbtn-{{ $item->id }}"><i class="fa-solid fa-plus"></i> {{ $cfg['addLabel'] }}</span><span class="load showload-{{ $item->id }}" style="display:none"></span></button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        {{-- STICKY NAV --}}
        <nav class="rt-nav">
            <div class="wrap rt-nav__in" id="rtNav">
                @foreach ($rtCats as $i => $c)<button type="button" class="rt-chip {{ $i == 0 ? 'active' : '' }}" data-target="rt-cat-{{ $c->id }}">{{ $c->name }}</button>@endforeach
            </div>
        </nav>

        {{-- MENU --}}
        <div class="rt-menu" id="rt-menu">
            <div class="wrap">
                <div class="rt-head" style="padding-top:36px;"><span class="rt-eyebrow">{{ $cfg['menuEyebrow'] }}</span><h2>{{ $cfg['menuTitle'] }}</h2><p>{{ $cfg['menuSub'] }}</p></div>
                @foreach ($rtCats as $c)
                    @php $catItems = collect($getitem)->where('cat_id', $c->id); @endphp
                    <section class="rt-cat" id="rt-cat-{{ $c->id }}">
                        <div class="rt-cat__h"><h2>{{ $c->name }}</h2><span class="n">{{ count($catItems) }} {{ $cfg['itemsLabel'] }}</span></div>
                        <div class="rt-grid">
                            @foreach ($catItems as $item)
                                @php
                                    $hasVar = isset($item['variation']) && $item['variation']->count() > 0;
                                    $price = $hasVar ? $item['variation'][0]->price : $item->item_price;
                                    $orig  = $item->item_original_price;
                                    $off   = ($orig && $orig > $price) ? round(100 - ($price * 100) / $orig) : 0;
                                    $iname = str_replace("'", "\\'", $item->item_name);
                                    $detail = URL::to($storeinfo->slug . '/details-' . $item->slug);
                                @endphp
                                <div class="rt-dish">
                                    <a href="{{ $detail }}" class="rt-dish__img">
                                        <img src="{{ $rtImg($item, $c->name) }}" alt="{{ $item->item_name }}" loading="lazy">
                                        @if ($off > 0)<span class="rt-dish__off">{{ $off }}% {{ trans('labels.off') }}</span>@endif
                                    </a>
                                    <h3><a href="{{ $detail }}" style="color:inherit">{{ \Illuminate\Support\Str::limit($item->item_name, 24) }}</a></h3>
                                    <div class="rt-stars">{!! $rtStars(@$item->avg_ratting) !!}</div>
                                    <p class="desc">{{ !empty($item->description) ? \Illuminate\Support\Str::limit(strip_tags($item->description), 66) : $cfg['menuSub'] }}</p>
                                    <div class="rt-dish__foot">
                                        <span class="rt-price">{{ $hasVar ? ($isAr ? 'من ' : 'From ') : '' }}{{ helper::currency_formate($price, @$storeinfo->id) }}@if ($off > 0)<del>{{ helper::currency_formate($orig, @$storeinfo->id) }}</del>@endif</span>
                                        <button type="button" class="rt-add" onclick="showitems('{{ $item->id }}','{{ $iname }}','{{ $item->item_price }}')"><span class="addcartbtn-{{ $item->id }}"><i class="fa-solid fa-cart-plus"></i> {{ $cfg['addLabel'] }}</span><span class="load showload-{{ $item->id }}" style="display:none"></span></button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endforeach
            </div>
        </div>

        {{-- TESTIMONIALS --}}
        @if (count($storereview) > 0)
            <div class="rt-tst">
                <div class="wrap">
                    <div class="rt-head"><span class="rt-eyebrow">{{ trans('labels.reviews_2') }}</span><h2>{{ trans('labels.what_our_customers_say') }}</h2></div>
                    <div class="rt-tst__grid">
                        @foreach (collect($storereview)->take(3) as $review)
                            <div class="rt-tcard">
                                <p class="q">“{{ \Illuminate\Support\Str::limit($review->description, 180) }}”</p>
                                <div class="who"><img src="{{ helper::image_path($review->image) }}" alt=""><div><b>{{ $review->name }}</b><span>{!! $rtStars($review->star) !!}</span></div></div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        {{-- SUBSCRIBE --}}
        <div class="rt-sub">
            <div class="wrap">
                <div class="rt-sub__box">
                    <h3>{{ trans('labels.subscribe_title') }}</h3>
                    <p>{{ trans('labels.subscribe_description') }}</p>
                    <form action="{{ URL::to($storeinfo->slug . '/subscribe') }}" method="post" class="rt-sub__form">
                        @csrf
                        <input type="hidden" value="{{ $storeinfo->id }}" name="id">
                        <input type="email" name="email" placeholder="{{ trans('labels.enter_email') }}" required>
                        <button type="submit">{{ trans('labels.subscribe') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function () {
            var chips = document.querySelectorAll('.rt-chip');
            var sections = Array.prototype.map.call(chips, function (c) { return document.getElementById(c.getAttribute('data-target')); });
            var nav = document.getElementById('rtNav');
            function centerChip(a) { if (!nav || !a) return; nav.scrollTo({ left: a.offsetLeft - (nav.clientWidth / 2) + (a.clientWidth / 2), behavior: 'smooth' }); }
            chips.forEach(function (chip) {
                chip.addEventListener('click', function () {
                    var el = document.getElementById(chip.getAttribute('data-target'));
                    if (el) window.scrollTo({ top: el.getBoundingClientRect().top + window.pageYOffset - 76, behavior: 'smooth' });
                });
            });
            if ('IntersectionObserver' in window) {
                var io = new IntersectionObserver(function (entries) {
                    entries.forEach(function (e) {
                        if (e.isIntersecting) {
                            var id = e.target.id;
                            chips.forEach(function (c) { c.classList.toggle('active', c.getAttribute('data-target') === id); });
                            centerChip(document.querySelector('.rt-chip.active'));
                        }
                    });
                }, { rootMargin: '-45% 0px -50% 0px' });
                sections.forEach(function (s) { if (s) io.observe(s); });
            }
        })();
    </script>
@else
    @include('front.nodata')
@endif
