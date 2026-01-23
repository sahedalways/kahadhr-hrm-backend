<?php

namespace App\Traits;

use Illuminate\Support\Facades\Hash;

trait VerifyPassword
{
  public $passwordInput;
  public $passwordVerified = false;


  public function verifyPassword()
  {
    if (Hash::check($this->passwordInput, auth()->user()->password)) {
      $this->passwordVerified = true;
    } else {
      $this->toast('Password is incorrect!', 'error');
    }
  }
}
