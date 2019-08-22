<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Admin;

class AdminSignInController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function check(Request $request) {
    // Do a validation for the input
        $this->validate($request, [
        	'email_or_username'    => 'required',
        	'password' => 'required'
        ]);
        //Generatate a token for the password recvery process
        $generateVerifyToken  = Str::random(60);
        $verify_token         =  hash('adler32', $generateVerifyToken);
        $email_or_username    = $request->input('email_or_username');
        $password             = $request->input('password');

        $update_token = hash('sha256', Str::random(60));
        //Generatate a token for the password recvery process

        if (!stripos($email_or_username, '@')) {
            $email_or_username =  '@'.$email_or_username;
        }

       //Query the database with the email giving
         $admin = Admin::where('email', '=', $email_or_username)->orWhere('username', '=', $email_or_username)->first();
       //Check if rthe user exist
        if ($admin === null) {
        	return response()->json(['data' =>['error' => false, 'message' => 'Not found']], 404);
        }
     //Get the user token from the database
      $token = $admin->api_token;
     //Check if password match
        if (Hash::check($password, $admin->password)) {
                    $admin->verify_code = $verify_token;
                    $admin->api_token = $update_token;
                    $admin->save();
                    return response()->json(['data' =>['success' => true, 'user' => $admin,
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
                                        'token' => 'Bearer '.$token]], 200);
        }else{
     
        }

    }

}
