<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeProfile extends Model
{
    protected $fillable = [
        'emp_id',
        'date_of_birth',
        'house_no',
        'address',
        'street',
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
        'passport_number',
        'passport_expiry_date',
    ];



    protected $casts = [
        'date_of_birth' => 'date',
        'passport_expiry_date' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'emp_id');
    }
}
