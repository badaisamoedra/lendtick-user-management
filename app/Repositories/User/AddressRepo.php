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
			->where('UA.status',1)
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
					'created_by'		=> $users->id_user_profile,
					'status'			=> 1 // aktif karena baru dibuat
				);

				return AddressDB::create(array_merge($value_insert,$id_user_profile)); 

			} else {
				throw new \Exception("Kesalahan pada proses permintaan", 400);
			}
			
		}catch(QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
	}

	// delete address of user
	public function delete_byuser($id_user = null , $id_user_address =  null){
		try { 
			if (!empty($id_user)) {
				// search id profile first
				$users = DB::table('user.user_profile as UP')
				->join('user.user as U', 'U.id_user', '=', 'UP.id_user')
				->where('U.id_user',$id_user)
				->select('UP.id_user_profile')
				->first();

				// cek apakah yang dihapus merupakan main address, jika iya tidak diperbolehkan
				$address = DB::table('user.user_address')
				->select('is_main_address','id_user_address')
				->where('id_user_address', $id_user_address)
				->where('id_user_profile',$users->id_user_profile)
				->first();

				if (is_null($address)) throw new \Exception("Tidak ditemukan alamat", 400);

				if ($address->is_main_address != 1) {

					AddressDB::withTrashed()
					->where('id_user_address', $id_user_address)
					->where('user_address.id_user_profile',$users->id_user_profile)
					->delete();

					return AddressDB::withTrashed()
					->where('id_user_address', $id_user_address)
					->where('user_address.id_user_profile',$users->id_user_profile)
					->update(['status' => 0 ]);

				} else {
					throw new \Exception("Alamat yang dihapus adalah alamat inti dari akun kamu, silahkan jadikan alamat ini menjadi bukan dari alamat inti agar dapat menghapus alamat ini.", 400);
				} 

			} else {
				throw new \Exception("Kesalahan pada proses permintaan", 400);
			}
			
		}catch(QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
	}

	// $this->AddressRepo->delete_byuser($request->id_user , $request->id_user_address)

}
