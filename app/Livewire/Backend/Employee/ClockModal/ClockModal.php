<?php

namespace App\Livewire\Backend\Employee\ClockModal;

use App\Livewire\Backend\Components\BaseComponent;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;

class ClockModal extends BaseComponent
{
    public $clockInLocation = '';
    public $clockOutLocation = '';

    protected $listeners = ['setLocation'];

    public function clockIn()
    {
        Attendance::create([
            'user_id' => Auth::id(),
            'company_id' => Auth::user()->company_id,
            'clock_in' => now(),
            'clock_in_location' => $this->clockInLocation,
            'status' => 'pending',
        ]);

        $this->emit('attendanceUpdated');
        $this->dispatchBrowserEvent('close-clock-modal');
    }

    public function clockOut()
    {
        $attendance = Attendance::where('user_id', Auth::id())
            ->latest()
            ->first();

        if ($attendance && !$attendance->clock_out) {
            $attendance->update([
                'clock_out' => now(),
                'clock_out_location' => $this->clockOutLocation,
            ]);

            $this->emit('attendanceUpdated');
            $this->dispatchBrowserEvent('close-clock-modal');
        }
    }

    public function setLocation($location)
    {
        $this->clockInLocation = $location;
        $this->clockOutLocation = $location;
    }

    public function render()
    {
        return view('livewire.backend.employee.clock-modal.clock-modal');
    }
}
