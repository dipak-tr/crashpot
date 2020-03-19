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
        $page=$last_read_id=0;
      $page=1*$request->pgn;
      
        
        if ($validator->fails()) {
            foreach ($validator->messages()->getMessages() as $key => $value) {
                $errors[$key] = $value;
            }

            $status_code = config('response_status_code.no_records_found');
            return $this->sendResponse(true, $status_code, trans('message.no_records_found'));
        } else {
            $last_read_id = $is_update = 0;

            $chatLogs = DB::table('chat_logs')
                    ->where('id', '>', $request->last_read_id)
                 //   ->where('user_id', '<>', $request->userId)
                    ->orderByRaw('id DESC')
                    ->limit($page,10)
                    ->get();

            if ($chatLogs != NULL) {
                foreach ($chatLogs as $chatLog) {
                    $responseData[] = ["Chatlog_Id" => $chatLog->id,
                        "userID" => $chatLog->user_id,
                        "message" => $chatLog->message,
                        "createdAt" => $chatLog->created_at
                    ];
                    if ($is_update == 0) {
                        $last_read_id = $chatLog->id;
                    }
                    $is_update++;
                }

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
