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

	// Update Address of User
	public function update_byuser($id_user = null , $id_user_address = null , $value_update = array()){
		try { 
			if (is_array($value_update)) {
				return AddressDB::join('user.user_profile as UP', 'UP.id_user_profile', '=', 'user_address.id_user_profile')
				->join('user.user as U', 'U.id_user', '=', 'UP.id_user')
				->where('id_user_address', $id_user_address)
				->where('U.id_user',$id_user)
				->update($value_update);
			} else {
				throw new \Exception("Kesalahan pada proses permintaan", 400);
			}
			
		}catch(QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
	}

	// Create Address of User
	public function create_byuser($id_user = null , $value_insert = array()){
		try { 
			if (is_array($value_insert)) {
				// search id profile first
				$users = DB::table('user.user_profile as UP')
							->join('user.user as U', 'U.id_user', '=', 'UP.id_user')
							->where('U.id_user',$id_user)
							->select('UP.id_user_profile')
							->first();
				
				$id_user_profile = array(
					'id_user_profile' 	=> $users->id_user_profile,
					'created_by'		=> $users->id_user_profile
				);

				return AddressDB::create(array_merge($value_insert,$id_user_profile)); 

			} else {
				throw new \Exception("Kesalahan pada proses permintaan", 400);
			}
			
		}catch(QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
	}

}
