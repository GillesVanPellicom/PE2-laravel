<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use File;

class RestoreBackup extends Command
{
    protected $signature = 'backup:restore-custom {name}';

    public function handle()
    {
        $backupName = $this->argument('name');
        $backupDirectory = storage_path('app/private/Laravel');
        $backupFilePath = "{$backupDirectory}/{$backupName}.zip";

        if (!file_exists($backupFilePath)) {
            $this->error("Backup file {$backupName}.zip not found.");
            return;
        }

        // Step 1: Unzip the backup file
        $this->info('Extracting the backup file...');
        $extractDirectory = storage_path('app/private/Laravel/restore');
        if (!is_dir($extractDirectory)) {
            mkdir($extractDirectory, 0755, true);
        }

        $zip = new \ZipArchive();
        if ($zip->open($backupFilePath) === true) {
            $zip->extractTo($extractDirectory);
            $zip->close();
            $this->info("Backup file extracted to {$extractDirectory}");
        } else {
            $this->error("Failed to extract backup file.");
            return;
        }

        // Step 2: Modify the SQL dump file
        $backupSqlFile = "{$extractDirectory}/db-dumps/mysql-laravel.sql";
        $this->info("Using SQL file: {$backupSqlFile}");

        if (file_exists($backupSqlFile)) {
            $this->info('Prepending SQL statements to the file...');
            $dropDatabaseStatement = <<<SQL
            DROP DATABASE IF EXISTS laravel;
            CREATE DATABASE laravel;
            USE laravel;

            SQL;

            // Read the current contents of the SQL dump
            $sqlContents = file_get_contents($backupSqlFile);

            // Prepend the statements and save back to the file
            file_put_contents($backupSqlFile, $dropDatabaseStatement . $sqlContents);

            $this->info('Successfully modified the SQL file.');
        } else {
            $this->error('No SQL backup file found.');
            return;
        }

        // Step 3: Restore the database
        $this->info('Restoring the database...');
        $command = "mysql -h" . env('DB_HOST') . " -u" . env('DB_USERNAME') . " -p" . env('DB_PASSWORD') . " " . env('DB_DATABASE') . " < {$backupSqlFile} 2>&1";
        $this->info("Running command: $command");

        $process = Process::fromShellCommandline($command);
        $process->run();

        if (!$process->isSuccessful()) {
            $this->error('Database restore failed: ' . $process->getErrorOutput());
            $this->info('Full output: ' . $process->getOutput());
            return;
        }

        $this->info('Database restored successfully.');

        // Step 4: Restore files (if applicable)
        $this->info('Restoring files...');
        // Add file restoration logic here if needed

        $this->info('Backup restoration completed successfully.');

        // Step 5: Cleanup after everything is done
        $this->info('Cleaning up temporary files...');
        if (is_dir($extractDirectory)) {
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($extractDirectory, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::CHILD_FIRST
            );

            foreach ($files as $fileinfo) {
                $todo = ($fileinfo->isDir()) ? 'rmdir' : 'unlink';
                $todo($fileinfo->getRealPath());
            }

            // Finally, delete the main directory
            rmdir($extractDirectory);
        }

        $this->info('Cleanup completed.');
    }
}
