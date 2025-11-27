<?php

namespace App\Models;

use App\Traits\Scopes\FilterByUserType;
use Illuminate\Database\Eloquent\Model;

class Training extends Model
{
    use FilterByUserType;
    protected $fillable = ['company_id', 'title', 'description', 'content_type', 'file_path'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function assignments()
    {
        return $this->hasMany(TrainingAssignment::class);
    }
}
