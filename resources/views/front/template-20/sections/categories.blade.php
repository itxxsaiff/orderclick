{{-- Categories: tiles / chips / circles, from the body class. --}}
@php
    $T = fn($k, $f = '') => \App\Services\StoreDesign::text($tDesign, $k, $f);
    $cats = collect($getcategory ?? []);
    $items = collect($getitem ?? []);
@endphp
@if ($cats->count())
<section class="section oce-cats">
  <div class="container">
    <div class="head-row reveal">
      <div class="section-head">
        <h2>{{ $T('categories_title', trans('labels.eng_categories_title')) }}</h2>
        @php $ocSub = $T('categories_text'); @endphp
        @if ($ocSub !== '')<p>{{ $ocSub }}</p>@endif
      </div>
      <a href="{{ URL::to($tSlug . '/categories') }}" class="link-arrow">{{ __('See all') }}
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
      </a>
    </div>
    <div class="cat-rail reveal">
      @foreach ($cats as $c)
        @php $cimg = !empty($c->image) ? helper::image_path($c->image) : (in_array(optional($tApp)->business_type, ['food', 'cafe', 'grocery', null, ''], true) ? helper::food_image($c->name, $c->id) : null); @endphp
        <a href="{{ URL::to($tSlug . '/categories') }}#cat-{{ $c->id }}" class="cat-card">
          @if ($cimg)<img src="{{ $cimg }}" alt="{{ $c->name }}" loading="lazy">@else<span class="oce-ph">{{ mb_strtoupper(mb_substr($c->name, 0, 1)) }}</span>@endif
          <span class="cat-card__label"><strong>{{ $c->name }}</strong><span>{{ $items->where('cat_id', $c->id)->count() }} {{ __('items') }}</span></span>
        </a>
      @endforeach
    </div>
  </div>
</section>
@endif
