<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
Use App\Activities;

use App\Goal;
use App\Task;
use App\Policies\ViewPolicy;


class GoalsController extends Controller
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

    public function index() {
      
    	$user = Auth::user();

    //fetching the goal
    $goals = Goal::where('owner_id', $user->id)->get();

       return response()->json(['data' => ['success' => true, 'goals' => $goals]], 200);

    }

    public function show(Goal $goal, ViewPolicy $viewpolicy, $goal_id) {
         Auth::user();
         $check_id = Goal::where('id', $goal_id)->exists();

         if ($check_id) {

             $detail = $viewpolicy->userPassage($goal_id);

             if ($detail) {

                $data = $goal->findOrfail($goal_id);

                return response()->json(['data' => [ 'success' => true, 'goal' => $data]], 200);

             }else {

                return response()->json(['data' => [ 'error' => false, 'message' => 'Unauthorize Access']], 401);
             }

         }else{

             return response()->json(['data' => [ 'error' => false, 'message' => 'Not Found']], 404);
         }
        	
     }
    public function store(Request $request, Goal $goal, Activities $activities) {

      $user = Auth::user();
       
       $attributes = $this->validate($request, [
    		'title'       => 'required',
    		'description' => 'required|min:6',
            'due_date'    => 'required',
    		'level'       => 'required'
    	]);  
      $now_time =  time();
       $check_title = Goal::where('owner_id', $user->id)->where('title', $request->input('title'))->exists();

        if($check_title) {

           return response()->json(['data' => ['error' => false, 'message' => 'Title already exist']], 401);

        }else{

              $goal->owner_id    = $user->id;  
              $goal->title       =  ucwords($request->input('title'));
              $goal->description =  ucfirst($request->input('description'));
              $goal->begin_date  = date('Y-m-d', $now_time+3600);;
              $goal->due_date    = $request->input('due_date');  
              $goal->level       =  ucfirst($request->input('level'));
              $goal->goal_status     = 0;

              $saved = $goal->save();

                if ($saved) {
                    $user_id = $user->id;
                    $info = "You created a New Goal—(".$goal->title.")";
                    $this->activitiesupdate($activities, $info, $user_id);
                   return response()->json(['data' => ['success' => true, 'goal' => $goal]], 201);
                }else{
                    return response()->json(['data' => ['error' => false, 'message' => 'An Error Occured!']], 401); 
                }
        }

    }

    public function update(Request $request, Goal $goal, ViewPolicy $viewpolicy, $goal_id, Activities $activities) {
        $user = Auth::user();
         $check_id = Goal::where('id', $goal_id)->exists();

         if ($check_id) {
             $fact = $viewpolicy->userPassage($goal_id);

             if ($fact) {
                 $this->validate($request, [
                  'title'       => 'required',
                  'description' => 'required|min:5',
                  'level'       => 'required',
                  'due_date'    => 'required'
                ]); 

                 $check_title = Goal::where('owner_id', $user->id)->where('title', $request->input('title'))->where('id', '!=', $goal_id)->first();

                  if($check_title) {
                        return response()->json(['data' => ['error' => false, 'message' => 'Title already exist']], 401);
                    }else{

                        $data = $goal->findOrfail($goal_id);

                        $data->title       = $request->input('title');
                        $data->description = $request->input('description');
                        $data->level       = $request->input('level');
                        $data->due_date  = $request->input('due_date');

                        $data->owner_id   = $user->id;

                        $saved = $data->save();

                        $user_id = $user->id;
                        $info = "you updated a Goal—(".$data->title.")";
                        $this->activitiesupdate($activities, $info, $user_id);


                        return response()->json(['data' => [ 'success' => true, 'goal' => $data]], 200);
                  }
             }else {
                return response()->json(['data' => [ 'error' => false, 'message' => 'Unauthorize Access']], 401);
             }
         }else{
             return response()->json(['data' => [ 'error' => false, 'message' => 'Not Found']], 404);
         }
    } 

    public function updateGoalStatus(Request $request, Task $task, Goal $goal, $goal_id, ViewPolicy $viewpolicy, Activities $activities) {

         $user = Auth::user();

         $check_id = Goal::where('id', $goal_id)->exists();
         $now_time =  time();
         if ($check_id) {
             $fact = $viewpolicy->userPassage($goal_id);
             if ($fact) {
                  $this->validate($request, [
                     'goal_status'       => 'required'
                  ]); 

                  $data = $goal->findOrfail($goal_id);

                  $data->goal_status = $request->input('goal_status');

                  $data->save();

                  if ($request->input('goal_status') == 1) {
                     $all_task_datas = Task::where('goal_id', $goal_id)->get();

                      foreach ($all_task_datas as $all_task_data) {
                        
                           $all_task_data->task_status = $request->input('goal_status');

                           $all_task_data->save();

                      }

                        $user_id = $user->id;
                        $info = "Goal—(".$data->title.") has been marked Completed!";
                        $this->activitiesupdate($activities, $info, $user_id);
                        return response()->json(['data' => [ 'success' => true, 'goalCompleted' => 'Goal Completed']], 200);

                  }elseif($request->input('goal_status') == 0){

                     $time_pass =  time();
                     $time_check = date('Y-m-d', $now_time+3600);

                     $all_task_datas = Task::where('goal_id', $goal_id)
                                        ->where('due_date', '<', $time_check)->get();

                      foreach ($all_task_datas as $all_task_data) {
                        
                           $all_task_data->task_status = $request->input('goal_status');

                           $all_task_data->save();

                      }

                        $user_id = $user->id;
                        $info = "Goal—(".$data->title.") has been marked UnCompleted with all tasks below current date!";
                        $this->activitiesupdate($activities, $info, $user_id);
                        return response()->json(['data' => [ 'success' => true, 'goalUnCompleted' => 'Goal UnCompleted']], 200);
                  }
                 
             }else {
                return response()->json(['data' => [ 'error' => false, 'message' => 'Unauthorize Access']], 401);
             }
         }else{
             return response()->json(['data' => [ 'error' => false, 'message' => 'Not Found']], 404);
         }

    }
    public function destroy(Goal $goal, ViewPolicy $viewpolicy, $goal_id, Activities $activities) {
         $user = Auth::user();
         $check_id = Goal::where('id', $goal_id)->exists();

         if ($check_id) {
             $fact = $viewpolicy->userPassage($goal_id);

             if ($fact) {
                $data = $goal->findOrfail($goal_id);

                $user_id = $user->id;
                $info = "A Goal—(".$data->title.") is deleted";
                $this->activitiesupdate($activities, $info, $user_id);
                $data->delete();

                return response()->json(['data' => [ 'success' => true, 'message' => 'deleted' ]], 200);
             }else {
                return response()->json(['data' => [ 'error' => false, 'message' => 'Unauthorize Access' ]], 401);
             }
         }else{
             return response()->json(['data' => [ 'error' => false, 'message' => 'Not Found']], 404);
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
