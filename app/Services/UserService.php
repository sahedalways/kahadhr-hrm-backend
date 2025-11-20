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




  public function register(array $data): User
  {

    return $this->userRepo->create($data);
  }

  public function updateUser(User $user, array $data): User
  {

    return $this->userRepo->update($user, $data);
  }

  public function getUser($id): ?User
  {
    return $this->userRepo->find($id);
  }

  public function getAllUsers()
  {
    return $this->userRepo->getAllUsers();
  }

  public function deleteUser($id): bool
  {
    $user = $this->getUser($id);

    if (!$user) {
      return false;
    }

    return $this->userRepo->delete($user);
  }
}
