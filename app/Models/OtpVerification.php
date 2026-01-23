<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class OtpVerification extends Model
{
    protected $fillable = [
        'phone',
        'email',
        'otp',
        'expires_at',
    ];

    protected $dates = ['expires_at'];

    public function isExpired()
    {
        return $this->expires_at->isPast();
    }
}
