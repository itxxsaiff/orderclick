{{--
    Reusable AI content-assist widget.
    Usage:  @include('admin.partials.ai_assist', ['target' => '#item_description', 'field' => 'product description'])
    - target : CSS selector of the input/textarea to read & fill
    - field  : human label passed to the AI for context (e.g. "product name")
--}}
@if (\App\Services\AiAssistant::enabled())
    <span class="oc-ai" data-target="{{ $target }}" data-field="{{ $field ?? 'content' }}">
        <button type="button" class="oc-ai-btn" onclick="ocAiToggle(event, this)">
            <i class="fa-solid fa-wand-magic-sparkles"></i> AI
            <i class="fa-solid fa-chevron-down oc-ai-caret"></i>
        </button>
        <span class="oc-ai-load" style="display:none;"><i class="fa-solid fa-spinner fa-spin"></i></span>
        <div class="oc-ai-menu">
            <button type="button" data-action="improve"><i class="fa-solid fa-wand-magic-sparkles"></i> {{ trans('labels.improve') }}</button>
            <button type="button" data-action="grammar"><i class="fa-solid fa-spell-check"></i> {{ trans('labels.fix_grammar') }}</button>
            <button type="button" data-action="professional"><i class="fa-solid fa-briefcase"></i> {{ trans('labels.make_professional') }}</button>
            <button type="button" data-action="seo"><i class="fa-solid fa-magnifying-glass"></i> {{ trans('labels.seo_friendly') }}</button>
            <div class="oc-ai-sep"></div>
            <button type="button" data-action="translate" data-lang="English"><i class="fa-solid fa-language"></i> {{ trans('labels.translate_english') }}</button>
            <button type="button" data-action="translate" data-lang="Arabic"><i class="fa-solid fa-language"></i> {{ trans('labels.translate_arabic') }}</button>
        </div>
    </span>
@endif

@once
    @if (\App\Services\AiAssistant::enabled())
        <style>
            .oc-ai { position: relative; display: inline-flex; align-items: center; gap: 6px; vertical-align: middle; }
            .oc-ai-btn { display: inline-flex; align-items: center; gap: 6px; height: 30px; padding: 0 11px; border: 1px solid #d9d0f5; border-radius: 8px;
                background: linear-gradient(135deg, #efeafe, #f6f2ff); color: #6b46e5; font-size: 12.5px; font-weight: 600; cursor: pointer; transition: .15s; }
            .oc-ai-btn:hover { border-color: #6b46e5; box-shadow: 0 6px 14px -8px rgba(107,70,229,.6); }
            .oc-ai-caret { font-size: 9px; opacity: .7; }
            .oc-ai-load { color: #6b46e5; font-size: 14px; }
            /* Opened on <body> and placed next to the button (ocAiPlace), so a card's overflow:hidden
               can never cut it off at the edge. */
            .oc-ai-menu { position: fixed; top: 0; left: 0; z-index: 2000; min-width: 210px; background: #fff; border: 1px solid #ece7fb;
                border-radius: 12px; box-shadow: 0 20px 44px -22px rgba(40,20,90,.4); padding: 6px; display: none; }
            .oc-ai-menu.show { display: block; }
            .oc-ai-menu button { display: flex; align-items: center; gap: 9px; width: 100%; background: transparent; border: 0; text-align: start;
                padding: 9px 11px; border-radius: 8px; font-size: 13px; color: #2c2440; cursor: pointer; }
            .oc-ai-menu button:hover { background: #f4f0ff; color: #6b46e5; }
            .oc-ai-menu button i { width: 16px; color: #8b78e8; }
            .oc-ai-sep { height: 1px; background: #f0ecfb; margin: 5px 4px; }
        </style>
        <script>
            var ocAiUrl = "{{ URL::to('admin/ai/assist') }}";
            var ocAiToken = "{{ csrf_token() }}";
            function ocAiCloseAll() {
                document.querySelectorAll('.oc-ai-menu.show').forEach(function (m) { m.classList.remove('show'); });
            }
            // The menu belongs to its widget even after it has been moved to <body>.
            function ocAiMenuOf(wrap) {
                if (!wrap._ocMenu) {
                    wrap._ocMenu = wrap.querySelector('.oc-ai-menu');
                    wrap._ocMenu._ocWrap = wrap;
                }
                return wrap._ocMenu;
            }
            // Line the menu up with the button's end edge, keep it on screen, flip above if needed.
            function ocAiPlace(menu, btn) {
                var r = btn.getBoundingClientRect();
                var vw = document.documentElement.clientWidth, vh = window.innerHeight;
                var w = menu.offsetWidth, h = menu.offsetHeight;
                var left = document.documentElement.dir === 'rtl' ? r.left : r.right - w;
                var top = r.bottom + 6;
                if (top + h > vh - 8 && r.top - h - 6 > 8) top = r.top - h - 6;
                menu.style.left = Math.max(8, Math.min(left, vw - w - 8)) + 'px';
                menu.style.top = Math.max(8, top) + 'px';
            }
            function ocAiToggle(e, btn) {
                e.preventDefault();
                var menu = ocAiMenuOf(btn.closest('.oc-ai'));
                var open = !menu.classList.contains('show');
                ocAiCloseAll();
                if (open) {
                    document.body.appendChild(menu);
                    menu._ocBtn = btn;
                    menu.classList.add('show');
                    ocAiPlace(menu, btn);
                }
            }
            document.addEventListener('click', function (e) {
                if (!e.target.closest('.oc-ai') && !e.target.closest('.oc-ai-menu')) ocAiCloseAll();
            });
            // Keep an open menu next to its button while the page scrolls; close it once the button is gone.
            function ocAiFollow() {
                document.querySelectorAll('.oc-ai-menu.show').forEach(function (m) {
                    var r = m._ocBtn ? m._ocBtn.getBoundingClientRect() : null;
                    if (!r || r.bottom < 0 || r.top > window.innerHeight) m.classList.remove('show');
                    else ocAiPlace(m, m._ocBtn);
                });
            }
            window.addEventListener('scroll', ocAiFollow, true);
            window.addEventListener('resize', ocAiFollow);
            document.addEventListener('click', function (e) {
                var opt = e.target.closest('.oc-ai-menu button');
                if (!opt) return;
                var field = opt.closest('.oc-ai-menu');
                var wrap = field._ocWrap || opt.closest('.oc-ai');
                if (!wrap) return;
                var target = document.querySelector(wrap.getAttribute('data-target'));
                if (!target) return;
                var action = opt.getAttribute('data-action');
                var lang = opt.getAttribute('data-lang') || 'English';
                var text = (target.value || '').trim();
                field.classList.remove('show');

                var load = wrap.querySelector('.oc-ai-load');
                var btn = wrap.querySelector('.oc-ai-btn');
                load.style.display = 'inline';
                btn.style.opacity = '.5';
                target.setAttribute('readonly', 'readonly');

                fetch(ocAiUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': ocAiToken, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                    body: JSON.stringify({ action: action, text: text, field: wrap.getAttribute('data-field'), target_lang: lang })
                }).then(function (r) { return r.json().then(function (d) { return { ok: r.ok, d: d }; }); })
                    .then(function (res) {
                        if (res.ok && res.d.success) {
                            target.value = res.d.text;
                            target.dispatchEvent(new Event('input', { bubbles: true }));
                            target.dispatchEvent(new Event('change', { bubbles: true }));
                        } else {
                            alert((res.d && res.d.error) ? res.d.error : 'AI request failed.');
                        }
                    }).catch(function () { alert('Could not reach the AI service.'); })
                    .finally(function () {
                        load.style.display = 'none';
                        btn.style.opacity = '1';
                        target.removeAttribute('readonly');
                    });
            });
        </script>
    @endif
@endonce
