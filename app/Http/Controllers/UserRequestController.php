<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

use App\User;
use App\RequestInvite;
use Illuminate\Support\Facades\DB;
/**
 *
 */

class UserRequestController extends Controller
{
	public function index() {
    $user = Auth::user();
    $request = RequestInvite::where('requester_id', $user->id)
                                ->orWhere('requestee_id',  $user->id)
                                ->get();

    return response()->json(['success' => true, 'request' => $request ], 200); 
  }

}