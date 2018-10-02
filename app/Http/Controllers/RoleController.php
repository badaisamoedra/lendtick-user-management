<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Master\RoleMaster AS MstRole;
use App\Helpers\Api;

class RoleController extends Controller
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
    *     path="/mst/role",
    *     consumes={"multipart/form-data"},
    *     description="List Role",
    *     operationId="rolelist",
    *     consumes={"application/x-www-form-urlencoded"},
    *     produces={"application/json"},
    *     security={{"Bearer":{}}},
    *     @SWG\Response(
    *         response="200",
    *         description="successful"
    *     ),
    *     summary="List Role Master",
    *     tags={
    *         "Master"
    *     }
    * )
    * */
    public function list(Request $r){
        $grd = MstRole::where('status',1)->get();
        $code = 200;
        return response()->json(Api::response(true, $code, $grd) ,$code);
    }
}
