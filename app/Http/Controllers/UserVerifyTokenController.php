<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationCode;
use App\User;

use Illuminate\Support\Facades\DB;

class UserVerifyTokenController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function validateUser(Request $request) {
    // Do a validation for the input
        $this->validate($request, [
        	'email' => 'required|email',
        ]);
        $email = $request->input('email');
     //Query the database with the email giving
       $user = User::where('email', $email)->first();
    //Check if rthe user exist
        if ($user === null) {
        	return response()->json(['data' =>['error' => false, 'message' => 'Email not found in our record!']], 404);
        }
        DB::beginTransaction();
        try{
             Mail::to($user->email)->send(new VerificationCode($user));
             DB::commit();
             return response()->json(['data' =>['success' => true, 'message' => "A verfication code has been sent to ".$user->email."!"]], 200);
          } catch (\Exception $e) {
             DB::rollBack();
             return response()->json(['data' =>['error' => false, 'message' => "Sending email failed , try again".$e]], 401);
          }
    }
}
