<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RestoreDatabase extends Command
{
    protected $signature   = 'markscraft:restore {file : Path to the SQL backup file} {--force : Force restore without confirmation}';
    protected $description = 'Restore database from a SQL backup file';

    public function handle(): int
    {
        $file = storage_path('app/' . $this->argument('file'));

        if (!file_exists($file)) {
            $this->error("Backup file not found: {$file}");
            return self::FAILURE;
        }

        if (!$this->option('force') && !$this->confirm("⚠ This will overwrite the current database. Continue?")) {
            return self::SUCCESS;
        }

        $config = config('database.connections.' . config('database.default'));
        $host   = $config['host'];
        $port   = $config['port'] ?? 3306;
        $db     = $config['database'];
        $user   = $config['username'];
        $pass   = $config['password'];

        // We use Docker to ensure we have Postgres 17 client utilities, preventing version mismatch errors
        $cmd = "docker run --rm -i -e PGPASSWORD=" . escapeshellarg($pass) . " postgres:17-alpine psql --host=" . escapeshellarg($host) . " --port=" . escapeshellarg($port) . " --username=" . escapeshellarg($user) . " --dbname=" . escapeshellarg($db) . " < " . escapeshellarg($file) . " 2> " . escapeshellarg($file . ".err");
        exec($cmd, $output, $code);

        if ($code !== 0) {
            $error = file_exists($file . ".err") ? file_get_contents($file . ".err") : 'Unknown error';
            if (file_exists($file . ".err")) @unlink($file . ".err");
            $this->error('Restore failed: ' . $error);
            return self::FAILURE;
        }

        if (file_exists($file . ".err")) @unlink($file . ".err");

        $this->info('Database restored successfully.');
        return self::SUCCESS;
    }
}
