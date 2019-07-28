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
               $token_reset = hash('sha256', Str::random(60));
               $allUser->api_token = $token_reset;
               $allUser->save();
            }

    }
    public function tokenDestroyCron($user) {
    // Do a validation for the input
        //Perform a Cron Jobs on this controller cron-jobs.com
            $allUsers = $user->all();
            foreach ($allUsers as $allUser) {
               $token_reset = hash('sha256', Str::random(60));
               $allUser->api_token = $token_reset;
               $allUser->save();
            }

    }
}
