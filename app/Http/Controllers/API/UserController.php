<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use App\User;
use Validator;
use Illuminate\Support\Facades\DB;


class UserController extends BaseController {

    /**
     * Update User Name using id
     * create at  = 03/03/2020
     * @param  Request  $request
     * @return [json] user object
     * */
    public function updateUserName(Request $request) {
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

    public function getDashboard(Request $request) {
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

            if ($user != NULL) {
                $responseData = ["guestNumber" => $user->name,
                    "userID" => $user->id,
                    "userName" => $user->name,
                    "totalXP" => $user->totalXP,
                    "totalCoins" => $user->totalCoins,
                    "profit" => $user->profit,
                    "wagered" => $user->wagered,
                    "playedGames" => $user->playedGames,
                    "rankingByLevel" => $user->rankingByLevel,
                    "rankingByProfit" => $user->rankingByProfit
                ];
            } else {
                $status_code = config('response_status_code.no_records_found');
                return $this->sendResponse(true, $status_code, trans('message.no_records_found'));
            }
        }

        $status_code = config('response_status_code.dashboard_fetched_success');
        return $this->sendResponse(true, $status_code, trans('message.dashboard_fetched_success'), $responseData);
    }
}