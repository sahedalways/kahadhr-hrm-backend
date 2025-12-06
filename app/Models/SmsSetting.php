<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class SmsSetting extends Model
{

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
