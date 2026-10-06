@php $T = fn($k, $f = '') => \App\Services\StoreDesign::text($tDesign, $k, $f); @endphp
<section class="section-tight">
  <div class="container">
    <div class="cta-band reveal">
      <div class="cta-band__inner">
        <div>
          <h2 class="mt-1">{{ $T('cta_title', trans('labels.eng_cta_title')) }}</h2>
          <p>{{ $T('cta_text', trans('labels.eng_cta_text_' . $tFlow)) }}</p>
        </div>
        <div class="flex gap-2 wrap">
          <a href="{{ $tCta['url'] }}" class="btn btn-primary btn-lg">{{ $T('cta_button', $tCta['label']) }}</a>
          @if (!empty($tWa))
            <a href="https://wa.me/{{ $tWa }}" class="btn btn-ghost btn-lg oce-on-dark" target="_blank" rel="noopener">{{ __('WhatsApp') }}</a>
          @endif
        </div>
      </div>
    </div>
  </div>
</section>
