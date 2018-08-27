<?php

namespace App\Http\Controllers;

use Exception;
use Validator;
use Illuminate\Http\Request;
use App\Repositories\Auth\ConfigureRepo as ConfigurationRepo;
use App\Helpers\Api;
use App\Helpers\Template;
use Illuminate\Hashing\BcryptHasher AS hash;

class ChangePasswordController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     * php artisan swagger-lume:generate
     */

    /**
    * @SWG\Put(
    *     path="/change-password", 
    *     description="Upload content to azure blob storage",
    *     operationId="auth",
    *     consumes={"application/json"},
    *     produces={"application/json"}, 
    *     @SWG\Parameter(
    *         description="Username",
    *         in="query",
    *         name="username",
    *         required=true,
    *         type="string"
    *     ), 
     *     @SWG\Parameter(
    *         description="old_password",
    *         in="query",
    *         name="old_password",
    *         required=true,
    *         type="string"
    *     ), 
     *     @SWG\Parameter(
    *         description="new_password",
    *         in="query",
    *         name="new_password",
    *         required=true,
    *         type="string"
    *     ), 
    *     @SWG\Response(
    *         response="200",
    *         description="successful"
    *     ),
    *     summary="Configuration",
    *     tags={
    *         "Configuration"
    *     }
    * )
    * */

    public function __construct(){
    } 

    public function get(Request $request, hash $hash){



        try{

            $this->validate($request, [
                'username'          => 'required',
                'old_password'      => 'required', 
                'new_password'      => 'required|max:8'
            ]);  
            
            $check_pass = ($data = ConfigurationRepo::search_auth($request->username)) ? $hash->check($request->old_password, $data->password) : false;


            if ($check_pass) {
                // mulai mengganti password dengan password baru. 
               $run_change_pass = ConfigurationRepo::change_password($data->id_user,  $request->username , $hash->make($request->new_password));

               if ($run_change_pass) {
                    $res = [];
               } else { 
                    throw New Exception("Ada kesalahan", 400);
               }
                //$request->new_password;
                // $pass = $h->make('kop2018');
            } else {
                throw New Exception("Password lama tidak sesuai", 400); 
            }

            $Message = 'Berhasil';
        } catch(Exception $e) {
            $res = false;
            $Message = $e->getMessage();
            $code = 400;
        }
        return Response()->json(Api::response($res?true:false,$Message, $res?$res:[]),isset($code)?$code:200);
    }
}
