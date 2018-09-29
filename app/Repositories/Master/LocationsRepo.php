<?php

namespace App\Repositories\Master;

use DB;
use App\Models\Master\LocationsMaster as LocationDB;
use Illuminate\Database\QueryException;

class LocationsRepo{
	
	public static function all(){
		try {
			// return $users = DB::connections('mongodb')->collection('location')->get();
			return DB::connection('mongodb')
			->collection('location')
			->where('lokasi_kabupatenkota','00')
			->where('lokasi_kecamatan','00')
			->where('lokasi_kelurahan','0000')
			->orderBy('lokasi_nama')
			->get();
			// if($columns == array('*')) return LocationDB::all();
			// else return LocationDB::select($columns)->get();
		}catch(QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
	}

	public static function get_city_by_province($kode = null){
		try {
			return DB::connection('mongodb')
			->collection('location')
			->where('lokasi_provinsi',$kode)
			->where('lokasi_kecamatan','00')
			->where('lokasi_kelurahan','0000')
			->where('lokasi_kabupatenkota', '!=' ,'00')
			->orderBy('lokasi_nama')
			->get(); 
			
		}catch(QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
	}



	public static function get_kec_by_city_and_province($propinsi_kode = null , $kabkota_kode = null){
		try {
			return DB::connection('mongodb')
            ->collection('location')
            ->where('lokasi_provinsi',$propinsi_kode)
            ->where('lokasi_kecamatan','!=','00')
            ->where('lokasi_kelurahan','0000')
            ->where('lokasi_kabupatenkota',$kabkota_kode)
            ->orderBy('lokasi_nama')
            ->get(); 
			
		}catch(QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
	}


	public static function get_kel_by_city_and_province_and_kecamatan($propinsi_kode = null , $kabkota_kode = null, $kec_kode = null){
		try {
			return DB::connection('mongodb')
            ->collection('location')
            ->where('lokasi_provinsi',$propinsi_kode)
            ->where('lokasi_kecamatan',$kec_kode)
            ->where('lokasi_kelurahan','!=','0000')
            ->where('lokasi_kabupatenkota',$kabkota_kode)
            ->orderBy('lokasi_nama')
            ->get(); 
			
		}catch(QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
	}


	

}
