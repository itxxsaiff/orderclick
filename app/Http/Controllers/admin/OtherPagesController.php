<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Privacypolicy;
use App\Models\Terms;
use App\Models\About;
use App\Models\Areas;
use App\Models\Subscriber;
use App\Models\City;
use Illuminate\Support\Facades\Auth;
use App\Models\Faq;
use App\Models\Contact;
use App\Models\RefundPrivacypolicy;
use App\Models\Settings;
use App\Models\Shipping;
use App\Models\User;

class OtherPagesController extends Controller
{
    public function share()
    {
        if (Auth::user()->type == 4) {
            $vendor_id = Auth::user()->vendor_id;
        } else {
            $vendor_id = Auth::user()->id;
        }
        $user = User::where('id', $vendor_id)->first();
        return view('admin.otherpages.share', compact('user'));
    }
    // -----------------------------------------------------------------
    // -------------------  Privacy-Policy  ----------------------------
    // -----------------------------------------------------------------
    public function privacypolicy()
    {
        if (Auth::user()->type == 4) {
            $vendor_id = Auth::user()->vendor_id;
        } else {
            $vendor_id = Auth::user()->id;
        }
        $getprivacypolicy = Privacypolicy::where('vendor_id', $vendor_id)->first();
        return view('admin.otherpages.privacypolicy', compact('getprivacypolicy'));
    }
    public function privacypolicy_update(Request $request)
    {
        if (Auth::user()->type == 4) {
            $vendor_id = Auth::user()->vendor_id;
        } else {
            $vendor_id = Auth::user()->id;
        }
        $privacypolicy = Privacypolicy::where('vendor_id', $vendor_id)->first();
        if (empty($privacypolicy)) {
            $privacypolicy = new Privacypolicy();
            $privacypolicy->vendor_id = $vendor_id;
        }
        $privacypolicy->privacypolicy_content = $request->privacypolicy;
        $privacypolicy->save();
        return redirect('admin/privacy-policy')->with('success', trans('messages.success'));
    }
    // -----------------------------------------------------------------
    // ------------------- Refund Privacy-Policy  ----------------------------
    // -----------------------------------------------------------------
    public function refundpolicy()
    {
        if (Auth::user()->type == 4) {
            $vendor_id = Auth::user()->vendor_id;
        } else {
            $vendor_id = Auth::user()->id;
        }
        $getrefundpolicy = RefundPrivacypolicy::where('vendor_id', $vendor_id)->first();
        return view('admin.otherpages.refund_privacypolicy', compact('getrefundpolicy'));
    }
    public function refundpolicy_update(Request $request)
    {
        if (Auth::user()->type == 4) {
            $vendor_id = Auth::user()->vendor_id;
        } else {
            $vendor_id = Auth::user()->id;
        }
        $refundpolicy = RefundPrivacypolicy::where('vendor_id', $vendor_id)->first();
        if (empty($refundpolicy)) {
            $refundpolicy = new RefundPrivacypolicy();
            $refundpolicy->vendor_id = $vendor_id;
        }
        $refundpolicy->refund_policy_content = $request->refundpolicy;
        $refundpolicy->save();
        return redirect('admin/refund-policy')->with('success', trans('messages.success'));
    }
    // -----------------------------------------------------------------
    // ------------------- Terms-Condition -----------------------------
    // -----------------------------------------------------------------
    public function termscondition()
    {
        if (Auth::user()->type == 4) {
            $vendor_id = Auth::user()->vendor_id;
        } else {
            $vendor_id = Auth::user()->id;
        }
        $gettermscondition = Terms::where('vendor_id', $vendor_id)->first();
        return view('admin.otherpages.termscondition', compact('gettermscondition'));
    }
    public function termscondition_update(Request $request)
    {
        if (Auth::user()->type == 4) {
            $vendor_id = Auth::user()->vendor_id;
        } else {
            $vendor_id = Auth::user()->id;
        }
        $termscondition = Terms::where('vendor_id', $vendor_id)->first();
        if (empty($termscondition)) {
            $termscondition = new Terms();
            $termscondition->vendor_id = $vendor_id;
        }
        $termscondition->terms_content = $request->termscondition;
        $termscondition->save();
        return redirect('admin/terms-conditions')->with('success', trans('messages.success'));
    }
    // -----------------------------------------------------------------
    // ------------------- About us -----------------------------
    // -----------------------------------------------------------------
    public function aboutus()
    {
        if (Auth::user()->type == 4) {
            $vendor_id = Auth::user()->vendor_id;
        } else {
            $vendor_id = Auth::user()->id;
        }
        $getaboutus = About::where('vendor_id', $vendor_id)->first();
        return view('admin.otherpages.aboutus', compact('getaboutus'));
    }
    public function aboutus_update(Request $request)
    {
        if (Auth::user()->type == 4) {
            $vendor_id = Auth::user()->vendor_id;
        } else {
            $vendor_id = Auth::user()->id;
        }
        $aboutus = About::where('vendor_id', $vendor_id)->first();
        if (empty($aboutus)) {
            $aboutus = new About();
            $aboutus->vendor_id = $vendor_id;
        }
        $aboutus->about_content = $request->aboutus;
        $aboutus->save();
        return redirect('admin/aboutus')->with('success', trans('messages.success'));
    }
    public function faq_index(Request $request)
    {
        if (Auth::user()->type == 4) {
            $vendor_id = Auth::user()->vendor_id;
        } else {
            $vendor_id = Auth::user()->id;
        }
        $faqs = Faq::where('vendor_id', $vendor_id)->orderBy('reorder_id')->get();

        return view('admin.faqs.index', compact('faqs'));
    }
    public function faq_add()
    {
        return view('admin.faqs.add');
    }
    public function faq_save(Request $request)
    {
        if (Auth::user()->type == 4) {
            $vendor_id = Auth::user()->vendor_id;
        } else {
            $vendor_id = Auth::user()->id;
        }
        $faqs = new Faq();
        $faqs->vendor_id = $vendor_id;
        $faqs->question = $request->question;
        $faqs->answer = $request->answer;
        $faqs->save();
        return redirect('/admin/faqs')->with('success', trans('messages.success'));
    }
    public function faq_edit(Request $request)
    {
        $getfaq = Faq::where('id', $request->id)->first();
        return view('admin.faqs.edit', compact('getfaq'));
    }
    public function faq_update(Request $request)
    {
        if (Auth::user()->type == 4) {
            $vendor_id = Auth::user()->vendor_id;
        } else {
            $vendor_id = Auth::user()->id;
        }
        $getfaq = Faq::where('id', $request->id)->first();
        $getfaq->vendor_id = $vendor_id;
        $getfaq->question = $request->question;
        $getfaq->answer = $request->answer;
        $getfaq->update();
        return redirect('/admin/faqs')->with('success', trans('messages.success'));
    }
    public function faq_delete(Request $request)
    {

        $deletefaq = Faq::where('id', $request->id)->first();
        $deletefaq->delete();
        return redirect('/admin/faqs')->with('success', trans('messages.success'));
    }
    public function subscribers(Request $request)
    {
        if (Auth::user()->type == 4) {
            $vendor_id = Auth::user()->vendor_id;
        } else {
            $vendor_id = Auth::user()->id;
        }
        $getsubscribers = Subscriber::where('vendor_id', $vendor_id)->orderByDesc('id')->get();
        return view('admin.subscriber.index', compact('getsubscribers'));
    }
    public function subscribers_delete(Request $request)
    {
        $subscriber = Subscriber::find($request->id);
        if (!empty($subscriber)) {
            $subscriber->delete();
            return redirect('/admin/subscribers')->with('success', trans('messages.success'));
        }
        return redirect('/admin/subscribers')->with('error', trans('messages.wrong'));
    }

    /**
     * Unsubscribe or reactivate an address.
     *
     * Unsubscribing never deletes the row — the address stays on record specifically so no future
     * marketing email is sent to it.
     */
    public function subscribers_status(Request $request)
    {
        $subscriber = Subscriber::find($request->id);
        if (empty($subscriber)) {
            return redirect('/admin/subscribers')->with('error', trans('messages.wrong'));
        }

        if ($request->status === 'unsubscribed') {
            $subscriber->unsubscribe();
            return redirect('/admin/subscribers')->with('success', trans('messages.subscriber_unsubscribed'));
        }

        $subscriber->resubscribe();

        return redirect('/admin/subscribers')->with('success', trans('messages.subscriber_reactivated'));
    }
    public function inquiries(Request $request)
    {
        if (Auth::user()->type == 4) {
            $vendor_id = Auth::user()->vendor_id;
        } else {
            $vendor_id = Auth::user()->id;
        }
        $query = Contact::where('vendor_id', $vendor_id)->where('product_id', null);

        if ($request->filled('type'))   $query->where('inquiry_type', $request->type);
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('system')) $query->where('related_system', $request->system);
        if ($search = trim((string) $request->get('q'))) {
            $like = '%' . $search . '%';
            $query->where(function ($w) use ($like) {
                $w->where('name', 'like', $like)->orWhere('email', 'like', $like)
                    ->orWhere('mobile', 'like', $like)->orWhere('message', 'like', $like);
            });
        }

        // Archived inquiries are hidden unless asked for — they are never deleted.
        $request->get('archived') === '1' ? $query->whereNotNull('archived_at') : $query->whereNull('archived_at');

        $getinquiries = $query->orderByDesc('id')->get();

        $counts = [
            'new'         => Contact::where('vendor_id', $vendor_id)->whereNull('product_id')->whereNull('archived_at')->where('status', Contact::STATUS_NEW)->count(),
            'in_progress' => Contact::where('vendor_id', $vendor_id)->whereNull('product_id')->whereNull('archived_at')->where('status', Contact::STATUS_IN_PROGRESS)->count(),
            'archived'    => Contact::where('vendor_id', $vendor_id)->whereNull('product_id')->whereNotNull('archived_at')->count(),
        ];

        return view('admin.inquiries.index', compact('getinquiries', 'counts'));
    }

    /** Full message + linked account, opened from the View action. */
    public function inquiries_view(Request $request)
    {
        $vendor_id = Auth::user()->type == 4 ? Auth::user()->vendor_id : Auth::user()->id;
        $inquiry = Contact::where('id', $request->id)->where('vendor_id', $vendor_id)->firstOrFail();

        return view('admin.inquiries.view', compact('inquiry'));
    }

    /** Update type / related system / status / internal note. */
    public function inquiries_update(Request $request)
    {
        $vendor_id = Auth::user()->type == 4 ? Auth::user()->vendor_id : Auth::user()->id;
        $inquiry = Contact::where('id', $request->id)->where('vendor_id', $vendor_id)->firstOrFail();

        if (array_key_exists((string) $request->inquiry_type, Contact::typeOptions())) {
            $inquiry->inquiry_type = $request->inquiry_type;
        }
        if (array_key_exists((string) $request->status, Contact::statusOptions())) {
            $inquiry->status = $request->status;
        }
        if (array_key_exists((string) $request->related_system, Contact::systemOptions())) {
            $inquiry->related_system = $request->related_system ?: null;
        }
        $inquiry->admin_note = $request->admin_note;
        $inquiry->save();

        return redirect('/admin/inquiries/view-' . $inquiry->id)->with('success', trans('messages.success'));
    }

    /**
     * Archive instead of delete, so a support conversation is never lost. Restore puts it back.
     */
    public function inquiries_archive(Request $request)
    {
        $vendor_id = Auth::user()->type == 4 ? Auth::user()->vendor_id : Auth::user()->id;
        $inquiry = Contact::where('id', $request->id)->where('vendor_id', $vendor_id)->firstOrFail();

        $restoring = $inquiry->isArchived();
        $inquiry->archived_at = $restoring ? null : now();
        $inquiry->save();

        return redirect('/admin/inquiries')
            ->with('success', trans($restoring ? 'messages.inquiry_restored' : 'messages.inquiry_archived'));
    }

    public function inquiries_delete(Request $request)
    {
        $inquiry = Contact::find($request->id);
        if (!empty($inquiry)) {
            $inquiry->delete();
            return redirect('/admin/inquiries')->with('success', trans('messages.success'));
        }
        return redirect('/admin/inquiries')->with('error', trans('messages.wrong'));
    }
    public function cities(Request $request)
    {
        $allcities = City::where('is_deleted', 2)->orderBy('reorder_id')->get();
        return view('admin.city.index', compact('allcities'));
    }
    public function add_city(Request $request)
    {
        return view('admin.city.add');
    }
    public function save_city(Request $request)
    {
        $city = new City();
        $city->name = $request->name;
        $city->save();
        return redirect('/admin/cities')->with('success', trans('messages.success'));
    }
    public function edit_city(Request $request)
    {
        $editcity = City::where('id', $request->id)->first();
        return view('admin.city.edit', compact('editcity'));
    }
    public function update_city(Request $request)
    {
        $editcity = City::where('id', $request->id)->first();
        $editcity->name = $request->name;
        $editcity->update();
        return redirect('/admin/cities')->with('success', trans('messages.success'));
    }
    public function delete_city(Request $request)
    {
        $city = City::where('id', $request->id)->first();
        $city->is_deleted = 1;
        $city->update();
        return redirect('/admin/cities')->with('success', trans('messages.success'));
    }
    public function statuschange_city(Request $request)
    {
        $city = City::where('id', $request->id)->first();
        $city->is_available = $request->status;
        $city->update();
        return redirect('/admin/cities')->with('success', trans('messages.success'));
    }
    public function areas(Request $request)
    {
        $allareas = Areas::with('city_info')->where('is_deleted', 2)->orderBy('reorder_id')->get();
        return view('admin.areas.index', compact('allareas'));
    }
    public function add_area(Request $request)
    {
        $allcity = City::where('is_deleted', 2)->orderBy('reorder_id')->get();
        return view('admin.areas.add', compact('allcity'));
    }
    public function save_area(Request $request)
    {
        $area = new Areas();
        $area->city_id = $request->city;
        $area->area = $request->name;
        $area->save();
        return redirect('/admin/areas')->with('success', trans('messages.success'));
    }
    public function edit_area(Request $request)
    {
        $allcity = City::where('is_deleted', 2)->orderBy('reorder_id')->get();
        $editarea = Areas::where('id', $request->id)->first();
        return view('admin.areas.edit', compact('editarea', 'allcity'));
    }
    public function update_area(Request $request)
    {
        $editarea = Areas::where('id', $request->id)->first();
        $editarea->city_id = $request->city;
        $editarea->area = $request->name;
        $editarea->update();
        return redirect('/admin/areas')->with('success', trans('messages.success'));
    }
    public function delete_area(Request $request)
    {
        $area = Areas::where('id', $request->id)->first();
        $area->is_deleted = 1;
        $area->update();
        return redirect('/admin/areas')->with('success', trans('messages.success'));
    }
    public function statuschange_area(Request $request)
    {
        $area = Areas::where('id', $request->id)->first();
        $area->is_available = $request->status;
        $area->update();
        return redirect('/admin/areas')->with('success', trans('messages.success'));
    }

    public function reorder_city(Request $request)
    {
        $getcity = city::where('is_deleted', 2)->get();
        foreach ($getcity as $city) {
            foreach ($request->order as $order) {
                $city = city::where('id', $order['id'])->first();
                $city->reorder_id = $order['position'];
                $city->save();
            }
        }
        return response()->json(['status' => 1, 'msg' => trans('messages.success')], 200);
    }

    public function reorder_area(Request $request)
    {
        $getarea = Areas::where('is_deleted', 2)->get();
        foreach ($getarea as $area) {
            foreach ($request->order as $order) {
                $area = Areas::where('id', $order['id'])->first();
                $area->reorder_id = $order['position'];
                $area->save();
            }
        }
        return response()->json(['status' => 1, 'msg' => trans('messages.success')], 200);
    }
    public function reorder_faq(Request $request)
    {
        if (Auth::user()->type == 4) {
            $vendor_id = Auth::user()->vendor_id;
        } else {
            $vendor_id = Auth::user()->id;
        }
        $getfaqs =  Faq::where('vendor_id', $vendor_id)->get();
        foreach ($getfaqs as $faq) {
            foreach ($request->order as $order) {
                $faq = Faq::where('id', $order['id'])->first();
                $faq->reorder_id = $order['position'];
                $faq->save();
            }
        }
        return response()->json(['status' => 1, 'msg' => trans('messages.success')], 200);
    }
    /*===================================== Shipping ==================================*/
    public function shippingindex(Request $request)
    {
        if (Auth::user()->type == 4) {
            $vendor_id = Auth::user()->vendor_id;
        } else {
            $vendor_id = Auth::user()->id;
        }
        $content = Settings::where('vendor_id', $vendor_id)->first();
        $allshippingcontent = Shipping::where('vendor_id', $vendor_id)->orderBy('reorder_id')->get();
        return view('admin.shipping.index', compact('content', 'allshippingcontent'));
    }
    public function savecontent(Request $request)
    {
        if (Auth::user()->type == 4) {
            $vendor_id = Auth::user()->vendor_id;
        } else {
            $vendor_id = Auth::user()->id;
        }
        $newcontent = Settings::where('vendor_id', $vendor_id)->first();
        $newcontent->min_order_amount_for_free_shipping = $request->min_order_amount_for_free_shipping;
        $newcontent->shipping_charges = $request->shipping_charges;
        $newcontent->shipping_area = isset($request->shipping_area) ? 1 : 2;
        $newcontent->save();
        return redirect('admin/shipping')->with('success', trans('messages.success'));
    }
}
