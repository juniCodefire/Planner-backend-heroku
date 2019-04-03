<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use App\User;
Use App\Activities;

class TokenDestroyController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function tokenDestroy(User $user, Activities $activities) {
    // Do a validation for the input 
        //Perform a Cron Jobs on this controller cron-jobs.com

            $allUsers = $user->all();

            foreach ($allUsers as $allUser) {

              $regenerateNewToken = Str::random(60);

               $tokenNew = hash('sha256', $regenerateNewToken);

               $allUser->api_token = $tokenNew;

               $allUser->save();
            
            }
              $user_id = 0;
              $info = "Session destroyed, you can login again to continue";
              $this->activitiesupdate($activities, $info, $user_id);   
                 
            return response()->json(['data' => ['success' => true, 'message' => 'Token Updated' ]], 200);
      
    }
    public function activitiesupdate($activities, $info, $user_id) {

         $time =  time();
         $created_time = date('h:i A — Y-m-d', $time+3600);

         $activities->owner_id = $user_id;
         $activities->narrative = $info." @ ".$created_time.".";
         $activities->save();
    }
}
