<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use App\User;
use Validator;

class AuthController extends BaseController
{

    /**
     * Login user using Social Media.
     *
     * @param  Request  $request
     * @return [json] user object
     */
    public function login(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'avatar' => 'required',
            'socialMediaType' => 'required',
            'socialMediaId' => 'required',
            'deviceType' => 'required',
            'deviceToken' => 'required',
            'userType' => 'required',
            'IMEI' => 'required',
        ]);

        if($validator->fails()) {
            $status_code = config('response_status_code.invalid_input');
            return $this->sendResponse(false, $status_code, trans('message.invalid_input'));
        }

        // $user = User::where('phone_number', $request['phoneNumber'])->first();
        // if(!$user) {
        //     $status_code = config('response_status_code.user_not_registered');
        //     return $this->sendResponse(false, $status_code, trans('message.user_not_registered'));
        // }

        // $isActive = ($user->is_active == '1') ? true : false;
        // $isIMEIVerified = (empty($user->imei_number) || $user->imei_number == $request['IMEI']) ? true : false;

        // $response_user['isActive'] = $isActive;
        // $response_user['isIMEIVerified'] = $isIMEIVerified;

        // if(!$isActive) {
        //     $status_code = config('response_status_code.user_not_active');
        //     return $this->sendResponse(true, $status_code, trans('message.user_not_active'), $response_user);
        // }

        // if(!$isIMEIVerified) {
        //     $status_code = config('response_status_code.imei_number_mismatch');
        //     return $this->sendResponse(true, $status_code, trans('message.imei_number_mismatch'), $response_user);
        // }

        $user = new User;
        $user->name = $request['name'];
        $user->avatar = $request['avatar'];
        $user->social_media_type = $request['socialMediaType'];
        $user->social_media_id = $request['socialMediaId'];
        $user->device_type = $request['deviceType'];
        $user->device_token = $request['deviceToken'];
        $user->user_type = $request['userType'];
        $user->IMEI = $request['IMEI'];
        $user->is_active = 1;
        $user->save();

        $status_code = config('response_status_code.login_success');
        return $this->sendResponse(true, $status_code, trans('message.login_success'));
    }
}
