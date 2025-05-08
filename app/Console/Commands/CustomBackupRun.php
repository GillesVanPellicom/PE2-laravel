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

        if ($customName) {
            $backupDirectory = storage_path('app/private/Laravel');

            // Ensure the directory exists
            if (!is_dir($backupDirectory)) {
                mkdir($backupDirectory, 0755, true);
            }

            $newBackupPath = $backupDirectory . '/' . $customName . '.zip';

            // Check if a file with the custom name already exists
            if (file_exists($newBackupPath)) {
                $this->error("A backup file with the name '{$customName}.zip' already exists. Please choose a different name.");
                return;
            }

            // Run the backup process with the custom name
            $this->info('Running the backup process with the custom name...');
            $process = Process::fromShellCommandline("php artisan backup:run --only-to-disk=local");
            $process->run();

            if (!$process->isSuccessful()) {
                $this->error('Backup process failed: ' . $process->getErrorOutput());
                return;
            }

            // Move the generated backup file to the custom name
            $allFiles = scandir($backupDirectory);
            $latestBackup = collect($allFiles)
                ->filter(fn($file) => Str::endsWith($file, '.zip'))
                ->sortByDesc(fn($file) => filemtime($backupDirectory . '/' . $file))
                ->first();

            if ($latestBackup) {
                rename($backupDirectory . '/' . $latestBackup, $newBackupPath);
                $this->info("Backup file created with the custom name: {$customName}.zip");
            } else {
                $this->error('No backup file found after the process.');
            }
        } else {
            $this->info('No custom name provided. Running the backup process with the default naming convention...');
            $process = Process::fromShellCommandline('php artisan backup:run');
            $process->run();

            if (!$process->isSuccessful()) {
                $this->error('Backup process failed: ' . $process->getErrorOutput());
                return;
            }

            $this->info('Backup process completed successfully.');
        }
    }
}