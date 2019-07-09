<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
Use App\Activities;
use App\User;


class ConfirmationController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function confirmUser($token, Activities $activities) {

    	if(strlen($token) < 30) {
    		return "<p style='text-align:center'>Invalid Confirmation Token</p>";
    	}else{
    		$time =  time();
            $created_time = date('h:i A — Y-m-d', $time+3600);

    		$user = User::where('confirm_token', $token)->exists();

	    	if ($user) {

	            $data_user = User::where('confirm_token', $token)->first();

	             //generate a ramdom api token for user confirmation
				$generateRandomConfirm = Str::random(60);

				$confirm_token = hash('sha256', $generateRandomConfirm);

		    	$data_user->confirm_token = $confirm_token;

		    	$data_user->status = "on";

		    	$data_user->save();

		    	 $activities->owner_id = $data_user->id;
                 $activities->narrative = "Account successfully confirmed @".$created_time.".";
                 $activities->save();

		        return "<div style='text-align:center'>Confirmation Successful</div>";
	    	}else{

	    		return "<div style='text-align:center'>Invalid Confirmation Token Or Already Confirmed, Try To Login</div>";
	    	}
    	}

    	
    }

    public function resetPassword(Request $request, Activities $activities) {

    	 $attributes = $this->validate($request, [
    		'verify_code' => 'required',
    		'password' => 'required|min:6|confirmed',
    	]);

    	 $time =  time();
         $created_time = date('h:i A — Y-m-d', $time+3600);

    	 $check_token = User::where('verify_code', $request->input('verify_code'))->exists();

    	 if ($check_token) {

    	 	$data_user = User::where('verify_code', $request->input('verify_code'))->first();

    	 	//Generatate a token for the password recvery process
	        $generateVerifyToken = Str::random(60);

	        $verify_token = hash('sha256', $generateVerifyToken);

	        $data_user->verify_code = $verify_token;

	        $data_user->password = Hash::make($request->input('password'));

		    $data_user->save();

		    $activities->owner_id = $data_user->id;
             $activities->narrative = "You just changed your password @".$created_time.".";
             $activities->save();


		    return response()->json(['data' =>['success' => true, 'message' => 'New password Update']], 200);


    	 }else{
    	 	return response()->json(['data' =>['error' => true, 'message' => 'Update Already Done or Invalid code']], 401);
    	 }


    }

}
