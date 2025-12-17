<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShiftDate extends Model
{
    protected $fillable = ['shift_id', 'date', 'start_time', 'end_time', 'total_hours'];

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function breaks()
    {
        return $this->hasMany(BreakofShift::class);
    }

    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'shift_employees');
    }
}