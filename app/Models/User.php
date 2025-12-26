<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasApiTokens, Notifiable;

    /**    '
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id',
        'f_name',
        'l_name',
        'phone_no',
        'email',
        'password',
        'permissions',
        'profile_completed',
        'email_verified_at',
        'phone_verified_at',
        'remember_token',
        'user_type',
        'is_active',
        'timezone',
    ];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'password' => 'hashed',
            'permissions' => 'array',
            'profile_completed' => 'boolean'
        ];
    }



    // Full Name Accessor
    public function getFullNameAttribute()
    {
        return $this->f_name . ' ' . $this->l_name;
    }




    // One User → One Company
    public function company()
    {
        return $this->hasOne(Company::class);
    }


    public function employee()
    {
        return $this->hasOne(Employee::class);
    }


    public function teams()
    {
        return $this->belongsToMany(Team::class, 'employee_team', 'user_id', 'team_id')
            ->using(EmployeeTeam::class)
            ->withPivot('is_team_lead')
            ->withTimestamps();
    }

    protected static function booted()
    {

        static::created(function ($user) {
            if ($user->employee) {
                $companyId = $user->employee->company_id;
                $employee = $user->employee;
                $annualLeaveHours = 0;

                if ($employee->salary_type === 'monthly') {
                    $annualLeaveHours = floatval(config('leave.full_time_hours', 100)) ?? 0;
                } elseif ($employee->salary_type === 'hourly') {

                    $contractHours = $employee->contract_hours ?? 0;
                    $partTimePercent = floatval(config('leave.part_time_percentage', 100));
                    $totalHours = ($contractHours * 52) * ($partTimePercent / 100);

                    $annualLeaveHours = ceil($totalHours);
                }

                if ($companyId) {
                    LeaveBalance::create([
                        'company_id'       => $companyId,
                        'user_id'          => $employee->user_id,
                        'total_annual_hours'      => $annualLeaveHours,
                        'used_annual_hours'       => 0,
                        'carry_over_hours' => $annualLeaveHours,
                    ]);
                }
            }
        });
    }
}
