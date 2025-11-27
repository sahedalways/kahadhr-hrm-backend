<?php

namespace App\Models;

use App\Traits\Scopes\FilterByUserType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class SmsSetting extends Model
{

    use FilterByUserType;

    protected $fillable = [
        'company_id',
        'provider',
        'twilio_sid',
        'twilio_auth_token',
        'twilio_from',
        'is_active',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
