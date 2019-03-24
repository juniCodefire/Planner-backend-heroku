<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\ConfirmationLink;

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
                     Mail::to($user->email)->send(new ConfirmationLink($user)); 
                  } catch (Exception $ex) {

                     return response()->json(['data' =>['error' => false, 'message' => "Try again"]], 500);

                  }
                 
                  $info = $user->save();

                  $splitemail = $user->email;
                  $emai_link = explode("@",$splitemail);

                  $emai_link = "www.".$emai_link[1];

                   return response()->json(['data' => ['success' => true, 'message' => 'Registration Successful, A confirmation link has been sent to '.$user->email.'', 'user' => $user, 'image_link' => 'http://res.cloudinary.com/getfiledata/image/upload/v1552380958/', 'email_link' => $emai_link]], 201);

        
    }
}
