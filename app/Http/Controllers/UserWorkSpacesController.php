<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\WorkSpacesRequest;

use App\User;
use App\WorkSpace;
use App\RequestInvite;
use Illuminate\Support\Facades\DB;
/**
 *
 */
class UserWorkSpacesController extends Controller
{
   public function request(Request $request, RequestInvite $request_invite) {
     $requester = Auth::user();
     //Check if the worksapce title is required and _exist
     $this->validateWorkSpace($request);
     $check_workspace = WorkSpace::where('title', $request->input('title'))->where('owner_id', '!=', Auth::user()->id)->exists();

       if ($check_workspace) {
          //Get the worksapce idea
          $workspace = WorkSpace::where('title', $request->input('title'))->first();

          if ($workspace->status === "Public") {
            //Get the owner of Worksapce Data
            $requestee = $workspace->users()->first();

            if (!RequestInvite::where('requestee_id', $requestee->id)->where('requester_id', Auth::user()->id)->where('workspace_id', $workspace->id)->exists()) {
                DB::beginTransaction();
                 try {
                   //Save to a temporary Request table
                   $request_invite->requestee_id = $requestee->id;
                   $request_invite->requester_id = Auth::user()->id;
                   $request_invite->workspace_id = $workspace->id;
                   $request_invite->save();
                   //Send a Request mail
                   Mail::to($requestee->email)->send(new WorkSpacesRequest($requester, $requestee, $workspace));
                   DB::commit();
                   return response()->json(['data' => ['success' => true, 'message' => 'A request has be sent to worksapce owner']], 200);
                 } catch (\Exception $e) {
                   DB::rollBack();
                   return response()->json(['data' =>['error' => false, 'message' => "Sending email failed , try again", 'hint' => $e->getMessage()]], 500);
                 }
            }return response()->json(['data' => ['success' => true, 'message' => 'Sorry your invitation to joining this work space have not yet been answered']], 403);

          }return response()->json(['data' => ['success' => true, 'message' => 'Sorry this is a secured workspace']], 401);

       }return response()->json(['data' => ['success' => true, 'message' => 'Sorry this workspace is not available or not allowed']], 404);

   }

   public function store() {
     return response()->json(['data' => ['success' => true]]);
   }

   public function validateWorkSpace($request) {

     $rules = [
       'title' => 'required'
     ];
     $messages = [
       'required' => ':attribute is required'
     ];
     $this->validate($request, $rules, $messages);
   }

}
