@php $aisles = collect($tCats ?? []); @endphp
@if ($aisles->count())
<nav class="aisle-bar hide-sm">
  <div class="container">
    <a href="{{ URL::to($tSlug . '/categories') }}" class="all"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><rect x="3" y="3" width="7.5" height="7.5" rx="2"/><rect x="13.5" y="3" width="7.5" height="7.5" rx="2"/><rect x="3" y="13.5" width="7.5" height="7.5" rx="2"/><rect x="13.5" y="13.5" width="7.5" height="7.5" rx="2"/></svg> {{ __('All aisles') }}</a>
    @foreach ($aisles->take(12) as $c)
      <a href="{{ URL::to($tSlug . '/categories') }}#cat-{{ $c->id }}">{{ $c->name }}</a>
    @endforeach
  </div>
</nav>
@endif
