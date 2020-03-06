<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Usercoin;

class UserCoinsControllers extends Controller {

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

            $responseData['status_code'] = 406;
            $responseData['success'] = false;
            $responseData['message'] = "Invalid Coins Data !";
            $responseData['data'] = $errors;
        } else {

            $Usercoin = new Usercoin;
            $Usercoin->user_id = $request->userId;
            $Usercoin->coins = $request->coins;
            $Usercoin->game_type = $request->gameType;
            $Usercoin->status = $request->status;
            $Usercoin->from_userid = $request->fromUserId;
            $Usercoin->save();

            if ($Usercoin != NULL) {
                $responseData['status_code'] = 201;
                $responseData['success'] = true;
                $responseData['message'] = "Coins Add Successfully..";
                $responseData['data'] = $Usercoin;
            } else {
                $responseData['status_code'] = 400;
                $responseData['success'] = false;
                $responseData['message'] = "Faild To Add Coins !";
                $responseData['data'] = "";
            }
        }

        echo json_encode($responseData);
    }

}
