<?php

namespace App\Models;

use App\Traits\Scopes\FilterByUserType;
use Illuminate\Database\Eloquent\Model;

class AdminPolicy extends Model
{
    use FilterByUserType;
    protected $fillable = [
        'title',
        'description',
        'file_path',
        'send_email',
    ];
}
