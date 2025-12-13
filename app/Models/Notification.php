<?php

namespace App\Models;

use App\Traits\Scopes\FilterByUserType;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use FilterByUserType;
    protected $fillable = ['company_id', 'user_id', 'type', 'data', 'is_read'];

    protected $casts = [
        'data' => 'array',

        'is_read' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
