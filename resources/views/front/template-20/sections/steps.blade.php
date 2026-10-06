@php
    $T = fn($k, $f = '') => \App\Services\StoreDesign::text($tDesign, $k, $f);
    $ocSteps = $T('steps', []);
    if (!is_array($ocSteps) || !$ocSteps) {
        $ocSteps = [];
        foreach ([1, 2, 3] as $n) {
            $ocSteps[] = ['title' => trans('labels.eng_step_' . $tFlow . '_' . $n . '_title'), 'text' => trans('labels.eng_step_' . $tFlow . '_' . $n . '_text')];
        }
    }
@endphp
<section class="section bg-alt">
  <div class="container">
    <div class="section-head center reveal"><h2>{{ $T('steps_title', trans('labels.eng_steps_title')) }}</h2></div>
    <div class="steps reveal oce-steps-{{ count($ocSteps) }}">
      @foreach ($ocSteps as $i => $st)
        <div class="step"><span class="ic oce-step-n">{{ $i + 1 }}</span><h4>{{ $st['title'] }}</h4>@if (!empty($st['text']))<p>{{ $st['text'] }}</p>@endif</div>
      @endforeach
    </div>
  </div>
</section>
