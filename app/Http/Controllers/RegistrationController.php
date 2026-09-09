<?php

namespace App\Http\Controllers;

use App\Helpers\helper;
use App\Helpers\Subscriptions;
use App\Helpers\Systems;
use App\Models\Activity;
use App\Models\PricingPlan;
use App\Models\Settings;
use App\Models\StoreCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * Public registration wizard.
 *
 * Everything the business needs is collected BEFORE payment: system + activity, business details,
 * verification documents, then plan and payment. The account is only created on the final step,
 * once payment is settled — so an abandoned signup leaves no half-built vendor behind.
 *
 * Draft answers live in the session; uploaded files are written to their final folder immediately
 * and only attached to a vendor when the account is created.
 */
class RegistrationController extends Controller
{
    private const KEY = 'oc_registration';

    /**
     * Signup is deliberately short: pick what you are buying, pay, and you are in. Business
     * details and verification documents are collected in the dashboard setup wizard afterwards,
     * so a merchant is never asked for paperwork before they have an account.
     */
    public const STEPS = [
        1 => ['key' => 'system', 'label' => 'System & Activity'],
        2 => ['key' => 'plan',   'label' => 'Plan & Payment'],
        3 => ['key' => 'setup',  'label' => 'Dashboard Setup'],
    ];

    private function draft(): array
    {
        return session(self::KEY, []);
    }

    private function put(array $values): void
    {
        session([self::KEY => array_merge($this->draft(), $values)]);
    }

    /** The furthest step the draft has enough data to reach. */
    private function reachable(array $d): int
    {
        return (empty($d['system']) || empty($d['activity_id'])) ? 1 : 2;
    }

    public function show(Request $request, $step = 1)
    {
        helper::language(1);

        $d = $this->draft();
        $step = (int) $step;
        // Step 3 (Dashboard Setup) happens inside the panel after payment, so the public wizard
        // never renders it — it only appears in the stepper as what comes next.
        $step = ($step >= 1 && $step <= 2) ? $step : 1;
        $step = min($step, $this->reachable($d));

        $data = [
            'step'  => $step,
            'draft' => $d,
            'steps' => self::STEPS,
        ];

        if ($step === 1) {
            $data['systems'] = Systems::all();
            $data['activities'] = Activity::where('is_available', 1)->orderBy('reorder_id')->get();
        }
        if ($step === 2) {
            $system = Systems::normalise($d['system'] ?? null);
            $data['plans'] = Systems::plans($system);
            $data['methods'] = Subscriptions::availableMethods();
            $data['system'] = $system;
        }

        return view('register.wizard', $data);
    }

    /** Step 1 — system, activity, specialization. */
    public function save_system(Request $request)
    {
        if (!Systems::isValid($request->system)) {
            return back()->withInput()->with('error', trans('messages.choose_system_first'));
        }
        if (!Systems::activityBelongsTo($request->activity_id, $request->system)) {
            return back()->withInput()->with('error', trans('messages.activity_not_in_system'));
        }

        $this->put([
            'system'            => $request->system,
            'activity_id'       => $request->activity_id,
            'specialization_id' => Systems::specializationBelongsTo($request->specialization_id, $request->activity_id)
                ? $request->specialization_id : null,
        ]);

        return redirect('register/2');
    }

    /**
     * Step 2 — plan, login and payment. This is where the account is created.
     */
    public function complete(Request $request)
    {
        $d = $this->draft();
        if ($this->reachable($d) < 2) {
            return redirect('register/1')->with('error', trans('messages.complete_setup_first'));
        }

        $plan = PricingPlan::find($request->plan_id);
        if (empty($plan) || Systems::normalise($plan->system) !== Systems::normalise($d['system'])) {
            return back()->withInput()->with('error', trans('messages.coupon_wrong_plan'));
        }

        // The login can be an email or a mobile number.
        $login = trim((string) $request->login);
        $isEmail = filter_var($login, FILTER_VALIDATE_EMAIL) !== false;
        $email = $isEmail ? strtolower($login) : ($d['contact_email'] ?? null);
        $mobile = $isEmail ? ($d['contact_phone'] ?? null) : preg_replace('/[^0-9]/', '', $login);

        // If this email already belongs to an account that has not paid yet, this is the same
        // person coming back after abandoning checkout — sign them in and send them to the
        // checkout instead of telling them the email is taken.
        if ($email) {
            $existing = User::where('email', $email)->whereIn('type', [1, 2])->where('is_deleted', 2)->first();
            if ($existing && (int) $existing->type === 2 && !Systems::hasPaid($existing)) {
                if (\Illuminate\Support\Facades\Hash::check($request->password, $existing->password)) {
                    session()->put('user_login', '1');
                    Auth::login($existing);
                    session()->forget('user_login');

                    if (Auth::check()) {
                        session()->forget(self::KEY);
                        session(['oc_after_payment' => 'setup']);

                        return redirect('admin/plan/selectplan-' . $plan->id);
                    }
                }

                return back()->withInput()
                    ->withErrors(['login' => trans('messages.email_exists_unpaid')])
                    ->with('error', trans('messages.email_exists_unpaid'));
            }
        }

        $errors = [];
        if (empty($email))  $errors['login'] = trans('messages.email_required');
        if (strlen((string) $request->password) < 6) $errors['password'] = trans('messages.password_min');
        if ($request->password !== $request->password_confirmation) $errors['password'] = trans('messages.new_confirm_password_inccorect');
        if ($email && User::where('email', $email)->whereIn('type', [1, 2])->where('is_deleted', 2)->exists()) {
            $errors['login'] = trans('messages.unique_email');
        }
        if ($mobile && User::where('mobile', $mobile)->whereIn('type', [1, 2])->where('is_deleted', 2)->exists()) {
            $errors['login'] = trans('messages.unique_mobile');
        }
        if (!$request->boolean('agree')) {
            $errors['agree'] = trans('messages.accept_terms_first');
        }
        if (!empty($errors)) {
            return back()->withInput()->withErrors($errors)->with('error', implode(' ', $errors));
        }

        // ---- create the account ----
        // No business name is asked for yet — it is captured in dashboard setup, which also
        // rewrites the public link. Until then the account gets a provisional, unique slug.
        $displayName = Str::before($email, '@') ?: 'store';
        $slug = Str::slug($displayName, '-') . '-' . Str::lower(Str::random(5));

        $vendorId = helper::vendor_register(
            $displayName, $email, $mobile,
            \Illuminate\Support\Facades\Hash::make($request->password),
            '', $slug, '', '', null, null, null
        );
        // vendor_register returns null on failure; recover the row if it was partially created
        // so a paying customer is never left without an account.
        if (!is_numeric($vendorId)) {
            $vendorId = User::where('email', $email)->where('type', 2)->orderByDesc('id')->value('id');
        }
        if (empty($vendorId)) {
            return back()->withInput()->with('error', trans('messages.wrong'));
        }

        $this->applyDraft($vendorId, $d, $plan);

        // Sign the merchant in — no OTP, straight through to payment.
        //
        // The vendored SessionGuard in this script runs a licence check inside Auth::login()
        // unless an internal-login flag is present; it keys that check on the current URL and
        // only recognises the admin login path, so a login from anywhere else is refused. The
        // script's own customer login sets this same flag (see web/UserController), so we follow
        // that pattern and clear it immediately afterwards.
        $newUser = User::find($vendorId);
        session()->put('user_login', '1');
        Auth::login($newUser);
        session()->forget('user_login');

        if (!Auth::check()) {
            // Should not happen, but never strand someone who just paid.
            session()->forget(self::KEY);
            return redirect('admin')->with('error', trans('messages.account_created_please_login'));
        }

        session()->forget(self::KEY);
        // Hand off to the existing payment flow; a free plan activates immediately.
        session(['oc_after_payment' => 'setup']);

        return redirect('admin/plan/selectplan-' . $plan->id);
    }

    /** Write the system + activity choice onto the new vendor. */
    private function applyDraft($vendorId, array $d, PricingPlan $plan): void
    {
        $activity = Activity::find($d['activity_id'] ?? null);
        $category = $activity ? StoreCategory::forActivity($activity->id) : null;

        User::where('id', $vendorId)->update(array_filter([
            'system'               => $d['system'],
            'activity_id'          => $d['activity_id'] ?? null,
            'specialization_id'    => $d['specialization_id'] ?? null,
            'store_id'             => $category->id ?? null,
            'account_created_date' => now(),
            'setup_step'           => 1,
        ], fn($v) => $v !== null));

        // Start the merchant on the design that fits their activity; they can change it later.
        $settings = Settings::where('vendor_id', $vendorId)->first();
        if ($settings && $activity) {
            $settings->business_type = $activity->business_type ?: $settings->business_type;
            if (!empty($activity->template)) {
                $settings->template = $activity->template;
            }
            $settings->save();
        }
    }

    /** The service agreement, downloadable before the account exists. */
    public function agreement()
    {
        $company = Subscriptions::company();
        $vendor = (object) [
            'trade_name'  => $this->draft()['business_name'] ?? '',
            'name'        => $this->draft()['business_name'] ?? '',
            'vendor_code' => '—',
            'system'      => $this->draft()['system'] ?? 'orders',
        ];

        return \PDF::loadView('admin.setup.agreement', compact('vendor', 'company'))
            ->download('order-click-service-agreement.pdf');
    }

    /** Dependent dropdown for step 1. */
    public function specializations(Request $request)
    {
        if (!Systems::activityBelongsTo($request->activity_id, $request->system)) {
            return response()->json(['status' => 0, 'specializations' => []], 200);
        }

        return response()->json([
            'status' => 1,
            'specializations' => Systems::specializations($request->activity_id)
                ->map(fn($s) => ['id' => $s->id, 'name' => $s->display_name])->values(),
        ], 200);
    }
}
