<?php

namespace App\Http\Controllers;

use Exception;
use Validator;
use Illuminate\Http\Request;
use App\Models\User\ProfileManagement AS Profile;
use App\Repositories\User\ProfileRepo as ProfileRepo;
use App\Helpers\Api;
use App\Helpers\Template;
use Illuminate\Hashing\BcryptHasher AS hash;

class ProfileController extends Controller
{ 

    /**
    * @SWG\Get(
    *     path="/profile/get",
    *     description="Get Profile",
    *     operationId="getprofile",
    *     produces={"application/json"},
    *     security={{"Bearer":{}}},
    *     @SWG\Response(
    *         response="200",
    *         description="successful"
    *     ),
    *     summary="Get Profile",
    *     tags={
    *         "Profile"
    *     }
    * )
    * */

    public function __construct(ProfileRepo $ProfileRepo)
    {
        $this->ProfileRepo = $ProfileRepo;
    }

    public function GetUserProfile(Request $request){
        try{ 
            $res = $this->ProfileRepo->getprofile($request->id_user);
            $Message = 'Berhasil';
        } catch(Exception $e) {
            $res = false;
            $Message = $e->getMessage();
            $code = 400;
        }
        return Response()->json(Api::response($res?true:false,$Message, $res?$res:[]),isset($code)?$code:200);
    }


    /**
    * @SWG\Get(
    *     path="/profile/generate-nik",
    *     description="Generate NIK",
    *     operationId="generate-nik",
    *     produces={"application/json"},
    *     security={{"Bearer":{}}},
    *     @SWG\Response(
    *         response="200",
    *         description="successful"
    *     ),
    *     summary="Generate NIK",
    *     tags={
    *         "Profile"
    *     }
    * )
    * */

     public function GenerateNIK(Request $request){
        try{ 
            $no_nik = $this->ProfileRepo->getnik();
            $kode =  array('nomor_NIK' => sprintf("%08s", $no_nik->NIK+1));
            $res  = $kode;
            $Message = 'Berhasil';
        } catch(Exception $e) {
            $res = false;
            $Message = $e->getMessage();
            $code = 400;
        }
        return Response()->json(Api::response($res?true:false,$Message, $res?$res:[]),isset($code)?$code:200);
    }

    /**
    * @SWG\Put(
    *     path="/profile/update",
    *     consumes={"multipart/form-data"},
    *     description="Update profile",
    *     operationId="profileUpdate",
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
    *         type="string"
    *     ),
    *     @SWG\Parameter(
    *         description="Date IN",
    *         in="formData",
    *         name="date_in",
    *         required=false,
    *         type="string"
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
    public function update(Response $r){
        $code = 200;
        $input = $r->only(['name','email','phone_number','identity_photo','personal_photo']);
        $profile = Profile::where('id_user',$r->id_user)->get();
        if($profile->count() > 0){
            $profile = $profile->first();

            if(!is_null($input['identity_photo']) && !empty($input['identity_photo'])){
                // upload Identity
                $identity_path = null;
                $blob = new BlobStorage;
                $blob::data([
                    'source' => $data['identity_photo'],
                    'path' => $id."/doc/"
                ]);
                if($res=$blob::upload()){
                    $profile->personal_identity_path = $res['data']['link'];
                }
            }

            if(!is_null($input['identity_photo']) && !empty($input['identity_photo'])){
                // upload Identity
                $personal_path = null;
                $blob = new BlobStorage;
                $blob::data([
                    'source' => $data['personal_photo'],
                    'path' => $id."/img/"
                ]);
                if($res=$blob::upload()){
                    $profile->personal_photo = $res['data']['link'];
                }
            }

            $profile->name = $input['name'];
            $profile->name = $input['company'];
            $profile->phone_number = $input['phone_number'];
            $profile->email = $input['email'];

            if(!$profile->save()){
                $code = 500;
            }
        }

        return response()->json(Api::response(($code==200),($code==200?"Sukses":"Gagal")),$code);
    }
}
