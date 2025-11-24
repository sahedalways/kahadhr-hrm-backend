<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserRepository
{
  /**
   * Get currently authenticated user
   */
  public function getAuthUser()
  {
    return Auth::user();
  }

  /**
   * Check if given password matches user password
   */
  public function checkPassword($user, string $password): bool
  {
    return Hash::check($password, $user->password);
  }

  /**
   * Update user's password
   */
  public function updatePassword($user, string $newPassword): void
  {
    $user->password = Hash::make($newPassword);
    $user->save();
  }
}
