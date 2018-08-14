<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Repositories\Master\CompanyRepo as Company;
use App\Helpers\Api;
use App\Helpers\Template;

class CompanyController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){
    }


    /**
    * @SWG\Get(
    *     path="/company/get",
    *     description="Get Company List",
    *     operationId="company",
    *     @SWG\Response(
    *         response="200",
    *         description="successful"
    *     ),
    *     summary="Get list Company",
    *     tags={
    *         "Company"
    *     }
    * )
    * */
    public function get(){
        try{
            $res = Company::all(["id_company", "name_company"]);
        } catch(Exception $e) {
            $res = false;
            $code = $e->getCode();
        }
        return Response()->json(Api::response($res?true:false,"List Company", $res?$res:[]),isset($code)?$code:200);
    }
}
