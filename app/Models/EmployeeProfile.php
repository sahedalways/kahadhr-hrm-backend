<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeProfile extends Model
{
    protected $fillable = [
        'emp_id',
        'date_of_birth',
        'street_1',
        'street_2',
        'city',
        'state',
        'postcode',
        'country',
        'nationality',
        'home_phone',
        'mobile_phone',
        'personal_email',
        'gender',
        'marital_status',
        'tax_reference_number',
        'immigration_status',
        'brp_number',
        'brp_expiry_date',
        'right_to_work_expiry_date',
        'passport_number',
        'passport_expiry_date',
    ];



    protected $casts = [
        'date_of_birth' => 'date',
        'brp_expiry_date' => 'date',
        'right_to_work_expiry_date' => 'date',
        'passport_expiry_date' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'emp_id');
    }
}
