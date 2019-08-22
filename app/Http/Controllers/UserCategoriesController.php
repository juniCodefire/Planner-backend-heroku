<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

use App\User;
use App\Category;
use Illuminate\Support\Facades\DB;
/**
 *
 */

class UserCategoriesController extends Controller
{
//This permits who is an admin
    public function show($id) {
	    $category = Category::find($id);
		    if ($category ) {
		    	$category = Category::with('interest')->get();
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
	    	return response()->json(['message' => "Error creating category, try again", 'error_hint' => $e->getMessage()], 401);
        }
	}
}