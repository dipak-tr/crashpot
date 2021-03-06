<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use App\User;
use App\Reportusers;
use App\Muteusers;
use App\Usernotifications;
use Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

use Illuminate\Foundation\Auth\AuthenticatesUsers;

use Illuminate\Support\Facades\DB;

class UserController extends BaseController {

    /**
     * Update User Name using id
     * create at  = 03/03/2020
     * @param  Request  $request
     * @return [json] user object
     * */
    public function updateUserName(Request $request) {
         $user_secondTime = User::where('IMEI', '!=',$request['IMEI'])->where('id',$request['userId'])->where('is_loged',1)->first();

            if($user_secondTime)
            {    
                      //  \Laravel\Passport\Token::where('user_id', $user_secondTime->id)->delete();
                        
          return response()->json([
                "success"=> false,
                "message"=>"Another device is running App",

                 ],402);
               // $success['token'] =  $user_new->createToken('MyApp')->accessToken;
    }else{



        $validator = Validator::make($request->all(), [
                    'userId' => 'required|digits_between:1,11',
                    'userName' => 'required|string|max:100',
        ]);

        $responseData = [];
        $errors = [];

        if ($validator->fails()) {
            foreach ($validator->messages()->getMessages() as $key => $value) {
                $errors[$key] = $value;
            }

            $responseData['status_code'] = 406;
            $responseData['success'] = false;
            $responseData['message'] = "Invalid User Data !";
            $responseData['data'] = $errors;
        } else {

            $User = User::find($request->userId);

            if ($User != NULL) {
                $User->name = $request->userName;
                $User->save();

                if ($User != NULL) {
                    $responseData['status_code'] = 201;
                    $responseData['success'] = true;
                    $responseData['message'] = "User Name Update Success fully.";
                    $responseData['data'] = ['userName' => $User->name];
                } else {
                    $responseData['status_code'] = 400;
                    $responseData['success'] = false;
                    $responseData['message'] = "Faild To Update User Name !";
                    $responseData['data'] = "";
                }
            } else {
                $responseData['status_code'] = 404;
                $responseData['success'] = false;
                $responseData['message'] = "User Id Not Found !";
                $responseData['data'] = "";
            }
        }

        echo json_encode($responseData);
    }
}
    public function getDashboard(Request $request) {


         $user_secondTime = User::where('IMEI', '!=',$request['IMEI'])->where('id',$request['userId'])->where('is_loged',1)->first();

            if($user_secondTime)
            {    
                      //  \Laravel\Passport\Token::where('user_id', $user_secondTime->id)->delete();
                        
          return response()->json([
                "success"=> false,
                "message"=>"Another device is running App",

                 ],402);
               // $success['token'] =  $user_new->createToken('MyApp')->accessToken;
               }else{
        $validator = Validator::make($request->all(), [
                    'userId' => 'required|digits_between:1,11'
        ]);

        $responseData = [];
        $errors = [];
        $unreadchat = $unreadNotification = $mutedUsers = $muteUser = array();
        if ($validator->fails()) {
            foreach ($validator->messages()->getMessages() as $key => $value) {
                $errors[$key] = $value;
            }

            $status_code = config('response_status_code.no_records_found');
            return $this->sendResponse(true, $status_code, trans('message.no_records_found'));
        } else {

            $user = User::find($request->userId);

            if($user->profit<0)
            {
                $user->profit=0;
            }
            if ($user != NULL) {
                $userLevel = intdiv($user->totalXP, 1000);
                $userLevelnew = round(($user->totalXP / 1000), 3);
                $remainXP = round(($userLevelnew - $userLevel) * 1000);

                $userLevelNewCeil=ceil($userLevelnew);
                if($userLevelNewCeil==0)
                {
                    $user->rankingByLevel=1;
                }
                else
                { 
                    $user->rankingByLevel=$userLevelNewCeil;
                }
                $user->save();

                $is_level_up = 0;
                if ($user->is_level_up == 1) {
                    $user->is_level_up = 0;
                    $user->save();
                }


                $unreadNotification = DB::table('usernotifications')
                        //->leftJoin('users', 'chat_logs.user_id', '=', 'users.id')
                        ->where('user_id', '=', $request->userId)
                        ->where('is_read', '=', 0)
                        //->orderByRaw('chat_logs.id DESC')
                        //->offset($page)
                        //->limit(10)
                        ->select('id')
                        ->get();
                if ($user->social_media_type != 0 && $user->is_block == 0) {
                    $unreadchat = DB::table('chat_logs')
                            //->leftJoin('users', 'chat_logs.user_id', '=', 'users.id')
                            ->where('user_id', '=', $request->userId)
                            ->where('id', '>', $user->last_read_id)
                            //->orderByRaw('chat_logs.id DESC')
                            //->offset($page)
                            //->limit(10)
                            ->select('id')
                            ->get();

                    $mutedUsers = DB::table('muteusers')
                            //->leftJoin('users', 'chat_logs.user_id', '=', 'users.id')
                            ->where('user_id', '=', $request->userId)
                            //->where('id', '>', $user->last_read_id)F
                            //->orderByRaw('chat_logs.id DESC')
                            //->offset($page)
                            //->limit(10)
                            ->select('mute_user_id')
                            ->get();

                    if (count($mutedUsers) > 0) {
                        foreach ($mutedUsers as $mutedUser) {
                            $muteUser[] = $mutedUser->mute_user_id;
                        }
                    }
                }

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

               if($user->ranking==0 && $user->totalXP==0)
            {
                
                $count=User::count();
                
                $user->ranking=$count;
            }
              if($user->profit==0 && $user->totalCoins==100000)
            {
                
                $count=User::count();
                
                $user->rankingByProfit=$count;
            }

     
                $responseData = ["guestNumber" => $user->name,
                    "userID" => $user->id,
                    "userName" => $user->name,
                    "userImage" => $avata,
                    "email" => $user->email,
                    "is_block" => $user->is_block,
                    "totalXP" => $user->totalXP,
                    "totalCoins" => $user->totalCoins,
                    "profit" => $user->profit,
                    "wagered" => $user->wagered,
                    "playedGames" => $user->playedGames,
                    "rankingByLevel" => $user->rankingByLevel,
                    "RankingByLevelPostion" => $user->ranking,
                    "rankingByProfit" => $user->rankingByProfit,
                    "rankingByProfitPosition" => $user->rankingByProfit,
                    "last_read_id" => $user->last_read_id,
                    "remainXP" => $remainXP,
                    "is_level_up" => $is_level_up,
                    "notificationCNT" => count($unreadNotification),
                    "chatCNT" => count($unreadchat),
                    "mutedUser" => $muteUser
                ];
            }
             else {
                $status_code = config('response_status_code.no_records_found');
                return $this->sendResponse(true, $status_code, trans('message.no_records_found'));
            }
        }

        $status_code = config('response_status_code.dashboard_fetched_success');
        return $this->sendResponse(true, $status_code, trans('message.dashboard_fetched_success'), $responseData);
    }
}
    public function getUserProfile(Request $request) {

      
        $validator = Validator::make($request->all(), [
                    'userId' => 'required|digits_between:1,11'
        ]);

        $responseData = [];
        $errors = [];

        if ($validator->fails()) {
            foreach ($validator->messages()->getMessages() as $key => $value) {
                $errors[$key] = $value;
            }

            $status_code = config('response_status_code.no_records_found');
            return $this->sendResponse(true, $status_code, trans('message.no_records_found'));
        } else {

            $user = User::find($request->userId);
             if($user->profit<0)
            {
                $user->profit=0;
            }

            if ($user != NULL) {
                $userLevel = intdiv($user->totalXP, 1000);
                $userLevelnew = round(($user->totalXP / 1000), 3);
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

                $responseData = ["guestNumber" => $user->name,
                    "userID" => $user->id,
                    "userName" => $user->name,
                    "userImage" => $avata,
                    "email" => $user->email,
                    "is_block" => $user->is_block,
                    "totalXP" => $user->totalXP,
                    "totalCoins" => $user->totalCoins,
                    "profit" => $user->profit,
                    "wagered" => $user->wagered,
                    "playedGames" => $user->playedGames,
                    "rankingByLevel" => $user->rankingByLevel,
                    "RankingByLevelPostion" => $user->ranking,
                    "rankingByProfit" => $user->rankingByProfit,
                    "rankingByProfitPosition" => $user->rankingByProfit,
                    "last_read_id" => $user->last_read_id,
                    "remainXP" => $remainXP,
                    "is_level_up" => $user->is_level_up
                ];
            } else {
                $status_code = config('response_status_code.no_records_found');
                return $this->sendResponse(true, $status_code, trans('message.no_records_found'));
            }
        }

        $status_code = config('response_status_code.fetched_success');
        return $this->sendResponse(true, $status_code, trans('message.fetched_success'), $responseData);
    
}
    public function reportUser(Request $request) {

     $user_secondTime = User::where('IMEI', '!=',$request['IMEI'])->where('id',$request['userId'])->where('is_loged',1)->first();

            if($user_secondTime)
            {    
                      //  \Laravel\Passport\Token::where('user_id', $user_secondTime->id)->delete();
                        
          return response()->json([
                "success"=> false,
                "message"=>"Another device is running App",

                 ],402);
               // $success['token'] =  $user_new->createToken('MyApp')->accessToken;
    }else{
        $validator = Validator::make($request->all(), [
                    'userId' => 'required|digits_between:1,11',
                    'reportUserId' => 'required|digits_between:1,11',
                    'reportType' => 'required|digits_between:1,11',
                    'chatMessage' => 'required|string|max:1000'
        ]);

        $responseData = [];
        $errors = [];

        if ($validator->fails()) {
            foreach ($validator->messages()->getMessages() as $key => $value) {
                $errors[$key] = $value;
            }

            $status_code = config('response_status_code.no_records_found');
            return $this->sendResponse(true, $status_code, trans('message.no_records_found'));
        } else {

            
            
            if ($request->reportType == 1) {

                $muteUser = new Muteusers;
                $muteUser->user_id = $request->userId;
                $muteUser->mute_user_id = $request->reportUserId;
                $muteUser->save();
                $reportUser = new Reportusers;
                $reportUser->user_id = $request->reportUserId;
                $reportUser->chat_message = $request->chatMessage;
                $reportUser->created_by = $request->userId;
                $reportUser->save();
            }
                 if ($request->reportType == 2) {

                $muteUser = new Muteusers;
                $muteUser->user_id = $request->userId;
                $muteUser->mute_user_id = $request->reportUserId;
                $muteUser->save();
               
            }

            if ($request->reportType == 3) {
                $reportUser = new Reportusers;
                $reportUser->user_id = $request->reportUserId;
                $reportUser->chat_message = $request->chatMessage;
                $reportUser->created_by = $request->userId;
                $reportUser->save();
            }
        }
        $responseData = ["userId" => $request->userId];
        $status_code = config('response_status_code.fetched_success');
        return $this->sendResponse(true, $status_code, trans('message.fetched_success'), $responseData);
    }
}
    public function userByLevel(Request $request) {

           

        $validator = Validator::make($request->all(), [
                    'userId' => 'required|digits_between:1,11',
                    'levelType' => 'digits_between:1,4',
        ]);

        $responseData = [];
        $errors = [];

        if ($validator->fails()) {
            foreach ($validator->messages()->getMessages() as $key => $value) {
                $errors[$key] = $value;
            }

            $status_code = config('response_status_code.no_records_found');
            return $this->sendResponse(true, $status_code, trans('message.no_records_found'));
        } else {
            $page = 0;
            $rank = 1;
            /* $users = DB::table("users")
              ->select("users.*", DB::raw("(SELECT sum(coins) as wincoins FROM `usercoins` WHERE `status` = 1 AND `is_xp_or_coin` = 0 AND user_id=users.id) as wincoins"), DB::raw("(SELECT sum(coins) as losscoins FROM `usercoins` WHERE `status` = 0 AND `is_xp_or_coin` = 0 AND user_id=users.id) as losscoins"))
              ->get(); */

            if ($request->levelType == 1) {
                $users = DB::table('users')
                        //->where('id', '>', $request->last_read_id)
                        ->where('is_active', '=', 1)
                        ->orderByRaw('profit DESC')
                        ->offset($page)
                        ->limit(500)
                        //->select('name', 'users.email', 'users.avatar')
                        ->get();
            } else {
                $users = DB::table('users')
                        ->where('is_active', '=', 1)
                        ->orderByRaw('totalXP DESC')
                        ->offset($page)
                        ->limit(500)
                        ->get();
            }

            if (count($users) > 0 && $users != NULL) {
                foreach ($users as $user) {

                    if ($request->levelType == 1) {
                        $userData = User::find($user->id);
                        $userData->rankingByProfit = $rank;
                        $rankingByLevel = $user->ranking;
                        $rankingByProfit = $rank;
                        $userData->save();
                    } else {
                        $userData = User::find($user->id);
                        $userData->ranking = $rank;
                        $rankingByProfit = $user->rankingByProfit;
                        $rankingByLevel = $rank;
                        $userData->save();
                    }

                    $rank++;
                }
            }

            $RankingByLevelPostion = $rankingByProfitPosition = 1;
            $userLogData = User::find($request->userId);
           
            $RankingByLevelPostion = $userLogData->ranking;

            $rankingByProfitPosition = $userLogData->rankingByProfit;

            $rank = 1;

            if ($request->levelType == 1) {
                $users = DB::table('users')
                        //->where('id', '>', $request->last_read_id)
                        ->where('is_active', '=', 1)
                        ->orderByRaw('profit DESC')
                        ->offset($page)
                        ->limit(50)
                        //->select('name', 'users.email', 'users.avatar')
                        ->get();
            } else {
                $users = DB::table('users')
                        ->where('is_active', '=', 1)
                        ->orderByRaw('totalXP DESC')
                        ->offset($page)
                        ->limit(50)
                        ->get();
            }
         
            if (count($users) > 0 && $users != NULL) {
                foreach ($users as $user) {
                    $userData = User::find($user->id);
                    $userLevel = intdiv($user->totalXP, 1000);
                    $userLevelnew = round(($user->totalXP / 1000), 3);
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
                    
                    /*
                      if ($request->levelType == 1) {
                      $userData->rankingByProfit = $rank;
                      $rankingByLevel = $user->rankingByLevel;
                      $rankingByProfit = $rank;
                      if ($request->userId == $user->id) {
                      $RankingByLevelPostion = $rankingByLevel;
                      $rankingByProfitPosition = $rankingByProfit;
                      }
                      $userData->save();
                      } else {
                      //$userData->rankingByLevel = $rank;
                      $rankingByProfit = $user->rankingByProfit;
                      $rankingByLevel = $rank;
                      if ($request->userId == $user->id) {
                      $RankingByLevelPostion = $rankingByLevel;
                      $rankingByProfitPosition = $rankingByProfit;
                      }
                      }

                      $rank++; */
                       if($user->profit<0)
            {               
                $user->profit=0;
            }
                    $responseData[] = ["guestNumber" => $user->name,
                        "userID" => $user->id,
                        "userName" => $user->name,
                        "userImage" => $avata,
                        "profit" => $user->profit,
                        "wagered" => $user->wagered,
                        "playedGames" => $user->playedGames,
                        "rankingByLevel" => $user->rankingByLevel,
                        "ranking" => $user->ranking,
                        "rankingByProfit" => $user->rankingByProfit,
                        "remainXP" => $remainXP,
                        "RankingByLevelPostion" => $RankingByLevelPostion,
                        "rankingByProfitPosition" => $rankingByProfitPosition
                    ];
                    
                }
            } else {
                $status_code = config('response_status_code.no_records_found');
                return $this->sendResponse(true, $status_code, trans('message.no_records_found'));
            }
            $status_code = config('response_status_code.fetched_success');
            return $this->sendResponse(true, $status_code, trans('message.fetched_success'), $responseData);
        }
    }

    public function logout(Request $request) {
        $validator = Validator::make($request->all(), [
                    'userId' => 'required|digits_between:1,11'
        ]);

        $responseData = [];
        $errors = [];

        if ($validator->fails()) {
            foreach ($validator->messages()->getMessages() as $key => $value) {
                $errors[$key] = $value;
            }
            $status_code = config('response_status_code.invalid_input');
            return $this->sendResponse(true, $status_code, trans('message.invalid_input'));
        } else {
            $User = User::find($request->userId);
            if ($User != NULL) {

                $User->IMEI = ' ';
                $User->is_loged = 0;

                $User->save();
                
                $userNotification = new Usernotifications;
                $userNotification->user_id = $request->userId;
                $userNotification->msg_title = 'logout ';
                $userNotification->notification_msg = 'you logout from the App.';
                $userNotification->is_read = 1;
                $userNotification->save();

                $responseData = [
                    "userID" => $User->id,
                    "userName" => $User->name
                ];
            } else {
                $status_code = config('response_status_code.invalid_input');
                return $this->sendResponse(true, $status_code, trans('message.invalid_input'));
            }
        }
           
        
        $status_code = config('response_status_code.fetched_success');
        return $this->sendResponse(true, $status_code, trans('message.fetched_success'));
    }

}
