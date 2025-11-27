<?php

namespace App\Models;

use App\Traits\Scopes\FilterByUserType;
use Illuminate\Database\Eloquent\Model;


class CompanyDocument extends Model
{
    use FilterByUserType;
    protected $fillable = [
        'company_id',
        'emp_id',
        'name',
        'file_path',
        'expires_at',
        'status',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'emp_id');
    }

    public function getDocumentUrlAttribute()
    {
        if (!$this->file_path) {
            return null;
        }

        return asset('storage/' . $this->file_path);
    }
}
