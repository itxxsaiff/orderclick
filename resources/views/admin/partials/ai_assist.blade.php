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
            <button type="button" data-action="improve"><i class="fa-solid fa-wand-magic-sparkles"></i> {{ app()->getLocale() === 'ar' ? 'تحسين النص' : 'Improve' }}</button>
            <button type="button" data-action="grammar"><i class="fa-solid fa-spell-check"></i> {{ app()->getLocale() === 'ar' ? 'تصحيح الإملاء' : 'Fix grammar' }}</button>
            <button type="button" data-action="professional"><i class="fa-solid fa-briefcase"></i> {{ app()->getLocale() === 'ar' ? 'صياغة احترافية' : 'Make professional' }}</button>
            <button type="button" data-action="seo"><i class="fa-solid fa-magnifying-glass-chart"></i> {{ app()->getLocale() === 'ar' ? 'مناسب لمحركات البحث' : 'SEO-friendly' }}</button>
            <div class="oc-ai-sep"></div>
            <button type="button" data-action="translate" data-lang="English"><i class="fa-solid fa-language"></i> {{ app()->getLocale() === 'ar' ? 'ترجمة إلى الإنجليزية' : 'Translate → English' }}</button>
            <button type="button" data-action="translate" data-lang="Arabic"><i class="fa-solid fa-language"></i> {{ app()->getLocale() === 'ar' ? 'ترجمة إلى العربية' : 'Translate → Arabic' }}</button>
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
            .oc-ai-menu { position: absolute; top: 36px; z-index: 1080; min-width: 210px; background: #fff; border: 1px solid #ece7fb;
                border-radius: 12px; box-shadow: 0 20px 44px -22px rgba(40,20,90,.4); padding: 6px; display: none; }
            .oc-ai-menu.show { display: block; }
            html[dir="rtl"] .oc-ai-menu { right: 0; } html:not([dir="rtl"]) .oc-ai-menu { left: 0; }
            .oc-ai-menu button { display: flex; align-items: center; gap: 9px; width: 100%; background: transparent; border: 0; text-align: start;
                padding: 9px 11px; border-radius: 8px; font-size: 13px; color: #2c2440; cursor: pointer; }
            .oc-ai-menu button:hover { background: #f4f0ff; color: #6b46e5; }
            .oc-ai-menu button i { width: 16px; color: #8b78e8; }
            .oc-ai-sep { height: 1px; background: #f0ecfb; margin: 5px 4px; }
        </style>
        <script>
            var ocAiUrl = "{{ URL::to('admin/ai/assist') }}";
            var ocAiToken = "{{ csrf_token() }}";
            function ocAiToggle(e, btn) {
                e.preventDefault();
                document.querySelectorAll('.oc-ai-menu.show').forEach(function (m) { if (m !== btn.parentNode.querySelector('.oc-ai-menu')) m.classList.remove('show'); });
                btn.parentNode.querySelector('.oc-ai-menu').classList.toggle('show');
            }
            document.addEventListener('click', function (e) {
                if (!e.target.closest('.oc-ai')) document.querySelectorAll('.oc-ai-menu.show').forEach(function (m) { m.classList.remove('show'); });
            });
            document.addEventListener('click', function (e) {
                var opt = e.target.closest('.oc-ai-menu button');
                if (!opt) return;
                var wrap = opt.closest('.oc-ai');
                var field = wrap.querySelector('.oc-ai-menu');
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
