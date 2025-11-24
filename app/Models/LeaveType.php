<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    protected $fillable = ['company_id', 'name', 'emoji', 'is_adjustable'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function balances()
    {
        return $this->hasMany(LeaveBalance::class);
    }

    public function requests()
    {
        return $this->hasMany(LeaveRequest::class);
    }
}
