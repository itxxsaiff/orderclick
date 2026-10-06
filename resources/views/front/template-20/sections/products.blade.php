{{-- Products (orders) or services sold as items (service flow). Card style comes from the body class. --}}
@php
    $T = fn($k, $f = '') => \App\Services\StoreDesign::text($tDesign, $k, $f);
    $cats = collect($getcategory ?? []);
    $ocList = collect($ocItems ?? $getitem ?? [])->take(8);
    $ocKey = $ocKey ?? 'products';
@endphp
@if ($ocList->count())
<section class="section {{ $ocKey === 'offers' ? '' : 'bg-alt' }}">
  <div class="container">
    <div class="head-row reveal">
      <div class="section-head">
        <h2>{{ $T($ocKey . '_title', trans('labels.eng_' . $ocKey . '_title')) }}</h2>
        @php $ocSub = $ocKey === 'offers' ? '' : $T('products_text'); @endphp
        @if ($ocSub !== '')<p>{{ $ocSub }}</p>@endif
      </div>
      <a href="{{ URL::to($tSlug . '/categories') }}" class="link-arrow">{{ __('See all') }}
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
      </a>
    </div>
    <div class="grid g-4 products reveal">
      @foreach ($ocList as $item)
        @include('front.template-20.partials.product_card', ['item' => $item, 'catNames' => $cats->pluck('name', 'id')])
      @endforeach
    </div>
  </div>
</section>
@endif
