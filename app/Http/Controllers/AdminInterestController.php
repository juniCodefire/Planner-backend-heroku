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
    public function show($id) {
	    $category = Category::find($id);
		    if ($category ) {
		    	$category = Category::with('interest')->get();
		    	return response()->json(['message' => 'Category and its interests', 'category' => $category,], 200);
		    }else {
		        return response()->json(['message' => 'Error or not found'], 404);
		    }
    }
    public function showAll( $category) {
    $categories = Category::with('interests')->get();
    return response()->json(['message' => 'All categories and interest', 'categories' => $categories], 200);
    }
	public function create(Request $request, Interest $category, $id) {
		$this->validate($request, [
        	'title'    => 'required',
        ]);
        	DB::beginTransaction();
        try {
        	$interest->title = $request->input('title');
        	$interest->category_id = $id;
			$interest->save();
			DB::commit();
	   		return response()->json(['message' => "Interest Created", 'category' => $category], 201);
        } catch (\Exception $e) {
        	DB::rollBack();
	    	return response()->json(['message' => "Error creating interest, try again", 'error_hint' => $e->getMessage()], 401);
        }
	}
	public function update(Request $request, $id) {

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

	public function delete($id) {
		$del = Interest::find($id);
		if ($del) {
			$del->delete();
			return response()->json(['message' => 'Interest deleted'], 200);
		}
	}
}