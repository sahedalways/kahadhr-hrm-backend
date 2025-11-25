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
        'invite_token',
        'invite_token_expires_at',
        'end_date',
        'start_date',
        'salary_type',
        'contract_hours',
        'team_id',
        'department_id',
        'job_title'
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
