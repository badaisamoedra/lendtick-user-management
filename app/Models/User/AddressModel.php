<?php 

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AddressModel extends Model {

    use SoftDeletes;

    protected $table = 'user.user_address';
    protected $dates = ['deleted_at'];
    // protected $primaryKey = 'id_authorization_company';

    protected $fillable = [
        'id_user_address',
        'id_user_profile',
        'address_name',
        'receiver_name',
        'address_text',
        'city_or_district',
        'postal_code',
        'receiver_phone',
        'address_latitude',
        'address_longitude',
        'address_latlong_text',
        'receiver_phone',
        'address_latitude',
        'address_longitude',
        'address_latlong_text',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deteled_at',
        'deleted_by',
        'status',
        'is_main_address'
    ];

    public $timestamps = true;
}
