<?php

namespace App\Models;

use App\Traits\Scopes\FilterByUserType;
use Illuminate\Database\Eloquent\Model;

class CompanyPolicy extends Model
{
    use FilterByUserType;
    protected $fillable = [
        'company_id',
        'title',
        'description',
        'file_path',
        'send_email',
    ];

    // Company relationship
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
