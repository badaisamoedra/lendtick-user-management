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

	public function GenerateNik(){
		return array('nomor_NIK' => sprintf("%08s", (($res = $this->getnik())?(!is_null($res->NIK)?(int)$res->NIK:0):0)+1));
	}

}
