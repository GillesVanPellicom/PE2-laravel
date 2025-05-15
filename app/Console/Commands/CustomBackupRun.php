<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class CustomBackupRun extends Command
{
    protected $signature = 'backup:run-custom {name?}';

    public function handle()
    {
        $customName = $this->argument('name');

        // Step 1: Run the backup:run command
        $this->info('Running the backup process...');
        $process = Process::fromShellCommandline('php artisan backup:run');
        $process->run();

        if (!$process->isSuccessful()) {
            $this->error('Backup process failed: ' . $process->getErrorOutput());
            return;
        }

        $this->info('Backup process completed successfully.');

        // Step 2: Rename the backup file if a custom name is provided
        if ($customName) {
            $this->info('Renaming the backup file to the custom name...');

            $backupDirectory = storage_path('app/private/Laravel');
            $this->info("Searching for backup files in: {$backupDirectory}");

            // Ensure the directory exists
            if (!is_dir($backupDirectory)) {
                $this->info("Directory does not exist. Creating: {$backupDirectory}");
                mkdir($backupDirectory, 0755, true);
            }

            $allFiles = scandir($backupDirectory);
            $this->info("Files found: " . implode(', ', $allFiles));

            $latestBackup = collect($allFiles)
                ->filter(fn($file) => Str::endsWith($file, '.zip'))
                ->sortByDesc(fn($file) => filemtime($backupDirectory . '/' . $file))
                ->first();

            if (!$latestBackup) {
                $this->error('No backup file found to rename. Ensure the backup process created a file in the expected directory.');
                return;
            }

            $this->info("Latest backup file found: {$latestBackup}");

            $newBackupPath = $backupDirectory . '/' . $customName . '.zip';
            rename($backupDirectory . '/' . $latestBackup, $newBackupPath);

            $this->info("Backup file renamed to: {$customName}.zip");
        } else {
            $this->info('No custom name provided. Backup file retains its original name.');
        }
    }
}