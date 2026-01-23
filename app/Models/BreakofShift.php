<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BreakofShift extends Model
{
    protected $fillable = ['title', 'type', 'duration', 'shift_date_id'];

    public function shiftDate()
    {
        return $this->belongsTo(ShiftDate::class, 'shift_date_id');
    }
}
