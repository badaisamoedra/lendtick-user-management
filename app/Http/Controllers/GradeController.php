<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Master\GradeMaster AS MstGrade;
use Illuminate\Hashing\BcryptHasher AS Hash;
use App\Helpers\Api;

class GradeController extends Controller
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
    *     path="/mst/grade",
    *     consumes={"multipart/form-data"},
    *     description="list grade",
    *     operationId="gradelist",
    *     consumes={"application/x-www-form-urlencoded"},
    *     produces={"application/json"},
    *     security={{"Bearer":{}}},
    *     @SWG\Response(
    *         response="200",
    *         description="successful"
    *     ),
    *     summary="List Grade Master",
    *     tags={
    *         "Master"
    *     }
    * )
    * */
    public function list(Request $r){
        $grd = MstGrade::all();
        $code = 200;
        return response()->json(Api::response(true, $code, $grd) ,$code);
    }
}
