<?php 

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class CompanyMaster extends Model {

    protected $table = 'user.master_company';
    // protected $primaryKey = 'id_master_role';

    protected $fillable = [
        'id_company',
        'name_company',
        'id_holding',
    ];

    public $timestamps = false;
}