<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User\UserManagement AS User;
use App\Models\User\ProfileManagement AS Profile;
use App\Models\User\CompanyManagement AS Company;
use App\Models\User\AuthCompanyManagement AS AuthCompany;
use App\Models\Master\RoleMaster AS Role;
use App\Models\Master\WorkflowMaster AS Workflow;
use Illuminate\Hashing\BcryptHasher AS Hash;
use App\Helpers\Api;
use App\Helpers\Template;

class UsersController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){
        $this->role = Role::orderBy('id_role_master','ASC')->first()->id_role_master;
        $this->workflow = Workflow::orderBy('id_workflow_status', 'ASC')->first()->id_workflow_status;
    }


    /**
    * @SWG\Post(
    *     path="/reg",
    *     consumes={"multipart/form-data"},
    *     description="Register Lendtick",
    *     operationId="reg",
    *     consumes={"application/x-www-form-urlencoded"},
    *     produces={"application/json"},
    *     @SWG\Parameter(
    *         description="Name",
    *         in="formData",
    *         name="name",
    *         required=true,
    *         type="string"
    *     ),
    *     @SWG\Parameter(
    *         description="Company code from list",
    *         in="formData",
    *         name="company",
    *         required=true,
    *         type="string"
    *     ),
    *     @SWG\Parameter(
    *         description="Base64 jpg of identity",
    *         in="formData",
    *         name="identity_photo",
    *         required=true,
    *         type="string"
    *     ),
    *     @SWG\Parameter(
    *         description="Base64 jpg of company identity",
    *         in="formData",
    *         name="company_identity_photo",
    *         required=true,
    *         type="string"
    *     ),
    *     @SWG\Parameter(
    *         description="Phone Number",
    *         in="formData",
    *         name="phone_number",
    *         required=true,
    *         type="string"
    *     ),
    *     @SWG\Parameter(
    *         description="Email new user",
    *         in="formData",
    *         name="email",
    *         required=true,
    *         type="string"
    *     ),
    *     @SWG\Parameter(
    *         description="Base64 jpg of self photo",
    *         in="formData",
    *         name="personal_photo",
    *         required=true,
    *         type="string"
    *     ),
    *     @SWG\Response(
    *         response="200",
    *         description="successful"
    *     ),
    *     summary="Registration",
    *     tags={
    *         "Authentication"
    *     }
    * )
    * */
    public function register(Request $r, Hash $h){
        // default password
        $pass = $h->make('kop2018');

        $data = $r->only(['name','company','identity_photo','company_identity_photo','phone_number','email','personal_photo']);
        if(count($data) == 7){
            try{
                // Data user
                $user = User::firstOrNew(['username' => $data['email']],['id_role_master' => $this->role, 'id_workflow_status' => $this->workflow, 'is_new_user' => 1, 'password' => $pass, 'created_by' => 1]);
                if(is_null($user->id_user)){
                    if($res = $user->save()){
                        $id = $user->id_user;
                        // try{
                            // Data profile
                            $profile = Profile::firstOrNew(['id_user' => $id], ['name' => $data['name'], 'personal_identity_path' => "asdfg.jpg", 'phone_number'=> $data['phone_number'], 'email' => $data['email']]);
                            if(is_null($profile->id_user_profile)){
                                if($res = $profile->save())
                                    $id_prof = $profile->id_user_profile;
                            }

                            // Data Company
                            if(isset($id_prof)){
                                $company = Company::firstOrNew(['id_user_profile' => $id_prof],['company_identity_path' => "hjaksdas.jpg", 'id_company' => $data['company']]);
                                if(is_null($company->id_user_company)){
                                    if($res = $company->save())
                                        $id_comp = $company->id_user_company;
                                }
                            }

                            // Data Authorization Company
                            if(isset($id) && isset($id_comp)){
                                $authCompany = AuthCompany::firstOrNew(['id_user' => $id],['id_company' => $data['company'], 'status' => 1, 'created_by' => 1]);
                                if(is_null($authCompany->id_authorization_company)){
                                    if($res = $authCompany->save())
                                        $id_auth = $authCompany->id_authorization_company;
                                }
                            }
                        // } catch(Exception $e){
                        //     return response()->json(Api::response(false,$e->getMessage()),$e->getCode());
                        // }
                    }

                    return response()->json(Api::response($res,$res?"user_created":"failed_user_created"),$res?200:400);
                }
                return response()->json(Api::response(false,"user_was_created"),400);
            } catch(Exception $e){
                return response()->json(Api::response(false,$e->getMessage()),$e->getCode());
            }
            return response()->json(Api::response(true,"ok"),400);
        }
        return response()->json(Api::response(false,Template::lang('failed')),400);
    }
}
