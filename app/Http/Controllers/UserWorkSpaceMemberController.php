<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\MemberRequest;

use App\User;
use App\WorkSpace;
use App\Company;
use App\WorkSpaceToMembers;
use App\RequestInvite;
use Illuminate\Support\Facades\DB;

/**
 *
 */
class UserWorkSpaceMemberController extends Controller
{

  //Send Request Invitation to member
  public function addMember(Request $request, RequestInvite $request_invite, $workspace_id, $company_id, $member_id)
  {
        $requester = Auth::user();
        $workspace = WorkSpace::where('id', $workspace_id)->where('owner_id', Auth::user()->id)->first();
        if ($workspace) {
           DB::beginTransaction();
            try {
              //Save to a temporary Request table
              if($member_id == 0) {
                   $this->validateWorkSpace($request);
                   $email = $request->input('email');
                   $requestee = User::where('email', $email)->first();
                   if ($requestee) {
                      $request_invite->requestee_id = $requestee->id;
                   }else {
                      $request_invite->requestee_id = $email;
                   }
                }else {                 
                 $requestee = User::where('id', $member_id)->first();
                 $request_invite->requestee_id = $member_id;
                }
              $request_invite->requester_id = Auth::user()->id;
              $request_invite->workspace_id = $workspace->id;
              if ($company_id != 0) {
                 $request_invite->company_id = $company_id;
                 $company = Company::where('id', $company_id)->first();
              }else {
                $company=null;
              }
              $request_invite->save();
              //Send a Request mail 
              if ($requestee) {
                Mail::to($requestee->email)->send(new MemberRequest($requester, $requestee, $workspace, $company));
              }else {
                // dd(json_decode($requestee));
                Mail::to($email)->send(new MemberRequest($requester, $requestee=null, $workspace, $company));
              }
              DB::commit();

              return response()->json(['data' => ['success' => true, 'message' => 'A request has be sent to workspace owner!']], 200);
            } catch (\Exception $e) {
              DB::rollBack();
              return response()->json(['data' => ['error' => false, 'message' => "Sending invite Request failed , try again!", 'hint' => $e->getMessage()]], 500);
            }
        }else {
            return response()->json(['error' => true, 'message' => 'Unauthorize Workspace Owner or Workspace not found']);
        }
       
  }

    public function validateWorkSpace($request)
  {
    $rules = [

        'email' => array(
          'required'
        ),
      ];
    $messages = [
      'required' => ':attribute is required'
    ];
    $this->validate($request, $rules, $messages);
  }

}
