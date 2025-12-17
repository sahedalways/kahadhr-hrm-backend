<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShiftBreak extends Model
{
    protected $fillable = ['company_id', 'title', 'type', 'duration'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }


    public function shiftDates()
    {
        return $this->belongsTo(ShiftDate::class);
    }
}