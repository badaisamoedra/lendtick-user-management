<?php 

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\HybridRelations;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class LocationsMaster extends Eloquent {
	use HybridRelations;
	protected $connection = 'mongodb';
	// protected $collection = 'location';
	protected $table = 'location';
    // protected $primaryKey = 'id_master_role';

    // protected $fillable = [
    //     'id_company',
    //     'name_company',
    //     'id_holding',
    // ];

    // public $timestamps = false;
}
