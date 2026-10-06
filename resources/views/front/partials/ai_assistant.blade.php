{{--
    AI assistant inside the store page. Answers from THIS store's data only (StoreAssistantController),
    suggests items, sends the customer to the store's own cart / booking / service-request page and
    pre-fills that form with the details shared in the chat. It never creates orders itself.

    Optional: $ocaSide = 'left' | 'right' (physical side; default = the reading-direction end).
--}}
@php
    $ocaVid = $vdata ?? null;
    if (empty($ocaVid) && isset($storeinfo)) {
        $ocaVid = $storeinfo instanceof \App\Models\Settings ? $storeinfo->vendor_id : ($storeinfo->id ?? null);
    }
    $ocaVendor = $ocaVid ? \App\Models\User::find($ocaVid) : null;
    $ocaOn = $ocaVendor && (int) $ocaVendor->type === 2 && config('services.store_assistant.enabled') && \App\Services\AiAssistant::enabled();
@endphp
@if ($ocaOn)
    @php
        $ocaSettings = \App\Models\Settings::where('vendor_id', $ocaVid)->first();
        $ocaFlow = \App\Services\StoreKnowledge::flow($ocaVid);
        $ocaName = \App\Services\StoreKnowledge::businessName($ocaVid);
        $ocaBase = request()->segment(1) === $ocaVendor->slug ? url($ocaVendor->slug) : url('/');
        $ocaColor = optional($ocaSettings)->primary_color ?: '#1f9d55';
        $ocaSideCss = ($ocaSide ?? null) === 'left' ? 'left:20px;right:auto;' : (($ocaSide ?? null) === 'right' ? 'right:20px;left:auto;' : '');
        $ocaChips = [
            'orders'  => [trans('labels.assistant_chip_recommend'), trans('labels.assistant_chip_delivery'), trans('labels.assistant_chip_hours')],
            'booking' => [trans('labels.assistant_chip_book'), trans('labels.assistant_chip_prices'), trans('labels.assistant_chip_hours')],
            'service' => [trans('labels.assistant_chip_request'), trans('labels.assistant_chip_prices'), trans('labels.assistant_chip_areas')],
        ][$ocaFlow];
        $ocaCfg = [
            'endpoint' => $ocaBase . '/assistant',
            'csrf' => csrf_token(),
            'vid' => (int) $ocaVid,
            'greeting' => trans('labels.assistant_greeting_' . $ocaFlow, ['store' => $ocaName]),
            'chips' => $ocaChips,
            'unavailable' => trans('messages.assistant_unavailable'),
            'prefilled' => trans('labels.assistant_prefilled'),
            'view' => trans('labels.assistant_view'),
            'book' => trans('labels.assistant_book'),
        ];
    @endphp
    <style>
        #oca { --oca: var(--brand, {{ $ocaColor }}); position: fixed; z-index: 2147483000; bottom: 20px; inset-inline-end: 20px; {{ $ocaSideCss }}
            font-family: inherit; font-size: 14.5px; line-height: 1.45; color: #1d2a21; }
        #oca *, #oca *::before, #oca *::after { box-sizing: border-box; }
        #oca .oca-launch { width: 58px; height: 58px; border-radius: 50%; border: 0; background: var(--oca); color: #fff; cursor: pointer;
            display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 28px -8px rgba(0,0,0,.45); transition: transform .15s; }
        #oca .oca-launch:hover { transform: scale(1.06); }
        #oca .oca-launch svg { width: 27px; height: 27px; }
        #oca .oca-badge { position: absolute; top: -4px; inset-inline-end: -4px; background: #fff; color: var(--oca); font-size: 10px; font-weight: 800;
            border-radius: 20px; padding: 2px 6px; box-shadow: 0 2px 6px rgba(0,0,0,.2); letter-spacing: .3px; }
        #oca .oca-panel { position: absolute; bottom: 72px; inset-inline-end: 0; {{ $ocaSideCss ? (($ocaSide ?? null) === 'left' ? 'left:0;right:auto;' : 'right:0;left:auto;') : '' }}
            width: 370px; height: min(600px, calc(100vh - 110px)); background: #fff; border-radius: 18px; overflow: hidden;
            box-shadow: 0 24px 60px -18px rgba(0,0,0,.45); display: none; flex-direction: column; border: 1px solid rgba(0,0,0,.06); }
        #oca.is-open .oca-panel { display: flex; }
        #oca .oca-head { background: var(--oca); color: #fff; padding: 14px 14px 14px 16px; display: flex; align-items: center; gap: 10px; }
        #oca .oca-avatar { width: 38px; height: 38px; border-radius: 50%; background: rgba(255,255,255,.22); display: flex; align-items: center;
            justify-content: center; font-weight: 700; flex: none; }
        #oca .oca-title { font-weight: 700; font-size: 15px; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        #oca .oca-sub { font-size: 12px; opacity: .85; margin: 0; }
        #oca .oca-hbtn { margin-inline-start: auto; background: rgba(255,255,255,.18); border: 0; color: #fff; border-radius: 9px;
            height: 32px; min-width: 32px; padding: 0 9px; cursor: pointer; font-size: 12px; display: flex; align-items: center; gap: 5px; }
        #oca .oca-hbtn + .oca-hbtn { margin-inline-start: 6px; }
        #oca .oca-body { flex: 1; overflow-y: auto; padding: 16px 14px; background: #f6f7f5; display: flex; flex-direction: column; gap: 10px; }
        #oca .oca-body > * { flex-shrink: 0; }
        #oca .oca-msg { max-width: 86%; padding: 9px 13px; border-radius: 15px; white-space: pre-wrap; word-wrap: break-word; }
        #oca .oca-msg.bot { background: #fff; border: 1px solid #e7ebe5; border-end-start-radius: 4px; align-self: flex-start; }
        #oca .oca-msg.me { background: var(--oca); color: #fff; border-end-end-radius: 4px; align-self: flex-end; }
        #oca .oca-cards { display: flex; gap: 8px; overflow-x: auto; padding-bottom: 2px; align-self: stretch; }
        #oca .oca-card { flex: 0 0 150px; background: #fff; border: 1px solid #e7ebe5; border-radius: 13px; overflow: hidden; text-decoration: none;
            color: inherit; display: flex; flex-direction: column; }
        #oca .oca-card img, #oca .oca-card .oca-ph { width: 100%; height: 86px; object-fit: cover; background: #eef1ec; display: flex;
            align-items: center; justify-content: center; font-size: 26px; font-weight: 700; color: var(--oca); }
        #oca .oca-card b { font-size: 13px; padding: 7px 9px 0; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        #oca .oca-card small { font-size: 11.5px; color: #6b7669; padding: 2px 9px 0; }
        #oca .oca-card span { margin: auto 9px 9px; padding-top: 6px; display: flex; justify-content: space-between; align-items: center; font-size: 12.5px; font-weight: 700; }
        #oca .oca-card span i { font-style: normal; color: var(--oca); }
        #oca .oca-action { align-self: flex-start; background: var(--oca); color: #fff !important; text-decoration: none; border-radius: 22px;
            padding: 8px 16px; font-weight: 700; font-size: 13.5px; }
        #oca .oca-note { align-self: center; font-size: 12px; color: #2f7a4a; background: #e8f5ec; border-radius: 10px; padding: 6px 10px; text-align: center; }
        #oca .oca-chips { display: flex; flex-wrap: wrap; gap: 6px; }
        #oca .oca-chip { border: 1px solid var(--oca); color: var(--oca); background: #fff; border-radius: 20px; padding: 5px 11px; font-size: 12.5px; cursor: pointer; }
        #oca .oca-typing { align-self: flex-start; background: #fff; border: 1px solid #e7ebe5; border-radius: 15px; padding: 11px 14px; display: flex; gap: 4px; }
        #oca .oca-typing i { width: 7px; height: 7px; border-radius: 50%; background: #9aa59c; animation: ocaDot 1.2s infinite; }
        #oca .oca-typing i:nth-child(2) { animation-delay: .2s; } #oca .oca-typing i:nth-child(3) { animation-delay: .4s; }
        @keyframes ocaDot { 0%, 60%, 100% { opacity: .3; transform: translateY(0); } 30% { opacity: 1; transform: translateY(-3px); } }
        #oca .oca-foot { border-top: 1px solid #eceee9; padding: 10px; background: #fff; }
        #oca .oca-form { display: flex; gap: 8px; }
        #oca .oca-input { flex: 1; border: 1px solid #dfe4dc; border-radius: 22px; padding: 10px 15px; font: inherit; outline: none; min-width: 0; resize: none; height: 42px; }
        #oca .oca-input:focus { border-color: var(--oca); }
        #oca .oca-send { width: 42px; height: 42px; border-radius: 50%; border: 0; background: var(--oca); color: #fff; cursor: pointer; flex: none;
            display: flex; align-items: center; justify-content: center; }
        #oca .oca-send:disabled { opacity: .5; cursor: default; }
        #oca .oca-legal { font-size: 10.5px; color: #8a968c; text-align: center; margin: 7px 0 0; }
        [dir="rtl"] #oca .oca-send svg { transform: scaleX(-1); }
        @media (max-width: 520px) {
            #oca { bottom: 16px; }
            #oca .oca-panel { position: fixed; inset: 10px; width: auto; height: auto; bottom: 10px; }
            #oca.is-open .oca-launch { display: none; }
        }
    </style>

    <div id="oca" role="complementary">
        <button type="button" class="oca-launch" aria-label="{{ trans('labels.assistant_open') }}" aria-expanded="false">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M21 12a8 8 0 0 1-11.6 7.1L4 20.5l1.4-4.9A8 8 0 1 1 21 12z"/><path d="M9 11h.01M12 11h.01M15 11h.01"/>
            </svg>
            <span class="oca-badge">AI</span>
        </button>
        <div class="oca-panel" role="dialog" aria-label="{{ $ocaName }}">
            <div class="oca-head">
                <div class="oca-avatar">{{ mb_strtoupper(mb_substr($ocaName, 0, 1)) }}</div>
                <div style="min-width:0">
                    <p class="oca-title">{{ $ocaName }}</p>
                    <p class="oca-sub">{{ trans('labels.assistant_subtitle') }}</p>
                </div>
                <button type="button" class="oca-hbtn" data-oca-reset title="{{ trans('labels.assistant_new_chat') }}" aria-label="{{ trans('labels.assistant_new_chat') }}">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M3 12a9 9 0 1 0 3-6.7L3 8"/><path d="M3 3v5h5"/></svg>
                </button>
                <button type="button" class="oca-hbtn" data-oca-close aria-label="{{ trans('labels.assistant_close') }}">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M6 6l12 12M18 6L6 18"/></svg>
                </button>
            </div>
            <div class="oca-body" aria-live="polite"></div>
            <div class="oca-foot">
                <form class="oca-form">
                    <input class="oca-input" type="text" maxlength="600" autocomplete="off" placeholder="{{ trans('labels.assistant_placeholder') }}"
                        aria-label="{{ trans('labels.assistant_placeholder') }}">
                    <button class="oca-send" type="submit" aria-label="{{ trans('labels.assistant_send') }}">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 2L11 13"/><path d="M22 2l-7 20-4-9-9-4 20-7z"/></svg>
                    </button>
                </form>
                <p class="oca-legal">{{ trans('labels.assistant_disclaimer') }}</p>
            </div>
        </div>
    </div>

    <script>
        (function () {
            var cfg = @json($ocaCfg);
            var root = document.getElementById('oca');
            var body = root.querySelector('.oca-body');
            var input = root.querySelector('.oca-input');
            var sendBtn = root.querySelector('.oca-send');
            var launch = root.querySelector('.oca-launch');
            var chatKey = 'oca_chat_' + cfg.vid, custKey = 'oca_customer_' + cfg.vid;

            // Browser storage can be blocked (private mode); the chat still works without it.
            function load(store, key, fallback) { try { return JSON.parse(store.getItem(key)) || fallback; } catch (e) { return fallback; } }
            function save(store, key, value) { try { store.setItem(key, JSON.stringify(value)); } catch (e) {} }

            var state = load(sessionStorage, chatKey, { open: false, messages: [] });
            var customer = load(localStorage, custKey, {});
            var busy = false;

            function el(tag, cls, text) { var n = document.createElement(tag); if (cls) n.className = cls; if (text != null) n.textContent = text; return n; }

            function renderMessage(m) {
                var bubble = el('div', 'oca-msg ' + (m.role === 'user' ? 'me' : 'bot'), m.text);
                bubble.dir = 'auto'; // an English message on an Arabic page keeps its own direction
                body.appendChild(bubble);
                if (m.items && m.items.length) {
                    var row = el('div', 'oca-cards');
                    m.items.forEach(function (it) {
                        var a = el('a', 'oca-card'); a.href = it.url;
                        if (it.image) { var img = el('img'); img.src = it.image; img.alt = ''; img.loading = 'lazy'; a.appendChild(img); }
                        else { a.appendChild(el('div', 'oca-ph', (it.name || '?').charAt(0).toUpperCase())); }
                        a.appendChild(el('b', null, it.name));
                        if (it.note) a.appendChild(el('small', null, it.note));
                        var foot = el('span'); foot.appendChild(el('em', null, it.price || ''));
                        foot.firstChild.style.fontStyle = 'normal';
                        foot.appendChild(el('i', null, it.type === 'product' ? cfg.view : cfg.book));
                        a.appendChild(foot); row.appendChild(a);
                    });
                    body.appendChild(row);
                }
                if (m.action && m.action.url) {
                    var btn = el('a', 'oca-action', m.action.label); btn.href = m.action.url; body.appendChild(btn);
                }
            }

            function render() {
                body.innerHTML = '';
                renderMessage({ role: 'assistant', text: cfg.greeting });
                if (!state.messages.length) {
                    var chips = el('div', 'oca-chips');
                    cfg.chips.forEach(function (c) {
                        var b = el('button', 'oca-chip', c); b.type = 'button';
                        b.addEventListener('click', function () { send(c); });
                        chips.appendChild(b);
                    });
                    body.appendChild(chips);
                }
                state.messages.forEach(renderMessage);
                body.scrollTop = body.scrollHeight;
            }

            function setOpen(open) {
                state.open = open; save(sessionStorage, chatKey, state);
                root.classList.toggle('is-open', open);
                launch.setAttribute('aria-expanded', open ? 'true' : 'false');
                if (open) { render(); setTimeout(function () { input.focus(); }, 50); }
            }

            function send(text) {
                text = (text || '').trim();
                if (!text || busy) return;
                busy = true; sendBtn.disabled = true; input.value = '';
                var history = state.messages.slice(-12).map(function (m) { return { role: m.role, text: m.text }; });
                state.messages.push({ role: 'user', text: text }); save(sessionStorage, chatKey, state); render();
                var typing = el('div', 'oca-typing'); typing.innerHTML = '<i></i><i></i><i></i>'; body.appendChild(typing); body.scrollTop = body.scrollHeight;

                fetch(cfg.endpoint, {
                    method: 'POST', credentials: 'same-origin',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': cfg.csrf, 'X-Requested-With': 'XMLHttpRequest' },
                    body: JSON.stringify({ message: text, history: history, customer: customer })
                }).then(function (r) { return r.json(); }).then(function (res) {
                    state.messages.push({ role: 'assistant', text: res.reply || cfg.unavailable, items: res.items || [], action: res.action || null });
                    if (res.customer && typeof res.customer === 'object') {
                        customer = Object.assign({}, customer, res.customer); save(localStorage, custKey, customer);
                        prefill();
                    }
                }).catch(function () {
                    state.messages.push({ role: 'assistant', text: cfg.unavailable });
                }).then(function () {
                    busy = false; sendBtn.disabled = false;
                    save(sessionStorage, chatKey, state); render(); input.focus();
                });
            }

            // Fill the store's own order / booking / service form with what the customer told the
            // assistant — only empty fields, and only inside the form that holds customer_name.
            function prefill() {
                var map = { name: ['customer_name'], phone: ['customer_mobile', 'mobile'], email: ['customer_email', 'email'], address: ['address'], details: ['notes'] };
                var filled = false;
                document.querySelectorAll('[name="customer_name"]').forEach(function (anchor) {
                    var scope = anchor.closest('form') || document;
                    Object.keys(map).forEach(function (key) {
                        if (!customer[key]) return;
                        map[key].forEach(function (name) {
                            var field = scope.querySelector('[name="' + name + '"]');
                            if (field && !field.value && !field.readOnly && !field.disabled) {
                                field.value = customer[key];
                                field.dispatchEvent(new Event('input', { bubbles: true }));
                                field.dispatchEvent(new Event('change', { bubbles: true }));
                                filled = true;
                            }
                        });
                    });
                });
                if (filled && state.messages.length) {
                    var note = el('div', 'oca-note', cfg.prefilled);
                    if (root.classList.contains('is-open')) { body.appendChild(note); body.scrollTop = body.scrollHeight; }
                }
            }

            launch.addEventListener('click', function () { setOpen(!root.classList.contains('is-open')); });
            root.querySelector('[data-oca-close]').addEventListener('click', function () { setOpen(false); });
            root.querySelector('[data-oca-reset]').addEventListener('click', function () { state.messages = []; save(sessionStorage, chatKey, state); render(); });
            root.querySelector('.oca-form').addEventListener('submit', function (e) { e.preventDefault(); send(input.value); });
            document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && root.classList.contains('is-open')) setOpen(false); });

            if (state.open) setOpen(true);
            if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', prefill); else prefill();
        })();
    </script>
@endif
