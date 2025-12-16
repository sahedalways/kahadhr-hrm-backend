<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BreakofShift extends Model
{
    protected $fillable = ['title', 'type', 'duration', 'shift_id'];

    public function shifts()
    {
        return $this->hasMany(Shift::class);
    }
}
