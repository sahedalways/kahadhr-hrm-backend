<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShiftTemplates extends Model
{
    protected $fillable = ['company_id', 'title', 'job', 'color', 'address', 'note', 'start_time', 'end_time'];



    public function shifts()
    {
        return $this->hasMany(Shift::class, 'template_id');
    }
}