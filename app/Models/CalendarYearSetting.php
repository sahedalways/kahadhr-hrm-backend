<?php

namespace App\Models;

use App\Traits\Scopes\FilterByUserType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class CalendarYearSetting extends Model
{
    use FilterByUserType;
    protected $fillable = [
        'company_id',
        'calendar_year',
    ];


    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
