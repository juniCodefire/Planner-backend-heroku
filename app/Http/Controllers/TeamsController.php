<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Mail\VerificationCode;

use App\User;
use App\Team;
Use App\Activities;
use App\Policies\ViewPolicy;

class TeamsController extends Controller
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


    public function index(Team $team) {
    // Do a validation for the input 
       $user = Auth::user();

       $show_all_team = $team->where('owner_id', $user->id)->get();
  
          return response()->json(['data' =>['success' => true, 'all_team' => $show_all_team]], 200);
                 	

    }

     public function showOne($team_id, Team $team) {
    // Do a validation for the input 
       $user = Auth::user();

       $show_one_team = $team->where('id', $team_id)->first();
  
          return response()->json(['data' =>['success' => true, 'one_team' => $show_one_team]], 200);
                  

    }


    public function storeTeam(Team $team, Request $request, Activities $activities) {
       $user = Auth::user();

        $this->validate($request, [
          'team_name' => 'required',
        ]);


         $check_team_name = $team->where('owner_id', $user->id)->where('team_name', $request->input('team_name'))->exists();

        if($check_team_name) {

           return response()->json(['data' => ['error' => false, 'message' => 'Team name already exist']], 401);

        }else{

         $team_name = $request->input('team_name');

         $team->owner_id = $user->id;
         $team->team_name = ucwords($team_name);

         $team->save();

         $user_id = $user->id;
         $info = "You created a new team with name ".$team_name;
         $this->activitiesupdate($activities, $info, $user_id); 

         return response()->json(['data' =>['success' => true, 'team' => $team]], 201);

       }
    }

    public function updateTeam($team_id, Team $team, Request $request, ViewPolicy $viewpolicy, Activities $activities) {
        $user = Auth::user();

        $this->validate($request, [
          'team_name' => 'required',
        ]);

          $check_id = $team->where('id', $team_id)->exists();

         if ($check_id) {

             $detail = $viewpolicy->TeamAccess($team_id);

             if ($detail) {
                  //Get the old team name to emit for activities feeds
                  $get_old_team_name = $team->where('owner_id', $user->id)->where('id', $team_id)->first();

                  $old_team_name = $get_old_team_name->team_name;

                  $check_team_name = $team->where('owner_id', $user->id)->where('team_name', $request->input('team_name'))->where('id', '!=', $team_id)->exists();

                  if($check_team_name) {

                     return response()->json(['data' => ['error' => false, 'message' => 'Team name already exist']], 401);

                  }else{

                   $data = Team::findOrfail($team_id);

                   $data->owner_id = $user->id;
                   $data->team_name = ucwords($request->input('team_name'));

                   $data->save();

                   $user_id = $user->id;
                   $info = "Team—(".$old_team_name.") updated to (".$data->team_name.")!";
                   $this->activitiesupdate($activities, $info, $user_id); 

                   return response()->json(['data' =>['success' => true, 'team' => $data]], 200);

                 }

             }else {

                return response()->json(['data' => [ 'error' => false, 'message' => 'Unauthorize Access']], 401);
             }

         }else{

             return response()->json(['data' => [ 'error' => false, 'message' => 'Not Found']], 404);
         }

    }


  public function destroy(ViewPolicy $viewpolicy, $team_id, Activities $activities) {
      $user = Auth::user();
      $detail = $viewpolicy->TeamAccess($team_id);   

      $team_exist = Team::where('id', $team_id)->exists();

      if ($team_exist && $detail) {

              $data = Team::findOrfail($team_id);

              $user_id = $user->id;
              $info = "Deleted a Team—(".$data->team_name.")";
              $this->activitiesupdate($activities, $info, $user_id); 

              $data->delete();

              return response()->json(['data' => [ 'success' => true, 'message' => 'Team Deleted' ]], 200);

      }else{

         return response()->json(['data' => ['error' => false, 'message' => 'Unauthorize Access!']], 401); 
      }
  }

     public function activitiesupdate($activities, $info, $user_id) {

         $time =  time();
         $created_time = date('h:i A — Y-m-d', $time+3600);

         $activities->owner_id = $user_id;
         $activities->narrative = $info." @ ".$created_time.".";
         $activities->save();
    }

}
