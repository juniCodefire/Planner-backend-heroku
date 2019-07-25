<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeNewUser;
use App\User;

class UserSignInController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function check(Request $request, Activities $activities) {
    // Do a validation for the input
        $this->validate($request, [
        	'email_or_username'    => 'required',
        	'password' => 'required'
        ]);
        //Generatate a token for the password recvery process
        $generateVerifyToken  = Str::random(60);
        $verify_token         =  hash('adler32', $generateVerifyToken);
        $email_Or_username    = $request->input('email_or_username');
        $password             = $request->input('password');

       //Query the database with the email giving
         $user = User::where('email', $email_or_username)orWhere('username', $email_or_username)->first();
       //Check if rthe user exist
        if ($user === null) {
        	return response()->json(['data' =>['error' => false, 'message' => 'Not found']], 404);
        }
     //Get the user token from the database
        $token = $user->api_token;
     //Check if password match
        if (Hash::check($password, $user->password)) {

                if ($user->status == "on") {

                    $user->verify_code = $verify_token;
                    $user->save();
                    return response()->json(['data' =>['success' => true, 'user' => $user,
                                                      'image_link'    => 'http://res.cloudinary.com/getfiledata/image/upload/',
                                                      'imageProp'     => [
                                                                          'cropType1' => 'c_fit',
                                                                          'cropType2' => 'g_face',
                                                                          'imageStyle' => 'c_thumb',
                                                                          'heigth' => 'h_577',
                                                                          'width' =>  '433',
                                                                          'widthThumb' => 'w_200',
                                                                          'aspectRatio' => 'ar_4:4'
                                                                        ],
                                                      'token' => 'Bearer'. $token]], 200);
                }else{
                     return response()->json(['data' =>['error' => false, 'message' => "Not Confirmed Yet"]], 402);
                }
        }else{
        	return response()->json(['data' =>['error' => false, 'message' => "Invalid Credential"]], 401);
        }

    }

}
