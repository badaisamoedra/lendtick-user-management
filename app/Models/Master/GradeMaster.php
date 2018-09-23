<?php 

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class GradeMaster extends Model {

    protected $table = 'user.master_grade';
    // protected $primaryKey = 'id_master_role';

    protected $fillable = [
        'id_grade',
        'name_grade',
        'microloan_amount'
    ];

    public $timestamps = false;
}