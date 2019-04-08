<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationCode;

use App\User;
Use App\Activities;

class VerifyTokenController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function validateUser(Request $request, Activities $activities) {
    // Do a validation for the input 
        $this->validate($request, [
        	'email' => 'required|email',
        ]);
    // store the request into a variable

        $email = $request->input('email');

     //Query the database with the email giving

       $user = User::where('email', $email)->first();

    //Check if rthe user exist
        if ($user === null) {
        	return response()->json(['data' =>['error' => false, 'message' => 'Not found']], 404);
        }

        try{
             Mail::to($user->email)->send(new VerificationCode($user)); 
          } catch (Exception $ex) {

             return response()->json(['data' =>['success' => true, 'message' => "Try again"]], 500);

          }

           $user_id = $user->id;
           $info = "You checked for an email validity and sent a password recovery token";
           $this->activitiesupdate($activities, $info, $user_id); 

          $user->save();
          return response()->json(['data' =>['success' => true, 'message' => "A verfication code has been sent to ".$user->email."!"]], 200);
                 	

    }
     public function activitiesupdate($activities, $info, $user_id) {

         $time =  time();
         $created_time = date('h:i A — Y-m-d', $time+3600);

         $activities->owner_id = $user_id;
         $activities->narrative = $info." @ ".$created_time.".";
         $activities->save();
    }

}
