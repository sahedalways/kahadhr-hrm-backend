<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'company_id',
        'f_name',
        'l_name',
        'nationality',
        'share_code',
        'share_code_status',
        'date_of_birth',
        'title',
        'is_active',
        'role',
        'invite_token',
        'invite_token_expires_at',
        'end_date',
        'start_date',
        'salary_type',
        'contract_hours',
        'employment_status',
        'phone_no',
        'team_id',
        'department_id',
        'job_title',
        'email',
        'avatar',
        'verified',
        'leave_in_liew',
        'annual_leave_hours',
        'billable_from',
        'is_billable',
        'deleted_at',
    ];



    protected $casts = [
        'is_active'        => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_billable' => 'boolean',
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

    public function teams()
    {
        return $this->belongsToMany(
            Team::class,
            'employee_teams',
            'user_id',
            'team_id'
        )
            ->using(EmployeeTeam::class)
            ->withPivot('is_team_lead')
            ->withTimestamps();
    }



    public function documents()
    {
        return $this->hasMany(EmpDocument::class, 'emp_id');
    }


    public function emergencyContacts()
    {
        return $this->hasMany(EmergencyContact::class);
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
            $user = auth()->check() ? auth()->user() : null;

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

        static::updating(function ($employee) {

            if ($employee->user && $employee->user->user_type === 'employee') {
                $data = [];

                if ($employee->isDirty('f_name')) {
                    $data['f_name'] = $employee->f_name;
                }


                if ($employee->isDirty('l_name')) {
                    $data['l_name'] = $employee->l_name;
                }

                if ($employee->isDirty('email')) {
                    $data['email'] = $employee->email;
                }

                if ($employee->isDirty('phone_no')) {
                    $data['phone_no'] = $employee->phone_no;
                }

                if ($employee->isDirty('is_active')) {
                    $data['is_active'] = $employee->is_active;
                }

                if (!empty($data)) {
                    $employee->user->update($data);
                }
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


        static::updated(function ($employee) {
            if (
                $employee->isDirty('contract_hours') ||
                $employee->isDirty('employment_status')
            ) {


                if ($employee->employment_status === 'part-time') {

                    $user = $employee->user;

                    if (! $user) {
                        return;
                    }

                    $contractHours = (float) ($employee->contract_hours ?? 0);

                    if ($contractHours <= 0) {
                        return;
                    }

                    $partTimePercent = (float) config('leave.part_time_percentage', 100);

                    $totalHours = ($contractHours * 52) * ($partTimePercent / 100);

                    $annualLeaveHours = ceil($totalHours);

                    if ($annualLeaveHours > 0) {
                        LeaveBalance::updateOrCreate(
                            [
                                'company_id' => $employee->company_id,
                                'user_id'    => $user->id,
                            ],
                            [
                                'total_annual_hours' => $annualLeaveHours,
                                'used_annual_hours'  => 0,
                                'carry_over_hours'   => $annualLeaveHours,
                            ]
                        );
                    }
                }
            }
        });




        static::saving(function ($employee) {

            if ($employee->nationality == 'British') {
                $employee->share_code_status = 'verified';
                $employee->share_code = null;
                return;
            }

            if (!$employee->share_code) {
                $employee->share_code_status = 'unavailable';
                $employee->share_code = null;
            } else {
                $employee->share_code_status = 'pending';
            }
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



    public function leaves()
    {
        // Assumes User has 'leaves' relation to LeaveRequest
        return $this->hasManyThrough(
            LeaveRequest::class,
            User::class,
            'id',
            'user_id',
            'id',
            'id'
        );
    }


    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'user_id', 'user_id');
    }
}
