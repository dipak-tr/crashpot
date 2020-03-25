<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use Validator;
use App\Usercoin;
use App\User;

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
            $Usercoin->is_xp_or_coin = $request->is_xp_or_coin;
            $Usercoin->save();

            if ($Usercoin != NULL) {
                if ($request->is_xp_or_coin == 1) {
                    if ($request->status == 1) {
                        $User = User::find($request->userId);
                        $User->totalXP += $request->coins;

                        /* if ($User->rankingByLevel < round((($User->totalXP + $request->coins) / 1000),0,PHP_ROUND_HALF_ODD)) 
                          { */

                        $User->rankingByLevel = intdiv($User->totalXP, 1000);
                        //}
                        /* if ($User->rankingByLevel != round(($User->totalXP + $request->coins) / 1000)) {
                          $User->is_level_up = 1;
                          } else {
                          $User->is_level_up = 0;
                          } */
                        $User->save();
                    }/* else {
                      $User = User::find($request->userId);
                      $User->totalXP -= $request->coins;
                      $User->rankingByLevel = round(($User->totalXP - $request->coins) / 1000);
                      if ($User->rankingByLevel != round(($User->totalXP - $request->coins) / 1000)) {
                      $User->is_level_up = 1;
                      } else {
                      $User->is_level_up = 0;
                      }
                      $User->save();
                      } */
                    $user = User::find($request->userId);
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
                        "rankingByProfit" => $user->rankingByProfit,
                        "last_read_id" => $user->last_read_id,
                        "remainXP" => $remainXP,
                        "is_level_up" => $user->is_level_up
                    ];

                    $status_code = config('response_status_code.xp_add');
                    return $this->sendResponse(true, $status_code, trans('message.xp_add'), $responseData);
                } else {
                    if ($request->status == 1) {
                        $User = User::find($request->userId);
                        $User->totalCoins += $request->coins;
                        //$User->rankingByProfit += $request->coins;
                        $User->profit += $request->coins;

                        if ($request->gameType == 5) {
                            $User->playedGames = $User->playedGames + 1;
                        }
                        //$User->rankingByLevel = round(($User->totalXP + $request->coins) / 1000);
                        /* if ($User->rankingByLevel != round($User->totalXP / 1000)) {
                          $User->is_level_up = 1;
                          } else {
                          $User->is_level_up = 0;
                          } */
                        $User->save();
                    } else {
                        $User = User::find($request->userId);
                        $User->totalCoins -= $request->coins;
                       // $User->rankingByProfit -= $request->coins;
                        $User->profit -= $request->coins;
                        if ($request->gameType == 5) {
                            $User->playedGames = $User->playedGames + 1;
                        }
                        // $User->rankingByLevel = round(($User->totalXP - $request->coins) / 1000);
                        /* if ($User->rankingByLevel != round($User->totalXP / 1000)) {
                          $User->is_level_up = 1;
                          } else {
                          $User->is_level_up = 0;
                          } */
                        $User->save();
                    }
                    $user = User::find($request->userId);
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
                        "rankingByProfit" => $user->rankingByProfit,
                        "last_read_id" => $user->last_read_id,
                        "remainXP" => $remainXP,
                        "is_level_up" => $user->is_level_up
                    ];
                    $status_code = config('response_status_code.coin_add');
                    return $this->sendResponse(true, $status_code, trans('message.coin_add'), $responseData);
                }
            } else {
                $status_code = config('response_status_code.invalid_input');
                return $this->sendResponse(true, $status_code, trans('message.invalid_input'));
            }
        }
    }

}
