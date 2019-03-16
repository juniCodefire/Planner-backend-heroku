<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

use App\User;

class TokenDestroyController extends Controller
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

    public function tokenDestroy() {
    // Do a validation for the input 
        //Perform a Cron Jobs on this controller cron-jobs.com
        $user = Auth::user();

            $regenerateNewToken = Str::random(60);

            $tokenNew = hash('sha256', $regenerateNewToken);
            
            $user->api_token = $tokenNew;

            $user->save();
    }
}
