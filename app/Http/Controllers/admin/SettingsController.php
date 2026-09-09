<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Settings;
use App\Models\Transaction;
use App\Models\AppSettings;
use App\Models\SocialLinks;
use App\Helpers\helper;
use App\Models\Footerfeatures;
use App\Models\City;
use App\Models\Order;
use App\Models\OtherSettings;
use App\Models\Payment;
use App\Models\Pixcel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SettingsController extends Controller
{

    public function settings_index(Request $request)
    {
        if (Auth::user()->type == 4) {
            $vendor_id = Auth::user()->vendor_id;
        } else {
            $vendor_id = Auth::user()->id;
        }
        $settingdata = Settings::where('vendor_id', $vendor_id)->first();
        $othersettingdata = OtherSettings::where('vendor_id', $vendor_id)->first();
        $theme = Transaction::select('themes_id')->where('vendor_id', $vendor_id)->orderByDesc('id')->first();
        $getfooterfeatures = Footerfeatures::where('vendor_id', $vendor_id)->get();
        $city = City::where('is_deleted', 2)->where('is_available', 1)->get();
        $app = AppSettings::where('vendor_id',  $vendor_id)->first();
        $getsociallinks = SocialLinks::where('vendor_id', $vendor_id)->get();
        $order = Order::where('vendor_id', $vendor_id)->get();
        $pixelsettings = Pixcel::where('vendor_id', $vendor_id)->first();
        $getpayment = Payment::where('is_available', 1)->where('is_activate', '1')->where('vendor_id', $vendor_id)->orderBy('reorder_id')->get();
        return view('admin.otherpages.settings', compact('settingdata', 'othersettingdata', 'theme', 'getfooterfeatures', 'city', 'app', 'getsociallinks', 'order', 'pixelsettings', 'getpayment'));
    }
    public function settings_update(Request $request)
    {
        if (Auth::user()->type == 4) {
            $vendor_id = Auth::user()->vendor_id;
        } else {
            $vendor_id = Auth::user()->id;
        }
        $settingsdata = Settings::where('vendor_id', $vendor_id)->first();
        $userslug = User::where('id', $vendor_id)->first();

        if ($request->hasfile('notification_sound')) {
            if (env('Environment') == 'sendbox') {
                return $this->sendError("This operation was not performed due to demo mode");
            }
            $request->validate([
                'notification_sound' => 'mimes:mp3',

            ]);
            if (file_exists(storage_path('app/public/admin-assets/notification/' . $settingsdata->notification_sound))) {
                @unlink(storage_path('app/public/admin-assets/notification/' . $settingsdata->notification_sound));
            }
            $sound = 'audio-' . uniqid() . '.' . $request->notification_sound->getClientOriginalExtension();
            $request->notification_sound->move(storage_path('app/public/admin-assets/notification/'), $sound);
            $settingsdata->notification_sound = $sound;
        }

        

        $settingsdata->vendor_register = isset($request->vendor_register) ? 1 : 2;
        $settingsdata->timezone = $request->timezone;
        $settingsdata->firebase = '';
        $settingsdata->copyright = $request->copyright;
        $settingsdata->website_title = $request->website_title;
        $settingsdata->description = $request->description;
        $settingsdata->time_format = $request->time_format;
        $settingsdata->date_format = $request->date_format;
        $settingsdata->order_prefix = $request->order_prefix;
        $settingsdata->min_order_amount = $request->min_order_amount;
        // V2: WhatsApp number that customer orders are sent to (keys-free wa.me flow).
        if ($request->has('whatsapp_number')) {
            $settingsdata->whatsapp_number = $request->whatsapp_number;
        }
        // V2: store Google Maps link + GPS location (lat/lng live on the users table).
        // Company identity used on subscription invoices and by every tax rule.
        if ($request->has('company_legal_name')) {
            $settingsdata->company_legal_name = $request->company_legal_name;
        }
        if ($request->has('tax_registration_number')) {
            $settingsdata->tax_registration_number = $request->tax_registration_number;
        }
        if ($request->has('map_link')) {
            $settingsdata->map_link = $request->map_link;
        }
        if (is_numeric($request->latitude) && is_numeric($request->longitude)) {
            $userslug->latitude = $request->latitude;
            $userslug->longitude = $request->longitude;
            $userslug->save();
        }
        $order = Order::where('vendor_id', $vendor_id)->get();
        if ($order->count() == 0 && $request->order_number_start != null && $request->order_number_start != "") {
            $settingsdata->order_number_start = $request->order_number_start;
        }
        if ($request->delivery_type != null) {
            $settingsdata->delivery_type = implode(",", $request->delivery_type);
        }
        if (Auth::user()->type == 1 || (Auth::user()->type == 4 && Auth::user()->vendor_id == 1)) {
            $settingsdata->image_size = $request->image_size;
        }
        if (Auth::user()->type == 2) {
            $settingsdata->checkout_login_required = isset($request->checkout_login_required) ? 1 : 2;
            $settingsdata->is_checkout_login_required = isset($request->is_checkout_login_required) ? 1 : 2;
            $settingsdata->email = $request->email;
            $settingsdata->address = $request->address;
            // Never blank an existing slug (a blank slug makes the store URL 404).
            if (helper::checkcustomdomain($vendor_id) == null && trim((string) $request->slug) !== "") {
                $userslug->slug = $request->slug;
                $userslug->update();
            }
        }
        if (!empty($request->feature_icon)) {
            foreach ($request->feature_icon as $key => $icon) {
                if (!empty($icon) && !empty($request->feature_title[$key]) && !empty($request->feature_description[$key])) {
                    $feature = new Footerfeatures;
                    $feature->vendor_id = $vendor_id;
                    $feature->icon = $icon;
                    $feature->title = $request->feature_title[$key];
                    $feature->description = $request->feature_description[$key];
                    $feature->save();
                }
            }
        }
        if (!empty($request->edit_icon_key)) {
            foreach ($request->edit_icon_key as $key => $id) {
                $feature = Footerfeatures::find($id);
                $feature->icon = $request->edi_feature_icon[$id];
                $feature->title = $request->edi_feature_title[$id];
                $feature->description = $request->edi_feature_description[$id];
                $feature->save();
            }
        }
        $settingsdata->save();

        return redirect()->back()->with('success', trans('messages.success'));
    }
    public function settings_updatetheme(Request $request)
    {
        if (Auth::user()->type == 4) {
            $vendor_id = Auth::user()->vendor_id;
        } else {
            $vendor_id = Auth::user()->id;
        }
        $settingsdata = Settings::where('vendor_id', $vendor_id)->first();
        $settingsdata->primary_color = $request->primary_color;
        $settingsdata->secondary_color = $request->secondary_color;
        $settingsdata->template = !empty($request->template) ? $request->template : 1;
        $settingsdata->template_type = !empty($request->template_type) ? $request->template_type : 1;
        if ($request->hasfile('logo')) {
            $validator = Validator::make($request->all(), [
                'logo' => 'image|max:' . helper::imagesize() . '|' . helper::imageext(),
            ], [
                'logo.max' => trans('messages.image_size_message'),
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            if (env('Environment') == 'sendbox') {
                return $this->sendError("This operation was not performed due to demo mode");
            }
            if ($settingsdata->logo != "default-logo.png" && $settingsdata->logo != "" && file_exists(storage_path('app/public/admin-assets/images/about/logo/' . $settingsdata->logo))) {
                unlink(storage_path('app/public/admin-assets/images/about/logo/' . $settingsdata->logo));
            }
            if (Auth::user()->type == 1 || (Auth::user()->type == 4 && Auth::user()->vendor_id == 1)) {
                if ($settingsdata->logo != "default-logo.png" && $settingsdata->logo != "" && file_exists(storage_path('app/public/admin-assets/images/about/defaultimages/' . $settingsdata->logo))) {
                    unlink(storage_path('app/public/admin-assets/images/about/defaultimages/' . $settingsdata->logo));
                }
                $logo_name = 'logo-' . uniqid() . '.' . $request->logo->getClientOriginalExtension();
                $request->file('logo')->move(storage_path('app/public/admin-assets/images/about/defaultimages/'), $logo_name);
            } else {
                if ($settingsdata->logo != "default-logo.png" && $settingsdata->logo != "" && file_exists(storage_path('app/public/admin-assets/images/about/logo/' . $settingsdata->logo))) {
                    unlink(storage_path('app/public/admin-assets/images/about/logo/' . $settingsdata->logo));
                }
                $logo_name = 'logo-' . uniqid() . '.' . $request->logo->getClientOriginalExtension();
                $request->file('logo')->move(storage_path('app/public/admin-assets/images/about/logo/'), $logo_name);
            }
            $settingsdata->logo = $logo_name;
        }
        if ($request->hasfile('darklogo')) {
            $validator = Validator::make($request->all(), [
                'darklogo' => 'image|max:' . helper::imagesize() . '|' . helper::imageext(),
            ], [
                'darklogo.max' => trans('messages.image_size_message'),
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            if (env('Environment') == 'sendbox') {
                return $this->sendError("This operation was not performed due to demo mode");
            }
            if ($settingsdata->darklogo != "default-darklogo.png" && $settingsdata->darklogo != "" && file_exists(storage_path('app/public/admin-assets/images/about/darklogo/' . $settingsdata->darklogo))) {
                unlink(storage_path('app/public/admin-assets/images/about/darklogo/' . $settingsdata->darklogo));
            }
            if (Auth::user()->type == 1 || (Auth::user()->type == 4 && Auth::user()->vendor_id == 1)) {
                if ($settingsdata->darklogo != "default-darklogo.png" && $settingsdata->darklogo != "" && file_exists(storage_path('app/public/admin-assets/images/about/defaultimages/' . $settingsdata->darklogo))) {
                    unlink(storage_path('app/public/admin-assets/images/about/defaultimages/' . $settingsdata->darklogo));
                }
                $darklogo_name = 'darklogo-' . uniqid() . '.' . $request->darklogo->getClientOriginalExtension();
                $request->file('darklogo')->move(storage_path('app/public/admin-assets/images/about/defaultimages/'), $darklogo_name);
            } else {
                if ($settingsdata->darklogo != "default-darklogo.png" && $settingsdata->darklogo != "" && file_exists(storage_path('app/public/admin-assets/images/about/darklogo/' . $settingsdata->darklogo))) {
                    unlink(storage_path('app/public/admin-assets/images/about/darklogo/' . $settingsdata->darklogo));
                }
                $darklogo_name = 'darklogo-' . uniqid() . '.' . $request->darklogo->getClientOriginalExtension();
                $request->file('darklogo')->move(storage_path('app/public/admin-assets/images/about/darklogo/'), $darklogo_name);
            }
            $settingsdata->darklogo = $darklogo_name;
        }
        if ($request->hasfile('favicon')) {
            $validator = Validator::make($request->all(), [
                'favicon' => 'image|max:' . helper::imagesize() . '|' . helper::imageext(),
            ], [
                'favicon.max' => trans('messages.image_size_message'),
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            if (Auth::user()->type == 1 || (Auth::user()->type == 4 && Auth::user()->vendor_id == 1)) {
                if ($settingsdata->favicon != "defaultlogo.png" && $settingsdata->favicon != "" && file_exists(storage_path('app/public/admin-assets/images/about/defaultimages/' . $settingsdata->favicon))) {
                    unlink(storage_path('app/public/admin-assets/images/about/defaultimages/' . $settingsdata->favicon));
                }
                $favicon_name = 'favicon-' . uniqid() . '.' . $request->favicon->getClientOriginalExtension();
                $request->favicon->move(storage_path('app/public/admin-assets/images/about/defaultimages/'), $favicon_name);
            } else {
                if ($settingsdata->favicon != "defaultlogo.png" && $settingsdata->favicon != "" && file_exists(storage_path('app/public/admin-assets/images/about/favicon/' . $settingsdata->favicon))) {
                    unlink(storage_path('app/public/admin-assets/images/about/favicon/' . $settingsdata->favicon));
                }
                $favicon_name = 'favicon-' . uniqid() . '.' . $request->favicon->getClientOriginalExtension();
                $request->favicon->move(storage_path('app/public/admin-assets/images/about/favicon/'), $favicon_name);
            }

            $settingsdata->favicon = $favicon_name;
        }
        $settingsdata->save();
        return redirect()->back()->with('success', trans('messages.success'));
    }
    public function settings_updateseo(Request $request)
    {
        if (Auth::user()->type == 4) {
            $vendor_id = Auth::user()->vendor_id;
        } else {
            $vendor_id = Auth::user()->id;
        }

        $settingsdata = Settings::where('vendor_id', $vendor_id)->first();

        $settingsdata->meta_title = $request->meta_title;
        $settingsdata->meta_description = $request->meta_description;
        if ($request->hasfile('og_image')) {
            $validator = Validator::make($request->all(), [
                'og_image' => 'image|max:' . helper::imagesize() . '|' . helper::imageext(),
            ], [
                'og_image.max' => trans('messages.image_size_message'),
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            if (env('Environment') == 'sendbox') {
                return $this->sendError("This operation was not performed due to demo mode");
            }
            if (Auth::user()->type == 1 || (Auth::user()->type == 4 && Auth::user()->vendor_id == 1)) {

                if ($settingsdata->og_image != "" && file_exists(storage_path('app/public/admin-assets/images/about/defaultimages/' . $settingsdata->og_image))) {
                    unlink(storage_path('app/public/admin-assets/images/about/defaultimages/' . $settingsdata->og_image));
                }
                $image = 'og_image-' . uniqid() . '.' . $request->og_image->getClientOriginalExtension();
                $request->og_image->move(storage_path('app/public/admin-assets/images/about/defaultimages/'), $image);
            } else {
                if ($settingsdata->og_image != "" && file_exists(storage_path('app/public/admin-assets/images/about/og_image/' . $settingsdata->og_image))) {
                    unlink(storage_path('app/public/admin-assets/images/about/og_image/' . $settingsdata->og_image));
                }
                $image = 'og_image-' . uniqid() . '.' . $request->og_image->getClientOriginalExtension();
                $request->og_image->move(storage_path('app/public/admin-assets/images/about/og_image/'), $image);
            }

            $settingsdata->og_image = $image;
        }
        $settingsdata->save();
        return redirect()->back()->with('success', trans('messages.success'));
    }
    public function landingsettings(Request $request)
    {
        if (Auth::user()->type == 4) {
            $vendor_id = Auth::user()->vendor_id;
        } else {
            $vendor_id = Auth::user()->id;
        }
        $settingsdata = Settings::where('vendor_id', $vendor_id)->first();
        $settingsdata->primary_color = $request->landing_primary_color;
        $settingsdata->secondary_color = $request->landing_secondary_color;
        $settingsdata->email = $request->landing_email;
        $settingsdata->contact = $request->landing_mobile;
        $settingsdata->address = $request->landing_address;
        $settingsdata->landing_website_title = $request->landing_website_title;
        if ($request->hasfile('logo')) {
            $validator = Validator::make($request->all(), [
                'logo' => 'image|max:' . helper::imagesize() . '|' . helper::imageext(),
            ], [
                'logo.max' => trans('messages.image_size_message'),
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            if (env('Environment') == 'sendbox') {
                return $this->sendError("This operation was not performed due to demo mode");
            }
            if ($settingsdata->logo != "default-logo.png" && $settingsdata->logo != "" && file_exists(storage_path('app/public/admin-assets/images/about/logo/' . $settingsdata->logo))) {
                unlink(storage_path('app/public/admin-assets/images/about/logo/' . $settingsdata->logo));
            }
            $logo_name = 'logo-' . uniqid() . '.' . $request->logo->getClientOriginalExtension();
            $request->file('logo')->move(storage_path('app/public/admin-assets/images/about/logo/'), $logo_name);
            $settingsdata->logo = $logo_name;
        }
        if ($request->hasfile('favicon')) {
            $validator = Validator::make($request->all(), [
                'favicon' => 'image|max:' . helper::imagesize() . '|' . helper::imageext(),
            ], [
                'favicon.max' => trans('messages.image_size_message'),
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            if (env('Environment') == 'sendbox') {
                return $this->sendError("This operation was not performed due to demo mode");
            }
            if ($settingsdata->favicon != "default-favicon.png" && $settingsdata->favicon != "" && file_exists(storage_path('app/public/admin-assets/images/about/favicon/' . $settingsdata->favicon))) {
                unlink(storage_path('app/public/admin-assets/images/about/favicon/' . $settingsdata->favicon));
            }
            $favicon_name = 'favicon-' . uniqid() . '.' . $request->favicon->getClientOriginalExtension();
            $request->favicon->move(storage_path('app/public/admin-assets/images/about/favicon/'), $favicon_name);
            $settingsdata->favicon = $favicon_name;
        }

        $settingsdata->save();
        return redirect()->back()->with('success', trans('messages.success'));
    }
    public function settings_updateanalytics(Request $request)
    {
        if (Auth::user()->type == 4) {
            $vendor_id = Auth::user()->vendor_id;
        } else {
            $vendor_id = Auth::user()->id;
        }
        $settingsdata = Settings::where('vendor_id', $vendor_id)->first();
        $settingsdata->tracking_id = $request->tracking_id;
        $settingsdata->view_id = $request->view_id;
        $settingsdata->save();
        return redirect()->back()->with('success', trans('messages.success'));
    }
    public function settings_updatecustomedomain(Request $request)
    {
        if (Auth::user()->type == 4) {
            $vendor_id = Auth::user()->vendor_id;
        } else {
            $vendor_id = Auth::user()->id;
        }
        $settingsdata = Settings::where('vendor_id', $vendor_id)->first();
        $settingsdata->cname_title = $request->cname_title;
        $settingsdata->cname_text = $request->cname_text;
        $settingsdata->save();
        return redirect()->back()->with('success', trans('messages.success'));
    }
    public function delete_feature(Request $request)
    {
        Footerfeatures::where('id', $request->id)->delete();

        return redirect()->back()->with('success', trans('messages.success'));
    }
    public function delete_viewall_page_image(Request $request)
    {
        if (Auth::user()->type == 4) {
            $vendor_id = Auth::user()->vendor_id;
        } else {
            $vendor_id = Auth::user()->id;
        }
        $settingsdata = Settings::where('vendor_id', $vendor_id)->first();
        if (!empty($settingsdata)) {
            if (!empty($settingsdata->viewallpage_banner) && file_exists(storage_path('app/public/admin-assets/images/about/viewallpage_banner/' . $settingsdata->viewallpage_banner))) {
                unlink(storage_path('app/public/admin-assets/images/about/viewallpage_banner/' . $settingsdata->viewallpage_banner));
            }
            $settingsdata->viewallpage_banner = "";
            $settingsdata->update();
            return redirect('admin/settings')->with('success', trans('messages.success'));
        }
        return redirect('admin/settings');
    }
    public function app_section(Request $request)
    {

        if (Auth::user()->type == 4) {
            $vendor_id = Auth::user()->vendor_id;
        } else {
            $vendor_id = Auth::user()->id;
        }
        $appsection = AppSettings::where('vendor_id', $vendor_id)->first();

        if (empty($appsection)) {
            $appsection = new AppSettings();
        }

        $appsection->vendor_id = $vendor_id;
        $appsection->android_link = $request->android_link;
        $appsection->ios_link = $request->ios_link;
        $appsection->mobile_app_on_off = isset($request->mobile_app_on_off) ? 1 : 2;

        if ($request->has('image')) {

            $validator = Validator::make($request->all(), [
                'image' => 'image|max:' . helper::imagesize() . '|' . helper::imageext(),
            ], [
                "image.image" => trans('messages.enter_image_file'),
            ]);
            if ($validator->fails()) {
                return redirect()->back()->with('error', trans('messages.image_size_message') . ' ' . helper::appdata('')->image_size . ' ' . 'MB');
            }
            if (!empty($appsection->image)) {
                if (file_exists(storage_path('app/public/admin-assets/images/index/' .  $appsection->image))) {
                    unlink(storage_path('app/public/admin-assets/images/index/' .  $appsection->image));
                }
            }
            $image = 'appsection-' . uniqid() . "." . $request->file('image')->getClientOriginalExtension();
            $request->file('image')->move(storage_path('app/public/admin-assets/images/index/'), $image);
            $appsection->image = $image;
        }
        $appsection->save();
        return redirect()->back()->with('success', trans('messages.success'));
    }
    public function social_links_update(Request $request)
    {
        if (Auth::user()->type == 4) {
            $vendor_id = Auth::user()->vendor_id;
        } else {
            $vendor_id = Auth::user()->id;
        }
        if (!empty($request->social_icon)) {
            foreach ($request->social_icon as $key => $icon) {
                if (!empty($icon) && !empty($request->social_link[$key])) {
                    $sociallink = new SocialLinks;
                    $sociallink->vendor_id = $vendor_id;
                    $sociallink->icon = $icon;
                    $sociallink->link = $request->social_link[$key];
                    $sociallink->save();
                }
            }
        }
        if (!empty($request->edit_icon_key)) {
            foreach ($request->edit_icon_key as $key => $id) {
                $sociallink = SocialLinks::find($id);
                $sociallink->icon = $request->edi_sociallink_icon[$id];
                $sociallink->link = $request->edi_sociallink_link[$id];
                $sociallink->save();
            }
        }
        return redirect()->back()->with('success', trans('messages.success'));
    }
    public function delete_sociallinks(Request $request)
    {
        SocialLinks::where('id', $request->id)->delete();
        return redirect()->back()->with('success', trans('messages.success'));
    }
    public function fun_fact_update(Request $request)
    {
        if (Auth::user()->type == 4) {
            $vendor_id = Auth::user()->vendor_id;
        } else {
            $vendor_id = Auth::user()->id;
        }
        if (!empty($request->fun_fact_title)) {
            foreach ($request->fun_fact_title as $key => $title) {
                if (!empty($title) && !empty($request->fun_fact_description[$key])) {
                    $sociallink = new Footerfeatures;
                    $sociallink->vendor_id = $vendor_id;
                    $sociallink->title = $title;
                    $sociallink->description = $request->fun_fact_description[$key];
                    $sociallink->save();
                }
            }
        }
        if (!empty($request->edit_icon_key)) {
            foreach ($request->edit_icon_key as $key => $id) {
                $sociallink = Footerfeatures::find($id);
                $sociallink->title = $request->edit_fun_fact_title[$id];
                $sociallink->description = $request->edit_fun_fact_description[$id];
                $sociallink->save();
            }
        }
        return redirect()->back()->with('success', trans('messages.success'));
    }
    public function safe_secure_store(Request $request)
    {
        if (Auth::user()->type == 4) {
            $vendor_id = Auth::user()->vendor_id;
        } else {
            $vendor_id = Auth::user()->id;
        }
        $settingsdata = OtherSettings::where('vendor_id', $vendor_id)->first();
        if (empty($settingsdata)) {
            $settingsdata = new OtherSettings();
            $settingsdata->vendor_id = $vendor_id;
        }
        if ($request->trusted_badges == 1) {
            // Handle image 1
            if ($request->hasFile('trusted_badge_image_1')) {
                if ($settingsdata->trusted_badge_image_1 != "trusted_badge_image_1.png" && file_exists(storage_path('app/public/admin-assets/images/about/trusted_badge/' . $settingsdata->trusted_badge_image_1))) {
                    @unlink(storage_path('app/public/admin-assets/images/about/trusted_badge/' . $settingsdata->trusted_badge_image_1));
                }
                $image1 = $request->file('trusted_badge_image_1');
                $imageName1 = 'trusted_badge-' . uniqid() . '.' . $image1->getClientOriginalExtension();
                $image1->move(storage_path('app/public/admin-assets/images/about/trusted_badge/'), $imageName1);
                $settingsdata->trusted_badge_image_1 = $imageName1;
            }

            // Handle image 2
            if ($request->hasFile('trusted_badge_image_2')) {
                if ($settingsdata->trusted_badge_image_2 != "trusted_badge_image_2.png" && file_exists(storage_path('app/public/admin-assets/images/about/trusted_badge/' . $settingsdata->trusted_badge_image_2))) {
                    @unlink(storage_path('app/public/admin-assets/images/about/trusted_badge/' . $settingsdata->trusted_badge_image_2));
                }
                $image2 = $request->file('trusted_badge_image_2');
                $imageName2 = 'trusted_badge-' . uniqid() . '.' . $image2->getClientOriginalExtension();
                $image2->move(storage_path('app/public/admin-assets/images/about/trusted_badge/'), $imageName2);
                $settingsdata->trusted_badge_image_2 = $imageName2;
            }

            // Handle image 3
            if ($request->hasFile('trusted_badge_image_3')) {
                if ($settingsdata->trusted_badge_image_3 != "trusted_badge_image_3.png" && file_exists(storage_path('app/public/admin-assets/images/about/trusted_badge/' . $settingsdata->trusted_badge_image_3))) {
                    @unlink(storage_path('app/public/admin-assets/images/about/trusted_badge/' . $settingsdata->trusted_badge_image_3));
                }
                $image3 = $request->file('trusted_badge_image_3');
                $imageName3 = 'trusted_badge-' . uniqid() . '.' . $image3->getClientOriginalExtension();
                $image3->move(storage_path('app/public/admin-assets/images/about/trusted_badge/'), $imageName3);
                $settingsdata->trusted_badge_image_3 = $imageName3;
            }

            // Handle image 4
            if ($request->hasFile('trusted_badge_image_4')) {
                if ($settingsdata->trusted_badge_image_4 != "trusted_badge_image_4.png" && file_exists(storage_path('app/public/admin-assets/images/about/trusted_badge/' . $settingsdata->trusted_badge_image_4))) {
                    @unlink(storage_path('app/public/admin-assets/images/about/trusted_badge/' . $settingsdata->trusted_badge_image_4));
                }
                $image4 = $request->file('trusted_badge_image_4');
                $imageName4 = 'trusted_badge-' . uniqid() . '.' . $image4->getClientOriginalExtension();
                $image4->move(storage_path('app/public/admin-assets/images/about/trusted_badge/'), $imageName4);
                $settingsdata->trusted_badge_image_4 = $imageName4;
            }
        }
        if ($request->safe_secure == 1) {
            $settingsdata->safe_secure_checkout_payment_selection = $request->safe_secure_checkout_payment_selection == null ? null : implode(',', $request->safe_secure_checkout_payment_selection);
            $settingsdata->safe_secure_checkout_text = $request->safe_secure_checkout_text;
            $settingsdata->safe_secure_checkout_text_color = $request->safe_secure_checkout_text_color;
        }
        $settingsdata->save();
        return redirect()->back()->with('success', trans('messages.success'));
    }

    public function tips_settings(Request $request)
    {
        if (Auth::user()->type == 4) {
            $vendor_id = Auth::user()->vendor_id;
        } else {
            $vendor_id = Auth::user()->id;
        }

        $othersettingsdata = OtherSettings::where('vendor_id', $vendor_id)->first();
        if (empty($othersettingdata)) {
            $othersettingdata = new OtherSettings();
            $othersettingdata->vendor_id = $vendor_id;
        }
        $othersettingsdata->tips_settings = isset($request->tips_settings) ? 1 : 2;
        $othersettingsdata->save();
        return redirect()->back()->with('success', trans('messages.success'));
    }

    public function otherdataupdate(Request $request)
    {
        if (Auth::user()->type == 4) {
            $vendor_id = Auth::user()->vendor_id;
        } else {
            $vendor_id = Auth::user()->id;
        }
        $settingsdata = Settings::where('vendor_id', $vendor_id)->first();
        if (Auth::user()->type == 1 || (Auth::user()->type == 4 && Auth::user()->vendor_id == 1)) {
            if ($request->hasfile('landing_home_banner')) {
                $validator = Validator::make($request->all(), [
                    'landing_home_banner' => 'image|max:' . helper::imagesize() . '|' . helper::imageext(),
                ], [
                    'landing_home_banner.max' => trans('messages.image_size_message'),
                ]);

                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput();
                }
                if (env('Environment') == 'sendbox') {
                    return $this->sendError("This operation was not performed due to demo mode");
                }
                if ($settingsdata->landing_home_banner != "default-photo.webp" && file_exists(storage_path('app/public/admin-assets/images/banners/' . $settingsdata->banner))) {
                    @unlink(storage_path('app/public/admin-assets/images/banners/' . $settingsdata->landing_home_banner));
                }
                $landing_home_banner = 'banner-' . uniqid() . '.' . $request->landing_home_banner->getClientOriginalExtension();
                $request->file('landing_home_banner')->move(storage_path('app/public/admin-assets/images/banners/'), $landing_home_banner);
                $settingsdata->landing_home_banner = $landing_home_banner;
            }

            if ($request->hasfile('store_unavailable_image')) {

                $validator = Validator::make($request->all(), [
                    'store_unavailable_image' => 'image|max:' . helper::imagesize() . '|' . helper::imageext(),
                ], [
                    "store_unavailable_image.image" => trans('messages.enter_image_file'),
                ]);
                if ($validator->fails()) {
                    return redirect()->back()->with('error', trans('messages.image_size_message') . ' ' . helper::appdata('')->image_size . ' ' . 'MB');
                }
                if (file_exists(storage_path('app/public/admin-assets/images/index/' . $settingsdata->store_unavailable_image))) {
                    @unlink(storage_path('app/public/admin-assets/images/index/' . $settingsdata->store_unavailable_image));
                }
                $maintenanceimage = 'store_unavailable-' . uniqid() . '.' . $request->store_unavailable_image->getClientOriginalExtension();
                $request->store_unavailable_image->move(storage_path('app/public/admin-assets/images/index/'), $maintenanceimage);
                $settingsdata->store_unavailable_image = $maintenanceimage;
            }
            if ($request->hasfile('subscribe_newsletter_image')) {

                $validator = Validator::make($request->all(), [
                    'subscribe_newsletter_image' => 'image|max:' . helper::imagesize() . '|' . helper::imageext(),
                ], [
                    "subscribe_newsletter_image.image" => trans('messages.enter_image_file'),
                ]);
                if ($validator->fails()) {
                    return redirect()->back()->with('error', trans('messages.image_size_message') . ' ' . helper::appdata('')->image_size . ' ' . 'MB');
                }
                if (file_exists(storage_path('app/public/admin-assets/images/index/' . $settingsdata->subscribe_newsletter_image))) {
                    @unlink(storage_path('app/public/admin-assets/images/index/' . $settingsdata->subscribe_newsletter_image));
                }
                $maintenanceimage = 'subscribe_newsletter-' . uniqid() . '.' . $request->subscribe_newsletter_image->getClientOriginalExtension();
                $request->subscribe_newsletter_image->move(storage_path('app/public/admin-assets/images/index/'), $maintenanceimage);
                $settingsdata->subscribe_newsletter_image = $maintenanceimage;
            }
        }
        if ($request->hasfile('auth_page_image')) {
            $validator = Validator::make($request->all(), [
                'auth_page_image' => 'image|max:' . helper::imagesize() . '|' . helper::imageext(),
            ], [
                'auth_page_image.max' => trans('messages.image_size_message'),
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            if (env('Environment') == 'sendbox') {
                return $this->sendError("This operation was not performed due to demo mode");
            }
            if (file_exists(storage_path('app/public/admin-assets/images/index/' . $settingsdata->auth_page_image))) {
                @unlink(storage_path('app/public/admin-assets/images/index/' . $settingsdata->auth_page_image));
            }
            $auth_page_image = 'auth-' . uniqid() . '.' . $request->auth_page_image->getClientOriginalExtension();
            $request->file('auth_page_image')->move(storage_path('app/public/admin-assets/images/index/'), $auth_page_image);
            $settingsdata->auth_page_image = $auth_page_image;
        }
        if (Auth::user()->type == 2 || (Auth::user()->type == 4 && Auth::user()->vendor_id != 1)) {
            if ($request->hasfile('banner')) {
                $validator = Validator::make($request->all(), [
                    'banner' => 'image|max:' . helper::imagesize() . '|' . helper::imageext(),
                ], [
                    'banner.max' => trans('messages.image_size_message'),
                ]);

                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput();
                }
                if (env('Environment') == 'sendbox') {
                    return $this->sendError("This operation was not performed due to demo mode");
                }
                if ($settingsdata->banner != "default-banner.png" && file_exists(storage_path('app/public/admin-assets/images/banners/' . $settingsdata->banner))) {
                    @unlink(storage_path('app/public/admin-assets/images/banners/' . $settingsdata->banner));
                }
                $banner = 'banner-' . uniqid() . '.' . $request->banner->getClientOriginalExtension();
                $request->file('banner')->move(storage_path('app/public/admin-assets/images/banners/'), $banner);
                $settingsdata->banner = $banner;
            }
            if ($request->hasfile('landin_page_cover_image')) {
                $validator = Validator::make($request->all(), [
                    'landin_page_cover_image' => 'image|max:' . helper::imagesize() . '|' . helper::imageext(),
                ], [
                    'landin_page_cover_image.max' => trans('messages.image_size_message'),
                ]);

                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput();
                }
                if (env('Environment') == 'sendbox') {
                    return $this->sendError("This operation was not performed due to demo mode");
                }
                if ($settingsdata->cover_image != "cover.png" && file_exists(storage_path('app/public/admin-assets/images/coverimage/' . $settingsdata->cover_image))) {
                    @unlink(storage_path('app/public/admin-assets/images/about/coverimage/' . $settingsdata->cover_image));
                }
                $coverimage = 'cover-' . uniqid() . '.' . $request->landin_page_cover_image->getClientOriginalExtension();
                $request->landin_page_cover_image->move(storage_path('app/public/admin-assets/images/coverimage/'), $coverimage);
                $settingsdata->cover_image = $coverimage;
            }

            if ($request->hasfile('subscribe_background')) {
                $validator = Validator::make($request->all(), [
                    'subscribe_background' => 'image|max:' . helper::imagesize() . '|' . helper::imageext(),
                ], [
                    'subscribe_background.max' => trans('messages.image_size_message'),
                ]);

                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput();
                }

                if (env('Environment') == 'sendbox') {
                    return $this->sendError("This operation was not performed due to demo mode");
                }
                if ($settingsdata->subscribe_background != "default_subscriber.png" && file_exists(storage_path('app/public/admin-assets/images/subscribe/' . $settingsdata->subscribe_background))) {
                    @unlink(storage_path('app/public/admin-assets/images/subscribe/' . $settingsdata->subscribe_background));
                }
                $subscribe_img = 'subscribe_bg-' . uniqid() . '.' . $request->subscribe_background->getClientOriginalExtension();
                $request->subscribe_background->move(storage_path('app/public/admin-assets/images/subscribe/'), $subscribe_img);
                $settingsdata->subscribe_background = $subscribe_img;
            }
            if ($request->hasfile('faq_image')) {
                $validator = Validator::make($request->all(), [
                    'faq_image' => 'image|max:' . helper::imagesize() . '|' . helper::imageext(),
                ], [
                    'faq_image.max' => trans('messages.image_size_message'),
                ]);

                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput();
                }
                if (env('Environment') == 'sendbox') {
                    return $this->sendError("This operation was not performed due to demo mode");
                }
                if (file_exists(storage_path('app/public/admin-assets/images/index/' . $settingsdata->faq_image))) {
                    @unlink(storage_path('app/public/admin-assets/images/index/' . $settingsdata->faq_image));
                }
                $faq_image = 'faq-' . uniqid() . '.' . $request->faq_image->getClientOriginalExtension();
                $request->file('faq_image')->move(storage_path('app/public/admin-assets/images/index/'), $faq_image);
                $settingsdata->faq_image = $faq_image;
            }
            if ($request->hasfile('book_table_image')) {
                $validator = Validator::make($request->all(), [
                    'book_table_image' => 'image|max:' . helper::imagesize() . '|' . helper::imageext(),
                ], [
                    'book_table_image.max' => trans('messages.image_size_message'),
                ]);

                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput();
                }
                if (env('Environment') == 'sendbox') {
                    return $this->sendError("This operation was not performed due to demo mode");
                }
                if (file_exists(storage_path('app/public/admin-assets/images/index/' . $settingsdata->book_table_image))) {
                    @unlink(storage_path('app/public/admin-assets/images/index/' . $settingsdata->book_table_image));
                }
                $book_table_image = 'book_table-' . uniqid() . '.' . $request->book_table_image->getClientOriginalExtension();
                $request->file('book_table_image')->move(storage_path('app/public/admin-assets/images/index/'), $book_table_image);
                $settingsdata->book_table_image = $book_table_image;
            }
            if ($request->hasfile('order_success_image')) {
                $validator = Validator::make($request->all(), [
                    'order_success_image' => 'image|max:' . helper::imagesize() . '|' . helper::imageext(),
                ], [
                    'order_success_image.max' => trans('messages.image_size_message'),
                ]);

                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput();
                }
                if (env('Environment') == 'sendbox') {
                    return $this->sendError("This operation was not performed due to demo mode");
                }
                if (file_exists(storage_path('app/public/admin-assets/images/index/' . $settingsdata->order_success_image))) {
                    @unlink(storage_path('app/public/admin-assets/images/index/' . $settingsdata->order_success_image));
                }
                $order_success_image = 'order_success-' . uniqid() . '.' . $request->order_success_image->getClientOriginalExtension();
                $request->file('order_success_image')->move(storage_path('app/public/admin-assets/images/index/'), $order_success_image);
                $settingsdata->order_success_image = $order_success_image;
            }
            if ($request->hasfile('no_data_image')) {
                $validator = Validator::make($request->all(), [
                    'no_data_image' => 'image|max:' . helper::imagesize() . '|' . helper::imageext(),
                ], [
                    'no_data_image.max' => trans('messages.image_size_message'),
                ]);

                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput();
                }
                if (env('Environment') == 'sendbox') {
                    return $this->sendError("This operation was not performed due to demo mode");
                }
                if (file_exists(storage_path('app/public/admin-assets/images/index/' . $settingsdata->no_data_image))) {
                    @unlink(storage_path('app/public/admin-assets/images/index/' . $settingsdata->no_data_image));
                }
                $no_data_image = 'no_data-' . uniqid() . '.' . $request->no_data_image->getClientOriginalExtension();
                $request->file('no_data_image')->move(storage_path('app/public/admin-assets/images/index/'), $no_data_image);
                $settingsdata->no_data_image = $no_data_image;
            }
            $settingsdata->google_review = $request->google_review;
        }
        $settingsdata->save();
        return redirect()->back()->with('success', trans('messages.success'));
    }
    public function maintenance_update(Request $request)
    {
        if (Auth::user()->type == 4) {
            $vendor_id = Auth::user()->vendor_id;
        } else {
            $vendor_id = Auth::user()->id;
        }
        $settingsdata = OtherSettings::where('vendor_id', $vendor_id)->first();
        if (empty($settingsdata)) {
            $settingsdata = new OtherSettings();
            $settingsdata->vendor_id = $vendor_id;
        }
        $settingsdata->maintenance_title = $request->maintenance_title;
        $settingsdata->maintenance_description = $request->maintenance_description;
        $settingsdata->maintenance_on_off = isset($request->maintenance_on_off) ? 1 : 2;

        if ($request->hasFile('maintenance_image')) {
            if ($settingsdata->maintenance_image  && file_exists(storage_path('app/public/admin-assets/images/index/' . $settingsdata->maintenance_image))) {
                @unlink(storage_path('app/public/admin-assets/images/index/' . $settingsdata->maintenance_image));
            }
            $image3 = $request->file('maintenance_image');
            $imageName3 = 'maintenance_image-' . uniqid() . '.' . $image3->getClientOriginalExtension();
            $image3->move(storage_path('app/public/admin-assets/images/index/'), $imageName3);
            $settingsdata->maintenance_image = $imageName3;
        }
        $settingsdata->save();
        return redirect()->back()->with('success', trans('messages.success'));
    }
    public function estimated_delivery(Request $request)
    {
        if (Auth::user()->type == 4) {
            $vendor_id = Auth::user()->vendor_id;
        } else {
            $vendor_id = Auth::user()->id;
        }
        if (isset($request->estimated_delivery) && $request->estimated_delivery == 1) {
            $othersettingsdata = OtherSettings::where('vendor_id', $vendor_id)->first();
            if (empty($othersettingsdata)) {
                $othersettingsdata = new OtherSettings();
                $othersettingsdata->vendor_id = $vendor_id;
            }
            $othersettingsdata->estimated_delivery_on_off = isset($request->estimated_delivery_on_off) ? 1 : 2;
            $othersettingsdata->days_of_estimated_delivery = $request->days_of_estimated_delivery;
            $othersettingsdata->save();
            return redirect()->back()->with('success', trans('messages.success'));
        }
    }
}
