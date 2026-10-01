{{-- System + Applicable Activity for a template image.
     Templates are created here and here only — the AI layer may recommend from these, never
     create or publish one. --}}
@php
    $ocT = $theme ?? null;
    $ocSystem = old('system', $ocT->system ?? 'orders');
    $ocSelected = array_map('strval', (array) old('activity_ids', $ocT ? $ocT->activityIds() : []));
@endphp

<div class="form-group col-md-6">
    <label class="form-label">{{ trans('labels.system') }}<span class="text-danger"> *</span></label>
    <select class="form-select" name="system" id="ocThemeSystem" required>
        @foreach (\App\Helpers\Systems::all() as $ocS)
            <option value="{{ $ocS['key'] }}" {{ $ocSystem === $ocS['key'] ? 'selected' : '' }}>
                {{ $ocS['name'] }}</option>
        @endforeach
    </select>
</div>

<div class="form-group col-md-6">
    <label class="form-label">{{ trans('labels.storefront_template') }}</label>
    <input type="number" min="1" max="20" class="form-control" name="template"
        value="{{ old('template', $ocT->template ?? 2) }}">
    <small class="text-muted">{{ trans('messages.storefront_template_hint') }}</small>
</div>

<div class="form-group col-md-12 mt-3">
    <label class="form-label">{{ trans('labels.applicable_activity') }}</label>
    <select class="form-select" name="activity_ids[]" id="ocThemeActivities" multiple size="6">
        @foreach ($activities as $ocA)
            <option value="{{ $ocA->id }}" data-system="{{ $ocA->system }}"
                {{ in_array((string) $ocA->id, $ocSelected, true) ? 'selected' : '' }}>{{ $ocA->display_name }}</option>
        @endforeach
    </select>
    <small class="text-muted">{{ trans('messages.applicable_activity_hint') }}</small>
</div>

<script>
    // Activities follow the selected system, so a template can never be tagged with an activity
    // from a system it does not belong to.
    (function () {
        var sys = document.getElementById('ocThemeSystem');
        var acts = document.getElementById('ocThemeActivities');
        if (!sys || !acts) return;

        function filter() {
            Array.prototype.forEach.call(acts.options, function (o) {
                var show = o.getAttribute('data-system') === sys.value;
                o.hidden = !show;
                o.disabled = !show;
                if (!show) o.selected = false;
            });
        }

        sys.addEventListener('change', filter);
        filter();
    })();
</script>
