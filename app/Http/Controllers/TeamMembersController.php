<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Mail\VerificationCode;

use App\User;
use App\Team;
use App\TeamMembers;
Use App\Activities;

class TeamMembersController extends Controller
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

   public function getTeamMembers($team_id){
          $user = Auth::user();

          $team_members =  array();
          $check_team_id = Team::where('id', $team_id)
                                 ->where('owner_id', $user->id)->exists(); 

         if($check_team_id){       
            $get_datas = TeamMembers::where('team_id', $team_id)
                                      ->where('owner_id', $user->id)
                                           ->get();  

            foreach ($get_datas as $get_data) {

                 $team_member = User::where('id',  $get_data->member_id)->first();

                 $package = array($team_member, $get_data);
                 array_push($team_members, $package);
            }


           return response()->json(['data' =>['success' => true, 'team_memebers' => $team_members]], 200);   

         }else{
            return response()->json(['data' => ['error' => false, 'message' => 'Unauthorize Access']], 401);
         }


    }


   public function getTeamMakers(){
          $user = Auth::user();

          $team_members =  array();  

            $get_datas = TeamMembers::where('member_id', $user->id)
                                         ->get();  

            foreach ($get_datas as $get_data) {

                 $team_member = User::where('id',  $get_data->owner_id)->first();

                 $team_name = Team::where('id',  $get_data->team_id)->first();

                 $package = array($team_member, $team_name, $get_data);
                 array_push($team_members, $package);

                 
            }


           return response()->json(['data' =>['success' => true, 
                            'team_members' => $team_members]], 200);   

    }


    public function searchTeamMember(Request $request, Activities $activities) {
    // Do a validation for the input 
       $user = Auth::user();

        $this->validate($request, [
        	'search_term' => 'required',
        ]);
    // store the request into a variable

        $search_term = $request->input('search_term');

     //Query the database with the email giving
        $get_member = User::where('id', '!=',  $user->id)
                        ->where('name', 'LIKE', "%{$search_term}%") 
                        ->get();
    //Check if rthe user exist
           $user_id = $user->id;
           $info = "You searched for a new team member with name ".$search_term;
           $this->activitiesupdate($activities, $info, $user_id); 

          $user->save();
          return response()->json(['data' =>['success' => true, 'add_members' => $get_member]], 200);               	
    }



    public function addMember(Request $request, Activities $activities, $team_id, TeamMembers $teamMembers) {
         $user = Auth::user();

         $this->validate($request, [
          'member_id' => 'required',
         ]);

         $member_id = $request->input('member_id');

          $check_team_id = Team::where('id', $team_id)
                                 ->where('owner_id', $user->id)->exists(); 

          $team_info = Team::findOrfail($team_id);

         if ($check_team_id ) {

             $check_member_exist = TeamMembers::where('team_id', $team_id)
                                               ->where('member_id', $member_id)
                                                ->exists();  
              if ($check_member_exist) {
                   return response()->json(['data' => ['error' => false, 'message' => 'Member already added to this team!']], 401);
              }else{

                  $teamMembers->team_id = $team_id;
                  $teamMembers->owner_id  = $user->id;
                  $teamMembers->member_id = $member_id;
                  $teamMembers->save();

                  $member_data = User::where('id',  $member_id)->first();
                  $user_id = $user->id;
                  $info = "Added Team Memeber—(".$member_data->name.") to Team—(".$team_info->team_name.")";
                  $this->activitiesupdate($activities, $info, $user_id); 

                  return response()->json(['data' =>['success' => true, 'message' => 'Member added to Team—('.$team_info->team_name.')', 'teamMembers' => $teamMembers]], 200);
              }
         }else{
              return response()->json(['data' => ['error' => false, 'message' => 'Unauthorize Access']], 401);
         }
    }
     public function destroyTeamMember($member_id, Activities $activities) {
              $user = Auth::user();

              $member_exist = TeamMembers::where('id', $teamMember_id)
                                                ->exists(); 

              if ($member_exist) {

                      $data = TeamMembers::findOrfail($teamMember_id);

                      $team_info = Team::findOrfail($data->team_id);

                      $access = "";
                      if ($user->id != $data->member_id) {
                          $access = "0";
                          $member_data = User::where('id',  $data->member_id)->first();

                          $user_id = $user->id;
                          $info = "Deleted a Team Memeber—(".$member_data->name.") from Team—(".$team_info->team_name.")";
                          $this->activitiesupdate($activities, $info, $user_id);

                      }elseif($user->id != $data->owner_id){
                          $access = "1";
                          $member_data = User::where('id',  $data->owner_id)->first();

                          $user_id = $user->id;
                          $info = "Deleted a Team Memeber—(".$member_data->name.") from Team—(".$team_info->team_name.")";
                          $this->activitiesupdate($activities, $info, $user_id); 
                      }

                      if ($access = "0") {
                           $data->delete();

                          return response()->json(['data' => [ 'success' => true, 'message' => 'Team Memeber—('.$member_data->name.') Deleted from Team—('.$team_info->team_name.')']], 200);
                      }
                      if ($access = "1") {
                            $data->delete();

                            return response()->json(['data' => [ 'success' => true, 'message' => 'Team Memeber—('.$member_data->name.') Deleted from Team—('.$team_info->team_name.')']], 200);
                      }
                     

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
