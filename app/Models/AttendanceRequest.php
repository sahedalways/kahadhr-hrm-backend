<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceRequest extends Model
{
    protected $fillable = ['user_id', 'attendance_id', 'type', 'reason', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }
}
