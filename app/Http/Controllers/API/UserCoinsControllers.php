<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use Validator;
use App\Usercoin;

class UserCoinsControllers extends BaseController {

    /**
     * Add User Coins using Api
     * create at  = 03/03/2020
     * @param  Request  $request
     * @return [json] user object
     * */
    public function addUserCoins(Request $request) {
        $validator = Validator::make($request->all(), [
                    'userId' => 'required|digits_between:1,11',
                    'coins' => 'required|digits_between:1,11',
                    'gameType' => 'required|digits_between:1,4',
                    'status' => 'required|digits_between:1,4',
                    'fromUserId' => 'digits_between:1,11',
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

            $Usercoin = new Usercoin;
            $Usercoin->user_id = $request->userId;
            $Usercoin->coins = $request->coins;
            $Usercoin->game_type = $request->gameType;
            $Usercoin->status = $request->status;
            $Usercoin->from_userid = $request->fromUserId;
            $Usercoin->save();

            if ($Usercoin != NULL) {
                $status_code = config('response_status_code.coin_add');
                return $this->sendResponse(true, $status_code, trans('message.coin_add'), $responseData);
            } else {
                $status_code = config('response_status_code.invalid_input');
                return $this->sendResponse(true, $status_code, trans('message.invalid_input'));
            }
        }
    }
}