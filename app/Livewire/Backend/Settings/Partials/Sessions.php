<?php

namespace App\Livewire\Backend\Settings\Partials;

use Illuminate\Support\Facades\DB;
use Jenssegers\Agent\Agent;
use Stevebauman\Location\Facades\Location;
use Illuminate\Support\Facades\Session;

use Livewire\WithPagination;
use App\Livewire\Backend\Components\BaseComponent;

class Sessions extends BaseComponent
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public function render()
    {
        $sessions = DB::table('sessions')
            ->where('user_id', auth()->id())
            ->orderBy('last_activity', 'desc')
            ->paginate(10);


        $sessions->getCollection()->transform(function ($s) {
            $agent = new Agent();
            $agent->setUserAgent($s->user_agent);
            $device = $agent->browser() . ' – ' . $agent->platform();

            $location = optional(Location::get($s->ip_address))->countryName ?? 'Unknown';

            return (object) [
                'id' => $s->id,
                'ip_address' => $s->ip_address,
                'device' => $device,
                'location' => $location,
                'login_time' => \Carbon\Carbon::createFromTimestamp($s->last_activity)->format('d M, Y h:i A'),
            ];
        });

        return view('livewire.backend.settings.partials.sessions', [
            'sessions' => $sessions
        ]);
    }

    public function logoutSession($sessionId)
    {
        DB::table('sessions')->where('id', $sessionId)->delete();
        Session::getHandler()->destroy($sessionId);
        $this->toast('Session logged out successfully', 'success');
    }

    public function logoutAllSessions()
    {
        $otherSessions = DB::table('sessions')
            ->where('user_id', auth()->id())
            ->where('id', '<>', session()->getId())
            ->pluck('id');

        foreach ($otherSessions as $sessionId) {
            Session::getHandler()->destroy($sessionId);
        }

        DB::table('sessions')->whereIn('id', $otherSessions)->delete();

        $this->toast('All other sessions have been logged out', 'success');
    }
}
