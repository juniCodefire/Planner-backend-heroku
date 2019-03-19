<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeNewUser;

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
        $generateVerifyToken = Str::random(60);

        $verify_token = hash('adler32', $generateVerifyToken);

        //generate a ramdom api token for user confirmation
        $generateRandomConfirm = Str::random(60);

        $confirm_token = hash('sha256', $generateRandomConfirm);
        
        //insert the details into the user class and into the model class

            $user->name = ucwords($request->input('name'));
            $user->email = $request->input('email');
            $user->phone_number = $request->input('phone_number');
            $user->password = Hash::make($request->input('password'));
            $user->account_type = ucfirst($request->input('account_type'));

            $user->verify_code = $verify_token;
            $user->user_image = "user.jpg";
            $user->api_token = $token;
            $user->confirm_token = $confirm_token;


                 try{
                     Mail::to($user->email)->send(new WelcomeNewUser($user)); 
                  } catch (Exception $ex) {

                     return response()->json(['data' =>['success' => true, 'message' => "Try again"]], 500);

                  }
                  $info = $user->save();
                   return response()->json(['data' => ['success' => true, 'user' => $user, 'image_link' => 'http://res.cloudinary.com/getfiledata/image/upload/v1552380958/']], 201);

        
    }
}
