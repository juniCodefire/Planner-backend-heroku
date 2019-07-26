<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\User;


class UserConfirmationController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function confirm($confirmtoken) {

    	if(strlen($confirmtoken) < 30) {
			 return response()->json(['data' =>['error' => false, 'message' => 'Invalid Confirmation Token']], 401);
    	}else{
    		$check_user = User::where('confirm_token', $confirmtoken)->exists();

	    	if ($check_user) {
	            $user = User::where('confirm_token', $confirmtoken)->first();
              //Get the user token from the database
              $token =  $user->api_token;
	            //generate a ramdom api token for user confirmation
				      $confirm_token = hash('sha256', Str::random(60));
		    	    $user->confirm_token = $confirm_token;
              //Update the user activated
		    	    $user->status = "on";
		          $user->save();
				      return response()->json(['data' =>['success' => true, 'message' => 'Confirmation Successful',
                                        'user' =>  $user,
                                        'image_link'     => 'http://res.cloudinary.com/getfiledata/image/upload/',
                                         'imageProp'     => [
                                                          'cropType1' => 'c_fit',
                                                          'cropType2' => 'g_face',
                                                          'imageStyle' => 'c_thumb',
                                                          'heigth' => 'h_577',
                                                          'width' =>  '433',
                                                          'widthThumb' => 'w_200',
                                                          'aspectRatio' => 'ar_4:4'
                                                        ],
                                         'token' => $token]], 200);
	    	}else{
				     return response()->json(['data' =>['error' => false, 'message' => 'Invalid Confirmation Token Or Already Confirmed, Try Loging In']], 403);
	    	}
    	}
    }

    public function resetPassword(Request $request) {

    	 $attributes = $this->validate($request, [
    		'verify_code' => 'required',
    		'password' => 'required|min:6|confirmed',
    	]);

    	 $check_token = User::where('verify_code', $request->input('verify_code'))->exists();

    	 if ($check_token) {
    	   	$user = User::where('verify_code', $request->input('verify_code'))->first();
    	   //Generatate a token for the password recvery process

         //the token of this users
          $token = $user->api_token;
	        $verify_token = hash('adler32', Str::random(60));
	        $user->verify_code = $verify_token;
	        $user->password = Hash::make($request->input('password'));
		      $user->save();
		      return response()->json(['data' =>['success' => true, 'message' => 'New Password Created', 'user' => $user, 'token' => 'Bearer'. $token]], 200);
    	 }else{
    	   	return response()->json(['data' =>['error' => true, 'message' => 'Update Already Done Or Invalid Code']], 401);
    	 }


    }

}
