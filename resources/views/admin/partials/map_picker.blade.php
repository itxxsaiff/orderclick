{{-- Shared location picker.
     Three ways in — Use current GPS, click/drag the pin on the map, or search an address — and all
     three reverse-geocode to fill country / city / area / written address / lat / lng automatically.
     No admin ever types a city or an area.

     Stack: Leaflet (map + draggable marker) over OpenStreetMap tiles, with Nominatim for forward
     and reverse geocoding. Both are free and need no API key or billing account.

     Expects the form to contain inputs named: country, city, area, address, latitude, longitude,
     geo_source. Pass $lat / $lng to pre-position the pin. --}}

@once
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
@endonce

@php
    $ocAr = app()->getLocale() === 'ar';
    $ocLat = $lat ?? null;
    $ocLng = $lng ?? null;
@endphp

<div class="row g-3">
    <div class="col-12">
        <div class="d-flex flex-wrap gap-2 mb-2">
            <button type="button" class="btn btn-sm btn-outline-success" id="ocGpsBtn">
                <i class="fa-solid fa-location-crosshairs mx-1"></i>{{ trans('labels.use_current_gps') }}
            </button>
            <div class="input-group input-group-sm" style="max-width:420px">
                <input type="text" class="form-control" id="ocGeoSearch"
                    placeholder="{{ trans('labels.search_address_placeholder') }}">
                <button type="button" class="btn btn-outline-secondary" id="ocGeoSearchBtn">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </div>
            <span class="small text-muted align-self-center" id="ocGeoStatus">{{ trans('labels.map_pin_hint') }}</span>
        </div>
        <div id="ocMap" style="height:320px;border:1px solid #d9e0d4;border-radius:12px;"></div>
        <div id="ocGeoResults" class="list-group mt-2" style="display:none;max-height:200px;overflow:auto;"></div>
    </div>

    {{-- Auto-filled. Left editable so an admin can correct a wrong reverse-geocode. --}}
    <div class="form-group col-md-4">
        <label class="form-label">{{ trans('labels.country') }}</label>
        <input type="text" class="form-control" name="country" id="ocCountry" value="{{ old('country', $branch->country ?? '') }}" readonly>
    </div>
    <div class="form-group col-md-4">
        <label class="form-label">{{ trans('labels.city') }}</label>
        <input type="text" class="form-control" name="city" id="ocCity" value="{{ old('city', $branch->city ?? '') }}" readonly>
    </div>
    <div class="form-group col-md-4">
        <label class="form-label">{{ trans('labels.area') }}</label>
        <input type="text" class="form-control" name="area" id="ocArea" value="{{ old('area', $branch->area ?? '') }}" readonly>
    </div>
    <div class="form-group col-12">
        <label class="form-label">{{ trans('labels.written_address') }}</label>
        <input type="text" class="form-control" name="address" id="ocAddress" value="{{ old('address', $branch->address ?? '') }}">
    </div>
    <div class="form-group col-md-6">
        <label class="form-label">{{ trans('labels.latitude') }}</label>
        <input type="text" class="form-control" name="latitude" id="ocLat" value="{{ old('latitude', $ocLat) }}" readonly>
    </div>
    <div class="form-group col-md-6">
        <label class="form-label">{{ trans('labels.longitude') }}</label>
        <input type="text" class="form-control" name="longitude" id="ocLng" value="{{ old('longitude', $ocLng) }}" readonly>
    </div>
    <input type="hidden" name="geo_source" id="ocGeoSource" value="{{ old('geo_source', $branch->geo_source ?? '') }}">
</div>

<script>
    (function () {
        var startLat = {{ $ocLat ?: 26.2285 }};   // default view: Bahrain
        var startLng = {{ $ocLng ?: 50.5860 }};
        var hasPin = {{ $ocLat && $ocLng ? 'true' : 'false' }};
        var lang = '{{ app()->getLocale() === "ar" ? "ar" : "en" }}';

        var map = L.map('ocMap').setView([startLat, startLng], hasPin ? 15 : 11);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        var marker = null;
        var status = document.getElementById('ocGeoStatus');

        function setStatus(text, cls) {
            status.textContent = text;
            status.className = 'small align-self-center ' + (cls || 'text-muted');
        }

        // Drop or move the pin, write the coordinates, then look the address up.
        function placePin(lat, lng, source, skipLookup) {
            lat = parseFloat(lat); lng = parseFloat(lng);
            if (isNaN(lat) || isNaN(lng)) return;

            if (marker) {
                marker.setLatLng([lat, lng]);
            } else {
                marker = L.marker([lat, lng], { draggable: true }).addTo(map);
                marker.on('dragend', function (e) {
                    var p = e.target.getLatLng();
                    placePin(p.lat, p.lng, 'map');
                });
            }

            document.getElementById('ocLat').value = lat.toFixed(6);
            document.getElementById('ocLng').value = lng.toFixed(6);
            document.getElementById('ocGeoSource').value = source || 'map';

            if (!skipLookup) reverseGeocode(lat, lng);
        }

        // Coordinates -> country / city / area / written address.
        function reverseGeocode(lat, lng) {
            setStatus('{{ $ocAr ? "جارٍ قراءة العنوان…" : "Reading address…" }}');

            fetch('https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=' + lat + '&lon=' + lng + '&accept-language=' + lang, {
                headers: { 'Accept': 'application/json' }
            })
                .then(function (r) { return r.json(); })
                .then(function (g) {
                    var a = g.address || {};
                    var city = a.city || a.town || a.village || a.municipality || a.state_district || a.county || '';
                    var area = a.suburb || a.neighbourhood || a.city_district || a.quarter || a.residential || '';

                    document.getElementById('ocCountry').value = a.country || '';
                    document.getElementById('ocCity').value = city;
                    document.getElementById('ocArea').value = area;

                    var addr = document.getElementById('ocAddress');
                    if (g.display_name) addr.value = g.display_name;

                    setStatus('{{ $ocAr ? "✓ تم تعبئة العنوان تلقائياً" : "✓ Address filled automatically" }}', 'text-success fw-600');
                })
                .catch(function () {
                    setStatus('{{ $ocAr ? "تعذّر قراءة العنوان — يمكنك كتابته يدوياً" : "Could not read the address — you can type it in" }}', 'text-danger');
                });
        }

        // Click anywhere on the map to move the pin.
        map.on('click', function (e) { placePin(e.latlng.lat, e.latlng.lng, 'map'); });

        if (hasPin) placePin(startLat, startLng, document.getElementById('ocGeoSource').value || 'manual', true);

        // ---- Use current GPS location ----
        document.getElementById('ocGpsBtn').addEventListener('click', function () {
            if (!navigator.geolocation) {
                setStatus('{{ $ocAr ? "الموقع غير مدعوم على هذا الجهاز" : "GPS is not supported on this device" }}', 'text-danger');
                return;
            }
            setStatus('{{ $ocAr ? "جارٍ تحديد موقعك…" : "Detecting your location…" }}');
            navigator.geolocation.getCurrentPosition(function (p) {
                map.setView([p.coords.latitude, p.coords.longitude], 16);
                placePin(p.coords.latitude, p.coords.longitude, 'gps');
            }, function (e) {
                setStatus(e.code === 1
                    ? '{{ $ocAr ? "تم رفض إذن الموقع" : "Location permission denied" }}'
                    : '{{ $ocAr ? "تعذّر تحديد الموقع" : "Could not get your location" }}', 'text-danger');
            }, { enableHighAccuracy: true, timeout: 10000 });
        });

        // ---- Address search (forward geocoding) ----
        var results = document.getElementById('ocGeoResults');

        function runSearch() {
            var q = document.getElementById('ocGeoSearch').value.trim();
            if (q.length < 3) return;

            setStatus('{{ $ocAr ? "جارٍ البحث…" : "Searching…" }}');
            fetch('https://nominatim.openstreetmap.org/search?format=jsonv2&limit=6&accept-language=' + lang + '&q=' + encodeURIComponent(q), {
                headers: { 'Accept': 'application/json' }
            })
                .then(function (r) { return r.json(); })
                .then(function (list) {
                    results.innerHTML = '';
                    if (!list.length) {
                        setStatus('{{ $ocAr ? "لا توجد نتائج" : "No matches found" }}', 'text-danger');
                        results.style.display = 'none';
                        return;
                    }
                    list.forEach(function (item) {
                        var a = document.createElement('a');
                        a.href = 'javascript:void(0)';
                        a.className = 'list-group-item list-group-item-action fs-7';
                        a.textContent = item.display_name;
                        a.addEventListener('click', function () {
                            map.setView([item.lat, item.lon], 16);
                            placePin(item.lat, item.lon, 'search');
                            results.style.display = 'none';
                        });
                        results.appendChild(a);
                    });
                    results.style.display = 'block';
                    setStatus('{{ $ocAr ? "اختر نتيجة ثم اسحب الدبوس للضبط" : "Pick a result, then drag the pin to fine-tune" }}');
                })
                .catch(function () {
                    setStatus('{{ $ocAr ? "تعذّر البحث" : "Search failed" }}', 'text-danger');
                });
        }

        document.getElementById('ocGeoSearchBtn').addEventListener('click', runSearch);
        document.getElementById('ocGeoSearch').addEventListener('keydown', function (e) {
            if (e.key === 'Enter') { e.preventDefault(); runSearch(); }
        });

        // Leaflet needs a nudge when it starts inside a hidden/animated container.
        setTimeout(function () { map.invalidateSize(); }, 250);
    })();
</script>
