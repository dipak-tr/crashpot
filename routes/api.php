<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::get('get/terms-conditions', 'API\SiteController@getTermsConditions');
Route::get('get/privacy-policy', 'API\SiteController@getPrivacyPolicy');
Route::post('guest-random-number', 'API\AuthController@getGuestRandomNumber');
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

 Route::group([
      'middleware' => 'auth:api'
    ], function() {
Route::post('add/user-coins', 'API\UserCoinsControllers@addUserCoins');
Route::put('update/user-name', 'API\UserController@updateUserName');
Route::get('dashboard', 'API\UserController@getDashboard');
Route::get('get/user-profile', 'API\UserController@getUserProfile');
Route::post('report-user', 'API\UserController@reportUser');
Route::get('chat-history', 'API\ChatLogController@chatHistory');
Route::get('user-by-level', 'API\UserController@userByLevel');
Route::post('login', 'API\AuthController@login');
Route::get('duplicatelogin', 'API\AuthController@duplicateLogin');


Route::get('logout', 'API\UserController@logout');
Route::get('notification', 'API\UserNotificationController@getNotification');

 });

