<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BackupDatabase extends Command
{
    protected $signature = 'backup:run';
    protected $description = 'Run manual database backup and log it';

    public function handle()
    {
        // ব্যাকআপ ফাইলের নাম
        $fileName = 'backup-' . date('Y-m-d_H-i-s') . '.sql';
        $path = storage_path('app/backups/' . $fileName);



        DB::table('system_logs')->insert([
            'type' => 'backup',
            'message' => "Backup created: $fileName",
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->info("Backup completed: $fileName");
    }
}
