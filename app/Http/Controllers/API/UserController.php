<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Validator;


class UserController extends Controller
{
    /**
     * Update User Name using id
     * create at  = 03/03/2020
     * @param  Request  $request
     * @return [json] user object
     **/
    public function updateUserName(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'userId'     => 'required|digits_between:1,11',
            'userName'      => 'required|string|max:100',
            ]);

        $responseData 	= [];
        $errors         = [];

        if ($validator->fails()) {
			foreach ($validator->messages()->getMessages() as $key => $value)
			{
  				$errors[$key] = $value;
            }
            
            $responseData['status_code'] = 406 ;
			$responseData['success'] 	 = false;
			$responseData['message'] 	 = "Invalid User Data !";
			$responseData['data'] 	 =  $errors;

        } else {

            $User = User::find($request->userId);

            if($User != NULL)
            {
                $User->name  = $request->userName;
                $User->save();
    
                if($User != NULL)
                {
                    $responseData['status_code'] = 201;
                    $responseData['success'] 	 = true;
                    $responseData['message'] 	 = "User Name Update Success fully."; 
                    $responseData['data'] 	     =  ['userName' => $User->name];
                        
                }
                else
                {
                    $responseData['status_code'] = 400;
                    $responseData['success'] 	 = false;
                    $responseData['message'] 	 = "Faild To Update User Name !"; 
                    $responseData['data'] 	     =  "";
                }
    
            }else{
                $responseData['status_code'] = 404;
                $responseData['success'] 	 = false;
                $responseData['message'] 	 = "User Id Not Found !"; 
                $responseData['data'] 	     =  "";

            }
        }

        echo json_encode($responseData);
    }
}
