<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ trans('labels.set_up_your_store_with_ai') }}</title>
    <link rel="stylesheet" href="{{ url(env('ASSETSPATHURL') . 'web-assets/font-awesome/css/all.min.css') }}">
    @php
        $ar = app()->getLocale() === 'ar';
        $ssMessages = $ar
            ? ['يتم قراءة معلومات نشاطك', 'إنشاء الأقسام المناسبة', 'إضافة المنتجات والخدمات', 'كتابة أوصاف احترافية', 'اختيار ألوان متجرك', 'اللمسات الأخيرة على متجرك']
            : ['Reading your business info', 'Creating the right categories', 'Adding products & services', 'Writing professional descriptions', 'Picking your store colours', 'Putting the finishing touches'];
        $ssRetry = $ar ? 'يمكنك المحاولة مرة أخرى أو المتابعة إلى لوحة التحكم.' : 'You can try again or continue to your dashboard.';
    @endphp
    <style>
        :root { --a:#1f9d55; --a-ink:#137a40; --a-soft:#e8f5ee; --ink:#17201a; --muted:#67736a; --line:#e4eae2; }
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; font-family: 'Poppins','Outfit', system-ui, -apple-system, sans-serif;
            background: radial-gradient(80% 100% at 85% -10%, #d9f0e2 0%, transparent 55%), radial-gradient(70% 90% at 5% 110%, #eef7f1 0%, transparent 50%), #f6faf7;
            display: flex; align-items: center; justify-content: center; padding: 28px; color: var(--ink); }
        .ss-card { background: #fff; border: 1px solid var(--line); border-radius: 28px; max-width: 640px; width: 100%;
            padding: 52px 52px 44px; box-shadow: 0 50px 100px -55px rgba(20,60,35,.4); text-align: center; }
        .ss-badge { display: inline-flex; align-items: center; gap: 9px; background: var(--a-soft); color: var(--a-ink); font-weight: 700;
            font-size: 12.5px; letter-spacing: .05em; text-transform: uppercase; padding: 9px 16px; border-radius: 30px; margin-bottom: 22px; }
        .ss-card h1 { font-size: 29px; font-weight: 800; margin: 0 0 12px; letter-spacing: -.02em; line-height: 1.2; }
        .ss-card h1 b { color: var(--a); font-weight: 800; }
        .ss-card p.sub { color: var(--muted); font-size: 16px; margin: 0 0 30px; line-height: 1.6; max-width: 30em; margin-inline: auto; }
        .ss-field { text-align: start; margin-bottom: 20px; }
        .ss-field label { display: block; font-weight: 650; font-size: 15px; margin-bottom: 10px; }
        .ss-field textarea { width: 100%; min-height: 150px; border: 1.5px solid var(--line); border-radius: 16px; padding: 18px 20px;
            font-size: 16px; line-height: 1.6; font-family: inherit; resize: vertical; background: #fbfdfb; transition: .15s; }
        .ss-field textarea::placeholder, .ss-field input::placeholder { color: #a7b1a9; }
        .ss-field textarea:focus, .ss-field input:focus { outline: none; border-color: var(--a); background: #fff; box-shadow: 0 0 0 4px rgba(31,157,85,.12); }
        .ss-field input { width: 100%; height: 52px; border: 1.5px solid var(--line); border-radius: 14px; padding: 0 16px; font-size: 15.5px; font-family: inherit; background: #fbfdfb; transition: .15s; }
        .ss-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        @media (max-width:520px){ .ss-row { grid-template-columns: 1fr; } }
        /* country-code select — matches .ss-field input exactly */
        .ss-wa { display: flex; gap: 10px; align-items: stretch; }
        .ss-code { flex: 0 0 130px; width: 130px; height: 52px; border: 1.5px solid var(--line); border-radius: 14px;
            padding: 0 12px; font-size: 15px; font-family: inherit; background: #fbfdfb; color: inherit; cursor: pointer; transition: .15s; }
        .ss-code:focus { outline: none; border-color: var(--a); background: #fff; box-shadow: 0 0 0 4px rgba(31,157,85,.12); }
        .ss-wa input { flex: 1; min-width: 0; }
        .ss-hint { font-size: 13px; color: #9aa79d; margin-top: 8px; }
        .ss-btn { width: 100%; height: 60px; border: 0; border-radius: 16px; background: linear-gradient(135deg, #23a95c, var(--a));
            color: #fff; font-weight: 700; font-size: 17.5px; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; gap: 11px; transition: .16s; }
        .ss-btn:hover { filter: brightness(1.04); transform: translateY(-2px); box-shadow: 0 22px 40px -18px rgba(31,157,85,.6); }
        .ss-skip { display: inline-block; margin-top: 18px; color: #8a978d; font-size: 14.5px; font-weight: 500; text-decoration: none; }
        .ss-skip:hover { color: var(--a-ink); }

        /* working overlay */
        .ss-working { display: none; }
        .ss-working.on { display: block; }
        .ss-form.off { display: none; }
        .ss-spinner { width: 84px; height: 84px; margin: 10px auto 24px; border-radius: 50%;
            border: 6px solid var(--a-soft); border-top-color: var(--a); animation: ssSpin 1s linear infinite; }
        @keyframes ssSpin { to { transform: rotate(360deg); } }
        .ss-working h2 { font-size: 23px; font-weight: 750; margin: 0 0 6px; }
        .ss-progress { color: var(--a-ink); font-weight: 650; font-size: 16px; min-height: 24px; transition: opacity .3s; }
        .ss-steps { list-style: none; padding: 0; margin: 26px auto 0; max-width: 340px; text-align: start; }
        .ss-steps li { display: flex; align-items: center; gap: 11px; padding: 8px 0; color: #a7b1a9; font-size: 14.5px; }
        .ss-steps li.done { color: var(--ink); }
        .ss-steps li.done i { color: var(--a); }
        .ss-steps li i { width: 18px; }
        .ss-note { color: #9aa79d; font-size: 13px; margin-top: 22px; }
        .ss-err { display:none; background:#fdecec; color:#b3261e; border:1px solid #f6c9c5; border-radius:14px; padding:13px 16px; font-size:14.5px; margin-top:16px; text-align:start; }
        /* AI file upload */
        .ss-drop { border: 2px dashed #cfe3d6; border-radius: 16px; padding: 24px 20px; text-align: center; background: #f7fcf9; cursor: pointer; transition: .18s; }
        .ss-drop:hover, .ss-drop.drag { border-color: var(--a); background: #eefaf2; }
        .ss-drop.busy { cursor: default; }
        .ss-drop__ic { width: 54px; height: 54px; border-radius: 15px; background: #e8f5ee; color: var(--a-ink); display:flex; align-items:center; justify-content:center; font-size:23px; margin: 0 auto 12px; }
        .ss-drop__t { font-weight: 700; font-size: 15.5px; color: var(--ink); }
        .ss-drop__s { font-size: 13px; color: #8a978d; margin-top: 5px; }
        .ss-drop__steps { display:none; }
        .ss-drop.busy .ss-drop__idle { display:none; }
        .ss-drop.busy .ss-drop__steps { display:block; }
        .ss-spin { width: 42px; height: 42px; border: 3px solid #d8ebe0; border-top-color: var(--a); border-radius: 50%; animation: ssspin .9s linear infinite; margin: 0 auto 14px; }
        @keyframes ssspin { to { transform: rotate(360deg); } }
        .ss-drop__ok { display:none; background:#eafaf0; color:#137a40; border:1px solid #bfe6cd; border-radius:13px; padding:12px 15px; font-size:14px; margin-top:14px; text-align:start; }
        .ss-drop__msg { display:none; background:#fdf3e6; color:#9a6a12; border:1px solid #f0d9ad; border-radius:13px; padding:12px 15px; font-size:14px; margin-top:14px; text-align:start; }
        .ss-or { position:relative; text-align:center; color:#a7b1a9; font-size:13px; font-weight:600; margin:20px 0 4px; }
        .ss-or::before, .ss-or::after { content:''; position:absolute; top:50%; width:38%; height:1px; background:var(--line); }
        .ss-or::before { left:0; } .ss-or::after { right:0; }
        @media (max-width:560px){ .ss-card { padding: 38px 26px 34px; border-radius: 22px; } .ss-card h1 { font-size: 24px; } }
    </style>
</head>

<body>
    <div class="ss-card">
        {{-- ===== Step 1: quick question ===== --}}
        <div class="ss-form" id="ssForm">
            <span class="ss-badge"><i class="fa-solid fa-wand-magic-sparkles"></i> {{ trans('labels.ai_setup') }}</span>
            <h1>{{ trans('labels.let_s_set_up') }} <b>{{ $storeName }}</b> {{ $ar ? 'لك' : '' }}</h1>
            <p class="sub">{{ trans('labels.tell_us_what_your_business_offers_and') }}</p>

            {{-- AI file upload: read a menu / product list (image or PDF) and pre-fill the form --}}
            <div class="ss-field">
                <div class="ss-drop" id="ssDrop">
                    <div class="ss-drop__idle">
                        <div class="ss-drop__ic"><i class="fa-solid fa-file-arrow-up"></i></div>
                        <div class="ss-drop__t">{{ trans('labels.upload_your_menu_or_product_list') }}</div>
                        <div class="ss-drop__s">{{ trans('labels.photo_or_pdf_ai_reads_it_and') }}</div>
                    </div>
                    <div class="ss-drop__steps">
                        <div class="ss-spin"></div>
                        <div class="ss-drop__t" id="ssDropState">{{ trans('labels.reading_your_file') }}</div>
                        <div class="ss-drop__s">{{ trans('labels.this_can_take_a_few_seconds') }}</div>
                    </div>
                    <input type="file" id="ssFile" accept="image/jpeg,image/png,image/webp,application/pdf,.jpg,.jpeg,.png,.webp,.pdf" hidden>
                </div>
                <div class="ss-drop__ok" id="ssDropOk"></div>
                <div class="ss-drop__msg" id="ssDropMsg"></div>
                <div class="ss-or">{{ trans('labels.or_fill_it_in_manually_below') }}</div>
            </div>

            <div class="ss-field">
                <label>{{ trans('labels.what_products_or_services_do_you_offer') }}</label>
                <textarea id="ssOfferings" placeholder="{{ trans('labels.e_g_italian_restaurant_margherita_pizza_3') }}"></textarea>
                <div class="ss-hint">{{ trans('labels.list_a_few_products_with_prices_if') }}</div>
            </div>
            <div class="ss-field">
                <label>{{ trans('labels.main_categories_optional') }}</label>
                <input type="text" id="ssCategories" placeholder="{{ trans('labels.e_g_pizzas_burgers_drinks') }}">
                <div class="ss-hint">{{ trans('labels.leave_blank_and_ai_will_choose_sensible') }}</div>
            </div>
            <div class="ss-field">
                <label>{{ trans('labels.whatsapp_number_for_orders') }} <span style="color:#d64545">*</span></label>
                <div class="ss-wa">
                    <select id="ssWaCode" class="ss-code">
                        @foreach (helper::countries() as $c)
                            <option value="{{ ltrim($c['dial'], '+') }}" {{ $c['iso'] === 'BH' ? 'selected' : '' }}>{{ $c['flag'] }} {{ $c['dial'] }}</option>
                        @endforeach
                    </select>
                    <input type="text" id="ssWhatsapp" inputmode="numeric" placeholder="{{ trans('labels.e_g_33001234') }}">
                </div>
                <div id="ssWaErr" style="display:none;color:#d64545;font-size:12.5px;margin-top:6px;">{{ trans('labels.whatsapp_number_is_required') }}</div>
            </div>
            <div class="ss-field">
                <label>{{ trans('labels.order_number_prefix_optional') }}</label>
                <input type="text" id="ssPrefix" maxlength="6" placeholder="{{ trans('labels.e_g_ord') }}">
                <div class="ss-hint">{{ trans('labels.orders_arrive_on_whatsapp_order_numbers_look') }}</div>
            </div>
            <button type="button" class="ss-btn" onclick="ssBuild()">
                <i class="fa-solid fa-wand-magic-sparkles"></i> {{ trans('labels.build_my_store_with_ai') }}
            </button>
            <div class="ss-err" id="ssErr"></div>
            <a href="{{ url('admin/dashboard') }}" class="ss-skip">{{ trans('labels.back_to_dashboard') }}</a>
        </div>

        {{-- ===== Step 2: working ===== --}}
        <div class="ss-working" id="ssWorking">
            <div class="ss-spinner"></div>
            <h2>{{ trans('labels.building_your_store') }}</h2>
            <div class="ss-progress" id="ssProgress">{{ trans('labels.getting_started') }}</div>
            <ul class="ss-steps" id="ssSteps">
                <li data-i="0"><i class="fa-regular fa-circle"></i> {{ trans('labels.reading_your_business_info') }}</li>
                <li data-i="1"><i class="fa-regular fa-circle"></i> {{ trans('labels.creating_categories') }}</li>
                <li data-i="2"><i class="fa-regular fa-circle"></i> {{ trans('labels.adding_products_services') }}</li>
                <li data-i="3"><i class="fa-regular fa-circle"></i> {{ trans('labels.writing_descriptions_colours') }}</li>
                <li data-i="4"><i class="fa-regular fa-circle"></i> {{ trans('labels.finishing_touches') }}</li>
            </ul>
            <p class="ss-note">{{ trans('labels.this_can_take_up_to_a_minute') }}</p>
        </div>
    </div>

    <script>
        var ssUrl = "{{ url('admin/store-setup/build') }}";
        var ssToken = document.querySelector('meta[name="csrf-token"]').content;
        var ssLang = "{{ trans('labels.english') }}";
        var ssMessages = @json($ssMessages);

        function ssBuild() {
            // WhatsApp number is required — always keep a country code with it.
            var waNum = document.getElementById('ssWhatsapp').value.replace(/[^0-9]/g, '');
            if (!waNum) {
                document.getElementById('ssWaErr').style.display = 'block';
                document.getElementById('ssWhatsapp').focus();
                return;
            }
            document.getElementById('ssWaErr').style.display = 'none';

            document.getElementById('ssForm').classList.add('off');
            document.getElementById('ssWorking').classList.add('on');

            // animate progress messages + step ticks while the request runs
            var steps = document.querySelectorAll('#ssSteps li');
            var mi = 0, si = 0;
            var prog = document.getElementById('ssProgress');
            var timer = setInterval(function () {
                mi = (mi + 1) % ssMessages.length;
                prog.style.opacity = 0;
                setTimeout(function () { prog.textContent = ssMessages[mi]; prog.style.opacity = 1; }, 200);
                if (si < steps.length) {
                    steps[si].classList.add('done');
                    steps[si].querySelector('i').className = 'fa-solid fa-circle-check';
                    si++;
                }
            }, 2200);

            fetch(ssUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': ssToken, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                body: JSON.stringify({
                    offerings: document.getElementById('ssOfferings').value,
                    categories: document.getElementById('ssCategories').value,
                    whatsapp: document.getElementById('ssWaCode').value + document.getElementById('ssWhatsapp').value.replace(/[^0-9]/g, ''),
                    prefix: document.getElementById('ssPrefix').value,
                    lang: ssLang
                })
            }).then(function (r) { return r.json().then(function (d) { return { ok: r.ok, d: d }; }); })
                .then(function (res) {
                    clearInterval(timer);
                    steps.forEach(function (s) { s.classList.add('done'); s.querySelector('i').className = 'fa-solid fa-circle-check'; });
                    if (res.ok && res.d.success) {
                        prog.textContent = "{{ trans('labels.done_opening_your_dashboard') }}";
                        setTimeout(function () { window.location.href = res.d.redirect || "{{ url('admin/dashboard') }}"; }, 900);
                    } else {
                        ssFail((res.d && res.d.error) ? res.d.error : 'Something went wrong.');
                    }
                }).catch(function () { clearInterval(timer); ssFail('Could not reach the AI service.'); });
        }

        function ssFail(msg) {
            document.getElementById('ssWorking').classList.remove('on');
            var f = document.getElementById('ssForm');
            f.classList.remove('off');
            var err = document.getElementById('ssErr');
            err.textContent = msg + ' ' + @json($ssRetry);
            err.style.display = 'block';
        }

        // ===== AI file upload: read a menu/product photo or PDF and pre-fill the form =====
        (function () {
            var drop = document.getElementById('ssDrop');
            if (!drop) return;
            var fileInput = document.getElementById('ssFile');
            var stateEl = document.getElementById('ssDropState');
            var okEl = document.getElementById('ssDropOk');
            var msgEl = document.getElementById('ssDropMsg');
            var extractUrl = "{{ url('admin/store-setup/extract') }}";
            @php $ssBusySteps = $ar ? ['جارٍ رفع الملف…', 'قراءة النص…', 'استخراج العناصر والأسعار…', 'اللمسات الأخيرة…'] : ['Uploading your file…', 'Reading the text…', 'Finding items & prices…', 'Almost done…']; @endphp
            var busySteps = @json($ssBusySteps);

            drop.addEventListener('click', function () { if (!drop.classList.contains('busy')) fileInput.click(); });
            ['dragover', 'dragenter'].forEach(function (e) { drop.addEventListener(e, function (ev) { ev.preventDefault(); drop.classList.add('drag'); }); });
            ['dragleave', 'dragend'].forEach(function (e) { drop.addEventListener(e, function (ev) { ev.preventDefault(); drop.classList.remove('drag'); }); });
            drop.addEventListener('drop', function (ev) { ev.preventDefault(); drop.classList.remove('drag'); if (ev.dataTransfer.files.length && !drop.classList.contains('busy')) handle(ev.dataTransfer.files[0]); });
            fileInput.addEventListener('change', function () { if (fileInput.files.length) handle(fileInput.files[0]); });

            function showMsg(t) { msgEl.textContent = t; msgEl.style.display = 'block'; okEl.style.display = 'none'; }

            function handle(file) {
                var okType = /^image\/(jpeg|png|webp)$/.test(file.type) || file.type === 'application/pdf' || /\.(jpe?g|png|webp|pdf)$/i.test(file.name);
                if (!okType) { showMsg("{{ trans('labels.please_upload_an_image_jpg_png_webp') }}"); return; }
                if (file.size > 10 * 1024 * 1024) { showMsg("{{ trans('labels.file_is_too_large_keep_it_under') }}"); return; }

                okEl.style.display = 'none'; msgEl.style.display = 'none';
                drop.classList.add('busy');
                var i = 0; stateEl.textContent = busySteps[0];
                var timer = setInterval(function () { i = (i + 1) % busySteps.length; stateEl.textContent = busySteps[i]; }, 1800);

                var fd = new FormData();
                fd.append('file', file);
                fd.append('lang', ssLang);

                fetch(extractUrl, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': ssToken, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                    body: fd
                }).then(function (r) { return r.json().then(function (d) { return { ok: r.ok, d: d }; }); })
                    .then(function (res) {
                        clearInterval(timer); drop.classList.remove('busy'); fileInput.value = '';
                        var d = res.d || {};
                        if (res.ok && d.success) {
                            document.getElementById('ssOfferings').value = d.offerings || '';
                            if (d.categories) document.getElementById('ssCategories').value = d.categories;
                            okEl.innerHTML = '<i class="fa-solid fa-circle-check"></i> ' + "{{ trans('labels.we_read_your_file_review_the_items') }}";
                            okEl.style.display = 'block';
                        } else {
                            // relevance/clarity/other errors — guide the merchant back to manual entry.
                            showMsg(d.error || "{{ trans('labels.could_not_read_that_file_try_a') }}");
                        }
                    }).catch(function () {
                        clearInterval(timer); drop.classList.remove('busy'); fileInput.value = '';
                        showMsg("{{ trans('labels.could_not_reach_the_ai_service_please') }}");
                    });
            }
        })();
    </script>
</body>

</html>
