<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\CompanyRequest;

use App\User;
use App\Company;
use App\Request;
use Illuminate\Support\Facades\DB;
/**
 *
 */
class UserCompaniesController extends Controller
{

  public function request(Request $request, Request $request_invite) {
     $requester = Auth::user();

     //Check if the worksapce title is required and _exist
     $this->validateCompany($request, $i = 0);
     $check_workspace = Company::where('title', ucwords($request->input('title')))
                                   ->where('owner_id', '!=', Auth::user()->id)
                                   ->exists();
     $check_unique_name = Company::where('unique_name', $request->input('title'))
                                   ->where('owner_id', '!=', Auth::user()->id)
                                   ->exists();
    if ($check_workspace || $check_unique_name) {
        $check_unique_name = $request->input('title');
        //Check if the user use the name or the username to send a requested
          if (stripos($check_unique_name, " ")) {
            //Return all work sapce with their name and their unique username for the user to choose and send a request
            $choose_companies = Company::where('title', $request->input('title'))->where('owner_id', '!=', Auth::user()->id)->where('status', 'Public')->get();
            return response()->json(['data' => ['success' => true, 'key' => '1', 'message' => 'Choose an ideal company from the list',
                                                                   'message-2' => 'If the comapany is not found in the list, it means the company is private!',
                                                                   'message-3' => 'You can contact the company owner to invite you!', 'choose_company' => $choose_companies]]);
          }
         //Here will continue if the username is know!
         //Get the worksapce unique_name
          $company = Company::where('title', $request->input('title'))->orWhere('unique_name', $request->input('title'))->first();

          if ($company->status === "Public") {
            //Get the owner of Worksapce Data
            $requestee = $company->users()->first();

            if (!RequestInvite::where('requestee_id', $requestee->id)->where('requester_id', Auth::user()->id)->where('company_id', $company->id)->exists()) {
                DB::beginTransaction();
                 try {
                   //Save to a temporary Request table
                   $request_invite->requestee_id = $requestee->id;
                   $request_invite->requester_id = Auth::user()->id;
                   $request_invite->company_id = $company->id;
                   $request_invite->save();
                   //Send a Request mail
                   Mail::to($requestee->email)->send(new CompanyRequest($requester, $requestee, $company));
                   DB::commit();
                   return response()->json(['data' => ['success' => true, 'key' => '2', 'message' => 'A request has be sent to company owner!']], 200);
                 } catch (\Exception $e) {
                   DB::rollBack();
                   return response()->json(['data' =>['error' => false, 'message' => "Sending email failed , try again!", 'hint' => $e->getMessage()]], 501);
                 }
            }return response()->json(['data' => ['success' => true, 'message' => 'Sorry your request to joining this company have not been confirmed!']], 403);

          }return response()->json(['data' => ['success' => true, 'message' => 'Sorry this is a secured company, contact the owner!']], 401);

       }return response()->json(['data' => ['success' => true, 'message' => 'Sorry this company is not available or not allowed, try using a company unique name instead!']], 403);

   }




   public function store(Request $request, Company $company) {
     $user = Auth::user();
     //Validate the input
     $this->validateCompany($request, $i= 1);
     //Recurssive Function to Regenerate Wiorkspace Unique Name
     $unique_name = $this->generateUniqueName($request);
     $description = $request->input('description');
     //Insert the company into the Database(Save)
     if ($description  == '') {
       $description  = 'Description can help improve clarity of Company actual purpose!';
     }
     DB::beginTransaction();
     try {
        $company->title = ucwords($request->input('title'));
        $company->owner_id = Auth::user()->id;
        $company->unique_name = $unique_name;
        $company->industry = ucwords($request->input('industry'));
        $company->role = ucwords($request->input('role'));
        $company->wallpaper = ucwords($request->input('wallpaper'));
        $company->description = ucwords($description);
        $company->status = ucwords($request->input('status'));
        $company->save();

        DB::commit();
        return response()->json(['data' => ['success' => true, 'message' => 'Successfully Created!', 'new_company' => $company ]], 200);
     } catch (\Exception $e) {

        DB::rollBack();
        return response()->json(['data' => ['error' => false, 'message' => 'An error occured retry again!', 'hint' => $e->getMessage()]], 501);
     }

   }

   public function generateUniqueName($request) {
     //Generate a ramdom unique_name for the Worksapce
     $rand = mt_rand(00000, 90000);
     $value = explode(" ", $request->input('title'));
     $unique_name = '#'.strtolower(implode($value)).$rand;
     //Check if the unique_name already exist in the workspace table "if yes regenerate a new one"
     $check_unique_name = Company::where('unique_name', $unique_name)->exists();
     if ($check_unique_name) {
        $this->generateUniqueName($request);
     }else{
       return $unique_name;
     }

   }

   public function validateCompany($request, $i = 0) {
        if($i == 0) {
           $rules = [
               'title' => array(
                          'required',
                          'regex:/(^([ #a-zA-Z]+)(\d+)?$)/u'
                        ),
           ];
         }else {
           $rules = [
               'title' => array(
                          'required',
                          'regex:/(^([ a-zA-Z]+)(\d+)?$)/u'
                        ),
               'industry' => array(
                          'required',
                          'string'
                          ),
               'role' =>  array(
                          'required',
                          'string'
                          ),
               'wallpaper' => 'required',
               'description' => 'max:150',
               'status' => 'required|string'
           ];
         }
     $messages = [
       'required' => ':attribute is required',
       'regex' => ':attribute is invalid accepted only format(a-z,A-Z,0-9)'
     ];
     $this->validate($request, $rules, $messages);
   }

}
