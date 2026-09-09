{{-- =========================================================================
     Classic Theme — Demo Showcase (Template 2)
     Renders sample categories + products (using the platform's own bundled
     category renders) so a brand-new / sparse store still looks full and
     professional. Clearly labelled as a preview, and it disappears on its own
     once the vendor adds enough of their own products (see index.blade.php).
     No real cart/data hooks are touched — every button here is inert.
========================================================================= --}}
@php
    $ct_demo_img = fn($f) => asset('storage/app/public/admin-assets/images/category/' . $f);

    $ct_demo_cats = [
        ['key' => 'all', 'name' => 'All', 'img' => 'category-6a50014c9dbcd.png'],
        ['key' => 'fastfood', 'name' => 'Fast Food', 'img' => 'category-6a50014c9dbcd.png'],
        ['key' => 'beverages', 'name' => 'Beverages', 'img' => 'category-6a5001590488d.png'],
        ['key' => 'grocery', 'name' => 'Grocery', 'img' => 'category-6a500161c0f85.png'],
        ['key' => 'flowers', 'name' => 'Flowers', 'img' => 'category-6a5001752ab49.png'],
        ['key' => 'gifts', 'name' => 'Gifts', 'img' => 'category-6a50018656db8.png'],
    ];

    $ct_demo_items = [
        ['cat' => 'fastfood', 'name' => 'Classic Beef Burger', 'img' => 'category-6a50014c9dbcd.png', 'price' => 8.9, 'old' => 11.0, 'rate' => '4.9', 'badge' => 'Popular'],
        ['cat' => 'fastfood', 'name' => 'Crispy Fries Combo', 'img' => 'category-6a50014c9dbcd.png', 'price' => 6.5, 'old' => 0, 'rate' => '4.7', 'badge' => null],
        ['cat' => 'beverages', 'name' => 'Cappuccino', 'img' => 'category-6a5001590488d.png', 'price' => 3.5, 'old' => 0, 'rate' => '4.8', 'badge' => 'Hot'],
        ['cat' => 'beverages', 'name' => 'Iced Caramel Latte', 'img' => 'category-6a5001590488d.png', 'price' => 4.2, 'old' => 5.5, 'rate' => '4.6', 'badge' => null],
        ['cat' => 'grocery', 'name' => 'Fresh Grocery Basket', 'img' => 'category-6a500161c0f85.png', 'price' => 24.0, 'old' => 0, 'rate' => '4.9', 'badge' => null],
        ['cat' => 'grocery', 'name' => 'Daily Essentials Pack', 'img' => 'category-6a500161c0f85.png', 'price' => 18.75, 'old' => 22.0, 'rate' => '4.5', 'badge' => 'Save'],
        ['cat' => 'flowers', 'name' => 'Pink Rose Bouquet', 'img' => 'category-6a5001752ab49.png', 'price' => 29.0, 'old' => 0, 'rate' => '5.0', 'badge' => 'New'],
        ['cat' => 'gifts', 'name' => 'Luxury Gift Box', 'img' => 'category-6a50018656db8.png', 'price' => 34.5, 'old' => 39.0, 'rate' => '4.8', 'badge' => null],
    ];
@endphp

<style>
    .ct-demo { background: linear-gradient(180deg, #fbf8f2 0%, #f5efe3 100%); position: relative; }
    .ct-demo__ribbon {
        position: absolute; top: 18px; right: -46px; z-index: 3;
        transform: rotate(45deg);
        background: var(--ct-accent); color: #fff;
        font-size: 11px; font-weight: 700; letter-spacing: .18em; text-transform: uppercase;
        padding: 7px 60px; box-shadow: 0 6px 18px -8px rgba(0,0,0,.5);
    }
    .ct-demo-note {
        display: flex; align-items: flex-start; gap: 16px;
        background: #fff; border: 1px solid var(--ct-line);
        border-left: 4px solid var(--ct-accent);
        border-radius: 4px; padding: 18px 20px; margin-bottom: 40px;
        box-shadow: 0 18px 40px -30px rgba(33,29,24,.55);
    }
    .ct-demo-note__ico {
        flex: 0 0 44px; width: 44px; height: 44px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        background: rgba(184,134,11,.12); color: var(--ct-accent); font-size: 19px;
    }
    .ct-demo-note h4 { font-family: var(--ct-serif); font-size: 18px; margin: 0 0 4px; color: var(--ct-ink); }
    .ct-demo-note p { margin: 0; font-size: 14px; color: var(--ct-muted); line-height: 1.55; }
    .ct-demo-note p b { color: var(--ct-ink); }
    .ct-demo-note__close {
        margin-left: auto; flex: 0 0 auto; border: 0; background: transparent;
        color: var(--ct-muted); font-size: 18px; cursor: pointer; line-height: 1; padding: 2px 4px;
    }
    .ct-demo-note__close:hover { color: var(--ct-ink); }
    .ct-demo-chip { cursor: pointer; }
    .ct-demo-sample {
        opacity: .7; cursor: not-allowed; pointer-events: none;
        border-style: dashed;
    }
    .ct-demo-item { transition: opacity .2s ease; }
</style>

<section class="ct-section ct-demo" id="ct-demo">
    <div class="ct-demo__ribbon">Preview</div>
    <div class="container">

        <div class="ct-demo-note" id="ct-demo-note">
            <div class="ct-demo-note__ico"><i class="fa-solid fa-wand-magic-sparkles"></i></div>
            <div>
                <h4>This is a sample preview of your storefront</h4>
                <p>
                    The categories and products below are <b>demo items</b> to show how your store will look once it's stocked.
                    Head to your dashboard → <b>Categories</b> and <b>Products</b> to add your own — this preview
                    <b>disappears automatically</b> as soon as you do.
                </p>
            </div>
            <button type="button" class="ct-demo-note__close" onclick="ctDemoDismiss()" title="Hide preview">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="ct-head-center">
            <span class="ct-eyebrow">Sample Menu</span>
            <h2 class="ct-heading ct-serif">A taste of what your store can look like</h2>
            <p class="ct-subheading">Beautiful product cards, categories and pricing — all ready for your own items.</p>
        </div>

        {{-- Category chips (self-contained filter, scoped to this preview) --}}
        <ul class="ct-cats" id="ct-demo-cats">
            @foreach ($ct_demo_cats as $i => $cat)
                <li class="ct-demo-chip {{ $i == 0 ? 'active1' : '' }}" data-cat="{{ $cat['key'] }}" onclick="ctDemoFilter(this,'{{ $cat['key'] }}')">
                    <div class="ct-cat">
                        <img src="{{ $ct_demo_img($cat['img']) }}" alt="{{ $cat['name'] }}">
                        <p>{{ $cat['name'] }}</p>
                    </div>
                </li>
            @endforeach
        </ul>

        <div class="row g-4" id="ct-demo-grid">
            @foreach ($ct_demo_items as $item)
                <div class="col-6 col-lg-3 col-md-4 ct-demo-item" data-cat="{{ $item['cat'] }}">
                    <div class="ct-card">
                        <div class="ct-card__media">
                            @if ($item['badge'])
                                <span class="ct-badge">{{ $item['badge'] }}</span>
                            @endif
                            <img src="{{ $ct_demo_img($item['img']) }}" alt="{{ $item['name'] }}" style="padding:14px;">
                        </div>
                        <div class="ct-card__body">
                            <span class="ct-card__rating"><i class="fa-solid fa-star"></i> {{ $item['rate'] }}</span>
                            <span class="ct-card__title">{{ $item['name'] }}</span>
                            <div class="ct-card__price">
                                <span class="now">{{ helper::currency_formate($item['price'], @$storeinfo->id) }}</span>
                                @if ($item['old'] > 0)
                                    <del>{{ helper::currency_formate($item['old'], @$storeinfo->id) }}</del>
                                @endif
                            </div>
                            <div class="ct-card__foot">
                                <span class="ct-add ct-demo-sample"><i class="fa-regular fa-plus"></i> Sample item</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    </div>
</section>

<script>
    function ctDemoFilter(el, cat) {
        document.querySelectorAll('#ct-demo-cats .ct-demo-chip').forEach(function (c) { c.classList.remove('active1'); });
        el.classList.add('active1');
        document.querySelectorAll('#ct-demo-grid .ct-demo-item').forEach(function (item) {
            var show = cat === 'all' || item.getAttribute('data-cat') === cat;
            item.style.display = show ? '' : 'none';
        });
    }
    function ctDemoDismiss() {
        var s = document.getElementById('ct-demo');
        if (s) { s.style.display = 'none'; }
        try { sessionStorage.setItem('ctDemoHidden', '1'); } catch (e) {}
    }
    (function () {
        try {
            if (sessionStorage.getItem('ctDemoHidden') === '1') {
                var s = document.getElementById('ct-demo');
                if (s) { s.style.display = 'none'; }
            }
        } catch (e) {}
    })();
</script>
