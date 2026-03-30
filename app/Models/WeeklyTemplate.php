<?php

namespace App\Models;

use App\Traits\Scopes\FilterByUserType;
use Illuminate\Database\Eloquent\Model;

class WeeklyTemplate extends Model
{
    use FilterByUserType;
    protected $fillable = [
        'company_id',
        'name',
        'description',
        'template_data'
    ];

    protected $casts = [
        'template_data' => 'array'
    ];
}
