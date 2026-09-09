{{-- Premium chrome + page styling for premium-theme stores: header, footer, modals,
     contact, cart, product details — modern colours, hovers, radius. --}}
<style>
    :root { --ocp: var(--bs-primary, #1f9d55); --ocp-cream:#faf7f1; --ocp-ink:#26201b; --ocp-line:#ece4d7;
        --ocp-muted:#7a726a; --ocp-dark:#18211c; }
    body { background: var(--ocp-cream) !important; color: var(--ocp-ink); font-family: 'Outfit','Poppins',system-ui,sans-serif; }

    /* ===================== HEADER ===================== */
    .header-main { background: rgba(255,255,255,.92) !important; backdrop-filter: blur(10px);
        box-shadow: 0 4px 24px -16px rgba(40,30,15,.4); border-bottom: 1px solid var(--ocp-line); }
    .header-main .nav-link { font-weight: 600; border-radius: 30px; padding: 8px 14px !important; transition: .16s; }
    .header-main .nav-link:hover { color: var(--ocp) !important; background: color-mix(in srgb, var(--ocp) 9%, #fff); }
    .header-main .nav-link.active { color: var(--ocp) !important; }
    .header-main .cart-counting, #cartcount { background: var(--ocp) !important; color: #fff !important; }
    .header-main .login-buuton, .header-main .btn { border-radius: 30px !important; }
    .header-main .profile_image { border: 2px solid var(--ocp); }

    /* ===================== FOOTER (premium dark, no more raw-green clash) ===================== */
    footer, footer.bg-changer { background: var(--ocp-dark) !important; color: #c3cec6 !important;
        border-radius: 34px 34px 0 0; margin-top: 46px; }
    footer .footer-title, footer h5 { color: #fff !important; font-weight: 700; letter-spacing: .01em; }
    footer .footersubtitle, footer p { color: #9fb0a5 !important; }
    footer .footer-logo, footer a.text-white, footer .footer-logo * { color: #fff !important; }
    footer .footer-right-side a { color: #b7c4bc !important; transition: .16s; display: inline-flex; }
    footer .footer-right-side a:hover { color: #fff !important; padding-inline-start: 5px; }
    footer .app_download_img a { border-color: rgba(255,255,255,.22) !important; border-radius: 12px; transition: .16s; }
    footer .app_download_img a:hover { border-color: var(--ocp) !important; transform: translateY(-2px); }
    footer hr { border-color: rgba(255,255,255,.12) !important; opacity: 1; }
    footer .footer-social a, footer .social a { border: 1px solid rgba(255,255,255,.18); border-radius: 50%;
        width: 38px; height: 38px; display: inline-flex; align-items: center; justify-content: center; transition: .16s; }
    footer .footer-social a:hover, footer .social a:hover { background: var(--ocp); border-color: var(--ocp); color: #fff; transform: translateY(-2px); }

    /* ===================== BUTTONS (pill, accent, hover) ===================== */
    .btn, .btn-primary, .btn-secondary, .btn-store, .addtocart, .add-cart-btn, .ct-add, .checkout,
    input[type="submit"].btn-primary, .btn-outline-primary { border-radius: 40px !important; font-weight: 650 !important; transition: .16s !important; }
    .btn-primary, .btn-store, button.addtocart, .add-cart-btn, .ct-add, .checkout, .bg-primary {
        background: var(--ocp) !important; border-color: var(--ocp) !important; color: #fff !important; }
    .btn-primary:hover, .btn-store:hover, button.addtocart:hover, .checkout:hover { filter: brightness(.93); transform: translateY(-2px);
        box-shadow: 0 14px 26px -12px color-mix(in srgb, var(--ocp) 70%, transparent); }

    /* ===================== CARDS + generic ===================== */
    .card, .card-bg, .post-slide, .box-shadow { border: 1px solid var(--ocp-line) !important; border-radius: 22px !important;
        box-shadow: 0 26px 52px -38px rgba(60,40,20,.34) !important; transition: transform .18s, box-shadow .18s; }
    .card { background: #fff !important; }
    .post-slide:hover, .product .card:hover, .box-post:hover .card { transform: translateY(-5px);
        box-shadow: 0 34px 60px -34px rgba(60,40,20,.4) !important; }

    /* headings + prices */
    .page-title, .pages-title, .post-title, .details_item_name { color: var(--ocp-ink); }
    .details_item_price.pricing, .pro-price, .now, .price, .products-price .price { color: var(--ocp) !important; }

    /* breadcrumb */
    .breadcrumb-sec { background: transparent !important; padding-top: 28px !important; }
    .breadcrumb-sec .breadcrumb { background: transparent !important; padding: 0 !important; }

    /* ===================== FORMS (contact / newsletter / checkout) ===================== */
    .form-control, .form-select, textarea.form-control, .input-width, select { border-radius: 12px !important;
        border: 1.5px solid var(--ocp-line) !important; padding: 11px 14px; transition: .15s; }
    .form-control:focus, .form-select:focus, textarea:focus { border-color: var(--ocp) !important;
        box-shadow: 0 0 0 3px color-mix(in srgb, var(--ocp) 16%, transparent) !important; }
    .form-label { font-weight: 600; color: var(--ocp-ink); }

    /* ===================== CONTACT PAGE ===================== */
    .select-delivery { border-radius: 26px !important; border: 1px solid var(--ocp-line) !important;
        border-top: 4px solid var(--ocp) !important; box-shadow: 0 36px 70px -46px rgba(40,30,15,.45) !important; overflow: hidden; }
    section.my-5 .card { border-radius: 24px !important; }
    .contact-info, .contact-detail, .map-box { border-radius: 20px !important; }
    .contact-info .card, .map iframe { border-radius: 18px !important; }

    /* ===================== PRODUCT DETAILS ===================== */
    .view-product .card, .view-product .card-bg { border-radius: 24px !important; }
    .sp-wrap img, .view-product img, .theme1grid_image img, .box-img img, .card-img-top { border-radius: 16px; }
    .theme-1category-card .theme-1active, .navgation_lower li { border-radius: 40px; }

    /* ===================== MODALS (add-to-cart / product / search) ===================== */
    .modal-content, .search-modal-content { border: 0 !important; border-radius: 26px !important; overflow: hidden;
        box-shadow: 0 60px 110px -55px rgba(30,20,10,.6) !important; }
    .modal-content .modal-header { border-bottom: 1px solid var(--ocp-line) !important; }
    .modal-content img, .card-modal-iages img { border-radius: 18px; }
    .modal .btn-close, .modal-content .btn-close { background-color: #f3ede3; border-radius: 50%; padding: 10px; opacity: 1; transition: .15s; }
    .modal .btn-close:hover { background-color: #ece2d4; transform: rotate(90deg); }
    .modal .btn, .modal-content .btn { border-radius: 30px !important; }
    .modal .quantity, .modal .qty-box, .modal .plus, .modal .minus, .qty-input { border-radius: 30px !important; }
    .modal .variant-box, .modal .extras-box, .modal .form-check, .modal .nav-pills .nav-link { border-radius: 12px; }
    .modal .nav-pills .nav-link.active { background: var(--ocp) !important; }
    .carousel-control-prev-icon, .carousel-control-next-icon { background-color: rgba(0,0,0,.35); border-radius: 50%; padding: 14px; background-size: 45%; }

    /* ===================== CART PAGE ===================== */
    .cart-item, .cart-card, .cart-product, .order-summery, .summary-card, .tbl_cart_product { border: 1px solid var(--ocp-line) !important;
        border-radius: 18px !important; background: #fff !important; }
    .cart-img img, .cart-product img, .item-img img { border-radius: 14px; }

    /* about / who-we-are / testimonial / blog cards */
    .who-we-are .card, .testimonial .card, .blogs-card .card, .about-card, .whoweare .card { border-radius: 22px !important; }

    /* ===================== NEWSLETTER / SUBSCRIBE band ===================== */
    .subscription-main { background: linear-gradient(120deg, var(--ocp), color-mix(in srgb, var(--ocp) 48%, #000)) !important;
        border-radius: 28px !important; overflow: hidden; position: relative; }
    .subscription-main img, .subscription-image { opacity: 0 !important; }
    .subscription-main .subscription-text h3, .subscription-main .subscription-text p,
    .caption-subscription h3, .caption-subscription p, .subscription-main h3, .subscription-main p { color: #fff !important; }
    .subscription-main .subscribe-input { border-radius: 40px !important; background: #fff !important; padding: 6px 6px 6px 18px; }
    .subscription-main .subscribe-input .btn, .subscription-main .btn { border-radius: 40px !important; }

    section { scroll-margin-top: 82px; }
</style>
