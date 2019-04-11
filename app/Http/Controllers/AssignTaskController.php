<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Task;
use App\User;
use App\Goal;
Use App\Activities;


class AssignTaskController extends Controller
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

   public function showAssignedTo() {

        $user = Auth::user();

         $assign_to =  array(); 

          $get_datas = Task::where('owner_id', $user->id)->get();

            foreach ($get_datas as $get_data) {

                 $task_member = User::where('id',  $get_data->assigned_id)->first();

                 $goal_data = Goal::where('id',  $get_data->goal_id)->first();

                 $packages = array($task_member, $goal_data, $get_data );

                 array_push($assign_to, $packages);
                
            }
           return response()->json(['data' =>['success' => true, 
                            'assign_to' => $assign_to]], 200);   


    }
  public function showAssignedFrom() {

         $user = Auth::user();

         $assign_from =  array(); 

          $get_datas = Task::where('assigned_id', $user->id)->get();

            foreach ($get_datas as $get_data) {

                 $task_member = User::where('id',  $get_data->owner_id)->first();

                 $goal_data = Goal::where('id',  $get_data->goal_id)->first();

                 $packages = array($task_member, $goal_data, $get_data );

                 array_push($assign_from, $packages);
                
            }
           return response()->json(['data' =>['success' => true, 
                            'assign_from' => $assign_from]], 200);   

    }

  public function assignTask(Request $request, Activities $activities,  Activities $activities_2) {
      $user = Auth::user();
           $this->validate($request, [
                'member_id'  => 'required',
                'task_id'  =>   'required'
            ]); 

        $task_assign = Task::where('id', $request->input('task_id'))->first();

              $task_assign->assigned_id = $request->input('member_id');
 
              $saved = $task_assign->save();

                    
            if ($saved) {
                 $member_data = User::where('id', $request->input('member_id'))
                                   ->first();
                                   
                    $user_id = $user->id;
                    $info = "You assigned a task to—(".$member_data->name.")";
                    $this->activitiesupdate($activities, $info, $user_id);

                    $user_id_2 = $request->input('member_id');
                    $info_2 = "A Task has been assigned to you from (".$user->name.")";
                    $this->activitiesupdate_2($activities_2, $info_2, $user_id_2);         

                 return response()->json(['data' => ['success' => true, 'message' => 'You assigned a task to ('.$member_data->name.')']], 200);
              }
    }

    public function removeTask(Request $request, Activities $activities,  Activities $activities_2) {
      $user = Auth::user();
           $this->validate($request, [
                'task_id'  =>   'required'
            ]); 

        $task_remove = Task::where('id', $request->input('task_id'))->first();
 
            
                  $member_data = User::where('id',  $task_remove->assigned_id)->first();
                                   
                    $user_id = $user->id;
                    $info = "You reverted a Task—(".$task_remove->titles.") from a member—(".$member_data->name.")";
                    $this->activitiesupdate($activities, $info, $user_id);

                    $user_id_2 = $task_remove->assigned_id;
                    $info_2 = $user->name." remove a Task—(".$task_remove->titles.") assigned to you";
                    $this->activitiesupdate_2($activities_2, $info_2, $user_id_2); 

                    $task_remove->assigned_id = null;

                     $saved = $task_remove->save();


                 return response()->json(['data' => ['success' => true, 'message' => 'You reverted a task from ('.$member_data->name.')' ]], 200);
    }

   public function activitiesupdate($activities, $info, $user_id) {

         $time =  time();
         $created_time = date('h:i A — Y-m-d', $time+3600);

         $activities->owner_id = $user_id;
         $activities->narrative = $info." @ ".$created_time.".";
         $activities->save();
    }
   public function activitiesupdate_2($activities_2, $info_2, $user_id_2) {

         $time =  time();
         $created_time = date('h:i A — Y-m-d', $time+3600);

         $activities_2->owner_id = $user_id_2;
         $activities_2->narrative = $info_2." @ ".$created_time.".";
         $activities_2->save();
    }
}
