<?php

namespace App\Livewire\Backend\Components;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Notifications extends Component
{

    public $notifications = [];
    public $perPage = 10;
    public $loadingMore = false;
    protected $user;
    protected function getListeners(): array
    {
        return [
            'newNotificationForDetails' => 'newNotification',
            'markAllUiRead' => 'markAllUiRead'
        ];
    }

    public $expandedNotificationId = null;

    // Toggle full message
    public function toggleNotification($id)
    {
        if ($this->expandedNotificationId === $id) {
            $this->expandedNotificationId = null;
        } else {
            $this->expandedNotificationId = $id;
        }
    }

    public function mount()
    {
        $this->loadNotifications();
    }


   public function loadNotifications()
{
    $userId = Auth::id();
    $userType = Auth::user()->user_type;

    $notifications = Notification::where('company_id', currentCompanyId())
        ->where(function ($q) use ($userId, $userType) {
            if ($userType == 'company') {
                $q->whereNull('user_id');
            } else {
                $q->where('user_id', $userId);
            }
        })
        ->orderBy('created_at', 'desc')
        ->take($this->perPage)
        ->get();

    $this->notifications = $notifications->toArray();

    Notification::whereIn('id', $notifications->pluck('id'))
        ->where('is_read', 0)
        ->update(['is_read' => 1]);

    $this->dispatch('mark-notifications-read-after-delay');
}



    public function markAllUiRead()
    {

        foreach ($this->notifications as &$notification) {
            $notification['is_read'] = 1;
        }
    }


    public function loadMore()
    {
        $this->loadingMore = true;
        $this->perPage += 10;

        $this->loadNotifications();

        $this->loadingMore = false;
    }



public function newNotification($notification)
{
    $authId = auth()->id();
    $userType = auth()->user()->user_type;

    if (isset($notification['user_id'])) {

        if (
            ($userType == 'company' && $notification['user_id'] === null) ||
            ($userType != 'company' && $notification['user_id'] == $authId)
        ) {
            $notification['is_read'] = 0;
            array_unshift($this->notifications, $notification);
        }
    }

    if (isset($notification['id'])) {
        Notification::where('id', $notification['id'])
            ->where('is_read', 0)
            ->update(['is_read' => 1]);
    }

    $this->dispatch('mark-notifications-read-after-delay');
}


    public function render()
    {
        return view('livewire.backend.components.notifications');
    }
}
