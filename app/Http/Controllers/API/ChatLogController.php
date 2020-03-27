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
        } else {
            $last_read_id = $is_update = 0;

            $chatLogs = DB::table('chat_logs')
                    ->leftJoin('users', 'chat_logs.user_id', '=', 'users.id')
                    ->where('chat_logs.id', '>', $request->last_read_id)
                    ->where('users.is_active', '=', 1)
                    ->orderByRaw('chat_logs.id DESC')
                    ->offset($page)
                    ->limit(10)
                    ->select('chat_logs.*', 'users.name', 'users.email', 'users.avatar')
                    ->get();

            if ($chatLogs != NULL && count($chatLogs) != 0) {
                foreach ($chatLogs as $chatLog) {

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
                        "messageId" => $chatLog->id,
                        "time" => $chatLog->created_at,
                        "messageType" => $chatLog->messageType,
                        "tagUserList" => $chatLog->tagUserList
                    ];
                    if ($is_update == 0) {
                        $last_read_id = $chatLog->id;
                    }
                    $is_update++;
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

        $status_code = config('response_status_code.fetched_success');
        return $this->sendResponse(true, $status_code, trans('message.fetched_success'), $responseData);
    }

}
