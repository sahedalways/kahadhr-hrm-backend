<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserTyping implements ShouldBroadcast
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  public $user;
  public $receiverId;

  public function __construct($user, $receiverId)
  {
    $this->user = $user;
    $this->receiverId = $receiverId;
  }

  public function broadcastOn()
  {
    return new Channel('chat-' . $this->receiverId);
  }

  public function broadcastWith()
  {
    return [
      'user_id'   => $this->user->id,
      'user_name' => $this->user->user_type === 'company'
        ? 'Company Admin'
        : trim(($this->user->f_name ?? '') . ' ' . ($this->user->l_name ?? '')),
    ];
  }

  public function broadcastAs()
  {
    return 'UserTyping';
  }
}
