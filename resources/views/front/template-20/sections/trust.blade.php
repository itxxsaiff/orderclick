@php
    $ocCopyTrust = \App\Services\StoreDesign::text($tDesign, 'trust', []);
    $ocCopyTrust = is_array($ocCopyTrust) ? $ocCopyTrust : [];
@endphp
<section class="value-strip">
  <div class="container">
    <div class="grid g-4">
      @foreach ($tDesign['trust'] as $i => $t)
        @php
          $n = $i + 1;
          $title = $ocCopyTrust[$i]['title'] ?? trans('labels.eng_trust_' . $tFlow . '_' . $n . '_title');
          $text = $ocCopyTrust[$i]['text'] ?? trans('labels.eng_trust_' . $tFlow . '_' . $n . '_text');
        @endphp
        <div class="value-item">
          <span class="ic">@include('front.template-20.partials.icon', ['name' => $t['icon']])</span>
          <div><strong>{{ $title }}</strong><span>{{ $text }}</span></div>
        </div>
      @endforeach
    </div>
  </div>
</section>
