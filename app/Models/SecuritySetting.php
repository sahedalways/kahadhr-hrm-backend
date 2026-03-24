<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SecuritySetting extends Model
{
    protected $table = 'security_settings';


    protected $fillable = [
        'user_id',
        'two_step_enabled',
        'verification_method',
    ];


    protected $casts = [
        'two_step_enabled' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
