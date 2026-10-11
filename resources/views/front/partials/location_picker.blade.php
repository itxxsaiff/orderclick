{{-- Customer delivery pin for checkout: "Use my current location" or drag the pin. The pin is sent
     to /checkout-location (kept in the session) and attached to the order when it is created;
     the address field is filled from the map when the customer left it empty. --}}
@php
    $ocBranch = \App\Models\VendorBranch::where('vendor_id', $vdata)->where('is_primary', 1)->first();
    $ocCenter = [(float) (optional($ocBranch)->latitude ?: 26.2235), (float) (optional($ocBranch)->longitude ?: 50.5876)];
    $ocSaved = session('oc_checkout_location_' . $vdata);
    $ocLocCfg = [
        'url' => URL::to(($storeinfo->slug ?? '') . '/checkout-location'),
        'token' => csrf_token(),
        'center' => $ocCenter,
        'saved' => $ocSaved,
        'saving' => trans('labels.location_saving'),
        'saved_text' => trans('labels.location_saved'),
        'denied' => trans('labels.location_denied'),
        'lang' => app()->getLocale(),
    ];
@endphp
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css">
<style>
    .oc-loc { margin: 4px 0 14px; }
    .oc-loc__bar { display: flex; flex-wrap: wrap; align-items: center; gap: 10px; margin-bottom: 8px; }
    .oc-loc__btn { display: inline-flex; align-items: center; gap: 8px; border: 1.5px solid var(--brand, #1f9d55); color: var(--brand, #1f9d55);
        background: transparent; border-radius: var(--r-btn, 999px); padding: 9px 16px; font-weight: 700; font-size: .9rem; cursor: pointer; }
    .oc-loc__btn:hover { background: var(--brand, #1f9d55); color: var(--on-brand, #fff); }
    .oc-loc__status { font-size: .85rem; color: var(--text-2, #6b7669); }
    .oc-loc__status.ok { color: #1a8a4a; font-weight: 600; }
    .oc-loc__map { height: 230px; border-radius: var(--r, 14px); border: 1px solid var(--line-strong, #ddd); overflow: hidden; z-index: 0; }
</style>
<div class="oc-loc">
    <label class="form-label">{{ trans('labels.delivery_location') }}</label>
    <div class="oc-loc__bar">
        <button type="button" class="oc-loc__btn" id="ocLocGps">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><circle cx="12" cy="12" r="3"/><path d="M12 2v3M12 19v3M2 12h3M19 12h3"/><circle cx="12" cy="12" r="8"/></svg>
            {{ trans('labels.use_my_location') }}
        </button>
        <span class="oc-loc__status {{ $ocSaved ? 'ok' : '' }}" id="ocLocStatus">
            {{ $ocSaved ? trans('labels.location_saved') : trans('labels.location_hint') }}
        </span>
    </div>
    <div class="oc-loc__map" id="ocLocMap"></div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
<script>
    (function () {
        if (!window.L) return;
        var cfg = @json($ocLocCfg);
        var start = cfg.saved ? [cfg.saved.lat, cfg.saved.lng] : cfg.center;
        var map = L.map('ocLocMap', { scrollWheelZoom: false }).setView(start, cfg.saved ? 16 : 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '&copy; OpenStreetMap' }).addTo(map);
        var marker = cfg.saved ? L.marker(start, { draggable: true }).addTo(map) : null;
        var status = document.getElementById('ocLocStatus');

        function save(lat, lng) {
            if (!marker) {
                marker = L.marker([lat, lng], { draggable: true }).addTo(map);
                marker.on('dragend', function () { var p = marker.getLatLng(); save(p.lat, p.lng); });
            } else {
                marker.setLatLng([lat, lng]);
            }
            status.textContent = cfg.saving; status.classList.remove('ok');
            var body = new FormData(); body.append('lat', lat); body.append('lng', lng);
            fetch(cfg.url, { method: 'POST', body: body, credentials: 'same-origin',
                headers: { 'X-CSRF-TOKEN': cfg.token, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                .then(function (r) { return r.json(); })
                .then(function (res) { if (res.success) { status.textContent = cfg.saved_text; status.classList.add('ok'); } });
            // Fill the written address from the map if the customer has not typed one.
            var addr = document.getElementById('address');
            if (addr && !addr.value) {
                fetch('https://nominatim.openstreetmap.org/reverse?format=json&lat=' + lat + '&lon=' + lng + '&accept-language=' + cfg.lang)
                    .then(function (r) { return r.json(); })
                    .then(function (g) { if (g && g.display_name && !addr.value) { addr.value = g.display_name; } })
                    .catch(function () {});
            }
        }
        if (marker) marker.on('dragend', function () { var p = marker.getLatLng(); save(p.lat, p.lng); });
        map.on('click', function (e) { save(e.latlng.lat, e.latlng.lng); });

        document.getElementById('ocLocGps').addEventListener('click', function () {
            if (!navigator.geolocation) { status.textContent = cfg.denied; return; }
            status.textContent = cfg.saving;
            navigator.geolocation.getCurrentPosition(function (pos) {
                map.setView([pos.coords.latitude, pos.coords.longitude], 17);
                save(pos.coords.latitude, pos.coords.longitude);
            }, function () { status.textContent = cfg.denied; }, { enableHighAccuracy: true, timeout: 12000 });
        });
        // The panel can start hidden (pickup selected); redraw the map when it becomes visible.
        if (window.ResizeObserver) {
            new ResizeObserver(function () { map.invalidateSize(); }).observe(document.getElementById('ocLocMap'));
        }
    })();
</script>
