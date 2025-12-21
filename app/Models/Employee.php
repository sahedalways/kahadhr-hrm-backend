<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use SoftDeletes;

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
        'verified',
        'leave_in_liew',
        'annual_leave_hours',
        'billable_from',
        'deleted_at',
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
        $initials = strtoupper(
            substr($this->f_name ?? '', 0, 1) . substr($this->l_name ?? '', 0, 1)
        );

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100">
                <rect width="100%" height="100%" fill="#0d6fc5ff"/>
                <text x="50%" y="55%" font-size="36" fill="#ffffff"
                      text-anchor="middle" dominant-baseline="middle"
                      font-family="Arial, sans-serif" font-weight="bold">'
            . $initials .
            '</text>
            </svg>';

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
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
                return;
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


        static::created(function ($employee) {

            $companyId = $employee->company_id;
            $annualLeaveHours = 0;

            if ($employee->salary_type === 'monthly') {
                $annualLeaveHours = floatval(config('leave.full_time_hours', 100)) ?? 0;
            } elseif ($employee->salary_type === 'hourly') {

                $contractHours = $employee->contract_hours ?? 0;
                $partTimePercent = floatval(config('leave.part_time_percentage', 100));
                $totalHours = ($contractHours * 52) * ($partTimePercent / 100);

                $annualLeaveHours = ceil($totalHours);
            }


            LeaveBalance::create([
                'company_id'       => $companyId,
                'user_id'          => $employee->user_id,
                'total_annual_hours'      => $annualLeaveHours,
                'used_annual_hours'       => 0,
                'carry_over_hours' => $annualLeaveHours,
            ]);
        });
    }

    public function profile()
    {
        return $this->hasOne(EmployeeProfile::class, 'emp_id');
    }


    public function customFieldValues()
    {
        return $this->hasMany(CustomEmployeeProfileFieldValue::class);
    }

    /**
     * Handy accessor:
     * $employee->customFields
     */
    public function customFields()
    {
        return $this->belongsToMany(
            CustomEmployeeProfileField::class,
            'custom_employee_profile_field_values',
            'employee_id',
            'field_id'
        )->withPivot('value');
    }
}
