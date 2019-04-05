<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Mail\VerificationCode;

use App\User;
Use App\Activities;

class TeamMembersController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function searchTeamMember(Request $request, Activities $activities) {
    // Do a validation for the input 
       $user = Auth::user();

        $this->validate($request, [
        	'email' => 'required|email',
        ]);
    // store the request into a variable

        $email = $request->input('email');

     //Query the database with the email giving

       $get_team = User::where('email', $email)->first();
    //Check if rthe user exist
        if ($get_team === null) {
        	return response()->json(['data' =>['error' => false, 'message' => 'No user found with email']], 404);
        }
           $user_id = $user->id;
           $info = "You searched for a new team member with email ".$email;
           $this->activitiesupdate($activities, $info, $user_id); 

          $user->save();
          return response()->json(['data' =>['success' => true, 'get_team' => $get_team]], 200);
                 	

    }

    public function storetTeamMember() {
 
    }
     public function activitiesupdate($activities, $info, $user_id) {

         $time =  time();
         $created_time = date('h:i A — Y-m-d', $time+3600);

         $activities->owner_id = $user_id;
         $activities->narrative = $info." @ ".$created_time.".";
         $activities->save();
    }
    }

}
