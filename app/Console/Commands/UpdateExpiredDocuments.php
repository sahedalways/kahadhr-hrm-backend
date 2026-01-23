<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CompanyDocument;
use Carbon\Carbon;

class UpdateExpiredDocuments extends Command
{

    protected $signature = 'documents:update-expired';


    protected $description = 'Update status to expired for pending documents whose expiration date has passed';

    public function handle()
    {
        $today = Carbon::now()->startOfDay();

        $documents = CompanyDocument::where('status', 'pending')
            ->whereNotNull('expires_at')
            ->whereDate('expires_at', '<', $today)
            ->get();

        $updatedCount = 0;

        foreach ($documents as $doc) {
            $doc->update(['status' => 'expired']);
            $updatedCount++;
        }

        $this->info("Total documents updated to expired: $updatedCount");
        return Command::SUCCESS;
    }
}
