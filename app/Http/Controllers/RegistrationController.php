<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

Use App\User;


class RegistrationController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function store(Request $request, User $user) {

    	$attribute = $this->validate($request, [
    		'name' => 'required',
    		'email' => 'required|email|unique:users',
    		'phone_number' => 'required|min:10|numeric',
    		'password' => 'required|min:6|confirmed',
            'account_type' => 'required'
    	]);

    	//generate a ramdom api token for user recognition
    	$generateRandomString = Str::random(60);

        $token = hash('sha256', $generateRandomString);
        //Generatate a token for the password recvery process
        $generateRecoveryToken = Str::random(60);

        $password_recover_token = hash('sha256', $generateRecoveryToken);
        
        //insert the details into the user class and into the model class

            $user->name = ucwords($request->input('name'));
            $user->email = $request->input('email');
            $user->password_recover_token = $password_recover_token;
            $user->phone_number = $request->input('phone_number');
            $user->password = Hash::make($request->input('password'));
            $user->account_type = ucfirst($request->input('account_type'));


            $user->user_image = "user.jpg";
            $user->api_token = $token;

            $info = $user->save();


            if ($info) {
               return response()->json(['data' => ['success' => true, 'user' => $user, 'image_link' => 'http://res.cloudinary.com/getfiledata/image/upload/v1552380958/', 'token' => 'Bearer '. $token]], 201);
            }else{
                return response()->json(['data' => ['error' => false, 'message' => 'An Error Occured!']], 401); 
            }  	
        
    }
}
