@php $svcs = collect($services ?? [])->take(30); @endphp
<div class="modal modal--wide" id="searchModal" role="dialog" aria-modal="true" aria-label="Search">
  <div class="modal__scrim" data-close></div>
  <div class="modal__dialog">
    <div class="modal__head">
      <div><h3>{{ __('Find a booking') }}</h3><p>{{ __('Search our services — results filter as you type.') }}</p></div>
      <button class="modal__close" data-close aria-label="Close"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg></button>
    </div>
    <div class="modal__body">
      <div class="input-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></svg>
        <input class="input" type="search" placeholder="{{ __('Search services…') }}" data-quick-search data-autofocus>
      </div>
      @if ($svcs->count())
      <div class="qs-list mt-3">
        @foreach ($svcs as $s)
          @php $img = !empty($s->image) ? helper::image_path($s->image) : helper::food_image($s->name, $s->id, 'retail'); @endphp
          <a href="{{ URL::to($tSlug . '/booking') }}?service={{ $s->id }}" class="qs-item" data-qs-item="{{ strtolower($s->name . ' ' . $s->category) }}"><img src="{{ $img }}" alt=""><div><strong>{{ \Illuminate\Support\Str::limit($s->name, 32) }}</strong><span>{{ $s->category }}</span></div><span class="price">{{ helper::currency_formate($s->price, $storeinfo->id) }}</span></a>
        @endforeach
      </div>
      @endif
    </div>
    <div class="modal__foot">
      <button class="btn btn-ghost btn-block" data-close>{{ __('Close') }}</button>
      <a href="{{ URL::to($tSlug . '/booking') }}" class="btn btn-dark btn-block">{{ __('Book appointment') }}</a>
    </div>
  </div>
</div>
