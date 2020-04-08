<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Usercoin;

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
        $old_user_id = $request['oldUserId'];
         $user_id = $request['userID'];
        
        $user_social_media=$request['socialMediaId'];
         $users=User::where('social_media_id',$user_social_media)->get();
         $username=$users[0]['name'];
          if (!$users->isEmpty()) {
        

          DB::table('users')
                        ->where('id', $old_user_id)
                       ->update(['name' => $username,
                            'avatar' => $request['avatar'],
                            'email' => $request['email'],
                            'social_media_type' => $request['socialMediaType'],
                            'social_media_id' => $request['socialMediaId'],
                            'device_type' => $request['deviceType'],
                            'device_token' => $request['deviceToken'],
                            'IMEI' => $request['IMEI'],

                ]);

                $user = DB::table('users')->where('IMEI', $request['IMEI'])->first();
                $userCoind = new Usercoin;
                $userCoind->user_id = $user->id;
                $userCoind->coins = 1;
                $userCoind->game_type = 7;
                $userCoind->status = 1;
                $userCoind->save();
                $deletedRows = User::where('id',$user_id)->delete();

          }

else{

        $userID = $request['userID'] ? $request['userID'] : 0;
         $user_id = $request['userID'];
             $users=User::where('id',$user_id)->get();
        if ($userID != 0) {
            
            if ($users->isEmpty()) {
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
                $userCoind->coins = 1;
                $userCoind->game_type = 6;
                $userCoind->status = 1;
                $userCoind->save();

                $userCoind = new Usercoin;
                $userCoind->user_id = $user->id;
                $userCoind->coins = 1;
                $userCoind->game_type = 7;
                $userCoind->status = 1;
                $userCoind->save();
            } else {

                DB::table('users')
                        ->where('id', $request['userID'])
                        ->update(['name' => $request['name'],
                            'avatar' => $request['avatar'],
                            'email' => $request['email'],
                            'social_media_type' => $request['socialMediaType'],
                            'social_media_id' => $request['socialMediaId'],
                            'device_type' => $request['deviceType'],
                            'device_token' => $request['deviceToken']
                ]);

                $user = DB::table('users')->where('IMEI', $request['IMEI'])->first();
                $userCoind = new Usercoin;
                $userCoind->user_id = $user->id;
                $userCoind->coins = 1;
                $userCoind->game_type = 7;
                $userCoind->status = 1;
                $userCoind->save();



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
            $userCoind->coins = 1;
            $userCoind->game_type = 7;
            $userCoind->status = 1;
            $userCoind->save();
        }

}
        $user = DB::table('users')->where('IMEI', $request['IMEI'])->first();
        $userLevel = ($user->totalXP) ? 0 : round($user->totalXP / 1000);
        $userLevelnew = ($user->totalXP) ? 0 : round(($user->totalXP / 1000), 3);
        $remainXP = round(($userLevelnew - $userLevel) * 1000);

        $records = [
            "userID" => $user->id,
            "userName" => $user->name,
            "userImage" => $user->avatar,
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
            $last_row = DB::table('users')->orderBy('id', 'DESC')->first();

            $user = new User;
            $user->name = $autogeneratednumber;
            //$user->avatar = 'users/default.png';
            $user->social_media_type = 0;
            // $user->social_media_id = $request['socialMediaId'];
            $user->device_type = $request['deviceType'];
            $user->device_token = $request['deviceToken'];
            //$user->user_type = $request['userType'];
            $user->IMEI = $request['IMEI'];
            $user->rankingByLevel=1;
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
            //$user = DB::table('users')->where('IMEI', $request['IMEI'])->first();
        }
         
       

        $userLevel = ($user->totalXP) ? 0 : round($user->totalXP / 1000);
        $userLevelnew = ($user->totalXP) ? 0 : round(($user->totalXP / 1000), 3);
        $remainXP = round(($userLevelnew - $userLevel) * 1000);

        $records = [
            "guestNumber" => $user->name,
            "userID" => $user->id,
            "userName" => $user->name,
            "userImage" => $user->avatar,
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