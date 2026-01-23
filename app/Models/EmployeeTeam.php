<?php

namespace App\Models;



use Illuminate\Database\Eloquent\Relations\Pivot;

class EmployeeTeam extends Pivot
{
    protected $table = 'employee_teams';

    protected $fillable = [
        'team_id',
        'user_id',
        'is_team_lead',
    ];

    protected $casts = [
        'is_team_lead' => 'boolean',
    ];
}
