<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Goal;
use App\Task;
use App\Activities;

class ActivitiesController extends Controller
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

        $activities = Activities::where('owner_id', $user->id)->orderBy('id', 'desc')->get();

        return response()->json(['data' => [ 'success' => true, 'activities' => $activities]], 200);

    }

    public function goalsCount() {

         $user = Auth::user(); 

         $goalsCount = Goal::where('owner_id', $user->id)->count();

         $goalsCompleted = Goal::where('owner_id', $user->id)->where('goal_status', 1)->count();

         $goalsUnCompleted = Goal::where('owner_id', $user->id)->where('goal_status', 0)->count();

         return response()->json(['data' => [ 'success' => true, 'goalsCount' =>  $goalsCount, 'goalsCompleted' => $goalsCompleted , '$goalsUncompleted' => $goalsUnCompleted]], 200);

    }

     public function tasksCount() {

         $user = Auth::user(); 

         $taskCount = Task::where('owner_id', $user->id)->count();

         $tasksCompleted = Task::where('owner_id', $user->id)->where('task_status', 1)->count();

         $tasksUnCompleted = Task::where('owner_id', $user->id)->where('task_status', 0)->count();

         return response()->json(['data' => [ 'success' => true, 'taskCount' =>  $taskCount, 'tasksCompleted' => $tasksCompleted , '$tasksUncompleted' => $tasksUnCompleted]], 200);

    }

    public function goalTasksCount($goal_id) {

         $user = Auth::user(); 

         $goalTaskCount = Task::where('goal_id', $goal_id)->count();

         $tasksCompleted = Task::where('goal_id', $goal_id)->where('task_status', 1)->count();

         $tasksUnCompleted = Task::where('goal_id', $goal_id)->where('task_status', 0)->count();

         return response()->json(['data' => [ 'success' => true, 'goalTaskCount' =>  $goalTaskCount, 'tasksCompleted' => $tasksCompleted , '$tasksUncompleted' => $tasksUnCompleted]], 200);

    }

}
