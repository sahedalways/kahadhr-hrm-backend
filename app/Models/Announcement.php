<?php

namespace App\Models;

use App\Traits\Scopes\FilterByUserType;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use FilterByUserType;
    protected $fillable = ['company_id', 'title', 'description', 'media', 'created_by'];

    protected $casts = [
        'media' => 'array'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
