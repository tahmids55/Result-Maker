<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BackupDatabase extends Command
{
    protected $signature   = 'markscraft:backup';
    protected $description = 'Create a SQL backup of the database';

    public function handle(): int
    {
        $config   = config('database.connections.' . config('database.default'));
        $filename = 'markscraft_backup_' . now()->format('Y-m-d_H-i-s') . '.sql';
        $path     = storage_path("app/backups/{$filename}");

        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0775, true);
        }

        $host   = $config['host'];
        $port   = $config['port'] ?? 3306;
        $db     = $config['database'];
        $user   = $config['username'];
        $pass   = $config['password'];

        // We use Docker to ensure we have Postgres 17 client utilities, preventing version mismatch errors
        $cmd = "docker run --rm -e PGPASSWORD=" . escapeshellarg($pass) . " postgres:17-alpine pg_dump --clean --if-exists --host=" . escapeshellarg($host) . " --port=" . escapeshellarg($port) . " --username=" . escapeshellarg($user) . " --dbname=" . escapeshellarg($db) . " > " . escapeshellarg($path) . " 2> " . escapeshellarg($path . ".err");
        exec($cmd, $output, $code);

        if ($code !== 0) {
            $error = file_exists($path . ".err") ? file_get_contents($path . ".err") : 'Unknown error';
            if (file_exists($path)) @unlink($path);
            if (file_exists($path . ".err")) @unlink($path . ".err");
            $this->error('Backup failed: ' . $error);
            return self::FAILURE;
        }

        if (file_exists($path . ".err")) @unlink($path . ".err");

        $this->info("Backup created: {$filename}");
        return self::SUCCESS;
    }
}
