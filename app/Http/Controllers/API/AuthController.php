<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Usercoin;
use App\Usernotification;

class AuthController extends BaseController {

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
                    //   'userType' => 'required',
                    'IMEI' => 'required',
        ]);

        if ($validator->fails()) {
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


        $userID = $request['userID'] ? $request['userID'] : 0;
        if ($userID != 0) {
            if ($request->is_register == 1) {
                DB::table('users')->where('id', '=', $userID)->delete();
                $userID = $request['oldUserId'] ? $request['oldUserId'] : 0;
                $user = DB::table('users')->find($userID);
            }

            if (empty($user)) {
                $user = DB::table('users')->where('social_media_id', $request['socialMediaId'])->first();
            }
            if (empty($user)) {
                $user = new User;
                $user->name = $request['name'];
                $user->email = $request['email'];
                $user->avatar = $request['avatar'];
                $user->social_media_type = $request['socialMediaType'];
                $user->social_media_id = $request['socialMediaId'];
                $user->device_type = $request['deviceType'];
                $user->device_token = $request['deviceToken'];
                // $user->user_type = $request['userType'];
                $user->IMEI = $request['IMEI'];
                $user->is_level_up = 0;
                $user->totalCoins = (setting('site.welcome_bonus') + setting('site.social_media_bonus'));
                $user->is_active = 1;
                $user->save();

                $user = DB::table('users')->where('IMEI', $request['IMEI'])->first();
                $userCoind = new Usercoin;
                $userCoind->user_id = $user->id;
                $userCoind->coins = setting('site.welcome_bonus');
                $userCoind->game_type = 6;
                $userCoind->status = 1;
                $userCoind->save();

                $userCoind = new Usercoin;
                $userCoind->user_id = $user->id;
                $userCoind->coins = setting('site.social_media_bonus');
                $userCoind->game_type = 7;
                $userCoind->status = 1;
                $userCoind->save();

                $socialMedia = 0;
                $userNotification = new Usernotification;
                $userNotification->user_id = $user->id;
                $userNotification->msg_title = 'logged in';
                if ($userNotification->social_media_type == 1) {
                    $socialMedia = 'facebook';
                } else {
                    $socialMedia = 'gmail';
                }
                $userNotification->notification_msg = 'you logged in with ' . $socialMedia . '.';
                $userNotification->is_read = 1;
                $userNotification->save();
            } else {

                DB::table('users')
                        ->where('id', $userID)
                        ->update(['name' => $request['name'],
                            'avatar' => $request['avatar'],
                            'email' => $request['email'],
                            'social_media_type' => $request['socialMediaType'],
                            'social_media_id' => $request['socialMediaId'],
                            'device_type' => $request['deviceType'],
                            'device_token' => $request['deviceToken']
                ]);

                $user = DB::table('users')->where('id',$userID)->first();
               
                $userCoind = new Usercoin;
                $userCoind->user_id = $userID;
                $userCoind->coins = setting('site.social_media_bonus');
                $userCoind->game_type = 7;
                $userCoind->status = 1;
                $userCoind->save();

                $socialMedia = 0;
                $userNotification = new Usernotification;
                $userNotification->user_id = $userID;
                $userNotification->msg_title = 'logged in';
                if ($userNotification->social_media_type == 1) {
                    $socialMedia = 'facebook';
                } else {
                    $socialMedia = 'gmail';
                }
                $userNotification->notification_msg = 'you logged in with ' . $socialMedia . '.';
                $userNotification->is_read = 1;
                $userNotification->save();
            }
        } else {
            $user = new User;
            $user->name = $request['name'];
            $user->avatar = $request['avatar'];
            $user->email = $request['email'];
            $user->social_media_type = $request['socialMediaType'];
            $user->social_media_id = $request['socialMediaId'];
            $user->device_type = $request['deviceType'];
            $user->device_token = $request['deviceToken'];
            //$user->user_type = $request['userType'];
            $user->IMEI = $request['IMEI'];
            $user->totalCoins = (setting('site.welcome_bonus') + setting('site.social_media_bonus'));
            $user->is_active = 1;
            $user->is_level_up = 0;
            $user->save();

            $user = DB::table('users')->where('IMEI', $request['IMEI'])->first();
            $userCoind = new Usercoin;
            $userCoind->user_id = $user->id;
            $userCoind->coins = setting('site.welcome_bonus');
            $userCoind->game_type = 6;
            $userCoind->status = 1;
            $userCoind->save();

            $userCoind = new Usercoin;
            $userCoind->user_id = $user->id;
            $userCoind->coins = setting('site.social_media_bonus');
            $userCoind->game_type = 7;
            $userCoind->status = 1;
            $userCoind->save();

            $socialMedia = 0;
            $userNotification = new Usernotification;
            $userNotification->user_id = $user->id;
            $userNotification->msg_title = 'logged in';
            if ($userNotification->social_media_type == 1) {
                $socialMedia = 'facebook';
            } else {
                $socialMedia = 'gmail';
            }
            $userNotification->notification_msg = 'you logged in with ' . $socialMedia . '.';
            $userNotification->is_read = 1;
            $userNotification->save();
        }
        $user = DB::table('users')->where('IMEI', $request['IMEI'])->first();
        $userLevel = ($user->totalXP) ? 0 : round($user->totalXP / 1000);
        $userLevelnew = ($user->totalXP) ? 0 : round(($user->totalXP / 1000), 3);
        $remainXP = round(($userLevelnew - $userLevel) * 1000);

        $avata = url('/') . '/images/users/default.png';
        if (!empty($user->avatar)) {
            $userImage = array();

            $userImage = explode("/", $user->avatar);
            if (isset($userImage[0]) && $userImage[0] == 'users') {
                $avata = url('/') . '/images/' . $user->avatar;
            } else {
                $avata = $user->avatar;
            }
        }

        $records = [
            "userID" => $user->id,
            "userName" => $user->name,
            "userImage" => $avata,
            "email" => $user->email,
            "is_block" => $user->is_block,
            "socialMediaType" => $user->social_media_type,
            "totalXP" => $user->totalXP,
            "totalCoins" => $user->totalCoins,
            "profit" => $user->profit,
            "wagered" => $user->wagered,
            "playedGames" => $user->playedGames,
            "rankingByLevel" => $user->rankingByLevel,
            "rankingByProfit" => $user->rankingByProfit,
            "last_read_id" => $user->last_read_id,
            "remainXP" => $remainXP,
            "is_level_up" => $user->is_level_up
        ];
        $status_code = config('response_status_code.login_success');
        return $this->sendResponse(true, $status_code, trans('message.login_success'), $records);
    }

    public function getGuestRandomNumber(Request $request) {
        $autogeneratednumber = 'guest_' . $this->randomNumber();
        $records = [
            "guestNumber" => $autogeneratednumber
        ];
        $isRegister = 1;
        $user = DB::table('users')->where('IMEI', $request['IMEI'])->first();

        if (empty($user)) {
            $last_row = DB::table('chat_logs')->orderBy('id', 'DESC')->first();

            $user = new User;
            $user->name = $autogeneratednumber;
            //$user->avatar = 'users/default.png';
            $user->social_media_type = 0;
            // $user->social_media_id = $request['socialMediaId'];
            $user->device_type = $request['deviceType'];
            $user->device_token = $request['deviceToken'];
            //$user->user_type = $request['userType'];
            $user->IMEI = $request['IMEI'];
            $user->is_active = 1;
            $user->totalCoins = setting('site.welcome_bonus');
            $user->last_read_id = $last_row->id;
            $user->is_level_up = 0;
            $user->save();
            $isRegister = 0;

            $userCoind = new Usercoin;
            $userCoind->user_id = $user->id;
            $userCoind->coins = setting('site.welcome_bonus');
            $userCoind->game_type = 6;
            $userCoind->status = 1;
            $userCoind->save();
            $user = DB::table('users')->where('IMEI', $request['IMEI'])->first();
        }

        $userLevel = ($user->totalXP) ? 0 : round($user->totalXP / 1000);
        $userLevelnew = ($user->totalXP) ? 0 : round(($user->totalXP / 1000), 3);
        $remainXP = round(($userLevelnew - $userLevel) * 1000);
        $avata = url('/') . '/images/users/default.png';

        if (!empty($chatLog->avatar)) {
            $userImage = array();

            $userImage = explode("/", $chatLog->avatar);
            if (isset($userImage[0]) && $userImage[0] == 'users') {
                $avata = url('/') . '/images/' . $chatLog->avatar;
            } else {
                $avata = $chatLog->avatar;
            }
        }
        $records = [
            "guestNumber" => $user->name,
            "userID" => $user->id,
            "userName" => $user->name,
            "userImage" => $avata,
            "email" => $user->email,
            "is_block" => $user->is_block,
            "isRegister" => $isRegister,
            "totalXP" => $user->totalXP,
            "totalCoins" => $user->totalCoins,
            "profit" => $user->profit,
            "wagered" => $user->wagered,
            "playedGames" => $user->playedGames,
            "socialMediaType" => $user->social_media_type,
            "rankingByLevel" => $user->rankingByLevel,
            "rankingByProfit" => $user->rankingByProfit,
            "last_read_id" => $user->last_read_id,
            "remainXP" => $remainXP,
            "is_level_up" => $user->is_level_up
        ];
        $status_code = config('response_status_code.random_number_fetched_success');
        return $this->sendResponse(true, $status_code, trans('message.random_number_fetched_success'), $records);
    }

    /**
     * Generate random number 
     *
     * @return [string] numberword
     */
    public function randomNumber() {
        $digit = '1234567890';
        $number = array(); //remember to declare $number as an array
        $digitLength = strlen($digit) - 1; //put the length -1 in cache
        for ($i = 0; $i < 6; $i++) {
            $n = rand(0, $digitLength);
            $number[] = $digit[$n];
        }
        return implode($number); //turn the array into a string
    }

    public function duplicateLogin(Request $request) {
        $validator = Validator::make($request->all(), [
                    'socialMediaId' => 'required'
        ]);

        if ($validator->fails()) {
            $status_code = config('response_status_code.invalid_input');
            return $this->sendResponse(false, $status_code, trans('message.invalid_input'));
        }


        $user = DB::table('users')->where('social_media_id', $request->socialMediaId)->first();

        if (empty($user)) {
            $records = [
                "social_media_id" => $request->social_media_id,
                "is_register" => 0,
                "oldUserId" => 0
            ];
            $status_code = config('response_status_code.no_records_found');
            return $this->sendResponse(true, $status_code, trans('message.no_records_found'), $records);
        } else {
            $records = [
                "social_media_id" => $request->socialMediaId,
                "is_register" => 1,
                "oldUserId" => $user->id
            ];
            $status_code = config('response_status_code.fetched_success');
            return $this->sendResponse(true, $status_code, trans('message.fetched_success'), $records);
        }
    }

}
