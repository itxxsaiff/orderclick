{{-- Booking: doctors / specialists (clinics and salons). --}}
@php
    $T = fn($k, $f = '') => \App\Services\StoreDesign::text($tDesign, $k, $f);
    $ocTeam = collect($doctors ?? [])->take(8);
@endphp
@if ($ocTeam->count())
<section class="section">
  <div class="container">
    <div class="section-head center reveal"><h2>{{ $T('team_title', trans('labels.eng_team_title')) }}</h2></div>
    <div class="grid g-4 products reveal">
      @foreach ($ocTeam as $doc)
        @php
          $img = !empty($doc->image) ? url(env('ASSETSPATHURL') . 'item/' . $doc->image) : null;
          $url = URL::to($tSlug . '/booking') . '?doctor=' . $doc->id;
        @endphp
        <article class="product-card oce-person">
          <div class="product-card__media"><a href="{{ $url }}">@if ($img)<img src="{{ $img }}" alt="{{ $doc->name }}" loading="lazy">@else<span class="oce-ph">{{ mb_strtoupper(mb_substr($doc->name, 0, 1)) }}</span>@endif</a></div>
          <div class="product-card__body">
            @if ($doc->specialty)<span class="product-card__meta">{{ $doc->specialty }}</span>@endif
            <h4><a href="{{ $url }}">{{ $doc->name }}</a></h4>
            @if (!empty($doc->qualification))<p class="product-card__desc">{{ \Illuminate\Support\Str::limit($doc->qualification, 70) }}</p>@endif
            <div class="product-card__foot">
              <span class="price">@if ($doc->fee){{ helper::currency_formate($doc->fee, $storeinfo->id) }}@endif</span>
              <a href="{{ $url }}" class="btn btn-primary btn-sm">{{ __('Book') }}</a>
            </div>
          </div>
        </article>
      @endforeach
    </div>
  </div>
</section>
@endif
