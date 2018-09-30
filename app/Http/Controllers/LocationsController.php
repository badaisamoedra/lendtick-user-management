<?php

namespace App\Http\Controllers;

use DB;
use Exception;
use Validator;
use Illuminate\Http\Request;
use App\Repositories\Master\LocationsRepo as LocationsRepo;
use App\Helpers\Api;
use App\Helpers\Template;
use Illuminate\Hashing\BcryptHasher AS hash;
use App\Models\Master\LocationsMaster as LocationDB;

class LocationsController extends Controller
{  

    

    public function __construct(LocationsRepo $LocationsRepo)
    {
        $this->LocationsRepo = $LocationsRepo;
    }

    /**
    * @SWG\Get(
    *     path="/all-province",
    *     description="Get All Propinsi",
    *     operationId="GetAllProvince",
    *     produces={"application/json"}, 
    *     @SWG\Response(
    *         response="200",
    *         description="successful"
    *     ),
    *     summary="Get All Propinsi",
    *     tags={
    *         "Locations"
    *     }
    * )
    * */

    public function GetAllProvince(Request $request){
        try {  
            $res =  $this->LocationsRepo->all(); 
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
    *     path="/get-city-by-province",
    *     description="Get City By Propinsi",
    *     operationId="GetCityByProvince",
    *     produces={"application/json"}, 
    *     @SWG\Parameter(
    *         description="Kode Provinsi ",
    *         in="query",
    *         name="propinsi_kode",
    *         required=true,
    *         type="string"
    *     ), 
    *     @SWG\Response(
    *         response="200",
    *         description="successful"
    *     ),
    *     summary="Get City By Propinsi",
    *     tags={
    *         "Locations"
    *     }
    * )
    * */


    // get city by id 
    public function GetCityByProvince(Request $request){
        try{   
             $this->validate($request, [
                'propinsi_kode'  => 'required',
            ]);

            $kode = $request->propinsi_kode;
            $res = $this->LocationsRepo->get_city_by_province($kode);
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
    *     path="/get-kec-by-province-city",
    *     description="Get Kecamatan By City and Provinsi",
    *     operationId="GetKecamatanByCityAndProvince",
    *     produces={"application/json"},
    *     @SWG\Parameter(
    *         description="Kode Provinsi ",
    *         in="query",
    *         name="propinsi_kode",
    *         required=true,
    *         type="string"
    *     ), 
     *     @SWG\Parameter(
    *         description="Kode Kabkota ",
    *         in="query",
    *         name="kabkota_kode",
    *         required=true,
    *         type="string"
    *     ), 
    *     @SWG\Response(
    *         response="200",
    *         description="successful"
    *     ),
    *     summary="Get Kecamatan By City and Provinsi",
    *     tags={
    *         "Locations"
    *     }
    * )
    * */
 
    // get kecamatan by id 
    public function GetKecamatanByCityAndProvince(Request $request){
        try{   
             $this->validate($request, [
                'propinsi_kode'  => 'required',
                'kabkota_kode'  => 'required',
            ]);

            $propinsi_kode = $request->propinsi_kode;
            $kabkota_kode = $request->kabkota_kode;

            $res = $this->LocationsRepo->get_kec_by_city_and_province($propinsi_kode,$kabkota_kode);
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
    *     path="/get-kel-by-province-city-kec",
    *     description="Get Kelurahan By City and Provinsi and Kecamatan",
    *     operationId="GetKelurahanByCityAndProvinceAndKecamatan",
    *     produces={"application/json"}, 
    *     @SWG\Parameter(
    *         description="Kode Provinsi ",
    *         in="query",
    *         name="propinsi_kode",
    *         required=true,
    *         type="string"
    *     ), 
     *     @SWG\Parameter(
    *         description="Kode Kabkota ",
    *         in="query",
    *         name="kabkota_kode",
    *         required=true,
    *         type="string"
    *     ), 
    *     @SWG\Parameter(
    *         description="Kode Kecamatan ",
    *         in="query",
    *         name="kec_kode",
    *         required=true,
    *         type="string"
    *     ), 
    *     @SWG\Response(
    *         response="200",
    *         description="successful"
    *     ),
    *     summary="Get Kelurahan By City and Provinsi and Kecamatan",
    *     tags={
    *         "Locations"
    *     }
    * )
    * */

    // get kelurahan by id 
    public function GetKelurahanByCityAndProvinceAndKecamatan(Request $request){
        try{   
             $this->validate($request, [
                'propinsi_kode'  => 'required',
                'kabkota_kode'  => 'required',
                'kec_kode'  => 'required',
            ]);

            $propinsi_kode = $request->propinsi_kode;
            $kabkota_kode = $request->kabkota_kode;
            $kec_kode = $request->kec_kode;

            $res = $this->LocationsRepo->get_kel_by_city_and_province_and_kecamatan($propinsi_kode,$kabkota_kode,$kec_kode);
            $Message = 'Berhasil';
        } catch(Exception $e) {
            $res = false;
            $Message = $e->getMessage();
            $code = 400;
        }
        return Response()->json(Api::response($res?true:false,$Message, $res?$res:[]),isset($code)?$code:200);
    }


}
