<?php

namespace App\Http\Controllers;

use Exception;
use Validator;
use Illuminate\Http\Request;
use App\Repositories\User\AddressRepo as AddressRepo;
use App\Helpers\Api;
use App\Helpers\Template;
use Illuminate\Hashing\BcryptHasher AS hash;

class AddressController extends Controller
{ 

    /**
    * @SWG\Get(
    *     path="/web/svc-user/public/profile/get-address-by-user",
    *     description="Get User Address of User",
    *     operationId="Address",
    *     produces={"application/json"},
    *     security={{"Bearer":{}}},
    *     @SWG\Response(
    *         response="200",
    *         description="successful"
    *     ),
    *     summary="Refresh Authentication",
    *     tags={
    *         "Profile"
    *     }
    * )
    * */

    public function __construct(AddressRepo $AddressRepo)
    {
        $this->AddressRepo = $AddressRepo;
    }

    public function GetListAddressOfUser(Request $request){
        try{ 
            $res = $this->AddressRepo->byuser($request->id_user);
            $Message = 'Berhasil';
        } catch(Exception $e) {
            $res = false;
            $Message = $e->getMessage();
            $code = 400;
        }
        return Response()->json(Api::response($res?true:false,$Message, $res?$res:[]),isset($code)?$code:200);
    }
}
