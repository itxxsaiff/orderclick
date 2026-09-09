@if (App\Models\SystemAddons::where('unique_identifier', 'google_recaptcha')->first() != null &&
        App\Models\SystemAddons::where('unique_identifier', 'google_recaptcha')->first()->activated == 1)

    @if (helper::adminappdata('')->recaptcha_version == 'v2')
        <div class="col-12">
            <div class="g-recaptcha {{ session()->get('direction') == 2 ? 'rtl' : '' }}"
                data-sitekey="{{ helper::adminappdata('')->google_recaptcha_site_key }}"></div>
            @error('g-recaptcha-response')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
    @endif

    @if (helper::adminappdata('')->recaptcha_version == 'v3')
        <div class="col-12">
            {!! RecaptchaV3::field('contact') !!}
            @error('g-recaptcha-response')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
    @endif
@endif
