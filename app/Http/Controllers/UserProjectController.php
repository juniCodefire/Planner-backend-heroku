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
	public function update() {

	}
	public function destroy() {

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