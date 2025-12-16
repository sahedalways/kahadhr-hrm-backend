<?php

namespace App\Models;

use App\Traits\Scopes\FilterByUserType;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use FilterByUserType;
    protected $fillable = ['title', 'job', 'color', 'address', 'note', 'template_id', 'company_id', 'break_id'];

    public function dates()
    {
        return $this->hasMany(ShiftDate::class);
    }

    public function template()
    {
        return $this->belongsTo(ShiftTemplates::class, 'template_id');
    }


    public function company()
    {
        return $this->belongsTo(Company::class);
    }


    public function breaks()
    {
        return $this->hasMany(BreakofShift::class);
    }
}
