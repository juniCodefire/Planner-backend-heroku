<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

use App\User;
use App\WorkSpace;
use App\WorkSpacesToMember;
use App\Company;
use App\WorkSpaceToMember;
use Illuminate\Support\Facades\DB;

/**
 *
 */
class UserSearchController extends Controller
{
  //Send Request Invitation to member
  public function search(Request $request)
  {
    $user = Auth::user();
    $valid = $this->validate($request, 
      [
        't' => 'required|regex:/(^([u,w,c,p]+)?$)/u',
        'q' => 'required|regex:/(^([a-zA-Z0-9]+)?$)/u'
      ],
      [
        't.regex' => ':attribute is invalid accepted only tables starting from(u,w,c)',
        'q.regex' => ':attribute is invalid accepted only format(a-zA-Z0-9)',
      ]
    );
        $table = $request->query('t');
        $query = $request->query('q');

        $modify_name = ucwords($query);
        $modify_username = "@".lcfirst($query);
        $modify_email = lcfirst($query);
        
        if($table == 'u') { 
          $table = 'Users';      
          $search_result = User::where('id', '!=', Auth::user()->id)
                                ->where(function ($query) use ($modify_name, $modify_username, $modify_email) {
                                    $query->where('name', 'LIKE',  "%{$modify_name}%")
                                          ->orWhere('username', 'LIKE', "%{$modify_username}%")
                                          ->orWhere('email', 'LIKE', "%{$modify_email}%");
                                }) 
                                ->get();
            
        }else if ($table == 'w') {
          $table = 'Workspace';
          // $search_result = DB::table('users')
          //     ->whereExists(function ($query) {
          //         $query->select(DB::raw(1))
          //               ->from('workspaces')
          //               ->whereRaw('workspaces.owner_id = users.id');
          //     })
          //   ->get();
            // $search_result = User::whereExists(
            //   function($query) {  
            //     $query->from('workspacestomembers')
            //           ->where('owner_id', '2');
            //   })->get();        

          // $search_result = WorkSpace::where('title', 'LIKE',  "%{$modify_name}%")
          //                             ->whereExists(function($query) {
          //                               $query->select(DB::raw(1))
          //                                       ->from('workspacestomembers')
          //                                       ->where('owner_id', Auth::user()->id);
          //                               })
          //                             ->get();

          $users_workspaces =  WorkSpaceToMember::where('owner_id', Auth::user()->id)
                                                    ->orWhere('member_id', Auth::user()->id)
                                                    ->pluck('workspace_id');
          $search_result = WorkSpace::whereIn('id', $users_workspaces)->get();

        }else if ($table == 'c') {
          $table = 'Company';
          $search_result = Company::where('status', 'Public')->where('title', 'LIKE',  "%{$modify_name}%")->get();
        }else if ($table == 'p') {
          $table = 'Project';
          $search_result = Project::where('status', 'Public')->where('title', 'LIKE',  "%{$modify_name}%")->get();
        }else {
          return response()->json(['error' => true, 'message' => 'An error occured'], 500);
        }
        return response()->json(['success' => true, 'message' => "$table table  search info!", 'search_result' => $search_result ]);
  
  }

}
