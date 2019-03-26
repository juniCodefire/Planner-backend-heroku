<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use App\Goal;
use App\Task;
use App\Policies\ViewPolicy;
use App\Policies\TaskPolicy;


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

                     $user_tasks = Task::where('goal_id', $goal_id)->get();   

                     return response()->json(['data' => [ 'success' => true, 'user_tasks' => $user_tasks]], 200);

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

     public function store(Request $request, ViewPolicy $viewpolicy, TaskPolicy $taskpolicy, Task $task, $goal_id) {

      Auth::user();
      $detail = $viewpolicy->userPassage($goal_id); 
      $goal = Goal::where('id', $goal_id)->exists();

      if ($goal && $detail) {

           $this->validate($request, [
                'task_title'  => 'required',
                'description' => 'required|min:5',
                'begin_date'  => 'required',
                'due_date'    => 'required'
            ]); 

        $detail = $viewpolicy->userPassage($goal_id); 

        $begin_date = $request->input('begin_date');

        $due_date   = $request->input('due_date');

        $task_title = $request->input('task_title');

        $goalData = Goal::where('id', $goal_id)->first();

        $taskvalidate = $taskpolicy->taskValidate($begin_date, $due_date, $goalData, $goal_id); 

        if ($taskvalidate) {

           return response()->json(['data' => ['error' => false, 'message' => 'Below Goals (begin date) Or Exceeded Goals (Due date)']], 500);     
          
        }      

        if ($task_title === $goalData->title) {

           return response()->json(['data' => ['error' => false, 'message' => 'Same Goal Title']], 401);
        }

        $check_title = Task::where('goal_id', $goal_id)->where('task_title', $request->input('task_title'))->exists();

        if($check_title) {

           return response()->json(['data' => ['error' => false, 'message' => 'Title already exist']], 401);

        }else{
              $task->goal_id     = $goal_id;
              $task->task_title  =  ucwords($task_title);
              $task->description =  ucfirst($request->input('description'));
              $task->begin_date  = $begin_date;
              $task->due_date    = $due_date;   
              $task->task_status     = 0;
              
              $saved = $task->save();

                if ($saved) {
                   return response()->json(['data' => ['success' => true, 'task' => $task]], 201);
                }else{
                    return response()->json(['data' => ['error' => false, 'message' => 'An Error Occured!']], 419); 
                }
        }
     }else{
         return response()->json(['data' => ['error' => false, 'message' => 'Unauthorize Access!']], 401); 
     } 

    }

    public function update(Request $request, ViewPolicy $viewpolicy, TaskPolicy $taskpolicy, $goal_id, $task_id) {
      $user = Auth::user();
      $detail = $viewpolicy->userPassage($goal_id);       
      $goal = Goal::where('id', $goal_id)->exists();

      if ($goal && $detail) {

           $this->validate($request, [
                'task_title'  => 'required',
                'description' => 'required|min:5',
                'begin_date'  => 'required',
                'due_date'    => 'required'
            ]); 

        $begin_date = $request->input('begin_date');

        $due_date   = $request->input('due_date');

        $task_title = $request->input('task_title');


        $goalData = Goal::where('id', $goal_id)->first();

        $taskvalidate = $taskpolicy->taskValidate($begin_date, $due_date, $goalData, $goal_id); 

        if ($taskvalidate) {

           return response()->json(['data' => ['error' => false, 'message' => 'Below Goals (begin date) Or Exceeded Goals (Due date)']], 500);     
          
        }      

        if ($task_title === $goalData->title) {

           return response()->json(['data' => ['error' => false, 'message' => 'Same Goal Title']], 401);
        }

        $check_title = Task::where('goal_id', $goal_id)->where('task_title', $task_title)->where('id', '!=', $task_id)->exists();

        if($check_title) {

           return response()->json(['data' => ['error' => false, 'message' => 'Title already exist']], 401);

        }
              $update = Task::findOrfail($task_id);

              $update->goal_id     =  $goal_id;
              $update->task_title  =  ucwords($task_title);
              $update->description =  ucfirst($request->input('description'));
              $update->begin_date  =  $begin_date;
              $update->due_date    =  $due_date;   
              $update->task_status = 0;
              
              $saved = $update->save();

                if ($saved) {
                   return response()->json(['data' => ['success' => true, 'message' => 'Tasks Updated']], 200);
                }else{
                    return response()->json(['data' => ['error' => false, 'message' => 'An Error Occured!']], 419); 
                }
     }else{
         return response()->json(['data' => ['error' => false, 'message' => 'Unauthorize Access!']], 401); 
     } 

    }

  public function destroy(ViewPolicy $viewpolicy, $task_id, $goal_id) {
      Auth::user();
      $detail = $viewpolicy->userPassage($goal_id);   

      $goal = Goal::where('id', $goal_id)->exists();

      if ($goal && $detail) {

           $check_task = Task::where('id', $task_id)->exists();  
           if ($check_task) {

              $data = Task::findOrfail($task_id);

              $data->delete();

              return response()->json(['data' => [ 'success' => true, 'message' => 'deleted' ]], 200);
           }

      }else{

         return response()->json(['data' => ['error' => false, 'message' => 'Unauthorize Access!']], 401); 
      }
  }

}
