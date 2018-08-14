<?php 

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class AuthCompanyManagement extends Model {

    protected $table = 'user.user_authorization_company';
    // protected $primaryKey = 'id_authorization_company';

    protected $fillable = [
        'id_user',
        'id_company',
        'status',
        'created_by',
        'updated_by',
        'deteled_at',
        'deleted_by',
    ];

    public $timestamps = true;
}