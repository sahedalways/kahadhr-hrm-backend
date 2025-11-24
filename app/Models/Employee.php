<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'user_id',
        'company_id',
        'f_name',
        'l_name',
        'title',
        'is_active',
        'role',
    ];

    protected $casts = [
        'is_active'        => 'boolean',
    ];

    /**
     * Relation: Employee belongs to a Company
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get full name
     */
    public function getFullNameAttribute(): string
    {
        return $this->f_name . ' ' . $this->l_name;
    }



    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
