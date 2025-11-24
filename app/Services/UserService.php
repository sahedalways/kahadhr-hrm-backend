<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\Models\User;


class UserService
{
  protected $userRepo;

  public function __construct(UserRepository $userRepo)
  {
    $this->userRepo = $userRepo;
  }

  /**
   * Change password for current user
   *
   * @param string $oldPassword
   * @param string $newPassword
   * @return array ['success' => bool, 'message' => string]
   */
  public function changePassword(string $oldPassword, string $newPassword): array
  {
    $user = $this->userRepo->getAuthUser();

    if (!$this->userRepo->checkPassword($user, $oldPassword)) {
      return ['success' => false, 'message' => 'Old password is incorrect'];
    }

    $this->userRepo->updatePassword($user, $newPassword);

    return ['success' => true, 'message' => 'Password updated successfully'];
  }
}
