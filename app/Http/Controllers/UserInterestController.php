<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

use App\User;
use App\Interest;
use App\UserInterest;
use App\Category;
use Illuminate\Support\Facades\DB;
/**
 *
 */

class UserInterestController extends Controller
{
	public function select(Request $request, Interest $interest) {
			$user = Auth::user();
			$this->validateRequest($request);
        	DB::beginTransaction();
        try {
        	$sel_interests = $request->input('user_interests');
        	foreach ($sel_interests as $value) {
        		$user_interest = new UserInterest();
        		$check_exist = Interest::where('id', $value[1])->where('category_id', $value[0])->exists();
        		if ($check_exist) {
        			$user_interest->owner_id = Auth::user()->id;
	        		$user_interest->interest_id = $value[1];
		        	$user_interest->category_id = $value[0];
        		}else {
        			return response()->json(['success' => 'false', 'message' => 'Category or interest with this ids does not exist', 'interest_id' => [$value[0],$value[1]]], 401);
        		} 
        		$user_interest->save();	
        	}

        	$getInterest = $user->interest()->get();
			DB::commit();
	   		return response()->json(['message' => "Interest Created", 'interest' => $getInterest], 201);
        } catch (\Exception $e) {
        	DB::rollBack();
	    	return response()->json(['message' => "Error: An unexpected issue occured or interest already exists, try again", 'error_hint' => $e->getMessage()], 401);
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

	public function validateRequest($request) {

		$rules = [
 		  'user_interests' =>   'required|array',
		];

		$messages = [
			'required' => ':attribute is required'
		];
		$this->validate($request, $rules);
	}
}