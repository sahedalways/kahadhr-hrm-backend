<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;


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
        'job_title',
        'email',
        'avatar',
        'verified'
    ];

    protected $casts = [
        'is_active'        => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
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


    public function documents()
    {
        return $this->hasMany(EmpDocument::class, 'emp_id');
    }


    // Accessor for logo URL
    public function getAvatarUrlAttribute()
    {
        return $this->avatar
            ? asset('storage/' . $this->avatar)
            : asset('assets/img/default-avatar.png');
    }



    protected static function booted()
    {
        static::addGlobalScope('filterByUserType', function (Builder $builder) {
            $user = auth()->check() ? app('authUser') : null;

            if (!$user) {
                $builder->whereNull('company_id');
                return;
            }

            if ($user->user_type === 'superAdmin') {
                $builder->whereNull('company_id');
            } elseif ($user->user_type === 'company') {
                $builder->where('company_id', $user->company->id ?? 0);
            }
        });


        static::updated(function ($employee) {


            if ($employee->isDirty('email')) {

                // get related user
                $user = $employee->user;

                if ($user) {
                    $user->update([
                        'email' => $employee->email,
                    ]);
                }
            }
        });
    }
}
