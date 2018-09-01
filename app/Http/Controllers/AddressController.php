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
    *     summary="Get List Address of User",
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

    public function UpdateAddressOfUser(Request $request){
        try{ 

            if(empty($request->json())) throw New \Exception('Params not found', 500);

            $this->validate($request, [
                'id_user_address'           => 'required',
                'address_name'              => 'required', 
                'receiver_name'             => 'required',
                'address_text'              => 'required',
                'city_or_district'          => 'required',
                'postal_code'               => 'required',
                'receiver_phone'            => 'required',
                'address_latitude'          => 'required',
                'address_longitude'         => 'required',
                'address_latlong_text'      => 'required',
                'receiver_phone'            => 'required',
                'address_latitude'          => 'required',
                'address_longitude'         => 'required',
                'address_latlong_text'      => 'required'

            ]);  

            $value_update = [
                'address_name'              => $request->address_name,
                'receiver_name'             => $request->receiver_name,
                'address_text'              => $request->address_text,
                'city_or_district'          => $request->city_or_district,
                'postal_code'               => $request->postal_code,
                'address_longitude'         => $request->address_longitude,
                'address_latlong_text'      => $request->address_latlong_text,
                'receiver_phone'            => $request->receiver_phone,
                'address_latitude'          => $request->address_latitude,
                'address_longitude'         => $request->address_longitude,
                'address_latlong_text'      => $request->address_latlong_text

            ];

            $id_user = $request->id_user; // from JWT Token Middleware
            $res = $this->AddressRepo->update_byuser($id_user , $request->id_user_address, $value_update);
            $Message = 'Berhasil';
        } catch(Exception $e) {
            $res = false;
            $Message = $e->getMessage();
            $code = 400;
        }
        return Response()->json(Api::response($res?true:false,$Message, $res?$res:[]),isset($code)?$code:200);
    }

    // Create Address Of User
     public function CreateAddressOfUser(Request $request){
        try{ 

            if(empty($request->json())) throw New \Exception('Params not found', 500);

            $this->validate($request, [
                'address_name'              => 'required', 
                'receiver_name'             => 'required',
                'address_text'              => 'required',
                'city_or_district'          => 'required',
                'postal_code'               => 'required',
                'receiver_phone'            => 'required',
                'address_latitude'          => 'required',
                'address_longitude'         => 'required',
                'address_latlong_text'      => 'required',
                'receiver_phone'            => 'required',
                'address_latitude'          => 'required',
                'address_longitude'         => 'required',
                'address_latlong_text'      => 'required'

            ]);  

            $value_insert = [
                'address_name'              => $request->address_name,
                'receiver_name'             => $request->receiver_name,
                'address_text'              => $request->address_text,
                'city_or_district'          => $request->city_or_district,
                'postal_code'               => $request->postal_code,
                'address_longitude'         => $request->address_longitude,
                'address_latlong_text'      => $request->address_latlong_text,
                'receiver_phone'            => $request->receiver_phone,
                'address_latitude'          => $request->address_latitude,
                'address_longitude'         => $request->address_longitude,
                'address_latlong_text'      => $request->address_latlong_text,
                'is_main_address'           => 0                


            ];

            $id_user = $request->id_user; // from JWT Token Middleware
            $res = $this->AddressRepo->create_byuser($id_user , $value_insert);
            $Message = 'Berhasil';
        } catch(Exception $e) {
            $res = false;
            $Message = $e->getMessage();
            $code = 400;
        }
        return Response()->json(Api::response($res?true:false,$Message, $res?$res:[]),isset($code)?$code:200);
    }


    /// delete address of user 
     public function DeleteAddressOfUser(Request $request){
        try{ 
            if(empty($request->json())) throw New \Exception('Params not found', 500);

            $this->validate($request, [
                'id_user_address'       => 'required'
            ]);  

            $id_user = $request->id_user; // from JWT Token Middleware
            $res = $this->AddressRepo->delete_byuser($id_user , $request->id_user_address);
            $Message = 'Berhasil';
        } catch(Exception $e) {
            $res = false;
            $Message = $e->getMessage();
            $code = 400;
        }
        return Response()->json(Api::response($res?true:false,$Message, $res?$res:[]),isset($code)?$code:200);
    }

    

}
