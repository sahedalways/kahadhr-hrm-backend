<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialInfoSettings extends Model
{
    protected $fillable = [
        'facebook',
        'twitter',
        'instagram',
        'linkedin',
        'youtube',
    ];
}
