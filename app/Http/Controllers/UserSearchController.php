<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

use App\User;
use App\WorkSpace;
use App\Company;
use App\WorkSpaceToMembers;
use Illuminate\Support\Facades\DB;

/**
 *
 */
class UserSearchController extends Controller
{

  //Send Request Invitation to member
  public function search(Request $request, $param)
  {
       
  }

}
