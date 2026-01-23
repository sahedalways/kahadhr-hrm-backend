<?php

namespace App\Models;

use App\Traits\Scopes\FilterByUserType;
use Illuminate\Database\Eloquent\Model;

class LeaveBalance extends Model
{
    use FilterByUserType;
    protected $fillable = [
        'user_id',
        'company_id',
        'total_annual_hours',
        'used_annual_hours',
        'total_leave_in_liew',
        'used_leave_in_liew',
        'carry_over_hours',
    ];



    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
