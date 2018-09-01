<?php

namespace App\Http\Controllers;

use Exception;
use Validator;
use Illuminate\Http\Request;
use App\Repositories\User\ProfileRepo as ProfileRepo;
use App\Helpers\Api;
use App\Helpers\Template;
use Illuminate\Hashing\BcryptHasher AS hash;

class ProfileController extends Controller
{ 

    /**
    * @SWG\Get(
    *     path="/web/svc-user/public/profile/get",
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
}
