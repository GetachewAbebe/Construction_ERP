<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class MaintenanceController extends Controller
{
    public function index()
    {
        // Get system information
        $systemInfo = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'environment' => config('app.env'),
            'debug_mode' => config('app.debug') ? 'Enabled' : 'Disabled',
            'timezone' => config('app.timezone'),
            'database_connection' => config('database.default'),
        ];

        // Get cache statistics
        $cacheInfo = [
            'driver' => config('cache.default'),
            'prefix' => config('cache.prefix'),
        ];

        // Get storage information
        $storageInfo = [
            'logs_size' => $this->getDirectorySize(storage_path('logs')),
            'cache_size' => $this->getDirectorySize(storage_path('framework/cache')),
            'sessions_size' => $this->getDirectorySize(storage_path('framework/sessions')),
        ];

        // Get recent log files
        $logFiles = $this->getRecentLogFiles();

        return view('admin.maintenance.index', compact('systemInfo', 'cacheInfo', 'storageInfo', 'logFiles'));
    }

    public function clearCache()
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');

            return redirect()->route('admin.maintenance.index')
                ->with('success', 'System cache matrix successfully purged. All cached configurations, routes, and views have been expunged from memory.');
        } catch (\Exception $e) {
            return redirect()->route('admin.maintenance.index')
                ->with('error', 'Cache purge operation failed: ' . $e->getMessage());
        }
    }

    public function optimizeSystem()
    {
        try {
            Artisan::call('optimize');
            Artisan::call('config:cache');
            Artisan::call('route:cache');
            Artisan::call('view:cache');

            return redirect()->route('admin.maintenance.index')
                ->with('success', 'System optimization protocols executed successfully. Application performance has been enhanced through strategic caching.');
        } catch (\Exception $e) {
            return redirect()->route('admin.maintenance.index')
                ->with('error', 'Optimization operation failed: ' . $e->getMessage());
        }
    }

    public function clearLogs()
    {
        try {
            $logPath = storage_path('logs');
            $files = File::files($logPath);
            
            foreach ($files as $file) {
                if ($file->getExtension() === 'log') {
                    File::delete($file->getPathname());
                }
            }

            return redirect()->route('admin.maintenance.index')
                ->with('success', 'System log archives successfully expunged. All historical log files have been removed from storage.');
        } catch (\Exception $e) {
            return redirect()->route('admin.maintenance.index')
                ->with('error', 'Log purge operation failed: ' . $e->getMessage());
        }
    }

    public function createBackup()
    {
        try {
            $timestamp = now()->format('Y-m-d_His');
            $backupFileName = "backup_{$timestamp}.sql";
            $backupPath = storage_path("app/backups/{$backupFileName}");

            // Ensure backup directory exists
            if (!File::exists(storage_path('app/backups'))) {
                File::makeDirectory(storage_path('app/backups'), 0755, true);
            }

            // Get database configuration
            $database = config('database.default');
            $connection = config("database.connections.{$database}");

            if ($database === 'pgsql') {
                // PostgreSQL backup
                $command = sprintf(
                    'pg_dump -h %s -U %s -d %s > %s',
                    escapeshellarg($connection['host']),
                    escapeshellarg($connection['username']),
                    escapeshellarg($connection['database']),
                    escapeshellarg($backupPath)
                );
                
                // Set password environment variable
                putenv("PGPASSWORD={$connection['password']}");
                exec($command, $output, $returnVar);
                putenv("PGPASSWORD");
                
            } elseif ($database === 'mysql') {
                // MySQL backup
                $command = sprintf(
                    'mysqldump -h %s -u %s -p%s %s > %s',
                    escapeshellarg($connection['host']),
                    escapeshellarg($connection['username']),
                    escapeshellarg($connection['password']),
                    escapeshellarg($connection['database']),
                    escapeshellarg($backupPath)
                );
                exec($command, $output, $returnVar);
            } else {
                return redirect()->route('admin.maintenance.index')
                    ->with('error', 'Database backup not supported for this connection type.');
            }

            if ($returnVar === 0 && File::exists($backupPath)) {
                return redirect()->route('admin.maintenance.index')
                    ->with('success', "Database backup successfully created: {$backupFileName}. Archive stored in secure backup repository.");
            } else {
                return redirect()->route('admin.maintenance.index')
                    ->with('error', 'Database backup operation failed. Ensure database credentials and utilities are properly configured.');
            }
        } catch (\Exception $e) {
            return redirect()->route('admin.maintenance.index')
                ->with('error', 'Backup creation failed: ' . $e->getMessage());
        }
    }

    public function downloadBackup($filename)
    {
        $backupPath = storage_path("app/backups/{$filename}");

        if (!File::exists($backupPath)) {
            return redirect()->route('admin.maintenance.index')
                ->with('error', 'Backup file not found in repository.');
        }

        return response()->download($backupPath);
    }

    public function listBackups()
    {
        $backupPath = storage_path('app/backups');
        
        if (!File::exists($backupPath)) {
            return [];
        }

        $files = File::files($backupPath);
        $backups = [];

        foreach ($files as $file) {
            $backups[] = [
                'name' => $file->getFilename(),
                'size' => $this->formatBytes($file->getSize()),
                'date' => date('Y-m-d H:i:s', $file->getMTime()),
            ];
        }

        return response()->json($backups);
    }

    private function getDirectorySize($path)
    {
        if (!File::exists($path)) {
            return '0 B';
        }

        $size = 0;
        foreach (File::allFiles($path) as $file) {
            $size += $file->getSize();
        }

        return $this->formatBytes($size);
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }

    private function getRecentLogFiles($limit = 5)
    {
        $logPath = storage_path('logs');
        
        if (!File::exists($logPath)) {
            return [];
        }

        $files = File::files($logPath);
        $logs = [];

        foreach ($files as $file) {
            if ($file->getExtension() === 'log') {
                $logs[] = [
                    'name' => $file->getFilename(),
                    'size' => $this->formatBytes($file->getSize()),
                    'modified' => date('Y-m-d H:i:s', $file->getMTime()),
                ];
            }
        }

        // Sort by modification time (newest first)
        usort($logs, function($a, $b) {
            return strtotime($b['modified']) - strtotime($a['modified']);
        });

        return array_slice($logs, 0, $limit);
    }
}
