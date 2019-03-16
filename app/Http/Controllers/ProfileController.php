<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use App\User;

class ProfileController extends Controller
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

    public function dashboard() {

        $user = Auth::user();

        return response()->json(['data' => [ 'success' => true, 'user' => $user, 'image_link' => 'http://res.cloudinary.com/getfiledata/image/upload/v1552380958/']], 200);

    }

    public function update(Request $request) {
        //Get the Auth Valid User
        $user = Auth::user();
        $id = Auth::id();


        $token = $user->api_token;

            $this->validate($request, [
                'name' => 'required',
                'email' => 'required|email|unique:users,email,'.$id,
                'phone_number' => 'required|min:10|numeric|phone',
                'password' => '|min:6|confirmed',
                'account_type' => 'required'
            ]);

            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->phone_number = $request->input('phone_number');
            $user->account_type = $request->input('account_type');

            $password = Hash::make($request->input('password'));
            //check if they did any password update
            if (!empty($password)) {              
               
               $user->password = $password;
            }

            $saved = $user->save();

            if ($saved) {
                return response()->json(['data' => ['success' => true, 'message' => 'User Updated!', 'user' => $user, 'image_link' => 'http://res.cloudinary.com/getfiledata/image/upload/v1552380958/', 'token' => 'Bearer ' .$token ]], 200);
            }else{
                return response()->json(['data' => ['error' => false, "message" => 'Error, Try Again!']], 401);
            }

    }

    public function destroy(Request $request) {
        //Get the Auth Valid User
        $user = Auth::user();

         $this->validate($request, [
                'password' => 'required|min:6',
            ]);

            $password = $request->input('password');

            //Check if password match
        if (Hash::check($password, $user->password)) {

            $delete = $user->delete();
            if ($delete) {
               return response()->json(['data' => ['success' => true, 'message' => 'User Deleted!' ]], 200);
            }
        
        }else{
            return response()->json(['data' => ['error' => false, 'messagee' => "Invalid Password"]], 401);
        }

    }
}
