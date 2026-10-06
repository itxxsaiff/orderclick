<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use App\Helpers\helper;

class FrontMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!file_exists(storage_path() . "/installed")) {
            return redirect('install');
            exit;
        }

        // if the current host contains the website domain
        $host = $_SERVER['HTTP_HOST'];
        if ($host  ==  env('WEBSITE_HOST')) {

            $user = User::where('slug', $request->vendor)->first();

            if ($request->vendor != "" || $request->vendor != null) {
                if (empty($user)) {
                    abort(404);
                }
                helper::language($user->id);
                if (helper::otherappdata($user->id)->maintenance_on_off == 1) {
                    
                    return response(view('errors.maintenance'));
                }
                // V2: a paid store stays private until the merchant activates their website.
                // The owner (and their staff) can still preview it while it is in setup — that is
                // when they design it — so the plan check below does not lock them out.
                $ownerInSetup = false;
                if (!\App\Helpers\Systems::isLive($user) && \App\Helpers\Systems::hasPaid($user)) {
                    $viewer = \Illuminate\Support\Facades\Auth::user();
                    $isOwner = $viewer && (($viewer->id == $user->id) || ($viewer->vendor_id == $user->id));
                    if (!$isOwner) {
                        return response(view('errors.notactivated', ['storeinfo' => $user]));
                    }
                    $ownerInSetup = true;
                }
                $checkplan = helper::checkplan($user->id, '3');
                $v = json_decode(json_encode($checkplan));
                if (@$v->original->status == 2 && !$ownerInSetup) {
                    return response(view('errors.accountdeleted'));
                }
                if ($user->is_available == 2) {
                    return response(view('errors.accountdeleted'));
                }
            }
        }
        // if the current host doesn't contain the website domain (meaning, custom domain)
        else {
            // if the current package doesn't have 'custom domain' feature || the custom domain is not connected
            $settingdata = User::where('custom_domain', $host)->first();
            helper::language(@$settingdata->id);
            if (@$settingdata->id != "" || @$settingdata->id != null) {
                $user = User::where('id', @$settingdata->id)->first();
                if (empty($user)) {
                    abort(404);
                }
                if (helper::appdata($user->id)->maintenance_mode == 1) {
                    return response(view('errors.maintenance'));
                }
                if ($user->is_available == 2) {
                    return response(view('errors.accountdeleted'));
                }
            }
        }
        return $next($request);
    }
}
