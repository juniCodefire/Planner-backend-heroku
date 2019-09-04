<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

use App\User;
use App\WorkSpace;
use App\Company;
use App\Project;
use Illuminate\Support\Facades\DB;
/**
 *
 */

class UserProjectController extends Controller
{
	public function showAll() {


	}
	public function showOne() {

	}
	public function create(Request $request, Project $project, $workspace_id, $company_id = '0') {
		$user = Auth::user();
		$this->validateRequest($request);
		$check_workspace_exist_1 = WorkSpace::where('id', $workspace_id)->exists();

		if ($check_workspace_exist_1) {
			$check_workspace_exist_2 = Project::where('workspace_id', $workspace_id)->exists();
			if (!$check_workspace_exist_2) {
				DB::beginTransaction();
				if (empty($request->input('description'))) {
					$description = 'Write a simple detail about you project for clarity...';
				}else {
					$description = $request->input('description');
				}
				try{
				$project->owner_id = Auth::user()->id;
				$project->workspace_id = (int)$workspace_id;
				if ($company_id != '0') {
					 $check_company = Company::where('id', $company_id)->where('owner_id', Auth::user()->id)->exists();
			         if ($check_company) {
			            $project->company_id = (int)$company_id;
			         }else {
			            return response()->json(['message' => 'Company does not exist'], 404);
			         }
				}
				$project->title = $request->input('title');
				$project->description = $description;
				$project->save();
				DB::commit();
				  return response()->json(['success' => true, 'message' => 'Project Created', 'project' => $project], 201);
				}catch(\Exception $e) {
					DB::rollBack();
				  return response()->json(['error' => true, 'message' => 'An error Occurred, please try again', 'hint' => $e->getMessage()], 500);
				}
			}else {
				  return response()->json(['error' => true, 'message' => 'Project already created for this workspace'], 401);
			}
		}else {
		    return response()->json(['error' => true, 'message' => 'Workspace or comapny does not exists'], 404);
		}

	}
	public function update(Request $request, Project $project, $id) {
		$user = Auth::user();
		$this->validateRequest($request);
		$edit_project = Project::find($id);
		if ($edit_project) {
			if (empty($request->input('description'))) {
					$description = 'Write a simple detail about you project for clarity...';
			}else {
				$description = $request->input('description');
			}
			try{
			   if (Auth::user()->id === $edit_project->owner_id) {
					$edit_project->title = $request->input('title');
					$edit_project->description = $description;
					$edit_project->save();
					return response()->json(['success' => true, 'message' => 'Edited Succesfully', 'project' => $edit_project], 200);
				}else {
					return response()->json(['success' => true, 'message' => 'Unauthorize'], 401);
				}
			}catch(\Exception $e) {
				return response()->json(['success' => true, 'message' => 'An Error Occurred', 'hint' => $e->getMessage()], 500);
			}
		}else {
			return response()->json(['success' => true, 'message' => 'Project Not found'], 404);
		}

	}
	public function destroy($id) {
		$del_project = Project::find($id);
		if ($del_project) {
			if (Auth::user()->id === $del_project->owner_id) {
				$del_project->delete();
				return response()->json(['error' => true, 'message' => 'Project deleted'], 200);
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