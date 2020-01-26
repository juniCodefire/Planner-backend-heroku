<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

use App\Admin;
use App\Interest;
use Illuminate\Support\Facades\DB;
/**
 *
 */

class AdminInterestController extends Controller
{
//This permits who is an admin
	public function __construct()
    {
        $this->middleware('admin.only');
    }
	public function create(Request $request, Interest $interest, $category_id) {
		$this->validate($request, [
        	'title'    => 'required|unique:interests',
        ]);
        	DB::beginTransaction();
        try {
        	$interest->title = $request->input('title');
        	$interest->category_id = $category_id;
			$interest->save();
			DB::commit();
	   		return response()->json(['message' => "Interest Created", 'interest' => $interest], 201);
        } catch (\Exception $e) {
        	DB::rollBack();
	    	return response()->json(['message' => "Error: interest already exists or you can try again", 'error_hint' => $e->getMessage()], 401);
        }
	}
	public function update(Request $request, $category_id, $id) {

		$edit = Interest::find($id);

		if ($edit) {
				$this->validate($request, [
	        	'title'    => 'required',
	        ]);
	       		 DB::beginTransaction();
	        try {
	        	$edit->title = $request->input('title');
				$edit->save();
				DB::commit();
		   		return response()->json(['message' => "Interest Updated" , 'category' => $edit], 200);
	        } catch (\Exception $e) {
	        	DB::rollBack();
		    	return response()->json(['message' => "Update Error, try again", 'error_hint' => $e->getMessage()], 401);
	        }
		}else {
				return response()->json(['message' => "Not Found"], 404);
		}
	}

	public function destroy($id) {
		$del = Interest::find($id);
		if ($del) {
			$del->delete();
			return response()->json(['message' => 'Interest deleted'], 200);
		}else  {
			return response()->json(['message' => 'Error Not Found'], 404);
		}
	}
}