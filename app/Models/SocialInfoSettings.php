<?php

namespace App\Models;

use App\Traits\Scopes\FilterByUserType;
use Illuminate\Database\Eloquent\Model;


class SocialInfoSettings extends Model
{
    use FilterByUserType;
    protected $fillable = [
        'company_id',
        'facebook',
        'twitter',
        'instagram',
        'linkedin',
        'youtube',
    ];



    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
