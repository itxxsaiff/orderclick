<?php
   
namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\api\BaseController as BaseController;
use App\Models\Terms;
use App\Models\About;
use App\Models\Privacypolicy;
use App\Models\RefundPrivacypolicy;
use App\Models\Contact;
use App\Models\Settings;
use App\Models\Timing;
use App\Models\User;
use App\Helpers\helper;
use Config;
   
class CMSController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function cms(Request $request)
    {
        if ($request->vendor_id == "") {
            return $this->sendError(trans('messages.vendor_id_required'));       
        }

        $terms = terms::where('vendor_id', $request->vendor_id)->first();

        $privacy = Privacypolicy::where('vendor_id', $request->vendor_id)->first();

        $refund_policy = RefundPrivacypolicy::where('vendor_id', $request->vendor_id)->first();

        $aboutus= About::select('about_content')->where('vendor_id',$request->vendor_id)->first();

        $data = [
            'terms' =>  $terms->terms_content,
            'privacy' =>  $privacy->privacypolicy_content,
            'refund_policy' =>  $refund_policy->refund_policy_content,
            'aboutus' =>  $aboutus->about_content,
        ];
        
        return $this->sendResponse($data, trans('messages.success'));
    }

    public function contactdata(Request $request)
    {
        if ($request->vendor_id == "") {
            return $this->sendError(trans('messages.vendor_id_required'));       
        }

        $data = Settings::where('vendor_id', $request->vendor_id)->first();

        $timings = Timing::select('day','open_time','break_start','break_end','close_time','is_always_close')->where('vendor_id', $request->vendor_id)->get();

        $data = [
            'address' =>  $data->address,
            'contact' =>  $data->contact,
            'email' =>  $data->email,
            'times' =>  $timings,
        ];
        
        return $this->sendResponse($data, trans('messages.success'));
    }

    public function save_contact(Request $request)
    {
        if ($request->vendor_id == "") {
            return $this->sendError(trans('messages.vendor_id_required'));       
        }
        if ($request->first_name == "") {
            return $this->sendError(trans('messages.first_name_required'));       
        }
        if ($request->last_name == "") {
            return $this->sendError(trans('messages.last_name_required'));       
        }
        if ($request->email == "") {
            return $this->sendError(trans('messages.email_required'));       
        }
        if ($request->mobile == "") {
            return $this->sendError(trans('messages.mobile_required'));       
        }
        if ($request->message == "") {
            return $this->sendError(trans('messages.message_required'));       
        }
    
        $newinquiry = new Contact;
        $newinquiry->vendor_id = $request->vendor_id;
        $newinquiry->name = $request->first_name . " " . $request->last_name;
        $newinquiry->email = $request->email;
        $newinquiry->mobile = $request->mobile;
        $newinquiry->message = $request->message;
        // Link to the sender's account when the email belongs to a registered vendor or customer;
        // a general inquiry from an unknown address stays unlinked.
        $newinquiry->linked_user_id = Contact::linkToAccount((string) $request->email);
        $newinquiry->status = Contact::STATUS_NEW;
        $newinquiry->save();

        $vendordata = User::where('id', $request->vendor_id)->first();
        $emaildata = helper::emailconfigration($vendordata->id);
        Config::set('mail', $emaildata);
        helper::vendor_contact_data($vendordata->name,$vendordata->email,$request->name,$request->email,$request->mobile,$request->message);
        return $this->sendSuccess(trans('messages.success'));
    }
}