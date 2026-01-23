<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    protected $fillable = ['name', 'emoji'];


    public function balances()
    {
        return $this->hasMany(LeaveBalance::class);
    }

    public function requests()
    {
        return $this->hasMany(LeaveRequest::class);
    }
}
