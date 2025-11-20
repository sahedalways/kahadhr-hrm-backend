<?php

namespace App\Traits;

trait ToastTrait
{
  /**
   * Dispatch a toast notification
   *
   * @param string $message
   * @param string $type (success, error, info, warning)
   * @return void
   */
  public function toast(string $message, string $type = 'success'): void
  {
    $this->dispatch('toast', message: $message, notify: $type);
  }
}
