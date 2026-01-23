<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordResetToken extends Model
{
  use HasFactory;

  protected $table = 'password_reset_tokens';

  // Primary key is 'email'
  protected $primaryKey = 'email';
  public $incrementing = false;
  protected $keyType = 'string';

  // Disable timestamps since only 'created_at' exists
  public $timestamps = false;

  protected $fillable = [
    'email',
    'token',
    'created_at',
  ];

  // Optionally, you can add a helper to check if token is expired
  public function isExpired($minutes = 2)
  {
    return now()->diffInMinutes($this->created_at) > $minutes;
  }
}
