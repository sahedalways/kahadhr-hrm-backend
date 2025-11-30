<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CleanChatTemp extends Command
{
    protected $signature = 'chat:clean-temp';
    protected $description = 'Clean old temp files in chat/attachments/tmp folder';

    public function handle()
    {
        $tmpFolder = public_path('storage/chat/attachments/tmp');

        if (!File::exists($tmpFolder)) {
            $this->info('Temp folder does not exist.');
            return;
        }

        $files = File::files($tmpFolder);
        $now = time();
        $deletedCount = 0;

        foreach ($files as $file) {
            // Remove files older than 24 hours
            if ($now - $file->getCTime() > 24 * 60 * 60) {
                File::delete($file->getPathname());
                $deletedCount++;
            }
        }

        $this->info("Deleted {$deletedCount} old temp files from tmp folder.");
    }
}
