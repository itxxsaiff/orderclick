@extends('landing.layout.default')

@php
    $landingCategories = \App\Models\StoreCategory::where('is_available', 1)
        ->where('is_deleted', 2)
        ->orderBy('reorder_id')
        ->get();

    $categoryIcons = [
        'restaurant' => 'fa-utensils', 'cafe' => 'fa-mug-hot', 'coffee' => 'fa-mug-hot',
        'grocery' => 'fa-basket-shopping', 'super' => 'fa-basket-shopping', 'market' => 'fa-basket-shopping',
        'pharmac' => 'fa-prescription-bottle-medical', 'medical' => 'fa-prescription-bottle-medical',
        'flower' => 'fa-seedling', 'cloth' => 'fa-shirt', 'fashion' => 'fa-shirt',
        'gift' => 'fa-gift', 'baker' => 'fa-bread-slice', 'sweet' => 'fa-cake-candles',
        'fruit' => 'fa-apple-whole', 'vegetable' => 'fa-carrot', 'dairy' => 'fa-cheese',
        'ice' => 'fa-ice-cream', 'book' => 'fa-book', 'juice' => 'fa-glass-water',
        'electronic' => 'fa-mobile-screen', 'perfume' => 'fa-spray-can-sparkles', 'toy' => 'fa-shapes',
    ];

    $registerUrl = env('Environment') == 'sendbox'
        ? URL::to('/admin')
        : (helper::appdata('')->vendor_register == 1 ? URL::to('/admin/register') : URL::to('/admin'));
    $loginUrl = URL::to('/admin');
    $ocLogo = helper::image_path(helper::appdata('')->logo);
@endphp

@section('styles')
    {{-- Hide the old dark chrome so only the new design shows --}}
    <style>

        :root { --ocl-accent: #1f9d55; --ocl-accent-ink: #137a40; }
        .ocl { --a: #1f9d55; --a-ink: #137a40; --a-soft: #e8f5ee; --ink: #17201a; --ink-2: #3c473f;
            --muted: #67736a; --line: #e4eae2; --bg: #f6faf7; --surface: #ffffff;
            font-family: 'Poppins', system-ui, -apple-system, sans-serif; color: var(--ink); }
        .ocl * { box-sizing: border-box; }
        .ocl a { text-decoration: none; }
        .ocl .wrap { max-width: 1160px; margin: 0 auto; padding: 0 22px; }
        .ocl section { position: relative; }
        .ocl h1, .ocl h2, .ocl h3 { color: var(--ink); margin: 0; line-height: 1.15; }
        .ocl p { margin: 0; color: var(--ink-2); }
        .ocl .eyebrow { display: inline-flex; align-items: center; gap: 12px; font-size: 12.5px; font-weight: 700;
            letter-spacing: .2em; text-transform: uppercase; color: var(--a-ink); }
        .ocl .center .eyebrow::before, .ocl .center .eyebrow::after { content: ''; width: 26px; height: 2px;
            background: currentColor; opacity: .45; border-radius: 2px; }
        .ocl .eyebrow--left::before { content: ''; width: 30px; height: 2px; background: var(--a); border-radius: 2px; }

        /* Scroll reveal */
        .ocl-reveal { opacity: 0; transform: translateY(26px); transition: opacity .6s ease, transform .7s cubic-bezier(.22,.61,.36,1); will-change: opacity, transform; }
        .ocl-reveal.in { opacity: 1; transform: none; }
        @media (prefers-reduced-motion: reduce) { .ocl-reveal { opacity: 1 !important; transform: none !important; transition: none !important; } }
        .ocl .sec-title { font-size: clamp(24px, 3.4vw, 34px); font-weight: 700; letter-spacing: -.01em; margin: 14px 0 10px; }
        .ocl .sec-sub { color: var(--muted); max-width: 620px; margin: 0 auto; font-size: 15.5px; }
        .ocl .center { text-align: center; }

        /* Nav */
        .ocl-nav { position: sticky; top: 0; z-index: 50; background: rgba(255,255,255,.92);
            backdrop-filter: saturate(180%) blur(10px); border-bottom: 1px solid var(--line); }
        .ocl-nav .ocl-bar { display: flex; align-items: center; gap: 22px; height: 96px; flex-wrap: nowrap; }
        .ocl-nav .logo img { height: 76px; width: auto; object-fit: contain; }
        .ocl-nav .links { display: flex; gap: 26px; margin-left: 12px; }
        .ocl-nav .links a { color: var(--ink-2); font-size: 14.5px; font-weight: 500; }
        .ocl-nav .links a:hover { color: var(--a-ink); }
        .ocl-nav .cta { margin-left: auto; display: flex; align-items: center; gap: 12px; }
        .ocl-btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; font-weight: 600;
            font-size: 14.5px; padding: 11px 20px; border-radius: 10px; cursor: pointer; border: 1px solid transparent; transition: .15s; white-space: nowrap; }
        .ocl-btn--primary { background: var(--a); color: #fff; }
        .ocl-btn--primary:hover { background: var(--a-ink); color: #fff; }
        .ocl-btn--ghost { background: #fff; color: var(--ink); border-color: var(--line); }
        .ocl-btn--ghost:hover { border-color: var(--a); color: var(--a-ink); }
        .ocl-btn--lg { padding: 14px 26px; font-size: 15.5px; border-radius: 12px; }
        @media (max-width: 900px) { .ocl-nav .links { display: none; } }

        /* Hero */
        .ocl-hero { background:
            radial-gradient(90% 120% at 88% -20%, #d9f0e2 0%, transparent 55%),
            radial-gradient(70% 90% at 5% 10%, #eef7f1 0%, transparent 50%), var(--bg);
            padding: 66px 0 74px; overflow: hidden; }
        .ocl-hero .grid { display: grid; grid-template-columns: 1.05fr .95fr; gap: 40px; align-items: center; }
        .ocl-hero .badge { display: inline-flex; align-items: center; gap: 12px; font-size: 13px; font-weight: 700;
            letter-spacing: .18em; text-transform: uppercase; color: var(--a-ink); }
        .ocl-hero .badge::before { content: ''; width: 32px; height: 2px; background: var(--a); border-radius: 2px; }
        .ocl-hero h1 { font-size: clamp(32px, 5vw, 52px); font-weight: 800; letter-spacing: -.02em; margin: 18px 0 16px; }
        .ocl-hero h1 .hl { color: var(--a); }
        .ocl-hero p.lead { font-size: 17px; max-width: 30em; margin-bottom: 26px; }
        .ocl-hero .cta { display: flex; gap: 14px; flex-wrap: wrap; }
        .ocl-hero .trust { display: flex; gap: 22px; margin-top: 26px; flex-wrap: wrap; }
        .ocl-hero .trust div { font-size: 13.5px; color: var(--muted); display: flex; align-items: center; gap: 8px; }
        .ocl-hero .trust i { color: var(--a); }
        .ocl-hero__art { position: relative; }
        .ocl-hero__art .frame { background: #fff; border: 1px solid var(--line); border-radius: 22px;
            box-shadow: 0 40px 80px -50px rgba(20,40,28,.6); padding: 14px; }
        .ocl-hero__art img { width: 100%; border-radius: 14px; display: block; }
        .ocl-hero__art .chip { position: absolute; background: #fff; border: 1px solid var(--line); border-radius: 14px;
            padding: 12px 16px; box-shadow: 0 18px 40px -24px rgba(20,40,28,.5); display: flex; align-items: center; gap: 10px; font-size: 13px; font-weight: 600; }
        .ocl-hero__art .chip i { width: 34px; height: 34px; border-radius: 9px; background: var(--a-soft); color: var(--a-ink);
            display: flex; align-items: center; justify-content: center; font-size: 15px; }
        .ocl-hero__art .chip.c1 { top: 22px; left: -14px; }
        .ocl-hero__art .chip.c2 { bottom: 26px; right: -10px; }
        @media (max-width: 860px) { .ocl-hero .grid { grid-template-columns: 1fr; } .ocl-hero__art { display: none; } }

        /* Generic section spacing */
        .ocl-sec { padding: 64px 0; }
        .ocl-sec--alt { background: var(--surface); border-top: 1px solid var(--line); border-bottom: 1px solid var(--line); }

        /* Category grid */
        .ocl-cats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 18px; margin-top: 40px; }
        .ocl-cat { background: var(--surface); border: 1px solid var(--line); border-radius: 16px; padding: 24px 20px;
            text-align: center; transition: .18s; display: block; }
        .ocl-cat:hover { transform: translateY(-4px); border-color: #bfe0cd; box-shadow: 0 22px 44px -30px rgba(20,40,28,.5); }
        .ocl-cat__ic { width: 58px; height: 58px; margin: 0 auto 14px; border-radius: 15px; background: var(--a-soft);
            color: var(--a-ink); display: flex; align-items: center; justify-content: center; font-size: 24px; overflow: hidden; }
        .ocl-cat__ic img { width: 100%; height: 100%; object-fit: cover; }
        .ocl-cat h4 { font-size: 16px; font-weight: 650; color: var(--ink); margin: 0 0 6px; }
        .ocl-cat p { font-size: 12.5px; color: var(--muted); margin: 0 0 14px; line-height: 1.4; }
        .ocl-cat .go { font-size: 13px; font-weight: 700; color: var(--a-ink); }
        @media (max-width: 900px) { .ocl-cats { grid-template-columns: repeat(2, 1fr); } }

        /* Services marketplace grid (icon + title) */
        .ocl-tagline { display: inline-flex; align-items: center; gap: 12px; color: var(--a-ink); font-weight: 600; font-size: 14px; margin-top: 6px; }
        .ocl-tagline::before, .ocl-tagline::after { content: ''; width: 22px; height: 2px; background: var(--a); opacity: .5; border-radius: 2px; }
        .ocl-svcs { display: grid; grid-template-columns: repeat(6, 1fr); gap: 16px; margin-top: 40px; }
        .ocl-svc { background: var(--surface); border: 1px solid var(--line); border-radius: 16px; padding: 22px 14px; text-align: center; transition: .18s; display: flex; flex-direction: column; align-items: center; gap: 12px; }
        .ocl-svc:hover { transform: translateY(-4px); border-color: #bfe0cd; box-shadow: 0 22px 44px -30px rgba(20,40,28,.5); }
        .ocl-ic { width: 62px; height: 62px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 26px; flex: none; }
        .ocl-svc h4 { font-size: 13.5px; font-weight: 600; color: var(--ink); margin: 0; line-height: 1.35; }
        @media (max-width: 1000px) { .ocl-svcs { grid-template-columns: repeat(4, 1fr); } }
        @media (max-width: 680px) { .ocl-svcs { grid-template-columns: repeat(3, 1fr); } }
        @media (max-width: 420px) { .ocl-svcs { grid-template-columns: repeat(2, 1fr); } }

        /* Booking systems grid (icon + title + desc) */
        .ocl-bks { display: grid; grid-template-columns: repeat(5, 1fr); gap: 16px; margin-top: 40px; }
        .ocl-bk { background: var(--surface); border: 1px solid var(--line); border-radius: 18px; padding: 26px 20px; text-align: center; transition: .18s; display: flex; flex-direction: column; align-items: center; gap: 12px; }
        .ocl-bk:hover { transform: translateY(-4px); border-color: #bfe0cd; box-shadow: 0 22px 44px -30px rgba(20,40,28,.5); }
        .ocl-bk h4 { font-size: 15.5px; font-weight: 650; color: var(--ink); margin: 0; }
        .ocl-bk p { font-size: 12.8px; color: var(--muted); margin: 0; line-height: 1.5; }
        @media (max-width: 1000px) { .ocl-bks { grid-template-columns: repeat(3, 1fr); } }
        @media (max-width: 680px) { .ocl-bks { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 420px) { .ocl-bks { grid-template-columns: 1fr; } }

        /* Steps (how it works) */
        .ocl-steps { display: grid; grid-template-columns: repeat(4, 1fr); gap: 18px; margin-top: 44px; }
        .ocl-step { background: var(--surface); border: 1px solid var(--line); border-radius: 16px; padding: 26px 22px; position: relative; }
        .ocl-step .n { position: absolute; top: 18px; right: 20px; font-size: 34px; font-weight: 800; color: #eef3ef; }
        .ocl-step .ic { width: 50px; height: 50px; border-radius: 13px; background: var(--a-soft); color: var(--a-ink);
            display: flex; align-items: center; justify-content: center; font-size: 20px; margin-bottom: 16px; }
        .ocl-step h4 { font-size: 16.5px; font-weight: 650; margin: 0 0 7px; }
        .ocl-step p { font-size: 13.5px; color: var(--muted); line-height: 1.5; }
        @media (max-width: 900px) { .ocl-steps { grid-template-columns: 1fr 1fr; } }
        @media (max-width: 560px) { .ocl-steps { grid-template-columns: 1fr; } }

        /* Templates */
        .ocl-tpl { display: grid; grid-template-columns: 1fr 1fr; gap: 34px; align-items: center; margin-top: 40px; }
        .ocl-tpl__img { border: 1px solid var(--line); border-radius: 18px; overflow: hidden; box-shadow: 0 30px 60px -40px rgba(20,40,28,.5); background: #fff; }
        .ocl-tpl__img img { width: 100%; display: block; }
        .ocl-tpl__list { list-style: none; padding: 0; margin: 20px 0 0; display: grid; gap: 12px; }
        .ocl-tpl__list li { display: flex; gap: 11px; align-items: flex-start; font-size: 15px; color: var(--ink-2); }
        .ocl-tpl__list i { color: var(--a); margin-top: 3px; }
        @media (max-width: 860px) { .ocl-tpl { grid-template-columns: 1fr; } }

        /* Pricing */
        .ocl-plans { display: grid; grid-template-columns: repeat(3, 1fr); gap: 22px; margin-top: 44px; align-items: stretch; }
        .ocl-plan { background: var(--surface); border: 1px solid var(--line); border-radius: 18px; padding: 28px 24px; transition: .18s; display: flex; flex-direction: column; }
        .ocl-plan.feat { border-color: var(--a); box-shadow: 0 30px 60px -38px rgba(31,157,85,.6); position: relative; }
        .ocl-plan.feat::before { content: 'Popular'; position: absolute; top: -12px; left: 24px; background: var(--a); color: #fff;
            font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; padding: 5px 12px; border-radius: 20px; }
        .ocl-plan .pname { font-size: 15px; font-weight: 700; color: var(--a-ink); text-transform: uppercase; letter-spacing: .04em; }
        .ocl-plan .price { font-size: 40px; font-weight: 800; color: var(--ink); margin: 12px 0 2px; letter-spacing: -.02em; }
        .ocl-plan .price small { font-size: 14px; font-weight: 500; color: var(--muted); }
        .ocl-plan .pdesc { font-size: 13.5px; color: var(--muted); margin: 6px 0 18px; min-height: 38px; }
        .ocl-plan ul { list-style: none; padding: 0; margin: 0 0 22px; display: grid; gap: 10px; align-content: start; flex: 1 0 auto; }
        .ocl-plan .ocl-btn { margin-top: auto; }
        .ocl-plan li { display: flex; gap: 10px; align-items: flex-start; font-size: 14px; color: var(--ink-2); }
        .ocl-plan li i { color: var(--a); margin-top: 3px; font-size: 13px; }
        .ocl-plan .ocl-btn { width: 100%; }
        .ocl-offer-badge { position: absolute; top: -12px; right: 20px; background: #d64545; color: #fff; font-size: 11px; font-weight: 700; padding: 5px 12px; border-radius: 20px; text-transform: uppercase; letter-spacing: .04em; }
        .ocl-plan .price .oc-old { color: var(--muted); text-decoration: line-through; font-size: 17px; font-weight: 500; margin-inline-end: 6px; }
        @media (max-width: 900px) { .ocl-plans { grid-template-columns: 1fr; max-width: 420px; margin-inline: auto; } }

        /* Stores strip */
        .ocl-stores { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; margin-top: 40px; }
        .ocl-store { background: var(--surface); border: 1px solid var(--line); border-radius: 16px; overflow: hidden; transition: .18s; }
        .ocl-store:hover { transform: translateY(-3px); box-shadow: 0 24px 44px -32px rgba(20,40,28,.5); }
        .ocl-store__cover { height: 130px; background: var(--a-soft) center/cover no-repeat; }
        .ocl-store__cover--ph { display: grid; place-items: center; background: linear-gradient(135deg, #12A150, #0b7c3b); }
        .ocl-store__cover--ph span { color: #fff; font-weight: 800; font-size: 34px; letter-spacing: .5px; opacity: .95; }
        /* themes gallery */
        .ocl-themes { display: grid; grid-template-columns: repeat(3, 1fr); gap: 26px; margin-top: 44px; justify-content: center; }
        @media (max-width: 1000px) { .ocl-themes { grid-template-columns: repeat(2, 1fr); } }
        .ocl-theme { background: var(--surface); border: 1px solid var(--line); border-radius: 18px; overflow: hidden; transition: .2s; display: flex; flex-direction: column; }
        .ocl-theme:hover { transform: translateY(-4px); box-shadow: 0 26px 48px -30px rgba(20, 40, 28, .5); }
        .ocl-theme__img { height: 340px; overflow: hidden; background: var(--a-soft); border-bottom: 1px solid var(--line); }
        .ocl-theme__img img { width: 100%; height: 100%; object-fit: cover; object-position: top center; display: block; }
        .ocl-theme__body { padding: 20px 22px 24px; display: flex; flex-direction: column; align-items: flex-start; gap: 8px; }
        .ocl-theme__body h4 { font-size: 19px; font-weight: 700; margin: 0; }
        .ocl-theme__body p { font-size: 13.5px; color: var(--muted); line-height: 1.55; margin: 0 0 6px; }
        @media (max-width: 640px) { .ocl-themes { grid-template-columns: 1fr; max-width: 380px; margin-inline: auto; } .ocl-theme__img { height: 240px; } }
        .ocl-store__body { padding: 16px 18px; }
        .ocl-store__body h4 { font-size: 16px; font-weight: 650; margin: 0 0 5px; }
        .ocl-store__body p { font-size: 13px; color: var(--muted); margin: 0 0 12px; line-height: 1.45;
            display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        @media (max-width: 900px) { .ocl-stores { grid-template-columns: 1fr; max-width: 420px; margin-inline: auto; } }

        /* Contact */
        .ocl-contact { display: grid; grid-template-columns: 1fr 1.05fr; gap: 36px; margin-top: 40px; align-items: start; }
        .ocl-contact__info { display: grid; gap: 16px; }
        .ocl-ci { display: flex; gap: 14px; align-items: flex-start; background: var(--surface); border: 1px solid var(--line);
            border-radius: 14px; padding: 18px 20px; }
        .ocl-ci i { width: 44px; height: 44px; border-radius: 11px; background: var(--a-soft); color: var(--a-ink);
            display: flex; align-items: center; justify-content: center; font-size: 17px; flex: 0 0 auto; }
        .ocl-ci .k { font-size: 13px; color: var(--muted); }
        .ocl-ci .v { font-size: 15px; font-weight: 600; color: var(--ink); word-break: break-word; }
        .ocl-form { background: var(--surface); border: 1px solid var(--line); border-radius: 18px; padding: 26px; }
        .ocl-form .g2 { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        .ocl-form label { display: block; font-size: 13px; font-weight: 600; color: var(--ink-2); margin: 0 0 6px; }
        .ocl-form .fld { margin-bottom: 14px; }
        .ocl-form input, .ocl-form textarea { width: 100%; border: 1px solid var(--line); border-radius: 10px; padding: 11px 13px;
            font-size: 14.5px; font-family: inherit; color: var(--ink); background: #fff; }
        .ocl-form input:focus, .ocl-form textarea:focus { outline: none; border-color: var(--a); box-shadow: 0 0 0 3px rgba(31,157,85,.12); }
        @media (max-width: 860px) { .ocl-contact { grid-template-columns: 1fr; } .ocl-form .g2 { grid-template-columns: 1fr; } }

        /* Newsletter */
        .ocl-news { background: var(--surface); border: 1px solid var(--line); border-radius: 24px; padding: 52px 40px;
            text-align: center; margin: 10px 0 0; box-shadow: 0 34px 70px -46px rgba(20,40,28,.55); position: relative; overflow: hidden; }
        .ocl-news::before { content: ''; position: absolute; inset: 0; background: radial-gradient(75% 130% at 50% -25%, var(--a-soft), transparent 60%); pointer-events: none; }
        .ocl-news > * { position: relative; }
        .ocl-news .mailic { width: 62px; height: 62px; border-radius: 17px; background: var(--a); color: #fff; display: flex;
            align-items: center; justify-content: center; font-size: 25px; margin: 0 auto 20px; box-shadow: 0 16px 30px -14px rgba(31,157,85,.7); }
        .ocl-news h2 { color: var(--ink); font-size: clamp(22px, 3vw, 30px); font-weight: 800; letter-spacing: -.01em; }
        .ocl-news p { color: var(--muted); max-width: 520px; margin: 12px auto 26px; font-size: 15.5px; }
        .ocl-news form { display: flex; align-items: center; gap: 10px; max-width: 520px; margin: 0 auto; }
        .ocl-news input { flex: 1; border: 1px solid var(--line); border-radius: 12px; height: 54px; padding: 0 18px; font-size: 15px; color: var(--ink); background: #fff; }
        .ocl-news input:focus { outline: none; border-color: var(--a); box-shadow: 0 0 0 3px rgba(31,157,85,.14); }
        .ocl-news .ocl-btn { height: 54px; padding: 0 30px; border-radius: 12px; flex: 0 0 auto; }
        /* Optional newsletter background image (from General Settings → Other) */
        .ocl-news.has-bg { background-size: cover; background-position: center; border-color: transparent; }
        .ocl-news.has-bg::before { background: linear-gradient(180deg, rgba(15,26,19,.74), rgba(15,26,19,.84)); }
        .ocl-news.has-bg h2, .ocl-news.has-bg p { color: #fff; }
        .ocl-news.has-bg .mailic { background: #fff; color: var(--a-ink); }
        @media (max-width: 560px) { .ocl-news form { flex-direction: column; } .ocl-news input, .ocl-news .ocl-btn { width: 100%; } }

        /* Footer */
        .ocl-foot { background: #0f1a13; color: #c7d3cb; padding: 54px 0 26px; }
        .ocl-foot .top { display: grid; grid-template-columns: 1.4fr 1fr 1fr 1fr; gap: 30px; }
        .ocl-foot img { height: 50px; margin-bottom: 16px; background: #fff; border-radius: 10px; padding: 6px 10px; }
        .ocl-foot p { color: #93a29a; font-size: 14px; line-height: 1.6; }
        .ocl-foot h5 { color: #fff; font-size: 15px; margin: 0 0 16px; font-weight: 600; }
        .ocl-foot ul { list-style: none; padding: 0; margin: 0; display: grid; gap: 10px; }
        .ocl-foot ul a { color: #93a29a; font-size: 14px; }
        .ocl-foot ul a:hover { color: var(--a); }
        .ocl-foot .bar { border-top: 1px solid #22302751; margin-top: 40px; padding-top: 20px; text-align: center; color: #6f7d74; font-size: 13px; }
        @media (max-width: 800px) { .ocl-foot .top { grid-template-columns: 1fr 1fr; } }

        /* ============ Small-phone hardening (down to ~320px) ============ */
        .ocl { overflow-x: clip; }
        .ocl img { max-width: 100%; height: auto; }
        .ocl h1, .ocl h2, .ocl h3, .ocl h4, .ocl p, .ocl a, .ocl .badge, .ocl .pname, .ocl .price { overflow-wrap: break-word; word-break: break-word; }
        .ocl .wrap, .ocl-cat, .ocl-svc, .ocl-bk, .ocl-step, .ocl-plan, .ocl-store, .ocl-theme, .ocl-ci { min-width: 0; }
        @media (max-width: 600px) {
            .ocl .wrap { padding: 0 16px; }
            .ocl-sec { padding: 46px 0; }
            .ocl-hero { padding: 40px 0 48px; }
            .ocl-hero h1 { font-size: clamp(26px, 8.5vw, 40px); }
            .ocl-hero .badge { letter-spacing: .1em; font-size: 11.5px; flex-wrap: wrap; }
            .ocl-hero p.lead { font-size: 15.5px; }
            .ocl-hero .cta { gap: 10px; }
            .ocl-hero .cta .ocl-btn { flex: 1 1 auto; }
            .ocl-nav .ocl-bar { gap: 10px; height: 62px; }
            .ocl-nav .logo img { height: 40px; }
            .ocl-nav .cta { gap: 8px; }
            .ocl-news { padding: 34px 20px; }
            .ocl-plan { padding: 24px 18px; }
            .ocl-plan .price { font-size: 34px; }
        }
        @media (max-width: 430px) {
            /* the top-bar CTA is duplicated inside the burger menu, so drop it here to save width */
            .ocl-nav .cta .ocl-btn--primary { display: none; }
            .ocl-cats { grid-template-columns: 1fr 1fr; gap: 12px; }
            .ocl-foot .top { grid-template-columns: 1fr; gap: 22px; }
        }
        @media (max-width: 360px) {
            .ocl .wrap { padding: 0 12px; }
            .ocl-cats { grid-template-columns: 1fr; max-width: 240px; margin-inline: auto; }
            .ocl-svcs { grid-template-columns: 1fr 1fr; }
            .ocl-hero .cta { flex-direction: column; }
            .ocl-hero .cta .ocl-btn { width: 100%; }
        }
    </style>
@endsection

@section('content')
<div class="ocl">

    {{-- ===================== HERO ===================== --}}
    <section class="ocl-hero" id="home">
        <div class="wrap grid">
            <div>
                <span class="badge">{{ trans('landing.launch_your_store_in_minutes') }}</span>
                <h1>{{ trans('landing.hero_banner_title') }}</h1>
                <p class="lead">{{ trans('landing.hero_banner_description') }}</p>
                <div class="cta">
                    <a href="{{ $registerUrl }}" class="ocl-btn ocl-btn--primary ocl-btn--lg">{{ trans('landing.get_started') }} &rarr;</a>
                    {{-- Same destination and wording as the "Marketplace" item in the top menu. --}}
                    <a href="{{ URL::to('marketplace') }}" class="ocl-btn ocl-btn--ghost ocl-btn--lg">
                        <i class="fa-solid fa-cart-shopping"></i> {{ trans('landing.marketplace') }}</a>
                </div>
                <div class="trust">
                    <div><i class="fa-brands fa-whatsapp"></i> {{ trans('landing.order_via_whatsapp') }}</div>
                    <div><i class="fa-solid fa-store"></i> {{ trans('landing.your_own_store') }}</div>
                    <div><i class="fa-solid fa-language"></i> {{ trans('landing.arabic_english') }}</div>
                </div>
            </div>
            <div class="ocl-hero__art">
                <div class="frame">
                    <img src="{{ helper::image_path(helper::appdata('')->landing_home_banner) }}" alt="Order Click">
                </div>
                <div class="chip c1"><i class="fa-solid fa-bag-shopping"></i> {{ trans('landing.new_order') }}</div>
                <div class="chip c2"><i class="fa-solid fa-bolt"></i> {{ trans('landing.fast_setup') }}</div>
            </div>
        </div>
    </section>

    {{-- ===================== BUSINESS CATEGORIES ===================== --}}
    <section class="ocl-sec" id="categories">
        <div class="wrap center">
            <span class="eyebrow">{{ trans('landing.business_categories') }}</span>
            <h2 class="sec-title">{{ trans('landing.choose_the_right_category_for_your_store') }}</h2>
            <p class="sec-sub">{{ trans('landing.pick_any_category_to_jump_straight_to') }}</p>
            @php
                // Arabic names for the built-in store categories (falls back to the DB name for custom ones).
                $ocCatAr = [
                    'restaurants' => 'مطاعم', 'restaurant' => 'مطاعم', 'cafés' => 'مقاهي', 'cafes' => 'مقاهي', 'cafe' => 'مقهى',
                    'grocery stores' => 'بقالات', 'grocery' => 'بقالة', 'supermarkets' => 'أسواق', 'pharmacies' => 'صيدليات',
                    'pharmacy' => 'صيدلية', 'flower shops' => 'محلات ورود', 'clothing stores' => 'متاجر ملابس', 'gift shops' => 'محلات هدايا',
                    'bakeries' => 'مخابز', 'fruit & vegetable markets' => 'أسواق الفواكه والخضار', 'dairy stores' => 'محلات ألبان',
                    'ice cream shops' => 'محلات آيس كريم', 'electronics' => 'إلكترونيات', 'perfumes' => 'عطور', 'toys' => 'ألعاب',
                    'salons' => 'صالونات', 'salon' => 'صالون', 'clinics' => 'عيادات', 'clinic' => 'عيادة', 'retail' => 'تجزئة',
                    'hotels' => 'فنادق', 'booking' => 'حجوزات',
                ];
                $ocIsAr = app()->getLocale() === 'ar';
            @endphp
            @if (count($landingCategories) > 0)
                <div class="ocl-cats">
                    @foreach ($landingCategories as $category)
                        @php
                            $categoryName = strtolower($category->name ?? '');
                            $categoryDisplay = $ocIsAr && isset($ocCatAr[$categoryName]) ? $ocCatAr[$categoryName] : $category->name;
                            $categoryIcon = 'fa-store';
                            foreach ($categoryIcons as $keyword => $icon) {
                                if (str_contains($categoryName, $keyword)) { $categoryIcon = $icon; break; }
                            }
                            $categoryImage = !empty($category->image) ? helper::image_path($category->image) : null;
                        @endphp
                        <a href="{{ $registerUrl }}" class="ocl-cat">
                            <div class="ocl-cat__ic">
                                @if ($categoryImage)
                                    <img src="{{ $categoryImage }}" alt="{{ $category->name }}">
                                @else
                                    <i class="fa-solid {{ $categoryIcon }}"></i>
                                @endif
                            </div>
                            <h4>{{ $categoryDisplay }}</h4>
                            <p>{{ trans('landing.launch_your_store_in_this_category') }}</p>
                            <span class="go">{{ trans('landing.register_now') }}</span>
                        </a>
                    @endforeach
                </div>
            @endif
                {{-- Features tagged to this system (admin > Features). --}}
                @php $ocSysFeatures = \App\Models\Features::forSystem('orders'); @endphp
                @if ($ocSysFeatures->count())
                    <div class="ocl-svcs" style="margin-top:18px;">
                        @foreach ($ocSysFeatures as $f)
                            <div class="ocl-svc">
                                <span class="ocl-ic" style="background:#1f9d551a;color:#1f9d55;">
                                    @if ($f->hasImage())
                                        <img src="{{ helper::image_path($f->image) }}" alt="" style="width:26px;height:26px;object-fit:contain;">
                                    @else
                                        <i class="fa-solid fa-star"></i>
                                    @endif
                                </span>
                                <h4>{{ $f->title }}</h4>
                                <p class="fs-7 text-muted">{{ \Illuminate\Support\Str::limit(strip_tags($f->description), 90) }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif
        </div>
    </section>

    {{-- ===================== SERVICES MARKETPLACE ===================== --}}
    @php $ar = app()->getLocale() === 'ar'; @endphp
    {{-- ===================== FEATURES (All Systems) ===================== --}}
    {{-- Marketing content from admin > Features. "All Systems" entries show here; a feature tagged
         to one system is rendered inside that system's own section instead. --}}
    @php $ocGeneralFeatures = \App\Models\Features::forSystem('all'); @endphp
    @if ($ocGeneralFeatures->count())
        <section class="ocl-sec" id="features">
            <div class="wrap center">
                <span class="eyebrow">{{ trans('landing.features') }}</span>
                <h2 class="sec-title">{{ trans('labels.features_general') }}</h2>
                <div class="ocl-svcs">
                    @foreach ($ocGeneralFeatures as $f)
                        <div class="ocl-svc">
                            <span class="ocl-ic" style="background:#1f9d551a;color:#1f9d55;">
                                @if ($f->hasImage())
                                    <img src="{{ helper::image_path($f->image) }}" alt="" style="width:26px;height:26px;object-fit:contain;">
                                @else
                                    <i class="fa-solid fa-star"></i>
                                @endif
                            </span>
                            <h4>{{ $f->title }}</h4>
                            <p class="fs-7 text-muted">{{ \Illuminate\Support\Str::limit(strip_tags($f->description), 90) }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section class="ocl-sec ocl-sec--alt" id="services-marketplace">
        <div class="wrap center">
            <span class="eyebrow">{{ trans('landing.services_marketplace') }}</span>
            <h2 class="sec-title">{{ trans('landing.services_marketplace') }}</h2>
            <p class="sec-sub">{{ trans('landing.discover_top_professionals_in_different_categories') }}</p>
            @php
                $ocServices = [
                    ['fa-pen-nib', '#7c5cff', 'Graphic Design', 'التصميم الجرافيكي'],
                    ['fa-clapperboard', '#ef4444', 'UGC Content Creators', 'صنّاع محتوى UGC'],
                    ['fa-camera', '#f59e0b', 'Photography', 'التصوير الفوتوغرافي'],
                    ['fa-video', '#3b82f6', 'Videography', 'تصوير الفيديو'],
                    ['fa-film', '#6366f1', 'Video Editing', 'مونتاج الفيديو'],
                    ['fa-share-nodes', '#ec4899', 'Social Media Management', 'إدارة وسائل التواصل'],
                    ['fa-pen', '#22c55e', 'Content Writing', 'كتابة المحتوى'],
                    ['fa-magnifying-glass-chart', '#2563eb', 'SEO Services', 'خدمات SEO'],
                    ['fa-file-lines', '#64748b', 'Article Writing', 'كتابة المقالات'],
                    ['fa-language', '#14b8a6', 'Translation Services', 'خدمات الترجمة'],
                    ['fa-code', '#0f172a', 'Website Development', 'تطوير المواقع'],
                    ['fa-mobile-screen', '#8b5cf6', 'Mobile App Development', 'تطوير التطبيقات'],
                    ['fa-bullhorn', '#2563eb', 'Digital Marketing', 'التسويق الرقمي'],
                    ['fa-envelope', '#4f46e5', 'Email Marketing', 'التسويق بالبريد'],
                    ['fa-microphone', '#6b7280', 'Voice Over', 'التعليق الصوتي'],
                    ['fa-crown', '#eab308', 'Celebrities & Public Figures', 'المشاهير والشخصيات'],
                    ['fa-headset', '#22c55e', 'Virtual Assistant', 'مساعد افتراضي'],
                    ['fa-ellipsis', '#94a3b8', 'More Services', 'المزيد من الخدمات'],
                ];
            @endphp
            <div class="ocl-svcs">
                @foreach ($ocServices as $s)
                    <a href="{{ $registerUrl }}" class="ocl-svc">
                        <span class="ocl-ic" style="background:{{ $s[1] }}1a;color:{{ $s[1] }};"><i class="fa-solid {{ $s[0] }}"></i></span>
                        <h4>{{ $ar ? $s[3] : $s[2] }}</h4>
                    </a>
                @endforeach
            </div>
                {{-- Features tagged to this system (admin > Features). --}}
                @php $ocSysFeatures = \App\Models\Features::forSystem('service'); @endphp
                @if ($ocSysFeatures->count())
                    <div class="ocl-svcs" style="margin-top:18px;">
                        @foreach ($ocSysFeatures as $f)
                            <div class="ocl-svc">
                                <span class="ocl-ic" style="background:#1f9d551a;color:#1f9d55;">
                                    @if ($f->hasImage())
                                        <img src="{{ helper::image_path($f->image) }}" alt="" style="width:26px;height:26px;object-fit:contain;">
                                    @else
                                        <i class="fa-solid fa-star"></i>
                                    @endif
                                </span>
                                <h4>{{ $f->title }}</h4>
                                <p class="fs-7 text-muted">{{ \Illuminate\Support\Str::limit(strip_tags($f->description), 90) }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif
        </div>
    </section>

    {{-- ===================== BOOKING SYSTEMS ===================== --}}
    <section class="ocl-sec" id="booking-systems">
        <div class="wrap center">
            <span class="eyebrow">{{ trans('landing.booking_systems') }}</span>
            <h2 class="sec-title">{{ trans('landing.booking_systems') }}</h2>
            <p class="sec-sub">{{ trans('landing.a_complete_set_of_smart_booking_systems') }}</p>
            <span class="ocl-tagline">{{ trans('landing.easy_management_better_experience_more_bookings') }}</span>
            @php
                $ocBookings = [
                    ['fa-stethoscope', '#22c55e', 'Clinics & Medical Centers', 'العيادات والمراكز الطبية', 'Book appointments with doctors and specialists.', 'حجز المواعيد مع الأطباء والمتخصصين.'],
                    ['fa-scale-balanced', '#d97706', 'Lawyers & Consultants', 'المحامون والمستشارون', 'Book legal consultations and meetings easily.', 'حجز الاستشارات القانونية والمواعيد بسهولة.'],
                    ['fa-graduation-cap', '#16a34a', 'Tutors & Courses', 'المدرسون والدورات', 'Book tutoring sessions and training courses.', 'حجز الدروس والدورات التدريبية بمرونة.'],
                    ['fa-scissors', '#ec4899', 'Salons & Beauty', 'الصالونات والتجميل', 'Book hair, beauty and skincare appointments.', 'حجز مواعيد العناية بالشعر والبشرة والتجميل.'],
                    ['fa-dumbbell', '#3b82f6', 'Gyms & Fitness', 'الأندية الرياضية واللياقة', 'Book workouts and fitness programs.', 'حجز البرامج التدريبية والحصص الرياضية.'],
                    ['fa-bell-concierge', '#8b5cf6', 'Hotels & Accommodation', 'الفنادق والمنتجعات', 'Book rooms and stays at the best prices.', 'حجز الغرف والإقامات بأفضل الأسعار.'],
                    ['fa-car', '#0f172a', 'Car Rentals', 'تأجير السيارات', 'Book daily or monthly car rentals in simple steps.', 'حجز السيارات اليومية أو الشهرية بخطوات بسيطة.'],
                    ['fa-calendar-check', '#14b8a6', 'Event & Venues', 'قاعات المناسبات', 'Book venues and halls for events and gatherings.', 'حجز القاعات للمناسبات والحفلات والاجتماعات.'],
                    ['fa-camera-retro', '#ec4899', 'Photographers', 'المصورون', 'Book photography sessions for individuals and companies.', 'حجز جلسات التصوير للأفراد والشركات.'],
                    ['fa-screwdriver-wrench', '#6b7280', 'Maintenance & Repair', 'خدمات الصيانة والإصلاح', 'Book maintenance and repair for homes and devices.', 'حجز مواعيد الصيانة والإصلاح للمنازل والأجهزة.'],
                    ['fa-truck', '#22c55e', 'Delivery Services', 'خدمات النقل والتوصيل', 'Book delivery for items within or between cities.', 'حجز خدمات نقل البضائع داخل المدن أو بينها.'],
                    ['fa-spa', '#ec4899', 'Spa & Wellness', 'المنتجعات والسبا', 'Book spa, massage and wellness sessions.', 'حجز جلسات الاسترخاء والعلاج الطبيعي.'],
                    ['fa-paw', '#d97706', 'Pet Care', 'رعاية الحيوانات الأليفة', 'Book pet care, grooming and veterinary visits.', 'حجز مواعيد الرعاية والتجميل والفحص البيطري.'],
                    ['fa-plane', '#3b82f6', 'Flights & Travel', 'حجز تذاكر الطيران', 'Book domestic and international flights.', 'حجز رحلات الطيران الداخلية والدولية.'],
                    ['fa-ellipsis', '#94a3b8', 'More Services', 'المزيد من الخدمات', 'Discover more services available for booking.', 'اكتشف المزيد من الخدمات المتاحة للحجز.'],
                ];
            @endphp
            <div class="ocl-bks">
                @foreach ($ocBookings as $b)
                    <div class="ocl-bk">
                        <span class="ocl-ic" style="background:{{ $b[1] }}1a;color:{{ $b[1] }};"><i class="fa-solid {{ $b[0] }}"></i></span>
                        <h4>{{ $ar ? $b[3] : $b[2] }}</h4>
                        <p>{{ $ar ? $b[5] : $b[4] }}</p>
                    </div>
                @endforeach
            </div>
                {{-- Features tagged to this system (admin > Features). --}}
                @php $ocSysFeatures = \App\Models\Features::forSystem('booking'); @endphp
                @if ($ocSysFeatures->count())
                    <div class="ocl-svcs" style="margin-top:18px;">
                        @foreach ($ocSysFeatures as $f)
                            <div class="ocl-svc">
                                <span class="ocl-ic" style="background:#1f9d551a;color:#1f9d55;">
                                    @if ($f->hasImage())
                                        <img src="{{ helper::image_path($f->image) }}" alt="" style="width:26px;height:26px;object-fit:contain;">
                                    @else
                                        <i class="fa-solid fa-star"></i>
                                    @endif
                                </span>
                                <h4>{{ $f->title }}</h4>
                                <p class="fs-7 text-muted">{{ \Illuminate\Support\Str::limit(strip_tags($f->description), 90) }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif
        </div>
    </section>

    {{-- ===================== HOW IT WORKS (WhatsApp flow) ===================== --}}
    <section class="ocl-sec ocl-sec--alt" id="how">
        <div class="wrap">
            <div class="center">
                @php
                    // Heading and steps come from admin > How It Works. The hardcoded copy below is
                    // only a fallback for installs that have not set any steps yet.
                    // On the Arabic site use the Arabic column, but fall back to the English one
                    // whenever it is blank so the heading can never disappear.
                    $ocIsArWork  = app()->getLocale() === 'ar';
                    $ocWorkTitle = trim((string) (helper::appdata('')->work_title ?? ''));
                    $ocWorkSub   = trim((string) (helper::appdata('')->work_subtitle ?? ''));
                    if ($ocIsArWork) {
                        $ocWorkTitle = trim((string) (helper::appdata('')->work_title_ar ?? '')) ?: $ocWorkTitle;
                        $ocWorkSub   = trim((string) (helper::appdata('')->work_subtitle_ar ?? '')) ?: $ocWorkSub;
                    }
                @endphp
                <span class="eyebrow">{{ trans('landing.how_it_works') }}</span>
                <h2 class="sec-title">{{ $ocWorkTitle ?: (app()->getLocale() === 'ar' ? 'من واتساب إلى الطلب في خطوات بسيطة' : 'From WhatsApp to order in a few simple steps') }}</h2>
                <p class="sec-sub">{{ $ocWorkSub ?: (app()->getLocale() === 'ar' ? 'الطلب يبدأ من واتساب وينتهي في لوحة تحكم البائع مباشرة.' : 'Ordering starts on WhatsApp and lands straight in the merchant dashboard.') }}</p>
            </div>
            @php
                $ocSteps = app()->getLocale() === 'ar'
                    ? [
                        ['fa-qrcode', 'إعلان / QR', 'العميل يمسح رمز QR أو يضغط على رابط الإعلان.'],
                        ['fa-whatsapp', 'واتساب البائع', 'ينتقل مباشرة إلى واتساب المتجر ويختار الخدمة.'],
                        ['fa-store', 'متجر Order Click', 'يفتح المتجر أو صفحة الحجز ويكمل طلبه بسهولة.'],
                        ['fa-bell', 'إشعار للبائع', 'يُحفظ الطلب في اللوحة ويصل إشعار إلى واتساب البائع.'],
                      ]
                    : [
                        ['fa-qrcode', 'Advertisement / QR', 'Customer scans a QR code or taps an ad link.'],
                        ['fa-whatsapp', 'Merchant WhatsApp', 'They land on the store WhatsApp and pick a service.'],
                        ['fa-store', 'Order Click store', 'The store or booking page opens and they complete the order.'],
                        ['fa-bell', 'Merchant notified', 'The order is saved to the dashboard and pings the merchant.'],
                      ];
            @endphp
            <div class="ocl-steps">
                @if (!empty($works) && count($works))
                    {{-- Admin-managed steps. Image is optional — a numbered default icon stands in. --}}
                    @php $ocStepIcons = ['fa-layer-group', 'fa-credit-card', 'fa-circle-check', 'fa-rocket']; @endphp
                    @foreach ($works as $i => $w)
                        <div class="ocl-step">
                            <span class="n">{{ $w->reorder_id ?: $i + 1 }}</span>
                            <div class="ic">
                                @if (!empty($w->image) && file_exists(storage_path('app/public/landing/images/png/' . $w->image)))
                                    <img src="{{ helper::image_path($w->image) }}" alt="" style="width:28px;height:28px;object-fit:contain;">
                                @else
                                    <i class="fa-solid {{ $ocStepIcons[$i] ?? 'fa-circle-check' }}"></i>
                                @endif
                            </div>
                            @php
                                $wTitle = $ocIsArWork ? (trim((string) $w->title_ar) ?: $w->title) : $w->title;
                                $wSub   = $ocIsArWork ? (trim((string) $w->sub_title_ar) ?: $w->sub_title) : $w->sub_title;
                            @endphp
                            <h4>{{ $wTitle }}</h4>
                            <p>{{ $wSub }}</p>
                        </div>
                    @endforeach
                @else
                    @foreach ($ocSteps as $i => $s)
                        <div class="ocl-step">
                            <span class="n">{{ $i + 1 }}</span>
                            <div class="ic"><i class="{{ $s[0] == 'fa-whatsapp' ? 'fa-brands fa-whatsapp' : 'fa-solid '.$s[0] }}"></i></div>
                            <h4>{{ $s[1] }}</h4>
                            <p>{{ $s[2] }}</p>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </section>

    {{-- ===================== TEMPLATES ===================== --}}
    <section class="ocl-sec" id="templates">
        <div class="wrap">
            <div class="ocl-tpl">
                @php
                    // Feature a single, good-looking preview here (the Restaurant theme, else the
                    // first live theme). The full list lives in the "Our themes" gallery below.
                    $themeDir = storage_path('app/public/admin-assets/images/theme/');
                    $liveThemes = collect($themes)->filter(fn($t) => !empty($t->image) && file_exists($themeDir . $t->image));
                    $featuredTheme = $liveThemes->firstWhere('name', 'Restaurant') ?: $liveThemes->first();
                    $featuredImg = $featuredTheme->image ?? 'theme-restaurant.png';
                @endphp
                <div class="ocl-tpl__img">
                    <img src="{{ helper::image_path($featuredImg) }}" alt="Store template preview" loading="lazy">
                </div>
                <div>
                    <span class="eyebrow eyebrow--left">{{ trans('landing.ready_templates') }}</span>
                    <h2 class="sec-title" style="text-align:start;">{{ trans('landing.beautiful_templates_that_convert') }}</h2>
                    <p style="text-align:start;">{{ trans('landing.pick_a_template_customise_colours_and_layout') }}</p>
                    <ul class="ocl-tpl__list">
                        <li><i class="fa-solid fa-circle-check"></i> {{ trans('landing.grid_or_list_layout') }}</li>
                        <li><i class="fa-solid fa-circle-check"></i> {{ trans('landing.your_own_colours_logo') }}</li>
                        <li><i class="fa-solid fa-circle-check"></i> {{ trans('landing.fully_mobile_responsive') }}</li>
                    </ul>
                    <div style="margin-top:24px;">
                        <a href="{{ $registerUrl }}" class="ocl-btn ocl-btn--primary ocl-btn--lg">{{ trans('landing.get_started') }} &rarr;</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== PRICING ===================== --}}
    @if (count($planlist) > 0)
    <section class="ocl-sec ocl-sec--alt" id="pricing">
        <div class="wrap">
            <div class="center">
                <span class="eyebrow">{{ trans('landing.pricing_plan') }}</span>
                <h2 class="sec-title">{{ trans('landing.choose_your_plan') }}</h2>
                <p class="sec-sub">{{ trans('landing.simple_transparent_plans_that_grow_with_your') }}</p>
            </div>
            <div class="ocl-plans">
                @foreach ($planlist as $index => $plan)
                    @if ((int) ($plan->visibility ?? 1) === 2) @continue @endif
                    @php
                        $isAr = app()->getLocale() === 'ar';
                        $mo = $isAr ? 'شهر' : 'month'; $mos = $isAr ? 'أشهر' : 'months'; $yr = $isAr ? 'سنة' : 'year';
                        $durMap = [1 => '/ '.$mo, 2 => '/ 3 '.$mos, 3 => '/ 6 '.$mos, 4 => '/ '.$yr, 5 => trans('landing.lifetime')];
                        $dur = $plan->duration && isset($durMap[$plan->duration]) ? $durMap[$plan->duration] : '';
                        $planFeatures = array_filter(explode('|', (string) $plan->features));
                        $offerActive = method_exists($plan, 'offerStatus') && $plan->offerStatus() === 'active';
                        $eff = method_exists($plan, 'effectivePrice') ? $plan->effectivePrice() : $plan->price;
                        $offerLabel = method_exists($plan, 'offerLabel') ? $plan->offerLabel() : null;
                        $isRec = (int) ($plan->recommended ?? 2) === 1 || $index == 1;
                        $lim = is_array($plan->plan_limits ?? null) ? $plan->plan_limits : [];
                        $xf = is_array($plan->plan_extra_features ?? null) ? $plan->plan_extra_features : [];
                        $limLine = function ($k, $labelEn, $labelAr) use ($lim, $isAr) {
                            if (empty($lim[$k]['type'])) return null;
                            $val = (string) $lim[$k]['type'] === '2' ? ($isAr ? 'غير محدود' : 'Unlimited') : ($lim[$k]['count'] ?? 0);
                            return $val . ' ' . ($isAr ? $labelAr : $labelEn);
                        };
                    @endphp
                    <div class="ocl-plan {{ $isRec ? 'feat' : '' }}">
                        @if ($offerLabel)<span class="ocl-offer-badge">{{ $offerLabel }}</span>@endif
                        <div class="pname">{{ $plan->name }}</div>
                        <div class="price">
                            @if ($offerActive && $eff < (float) $plan->price)<small class="oc-old">{{ $plan->priceFmt($plan->price) }}</small> @endif
                            {{ method_exists($plan, 'priceFmt') ? $plan->priceFmt($eff) : $eff }} <small>{{ $dur }}</small>
                        </div>
                        <p class="pdesc">{{ $plan->description }}</p>
                        <ul>
                            <li><i class="fa-solid fa-check"></i>
                                {{ $plan->order_limit == -1 ? trans('landing.unlimited') : $plan->order_limit }}
                                {{ \App\Helpers\Systems::entityLabel($plan->system, 'primary', $plan->order_limit) }}</li>
                            @if ($l = $limLine('branch', 'branches', 'فرع'))<li><i class="fa-solid fa-check"></i> {{ $l }}</li>@endif
                            @if ($l = $limLine('whatsapp', 'WhatsApp numbers', 'رقم واتساب'))<li><i class="fa-solid fa-check"></i> {{ $l }}</li>@endif
                            @if ($l = $limLine('team', 'team / staff', 'عضو فريق'))<li><i class="fa-solid fa-check"></i> {{ $l }}</li>@endif
                            @foreach (array_keys($xf) as $fk)
                                <li><i class="fa-solid fa-check"></i> {{ \Illuminate\Support\Str::title(str_replace('_', ' ', $fk)) }}</li>
                            @endforeach
                            @foreach ($planFeatures as $feature)
                                <li><i class="fa-solid fa-check"></i> {{ $feature }}</li>
                            @endforeach
                        </ul>
                        <a href="{{ $registerUrl }}" class="ocl-btn {{ $isRec ? 'ocl-btn--primary' : 'ocl-btn--ghost' }} ocl-btn--lg">{{ trans('landing.subscribe') }}</a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- ===================== THEMES GALLERY ===================== --}}
    @php
        $themeDir = storage_path('app/public/admin-assets/images/theme/');
        // Only themes whose preview image actually exists on disk.
        $galleryThemes = collect($themes)->filter(fn($t) => !empty($t->image) && file_exists($themeDir . $t->image));
        $isArLp = app()->getLocale() === 'ar';
        // Short blurb per theme (the theme table has no description column).
        $themeBlurbs = [
            'Restaurant' => $isArLp
                ? 'قالب دافئ يفتح الشهية للمطاعم والكافيهات والمطابخ السحابية — قائمة أنيقة، سلة جانبية، صفحات منتجات ودفع سلس، متجاوب بالكامل.'
                : 'A warm, appetite-first storefront for restaurants, cafés & cloud kitchens — hero menu, live cart drawer, product pages and a smooth checkout, fully mobile.',
            'Retail' => $isArLp
                ? 'متجر أنيق للأزياء والأحذية والإكسسوارات — قائمة تسوّق بفلاتر، صفحات منتجات بخيارات ومقاسات، سلة جانبية ودفع كامل، متجاوب بالكامل.'
                : 'A polished storefront for fashion, shoes, bags & accessories — filterable shop, product pages with options & sizes, side bag and full checkout, fully mobile.',
            'Grocery' => $isArLp
                ? 'متجر بقالة وخضار وفواكه سريع — أرفف بفلاتر، بطاقات منتجات مدمجة، سلة جانبية ودفع كامل مع الطلب عبر واتساب.'
                : 'A fast grocery & fresh-market storefront — aisle filters, compact product cards, a side basket and full checkout with WhatsApp ordering.',
            'Clinic' => $isArLp
                ? 'موقع عيادة أو مستشفى — أطباء وأقسام وحجز مواعيد مع تأكيد عبر واتساب. أضف أطبائك من لوحة التحكم.'
                : 'A clinic & hospital site — doctors, departments and appointment booking with WhatsApp confirmation. Manage your doctors from the panel.',
            'Booking' => $isArLp
                ? 'موقع حجوزات للفنادق والقاعات والمواعيد — عرض الخدمات ونموذج حجز مع تأكيد عبر واتساب.'
                : 'A booking site for hotels, venues & appointments — service listings and a booking form with WhatsApp confirmation.',
            'Pharmacy' => $isArLp
                ? 'متجر صيدلية ودواء نظيف — تصنيفات، بطاقات منتجات مدمجة، فلاتر، سلة جانبية ودفع كامل مع الطلب عبر واتساب.'
                : 'A clean pharmacy & drugstore storefront — category tiles, compact product cards, filters, a side basket and full checkout with WhatsApp ordering.',
            'Appointment' => $isArLp
                ? 'موقع صالون وتجميل — خدمات ومختصون وحجز مواعيد مع تأكيد عبر واتساب. للشعر والبشرة والأظافر والسبا.'
                : 'A salon & beauty site — services, specialists and appointment booking with WhatsApp confirmation. For hair, skin, nails & spa.',
            'Classic' => $isArLp
                ? 'تصميم كلاسيكي مرن يناسب أي متجر — بسيط وسريع وسهل التخصيص بألوانك وشعارك.'
                : 'A clean, flexible layout that suits any store — simple, fast and easy to brand with your own colours & logo.',
        ];
    @endphp
    @if ($galleryThemes->count() > 0)
    <section class="ocl-sec" id="templates-gallery">
        <div class="wrap">
            <div class="center">
                <span class="eyebrow">{{ $isArLp ? 'القوالب' : 'Our themes' }}</span>
                <h2 class="sec-title">{{ $isArLp ? 'قوالب جاهزة لمتجرك' : 'Ready-made themes for your store' }}</h2>
                <p class="sec-sub">{{ $isArLp ? 'اختر قالباً احترافياً، خصّصه بألوانك، وأطلق متجرك خلال دقائق.' : 'Pick a professional theme, brand it with your colours, and launch in minutes.' }}</p>
            </div>
            <div class="ocl-themes">
                @foreach ($galleryThemes as $theme)
                    @php
                        $blurb = $themeBlurbs[$theme->name] ?? ($isArLp
                            ? 'قالب عصري متجاوب مع الجوال يمكنك تخصيصه بألوانك وشعارك.'
                            : 'A modern, mobile-ready storefront theme you can brand with your own colours & logo.');
                        $themeNamesAr = [
                            'Classic' => 'كلاسيكي', 'Restaurant' => 'مطاعم', 'Retail' => 'تجزئة', 'Grocery' => 'بقالة',
                            'Pharmacy' => 'صيدلية', 'Booking' => 'حجوزات', 'Clinic' => 'عيادة', 'Appointment' => 'مواعيد وصالونات',
                        ];
                        $themeDisplay = $isArLp && isset($themeNamesAr[$theme->name]) ? $themeNamesAr[$theme->name] : $theme->name;
                    @endphp
                    <div class="ocl-theme">
                        <div class="ocl-theme__img">
                            <img src="{{ helper::image_path($theme->image) }}" alt="{{ $theme->name }} theme preview" loading="lazy">
                        </div>
                        <div class="ocl-theme__body">
                            <h4>{{ $themeDisplay }}</h4>
                            <p>{{ $blurb }}</p>
                            <a href="{{ $registerUrl }}" class="ocl-btn ocl-btn--ghost">{{ trans('landing.get_started') }} &rarr;</a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- ===================== CONTACT ===================== --}}
    <section class="ocl-sec ocl-sec--alt" id="contact">
        <div class="wrap">
            <div class="center">
                <span class="eyebrow">{{ trans('landing.contact_us') }}</span>
                <h2 class="sec-title">{{ trans('landing.get_in_touch') }}</h2>
                <p class="sec-sub">{{ trans('landing.contact_section_title') }}</p>
            </div>
            <div class="ocl-contact">
                <div class="ocl-contact__info">
                    <div class="ocl-ci"><i class="fa-solid fa-envelope"></i><div><div class="k">{{ trans('landing.email') }}</div><div class="v">{{ helper::appdata('')->email }}</div></div></div>
                    <div class="ocl-ci"><i class="fa-solid fa-phone"></i><div><div class="k">{{ trans('landing.mobile') }}</div><div class="v">{{ helper::appdata('')->contact }}</div></div></div>
                    <div class="ocl-ci"><i class="fa-solid fa-location-dot"></i><div><div class="k">{{ trans('landing.address') }}</div><div class="v">{{ helper::appdata('')->address }}</div></div></div>
                </div>
                <form class="ocl-form" action="{{ URL::to('inquiry') }}" method="POST">
                    @csrf
                    <div class="g2">
                        <div class="fld"><label>{{ trans('landing.first_name') }} *</label><input type="text" name="first_name" required></div>
                        <div class="fld"><label>{{ trans('landing.last_name') }} *</label><input type="text" name="last_name" required></div>
                        <div class="fld"><label>{{ trans('landing.email') }} *</label><input type="email" name="emaill" required></div>
                        <div class="fld"><label>{{ trans('landing.mobile') }} *</label><input type="number" name="mobile" onKeyPress="if(this.value.length==10) return false;" required></div>
                    </div>
                    <div class="fld"><label>{{ trans('landing.message') }} *</label><textarea rows="4" name="message" placeholder="{{ trans('landing.write_your_message_2') }}" required></textarea></div>
                    @include('landing.layout.recaptcha')
                    <button type="submit" class="ocl-btn ocl-btn--primary ocl-btn--lg" style="width:100%;">{{ trans('landing.submit') }}</button>
                </form>
            </div>
        </div>
    </section>

    {{-- ===================== NEWSLETTER ===================== --}}
    <section class="ocl-sec" style="padding-top:20px;">
        <div class="wrap">
            @php $nlBg = @helper::appdata('')->subscribe_newsletter_image; @endphp
            <div class="ocl-news {{ $nlBg ? 'has-bg' : '' }}" @if ($nlBg) style="background-image:url('{{ helper::image_path($nlBg) }}')" @endif>
                <div class="mailic"><i class="fa-regular fa-envelope"></i></div>
                <h2>{{ trans('landing.subscribe_section_title_msg') }}</h2>
                <p>{{ trans('landing.subscribe_section_description') }}</p>
                <form action="{{ URL::to('emailsubscribe') }}" method="POST">
                    @csrf
                    <input type="email" name="email" placeholder="{{ trans('landing.enter_your_email') }}" required>
                    <button type="submit" class="ocl-btn ocl-btn--primary">{{ trans('landing.subscribe') }}</button>
                </form>
            </div>
        </div>
    </section>


</div>
@endsection

@section('scripts')
<script>
    (function () {
        if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
        var sel = '.ocl .center, .ocl-cat, .ocl-step, .ocl-plan, .ocl-store, .ocl-theme, .ocl-tpl__img, .ocl-tpl > div:last-child, .ocl-contact__info, .ocl-form, .ocl-news, .ocl-foot .top';
        var els = Array.prototype.slice.call(document.querySelectorAll(sel));
        if (!els.length || !('IntersectionObserver' in window)) return;
        // Hide only now that we know JS + observer are available (no-JS keeps everything visible).
        els.forEach(function (el) { el.classList.add('ocl-reveal'); });
        // Stagger cards within each grid.
        ['.ocl-cats', '.ocl-steps', '.ocl-plans', '.ocl-stores', '.ocl-themes'].forEach(function (g) {
            document.querySelectorAll(g).forEach(function (grid) {
                Array.prototype.slice.call(grid.children).forEach(function (c, i) {
                    c.style.transitionDelay = (i % 4) * 70 + 'ms';
                });
            });
        });
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (e) {
                if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); }
            });
        }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });
        els.forEach(function (el) { io.observe(el); });
    })();
</script>
@endsection
