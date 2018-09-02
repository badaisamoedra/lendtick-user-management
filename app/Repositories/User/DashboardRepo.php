<?php

namespace App\Repositories\User;

use App\Models\User\UserManagement as UserManagementDB;
use Illuminate\Database\QueryException; 
use DB;

class DashboardRepo {

	public function gettotal($where = null , $as = null){
		try { 
			return DB::table('user.user')
                     ->select(DB::raw('count(id_workflow_status) as '.$as))
                     ->where('id_workflow_status', '=', $where)
                     ->get();
		}catch(QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
	}  
}
