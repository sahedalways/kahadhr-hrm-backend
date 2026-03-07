<?php

namespace App\Livewire\Backend\Components;

use App\Models\Attendance;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;


class Header extends Component
{
    public $unreadCount = 0;

    public $perPage = 10;
    public $loadingMore = false;

    protected $listeners = [
        'newNotificationForDetails' => 'newNotification',
        'update-header-timer' => 'updateTimer',
    ];



    public $headerTimer = '00:00:00';
    public $initialSeconds = 0;
    public $isRunning = false;

    public function updateTimer($time, $running)
    {

        $this->headerTimer = $time;
        $this->isRunning = $running;
    }


    public function loadTimerState()
    {
        $attendance = Attendance::where('user_id', auth()->user()->id)
            ->whereNull('clock_out')
            ->latest()
            ->first();

        $userTimeZone = auth()->user()->timezone ?? 'Asia/Dhaka';

        if ($attendance && $attendance->clock_in) {
            $this->isRunning = true;
            $clockInTime = Carbon::parse($attendance->clock_in)->setTimezone($userTimeZone);
            $this->initialSeconds = $clockInTime->diffInSeconds(now()->setTimezone($userTimeZone));
            $this->headerTimer = gmdate("H:i:s", $this->initialSeconds);
        } else {
            $this->isRunning = false;
            $this->initialSeconds = 0;
            $this->headerTimer = '00:00:00';
        }
    }



    public function markAllAsRead()
    {

        $this->unreadCount = 0;

        $this->dispatch('markAllAsRead');
    }




    public function newNotification($notification)
    {
        $authId   = auth()->id();
        $userType = auth()->user()->user_type;

        if (array_key_exists('user_id', $notification)) {

            if (
                ($userType === 'company' && $notification['user_id'] === null) ||
                ($userType !== 'company' && (int)$notification['user_id'] === (int)$authId)
            ) {
                $notification['is_read'] = 0;
                $this->unreadCount++;
            }
        }
    }


    public function mount()
    {
        $this->loadUnreadCount();

        $this->loadTimerState();

        if ($this->headerTimer) {
            $parts = explode(':', $this->headerTimer);
            $this->initialSeconds = ($parts[0] * 3600) + ($parts[1] * 60) + $parts[2];
        }
    }


    public function render()
    {


        return view('livewire.backend.components.header');
    }


    public function loadUnreadCount()
    {
        $userId = Auth::id();
        $userType = Auth::user()->user_type;

        $this->unreadCount = Notification::where('company_id', currentCompanyId())
            ->where(function ($q) use ($userId, $userType) {
                if ($userType == 'company') {
                    $q->whereNull('user_id');
                } else {
                    $q->where('user_id', $userId);
                }
            })
            ->where('is_read', 0)
            ->count();
    }





    public function logout()
    {
        $user = auth()->user();

        if ($user && $user->user_type == 'employee' && $user->employee) {
            $sub = $user->employee->company->sub_domain;
            auth()->logout();
            session()->invalidate();
            session()->regenerateToken();

            return redirect()->route('employee.auth.empLogin', [
                'company' => $sub
            ]);
        }


        if ($user && $user->user_type == 'company' && $user->company) {
            $sub = $user->company->sub_domain;

            auth()->logout();
            session()->invalidate();
            session()->regenerateToken();

            $baseDomain = config('app.base_domain');

            $fullDomain = "https://{$sub}.{$baseDomain}";

            return redirect()->away($fullDomain);
        }



        auth()->logout();
        session()->invalidate();
        session()->regenerateToken();


        return redirect('/');
    }
}
