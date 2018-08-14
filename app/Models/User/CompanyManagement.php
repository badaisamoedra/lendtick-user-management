<?php 

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class CompanyManagement extends Model {

    protected $table = 'user.user_company';
    protected $primaryKey = 'id_user_company';

    protected $fillable = [
        'id_user_profile',
        'id_employee',
        'id_grade',
        'id_company',
        'employee_starting_date',
        'company_identity_path',
        'termination_date',
        'id_workflow_status'
    ];

    public $timestamps = false;
}