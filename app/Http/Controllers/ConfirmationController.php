<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use App\User;


class ConfirmationController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function confirmUser($token) {

    	if(strlen($token) < 30) {
    		return "<p style='text-align:center'>Invalid Confirmation Token</p>";
    	}else{

    		$user = User::where('confirm_token', $token)->exists();

	    	if ($user) {

	            $data_user = User::where('confirm_token', $token)->first();

		    	$data_user->confirm_token = "0";

		    	$data_user->status = "on";

		    	$data_user->save();

		        return "<div style='text-align:center'>Confirmation Successful</div>";
	    	}else{

	    		return "<div style='text-align:center'>Invalid Confirmation Token Or Already Confirmed, Try To Login</div>";
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

    	 	$data_user = User::where('verify_code', $request->input('verify_code'))->first();

    	 	//Generatate a token for the password recvery process
	        $generateVerifyToken = Str::random(60);

	        $verify_token = hash('sha256', $generateVerifyToken);

	        $data_user->verify_code = $verify_token;

	        $data_user->password = Hash::make($request->input('password'));

		    $data_user->save();

		    return response()->json(['data' =>['success' => true, 'message' => 'New password Update']], 200);


    	 }else{
    	 	return response()->json(['data' =>['error' => true, 'message' => 'Update Already Done or Invalid code']], 401);
    	 }


    }

}
