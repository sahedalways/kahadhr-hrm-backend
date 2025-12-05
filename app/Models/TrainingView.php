<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingView extends Model
{
    protected $fillable = [
        'training_id',
        'user_id',
        'view_percentage',
        'fully_watched'
    ];

    /**
     * TrainingView belongs to a Training
     */
    public function training()
    {
        return $this->belongsTo(Training::class);
    }

    /**
     * TrainingView belongs to a User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
