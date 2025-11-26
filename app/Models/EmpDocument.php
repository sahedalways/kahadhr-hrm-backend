<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmpDocument extends Model
{
    protected $fillable = [
        'doc_type_id',
        'emp_id',
        'company_id',
        'file_path',
        'expires_at',
    ];

    public function documentType()
    {
        return $this->belongsTo(DocumentType::class, 'doc_type_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'emp_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
