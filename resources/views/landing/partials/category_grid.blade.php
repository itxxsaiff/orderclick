{{--
    One system's store categories (Admin > Store Categories) as a card grid.
    $ocGridCats  collection of StoreCategory
    $ocGridSys   orders | booking | service (pre-selects the system on the signup wizard)
    $ocGridId    unique element id
    Shows the first 12; the rest open with "View all".
--}}
@php
    $ocVisible = 12;
    $ocIcons = [
        'dental' => 'fa-tooth', 'clinic' => 'fa-stethoscope', 'hospital' => 'fa-hospital', 'laborator' => 'fa-flask',
        'radiolog' => 'fa-x-ray', 'therap' => 'fa-hand-holding-medical', 'health' => 'fa-heart-pulse', 'veterin' => 'fa-paw',
        'pet care' => 'fa-paw', 'barber' => 'fa-scissors', 'salon' => 'fa-scissors', 'spa' => 'fa-spa', 'gym' => 'fa-dumbbell',
        'fitness' => 'fa-dumbbell', 'trainer' => 'fa-dumbbell', 'swimming' => 'fa-person-swimming', 'hotel' => 'fa-bell-concierge',
        'chalet' => 'fa-house-chimney', 'yacht' => 'fa-ship', 'car rental' => 'fa-car', 'car wash' => 'fa-car', 'used cars' => 'fa-car',
        'photograph' => 'fa-camera-retro', 'camera' => 'fa-camera', 'event' => 'fa-calendar-check', 'wedding' => 'fa-champagne-glasses',
        'travel' => 'fa-plane', 'flight' => 'fa-plane', 'lawyer' => 'fa-scale-balanced', 'tutor' => 'fa-graduation-cap',
        'lesson' => 'fa-graduation-cap', 'workshop' => 'fa-chalkboard-user', 'graphic' => 'fa-pen-nib', 'website' => 'fa-code',
        'app development' => 'fa-mobile-screen', 'video' => 'fa-film', 'marketing' => 'fa-bullhorn', 'social media' => 'fa-share-nodes',
        'writing' => 'fa-pen', 'translation' => 'fa-language', 'voice' => 'fa-microphone', 'music' => 'fa-music',
        'podcast' => 'fa-podcast', 'animation' => 'fa-cube', 'celebrit' => 'fa-crown', 'clean' => 'fa-broom', 'plumb' => 'fa-wrench',
        'electric' => 'fa-bolt', 'engineer' => 'fa-helmet-safety', 'contracting' => 'fa-helmet-safety', 'repair' => 'fa-screwdriver-wrench',
        'maintenance' => 'fa-screwdriver-wrench', 'towing' => 'fa-truck-pickup', 'delivery' => 'fa-truck', 'courier' => 'fa-truck',
        'shipping' => 'fa-truck', 'logistic' => 'fa-truck', 'moving' => 'fa-truck-moving', 'driver' => 'fa-id-card', 'security' => 'fa-shield-halved',
        'accounting' => 'fa-calculator', 'advisor' => 'fa-briefcase', 'real estate' => 'fa-building', 'restaurant' => 'fa-utensils',
        'cafe' => 'fa-mug-hot', 'café' => 'fa-mug-hot', 'grocery' => 'fa-basket-shopping', 'pharmac' => 'fa-prescription-bottle-medical',
        'flower' => 'fa-seedling', 'cloth' => 'fa-shirt', 'gift' => 'fa-gift', 'baker' => 'fa-bread-slice', 'sweet' => 'fa-cake-candles',
        'cake' => 'fa-cake-candles', 'fruit' => 'fa-apple-whole', 'dairy' => 'fa-cheese', 'ice cream' => 'fa-ice-cream', 'book' => 'fa-book',
        'electronic' => 'fa-mobile-screen', 'perfume' => 'fa-spray-can-sparkles', 'toy' => 'fa-shapes', 'shoe' => 'fa-shoe-prints',
        'jewel' => 'fa-gem', 'furniture' => 'fa-couch', 'meat' => 'fa-drumstick-bite', 'fish' => 'fa-fish', 'eyewear' => 'fa-glasses',
        'rental' => 'fa-key', 'market' => 'fa-basket-shopping',
    ];
    $ocGridLink = $registerUrl . (str_contains($registerUrl, 'register') ? '?system=' . $ocGridSys : '');
@endphp
<div class="ocl-cats" id="{{ $ocGridId }}">
    @foreach ($ocGridCats as $category)
        @php
            $ocLower = mb_strtolower($category->name ?? '');
            $ocIcon = 'fa-store';
            foreach ($ocIcons as $keyword => $icon) {
                if (str_contains($ocLower, $keyword)) { $ocIcon = $icon; break; }
            }
            $ocImage = !empty($category->image) ? helper::image_path($category->image) : null;
            $ocHref = $ocGridLink . (str_contains($ocGridLink, '?') && $category->activity_id ? '&activity=' . $category->activity_id : '');
        @endphp
        <a href="{{ $ocHref }}" class="ocl-cat{{ $loop->index >= $ocVisible ? ' ocl-cat--more' : '' }}">
            <div class="ocl-cat__ic">
                @if ($ocImage)
                    {{-- Missing image file: fall back to the icon instead of printing the alt text. --}}
                    <img src="{{ $ocImage }}" alt="" loading="lazy"
                        onerror="this.style.display='none';this.nextElementSibling.style.display='';">
                    <i class="fa-solid {{ $ocIcon }}" style="display:none"></i>
                @else
                    <i class="fa-solid {{ $ocIcon }}"></i>
                @endif
            </div>
            <h4>{{ $category->label() }}</h4>
            <p>{{ trans('landing.launch_your_store_in_this_category') }}</p>
            <span class="go">{{ trans('landing.register_now') }}</span>
        </a>
    @endforeach
</div>
@if ($ocGridCats->count() > $ocVisible)
    <button type="button" class="ocl-btn ocl-btn--ghost ocl-cats-toggle" data-target="{{ $ocGridId }}"
        data-more="{{ trans('landing.view_all') }} ({{ $ocGridCats->count() }})"
        data-less="{{ trans('landing.show_less') }}">{{ trans('landing.view_all') }} ({{ $ocGridCats->count() }})</button>
@endif
