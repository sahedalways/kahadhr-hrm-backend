<?php

namespace App\Livewire\Backend\Settings\Partials;


use Illuminate\Support\Facades\DB;
use Jenssegers\Agent\Agent;
use Stevebauman\Location\Facades\Location;
use Illuminate\Support\Facades\Session;
use App\Livewire\Backend\Components\BaseComponent;

class Sessions extends BaseComponent
{

    public $sessions;



    public function mount()
    {

        $this->loadSessions();
    }


    public function loadSessions()
    {
        $this->sessions = collect(DB::table('sessions')
            ->where('user_id', auth()->id())
            ->orderBy('last_activity', 'desc')
            ->get())
            ->map(function ($s) {
                // Parse device and platform
                $agent = new Agent();
                $agent->setUserAgent($s->user_agent);
                $device = $agent->browser() . ' – ' . $agent->platform();


                $location = optional(Location::get($s->ip_address))->countryName ?? 'Unknown';

                return (object) [
                    'id' => $s->id,
                    'ip_address' => $s->ip_address,
                    'device' => $device,
                    'location' => $location,
                    'login_time' => \Carbon\Carbon::createFromTimestamp($s->last_activity)->format('H:i'),
                ];
            });
    }


    public function logoutSession($sessionId)
    {
        DB::table('sessions')->where('id', $sessionId)->delete();

        Session::getHandler()->destroy($sessionId);


        $this->loadSessions();
        $this->toast('Session logged out successfully', 'success');
    }

    /**
     * Logout all other sessions
     */
    public function logoutAllSessions()
    {

        $otherSessions = DB::table('sessions')
            ->where('user_id', auth()->id())
            ->where('id', '<>', session()->getId())
            ->pluck('id');

        foreach ($otherSessions as $sessionId) {
            Session::getHandler()->destroy($sessionId);
        }


        DB::table('sessions')
            ->whereIn('id', $otherSessions)
            ->delete();

        $this->loadSessions();
        $this->toast('All other sessions have been logged out', 'success');
    }



    public function render()
    {
        return view('livewire.backend.settings.partials.sessions');
    }
}
