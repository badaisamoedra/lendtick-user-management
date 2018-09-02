<?php

namespace App\Http\Controllers;

use Exception;
use Validator;
use Illuminate\Http\Request;
use App\Repositories\User\DashboardRepo as DashboardRepo;
use App\Helpers\Api;
use App\Helpers\Template;
use Illuminate\Hashing\BcryptHasher AS hash;

class DashboardController extends Controller
{ 

    /**
    * @SWG\Get(
    *     path="/dashboard/get-completed-by-sbu-hr",
    *     description="Get Total User Completed By SBUHR",
    *     operationId="TotalUserCompletedBySBUHR",
    *     produces={"application/json"},
    *     security={{"Bearer":{}}},
    *     @SWG\Response(
    *         response="200",
    *         description="successful"
    *     ),
    *     summary="Get Total User Completed By SBUHR",
    *     tags={
    *         "Dashboard"
    *     }
    * )
    * */

    public function __construct(DashboardRepo $DashboardRepo)
    {
        $this->DashboardRepo = $DashboardRepo;
    }

    // T40
    public function TotalUserCompletedBySBUHR(){
        try{ 
            $res = $this->DashboardRepo->gettotal('MBRSTS01' ,'total_user_completed_by_sbu_hr');
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
    *     path="/dashboard/get-completed-by-kopadmin",
    *     description="Get Total User Completed By Kop Admin",
    *     operationId="TotalUserCompletedByKopAdmin",
    *     produces={"application/json"},
    *     security={{"Bearer":{}}},
    *     @SWG\Response(
    *         response="200",
    *         description="successful"
    *     ),
    *     summary="Get Total User Completed By Kop Admin",
    *     tags={
    *         "Dashboard"
    *     }
    * )
    * */

    // T33
    public function TotalUserCompletedByKopAdmin(){
        try{ 
            $res = $this->DashboardRepo->gettotal('MBRSTS02' ,'total_user_completed_by_kopadmin');
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
    *     path="/dashboard/get-pending-by-sbu-hr",
    *     description="Get Total User Pending By SBU HR",
    *     operationId="TotalUserPendingBySBUHR",
    *     produces={"application/json"},
    *     security={{"Bearer":{}}},
    *     @SWG\Response(
    *         response="200",
    *         description="successful"
    *     ),
    *     summary="Get Total User Pending By SBU HR",
    *     tags={
    *         "Dashboard"
    *     }
    * )
    * */

    // T38
    public function TotalUserPendingBySBUHR(){
        try{ 
            $res = $this->DashboardRepo->gettotal('MBRSTS00' ,'total_user_pending_by_sbu_hr');
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
    *     path="/dashboard/get-pending-by-kopadmin",
    *     description="Get Total User Pending By Koperasi Admin",
    *     operationId="TotalUserPendingByKoperasiAdmin",
    *     produces={"application/json"},
    *     security={{"Bearer":{}}},
    *     @SWG\Response(
    *         response="200",
    *         description="successful"
    *     ),
    *     summary="Get Total User Pending By Koperasi Admin",
    *     tags={
    *         "Dashboard"
    *     }
    * )
    * */

    // T31
    public function TotalUserPendingByKoperasiAdmin(){
        try{ 
            $res = $this->DashboardRepo->gettotal('MBRSTS01' ,'total_user_pending_by_kopadmin');
            $Message = 'Berhasil';
        } catch(Exception $e) {
            $res = false;
            $Message = $e->getMessage();
            $code = 400;
        }
        return Response()->json(Api::response($res?true:false,$Message, $res?$res:[]),isset($code)?$code:200);
    }  
}
