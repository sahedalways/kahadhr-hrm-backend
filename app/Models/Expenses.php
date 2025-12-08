<?php

namespace App\Models;

use App\Traits\Scopes\FilterByUserType;
use Illuminate\Database\Eloquent\Model;

class Expenses extends Model
{
    use FilterByUserType;
    protected $fillable = [
        'company_id',
        'user_id',
        'category',
        'amount',
        'description',
        'attachments',
        'submitted_at',
    ];

    protected $casts = [
        'attachments' => 'array',
        'submitted_at' => 'datetime',
    ];

    // relation: each expense belongs to one user
    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // check if editable/deleteable (within 24 hours)
    public function canEditOrDelete()
    {
        if (!$this->submitted_at) return false;
        return $this->submitted_at->diffInHours(now()) < 24;
    }
}
