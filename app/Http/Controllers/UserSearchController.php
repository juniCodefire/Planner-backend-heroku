<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

use App\User;
use App\WorkSpace;
use App\WorkSpacesToMember;
use App\CompanyToMember;
use App\Company;
use App\Project;
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
        $modify_unique_name = "#".lcfirst($query);
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
          $users_workspaces =  WorkSpaceToMember::where('owner_id', Auth::user()->id)        
                                                    ->orWhere('member_id', Auth::user()->id)
                                                    ->pluck('workspace_id');
          if(!$users_workspaces) {
            return response()->json(['error' => true, 'message' => 'No workspaces found'], 500);
          }else {
            $search_result = WorkSpace::whereIn('id', $users_workspaces)    
                                        ->where(function ($query) use ($modify_name, $modify_unique_name) {
                                          $query->where('title', 'LIKE', "%{$modify_name}%")
                                                 ->orWhere('unique_name', 'LIKE', "%{$modify_unique_name}%");
                                        })
                                        ->get();
      }

        }else if ($table == 'c') {
          $table = 'Company';                            
            $users_company =  CompanyToMember::where('owner_id', Auth::user()->id)
                                                    ->orWhere('member_id', Auth::user()->id)
                                                    ->pluck('company_id');
            if(!$users_company) {
              return response()->json(['error' => true, 'message' => 'No company found'], 500);
            }else {

              $search_result = Company::whereIn('id', $users_company)    
                                        ->where(function ($query) use ($modify_name, $modify_unique_name) {
                                          $query->where('title', 'LIKE', "%{$modify_name}%")
                                                 ->orWhere('unique_name', 'LIKE', "%{$modify_unique_name}%");
                                        })
                                        ->get();
            }
        }else if ($table == 'p') {
          $table = 'Project';
          $users_company =  CompanyToMember::where('owner_id', Auth::user()->id)
                                    ->orWhere('member_id', Auth::user()->id)
                                    ->pluck('company_id');
          if(!$users_company) {
             return response()->json(['error' => true, 'message' => 'No company found'], 500);
          }else {

          $search_result = Company::whereIn('id', $users_company)    
          ->where(function ($query) use ($modify_name, $modify_unique_name) {
          $query->where('title', 'LIKE', "%{$modify_name}%")
          ->orWhere('unique_name', 'LIKE', "%{$modify_unique_name}%");
          })
          ->get();
          }
          $search_result = Project::where('title', 'LIKE',  "%{$modify_name}%")->get();
        }else {
          return response()->json(['error' => true, 'message' => 'An error occured'], 500);
        }
        return response()->json(['success' => true, 'message' => "$table table  search info!", 'search_result' => $search_result ]);
  
  }

}
