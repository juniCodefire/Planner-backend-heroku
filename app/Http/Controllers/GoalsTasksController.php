<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use App\Goal;
use App\Task;
use App\User;
use App\Policies\ViewPolicy;
use App\Policies\TaskPolicy;
Use App\Activities;


class GoalsTasksController extends Controller
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

    public function index(ViewPolicy $viewpolicy, $goal_id) {
         $user = Auth::user();
         $check_id = Goal::where('id', $goal_id)->exists();

         if ($check_id) {

             $detail = $viewpolicy->userPassage($goal_id);

             if ($detail) {  

                     $check_tasks = Task::where('goal_id', $goal_id)->exists(); 

                if ($check_tasks) {
                     $all_task =  array(); 
                     $user_tasks = Task::where('goal_id', $goal_id)->get();   
                     
                     foreach ($user_tasks as $user_task) {

                              if ($user_task->assigned_id != null) {
                                $task_member = User::where('id', $user_task->assigned_id)->first();
                                
                               }else{
                                   $task_member = "";
                               }

                              $packages = array($task_member, $user_task);

                              array_push($all_task, $packages);
                     }
                     return response()->json(['data' => [ 'success' => true, 'user_tasks' => $all_task]], 200);

                }else{
                     return response()->json(['data' => [ 'success' => true, 'message' => "No Recent Task"]], 200);
                }

             }else{

                return response()->json(['data' => [ 'error' => false, 'message' => "Unauthorize Access"]], 401);
             }
         }else{

              return response()->json(['data' => [ 'error' => false, 'message' => "Not Found"]], 404);
         }
    }


    public function show(ViewPolicy $viewpolicy, $goal_id, $task_id) {

      $user = Auth::user();
      
      $check_id = Goal::where('id', $goal_id)->exists();

      $task_check_id = Task::where('id', $task_id)->exists();

     if ($check_id && $task_check_id) {

            $detail = $viewpolicy->userPassage($goal_id); 

            if ($detail) {      

                $user_tasks = Task::where('id', $task_id)->where('goal_id', $goal_id)->first();  

                if ($user_tasks !== null) {

                    return response()->json(['data' => [ 'success' => true, 'user_tasks' => $user_tasks]], 200);

                }else{

                    return response()->json(['data' => [ 'error' => false, 'message' => "Not Found"]], 404);
                }
            }else{

              return response()->json(['data' => [ 'error' => false, 'message' => "Unauthorize Access"]], 401);
            }        
         }else{

              return response()->json(['data' => [ 'error' => false, 'message' => "Not Found"]], 404);
         }

    }

     public function store(Request $request, ViewPolicy $viewpolicy, TaskPolicy $taskpolicy, Task $task, $goal_id, Activities $activities) {

      $user = Auth::user();
      $detail = $viewpolicy->userPassage($goal_id); 
      $goal = Goal::where('id', $goal_id)->exists();
      $now_time =  time();
      if ($goal && $detail) {

           $this->validate($request, [
                'task_title'  => 'required',
                'description' => 'required|min:5',
                'begin_time'  => 'required',
                'due_time'    => 'required',
                'due_date'    => 'required'
            ]); 

        $detail = $viewpolicy->userPassage($goal_id); 

        $begin_time =  date('h:i A', strtotime($request->input('begin_time')));

        $due_time   = date('h:i A', strtotime($request->input('due_time')));

        $begin_date = date('Y-m-d', $now_time+3600);

        $due_date   = $request->input('due_date');

        $reminder   = $request->input('reminder');

        $task_title = $request->input('task_title');

        $goalData = Goal::where('id', $goal_id)->first();

        $taskvalidate = $taskpolicy->taskValidate($due_date, $goalData); 

        if (empty($reminder)) {
          $reminder ="No Reminder";
        }   
        if ($taskvalidate) {

           return response()->json(['data' => ['error' => false, 'message' => 'your due date cannot exceed goal due date']], 401);     
          
        }      

        if ($task_title === $goalData->title) {

           return response()->json(['data' => ['error' => false, 'message' => 'Same Goal Title']], 401);
        }

        $check_title = Task::where('goal_id', $goal_id)->where('task_title', $request->input('task_title'))->exists();

        if($check_title) {

           return response()->json(['data' => ['error' => false, 'message' => 'Title already exist']], 401);

        }else{
              $task->goal_id     = $goal_id;
              $task->owner_id    = $user->id;
              $task->task_title  =  ucwords($task_title);
              $task->description =  ucfirst($request->input('description'));
              $task->begin_time  = $begin_time;
              $task->begin_date  = $begin_date;
              $task->due_time    = $due_time;
              $task->due_date    = $due_date;  
              $task->reminder    = ucwords($reminder); 
              $task->task_status = 0;
              
              $saved = $task->save();

                if ($saved) {

                   $user_id = $user->id;
                    $info = "You created a new Task—(".$task->task_title.") from Goal—(".$goalData->title.")";
                    $this->activitiesupdate($activities, $info, $user_id);

                   return response()->json(['data' => ['success' => true, 'task' => $task]], 201);
                }else{
                    return response()->json(['data' => ['error' => false, 'message' => 'An Error Occured!']], 419); 
                }
        }
     }else{
         return response()->json(['data' => ['error' => false, 'message' => 'Unauthorize Access!']], 401); 
     } 

    }

    public function update(Request $request, ViewPolicy $viewpolicy, TaskPolicy $taskpolicy, $goal_id, $task_id, Activities $activities) {
      $user = Auth::user();
      $detail = $viewpolicy->userPassage($goal_id);       
      $goal = Goal::where('id', $goal_id)->exists();

      if ($goal && $detail) {

           $this->validate($request, [
                'task_title'  => 'required',
                'description' => 'required|min:5',
                'begin_time'  => 'required',
                'due_time'    => 'required',
                'due_date'    => 'required',
            ]); 
       
        $begin_time =  date('h:i A', strtotime($request->input('begin_time')));

        $due_time   = date('h:i A', strtotime($request->input('due_time')));

        $due_date   = $request->input('due_date');

        $reminder   = $request->input('reminder');

        $task_title = $request->input('task_title');

        $goalData = Goal::where('id', $goal_id)->first();

        $taskvalidate = $taskpolicy->taskValidate($due_date, $goalData); 

        if ($taskvalidate) {

           return response()->json(['data' => ['error' => false, 'message' => 'your due date cannot exceed goal due date']], 401);     
          
        }      

        if ($task_title === $goalData->title) {

           return response()->json(['data' => ['error' => false, 'message' => 'Cannot have thesame goal title']], 401);
        }

        $check_title = Task::where('goal_id', $goal_id)->where('task_title', $task_title)->where('id', '!=', $task_id)->exists();
        $task_act = Task::where('goal_id', $goal_id)->where('id', $task_id)->first();

        $title_activities = $task_act->task_title;

        if($check_title) {

           return response()->json(['data' => ['error' => false, 'message' => 'Title already exist']], 401);

        }

              $update = Task::findOrfail($task_id);

              $update->goal_id     =  $goal_id;
              $update->task_title  =  ucwords($task_title);
              $update->description =  ucfirst($request->input('description'));
              $update->due_date    =  $due_date;   
              $update->begin_time  = $begin_time;
              $update->due_time    = $due_time;

               if (!empty($reminder)) {
                $update->reminder    = ucwords($reminder); 
                }
              
              $saved = $update->save();

                if ($saved) {
                      $user_id = $user->id;
                      $info = "You updated a Task——(".$title_activities.") from Goal—(".$goalData->title.") to (".$update->task_title.")!";
                      $this->activitiesupdate($activities, $info, $user_id);
                   return response()->json(['data' => ['success' => true, 'message' => 'Sccessfully Updated tasks']], 200);
                }else{
                    return response()->json(['data' => ['error' => false, 'message' => 'An Error Occured!']], 401); 
                }
     }else{
         return response()->json(['data' => ['error' => false, 'message' => 'Unauthorize Access!']], 401); 
     } 

    }

   public function updateTaskStatus(Request $request, ViewPolicy $viewpolicy, $goal_id, $task_id, Activities $activities) {


         $user = Auth::user();
          $detail = $viewpolicy->userPassage($goal_id);       
          $goal = Goal::where('id', $goal_id)->exists();

          $task_status = $request->input('task_status');

         if ($goal && $detail) {

             $data = Task::findOrfail($task_id);

             if ($task_status == "0" ||  $task_status == "1") {
                $data->task_status = $task_status;

                 $data->save();

                  $user_id = $user->id;
              $info = "Task—(".$data->task_title.") has been marked Completed!";
              $this->activitiesupdate($activities, $info, $user_id);
            
             return response()->json(['data' => [ 'success' => true, 'taskCompleted' => 'Task Completed']], 200);
             }else{
                 return response()->json(['data' => [ 'success' => true, 'message' => 'Invalid Credentials']], 401);
             }
             

             
          }else{
              return response()->json(['data' => ['error' => false, 'message' => 'Unauthorize Access!']], 401); 
          } 

    }

  public function destroy(ViewPolicy $viewpolicy, $task_id, $goal_id, Activities $activities) {
      $user = Auth::user();
      $detail = $viewpolicy->userPassage($goal_id);   

      $goal = Goal::where('id', $goal_id)->exists();

      if ($goal && $detail) {

           $check_task = Task::where('id', $task_id)->exists();  
           if ($check_task) {
              $data1 = Task::findOrfail($task_id);
              $data2 = Goal::findOrfail($goal_id);

              $user_id = $user->id;
              $info = "A Task—(".$data1->task_title.") of Goal—(".$data2->title.") is deleted";
              $this->activitiesupdate($activities, $info, $user_id); 

              $data1->delete();

              return response()->json(['data' => [ 'success' => true, 'message' => 'deleted' ]], 200);
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
