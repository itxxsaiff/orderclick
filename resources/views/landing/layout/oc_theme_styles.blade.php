{{-- Shared Order Click landing/visitor theme (clean light). Loaded on every landing page. --}}
<style>
    body { background: #f6faf7 !important; }
    html, body { max-width: 100%; overflow-x: clip; }
    .ocl img { max-width: 100%; height: auto; }
    header.main-header, body > footer.dark-legacy { display: none !important; }

    .ocl { --a: #1f9d55; --a-ink: #137a40; --a-soft: #e8f5ee; --ink: #17201a; --ink-2: #3c473f;
        --muted: #67736a; --line: #e4eae2; --bg: #f6faf7; --surface: #ffffff;
        font-family: 'Poppins', system-ui, -apple-system, sans-serif; color: var(--ink); }
    .ocl * { box-sizing: border-box; }
    .ocl a { text-decoration: none; }
    .ocl .wrap { max-width: 1160px; margin: 0 auto; padding: 0 22px; }
    .ocl h1, .ocl h2, .ocl h3 { color: var(--ink); margin: 0; line-height: 1.15; }
    .ocl p { margin: 0; color: var(--ink-2); }
    .ocl .center { text-align: center; }

    .ocl-btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; font-weight: 600;
        font-size: 14.5px; padding: 11px 20px; border-radius: 10px; cursor: pointer; border: 1px solid transparent; transition: .15s; white-space: nowrap; }
    .ocl-btn--primary { background: var(--a); color: #fff; }
    .ocl-btn--primary:hover { background: var(--a-ink); color: #fff; }
    .ocl-btn--ghost { background: #fff; color: var(--ink); border-color: var(--line); }
    .ocl-btn--ghost:hover { border-color: var(--a); color: var(--a-ink); }
    .ocl-btn--lg { padding: 14px 26px; font-size: 15.5px; border-radius: 12px; }

    /* Shared clean nav */
    .ocl-nav { position: sticky; top: 0; z-index: 60; background: rgba(255,255,255,.94);
        backdrop-filter: saturate(180%) blur(10px); border-bottom: 1px solid var(--line); }
    .ocl-nav .ocl-bar { display: flex; align-items: center; gap: 22px; height: 96px; flex-wrap: nowrap; }
    .ocl-nav .logo img { height: 76px; width: auto; object-fit: contain; }
    .ocl-nav .links { display: flex; gap: 24px; margin-left: 12px; }
    .ocl-nav .links a { color: var(--ink-2); font-size: 14.5px; font-weight: 500; }
    .ocl-nav .links a:hover { color: var(--a-ink); }
    .ocl-nav .cta { margin-left: auto; display: flex; align-items: center; gap: 12px; }
    .ocl-nav .lang { display: flex; align-items: center; }
    .ocl-nav .lang > a { display: inline-flex; align-items: center; }
    .ocl-nav .lang img { width: 26px; height: 26px; border-radius: 50%; object-fit: cover; border: 1px solid var(--line); }
    .ocl-nav .burger { display: none; background: none; border: 0; color: var(--ink); font-size: 22px; }
    @media (max-width: 992px) { .ocl-nav .links { display: none; } .ocl-nav .burger { display: inline-flex; } .ocl-nav .ocl-bar { height: 74px; } .ocl-nav .logo img { height: 56px; } .ocl-nav .cta .ocl-btn--ghost { display: none; } }
    .ocl-nav .burger { width: 44px; height: 44px; border-radius: 12px; align-items: center; justify-content: center;
        border: 1px solid var(--line); background: #fff; }
    .ocl-nav .burger:hover { border-color: var(--a); color: var(--a-ink); }

    /* Redesigned mobile offcanvas menu */
    .ocl-mnav { width: min(340px, 86vw) !important; background: var(--bg); border: 0;
        box-shadow: 0 24px 70px -20px rgba(16,40,26,.35); display: flex; flex-direction: column; }
    .ocl-mnav__head { display: flex; align-items: center; justify-content: space-between; gap: 12px;
        padding: 16px 18px; background: #fff; border-bottom: 1px solid var(--line); }
    .ocl-mnav__logo img { height: 46px; width: auto; object-fit: contain; }
    .ocl-mnav__close { width: 40px; height: 40px; border-radius: 11px; border: 1px solid var(--line);
        background: #fff; color: var(--ink); font-size: 18px; display: inline-flex; align-items: center; justify-content: center; transition: .15s; }
    .ocl-mnav__close:hover { background: var(--a-soft); color: var(--a-ink); border-color: var(--a); }
    .ocl-mnav__body { flex: 1; overflow-y: auto; padding: 14px; }
    .ocl-mnav__links { display: flex; flex-direction: column; gap: 4px; }
    .ocl-mnav__links a { display: flex; align-items: center; gap: 13px; padding: 13px 14px; border-radius: 12px;
        color: var(--ink); font-size: 15.5px; font-weight: 500; transition: .15s; }
    .ocl-mnav__links a .ic { width: 38px; height: 38px; flex: 0 0 38px; border-radius: 10px; background: var(--a-soft);
        color: var(--a-ink); display: inline-flex; align-items: center; justify-content: center; font-size: 15px; transition: .15s; }
    .ocl-mnav__links a .arw { margin-inline-start: auto; font-size: 12px; color: var(--muted); transition: .15s; }
    .ocl-mnav__links a:active, .ocl-mnav__links a:hover { background: #fff; box-shadow: 0 6px 18px -10px rgba(16,40,26,.25); }
    .ocl-mnav__links a:hover .ic { background: var(--a); color: #fff; }
    .ocl-mnav__links a:hover .arw { color: var(--a-ink); transform: translateX(2px); }
    .ocl-mnav__lang { margin-top: 18px; padding-top: 16px; border-top: 1px solid var(--line); }
    .ocl-mnav__lbl { display: block; font-size: 12px; font-weight: 600; text-transform: uppercase;
        letter-spacing: .06em; color: var(--muted); margin: 0 6px 10px; }
    .ocl-mnav__flags { display: flex; flex-wrap: wrap; gap: 8px; }
    .ocl-mnav__flags a { display: inline-flex; align-items: center; gap: 8px; padding: 8px 12px; border-radius: 999px;
        border: 1px solid var(--line); background: #fff; color: var(--ink-2); font-size: 13.5px; font-weight: 500; transition: .15s; }
    .ocl-mnav__flags a img { width: 20px; height: 20px; border-radius: 50%; object-fit: cover; }
    .ocl-mnav__flags a.on, .ocl-mnav__flags a:hover { border-color: var(--a); color: var(--a-ink); background: var(--a-soft); }
    .ocl-mnav__foot { padding: 16px 18px calc(16px + env(safe-area-inset-bottom)); background: #fff;
        border-top: 1px solid var(--line); display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
    .ocl-mnav__foot .ocl-btn { width: 100%; padding: 13px 14px; }

    /* Shared clean footer */
    .ocl-foot { background: #0f1a13; color: #c7d3cb; padding: 54px 0 26px; }
    .ocl-foot .top { display: grid; grid-template-columns: 1.4fr 1fr 1fr 1fr; gap: 30px; }
    .ocl-foot img { height: 50px; margin-bottom: 16px; background: #fff; border-radius: 10px; padding: 6px 10px; }
    .ocl-foot p { color: #93a29a; font-size: 14px; line-height: 1.6; }
    .ocl-foot h5 { color: #fff; font-size: 15px; margin: 0 0 16px; font-weight: 600; }
    .ocl-foot ul { list-style: none; padding: 0; margin: 0; display: grid; gap: 10px; }
    .ocl-foot ul a { color: #93a29a; font-size: 14px; }
    .ocl-foot ul a:hover { color: var(--a); }
    .ocl-socials { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 18px; }
    .ocl-socials a { width: 40px; height: 40px; border-radius: 50%; display: grid; place-items: center; background: rgba(255,255,255,.08);
        color: #fff; font-size: 16px; transition: background .2s, transform .2s; }
    .ocl-socials a:hover { background: var(--a); color: #fff; transform: translateY(-2px); }
    .ocl-foot .barbottom { border-top: 1px solid #22302751; margin-top: 40px; padding-top: 20px; text-align: center; color: #6f7d74; font-size: 13px; }
    @media (max-width: 800px) { .ocl-foot .top { grid-template-columns: 1fr 1fr; } }

    /* Inner visitor pages (about / privacy / terms / blogs / faqs) — clean, no card */
    .ocl-page { background: var(--bg); min-height: 60vh; }
    .ocl-content { padding: 40px 0 76px; }
    .ocl .crumbs { display: inline-flex; align-items: center; gap: 8px; font-size: 13.5px; color: var(--muted); margin-bottom: 34px; }
    .ocl .crumbs a { color: var(--a-ink); font-weight: 600; }
    .ocl .crumbs .sep { color: var(--line-2, #cdd8c8); }
    .ocl-doc { max-width: 860px; }
    .ocl-doc > :first-child { margin-top: 0 !important; }
    .ocl-content, .ocl-content p, .ocl-content li, .ocl-content span, .ocl-content td { color: var(--ink-2); line-height: 1.8; font-size: 16px; }
    .ocl-content h1 { color: var(--ink); font-size: clamp(28px, 3.4vw, 36px); font-weight: 800; letter-spacing: -.015em; margin: 34px 0 16px; }
    .ocl-content h2 { color: var(--ink); font-size: 23px; font-weight: 700; margin: 32px 0 12px; }
    .ocl-content h3, .ocl-content h4 { color: var(--ink); font-size: 18px; font-weight: 700; margin: 26px 0 10px; }
    .ocl-content a { color: var(--a-ink); font-weight: 600; }
    .ocl-content img { max-width: 100%; border-radius: 10px; }
    .ocl-content ul, .ocl-content ol { padding-left: 20px; margin: 12px 0; }

    .ocl-blogs { display: grid; grid-template-columns: repeat(3, 1fr); gap: 22px; }
    .ocl-blog { background: var(--surface); border: 1px solid var(--line); border-radius: 16px; overflow: hidden; transition: .18s; display: block; }
    .ocl-blog:hover { transform: translateY(-3px); box-shadow: 0 24px 44px -32px rgba(20,40,28,.5); }
    .ocl-blog__img { height: 184px; background: var(--a-soft) center/cover no-repeat; }
    .ocl-blog__body { padding: 18px 20px; }
    .ocl-blog__body h4 { font-size: 17px; font-weight: 650; color: var(--ink); margin: 0 0 8px; }
    .ocl-blog__body p { font-size: 13.5px; color: var(--muted); margin: 0 0 12px; line-height: 1.5; }
    .ocl-blog .go { color: var(--a-ink); font-weight: 700; font-size: 13px; }
    .ocl-empty { text-align: center; color: var(--muted); padding: 40px; }
    @media (max-width: 900px) { .ocl-blogs { grid-template-columns: 1fr; max-width: 440px; margin-inline: auto; } }

    .ocl-reveal { opacity: 0; transform: translateY(26px); transition: opacity .6s ease, transform .7s cubic-bezier(.22,.61,.36,1); will-change: opacity, transform; }
    .ocl-reveal.in { opacity: 1; transform: none; }
    @media (prefers-reduced-motion: reduce) { .ocl-reveal { opacity: 1 !important; transform: none !important; transition: none !important; } }

    /* Feature cards (landing.partials.feature_cards): centred, wide, icon beside the text. */
    .ocl-feats { display: flex; flex-wrap: wrap; justify-content: center; gap: 18px; margin-top: 36px; }
    .ocl-feat { flex: 1 1 300px; max-width: 370px; display: flex; align-items: flex-start; gap: 16px; text-align: start;
        background: #fff; border: 1px solid var(--line, #e4eae2); border-radius: 18px; padding: 22px 22px; transition: .18s; }
    .ocl-feat:hover { transform: translateY(-3px); border-color: #bfe0cd; box-shadow: 0 22px 44px -30px rgba(20,40,28,.5); }
    .ocl-feat__ic { flex: none; width: 50px; height: 50px; border-radius: 14px; display: flex; align-items: center; justify-content: center;
        background: var(--a-soft, #e8f5ee); color: var(--a, #1f9d55); font-size: 21px; }
    .ocl-feat__ic img { width: 28px; height: 28px; object-fit: contain; }
    .ocl-feat h4 { font-size: 16px; font-weight: 700; color: var(--ink, #17201a); margin: 2px 0 6px; line-height: 1.3; }
    .ocl-feat p { font-size: 13.5px; color: var(--muted, #67736a); margin: 0; line-height: 1.55; }
    /* Only one feature: a wide green highlight band instead of a lonely small box. */
    .ocl-feats--single .ocl-feat { flex-basis: 100%; max-width: 760px; align-items: center; gap: 22px; padding: 28px 32px;
        border-color: #cfe9da; background: linear-gradient(135deg, #f1faf4, #e3f4ea); }
    .ocl-feats--single .ocl-feat__ic { width: 64px; height: 64px; border-radius: 18px; font-size: 26px; background: #fff; color: var(--a, #1f9d55);
        box-shadow: 0 12px 26px -16px rgba(20,40,28,.45); }
    .ocl-feats--single .ocl-feat__ic img { width: 34px; height: 34px; }
    .ocl-feats--single .ocl-feat h4 { font-size: 19px; }
    .ocl-feats--single .ocl-feat p { font-size: 14.5px; }
    @media (max-width: 560px) {
        .ocl-feats--single .ocl-feat { flex-direction: column; align-items: center; text-align: center; padding: 24px 20px; }
    }
</style>
