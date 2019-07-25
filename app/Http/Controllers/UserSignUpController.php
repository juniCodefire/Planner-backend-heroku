<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\ConfirmationLink;
use App\User;

use Illuminate\Support\Facades\DB;


class UserSignUpController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function store(Request $request, User $user) {
        //validate input
        $this->validateRequest($request);
      	//generate a ramdom api token for user recognition
        $token = hash('sha256', Str::random(60));
        //Generatate a token for the password recvery process
        $verify_token = hash('adler32', Str::random(60));
        //generate a ramdom api token for user confirmation
        $confirm_token = hash('sha256', Str::random(60));
        //Create a Username
        $uniqueID = mt_rand(00000, 90000);

        $username = explode(" ", $request->input('name'));
        $username = "@".lcfirst($username[0].$uniqueID);
        if (User::where('username', $username)->exists()) {
          $username = explode(" ", $request->input('name'));
          $username = "@".lcfirst($username[0].$uniqueID);
        }

        //insert the details into the user class and into the model class
         DB::beginTransaction();
         try{
            $user->name = ucwords($request->input('name'));
            $user->username = $username;
            $user->email = $request->input('email');
            $user->phone_number = $request->input('phone_number');
            $user->password = Hash::make($request->input('password'));

            $user->verify_code = $verify_token;
            $user->user_image = "user.jpg";
            $user->api_token = $token;
            $user->confirm_token = $confirm_token;

               Mail::to($user->email)->send(new ConfirmationLink($user));
               $split_email = $user->email;
               $emai_link = explode("@",$split_email);
               $emai_link = "www.".$emai_link[1];

               $info = $user->save();
               DB::commit();
               return response()->json(['data' => ['success' => true,
                                                      'message' => 'Registrtion Successful, A confirmation link has been sent to '.$user->email.'',
                                                      'user' => $user,
                                                      'email_link' => $emai_link]], 201);

            } catch (\Exception $e) {
               DB::rollBack();
               return response()->json(['data' =>['error' => false, 'message' => "Sending email failed , try again"]], 401);

            }

    }
    public function validateRequest($request) {
      $rules = [
       'name' => 'required',
       'email' => 'required|email|unique:users',
       'phone_number' => 'required|min:10|numeric',
       'password' => 'required|min:8|confirmed',
     ];
     $messages = [
    'required' => ':attribute is required',
    'email' => ':attribute not a valid format',
     ];

    $this->validate($request, $rules, $messages);
    }
}
