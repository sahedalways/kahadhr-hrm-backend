<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillingPlan extends Model
{
    protected $fillable = [
        'admin_fee',
        'employee_fee',
        'is_active',
    ];


    public function companies()
    {
        return $this->hasMany(Company::class);
    }
}
