<?php

namespace App\Livewire\Backend\Components;

use Livewire\Component;

class Header extends Component
{
    protected $listeners = [
        'update-header-timer' => 'updateTimer',
        'tick' => 'increaseOneSecond'
    ];


    public $headerTimer = '00:00:00';
    public $isRunning = false;

    public function updateTimer($time, $running)
    {
        $this->headerTimer = $time;
        $this->isRunning = $running;
    }


    public function increaseOneSecond()
    {
        if (!$this->isRunning) return;

        $parts = explode(':', $this->headerTimer);
        $seconds = ($parts[0] * 3600) + ($parts[1] * 60) + $parts[2];

        $seconds++;

        $h = floor($seconds / 3600);
        $m = floor(($seconds % 3600) / 60);
        $s = $seconds % 60;

        $this->headerTimer = sprintf('%02d:%02d:%02d', $h, $m, $s);
    }

    public function render()
    {
        return view('livewire.backend.components.header');
    }
}
