<?php

namespace App\Events;

use App\Models\Notification;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NotificationEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $notification;

    public function __construct(Notification $notification)
    {
        $this->notification = $notification;
    }

    // Use company ID for the channel
    public function broadcastOn()
    {
        return new Channel('company.' . $this->notification->company_id);
    }

    public function broadcastAs()
    {
        return 'allNotifications';
    }
}
