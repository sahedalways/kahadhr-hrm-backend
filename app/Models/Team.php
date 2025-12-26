<?php

namespace App\Models;

use App\Traits\Scopes\FilterByUserType;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use FilterByUserType;
    protected $fillable = ['company_id', 'department_id', 'name', 'team_lead_id'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function employees()
    {
        return $this->belongsToMany(User::class, 'employee_teams', 'team_id', 'user_id')
            ->using(EmployeeTeam::class)
            ->withPivot('is_team_lead')
            ->withTimestamps();
    }
}
