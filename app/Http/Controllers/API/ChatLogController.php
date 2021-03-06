<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use App\User;
use App\Chatlog;
use App\Reportusers;
use Validator;
use Illuminate\Support\Facades\DB;

class ChatLogController extends BaseController {


    public function chatHistory(Request $request) {
         $user_secondTime = User::where('IMEI', '!=',$request['IMEI'])->where('id',$request['userId'])->where('is_loged',1)->first();

            if($user_secondTime)
            {    
                      //  \Laravel\Passport\Token::where('user_id', $user_secondTime->id)->delete();
                        
          return response()->json([
                "success"=> false,
                "message"=>"Another device is running App",

                 ],402);
               // $success['token'] =  $user_new->createToken('MyApp')->accessToken;
    }else
    {
        $validator = Validator::make($request->all(), [
                    'userId' => 'required|digits_between:1,11',
                    'last_read_id' => 'digits_between:0,11',
                    'pgn' => 'digits_between:0,11'
        ]);

        $responseData = [];
        $errors = [];
        $page = $last_read_id = 0;
        $page = 10 * $request->pgn;


        if ($validator->fails()) {
            foreach ($validator->messages()->getMessages() as $key => $value) {
                $errors[$key] = $value;
            }

            $status_code = config('response_status_code.no_records_found');
            return $this->sendResponse(true, $status_code, trans('message.no_records_found'));
        } else
         {
            $last_read_id = $is_update = 0;
            $muteUser = array();
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
            $chatLogs = DB::table('chat_logs')
                    ->leftJoin('users', 'chat_logs.user_id', '=', 'users.id')
                    ->where('chat_logs.id', '>', $request->last_read_id)
                    ->where('users.is_active', '=', 1)
                    ->whereNotIn('chat_logs.user_id', $muteUser)
                    ->orderByRaw('chat_logs.id DESC')
                    ->offset($page)
                    ->limit(10)
                    ->select('chat_logs.*', 'users.name', 'users.email', 'users.avatar', 'users.rankingByLevel')
                    ->get();

            if ($chatLogs != NULL && count($chatLogs) != 0) {
                foreach ($chatLogs as $chatLog) {

                    // if (empty($muteUser) && !in_array($chatLog->user_id, $muteUser)) {

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
                    $responseData[] = [
                        "message" => $chatLog->message,
                        "userId" => $chatLog->user_id,
                        "name" => $chatLog->name,
                        "email" => $chatLog->email,
                        "userImage" => $avata,
                        "messageType" => $chatLog->messageType,
                        "rankingByLevel" => $chatLog->rankingByLevel,
                        "tagUserList" => $chatLog->tagUserList,
                        "messageId" => $chatLog->id,
                        "time" => $chatLog->created_at,
                        "messageType" => $chatLog->messageType,
                        "tagUserList" => $chatLog->tagUserList
                    ];
                    if ($is_update == 0) {
                        $last_read_id = $chatLog->id;
                    }
                    $is_update++;
                    //}
                }
                $responseData = array_reverse($responseData);
                DB::table('users')
                        ->where('id', $request->userId)
                        ->update(['last_read_id' => $last_read_id
                ]);
            } else {
                $status_code = config('response_status_code.no_records_found');
                return $this->sendResponse(true, $status_code, trans('message.no_records_found'));
            }
        }
        if (count($responseData) == 0) {
            $status_code = config('response_status_code.no_records_found');
            return $this->sendResponse(true, $status_code, trans('message.no_records_found'));
        } else {
            $status_code = config('response_status_code.fetched_success');
            return $this->sendResponse(true, $status_code, trans('message.fetched_success'), $responseData);
        }
    }

}
}