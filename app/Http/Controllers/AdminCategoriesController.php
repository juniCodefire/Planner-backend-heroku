<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

use App\Admin;
use App\Category;
use Illuminate\Support\Facades\DB;
/**
 *
 */

class AdminCategoriesController extends Controller
{
//This permits who is an admin
	public function __construct()
    {
        $this->middleware('admin.only');
    }
    public function show($id) {
	    $category = Category::find($id);
		    if ($category) {
		    	$category = Category::with('interests')->first();
		    	return response()->json(['message' => 'Category and its interests', 'category' => $category,], 200);
		    }else {
		        return response()->json(['message' => 'Error or not found'], 404);
		    }
    }
    public function showAll(Category $category) {
    $categories = Category::with('interests')->get();
    return response()->json(['message' => 'All categories and interest', 'categories' => $categories], 200);
    }
	public function create(Request $request, Category $category) {
		$this->validate($request, [
        	'title'    => 'required',
        ]);
        	DB::beginTransaction();
        try {
        	$category->title = $request->input('title');
			$category->save();
			DB::commit();
	   		return response()->json(['message' => "Category Created", 'category' => $category], 201);
        } catch (\Exception $e) {
        	DB::rollBack();
	    	return response()->json(['message' => "Error: category already exists or you can try again", 'error_hint' => $e->getMessage()], 401);
        }
	}
	public function update(Request $request, $id) {

		$edit = Category::find($id);

		if ($edit) {
				$this->validate($request, [
	        	'title'    => 'required|unique:categories',
	        ]);
	       		 DB::beginTransaction();
	        try {
	        	$edit->title = $request->input('title');
				$edit->save();
				DB::commit();
		   		return response()->json(['message' => "Category Updated" , 'category' => $edit], 200);
	        } catch (\Exception $e) {
	        	DB::rollBack();
		    	return response()->json(['message' => "Update Error, try again", 'error_hint' => $e->getMessage()], 401);
	        }
		}else {
				return response()->json(['message' => "Not Found"], 404);
		}
		
	}
	public function destroy($id) {
		$del_category = Category::find($id);
		if ($del_category) {
			$del_category->delete();
			return response()->json(['message' => 'Category deleted'], 200);
		}else  {
			return response()->json(['message' => 'Error Not Found'], 404);
		}
	}
}