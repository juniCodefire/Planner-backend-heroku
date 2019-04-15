<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Mail\ConfirmationLink;
use Illuminate\Support\Facades\Mail;
use Cloudder;
use App\User;
Use App\Activities;

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

    public function index() {

        $user = Auth::user();

        return response()->json(['data' => [ 'success' => true, 'user' => $user, 'image_link' => 'http://res.cloudinary.com/getfiledata/image/upload/v1552380958/']], 200);

    }

    public function update(Request $request, Activities $activities) {
        //Get the Auth Valid User
        $user = Auth::user();
        $id = Auth::id();


        $token = $user->api_token;

            $this->validate($request, [
                'name' => 'required',
                'email' => 'required|email|unique:users,email,'.$id,
                'phone_number' => 'required|min:10|numeric',
                'password' => '|min:6|confirmed',
            ]);

            $email = "";
            if ($user->email != $request->input('email')) {

                try{
                    Mail::to($request->input('email'))->send(new ConfirmationLink($user));

                    $email = "A verfication code has been sent to ".$user->email."!";

                 } catch (Exception $ex) {

                     return response()->json(['data' => ['error' => false, 'message' => 'Try again']], 401);

                 }
                  $user->status = "off";
            }

            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->phone_number = $request->input('phone_number');
            $password = $request->input('password');
            //check if they did any password update
            if (!empty($password)) {              
               $password = Hash::make($password);
               $user->password = $password;
            }
            $saved = $user->save();

            if ($saved) {
                $user_id = $user->id;
                $info = "Your profile account details is updated";
                $this->activitiesupdate($activities, $info, $user_id);    

                if (empty($email)) {
                    return response()->json(['data' => ['success' => true, 'message' => 'User Updated!', 'user' => $user, 'image_link' => 'http://res.cloudinary.com/getfiledata/image/upload/v1552380958/', 'token' => 'Bearer ' .$token ]], 201);
                }else{
                    return response()->json(['data' => ['success' => true, 'message' => 'User Updated, A Confirmation email has been sent for this email!', 'user' => $user, 'image_link' => 'http://res.cloudinary.com/getfiledata/image/upload/v1552380958/', 'token' => 'Bearer ' .$token ]], 201);     
                }
               
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

             if ($user->user_image != "user.jpg") {

                $image_filename = pathinfo($user->user_image, PATHINFO_FILENAME);

                try {
                    $delete_old_image = Cloudder::destroyImage($image_filename);
                } catch (Exception $e) {

                    return response()->json(['data' => ['error' => false, 'message' => 'Try again']], 401);
                }
            } 
    
            $delete = $user->delete();
            if ($delete) {
               return response()->json(['data' => ['success' => true, 'message' => 'User Deleted!' ]], 200);
            }
        
        }else{
            return response()->json(['data' => ['error' => false, 'messagee' => "Invalid Password"]], 401);
        }

    }
    public function activitiesupdate($activities, $info, $user_id) {

         $time =  time();
         $created_time = date('h:i A — Y-m-d', $time+3600);

         $activities->owner_id = $user_id;
         $activities->narrative = $info." @ ".$created_time.".";
         $activities->save();
    }

}
