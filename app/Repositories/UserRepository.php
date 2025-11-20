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



  public function create(array $data): User
  {
    $user = new User();
    $user->f_name    = $data['first_name'];
    $user->l_name    = $data['last_name'];
    $user->email     = $data['email'];
    $user->phone_no  = $data['phone_no'];
    $user->password  = Hash::make($data['password']);
    $user->is_active = $data['is_active'] ?? false;
    $user->user_type = 'user';
    $user->email_verified_at = now();
    $user->save();

    return $user;
  }

  public function update(User $user, array $data): User
  {
    $user->f_name    = $data['first_name'];
    $user->l_name    = $data['last_name'];
    $user->email     = $data['email'];
    $user->phone_no  = $data['phone_no'];

    if (!empty($data['password'])) {
      $user->password = Hash::make($data['password']);
    }

    $user->is_active = $data['is_active'] ?? false;
    $user->save();

    return $user;
  }

  public function find($id): ?User
  {
    return User::where('id', $id)->where('user_type', 'user')->first();
  }


  public function delete(User $user): bool
  {
    return $user->delete();
  }


  public function getAllUsers()
  {
    return User::where('user_type', 'user')->latest()->get();
  }
}
