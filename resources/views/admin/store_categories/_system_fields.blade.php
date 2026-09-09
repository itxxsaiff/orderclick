{{-- System + the activity this category represents.
     The activity link is what lets registration assign a category automatically — the merchant
     picks a system and activity, and the marketplace category follows. --}}
@php
    $ocCat = $category ?? null;
    $ocSystem = old('system', $ocCat->system ?? 'orders');
    $ocActivity = old('activity_id', $ocCat->activity_id ?? '');
@endphp

<div class="form-group">
    <label class="form-label">{{ trans('labels.system') }}<span class="text-danger"> *</span></label>
    <select class="form-select" name="system" id="ocCatSystem" required>
        @foreach (\App\Helpers\Systems::all() as $ocS)
            <option value="{{ $ocS['key'] }}" {{ $ocSystem === $ocS['key'] ? 'selected' : '' }}>
                {{ app()->getLocale() === 'ar' ? $ocS['name_ar'] : $ocS['name'] }}</option>
        @endforeach
    </select>
</div>

<div class="form-group mt-3">
    <label class="form-label">{{ trans('labels.activity') }}</label>
    <select class="form-select" name="activity_id" id="ocCatActivity">
        <option value="">{{ trans('labels.none') }}</option>
        @foreach ($activities as $ocA)
            <option value="{{ $ocA->id }}" data-system="{{ $ocA->system }}"
                {{ (string) $ocActivity === (string) $ocA->id ? 'selected' : '' }}>{{ $ocA->display_name }}</option>
        @endforeach
    </select>
    <small class="text-muted">{{ trans('messages.category_activity_hint') }}</small>
</div>

<div class="form-check form-switch mt-3">
    <input class="form-check-input" type="checkbox" name="is_other" value="1" id="ocCatOther"
        {{ (int) old('is_other', $ocCat->is_other ?? 2) === 1 ? 'checked' : '' }}>
    <label class="form-check-label" for="ocCatOther">
        {{ trans('labels.other_category') }}
        <span class="text-muted fs-7">— {{ trans('messages.other_category_hint') }}</span>
    </label>
</div>

<script>
    // Activities follow the chosen system, so a category can never point at an activity from a
    // system it does not belong to.
    (function () {
        var sys = document.getElementById('ocCatSystem');
        var act = document.getElementById('ocCatActivity');
        if (!sys || !act) return;

        function filter() {
            Array.prototype.forEach.call(act.options, function (o) {
                if (!o.value) return;
                var show = o.getAttribute('data-system') === sys.value;
                o.hidden = !show;
                o.disabled = !show;
                if (!show && o.selected) act.value = '';
            });
        }

        sys.addEventListener('change', filter);
        filter();
    })();
</script>
