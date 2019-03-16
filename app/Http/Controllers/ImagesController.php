<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use App\Goal;
use App\Policies\ViewPolicy;
use Cloudder;

class ImagesController extends Controller
{

	 public function __construct()
    {
        $this->middleware('auth');
    }

    public function upload(Request $request)
    {
        $user = Auth::user();

    	$this->validate($request, [
    		'user_image' => 'required',
    	]);

        //Get the extension of the image
        $extension = strtolower($request->file('user_image')->extension());
        //Get the allow extentions
        $allowed_ext = array("png", "jpg", "jpeg");
        //Get the Image Size
        $file_size = filesize($request->file('user_image'));

        		if ($request->hasFile('user_image') && $request->file('user_image')->isValid()){

        			if (in_array($extension, $allowed_ext)) {

        				//Error hadling to control file size
        				if ($file_size > 500000) {
        					return response()->json(['data' => ['error' => false, 'message' => 'Too large(Only <= 500000)']], 400);
        				}
	        			if ($user->user_image != "user.jpg") {

	        				$image_filename = pathinfo($user->user_image, PATHINFO_FILENAME);

	        				try {
	        					$delete_old_image = Cloudder::destroyImage($image_filename);
	        				} catch (Exception $e) {

	        					return response()->json(['data' => ['error' => false, 'message' => 'Try again']], 401);
	        				}
	        			}
        		   		  //Save to Cloud api
	        			    try {
	        					$cloudder = Cloudder::upload($request->file('user_image')->getRealPath());
	        				} catch (Exception $e) {
	        					
	        					return response()->json(['data' => ['error' => false, 'message' => 'Try again']], 401);
	        				}
		                 

				    if ($cloudder) {
				    	//Request the image info from api and save to db
				    	$uploadResult = $cloudder->getResult();
				    	//Get the public id or the image name
					    $file_url = $uploadResult["public_id"];
					    //Get the image format from the api
					    $format = $uploadResult["format"];

					    $user_image = $file_url.".".$format;

					    $user->user_image = $user_image;

					    $user->save();

					    return response()->json(['data' => ['success' => true, 'user_image' => $user_image, 'image_link' => 'http://res.cloudinary.com/getfiledata/image/upload/v1552380958/']], 200);

				    }else{
				    	return response()->json(['data' => ['error'=>false, 'message' => 'There was an error']], 401);
				    }
				    
				}else{
					return response()->json(['data' => ['error'=> false, 'message' => 'Invalid format(Use jpg,jpeg,png)!']], 400);
				}
        	}

    }
}
