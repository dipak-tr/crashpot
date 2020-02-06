<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;

class BaseController extends Controller
{
    /**
     * response method. (created date: 22-03-2019, created by: Tridhya Tech)
     *
     * @param [bool] success
     * @param [int] status_code
     * @param [string] message
     * @param [object] data
     * @param [int] http_code
     * @return \Illuminate\Http\Response
     */
    public function sendResponse($success, $status_code, $message, $data= [], $http_code = 200)
    {

        $response = [
            'success' => $success,
            'status_code' => $status_code,
            'message' => $message,
            'data'    => $data,
        ];
        
        return response()->json($response, $http_code);
    }
}