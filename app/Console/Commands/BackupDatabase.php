<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BackupDatabase extends Command
{
    protected $signature = 'backup:run';
    protected $description = 'Run manual database backup and log it';

    public function handle()
    {
        $backupPath = storage_path('app/backups/');


        if (!file_exists($backupPath)) {
            mkdir($backupPath, 0755, true);
        }


        foreach (glob($backupPath . '*.sql') as $oldFile) {
            unlink($oldFile);
        }

        $fileName = 'backup-' . date('Y-m-d_H-i-s') . '.sql';
        $fullPath = $backupPath . $fileName;

        try {

            $db = config('database.connections.mysql.database');
            $user = config('database.connections.mysql.username');
            $pass = config('database.connections.mysql.password');
            $host = config('database.connections.mysql.host');


            $command = "mysqldump -h {$host} -u {$user} --password=\"{$pass}\" {$db} > {$fullPath}";
            exec($command);


            DB::table('system_logs')->insert([
                'type' => 'backup',
                'message' => "Backup created: $fileName",
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->info("Backup completed: $fileName stored at {$fullPath}");
        } catch (\Exception $e) {
            $this->error("Backup failed: " . $e->getMessage());
            Log::error("Backup command failed: " . $e->getMessage());
        }
    }
}
