<?php

namespace App\Repositories\User;

use App\Models\User\AddressModel as AddressDB;
use App\Models\User\ProfileManagement as ProfileDB;
use Illuminate\Database\QueryException; 
use DB;

class ProfileRepo {

	public function getprofile($id_user = null){
		try { 
			return $users = ProfileDB::where('id_user', $id_user)
			->select('*')
			->get();
		}catch(QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
	}

	public function getnik(){
		try { 
			return DB::table('user.user_profile')
			->select(DB::raw('max(id_koperasi) as NIK'))
			->first();
		}catch(QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
	}

}
