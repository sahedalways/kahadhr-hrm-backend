<?php

namespace App\Models;

use App\Traits\Scopes\FilterByUserType;
use Illuminate\Database\Eloquent\Model;

class LeaveSetting extends Model
{
    use FilterByUserType;
    protected $fillable = [
        'company_id',
        'full_time_hours'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
