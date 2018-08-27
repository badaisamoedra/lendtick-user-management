<?php

namespace App\Repositories\Auth;

use App\Models\User\UserManagement as UserManagement;
use Illuminate\Database\QueryException; 
use DB;

class ConfigureRepo{ 

	public static function search_auth($username)
	{
		try {

			$Check =  UserManagement::where('username',$username)->where('deleted_by')->first();

			if (count($Check)>0) {
				return $Check;
			} else {
				throw new \Exception('Username tidak ditemukan', 400); 
			}
			
		} catch (QueryException $e) {
			throw new \Exception($e->getMessage(), 500);
		}
	}

	public static function change_password($id_user , $username,$new_password)
	{
		try {

			if (!empty($username) and !empty($new_password)) {
				UserManagement::where('username',$username)
								->where('id_user',$id_user)
								->update(['password' => $new_password]);
				return true;
			} else {
				throw new \Exception('Ada kesalahan silahkan dicoba kembali.', 400); 
			}
			
		} catch (QueryException $e) {
			throw new \Exception($e->getMessage(), 500);
		}
	} 
}
