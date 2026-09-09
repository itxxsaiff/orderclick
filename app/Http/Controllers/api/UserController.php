<?php
   
namespace App\Http\Controllers\api;
   
use Illuminate\Http\Request;
use App\Http\Controllers\api\BaseController as BaseController;
use App\Models\User;
use App\Models\SystemAddons;
use App\Helpers\helper;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Config;
   
class UserController extends BaseController
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'mobile' => 'required',
            'password' => 'required',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = hash::make($request->password);
        $user->mobile = $request->mobile;
        $user->type = "3";
        $user->login_type = "email";
        $user->image = "default-logo.png";
        $user->is_available = "1";
        $user->is_verified = "1";
        $user->save();
        
        $success['token'] =  $user->createToken('')->plainTextToken;
        $success['id'] =  $user->id;
        $success['name'] =  $user->name;
        $success['email'] =  $user->email;
   
        return $this->sendResponse($success, trans('messages.success'));
    }
   
    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $checkuser = User::where('email', $request->email)->where('type', '3')->first();
        if (!empty($checkuser)) {
            if (Hash::check($request->password, $checkuser->password)) {
                $success['token'] = $checkuser->createToken('')->plainTextToken;
                $success['id'] =  $checkuser->id;
                $success['name'] =  $checkuser->name;
                $success['email'] =  $checkuser->email;
                $success['image'] =  helper::image_path($checkuser->image);
                return $this->sendResponse($success, trans('messages.success'));
            } else {
                return $this->sendError(trans('messages.email_password_not_matchemail_password_not_match'));
            }
        } else {
            return $this->sendError(trans('messages.email_password_not_matchemail_password_not_match'));
        }
    }

    public function editprofile(Request $request)
    {
        if (env('Environment') == 'sendbox') {
            return $this->sendError("This operation was not performed due to demo mode");
        }
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'name' => 'required',
            'email' => 'required|unique:users,email,' . $request->user_id,
            'mobile' => 'required',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        $edituser = User::where('id', $request->user_id)->first();
        $edituser->name = $request->name;
        $edituser->email = $request->email;
        $edituser->mobile = $request->mobile;
        if ($request->has('profile')) {
            if ($edituser->image != "" && file_exists(storage_path('app/public/admin-assets/images/profile/' . $edituser->image))) {
                unlink(storage_path('app/public/admin-assets/images/profile/' . $edituser->image));
            }
            $edit_image = $request->file('profile');
            $profileImage = 'profile-' . uniqid() . "." . $edit_image->getClientOriginalExtension();
            $edit_image->move(storage_path('app/public/admin-assets/images/profile/'), $profileImage);
            $edituser->image = $profileImage;
        }
        $edituser->update();

        $success['id'] =  $edituser->id;
        $success['name'] =  $edituser->name;
        $success['email'] =  $edituser->email;
        $success['image'] =  helper::image_path($edituser->image);

        if ($edituser) {
            return $this->sendResponse($success, trans('messages.success'));
        } else {
            return $this->sendError(trans('messages.wrong'));
        }
    }

    public function changepassword(Request $request)
    {
        if (env('Environment') == 'sendbox') {
            return $this->sendError("This operation was not performed due to demo mode");
        }
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'current_password' => 'required',
            'new_password' => 'required',
            'confirm_password' => 'required'
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
        $userdetails = User::where('id', $request->user_id)->first();
        if (Hash::check($request->current_password, $userdetails->password)) {
            if ($request->current_password == $request->new_password) {
                return $this->sendError(trans('messages.new_old_password_diffrent'));
            } else {
                if ($request->new_password == $request->confirm_password) {
                    $changepassword = User::where('id', $request->user_id)->first();
                    $changepassword->password = Hash::make($request->new_password);
                    $changepassword->update();
                    return $this->sendSuccess(trans('messages.success'));
                } else {
                    return $this->sendError(trans('messages.new_confirm_password_inccorect'));
                }
            }
        } else {
            return $this->sendError(trans('messages.old_password_incorect'));
        }
    }

    public function forgotpassword(Request $request)
    {
        if (env('Environment') == 'sendbox') {
            return $this->sendError("This operation was not performed due to demo mode");
        }
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'vendor_id' => 'required',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
        $checkuser = User::where('email', $request->email)->where('is_available', 1)->where('type',3)->first();
        if (!empty($checkuser)) {
            $password = substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 1, 6);
            $emaildata = helper::emailconfigration($request->vendor_id);
            Config::set('mail',$emaildata);

            $pass = Helper::send_pass($request->email, $checkuser->name, $password, '1');
            if ($pass == 1) {
                $checkuser->password = Hash::make($password);
                $checkuser->save();
                return $this->sendSuccess(trans('messages.success'));
            } else {
                return $this->sendError(trans('messages.wrong'));
            }
        } else {
            return $this->sendError(trans('messages.invalid_user'));
        }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        
        return response()->json(['success' => 'logout']);
    }

    public function systemaddon(Request $request)
    {
        $addons = SystemAddons::select('unique_identifier', 'activated')->get();
        
        $data = [
            'addons' =>  $addons,
            'primary_color' => @helper::appdata($request->vendor_id)->primary_color,
            'secondary_color' => @helper::appdata($request->vendor_id)->secondary_color,
        ];
        
        return $this->sendResponse($data, trans('messages.success'));
    }

    //Social Login

    public function social_loginuser(Request $request)
    {
        if ($request->name == "") {
            return $this->sendError(trans('messages.name_required'));
        }
        if ($request->email == "") {
            return $this->sendError(trans('messages.email_required'));
        }
        if ($request->token == "") {
            return $this->sendError(trans('messages.token_required'));
        }
        
        if ($request->google_id != "") {
            $checkuser = User::where('email', '=', $request->email)->where('type', '3')->where('google_id', $request->google_id)->first();
            if (!empty($checkuser)) {
                if ($checkuser->is_available == '1') {
                    $checkuser->token = $request->token;
                    $checkuser->save();
                    $checkuser = $checkuser::select('id','name','email','mobile','image','login_type')->where('id',$checkuser->id)->first();
                    $checkuser->image = helper::image_path($checkuser->image);
                    
                    return $this->sendResponse($checkuser, trans('messages.success'));
                } else {
                    return $this->sendError(trans('messages.blocked'));
                }
            } else {
                $checkemail=User::where('email',$request->email)->first();
                if(!empty($checkemail)){
                    return $this->sendError(trans('messages.unique_email'));
                }
                $data = new User();
                $data->name = $request->name;
                $data->email = $request->email;
                $data->google_id = $request->google_id;
                $data->mobile = $request->mobile ;
                $data->type = "3";
                $data->login_type = "google";
                $data->token = $request->token;
                $data->image = "default-logo.png";
                $data->is_available = "1";
                $data->is_verified = "1";
                $data->save();
                if (!empty($data)) {
                    $newuser = User::select('id','name','email','mobile','image','login_type')->where('id',$data->id)->first();
                    $newuser->image = helper::image_path($newuser->image);

                    return $this->sendResponse($newuser, trans('messages.success'));
                } else {
                    return $this->sendError(trans('messages.wrong'));
                }
            }
        }
        if ($request->facebook_id != "") {
            $checkuser = User::where('type', '3')->where('facebook_id', $request->facebook_id)->first();
            if (!empty($checkuser)) {
                if ($checkuser->is_available == '1') {
                    $checkuser->token = $request->token;
                    $checkuser->save();
                    $checkuser = $checkuser::select('id','name','email','mobile','image','login_type')->where('id',$checkuser->id)->first();
                    $checkuser->image = helper::image_path($checkuser->image);

                    return $this->sendResponse($checkuser, trans('messages.success'));
                } else {
                    return $this->sendError(trans('messages.blocked'));
                }
            } else {
                $data = new User();
                $data->name = $request->name;
                $data->email = $request->email;
                $data->google_id = $request->facebook_id;
                $data->mobile = $request->mobile ;
                $data->type = "3";
                $data->login_type = "facebook";
                $data->token = $request->token;
                $data->image = "default-logo.png";
                $data->is_available = "1";
                $data->is_verified = "1";
                $data->save();
                if (!empty($data)) {
                    $newuser = User::select('id','name','email','mobile','image','login_type')->where('id',$data->id)->first();
                    $newuser->image = helper::image_path($newuser->image);
                    return $this->sendResponse($newuser, trans('messages.success'));
                } else {
                    return $this->sendError(trans('messages.wrong'));
                }
            }
            
        }

        return $this->sendError(trans('messages.wrong'));
    }
}