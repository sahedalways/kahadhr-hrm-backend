<?php

namespace App\Models;

use App\Traits\Scopes\FilterByUserType;
use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model
{
    use FilterByUserType;
    protected $fillable = ['company_id', 'user_id', 'leave_type_id', 'start_date', 'end_date', 'total_hours', 'status', 'other_reason', 'paid_hours', 'paid_status', 'remaining_annual_hours', 'reason'];

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
