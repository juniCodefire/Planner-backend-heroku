<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

use App\User;

class LoginController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function login(Request $request) {
    // Do a validation for the input 
        $this->validate($request, [

        	'email' => 'required',
        	'password' => 'required'
        ]);
    // store the request into a variable

        $email = $request->input('email');
        $password = $request->input('password');

     //Query the database with the email giving

       $user = User::where('email', $email)->first();
    //Check if rthe user exist
        if ($user === null) {
        	return response()->json(['data' =>['error' => false, 'message' => 'Not found']], 404);
        }
     //Get the user token from the database
        $token = $user->api_token;

     //Check if password match
        if (Hash::check($password, $user->password)) {
        	
        	return response()->json(['data' =>['success' => true, 'user' => $user, 'image_link' => 'http://res.cloudinary.com/getfiledata/image/upload/v1552380958/', 'token' => 'Bearer '. $token]], 200);
        }else{
        	return response()->json(['data' =>['error' => false, 'messagee' => "Invalid Credential"]], 401);
        }

    }

}
