@extends('front.theme.default')
@section('content')
    @php
        // Read THIS vendor's own settings directly. helper::appdata() matches on
        // host-without-port vs WEBSITE_HOST (which carries :8000 locally), so it can
        // silently return vendor 1's settings — that's why saif's banner/colors/title
        // weren't applying. Querying by vendor_id is host-independent and correct.
        $ct_app = \App\Models\Settings::where('vendor_id', $storeinfo->id)->first() ?: helper::appdata($storeinfo->id);
        $ct_accent = !empty($ct_app->primary_color) ? $ct_app->primary_color : '#b8860b';
        $ct_accent2 = !empty($ct_app->secondary_color) ? $ct_app->secondary_color : '#8a6d1e';

        // Drop missing translation keys ("labels.x") and shipped placeholder copy
        // ("lorem ipsum" / "dummy text") so nothing junky ever reaches the page.
        $ct_clean = function ($t) {
            $t = trim((string) $t);
            if ($t === '' || Str::startsWith($t, 'labels.')) {
                return null;
            }
            if (Str::contains(strtolower($t), ['lorem ipsum', 'dummy text'])) {
                return null;
            }
            return $t;
        };

        // Real store name lives in website_title (may carry a "| tagline" suffix).
        $ct_store_name = trim(Str::before(@$ct_app->website_title ?: @$storeinfo->name, '|'));
        $ct_welcome = $ct_clean(trans('labels.welcome')) ?: 'Welcome';
        $ct_tagline = $ct_clean(@$ct_app->whoweare_subtitle) ?: Str::limit((string) $ct_clean(@$ct_app->meta_description), 150);
        $ct_products_desc = $ct_clean(trans('labels.our_products_desc'));
        $ct_review_desc = $ct_clean(trans('labels.review_note'));
        // Hero image: the vendor's own Banner, else their Landing Page Cover Image,
        // else a bundled food photo — so whichever field the vendor uploads, it shows.
        $ct_hero_src = !empty($ct_app->banner)
            ? $ct_app->banner
            : (!empty($ct_app->cover_image) ? $ct_app->cover_image : null);
        $ct_has_banner = !empty($ct_hero_src);
        $ct_hero_bg = $ct_has_banner
            ? helper::image_path($ct_hero_src)
            : asset('storage/app/public/web-assets/iamges/jpg/theme-3-banner.jpg');

        // A brand-new / EMPTY store shows the demo showcase so it never looks blank.
        // As soon as the vendor adds even one real product of their own, the demo disappears.
        $ct_real_ready = count($getcategory) > 0 && count($getitem) > 0;
        $ct_show_demo = count($getitem) < 1;
    @endphp
    {{-- ================= Classic Theme (Template 2) styles ================= --}}
    <style>
        .ct-theme {
            --ct-accent: {{ $ct_accent }};
            --ct-accent-2: {{ $ct_accent2 }};
            --ct-bg: #faf7f1;
            --ct-surface: #ffffff;
            --ct-ink: #211d18;
            --ct-muted: #7b7267;
            --ct-line: #e7ded0;
            --ct-serif: 'Playfair Display', Georgia, 'Times New Roman', serif;
            background: var(--ct-bg);
            color: var(--ct-ink);
            font-family: 'Outfit', system-ui, sans-serif;
        }

        .ct-theme .ct-serif { font-family: var(--ct-serif); }

        .ct-theme .ct-section { padding: 64px 0; }
        .ct-theme .ct-section-sm { padding: 40px 0; }

        .ct-theme .ct-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            letter-spacing: .22em;
            text-transform: uppercase;
            color: var(--ct-accent);
            font-weight: 600;
            margin-bottom: 12px;
        }
        .ct-theme .ct-eyebrow::before,
        .ct-theme .ct-eyebrow::after {
            content: '';
            width: 26px;
            height: 1px;
            background: var(--ct-accent);
            opacity: .6;
        }
        .ct-theme .ct-heading {
            font-family: var(--ct-serif);
            font-weight: 700;
            font-size: clamp(26px, 4vw, 40px);
            line-height: 1.15;
            margin: 0 0 10px;
            color: var(--ct-ink);
        }
        .ct-theme .ct-subheading {
            color: var(--ct-muted);
            max-width: 620px;
            margin: 0 auto;
            font-size: 15.5px;
        }
        .ct-theme .ct-head-center { text-align: center; }

        /* ---------- Hero ---------- */
        .ct-hero {
            position: relative;
            min-height: clamp(400px, 56vh, 600px);
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: #fff;
            overflow: hidden;
            background: linear-gradient(135deg, #2a251f 0%, #14100c 58%, #201b15 100%);
        }
        /* Warm accent glow so a bannerless hero still feels designed, not empty. */
        .ct-hero--plain::before {
            content: '';
            position: absolute; inset: 0; z-index: 1;
            background:
                radial-gradient(circle at 22% 28%, rgba(244,228,189,.16), transparent 46%),
                radial-gradient(circle at 82% 78%, var(--ct-accent), transparent 62%);
            opacity: .5;
        }
        .ct-hero__bg {
            position: absolute; inset: 0;
            background-size: cover; background-position: center;
            transform: scale(1.03);
        }
        .ct-hero__overlay {
            position: absolute; inset: 0;
            background: linear-gradient(180deg, rgba(20,16,12,.55), rgba(20,16,12,.72));
        }
        .ct-hero__inner {
            position: relative;
            z-index: 2;
            padding: 48px 22px;
            max-width: 760px;
        }
        .ct-hero__frame {
            border: 1px solid rgba(255,255,255,.35);
            padding: 40px 34px;
        }
        .ct-hero__title {
            font-family: var(--ct-serif);
            font-weight: 700;
            font-size: clamp(30px, 6vw, 58px);
            line-height: 1.08;
            margin: 0 0 14px;
            text-shadow: 0 2px 18px rgba(0,0,0,.35);
        }
        .ct-hero__tag {
            font-size: 15px;
            letter-spacing: .04em;
            color: rgba(255,255,255,.9);
            margin-bottom: 26px;
        }
        .ct-hero__eyebrow {
            display: inline-flex; align-items: center; gap: 12px;
            text-transform: uppercase; letter-spacing: .3em;
            font-size: 12px; font-weight: 600;
            color: #f4e4bd; margin-bottom: 18px;
        }
        .ct-hero__eyebrow::before, .ct-hero__eyebrow::after {
            content: ''; width: 34px; height: 1px; background: #f4e4bd; opacity: .7;
        }

        /* ---------- Buttons ---------- */
        .ct-theme .ct-btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 8px;
            font-weight: 600; font-size: 14px; letter-spacing: .02em;
            padding: 12px 26px; border-radius: 2px; cursor: pointer;
            border: 1px solid var(--ct-accent);
            transition: all .25s ease; text-decoration: none;
        }
        .ct-theme .ct-btn--solid { background: var(--ct-accent); color: #fff; }
        .ct-theme .ct-btn--solid:hover { background: transparent; color: var(--ct-accent); }
        .ct-theme .ct-btn--ghost { background: transparent; color: #fff; border-color: rgba(255,255,255,.7); }
        .ct-theme .ct-btn--ghost:hover { background: #fff; color: var(--ct-ink); border-color: #fff; }
        .ct-theme .ct-btn--line { background: transparent; color: var(--ct-accent); }
        .ct-theme .ct-btn--line:hover { background: var(--ct-accent); color: #fff; }

        /* ---------- Features strip ---------- */
        .ct-features { background: #fff; border-top: 1px solid var(--ct-line); border-bottom: 1px solid var(--ct-line); }
        .ct-feature { display: flex; gap: 14px; align-items: center; padding: 22px 10px; }
        .ct-feature__icon {
            width: 46px; height: 46px; flex: 0 0 46px;
            display: flex; align-items: center; justify-content: center;
            color: var(--ct-accent); font-size: 22px;
        }
        .ct-feature__icon svg { width: 26px; height: 26px; }
        .ct-feature h3 { font-size: 15px; font-weight: 600; margin: 0 0 3px; color: var(--ct-ink); }
        .ct-feature p { font-size: 13px; color: var(--ct-muted); margin: 0; }

        /* ---------- Category tabs ---------- */
        .ct-cats {
            display: flex; flex-wrap: nowrap; gap: 6px; overflow-x: auto;
            justify-content: flex-start; padding: 6px 0 14px; margin: 26px 0 34px;
            border-bottom: 1px solid var(--ct-line);
            scrollbar-width: thin;
        }
        .ct-cats::-webkit-scrollbar { height: 4px; }
        .ct-cats::-webkit-scrollbar-thumb { background: var(--ct-line); border-radius: 4px; }
        .ct-cats li { list-style: none; }
        .ct-cat {
            display: flex; flex-direction: column; align-items: center; gap: 8px;
            padding: 10px 20px; cursor: pointer; white-space: nowrap;
            border-bottom: 2px solid transparent; margin-bottom: -1px;
            transition: all .2s ease;
        }
        .ct-cat img { width: 46px; height: 46px; border-radius: 50%; object-fit: cover; border: 1px solid var(--ct-line); }
        .ct-cat p { margin: 0; font-size: 14px; font-weight: 600; color: var(--ct-muted); }
        .ct-cats li.active1 .ct-cat { border-bottom-color: var(--ct-accent); }
        .ct-cats li.active1 .ct-cat p { color: var(--ct-accent); }
        .ct-cats li.active1 .ct-cat img { border-color: var(--ct-accent); }

        /* ---------- Product card ---------- */
        .ct-card {
            background: var(--ct-surface);
            border: 1px solid var(--ct-line);
            border-radius: 3px;
            overflow: hidden;
            height: 100%;
            display: flex; flex-direction: column;
            position: relative;
            transition: box-shadow .28s ease, transform .28s ease, border-color .28s ease;
        }
        .ct-card:hover {
            box-shadow: 0 16px 40px -22px rgba(33,29,24,.5);
            transform: translateY(-4px);
            border-color: var(--ct-accent);
        }
        .ct-card__media { position: relative; overflow: hidden; aspect-ratio: 1/1; background: #f3ede2; }
        .ct-card__media img { width: 100%; height: 100%; object-fit: cover; transition: transform .5s ease; }
        .ct-card:hover .ct-card__media img { transform: scale(1.06); }
        .ct-badge {
            position: absolute; top: 12px; left: 12px; z-index: 2;
            background: var(--ct-accent); color: #fff;
            font-size: 11px; font-weight: 700; letter-spacing: .04em;
            padding: 4px 9px; border-radius: 2px;
        }
        .ct-fav {
            position: absolute; top: 10px; right: 10px; z-index: 2;
            width: 34px; height: 34px; border-radius: 50%;
            background: rgba(255,255,255,.92); display: flex; align-items: center; justify-content: center;
            box-shadow: 0 2px 10px rgba(0,0,0,.12);
        }
        .ct-fav a { color: var(--ct-accent); font-size: 15px; }
        .ct-card__body { padding: 16px 16px 18px; display: flex; flex-direction: column; flex: 1; }
        .ct-card__rating { font-size: 12.5px; color: var(--ct-muted); display: inline-flex; align-items: center; gap: 4px; margin-bottom: 6px; }
        .ct-card__rating i { color: #e0a900; }
        .ct-card__title {
            font-family: var(--ct-serif); font-weight: 600; font-size: 18px; line-height: 1.3;
            color: var(--ct-ink); text-decoration: none; display: block; margin-bottom: 8px;
            display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
        }
        .ct-card__title:hover { color: var(--ct-accent); }
        .ct-card__price { display: flex; align-items: baseline; gap: 8px; margin-top: auto; padding-top: 6px; }
        .ct-card__price .now { font-weight: 700; font-size: 18px; color: var(--ct-accent); }
        .ct-card__price del { color: var(--ct-muted); font-size: 14px; }
        .ct-card__foot { display: flex; align-items: center; justify-content: space-between; gap: 10px; margin-top: 14px; }
        .ct-add {
            flex: 1;
            display: inline-flex; align-items: center; justify-content: center; gap: 8px;
            border: 1px solid var(--ct-ink); background: transparent; color: var(--ct-ink);
            font-weight: 600; font-size: 13px; letter-spacing: .03em;
            padding: 10px 12px; border-radius: 2px; cursor: pointer;
            transition: all .22s ease;
        }
        .ct-add:hover { background: var(--ct-ink); color: #fff; }
        .ct-add .load { width: 16px; height: 16px; border: 2px solid currentColor; border-right-color: transparent; border-radius: 50%; animation: ct-spin .7s linear infinite; }
        @keyframes ct-spin { to { transform: rotate(360deg); } }

        /* ---------- List layout ---------- */
        .ct-list-card { flex-direction: row; }
        .ct-list-card .ct-card__media { width: 190px; flex: 0 0 190px; aspect-ratio: auto; }
        @media (max-width: 575px) {
            .ct-list-card { flex-direction: column; }
            .ct-list-card .ct-card__media { width: 100%; flex: none; aspect-ratio: 16/10; }
        }

        /* ---------- Who we are ---------- */
        .ct-about-img { border-radius: 3px; overflow: hidden; border: 1px solid var(--ct-line); }
        .ct-about-img img { width: 100%; height: 100%; object-fit: cover; }
        .ct-about-item { display: flex; gap: 14px; }
        .ct-about-item .ico { width: 48px; height: 48px; flex: 0 0 48px; border-radius: 50%; overflow: hidden; border: 1px solid var(--ct-line); }
        .ct-about-item .ico img { width: 100%; height: 100%; object-fit: cover; }
        .ct-about-item h6 { font-family: var(--ct-serif); font-size: 16px; margin: 0 0 4px; }
        .ct-about-item p { font-size: 13.5px; color: var(--ct-muted); margin: 0; }

        /* ---------- Blog ---------- */
        .ct-blog-card { background: #fff; border: 1px solid var(--ct-line); border-radius: 3px; overflow: hidden; height: 100%; transition: box-shadow .25s ease; text-decoration: none; display: block; }
        .ct-blog-card:hover { box-shadow: 0 16px 40px -24px rgba(33,29,24,.5); }
        .ct-blog-card img { width: 100%; aspect-ratio: 16/10; object-fit: cover; }
        .ct-blog-card__body { padding: 18px; }
        .ct-blog-card__body h4 { font-family: var(--ct-serif); font-size: 18px; color: var(--ct-ink); margin: 0 0 8px; }
        .ct-blog-card__body .desc { font-size: 13.5px; color: var(--ct-muted); display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }

        /* ---------- Review ---------- */
        .ct-review { background: #fff; border: 1px solid var(--ct-line); border-radius: 3px; padding: 26px; height: 100%; }
        .ct-review__stars { color: #e0a900; margin-bottom: 12px; }
        .ct-review__text { font-size: 15px; font-style: italic; color: #4a443c; font-family: var(--ct-serif); line-height: 1.55; margin-bottom: 18px; }
        .ct-review__person { display: flex; gap: 12px; align-items: center; }
        .ct-review__person img { width: 46px; height: 46px; border-radius: 50%; object-fit: cover; }
        .ct-review__person b { display: block; font-size: 15px; }
        .ct-review__person span { font-size: 12.5px; color: var(--ct-muted); }

        /* ---------- Newsletter ---------- */
        .ct-news { background: linear-gradient(135deg, var(--ct-ink), #35302a); color: #fff; border-radius: 4px; padding: 54px 22px; text-align: center; }
        .ct-news h3 { font-family: var(--ct-serif); font-size: clamp(24px, 4vw, 34px); margin: 0 0 10px; }
        .ct-news p { color: rgba(255,255,255,.78); max-width: 520px; margin: 0 auto 24px; }
        .ct-news form { display: flex; gap: 10px; max-width: 480px; margin: 0 auto; flex-wrap: wrap; justify-content: center; }
        .ct-news input { flex: 1; min-width: 220px; border: 1px solid rgba(255,255,255,.3); background: rgba(255,255,255,.06); color: #fff; padding: 13px 16px; border-radius: 2px; }
        .ct-news input::placeholder { color: rgba(255,255,255,.6); }

        .ct-theme del { text-decoration: line-through; }
    </style>

    <div class="ct-theme">

            {{-- ===================== HERO ===================== --}}
            <section class="ct-hero">
                <div class="ct-hero__bg" style="background-image:url('{{ $ct_hero_bg }}');"></div>
                <div class="ct-hero__overlay"></div>
                <div class="ct-hero__inner">
                    <div class="ct-hero__frame">
                        <span class="ct-hero__eyebrow">{{ $ct_welcome }}</span>
                        <h1 class="ct-hero__title">{{ $ct_store_name }}</h1>
                        @if ($ct_tagline)
                            <p class="ct-hero__tag">{{ $ct_tagline }}</p>
                        @endif
                        @if ($ct_real_ready)
                            <a href="#ct-products" class="ct-btn ct-btn--ghost">
                                {{ trans('labels.our_products') }} <i class="fa-solid fa-arrow-down-long"></i>
                            </a>
                        @elseif ($ct_show_demo)
                            <a href="#ct-demo" class="ct-btn ct-btn--ghost">
                                {{ trans('labels.our_products') }} <i class="fa-solid fa-arrow-down-long"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </section>

            {{-- ===================== FEATURES ===================== --}}
            @if (count(helper::footer_features(@$vdata)) > 0)
                <section class="ct-features">
                    <div class="container">
                        <div class="row justify-content-center">
                            @foreach (helper::footer_features(@$vdata) as $feature)
                                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12">
                                    <div class="ct-feature">
                                        <div class="ct-feature__icon">{!! $feature->icon !!}</div>
                                        <div>
                                            <h3>{{ $feature->title }}</h3>
                                            <p>{{ $feature->description }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </section>
            @endif

            {{-- ===================== PRODUCTS ===================== --}}
            @if (count($getcategory) > 0 && count($getitem) > 0)
                <section class="ct-section" id="ct-products">
                    <div class="container">
                        <div class="ct-head-center">
                            <span class="ct-eyebrow">{{ trans('labels.menu') ?? 'Our Menu' }}</span>
                            <h2 class="ct-heading ct-serif">{{ trans('labels.our_products') }}</h2>
                            @if ($ct_products_desc)
                                <p class="ct-subheading">{{ $ct_products_desc }}</p>
                            @endif
                        </div>

                        {{-- Category tabs (index-based filter — must match .specs order/count) --}}
                        <ul class="ct-cats navgation_lower">
                            @foreach ($getcategory as $key => $category)
                                @php $check_cat_count = 0; @endphp
                                @foreach ($getitem as $item)
                                    @if ($category->id == $item->cat_id) @php $check_cat_count++; @endphp @endif
                                @endforeach
                                @if ($check_cat_count > 0)
                                    <li class="{{ $key == 0 ? 'active1' : '' }}" id="specs-{{ $category->id }}">
                                        <div class="ct-cat">
                                            <img src="{{ helper::image_path($category->image) }}" alt="{{ $category->name }}">
                                            <p>{{ $category->name }}</p>
                                        </div>
                                    </li>
                                @endif
                            @endforeach
                        </ul>

                        @if (@$ct_app->template_type == 2)
                            @include('front.template-2.theme-list')
                        @else
                            @include('front.template-2.theme-grid')
                        @endif
                    </div>
                </section>
            @endif

            {{-- ===================== DEMO SHOWCASE (sparse / new store) ===================== --}}
            @if ($ct_show_demo)
                @include('front.template-2.demo-showcase')
            @endif

            {{-- ===================== WHO WE ARE ===================== --}}
            @if (count($whowearedata) > 0)
                <section class="ct-section-sm">
                    <div class="container">
                        <div class="row g-4 align-items-center">
                            <div class="col-lg-6">
                                <span class="ct-eyebrow">{{ trans('labels.about_us') ?? 'About Us' }}</span>
                                <h2 class="ct-heading ct-serif">{{ @$ct_app->whoweare_title }}</h2>
                                <p class="ct-subheading" style="margin-left:0;margin-bottom:22px;">{{ @$ct_app->whoweare_subtitle }}</p>
                                <div class="row g-3">
                                    @foreach ($whowearedata as $whoweare)
                                        <div class="col-md-6">
                                            <div class="ct-about-item">
                                                <div class="ico"><img src="{{ helper::image_path($whoweare->image) }}" alt=""></div>
                                                <div>
                                                    <h6>{{ $whoweare->title }}</h6>
                                                    <p>{{ $whoweare->sub_title }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="ct-about-img">
                                    <img src="{{ helper::image_path(@$ct_app->whoweare_image) }}" alt="">
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            @endif

            {{-- ===================== BLOGS ===================== --}}
            @if (App\Models\SystemAddons::where('unique_identifier', 'blog')->first() != null &&
                    App\Models\SystemAddons::where('unique_identifier', 'blog')->first()->activated == 1)
                @php
                    $ct_blog = helper::vendordata(@$vdata)->allow_without_subscription == 1 ? 1 : @helper::get_plan($storeinfo->id)->blogs;
                @endphp
                @if ($ct_blog == 1 && count($blogs) > 0)
                    <section class="ct-section" style="background:#fff;border-top:1px solid var(--ct-line);">
                        <div class="container">
                            <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
                                <div>
                                    <span class="ct-eyebrow">{{ trans('labels.blogs') }}</span>
                                    <h2 class="ct-heading ct-serif mb-0">{{ trans('labels.blogs') }}</h2>
                                </div>
                                <a href="{{ URL::to(@$storeinfo->slug . '/blog-list') }}" class="ct-btn ct-btn--line">{{ trans('labels.view_all') }}</a>
                            </div>
                            <div class="row g-4">
                                @foreach ($blogs->take(3) as $blog)
                                    <div class="col-md-4">
                                        <a href="{{ URL::to(@$storeinfo->slug . '/blog-details-' . $blog->slug) }}" class="ct-blog-card">
                                            <img src="{{ helper::image_path($blog->image) }}" alt="">
                                            <div class="ct-blog-card__body">
                                                <h4>{{ $blog->title }}</h4>
                                                <div class="desc">{!! $blog->description !!}</div>
                                            </div>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </section>
                @endif
            @endif

            {{-- ===================== REVIEWS ===================== --}}
            @if (App\Models\SystemAddons::where('unique_identifier', 'store_reviews')->first() != null &&
                    App\Models\SystemAddons::where('unique_identifier', 'store_reviews')->first()->activated == 1)
                @if (count($storereview) > 0)
                    <section class="ct-section">
                        <div class="container">
                            <div class="ct-head-center mb-4">
                                <span class="ct-eyebrow">{{ trans('labels.review_tital') }}</span>
                                <h2 class="ct-heading ct-serif">{{ trans('labels.review_tital') }}</h2>
                                @if ($ct_review_desc)
                                    <p class="ct-subheading">{{ $ct_review_desc }}</p>
                                @endif
                            </div>
                            <div class="row g-4">
                                @foreach ($storereview->take(3) as $review)
                                    <div class="col-md-4">
                                        <div class="ct-review">
                                            <div class="ct-review__stars">
                                                @for ($s = 1; $s <= 5; $s++)
                                                    <i class="fa-{{ $s <= $review->star ? 'solid' : 'regular' }} fa-star"></i>
                                                @endfor
                                            </div>
                                            <p class="ct-review__text">“{{ Str::limit($review->description, 160) }}”</p>
                                            <div class="ct-review__person">
                                                <img src="{{ helper::image_path($review->image) }}" alt="">
                                                <div>
                                                    <b>{{ $review->name }}</b>
                                                    <span>{{ $review->position }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </section>
                @endif
            @endif

            {{-- ===================== NEWSLETTER ===================== --}}
            <section class="ct-section-sm">
                <div class="container">
                    <div class="ct-news">
                        <h3 class="ct-serif">{{ trans('labels.subscribe_title') }}</h3>
                        <p>{{ trans('labels.subscribe_description') }}</p>
                        <form action="{{ URL::to($storeinfo->slug . '/subscribe') }}" method="post">
                            @csrf
                            <input type="hidden" value="{{ $storeinfo->id }}" name="id">
                            <input type="email" name="email" placeholder="{{ trans('labels.enter_email') }}" required>
                            <button type="submit" class="ct-btn ct-btn--solid">{{ trans('labels.subscribe') }}</button>
                        </form>
                    </div>
                </div>
            </section>
    </div>
@endsection
@section('script')
@endsection
