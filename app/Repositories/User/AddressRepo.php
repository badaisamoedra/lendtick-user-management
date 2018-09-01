<?php

namespace App\Repositories\User;

use App\Models\User\AddressModel as AddressDB;
use Illuminate\Database\QueryException; 
use DB;

class AddressRepo {

	public function byuser($id_user = null){
		try { 
			return $users = DB::table('user.user_profile as UP')
            ->join('user.user as U', 'U.id_user', '=', 'UP.id_user')
            ->join('user.user_address as UA', 'UA.id_user_profile', '=', 'UP.id_user_profile')
            ->where('U.id_user', $id_user)
            ->select('UA.*')
            ->get();
		}catch(QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
	}
 
}
