<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use App\User;

class TokenDestroyController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function tokenDestroy(User $user) {
    // Do a validation for the input 
        //Perform a Cron Jobs on this controller cron-jobs.com

            $allUsers = $user->all();

            foreach ($allUsers as $allUser) {

              $regenerateNewToken = Str::random(60);

               $tokenNew = hash('sha256', $regenerateNewToken);

               $allUser->api_token = $tokenNew;

               $allUser->save();
            
            }
                 
            return response()->json(['data' => ['success' => true, 'message' => 'Token Updated' ]], 200);

            

    }
}
