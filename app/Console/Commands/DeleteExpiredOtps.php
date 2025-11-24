<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DeleteExpiredOtps extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'otp:delete-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete OTPs older than 2 minutes from otp_verifications table';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $cutoff = Carbon::now()->subMinutes(2);

        $deleted = DB::table('otp_verifications')
            ->where('created_at', '<', $cutoff)
            ->delete();

        $this->info("Deleted $deleted expired OTP(s).");

        return 0;
    }
}
