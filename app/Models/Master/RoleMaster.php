<?php 

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class RoleMaster extends Model {

    protected $table = 'user.master_role';
    // protected $primaryKey = 'id_master_role';

    protected $fillable = [
        'id_role_master',
        'name_role_master',
        'is_front_end',
        'created_by',
        'updated_by',
        'deleted_at',
        'deleted_by',
        'status'
    ];

    public $timestamps = true;
}