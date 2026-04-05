<?php

namespace App\Models;

use App\Traits\Scopes\FilterByUserType;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use FilterByUserType;
    protected $fillable = [
        'user_id',
        'company_id',
        'clock_in',
        'clock_out',
        'clock_in_location',
        'clock_out_location',
        'is_manual',
        'needs_approval',
        'status',
        'clock_in_latitude',
        'clock_in_longitude',
        'clock_in_accuracy',
        'clock_out_latitude',
        'clock_out_longitude',
        'clock_out_accuracy',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function requests()
    {
        return $this->hasMany(AttendanceRequest::class);
    }


    public function breaks()
    {
        return $this->hasMany(BreakofShift::class, 'attendance_id');
    }
}
