{{-- STEP 4 — Plan, login and payment. The account is created when this is submitted. --}}
<span class="ocw__pill">{{ trans('labels.step') }} 2 {{ $ar ? 'من' : 'of' }} 3</span>
<h1 class="ocw__title">{{ $ar ? 'اختر باقتك وافتح حسابك' : 'Choose your plan and open your account' }}</h1>
<p class="ocw__sub">
    {{ $ar ? 'المحدد:' : 'Selected:' }}
    <strong>{{ \App\Helpers\Systems::label($system) }}</strong>
    @php $ocAct = \App\Models\Activity::find($draft['activity_id'] ?? null); @endphp
    @if ($ocAct) &bull; <strong>{{ $ocAct->display_name }}</strong> @endif
</p>

<form method="POST" action="{{ URL::to('register/complete') }}">
    @csrf
    <input type="hidden" name="plan_id" id="ocwPlan" value="{{ old('plan_id') }}">

    {{-- Plans for the purchased system only. --}}
    @if ($plans->isEmpty())
        <div class="alert alert-warning">{{ trans('messages.no_plans_for_system') }}</div>
    @else
        <div class="ocw__grid g3">
            @foreach ($plans as $i => $p)
                @php $feat = array_filter(explode('|', (string) $p->features)); @endphp
                <div class="ocw__pick ocw-plan {{ (string) old('plan_id') === (string) $p->id ? 'sel' : '' }}" data-id="{{ $p->id }}">
                    <span class="tick"><i class="fa-solid fa-check"></i></span>
                    <h4>{{ $p->name }}
                        @if ($p->recommended == 1)<span class="ocw__badge">{{ $ar ? 'الأكثر شيوعاً' : 'Most popular' }}</span>@endif
                    </h4>
                    <p>{{ \Illuminate\Support\Str::limit(strip_tags($p->description), 60) }}</p>
                    <ul>
                        @foreach (array_slice($feat, 0, 3) as $f)
                            <li><i class="fa-solid fa-check"></i>{{ $f }}</li>
                        @endforeach
                    </ul>
                    <div class="ocw__price">
                        {{ number_format((float) $p->price, 2) }} {{ $p->currency ?: 'USD' }}
                        <span style="font-weight:500;color:#7d887f;">/ {{ $ar ? 'شهر' : 'month' }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <div class="row" style="margin-top:26px;">
        <div class="col-12 col-lg-6">
            <h6 style="font-weight:700;margin-bottom:14px;">{{ $ar ? 'أنشئ بيانات الدخول' : 'Create your login' }}</h6>
            <div class="fg">
                <label class="f">{{ $ar ? 'البريد الإلكتروني أو رقم الجوال' : 'Email address or mobile number' }}<span class="req"> *</span></label>
                <input type="text" class="in" name="login" value="{{ old('login', $draft['contact_email'] ?? '') }}" required
                    placeholder="name@example.com">
                @error('login')<span class="err">{{ $message }}</span>@enderror
            </div>
            <div class="row">
                <div class="col-12 col-md-6 fg">
                    <label class="f">{{ trans('labels.password') }}<span class="req"> *</span></label>
                    <div style="position:relative;">
                        <input type="password" class="in oc-pw" name="password" id="ocwPw1" required style="padding-right:44px;">
                        <button type="button" class="oc-eye" data-target="ocwPw1" aria-label="Show password"
                            style="position:absolute;top:0;{{ $ar ? 'left' : 'right' }}:0;height:46px;width:44px;border:0;background:none;color:#8a978d;cursor:pointer;">
                            <i class="fa-regular fa-eye"></i>
                        </button>
                    </div>
                    @error('password')<span class="err">{{ $message }}</span>@enderror
                </div>
                <div class="col-12 col-md-6 fg">
                    <label class="f">{{ $ar ? 'تأكيد كلمة المرور' : 'Confirm password' }}<span class="req"> *</span></label>
                    <div style="position:relative;">
                        <input type="password" class="in oc-pw" name="password_confirmation" id="ocwPw2" required style="padding-right:44px;">
                        <button type="button" class="oc-eye" data-target="ocwPw2" aria-label="Show password"
                            style="position:absolute;top:0;{{ $ar ? 'left' : 'right' }}:0;height:46px;width:44px;border:0;background:none;color:#8a978d;cursor:pointer;">
                            <i class="fa-regular fa-eye"></i>
                        </button>
                    </div>
                </div>
            </div>
            <small style="color:#8a978d;">{{ trans('messages.no_otp_note') }}</small>
        </div>

        <div class="col-12 col-lg-6">
            <h6 style="font-weight:700;margin-bottom:14px;">{{ $ar ? 'ماذا بعد؟' : 'What happens next' }}</h6>
            <div class="ocw__note" style="margin-top:0;">
                <i class="fa-solid fa-circle-check" style="margin-top:2px;"></i>
                <span>{{ trans('messages.after_account_checkout_note') }}</span>
            </div>

            <label style="display:flex;gap:9px;align-items:flex-start;margin-top:16px;font-size:13.5px;color:#39443c;">
                <input type="checkbox" name="agree" value="1" {{ old('agree') ? 'checked' : '' }} required style="margin-top:3px;">
                <span>{{ trans('messages.agree_terms_nonrefundable') }}</span>
            </label>
            @error('agree')<span class="err">{{ $message }}</span>@enderror
        </div>
    </div>

    <div class="ocw__actions">
        <a href="{{ URL::to('register/1') }}" class="ocw__btn ocw__btn--g">&larr; {{ trans('labels.back') }}</a>
        <span style="font-size:13px;color:#8a978d;text-align:center;">
            {{ trans('messages.already_paid_help') }}
            <a href="{{ URL::to('/') }}#contact" style="color:#1f9d55;font-weight:600;">{{ trans('labels.contact_support') }}</a>
        </span>
        <button type="submit" class="ocw__btn ocw__btn--p">
            {{ $ar ? 'إنشاء الحساب والمتابعة للدفع' : 'Create account & continue to checkout' }} &rarr;
        </button>
    </div>
    <div class="ocw__note">
        <i class="fa-solid fa-circle-check" style="margin-top:2px;"></i>
        <span>{{ trans('messages.after_payment_setup_note') }}</span>
    </div>
</form>

<script>
    // Show / hide password.
    document.querySelectorAll('.oc-eye').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var input = document.getElementById(this.dataset.target);
            var icon = this.querySelector('i');
            var show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            icon.className = show ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye';
        });
    });

    (function () {
        function pick(selector, hiddenId) {
            var hidden = document.getElementById(hiddenId);
            document.querySelectorAll(selector).forEach(function (el) {
                el.addEventListener('click', function () {
                    document.querySelectorAll(selector).forEach(function (o) { o.classList.remove('sel'); });
                    this.classList.add('sel');
                    hidden.value = this.getAttribute('data-id');
                });
                if (el.getAttribute('data-id') === hidden.value) el.classList.add('sel');
            });
        }
        pick('.ocw-plan', 'ocwPlan');

        // Default to the recommended plan and the first payment method.
        var plans = document.querySelectorAll('.ocw-plan');
        if (plans.length && !document.getElementById('ocwPlan').value) plans[0].click();

        document.querySelector('form').addEventListener('submit', function (e) {
            if (!document.getElementById('ocwPlan').value) { e.preventDefault(); alert('{{ trans('messages.select_plan_first') }}'); return; }
        });
    })();
</script>
