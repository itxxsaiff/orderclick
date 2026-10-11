<?php

namespace App\Http\Controllers\addons\included;

use App\helper\helper;
use App\Http\Controllers\Controller;
use App\Models\Currencies;
use App\Models\CurrencySettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CurrencyController extends Controller
{
    public function index(Request $request)
    {
        // dd('123');
        if (Auth::user()->type == 2 || (Auth::user()->type == 4 && Auth::user()->vendor_id != 1)) {
            $getcurrency = CurrencySettings::where('is_available', 1)->get();
        } else {
            $getcurrency = CurrencySettings::get();
        }
        
        return view('admin.currency_settings.index', compact("getcurrency"));
    }
    private function isPlatformAdmin(): bool
    {
        return Auth::user()->type == 1 || (Auth::user()->type == 4 && Auth::user()->vendor_id == 1);
    }

    public function edit(Request $request)
    {
        abort_unless($this->isPlatformAdmin(), 403);
        $editcurrency = CurrencySettings::where('id', $request->id)->first();
        $currencies = Currencies::where('is_available', 1)->get();
        return view('admin.currency_settings.edit', compact("editcurrency","currencies"));
    }

    public function update(Request $request, $id)
    {
        abort_unless($this->isPlatformAdmin(), 403);
        try {
            $currency = CurrencySettings::where('id', $id)->first();
            $currency->currency = $request->currency;
            $currency->exchange_rate = $request->exchange_rate;
            $currency->currency_position = $request->currency_position;
            $currency->currency_space = $request->currency_space;
            $currency->currency_formate = $request->currency_formate;
            $currency->decimal_separator = $request->decimal_separator;
            $currency->update();
            return redirect('admin/currency-settings')->with('success', trans('messages.success'));
        } catch (\Throwable $th) {
            return redirect('admin/currency-settings')->with('error', trans('messages.wrong'));
        }
    }

    /** The store this account manages (staff act for their vendor). */
    private function vendorId(): int
    {
        return (int) (Auth::user()->type == 4 ? Auth::user()->vendor_id : Auth::user()->id);
    }

    /**
     * Make a currency this store's currency (prices are shown in it). For the super admin this is
     * the platform default, which new stores start with.
     */
    public function setdefault(Request $request)
    {
        $currency = CurrencySettings::where('code', $request->code)->where('is_available', 1)->first();
        if (!$currency) {
            return redirect('admin/currency-settings')->with('error', trans('messages.wrong'));
        }
        $settings = \App\Models\Settings::where('vendor_id', $this->vendorId())->first();
        $settings->default_currency = $currency->code;
        // The store's own currency is always one customers can see.
        $list = array_filter(explode('|', (string) $settings->currencies));
        if (!in_array($currency->code, $list, true)) {
            $list[] = $currency->code;
        }
        $settings->currencies = implode('|', $list);
        $settings->save();
        \Illuminate\Support\Facades\Cookie::queue(\Illuminate\Support\Facades\Cookie::forget('code'));

        return redirect('admin/currency-settings')->with('success', trans('messages.success'));
    }

    /** Super admin: offer or withdraw a currency platform-wide (the platform default stays on). */
    public function changestatus(Request $request)
    {
        abort_unless($this->isPlatformAdmin(), 403);
        $currency = CurrencySettings::where('code', $request->code)->firstOrFail();
        $platformDefault = \App\Models\Settings::where('vendor_id', 1)->value('default_currency');
        if ($request->status == 2 && $currency->code === $platformDefault) {
            return redirect('admin/currency-settings')->with('error', trans('messages.currency_default_cannot_disable'));
        }
        $currency->is_available = $request->status == 1 ? 1 : 2;
        $currency->save();

        return redirect('admin/currency-settings')->with('success', trans('messages.success'));
    }

    /** Vendor: which extra currencies customers may switch to (the store currency always stays). */
    public function currencystatus(Request $request)
    {
        $settings = \App\Models\Settings::where('vendor_id', $this->vendorId())->first();
        $list = array_filter(explode('|', (string) $settings->currencies));
        if ($request->status == 1) {
            if (CurrencySettings::where('code', $request->code)->where('is_available', 1)->exists() && !in_array($request->code, $list, true)) {
                $list[] = $request->code;
            }
        } elseif ($request->code !== $settings->default_currency) {
            $list = array_values(array_diff($list, [$request->code]));
        }
        $settings->currencies = implode('|', $list);
        $settings->save();

        return redirect('admin/currency-settings')->with('success', trans('messages.success'));
    }

    /** Super admin: remove a currency. Stores that used it fall back to the platform currency. */
    public function delete(Request $request)
    {
        abort_unless($this->isPlatformAdmin(), 403);
        $currency = CurrencySettings::findOrFail($request->id);
        $platformDefault = \App\Models\Settings::where('vendor_id', 1)->value('default_currency');
        if ($currency->code === $platformDefault) {
            return redirect('admin/currency-settings')->with('error', trans('messages.currency_default_cannot_disable'));
        }
        \App\Models\Settings::where('default_currency', $currency->code)->update(['default_currency' => $platformDefault]);
        $currency->delete();

        return redirect('admin/currency-settings')->with('success', trans('messages.success'));
    }
}
