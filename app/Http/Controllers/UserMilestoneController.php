<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

use App\User;
use App\Project;
use App\MileStone;
use Illuminate\Support\Facades\DB;
/**
 *
 */

class UserMileStoneController extends Controller
{
	public function showAll($project_id, $milestone_id) {
   $user = Auth::user();
    $milestone = MileStone::where('project_id', $project_id)
              ->with('tasks')
              ->get();

    if ( $milestone ) { 
      return response()->json(['success' => true, 'milestones ' => $milestone ]); 
    }else {
      return response()->json(['error' => true, 'message' => 'Milestone not found '], 404);
    }

  }

  public function showOne($project_id, $milestone_id) {
    $user = Auth::user();
    $milestone = MileStone::where('project_id', $project_id)->where('id', $milestone_id)
              ->with('tasks')
              ->get();

    if ( $milestone ) { 
      return response()->json(['success' => true, 'milestone ' => $milestone ]); 
    }else {
      return response()->json(['error' => true, 'message' => 'Milestone not found '], 404);
    }
  }

  public function store(Request $request, MileStone $milestone, $project_id, $assigned_id)
  {
    $user = Auth::user();
    //Validate the input
    $this->validateRequest($request);
    DB::beginTransaction();

    try {
      $milestone->owner_id = $user->id;	
      $milestone->assigned_id = $assigned_id;
      $milestone->project_id = $project_id;
      $milestone->title = ucwords($request->input('title'));
      if ($request->input('description')) {
      	$description = $request->input('description');
      }else {
        $description = 'Description can help improve clarity of milestone actual purpose...';
      }
      $milestone->description = ucwords($description);
      $milestone->save();

      DB::commit();
      return response()->json(['data' => ['success' => true, 'message' => 'Successfully Created!', 'milestone' => $milestone]], 200);
    } catch (\Exception $e) {

      DB::rollBack();
      return response()->json(['data' => ['error' => false, 'message' => 'An error occured retry again!', 'hint' => $e->getMessage()]], 501);
    }
  }

  public function update(Request $request, MileStone $milestone, $id, $assigned_id) {
    $user = Auth::user();
    $this->validateRequest($request);
    $edit_milestone = MileStone::find($id);
    if ($edit_milestone) {
      if (empty($request->input('description'))) {
          $description = 'Write a simple detail about you milestone for clarity...';
      }else {
           $description = $request->input('description');
      }
      try{
         if (Auth::user()->id === $edit_milestone->owner_id) {
           $edit_milestone->title = $request->input('title');
           $edit_milestone->assigned_id = $assigned_id;
           $edit_milestone->description = $description;
           $edit_milestone->save();
          return response()->json(['success' => true, 'message' => 'Edited Succesfully', 'Milestone' => $edit_milestone], 200);
        }else {
          return response()->json(['error' => true, 'message' => 'Unauthorize'], 401);
        }
      }catch(\Exception $e) {
        return response()->json(['error' => true, 'message' => 'An Error Occurred', 'hint' => $e->getMessage()], 500);
      }
    }else {
      return response()->json(['error' => true, 'message' => 'Milestone Not found'], 404);
    }

  }

  public function destroy($id) {
      $del_milestone = MileStone::find($id);
      if ($del_milestone) {
        if (Auth::user()->id === $del_milestone->owner_id) {
          $del_milestone->delete();
          return response()->json(['success' => true, 'message' => 'Milestone deleted'], 200);
        }else {
          return response()->json(['error' => true, 'message' => 'Unauthorize'], 401);
        }
      }else {
        return response()->json(['error' => true, 'message' => 'An error occured, please try agian'], 500);
      }
    }

	public function validateRequest($request) {

		$rules = [
 		  'title' =>   'required',
 		  'description' => 'min:5'
		];

		$messages = [
			'required' => ':attribute is required'
		];
		$this->validate($request, $rules);
	}

}