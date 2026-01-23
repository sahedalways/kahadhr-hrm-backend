<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class TrainingAssignment extends Model
{
    protected $fillable = ['training_id', 'user_id', 'status', 'completed_at', 'proof_file'];

    public function training()
    {
        return $this->belongsTo(Training::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
