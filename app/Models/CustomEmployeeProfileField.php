<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomEmployeeProfileField extends Model
{
    use HasFactory;


    protected $fillable = [
        'company_id',
        'name',
        'key',
        'type',
        'options',
        'required',
    ];

    protected $casts = [
        'options' => 'array',
        'required' => 'boolean',
    ];

    /* ================= Relationships ================= */

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function values()
    {
        return $this->hasMany(CustomEmployeeProfileFieldValue::class, 'field_id');
    }
}
