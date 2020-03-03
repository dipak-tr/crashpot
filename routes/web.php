<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');


Route::group(['prefix' => 'admin'], function () {
    
    Voyager::routes();

    Route::post('users/change-activation-status','Voyager\VoyagerUserController@changeActivationStatus')->name('users.changeActivationStatus');
    Route::post('users/change-block-status','Voyager\VoyagerUserController@changeBlockStatus')->name('users.changeBlockStatus');
});
