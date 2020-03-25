<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use App\User;
use App\Reportusers;
use Validator;
use Illuminate\Support\Facades\DB;

class UserNotificationController extends BaseController {

    public function getNotification(Request $request) {
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

            
            $userNotifications = DB::table('usernotification')
                    ->where('user_id', '=', $request->userId)
                    ->orderByRaw('id DESC')
                    //->offset($page)
                   // ->limit(500)
                    ->get();
        }

        if (count($userNotifications) > 0 && $userNotifications != NULL) {
            foreach ($userNotifications as $userNotification) {
                 
            $responseData[] = ["msgTitle" => strtoupper($userNotification->msg_title),
                "notificationMsg" => $userNotification->notification_msg,
                "createdAt" => $user->id
            ];
            }
        }
         else {
            $status_code = config('response_status_code.no_records_found');
            return $this->sendResponse(true, $status_code, trans('message.no_records_found'));
        }
        

        $status_code = config('response_status_code.dashboard_fetched_success');
        return $this->sendResponse(true, $status_code, trans('message.dashboard_fetched_success'), $responseData);
    }

    
}
