<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\WorkSpacesRequest;

use App\User;
use App\WorkSpace;
use App\Company;
use App\RequestInvite;
use Illuminate\Support\Facades\DB;

/**
 *
 */
class UserWorkSpacesController extends Controller
{

  public function show()
  {
    $user = Auth::user();
    $workspaces = WorkSpace::where('owner_id', $user->id)
      ->with('companies')
      ->with('users')
      ->get();
    return response()->json(['data' => ['success' => true, 'message' => 'Workspaces Successfully', 'workspaces' => $workspaces]], 200);
  }


  public function request(Request $request, RequestInvite $request_invite)
  {
    $requester = Auth::user();

    //Check if the worksapce title is required and _exist
    $this->validateWorkSpace($request, $i = 0);
    $check_workspace = WorkSpace::where('title', ucwords($request->input('title')))
      ->where('owner_id', '!=', Auth::user()->id)
      ->exists();
    $check_unique_name = WorkSpace::where('unique_name', $request->input('title'))
      ->where('owner_id', '!=', Auth::user()->id)
      ->exists();
    if ($check_workspace || $check_unique_name) {
      $check_unique_name = $request->input('title');
      //Check if the user use the name or the username to send a requested
      if (stripos($check_unique_name, " ")) {
        //Return all work sapce with their name and their unique username for the user to choose and send a request
        $choose_workspace = WorkSpace::where('title', $request->input('title'))->where('owner_id', '!=', Auth::user()->id)->where('status', 'Public')->get();
        return response()->json(['data' => [
          'success' => true, 'key' => '1', 'message' => 'Choose an ideal workspace from the list',
          'message-2' => 'If the workspace is not found in the list, it means the workspace is private',
          'message-3' => 'You can send a message to the workspace owner to invite you', 'choose_workspace' => $choose_workspace
        ]]);
      }
      //Here will continue if the username is know!
      //Get the worksapce unique_name
      $workspace = WorkSpace::where('title', $request->input('title'))->orWhere('unique_name', $request->input('title'))->first();

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
            return response()->json(['data' => ['success' => true, 'key' => '2',  'message' => 'A request has be sent to worksapce owner!']], 200);
          } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['data' => ['error' => false, 'message' => "Sending email failed , try again!", 'hint' => $e->getMessage()]], 501);
          }
        }
        return response()->json(['data' => ['error' => false, 'message' => 'Sorry your invitation to joining this workspace have not been confirmed!']], 501);
      }
      return response()->json(['data' => ['error' => false, 'message' => 'Sorry this is a secured workspace!']], 401);
    }
    return response()->json(['data' => ['error' => false, 'message' => 'Sorry this workspace is not available or not allowed, try using a workspace unique id instead!']], 403);
  }




  public function store(Request $request, WorkSpace $workspace, $company_id = '0')
  {
    $user = Auth::user();
    //Validate the input
    $this->validateWorkSpace($request, $i = 1);
    //Recurssive Function to Regenerate Wiorkspace Unique Name
    $unique_name = $this->generateUniqueName($request);
    //Insert the worksapce into the Database(Save)
    DB::beginTransaction();
    try {
      $workspace->title = ucwords($request->input('title'));
      $workspace->owner_id = Auth::user()->id;
      if ($company_id != '0') {
         $check_company = Company::where('id', $company_id)->where('owner_id', Auth::user()->id)->exists();
         if ($check_company) {
            $workspace->company_id = (int)$company_id;
         }else {
            return response()->json(['message' => 'Company does not exist'], 404);
         }
      }
      $workspace->unique_name = $unique_name;
      $workspace->role = ucwords($request->input('role'));
      $workspace->wallpaper = $request->input('wallpaper');
      $workspace->description = ucwords($request->input('description'));
      $workspace->status = ucwords($request->input('status'));
      $workspace->save();

      DB::commit();
      return response()->json(['data' => ['success' => true, 'message' => 'Successfully Created!', 'new_workspace' => $workspace]], 200);
    } catch (\Exception $e) {

      DB::rollBack();
      return response()->json(['data' => ['error' => false, 'message' => 'An error occured retry again!', 'hint' => $e->getMessage()]], 501);
    }
  }

  public function generateUniqueName($request)
  {
    //Generate a ramdom unique_name for the Worksapce
    $rand = mt_rand(00000, 90000);
    $value = explode(" ", $request->input('title'));
    $unique_name = '#' . strtolower(implode($value)) . $rand;
    //Check if the unique_name already exist in the workspace table "if yes regenerate a new one"
    $check_unique_name = WorkSpace::where('unique_name', $unique_name)->exists();
    if ($check_unique_name) {
      $this->generateUniqueName($request);
    } else {
      return $unique_name;
    }
  }

  public function validateWorkSpace($request, $i = 0)
  {
    if ($i == 0) {
      $rules = [

        'title' => array(
          'required',
          'regex:/(^([ #a-zA-Z]+)(\d+)?$)/u'
        ),
      ];
    } else {
      $rules = [

        'title' => array(
          'required',
          'regex:/(^([ a-zA-Z]+)(\d+)?$)/u'
        ),
        'role' => 'string',
        'description' => 'max:150',
        'wallpaper' => 'required',
        'status' => 'required|string'
      ];
    }
    $messages = [
      'required' => ':attribute is required',
      'regex' => ':attribute is invalid accepted only format(a-z,A-Z,0-9)'
    ];
    $this->validate($request, $rules, $messages);
  }
}
