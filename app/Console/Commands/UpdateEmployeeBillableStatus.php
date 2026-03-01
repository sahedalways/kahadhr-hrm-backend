<?php

namespace App\Console\Commands;

use App\Models\Employee;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class UpdateEmployeeBillableStatus extends Command
{
    protected $signature = 'employees:update-billable';
    protected $description = 'Update employees billable status based on billable_from date';



    public function handle()
    {
        $today = Carbon::today();

        $updated = Employee::query()
            ->withoutGlobalScope('filterByUserType')
            ->whereNull('deleted_at')
            ->where('is_billable', false)
            ->whereNotNull('billable_from')
            ->whereDate('billable_from', '<=', $today)
            ->update([
                'is_billable' => true,
                'updated_at' => now(),
            ]);

        $this->info("Updated {$updated} employees to billable.");
    }
}
