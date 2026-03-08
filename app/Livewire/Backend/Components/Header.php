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
    ];



    public $headerTimer = '00:00:00';
    public $initialSeconds = 0;
    public $isRunning = false;

    public $attendance;



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



    public function updateWorkingHoursCount()
    {
        $userTimeZone = auth()->user()->timezone ?? 'Asia/Dhaka';

        $userTodayStart = now()->setTimezone('Asia/Dhaka')->startOfDay();
        $userTodayEnd   = now()->setTimezone('Asia/Dhaka')->endOfDay();

        $this->attendance = Attendance::where('user_id', Auth::id())
            ->whereBetween('clock_in', [$userTodayStart, $userTodayEnd])
            ->latest()
            ->first();


        if (!$this->attendance) {
            $this->headerTimer = '00:00:00';
            $this->isRunning = false;
            return;
        }



        if ($this->attendance) {
            $clockInTime = Carbon::parse($this->attendance->clock_in, $userTimeZone);
            $currentTime = now()->setTimezone($userTimeZone);

            if ($this->attendance->clock_out) {
                $clockOutTime = Carbon::parse($this->attendance->clock_out, $userTimeZone);

                $elapsedSeconds = $clockOutTime->diffInSeconds($clockInTime);
            } else {
                $elapsedSeconds = $currentTime->diffInSeconds($clockInTime);
            }


            $elapsedSeconds = abs($elapsedSeconds);

            $hours = floor($elapsedSeconds / 3600);
            $minutes = floor(($elapsedSeconds % 3600) / 60);
            $seconds = $elapsedSeconds % 60;

            $this->headerTimer = sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
            if ($this->attendance->clock_in && $this->attendance->clock_out) {
                $this->isRunning = false;
                return;
            } else if ($this->attendance->clock_in) {
                $this->isRunning = true;
                return;
            }
        } else {
            $this->headerTimer = '00:00:00';
            $this->isRunning = false;
        }
    }




    public function mount()
    {
        $this->loadUnreadCount();
        $this->updateWorkingHoursCount();

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
