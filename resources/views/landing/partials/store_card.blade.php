@php
    $mkAr = app()->getLocale() === 'ar';
    // The store's display name is always users.name — website_title is an SEO/browser title and
    // is usually the uncustomised platform default ("Order Click | …"), so never show it as the name.
    $storeName = trim((string) $store->name) ?: trim((string) $store->website_title) ?: 'Store';

    // Category label from the reliable business_type (store_id/StoreCategory is often unset or mis-seeded).
    $mkTypeMap = [
        'food'     => ['Restaurant', 'مطاعم'],
        'grocery'  => ['Grocery', 'بقالة'],
        'retail'   => ['Retail', 'تجزئة'],
        'pharmacy' => ['Pharmacy', 'صيدلية'],
        'clinic'   => ['Clinic', 'عيادة'],
        'salon'    => ['Salon & Beauty', 'صالون وتجميل'],
        'booking'  => ['Booking', 'حجوزات'],
        'service'  => ['Services', 'خدمات'],
    ];
    $catLabel = isset($mkTypeMap[$store->business_type])
        ? $mkTypeMap[$store->business_type][$mkAr ? 1 : 0]
        : ($store->cat_name ?: ucfirst((string) $store->business_type));

    // Location: prefer the country (real name) over the city, which can be a cryptic code.
    $placeName = trim((string) $store->country) ?: null;

    // Description: if it opens with the (usually default) website title, swap that prefix for the
    // real store name so cards don't read "Order Click | Multi-Business Ordering Platform brings…".
    $desc = trim((string) $store->description);
    // Candidate placeholder prefixes (default titles), longest first so the fuller one wins.
    $mkPrefixes = array_filter([trim((string) $store->website_title), trim((string) ($platformTitle ?? ''))]);
    usort($mkPrefixes, fn($a, $b) => strlen($b) <=> strlen($a));
    foreach ($mkPrefixes as $pfx) {
        if ($desc !== '' && $pfx !== '' && $pfx !== $storeName && stripos($desc, $pfx) === 0) {
            $desc = $storeName . substr($desc, strlen($pfx));
            break;
        }
    }
@endphp
<a href="{{ URL::to($store->slug . '/') }}" target="_blank" class="mk-card">
    <div class="mk-cover">
        <img src="{{ helper::image_path($store->cover_image) }}" alt="{{ $storeName }}" loading="lazy">
        <span class="mk-logo"><img src="{{ helper::image_path($store->logo) }}" alt=""></span>
    </div>
    <div class="mk-body">
        <div class="mk-row">
            <h3 class="mk-name">{{ $storeName }}</h3>
            @if ($store->rating_count > 0)
                <span class="mk-rate"><i class="fa-solid fa-star"></i> {{ number_format($store->avg_rating, 1) }}</span>
            @endif
        </div>
        <div class="mk-meta">
            <span>{{ $catLabel }}</span>
            @if ($placeName)<span class="mk-dot">·</span><span><i class="fa-solid fa-location-dot"></i> {{ $placeName }}</span>@endif
        </div>
        @if ($desc)
            <p class="mk-desc">{{ \Illuminate\Support\Str::limit($desc, 72) }}</p>
        @endif
        <div class="mk-status">
            @if ($store->is_open)
                <span class="on"><i class="fa-solid fa-circle"></i> {{ trans('landing.open_now') }}</span>
            @endif
            @if ($store->business_type === 'booking')
                <span><i class="fa-regular fa-calendar-check"></i> {{ trans('landing.bookings') }}</span>
            @elseif ($store->is_delivery == 1)
                <span><i class="fa-solid fa-truck"></i> {{ trans('landing.delivery') }}</span>
            @endif
        </div>
        <span class="mk-open-btn">{{ trans('landing.open_store') }} <i class="fa-solid fa-arrow-{{ $mkAr ? 'left' : 'right' }}"></i></span>
    </div>
</a>
