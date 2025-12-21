<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomEmployeeProfileFieldValue extends Model
{
    use HasFactory;

    protected $table = 'custom_employee_profile_field_values';

    protected $fillable = [
        'employee_id',
        'field_id',
        'value',
    ];

    /* ================= Relationships ================= */

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function field()
    {
        return $this->belongsTo(CustomEmployeeProfileField::class, 'field_id');
    }
}
