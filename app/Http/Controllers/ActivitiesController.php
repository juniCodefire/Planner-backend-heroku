<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Activities;

class ActivitiesController extends Controller
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

    public function index() {

        $user = Auth::user();

        $activities = Activities::where('owner_id', $user->id)->get();

        return response()->json(['data' => [ 'success' => true, 'activities' => $activities]], 200);

    }

}
