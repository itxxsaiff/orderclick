{{-- STEP 1 — System, Activity, Specialization --}}
<span class="ocw__pill">{{ trans('labels.step') }} 1 {{ trans('labels.of_2') }} 3</span>
<h1 class="ocw__title">{{ trans('labels.choose_your_system_and_activity') }}</h1>
<p class="ocw__sub">{{ trans('messages.step_system_sub') }}</p>

<form method="POST" action="{{ URL::to('register/system') }}" id="ocwForm">
    @csrf
    <input type="hidden" name="system" id="ocwSystem" value="{{ old('system', $draft['system'] ?? '') }}">

    <div class="ocw__grid g3">
        @foreach ($systems as $s)
            <div class="ocw__pick ocw-sys {{ ($draft['system'] ?? '') === $s['key'] ? 'sel' : '' }}" data-key="{{ $s['key'] }}">
                <span class="tick"><i class="fa-solid fa-check"></i></span>
                <div style="font-size:26px;line-height:1;">{{ $s['icon'] }}</div>
                <h4 style="margin-top:10px;">{{ $s['name'] }}</h4>
                <p>{{ $s['desc'] }}</p>
            </div>
        @endforeach
    </div>

    <div class="row mt-4">
        <div class="col-12 col-md-6 fg">
            <label class="f">{{ trans('labels.activity') }}<span class="req"> *</span></label>
            <select class="in" name="activity_id" id="ocwActivity" required>
                <option value="">{{ trans('labels.select') }}</option>
                @foreach ($activities as $a)
                    <option value="{{ $a->id }}" data-system="{{ $a->system }}"
                        {{ (string) ($draft['activity_id'] ?? '') === (string) $a->id ? 'selected' : '' }}>
                        {{ $a->display_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-12 col-md-6 fg">
            <label class="f">{{ trans('labels.specialization') }}</label>
            <select class="in" name="specialization_id" id="ocwSpecialization">
                <option value="">{{ trans('labels.select') }}</option>
                @foreach (\App\Helpers\Systems::specializations($draft['activity_id'] ?? null) as $sp)
                    <option value="{{ $sp->id }}" {{ (string) ($draft['specialization_id'] ?? '') === (string) $sp->id ? 'selected' : '' }}>
                        {{ $sp->display_name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="ocw__actions">
        <a href="{{ URL::to('/') }}" class="ocw__btn ocw__btn--g">&larr; {{ trans('labels.back_to_website') }}</a>
        <button type="submit" class="ocw__btn ocw__btn--p">{{ trans('labels.save_and_continue') }} &rarr;</button>
    </div>
</form>

<script>
    (function () {
        var hidden = document.getElementById('ocwSystem');
        var act = document.getElementById('ocwActivity');
        var spec = document.getElementById('ocwSpecialization');

        function filterActivities() {
            var sys = hidden.value;
            Array.prototype.forEach.call(act.options, function (o) {
                if (!o.value) return;
                var show = o.getAttribute('data-system') === sys;
                o.hidden = !show; o.disabled = !show;
                if (!show && o.selected) act.value = '';
            });
        }

        document.querySelectorAll('.ocw-sys').forEach(function (card) {
            card.addEventListener('click', function () {
                document.querySelectorAll('.ocw-sys').forEach(function (c) { c.classList.remove('sel'); });
                this.classList.add('sel');
                hidden.value = this.getAttribute('data-key');
                filterActivities();
                spec.innerHTML = '<option value="">{{ trans('labels.select') }}</option>';
            });
        });

        // Specializations follow the chosen activity.
        act.addEventListener('change', function () {
            spec.innerHTML = '<option value="">{{ trans('labels.select') }}</option>';
            if (!this.value) return;
            fetch('{{ URL::to('register/specializations') }}?system=' + encodeURIComponent(hidden.value) +
                  '&activity_id=' + encodeURIComponent(this.value))
                .then(function (r) { return r.json(); })
                .then(function (d) {
                    (d.specializations || []).forEach(function (s) {
                        var o = document.createElement('option');
                        o.value = s.id; o.textContent = s.name; spec.appendChild(o);
                    });
                });
        });

        if (hidden.value) filterActivities();

        document.getElementById('ocwForm').addEventListener('submit', function (e) {
            if (!hidden.value) { e.preventDefault(); alert('{{ trans('messages.choose_system_first') }}'); }
        });
    })();
</script>
