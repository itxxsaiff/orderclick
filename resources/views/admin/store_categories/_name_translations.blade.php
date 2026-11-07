{{-- The category name in every other active language (shown on the landing page, marketplace and setup). --}}
@php
    $ocCatTr = (isset($category) && is_array($category->name_translations)) ? $category->name_translations : [];
    $ocCatLangs = helper::listoflanguage()->filter(fn($l) => $l->code !== 'en');
@endphp
@if ($ocCatLangs->count())
    <div class="row">
        @foreach ($ocCatLangs as $ocLang)
            <div class="form-group col-md-4">
                <label class="form-label">{{ trans('labels.name') }} ({{ $ocLang->name }})</label>
                <input type="text" class="form-control" name="name_translations[{{ $ocLang->code }}]"
                    value="{{ old('name_translations.' . $ocLang->code, $ocCatTr[$ocLang->code] ?? '') }}"
                    dir="{{ $ocLang->layout == 2 ? 'rtl' : 'ltr' }}" maxlength="150">
            </div>
        @endforeach
        <small class="text-muted d-block mb-2">{{ trans('messages.category_translation_hint') }}</small>
    </div>
@endif
