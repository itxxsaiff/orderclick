@php
    $catNames = collect($getcategory ?? [])->pluck('name', 'id');
    // $getitem is a collection on list pages but a single model on product detail — guard it.
    $searchItems = (isset($getitem) && $getitem instanceof \Illuminate\Support\Collection)
        ? $getitem->take(40)
        : collect();
@endphp
<div class="modal modal--wide" id="searchModal" role="dialog" aria-modal="true" aria-label="Search the menu">
  <div class="modal__scrim" data-close></div>
  <div class="modal__dialog">
    <div class="modal__head">
      <div>
        <h3>{{ __('Search the menu') }}</h3>
        <p>{{ __('Type a dish, an ingredient or a category — results update as you type.') }}</p>
      </div>
      <button class="modal__close" data-close aria-label="Close">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg>
      </button>
    </div>
    <div class="modal__body">
      <div class="input-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></svg>
        <input class="input" type="search" placeholder="{{ __('Search dishes, categories…') }}" data-quick-search data-autofocus>
      </div>

      @if ($searchItems->count())
        <p class="small muted mt-2" style="font-weight:700;letter-spacing:.1em;text-transform:uppercase">{{ __('On the menu') }}</p>
        <div class="qs-list">
          @foreach ($searchItems as $item)
            @php
              $cat = $catNames[$item->cat_id] ?? '';
              $up = @$item['item_image']->image;
              $img = !empty($up) ? helper::image_path($up) : helper::food_image($item->item_name . ' ' . $cat, $item->id);
              $detail = URL::to($tSlug . '/details-' . $item->slug);
              $hasVar = isset($item['variation']) && $item['variation']->count() > 0;
              $price = $hasVar ? $item['variation'][0]->price : $item->item_price;
            @endphp
            <a href="{{ $detail }}" class="qs-item" data-qs-item="{{ strtolower($item->item_name . ' ' . $cat) }}">
              <img src="{{ $img }}" alt="{{ $item->item_name }}" loading="lazy">
              <div><strong>{{ \Illuminate\Support\Str::limit($item->item_name, 32) }}</strong><span>{{ $cat }}</span></div>
              <span class="price">{{ helper::currency_formate($price, $storeinfo->id) }}</span>
            </a>
          @endforeach
        </div>
      @endif

      @if (($getcategory ?? collect())->count())
        <div class="qs-terms">
          @foreach (($getcategory ?? collect())->take(6) as $c)
            <a href="{{ URL::to($tSlug . '/categories') }}#cat-{{ $c->id }}" class="chip">{{ $c->name }}</a>
          @endforeach
        </div>
      @endif
    </div>
    <div class="modal__foot">
      <button class="btn btn-ghost btn-block" data-close>{{ __('Cancel') }}</button>
      <a href="{{ URL::to($tSlug . '/categories') }}" class="btn btn-dark btn-block">{{ __('Browse full menu') }}</a>
    </div>
  </div>
</div>
