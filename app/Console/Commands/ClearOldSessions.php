<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ClearOldSessions extends Command
{
    protected $signature = 'sessions:clear-old';
    protected $description = 'Delete sessions older than 1 month';

    public function handle()
    {

        $oneMonthAgo = Carbon::now()->subMonth()->timestamp;

        $deleted = DB::table('sessions')
            ->where('last_activity', '<', $oneMonthAgo)
            ->delete();

        $this->info("Deleted $deleted old session(s).");
    }
}
