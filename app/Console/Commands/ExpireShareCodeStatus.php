<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee;
use App\Models\EmpDocument;
use Carbon\Carbon;

class ExpireShareCodeStatus extends Command
{
    protected $signature = 'sharecode:expire';
    protected $description = 'Expire Share Code status if latest document expires';

    public function handle()
    {
        $employees = Employee::where('nationality', '!=', 'British')
            ->whereNotNull('share_code')
            ->get();

        foreach ($employees as $employee) {


            $latestDoc = EmpDocument::where('emp_id', $employee->id)
                ->whereHas('documentType', function ($q) {
                    $q->where('name', 'Share Code');
                })
                ->latest('created_at')
                ->first();

            if (!$latestDoc) {
                continue;
            }

            $expiresAt = $latestDoc->expires_at;

            if ($expiresAt && Carbon::parse($expiresAt)->isPast()) {
                $employee->share_code_status = 'expired';
                $employee->saveQuietly();
            }
        }

        $this->info('Share Code expiry check completed.');
    }
}
