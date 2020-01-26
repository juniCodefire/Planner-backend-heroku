<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

use App\User;
use App\Project;
use App\MileStone;
use App\Task;
use Illuminate\Support\Facades\DB;
/**
 *
 */

class UserTaskController extends Controller
{
	public function showOne($id) {
    $user = Auth::user();
    $task = Task::where('id', $id)
                  ->where('owner_id', $user->id)
                  ->first();

    if ($task) { 
      return response()->json(['success' => true, 'task' => $task ]); 
    }else {
      return response()->json(['error' => true, 'message' => 'Task not found'], 404);
    }

  }

  public function showAll() {
    $user = Auth::user();
    $tasks= Task::where('owner_id', $user->id)
                  ->get();

    if ($tasks) { 
      return response()->json(['success' => true, 'tasks' => $tasks ]); 
    }else {
      return response()->json(['error' => true, 'message' => 'Task not found'], 404);
    }

  }

  public function store(Request $request, Task $task, $milestone_id)
  {
    $user = Auth::user();
    //Validate the input
    $this->validateRequest($request);
    DB::beginTransaction();
    $check_milestone = MileStone::where('id', $milestone_id)->exists();
    if($check_milestone) {
      try {
        $task->owner_id = $user->id;  
        $task->milestone_id = $milestone_id;
        $task->title = ucwords($request->input('title'));
        if ($request->input('description')) {
          $description = $request->input('description');
        }else {
          $description = 'Description can help improve clarity of task actual purpose...';
        }
        $task->description = ucwords($description);
        $task->save();

        DB::commit();
        return response()->json(['data' => ['success' => true, 'message' => 'Task Successfully Created!', 'task' => $task]], 200);
      } catch (\Exception $e) {

        DB::rollBack();
        return response()->json(['data' => ['error' => false, 'message' => 'Task not created: An error occured please try again!', 'hint' => $e->getMessage()]], 501);
      }
    }else {
        return response()->json(['data' => ['error' => false, 'message' => 'MileStone not does not belong to this user!']], 403);
    }
  }

  public function update(Request $request, $id) {
        $user = Auth::user();
        $task = Task::find($id);
        if ($task) {
          $this->validate($request, [
              'title' => 'required|min:2|unique:tasks,title,'.$task->id,
              'description' => 'required|min:5'
          ]);

          if (empty($request->input('description'))) {
              $description = 'Write a simple detail about you milestone for clarity...';
          }else {
               $description = $request->input('description');
          }
          try{
            if ($user->id === $task->owner_id) {
               $task->title = $request->input('title');
               $task->description = $description;
               $task->save();
               return response()->json(['success' => true, 'message' => 'Task edited succesfully', 'task' => $task], 200);
            }else {
               return response()->json(['error' => true, 'message' => 'Unauthorize'], 401);
            }
          }catch(\Exception $e) {
            return response()->json(['error' => true, 'message' => 'Task not Updated An Error Occurred', 'hint' => $e->getMessage()], 500);
          }
        }else {
          return response()->json(['error' => true, 'message' => 'Task Not found'], 404);
        }
  }

  public function destroy($id) {
     $del_task = task::find($id);
      if ($del_task) {
        if (Auth::user()->id === $del_task->owner_id) {
          $del_task->delete();
          return response()->json(['success' => true, 'message' => 'Task deleted'], 200);
        }else {
          return response()->json(['error' => true, 'message' => 'Unauthorize'], 401);
        }
      }else {
        return response()->json(['error' => true, 'message' => 'An error occured, please try agian'], 500);
      }
  }

	public function validateRequest($request) {

		$rules = [
 		  'title' =>   'required|unique:tasks',
 		  'description' => 'required|min:5'
		];

		$messages = [
			'required' => ':attribute is required'
		];
		$this->validate($request, $rules);
	}

}