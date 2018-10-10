<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User\UserManagement AS User;
use App\Models\User\ProfileManagement AS Profile;
use App\Models\User\CompanyManagement AS Company;
use App\Models\User\AuthCompanyManagement AS AuthCompany;
use App\Models\User\RegisterMemberFlowManagement AS RegisterFlow;
use App\Models\Master\RoleMaster AS Role;
use App\Models\Master\WorkflowMaster AS Workflow;
use App\Models\Master\RegisterMemberFlowMaster AS MstRegisterFlow;
use App\Models\Master\GradeMaster AS MstGrade;
use App\Repositories\User\RegisterMemberFlowRepo AS RegisterFlowRepo;
use App\Repositories\User\ProfileRepo as ProfileRepo;
use Illuminate\Hashing\BcryptHasher AS Hash;
use App\Helpers\Api;
use App\Helpers\Template;
use App\Helpers\BlobStorage;
use App\Helpers\RestCurl;

class UsersController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){
        // contruct mode
    }

    public function __destruct(){
        // destruct mode
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
    *         description="Company code from list",
    *         in="formData",
    *         name="company",
    *         required=true,
    *         type="string",
    *         default="COMP001"
    *     ),
    *     @SWG\Parameter(
    *         description="Role id",
    *         in="formData",
    *         name="role",
    *         required=false,
    *         type="string",
    *         default="ROLE001"
    *     ),
    *     @SWG\Parameter(
    *         description="Name",
    *         in="formData",
    *         name="name",
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

        $data = $r->only(['name','company','identity_photo','company_identity_photo','phone_number','email','personal_photo','role']);
        if(count($data) >= 7){
            try{
                // validate role insert
                $tmp = Role::where('status', 1);

                if(isset($data['role']))
                    $tmp->where('id_role_master', $data['role']);
                else
                    $tmp->where('is_front_end', 1);
                // get role data
                $tmp->get();
                // get role id
                $role = ($tmp->count() > 0)?$tmp->first()->id_role_master:"ROLE001";
                // flush variable tmp
                unset($tmp);

                // Data user
                $user = User::firstOrNew(['username' => $data['email']],['id_role_master' => $role, 'id_workflow_status' => 'EMPSTS01', 'is_new_user' => 1, 'password' => $pass, 'created_by' => 1]);
                if(is_null($user->id_user)){
                    if($res = $user->save()){
                        $id = $user->id_user;

                        // upload Identity
                        $identity_path = null;
                        $blob = new BlobStorage;
                        $blob::data([
                            'source' => $data['identity_photo'],
                            'path' => $id."/doc/"
                        ]);
                        if($res=$blob::upload())
                            $identity_path = $res['data']['link'];

                        // upload Identity
                        $personal_path = null;
                        $blob = new BlobStorage;
                        $blob::data([
                            'source' => $data['personal_photo'],
                            'path' => $id."/img/"
                        ]);
                        if($res=$blob::upload())
                            $personal_path = $res['data']['link'];

                        // try{
                            // Data profile
                            $profile = Profile::firstOrNew(['id_user' => $id], ['name' => $data['name'], 'personal_identity_path' => $identity_path, 'phone_number'=> $data['phone_number'], 'email' => $data['email'], 'personal_photo' => $personal_path]);
                            if(is_null($profile->id_user_profile)){
                                if($res = $profile->save())
                                    $id_prof = $profile->id_user_profile;
                            }


                        $company_path = null;
                        $blob = new BlobStorage;
                        $blob::data([
                            'source' => $data['company_identity_photo'],
                            'path' => $id."/doc/"
                        ]);
                        if($res=$blob::upload())
                            $company_path = $res['data']['link'];

                            // Data Company
                            if(isset($id_prof)){
                                $company = Company::firstOrNew(['id_user_profile' => $id_prof],['company_identity_path' => $company_path, 'id_company' => $data['company'], 'id_grade' => 'GRD000', 'id_workflow_status' => 'EMPSTS01']);
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

                            if($role == "ROLE001"){
                                // Data working approval overflow
                                if(isset($id)){
                                    $cnt = RegisterFlow::where('id_user','=',$id)->get()->count();
                                    if($cnt == 0){
                                        RegisterFlowRepo::flow($id);
                                    }
                                }
                            }
                        // } catch(Exception $e){
                        //     return response()->json(Api::response(false,$e->getMessage()),$e->getCode());
                        // }

                        // send to notification
                        $email = [
                            "to"=> $user->username,
                            "cc"=> "",
                            "subject"=> "Register",
                            "body"=> Template::email('register'),
                            "type"=> "email",
                            "attachment"=> ""
                        ];
                        $res_email = RestCurl::post(env('LINK_NOTIF','https://lentick-api-notification-dev.azurewebsites.net')."/send", $email);

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

    /**
    * @SWG\Put(
    *     path="/user/approve",
    *     consumes={"multipart/form-data"},
    *     description="Register Lendtick",
    *     operationId="aprroveUser",
    *     consumes={"application/x-www-form-urlencoded"},
    *     produces={"application/json"},
    *     security={{"Bearer":{}}},
    *     @SWG\Parameter(
    *         description="ID user",
    *         in="formData",
    *         name="id",
    *         required=true,
    *         type="string"
    *     ),
    *     @SWG\Parameter(
    *         description="Grade",
    *         in="formData",
    *         name="grade",
    *         required=false,
    *         type="string",
    *         default="GRD001"
    *     ),
    *     @SWG\Parameter(
    *         description="Date IN",
    *         in="formData",
    *         name="date_in",
    *         required=false,
    *         type="string",
    *         default="2018-12-31"
    *     ),
    *     @SWG\Response(
    *         response="200",
    *         description="successful"
    *     ),
    *     summary="Approve User",
    *     tags={
    *         "Flow Approval"
    *     }
    * )
    * */
    public function approve(Request $r, ProfileRepo $Pr){
        $id = $r->input('id');
        $grade = $r->input('grade');
        $in = $r->input('date_in');
        $code = 200;
        $mst_flow = MstRegisterFlow::where('id_role_master', $r->id_role_master)->get()->first();
        $data = RegisterFlow::where('id_user','=',$id)->where('id_master_register_member_flow', '=', $mst_flow->id_master_register_member_flow)->get()->first();
        $data->approve_at = date("Y-m-d H:i:s");
        if($data->save()){
            // last level
            $last_level = ($data->level == MstRegisterFlow::orderBy('level','desc')->first()->level);
            // change user to lendtick number
            $user = User::where('id_user',$data->id_user)->get()->first();
            // $user->id_workflow_status = $mst_flow->set_workflow_status_code;

            // change user
            if($last_level){
                $user->username = $Pr->GenerateNik()["nomor_NIK"]; // autogenerate by code
                $user->save();
            }

            $profile = Profile::where('id_user', $user->id_user)->get()->first();

            // change grade
            if(!empty($grade) && !empty($in) && !is_null($grade) && !is_null($in)){
                $comp = Company::where('id_user_profile', $profile->id_user_profile)->get()->first();
                $comp->id_workflow_status = $mst_flow->set_workflow_status_code;
                if($grade && $in){
                    $comp->id_grade = $grade;
                    $comp->employee_starting_date = date("Y-m-d",strtotime($in));
                }
                $comp->save();

                // change data loan plafond
                $profile->loan_plafond = 1000;
                $profile->microloan_plafond = MstGrade::where('id_grade',$grade)->get()->first()->microloan_amount;
                $profile->save();
            }

            // check level for last approval
            if($last_level){
                // update id_koperasi
                $profile->id_koperasi = $user->username;
                $profile->save();

                // send email
                $email = [
                    "to"=> $profile->email,
                    "cc"=> "",
                    "subject"=> "Register",
                    "body"=> 'username : '.$user->username."\n\nPassword : kop2018",
                    "type"=> "email",
                    "attachment"=> ""
                ];
                RestCurl::post(env('LINK_NOTIF','https://lentick-api-notification-dev.azurewebsites.net')."/send", $email);
            } else {
                // get next level
                $data_next = RegisterFlow::where('id_user','=',$id)->where('level', '=', ((int)$data->level+1))->get();
                if($data_next->count() > 0){
                    $data_next = $data_next->first();
                    $next_profile = $profile = Profile::where('id_user', $data_next->approve_by)->get()->first();

                    $email = [
                        "to"=> $next_profile->email,
                        "cc"=> "",
                        "subject"=> "Register",
                        "body"=> 'Ada permintaan approval untuk orang bernama : '.$profile->name,
                        "type"=> "email",
                        "attachment"=> ""
                    ];
                    RestCurl::post(env('LINK_NOTIF','https://lentick-api-notification-dev.azurewebsites.net')."/send", $email);
                }
            }
        } else {
            $code = 400;
        }
        return response()->json(Api::response(true,$code==400?Template::lang('failed'):Template::lang('success')),$code);
    }

    /**
    * @SWG\Put(
    *     path="/user/reject",
    *     consumes={"multipart/form-data"},
    *     description="Reject Request",
    *     operationId="rejectUser",
    *     consumes={"application/x-www-form-urlencoded"},
    *     produces={"application/json"},
    *     security={{"Bearer":{}}},
    *     @SWG\Parameter(
    *         description="ID user",
    *         in="formData",
    *         name="id",
    *         required=true,
    *         type="string"
    *     ),
    *     @SWG\Response(
    *         response="200",
    *         description="successful"
    *     ),
    *     @SWG\Response(
    *         response="400",
    *         description="failed"
    *     ),
    *     summary="Reject User",
    *     tags={
    *         "Flow Approval"
    *     }
    * )
    * */
    public function reject(Request $r){
        $id = $r->input('id');
        if($id){
            $user = User::where('id_user','=',$id)->get()->first();
            $user->id_workflow_status = 'EMPSTS00';
            if($res = $user->save()){
                $profile = Profile::where('id_user','=',$user->id_user)->get()->first();
                $role = Role::where('id_role_master', $r->id_role_master)->get()->first();
                $email = [
                    "to"=> $profile->email,
                    "cc"=> "",
                    "subject"=> "Reject Request From ".$role->name_role_master,
                    "body"=> 'Pengajuan anda di tolak oleh '.$role->name_role_master,
                    "type"=> "email",
                    "attachment"=> ""
                ];
                RestCurl::post(env('LINK_NOTIF','https://lentick-api-notification-dev.azurewebsites.net')."/send", $email);
                return response()->json(Api::response(true,Template::lang('success')),200);
            }
            return response()->json(Api::response(false,Template::lang('failed')),400);
        }
        return response()->json(Api::response(false,Template::lang('failed')),400);
    }
    
    /**
    * @SWG\Get(
    *     path="/user/approve/list",
    *     consumes={"multipart/form-data"},
    *     description="Approval list for BE",
    *     operationId="approvelist",
    *     consumes={"application/x-www-form-urlencoded"},
    *     produces={"application/json"},
    *     security={{"Bearer":{}}},
    *     @SWG\Parameter(
    *         description="Start row",
    *         in="query",
    *         name="start",
    *         required=true,
    *         type="string",
    *         default="0"
    *     ),
    *     @SWG\Parameter(
    *         description="Length of get row",
    *         in="query",
    *         name="length",
    *         required=true,
    *         type="string",
    *         default="10"
    *     ),
    *     @SWG\Parameter(
    *         description="Sort by",
    *         in="query",
    *         name="sort",
    *         required=true,
    *         type="string",
    *         default="name,asc"
    *     ),
    *     @SWG\Parameter(
    *         description="Filter column",
    *         in="query",
    *         name="filter",
    *         required=false,
    *         type="string"
    *     ),
    *     @SWG\Response(
    *         response="200",
    *         description="successful"
    *     ),
    *     summary="List Approve User",
    *     tags={
    *         "Flow Approval"
    *     }
    * )
    * */
    public function approve_list(Request $r){
        $start = $r->input('start');
        $length = $r->input('length');
        $sort = (array) json_decode($r->input('sort'));
        $where = $r->input('filter');
        return response()->json(Api::response(true,Template::lang('success'),RegisterFlowRepo::approve_list($r, $where, $start, $length, $sort)),200);
    }
}
