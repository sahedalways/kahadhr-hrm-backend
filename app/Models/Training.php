<?php

namespace App\Models;

use App\Traits\Scopes\FilterByUserType;
use Illuminate\Database\Eloquent\Model;

class Training extends Model
{
    use FilterByUserType;
    protected $fillable = ['company_id', 'course_name', 'description', 'content_type', 'file_path', 'from_date', 'to_date', 'expiry_date', 'required_proof'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function assignments()
    {
        return $this->hasMany(TrainingAssignment::class, 'training_id');
    }
}
