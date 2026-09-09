@extends('admin.layout.auth_default')
@section('content')

    <style>
        html, body { overflow-x: hidden; }
        .oc-onboard, .oc-onboard * { box-sizing: border-box; }
        .oc-onboard { width: 100%; max-width: 100%; overflow-x: hidden; }
        .oc-card { width: 100%; max-width: min(760px, 100%); }
        .oc-card * { min-width: 0; }
        .oc-input, .oc-select, .oc-ccode, .oc-slug input { max-width: 100%; }
        .oc-onboard { min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 28px 16px;
            background: radial-gradient(120% 120% at 90% -10%, rgba(31,157,85,.10), transparent 55%), #f5f7f4; }
        .oc-card { width: 100%; max-width: 760px; background: #fff; border: 1px solid #e5e9e2; border-radius: 18px;
            box-shadow: 0 24px 60px -32px rgba(20,32,24,.4); overflow: hidden; }
        .oc-card__head { padding: 26px 30px 0; }
        .oc-title { font-size: 26px; font-weight: 700; color: #17201a; margin: 0; }
        .oc-sub { color: #6b7669; margin: 6px 0 0; font-size: 14.5px; }
        .oc-sub a { color: #1f9d55; font-weight: 600; text-decoration: none; }

        /* Stepper */
        .oc-steps { display: flex; align-items: center; gap: 8px; padding: 22px 30px 6px; }
        .oc-steps .st { display: flex; align-items: center; gap: 9px; flex: 1; }
        .oc-steps .num { width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 14px; background: #eef2ec; color: #8a978d; flex: 0 0 auto; transition: .2s; }
        .oc-steps .lbl { font-size: 13px; font-weight: 600; color: #8a978d; transition: .2s; }
        .oc-steps .bar { height: 3px; flex: 1; background: #eef2ec; border-radius: 3px; transition: .3s; }
        .oc-steps .st.active .num { background: #1f9d55; color: #fff; }
        .oc-steps .st.active .lbl { color: #17201a; }
        .oc-steps .st.done .num { background: #d4ede0; color: #137a40; }

        .oc-body { padding: 14px 30px 30px; }
        .oc-step { display: none; }
        .oc-step.show { display: block; animation: ocFade .25s ease; }
        @keyframes ocFade { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: none; } }

        .oc-field { margin-bottom: 15px; }
        .oc-field label { display: block; font-size: 13.5px; font-weight: 600; color: #39443c; margin-bottom: 6px; }
        .oc-field label .req { color: #d64545; }
        .oc-input, .oc-select { width: 100%; height: 46px; border: 1px solid #d9e0d4; border-radius: 10px; padding: 0 14px;
            font-size: 15px; color: #17201a; background: #fff; transition: .15s; }
        .oc-input:focus, .oc-select:focus { outline: none; border-color: #1f9d55; box-shadow: 0 0 0 3px rgba(31,157,85,.12); }
        .oc-grid2 { display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 1fr); gap: 14px; }
        .oc-slug { display: flex; align-items: stretch; border: 1px solid #d9e0d4; border-radius: 10px; overflow: hidden; }
        .oc-slug .pfx { background: #f0f3ee; color: #6b7669; font-size: 12.5px; display: flex; align-items: center; padding: 0 12px; border-right: 1px solid #d9e0d4; white-space: nowrap; }
        .oc-slug input { border: 0; height: 46px; padding: 0 12px; flex: 1; font-size: 15px; }
        .oc-slug input:focus { outline: none; }
        .oc-err { color: #d64545; font-size: 12.5px; margin-top: 5px; display: block; }

        /* Phone (country code + number) */
        .oc-phone { display: flex; gap: 8px; }
        .oc-ccode { flex: 0 0 116px; width: 116px; height: 46px; border: 1px solid #d9e0d4; border-radius: 10px;
            padding: 0 8px; font-size: 14px; background: #fff; color: #17201a; }
        .oc-ccode:focus { outline: none; border-color: #1f9d55; box-shadow: 0 0 0 3px rgba(31,157,85,.12); }
        .oc-phone .oc-input { flex: 1; min-width: 0; }
        /* GPS */
        .oc-gps { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
        .oc-gps-btn { display: inline-flex; align-items: center; gap: 8px; height: 46px; padding: 0 18px; border: 1px solid #1f9d55;
            background: #eafaf0; color: #137a40; font-weight: 600; border-radius: 10px; cursor: pointer; font-size: 14px; }
        .oc-gps-btn:hover { background: #1f9d55; color: #fff; }
        .oc-gps-status { font-size: 13px; color: #8a978d; }
        .oc-gps-status.ok { color: #137a40; font-weight: 600; }
        .oc-gps-status.err { color: #d64545; }

        /* Business type cards */
        .oc-types { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; }
        .oc-type { border: 2px solid #e5e9e2; border-radius: 14px; padding: 18px 14px; text-align: center; cursor: pointer;
            background: #fff; transition: .18s; position: relative; }
        .oc-type:hover { border-color: #bfe0cd; transform: translateY(-2px); }
        .oc-type.sel { border-color: #1f9d55; background: #f2fbf6; box-shadow: 0 10px 26px -16px rgba(31,157,85,.6); }
        .oc-type .emo { font-size: 30px; line-height: 1; }
        .oc-type .nm { font-weight: 650; color: #17201a; font-size: 14.5px; margin-top: 10px; }
        .oc-type .ds { font-size: 11.5px; color: #7d887f; margin-top: 3px; line-height: 1.35; }
        .oc-type .tick { position: absolute; top: 9px; right: 9px; width: 20px; height: 20px; border-radius: 50%; background: #1f9d55;
            color: #fff; font-size: 11px; display: none; align-items: center; justify-content: center; }
        .oc-type.sel .tick { display: flex; }

        /* Template preview */
        .oc-tpls { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .oc-tpl { border: 2px solid #e5e9e2; border-radius: 14px; overflow: hidden; cursor: pointer; background: #fff; transition: .18s; }
        .oc-tpl:hover { border-color: #bfe0cd; }
        .oc-tpl.sel { border-color: #1f9d55; box-shadow: 0 10px 26px -16px rgba(31,157,85,.6); }
        .oc-tpl.disabled { opacity: .55; cursor: not-allowed; }
        .oc-tpl__img { height: 158px; background: #f0f3ee center/cover no-repeat; border-bottom: 1px solid #eef2ec; position: relative; }
        .oc-tpl__cap { padding: 11px 14px; display: flex; align-items: center; justify-content: space-between; }
        .oc-tpl__cap .nm { font-weight: 650; color: #17201a; font-size: 14px; }
        .oc-tpl__cap .badge2 { font-size: 10.5px; font-weight: 700; text-transform: uppercase; letter-spacing: .04em;
            color: #137a40; background: #e6f4ec; padding: 3px 8px; border-radius: 20px; }
        .oc-tpl__cap .badge2.soon { color: #8a7320; background: #f7edda; }
        .oc-tpl .picked { position: absolute; top: 10px; left: 10px; background: #1f9d55; color: #fff; font-size: 11px; font-weight: 700;
            padding: 4px 10px; border-radius: 20px; display: none; }
        .oc-tpl.sel .picked { display: inline-block; }

        /* Buttons */
        .oc-actions { display: flex; gap: 12px; margin-top: 22px; }
        .oc-btn { height: 48px; border-radius: 11px; font-weight: 650; font-size: 15px; border: 0; cursor: pointer; padding: 0 22px; transition: .15s; }
        .oc-btn--primary { background: #1f9d55; color: #fff; flex: 1; }
        .oc-btn--primary:hover { background: #198a49; }
        .oc-btn--primary:disabled { background: #c3d6ca; cursor: not-allowed; }
        .oc-btn--ghost { background: #fff; color: #39443c; border: 1px solid #d9e0d4; }
        .oc-btn--ghost:hover { background: #f5f7f4; }
        .oc-terms { display: flex; align-items: center; gap: 9px; margin-top: 18px; font-size: 13.5px; color: #6b7669; }
        .oc-terms a { color: #1f9d55; font-weight: 600; text-decoration: none; }
        .oc-note { font-size: 12.5px; color: #8a978d; margin-top: 10px; }

        @media (max-width: 640px) {
            .oc-onboard { padding: 12px 10px; align-items: flex-start; }
            .oc-card { border-radius: 16px; }
            .oc-card__head { padding: 22px 20px 0; }
            .oc-title { font-size: 23px; }
            .oc-steps { padding: 18px 18px 4px; gap: 6px; }
            .oc-steps .lbl { font-size: 12px; }
            .oc-steps .num { width: 26px; height: 26px; font-size: 13px; }
            .oc-body { padding: 12px 18px 26px; }
            .oc-grid2 { grid-template-columns: minmax(0, 1fr); }
            .oc-types { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .oc-tpls { grid-template-columns: minmax(0, 1fr); }
            .oc-actions { flex-wrap: wrap; }
            .oc-ccode { flex-basis: 104px; width: 104px; }
        }
        @media (max-width: 380px) {
            .oc-types { grid-template-columns: 1fr; }
            .oc-steps .lbl { display: none; }
        }
    </style>

    <div class="oc-onboard">
        <div class="oc-card">
            <div class="oc-card__head">
                <h1 class="oc-title">{{ trans('labels.register') }}</h1>
                <p class="oc-sub">{{ trans('labels.already_have_an_account') }}
                    <a href="{{ URL::to('/admin') }}">{{ trans('labels.login') }}</a>
                </p>
            </div>

            <div class="oc-steps">
                <div class="st active" data-dot="1"><span class="num">1</span><span class="lbl">System</span></div>
                <div class="bar"></div>
                <div class="st" data-dot="2"><span class="num">2</span><span class="lbl">Account</span></div>
                <div class="bar"></div>
                <div class="st" data-dot="3"><span class="num">3</span><span class="lbl">Business</span></div>
                <div class="bar"></div>
                <div class="st" data-dot="4"><span class="num">4</span><span class="lbl">Template</span></div>
            </div>

            <div class="oc-body">
                <form method="POST" action="{{ URL::to('admin/register_vendor') }}" id="ocForm">
                    @csrf
                    {{-- old() keeps these across a failed submit so nothing the merchant picked is lost. --}}
                    <input type="hidden" name="business_type" id="business_type" value="{{ old('business_type') }}">
                    <input type="hidden" name="template" id="template" value="{{ old('template', 3) }}">
                    <input type="hidden" name="system" id="system" value="{{ old('system', request('system')) }}">

                    {{-- ============ STEP 1: SYSTEM ============ --}}
                    {{-- V2: the System is the level above business type. It decides which plans the
                         merchant is offered, and is locked onto the account once they pay. --}}
                    <div class="oc-step show" data-step="1">
                        @php $oc_ar_sys = app()->getLocale() === 'ar'; @endphp
                        <p class="oc-sub" style="margin:0 0 16px;">
                            {{ $oc_ar_sys ? 'اختر النظام الذي يناسب نشاطك. لا يمكن تغييره بعد الدفع.' : 'Which system fits your business? This is locked once you pay.' }}
                        </p>
                        <div class="oc-types">
                            @foreach (\App\Helpers\Systems::all() as $sys)
                                <div class="oc-type" data-systemkey="{{ $sys['key'] }}">
                                    <span class="tick">&#10003;</span>
                                    <div class="emo">{{ $sys['icon'] }}</div>
                                    <div class="nm">{{ $oc_ar_sys ? $sys['name_ar'] : $sys['name'] }}</div>
                                    <div class="ds">{{ $oc_ar_sys ? $sys['desc_ar'] : $sys['desc'] }}</div>
                                </div>
                            @endforeach
                        </div>
                        <span class="oc-err" id="errSys" style="display:none;">
                            {{ $oc_ar_sys ? 'الرجاء اختيار النظام للمتابعة.' : 'Please choose a system to continue.' }}
                        </span>
                        <div class="oc-actions">
                            <button type="button" class="oc-btn oc-btn--primary" id="toStep2" data-next="2" disabled>Continue &rarr;</button>
                        </div>
                    </div>

                    {{-- ============ STEP 2: ACCOUNT ============ --}}
                    <div class="oc-step" data-step="2">
                        @php
                            $oc_ar = app()->getLocale() === 'ar';
                            $oc_countries = array_map(function ($c) {
                                return ['n' => $c['name'], 'iso' => $c['iso'], 'd' => $c['dial'], 'f' => $c['flag']];
                            }, helper::countries());
                        @endphp
                        <div class="oc-grid2">
                            <div class="oc-field">
                                <label>{{ trans('labels.name') }} <span class="req">*</span></label>
                                @if (session()->has('social_login'))
                                    <input type="text" class="oc-input" name="name" value="{{ session()->get('social_login')['name'] }}" id="name" placeholder="{{ trans('labels.name') }}">
                                @else
                                    <input type="text" class="oc-input" name="name" value="{{ old('name') }}" id="name" placeholder="{{ trans('labels.name') }}">
                                @endif
                            </div>
                            <div class="oc-field">
                                <label>{{ trans('labels.email') }} <span class="req">*</span></label>
                                @if (session()->has('social_login'))
                                    <input type="email" class="oc-input" name="email" value="{{ session()->get('social_login')['email'] }}" id="email" placeholder="{{ trans('labels.email') }}">
                                @else
                                    <input type="email" class="oc-input" name="email" value="{{ old('email') }}" id="email" placeholder="{{ trans('labels.email') }}">
                                @endif
                                <span class="oc-err" id="err-email" style="{{ $errors->has('email') ? '' : 'display:none;' }}">{{ $errors->first('email') }}</span>
                            </div>
                            <div class="oc-field">
                                <label>{{ trans('labels.mobile') }} <span class="req">*</span></label>
                                <div class="oc-phone">
                                    <select name="country_code" id="country_code" class="oc-ccode">
                                        @foreach ($oc_countries as $c)
                                            <option value="{{ $c['d'] }}" data-iso="{{ $c['iso'] }}" {{ $c['iso'] == 'BH' ? 'selected' : '' }}>{{ $c['f'] }} {{ $c['d'] }}</option>
                                        @endforeach
                                    </select>
                                    <input type="tel" inputmode="numeric" class="oc-input" name="mobile" value="{{ old('mobile') }}" id="mobile" placeholder="{{ trans('labels.mobile') }}">
                                </div>
                                <span class="oc-err" id="err-mobile" style="{{ $errors->has('mobile') ? '' : 'display:none;' }}">{{ $errors->first('mobile') }}</span>
                            </div>
                            @if (!session()->has('social_login'))
                                <div class="oc-field">
                                    <label>{{ trans('labels.password') }} <span class="req">*</span></label>
                                    <input type="password" class="oc-input" name="password" value="{{ old('password') }}" id="password" placeholder="{{ trans('labels.password') }}">
                                </div>
                            @endif
                            <div class="oc-field">
                                <label>{{ $oc_ar ? 'الدولة' : 'Country' }} <span class="req">*</span></label>
                                <select name="country" id="country" class="oc-select">
                                    @foreach ($oc_countries as $c)
                                        <option value="{{ $c['n'] }}" data-iso="{{ $c['iso'] }}" data-dial="{{ $c['d'] }}" {{ $c['iso'] == 'BH' ? 'selected' : '' }}>{{ $c['f'] }} {{ $c['n'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="oc-field">
                                <label>{{ trans('labels.city') }}</label>
                                <input type="text" name="city" id="city" class="oc-input" value="{{ old('city') }}"
                                    placeholder="{{ $oc_ar ? 'مثال: المنامة' : 'e.g. Manama' }}" autocomplete="address-level2">
                            </div>
                            <div class="oc-field">
                                <label>{{ trans('labels.area') }}</label>
                                <input type="text" name="area" id="area" class="oc-input" value="{{ old('area') }}"
                                    placeholder="{{ $oc_ar ? 'الحي / المنطقة' : 'District / area' }}" autocomplete="address-level3">
                            </div>
                        </div>
                        <div class="oc-field">
                            <label>{{ $oc_ar ? 'الموقع (GPS)' : 'Location (GPS)' }}</label>
                            <div class="oc-gps">
                                <button type="button" class="oc-gps-btn" id="gpsBtn"><i class="fa-solid fa-location-dot"></i> {{ $oc_ar ? 'تحديد موقعي' : 'Detect my location' }}</button>
                                <span class="oc-gps-status" id="gpsStatus">{{ $oc_ar ? 'اختياري — يساعد العملاء في العثور عليك' : 'Optional — helps customers find you' }}</span>
                            </div>
                            <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') }}">
                            <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}">
                            <div id="mapWrap" style="display:none;margin-top:12px;border:1px solid #d9e0d4;border-radius:12px;overflow:hidden;">
                                <iframe id="mapFrame" title="Store location" style="width:100%;height:200px;border:0;display:block;" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                            </div>
                        </div>
                        <div class="oc-field">
                            <label>{{ trans('labels.personlized_link') }} <span class="req">*</span></label>
                            <div class="oc-slug">
                                <span class="pfx">{{ URL::to('/') }}/</span>
                                <input type="text" id="slug" name="slug" value="{{ old('slug') }}" placeholder="my-store">
                            </div>
                            <span class="oc-err" id="err-slug" style="{{ $errors->has('slug') ? '' : 'display:none;' }}">{{ $errors->first('slug') }}</span>
                            <span class="oc-err" id="err1" style="display:none;">Please fill your name, a valid email, mobile, password and store link.</span>
                        </div>
                        <div class="oc-actions">
                            <button type="button" class="oc-btn oc-btn--ghost" data-back="1">&larr; Back</button>
                            <button type="button" class="oc-btn oc-btn--primary" data-next="3">Continue &rarr;</button>
                        </div>
                    </div>

                    {{-- ============ STEP 3: BUSINESS TYPE ============ --}}
                    <div class="oc-step" data-step="3">
                        <p class="oc-sub" style="margin:0 0 16px;">What kind of business is this? Pick one — you can change it later.</p>
                        <div class="oc-types">
                            @php
                                // 'sys' ties each business type to its System, so step 3 only shows the
                                // types that belong to the System chosen in step 1.
                                $ocTypes = [
                                    ['key' => 'food',     'sys' => 'orders',  'emo' => '🍔', 'nm' => 'Food',     'ds' => 'Restaurants, cafes, bakeries, juices'],
                                    ['key' => 'grocery',  'sys' => 'orders',  'emo' => '🛒', 'nm' => 'Grocery',   'ds' => 'Groceries, supermarkets, fruit & veg, mini-marts'],
                                    ['key' => 'pharmacy', 'sys' => 'orders',  'emo' => '💊', 'nm' => 'Pharmacy',  'ds' => 'Pharmacies, drugstores, medical supplies'],
                                    ['key' => 'retail',   'sys' => 'orders',  'emo' => '🛍️', 'nm' => 'Retail',   'ds' => 'Fashion, gifts, flowers, electronics'],
                                    ['key' => 'clinic',   'sys' => 'booking', 'emo' => '🩺', 'nm' => 'Clinic & Hospital', 'ds' => 'Clinics, hospitals, doctors, dental'],
                                    ['key' => 'salon',    'sys' => 'booking', 'emo' => '💇', 'nm' => 'Salon & Beauty', 'ds' => 'Salons, barbers, spa, nails, beauty'],
                                    ['key' => 'booking',  'sys' => 'booking', 'emo' => '📅', 'nm' => 'Booking',   'ds' => 'Hotels, gyms, venues, rentals'],
                                    ['key' => 'service',  'sys' => 'service', 'emo' => '🧰', 'nm' => 'Service Request', 'ds' => 'Cleaning, maintenance, plumbing'],
                                ];
                            @endphp
                            @foreach ($ocTypes as $t)
                                <div class="oc-type" data-type="{{ $t['key'] }}" data-sys="{{ $t['sys'] }}">
                                    <span class="tick">&#10003;</span>
                                    <div class="emo">{{ $t['emo'] }}</div>
                                    <div class="nm">{{ $t['nm'] }}</div>
                                    <div class="ds">{{ $t['ds'] }}</div>
                                </div>
                            @endforeach
                        </div>
                        <div class="oc-actions">
                            <button type="button" class="oc-btn oc-btn--ghost" data-back="2">&larr; Back</button>
                            <button type="button" class="oc-btn oc-btn--primary" id="toStep3" data-next="4" disabled>Continue &rarr;</button>
                        </div>
                    </div>

                    {{-- ============ STEP 4: TEMPLATE ============ --}}
                    <div class="oc-step" data-step="4">
                        <p class="oc-sub" style="margin:0 0 16px;">Choose your store design. This is how customers will see your store.</p>
                        @php
                            // Store-design picker. Classification (system + applicable activity +
                            // storefront template number) lives on the theme row, so there is no
                            // name map to keep in sync — see admin > Template Images.
                            $pickerThemes = \App\Models\Theme::orderBy('reorder_id')->get()
                                ->filter(fn($t) => $t->hasPreview())
                                ->values();
                            // Which business types each system covers, so the picker can narrow
                            // as the merchant moves through the steps.
                            $ocSysTypes = [
                                'orders'  => 'food,grocery,pharmacy,retail',
                                'booking' => 'clinic,salon,booking',
                                'service' => 'service',
                            ];
                        @endphp
                        <div class="oc-tpls" id="ocTpls">
                            @foreach ($pickerThemes as $i => $theme)
                                <div class="oc-tpl {{ $i == 0 ? 'sel' : '' }}"
                                    data-for="{{ $ocSysTypes[$theme->system] ?? 'food,grocery,retail,booking,service' }}"
                                    data-systemkey="{{ $theme->system }}"
                                    data-template="{{ $theme->template ?: 2 }}">
                                    <div class="oc-tpl__img" style="background-image:url('{{ helper::image_path($theme->image) }}');">@if ($i == 0)<span class="picked">✓ Selected</span>@endif</div>
                                    <div class="oc-tpl__cap"><span class="nm">{{ $theme->name }}</span><span class="badge2">Ready</span></div>
                                </div>
                            @endforeach
                        </div>
                        <p class="oc-note" id="ocTypeNote"></p>

                        @include('landing.layout.recaptcha')

                        <div class="oc-terms">
                            <input class="form-check-input p-0" type="checkbox" name="check_terms" id="check_terms" checked required>
                            <label for="check_terms">{{ trans('labels.i_accept_the') }}
                                <a href="{{ URL::to('/terms_condition') }}" target="_blank">{{ trans('labels.terms') }}</a>
                            </label>
                        </div>

                        <div class="oc-actions">
                            <button type="button" class="oc-btn oc-btn--ghost" data-back="3">&larr; Back</button>
                            <button class="oc-btn oc-btn--primary"
                                @if (env('Environment') == 'sendbox') type="button" onclick="myFunction()" @else type="submit" @endif>
                                Create my store
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        var areaurl = "{{ URL::to('admin/getarea') }}";
        var select = "{{ trans('labels.select') }}";
        var areaid = '0';
    </script>
    <script src="{{ url(env('ASSETSPATHURL') . '/admin-assets/js/user.js') }}"></script>
    <script>
        // City/area are free-text now — drop the legacy dropdown→area AJAX from user.js on this page.
        if (window.jQuery) { jQuery('#city').off('change'); }
        (function () {
            var form = document.getElementById('ocForm');
            var steps = form.querySelectorAll('.oc-step');
            var dots = document.querySelectorAll('.oc-steps .st');

            function showStep(n) {
                steps.forEach(function (s) { s.classList.toggle('show', s.getAttribute('data-step') == n); });
                dots.forEach(function (d) {
                    var dn = parseInt(d.getAttribute('data-dot'), 10);
                    d.classList.toggle('active', dn == n);
                    d.classList.toggle('done', dn < n);
                });
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }

            function validEmail(v) { return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v); }

            function validateStep1() {
                var name = document.getElementById('name');
                var email = document.getElementById('email');
                var mobile = document.getElementById('mobile');
                var pass = document.getElementById('password');
                var slug = document.getElementById('slug');
                var ok = name.value.trim() && validEmail(email.value.trim()) && mobile.value.trim()
                    && (!pass || pass.value.trim()) && slug.value.trim();
                document.getElementById('err1').style.display = ok ? 'none' : 'block';
                return ok;
            }

            // Next / Back
            form.querySelectorAll('[data-next]').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var target = this.getAttribute('data-next');
                    if (target === '2' && !validateSystem()) return;
                    if (target === '3' && !validateStep1()) return;
                    showStep(target);
                });
            });
            form.querySelectorAll('[data-back]').forEach(function (btn) {
                btn.addEventListener('click', function () { showStep(this.getAttribute('data-back')); });
            });

            // System selection (step 1). The chosen system decides which business types are
            // offered in step 3, and which plans the merchant is shown after registering.
            function validateSystem() {
                var ok = !!document.getElementById('system').value;
                document.getElementById('errSys').style.display = ok ? 'none' : 'block';
                return ok;
            }

            // Show only the business types that belong to the chosen system, and clear any
            // selection that no longer applies.
            function ocFilterTypes(sys) {
                var chosen = document.getElementById('business_type');
                var stillValid = false;
                document.querySelectorAll('.oc-type[data-type]').forEach(function (card) {
                    var show = card.getAttribute('data-sys') === sys;
                    card.style.display = show ? '' : 'none';
                    if (!show && card.classList.contains('sel')) {
                        card.classList.remove('sel');
                    } else if (show && card.classList.contains('sel')) {
                        stillValid = true;
                    }
                });
                if (!stillValid) {
                    chosen.value = '';
                    document.getElementById('toStep3').disabled = true;
                    var note = document.getElementById('ocTypeNote');
                    if (note) note.textContent = '';
                }
            }

            document.querySelectorAll('.oc-type[data-systemkey]').forEach(function (card) {
                card.addEventListener('click', function () {
                    document.querySelectorAll('.oc-type[data-systemkey]').forEach(function (c) { c.classList.remove('sel'); });
                    this.classList.add('sel');
                    document.getElementById('system').value = this.getAttribute('data-systemkey');
                    document.getElementById('toStep2').disabled = false;
                    document.getElementById('errSys').style.display = 'none';
                    ocFilterTypes(this.getAttribute('data-systemkey'));
                });
            });

            // Pre-select when the merchant arrived from a plan page (/admin/register?system=booking),
            // and restore every card selection after a failed submit so nothing looks unpicked.
            var ocPreset = document.getElementById('system').value;
            if (ocPreset) {
                var ocPresetCard = document.querySelector('.oc-type[data-systemkey="' + ocPreset + '"]');
                if (ocPresetCard) ocPresetCard.click();
            }

            (function restoreSelections() {
                var savedType = document.getElementById('business_type').value;
                if (savedType) {
                    var typeCard = document.querySelector('.oc-type[data-type="' + savedType + '"]');
                    if (typeCard && !typeCard.hidden) {
                        document.querySelectorAll('.oc-type[data-type]').forEach(function (c) { c.classList.remove('sel'); });
                        typeCard.classList.add('sel');
                        document.getElementById('business_type').value = savedType;
                        document.getElementById('toStep3').disabled = false;
                        var note = document.getElementById('ocTypeNote');
                        if (note) note.textContent = typeNotes[savedType] || '';
                        ocFilterTemplates(savedType);
                    }
                }

                var savedTpl = document.getElementById('template').value;
                if (savedTpl) {
                    var tplCard = document.querySelector('.oc-tpl[data-template="' + savedTpl + '"]');
                    if (tplCard && tplCard.style.display !== 'none') {
                        document.querySelectorAll('.oc-tpl').forEach(function (t) { t.classList.remove('sel'); });
                        tplCard.classList.add('sel');
                        document.getElementById('template').value = savedTpl;
                    }
                }
            })();

            // Final guard on submit — the merchant can jump steps with the browser's back button.
            form.addEventListener('submit', function (e) {
                if (!validateSystem()) { e.preventDefault(); showStep(1); return; }
                if (!validateStep1()) { e.preventDefault(); showStep(2); return; }
                if (!document.getElementById('business_type').value) { e.preventDefault(); showStep(3); }
            });

            // Business type selection
            var typeNotes = {
                food: 'Food ordering store — menu, categories, cart and WhatsApp checkout.',
                grocery: 'Grocery store — products, categories, cart and delivery.',
                pharmacy: 'Pharmacy / drugstore — medicines, categories, cart and delivery.',
                salon: 'Salon / beauty — services, specialists and appointment booking.',
                clinic: 'Clinic / hospital — doctors, departments and appointment booking.',
                retail: 'Retail store — products with variants, cart and checkout.',
                booking: 'Booking business — customers request an appointment (booking module rolling out).',
                service: 'Service requests — customers send a request/quote (service module rolling out).'
            };
            document.querySelectorAll('.oc-type[data-type]').forEach(function (card) {
                card.addEventListener('click', function () {
                    document.querySelectorAll('.oc-type[data-type]').forEach(function (c) { c.classList.remove('sel'); });
                    this.classList.add('sel');
                    var t = this.getAttribute('data-type');
                    document.getElementById('business_type').value = t;
                    document.getElementById('toStep3').disabled = false;
                    var note = document.getElementById('ocTypeNote');
                    if (note) note.textContent = typeNotes[t] || '';
                    ocFilterTemplates(t);
                });
            });

            // Show the themes that fit the chosen business type, select the first.
            // If no theme is tagged for this type, fall back to showing all available themes.
            function ocFilterTemplates(type) {
                var tpls = Array.prototype.slice.call(document.querySelectorAll('#ocTpls .oc-tpl'));
                var chosenSystem = document.getElementById('system').value;
                var matches = tpls.filter(function (tpl) {
                    // A template must belong to the purchased system AND suit the business type.
                    if (chosenSystem && tpl.getAttribute('data-systemkey') !== chosenSystem) return false;
                    return (tpl.getAttribute('data-for') || '').split(',').indexOf(type) !== -1;
                });
                var visible = matches.length ? matches : tpls;
                var firstVisible = null;
                tpls.forEach(function (tpl) {
                    var show = visible.indexOf(tpl) !== -1;
                    tpl.style.display = show ? '' : 'none';
                    tpl.classList.remove('sel');
                    if (show && !firstVisible) firstVisible = tpl;
                });
                if (firstVisible) {
                    firstVisible.classList.add('sel');
                    document.getElementById('template').value = firstVisible.getAttribute('data-template');
                }
            }

            // Template selection
            document.querySelectorAll('.oc-tpl').forEach(function (tpl) {
                tpl.addEventListener('click', function () {
                    if (this.classList.contains('disabled')) return;
                    document.querySelectorAll('.oc-tpl').forEach(function (t) { t.classList.remove('sel'); });
                    this.classList.add('sel');
                    document.getElementById('template').value = this.getAttribute('data-template');
                });
            });

            // Keep the phone country-code in sync when the Country is changed.
            var countrySel = document.getElementById('country');
            var codeSel = document.getElementById('country_code');
            if (countrySel && codeSel) {
                countrySel.addEventListener('change', function () {
                    var dial = this.options[this.selectedIndex].getAttribute('data-dial');
                    if (dial) {
                        for (var i = 0; i < codeSel.options.length; i++) {
                            if (codeSel.options[i].value === dial) { codeSel.selectedIndex = i; break; }
                        }
                    }
                });
            }

            // GPS: detect the merchant's current location.
            var gpsBtn = document.getElementById('gpsBtn');
            if (gpsBtn) {
                gpsBtn.addEventListener('click', function () {
                    var status = document.getElementById('gpsStatus');
                    if (!navigator.geolocation) {
                        status.textContent = '{{ $oc_ar ? "الموقع غير مدعوم" : "GPS not supported on this device." }}';
                        status.className = 'oc-gps-status err';
                        return;
                    }
                    status.textContent = '{{ $oc_ar ? "جارٍ التحديد…" : "Detecting…" }}';
                    status.className = 'oc-gps-status';
                    navigator.geolocation.getCurrentPosition(function (pos) {
                        var lat = pos.coords.latitude, lng = pos.coords.longitude;
                        document.getElementById('latitude').value = lat.toFixed(6);
                        document.getElementById('longitude').value = lng.toFixed(6);
                        status.innerHTML = '{{ $oc_ar ? "✓ تم تحديد الموقع" : "✓ Location detected" }} (' + lat.toFixed(4) + ', ' + lng.toFixed(4) + ')';
                        status.className = 'oc-gps-status ok';

                        // Show a free embedded map (OpenStreetMap — no API key needed).
                        var wrap = document.getElementById('mapWrap'), frame = document.getElementById('mapFrame');
                        if (wrap && frame) {
                            var d = 0.01;
                            frame.src = 'https://www.openstreetmap.org/export/embed.html?bbox=' +
                                (lng - d) + ',' + (lat - d) + ',' + (lng + d) + ',' + (lat + d) +
                                '&layer=mapnik&marker=' + lat + ',' + lng;
                            wrap.style.display = 'block';
                        }

                        // Reverse-geocode to auto-fill city / area / country (free, no key).
                        var lang = '{{ app()->getLocale() === "ar" ? "ar" : "en" }}';
                        fetch('https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=' + lat + '&lon=' + lng + '&accept-language=' + lang, {
                            headers: { 'Accept': 'application/json' }
                        }).then(function (r) { return r.json(); }).then(function (g) {
                            var a = g.address || {};
                            var cityVal = a.city || a.town || a.village || a.municipality || a.state_district || a.county || '';
                            var areaVal = a.suburb || a.neighbourhood || a.city_district || a.quarter || a.residential || a.road || '';
                            var countryVal = a.country || '';
                            var cityEl = document.getElementById('city'), areaEl = document.getElementById('area');
                            if (cityEl && cityVal && !cityEl.value) cityEl.value = cityVal;
                            if (areaEl && areaVal && !areaEl.value) areaEl.value = areaVal;
                            // Country is a <select> — pick the matching option (also updates the dial code via its change handler).
                            var countrySel = document.getElementById('country');
                            if (countrySel && countryVal) {
                                for (var i = 0; i < countrySel.options.length; i++) {
                                    if (countrySel.options[i].text.toLowerCase().indexOf(countryVal.toLowerCase()) !== -1 ||
                                        (countrySel.options[i].value || '').toLowerCase() === countryVal.toLowerCase()) {
                                        countrySel.selectedIndex = i;
                                        countrySel.dispatchEvent(new Event('change'));
                                        break;
                                    }
                                }
                            }
                        }).catch(function () { /* geocoding is best-effort; ignore failures */ });
                    }, function (err) {
                        status.textContent = (err.code === 1)
                            ? '{{ $oc_ar ? "تم رفض إذن الموقع" : "Location permission denied." }}'
                            : '{{ $oc_ar ? "تعذر تحديد الموقع، حاول مرة أخرى" : "Could not get location, try again." }}';
                        status.className = 'oc-gps-status err';
                    }, { enableHighAccuracy: true, timeout: 10000 });
                });
            }

            // ---- Live availability: catch a duplicate email / mobile / store link as it is typed,
            // instead of letting the merchant finish every step and lose the form on submit. ----
            var checkUrl = '{{ URL::to('admin/register/check') }}';
            var taken = { email: false, mobile: false, slug: false };

            function showFieldError(field, message) {
                var el = document.getElementById('err-' + field);
                if (!el) return;
                el.textContent = message || '';
                el.style.display = message ? 'block' : 'none';
                var input = document.getElementById(field);
                if (input) input.style.borderColor = message ? '#d64545' : '';
            }

            function checkField(field) {
                var input = document.getElementById(field);
                if (!input) return;
                var value = input.value.trim();
                if (!value) { taken[field] = false; showFieldError(field, ''); return; }

                fetch(checkUrl + '?field=' + field + '&value=' + encodeURIComponent(value), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(function (r) { return r.json(); })
                    .then(function (d) {
                        taken[field] = d.status === 0;
                        showFieldError(field, d.status === 0 ? d.message : '');
                    })
                    .catch(function () { /* availability is advisory; the server still validates */ });
            }

            ['email', 'mobile', 'slug'].forEach(function (field) {
                var input = document.getElementById(field);
                if (!input) return;
                var timer = null;
                input.addEventListener('input', function () {
                    clearTimeout(timer);
                    timer = setTimeout(function () { checkField(field); }, 450);
                });
                input.addEventListener('blur', function () { checkField(field); });
            });

            // Block the step-2 "Continue" while a value is known to be taken.
            var _validateStep1 = validateStep1;
            validateStep1 = function () {
                var ok = _validateStep1();
                if (!ok) return false;
                if (taken.email || taken.mobile || taken.slug) {
                    ['email', 'mobile', 'slug'].forEach(function (f) {
                        if (taken[f]) checkField(f);
                    });
                    return false;
                }
                return true;
            };

            // ---- After a failed submit, reopen the step that has the problem with the values
            // still in place, and focus the offending field. ----
            @if (session('focus_field'))
                (function () {
                    showStep(2);
                    var field = '{{ session('focus_field') }}';
                    var input = document.getElementById(field);
                    if (input) { input.focus(); input.style.borderColor = '#d64545'; }
                    taken[field] = true;
                })();
            @elseif ($errors->any() || old('name'))
                // Any other validation bounce: keep the merchant on the account step.
                showStep(2);
            @endif

            // Optional deep-link to a step (e.g. /admin/register#business)
            var startMap = { '#account': '2', '#business': '3', '#template': '4' };
            if (startMap[location.hash]) { showStep(startMap[location.hash]); }
        })();
    </script>
@endsection
