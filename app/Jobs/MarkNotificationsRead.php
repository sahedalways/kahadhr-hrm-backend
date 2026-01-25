<?php

namespace App\Jobs;

use App\Models\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

class MarkNotificationsRead implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    protected $notificationIds;

    public function __construct($notificationIds)
    {
        $this->notificationIds = $notificationIds;
    }

    public function handle()
    {
        Notification::whereIn('id', $this->notificationIds)
            ->where('is_read', 0)
            ->update(['is_read' => 1]);
    }
}
