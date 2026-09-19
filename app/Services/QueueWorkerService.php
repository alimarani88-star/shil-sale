<?php

namespace App\Services;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class QueueWorkerService
{
    public function connection(): string
    {
        return (string) config('queue.default', 'sync');
    }

    public function isDatabaseQueue(): bool
    {
        return $this->connection() === 'database';
    }

    public function isWorkerRunning(): bool
    {
        return count($this->findWorkerPids()) > 0;
    }

    public function startWorker(): array
    {
        if (!$this->isDatabaseQueue()) {
            return [
                'ok' => false,
                'message' => 'صف باید database باشد. الان ' . $this->connection() . ' است.',
            ];
        }

        if ($this->isWorkerRunning()) {
            return [
                'ok' => true,
                'message' => 'Worker از قبل فعال است.',
            ];
        }

        $log = storage_path('logs/queue-worker.out.log');
        $pidFile = $this->pidPath();

        if (PHP_OS_FAMILY === 'Windows') {
            $pid = $this->startWindowsWorker($log);
        } else {
            $pid = $this->startUnixWorker($log);
        }

        if ($pid > 0) {
            file_put_contents($pidFile, (string) $pid);
        }

        usleep(400000);

        if ($this->isWorkerRunning()) {
            return [
                'ok' => true,
                'message' => 'Worker فعال شد.',
            ];
        }

        return [
            'ok' => false,
            'message' => 'Worker شروع نشد. لاگ را در storage/logs/queue-worker.out.log ببینید.',
        ];
    }

    public function stopWorker(): array
    {
        $pids = $this->findWorkerPids();
        $stored = $this->storedPid();
        if ($stored > 0 && $this->pidBelongsToThisApp($stored)) {
            $pids[] = $stored;
        }
        $pids = array_values(array_unique(array_filter($pids)));

        if (count($pids) === 0) {
            $this->clearPidFile();
            return [
                'ok' => true,
                'message' => 'Worker در حال اجرا نبود.',
            ];
        }

        foreach ($pids as $pid) {
            if ($this->pidBelongsToThisApp($pid)) {
                $this->killPid($pid);
            }
        }

        usleep(300000);
        $this->clearPidFile();

        return [
            'ok' => !$this->isWorkerRunning(),
            'message' => $this->isWorkerRunning()
                ? 'برخی فرآیندهای worker متوقف نشدند.'
                : 'Worker متوقف شد.',
        ];
    }

    public function pendingJobs(int $limit = 50): array
    {
        if (!Schema::hasTable('jobs')) {
            return [];
        }

        $limit = max(1, min(50, $limit));

        return DB::table('jobs')
            ->orderByDesc('id')
            ->limit($limit)
            ->get()
            ->map(function ($row) {
                return [
                    'id' => $row->id,
                    'queue' => $row->queue,
                    'attempts' => $row->attempts,
                    'name' => $this->jobNameFromPayload($row->payload),
                    'available_at' => $row->available_at
                        ? date('Y-m-d H:i:s', (int) $row->available_at)
                        : '',
                ];
            })
            ->all();
    }

    public function failedJobs(int $limit = 50): array
    {
        if (!Schema::hasTable('failed_jobs')) {
            return [];
        }

        $limit = max(1, min(50, $limit));

        return DB::table('failed_jobs')
            ->orderByDesc('id')
            ->limit($limit)
            ->get()
            ->map(function ($row) {
                $exception = (string) ($row->exception ?? '');
                return [
                    'id' => $row->id,
                    'uuid' => $row->uuid ?? (string) $row->id,
                    'queue' => $row->queue ?? '',
                    'name' => $this->jobNameFromPayload($row->payload ?? ''),
                    'exception' => mb_substr($exception, 0, 280),
                    'failed_at' => $row->failed_at ?? '',
                ];
            })
            ->all();
    }

    public function pendingCount(): int
    {
        return Schema::hasTable('jobs') ? (int) DB::table('jobs')->count() : 0;
    }

    public function failedCount(): int
    {
        return Schema::hasTable('failed_jobs') ? (int) DB::table('failed_jobs')->count() : 0;
    }

    public function retryFailed(string $uuid): array
    {
        $uuid = trim($uuid);
        if (!$this->isValidFailedJobUuid($uuid)) {
            return ['ok' => false, 'message' => 'شناسه نامعتبر است.'];
        }

        if (!Schema::hasTable('failed_jobs')) {
            return ['ok' => false, 'message' => 'جدول جاب‌های ناموفق موجود نیست.'];
        }

        $exists = DB::table('failed_jobs')->where('uuid', $uuid)->exists();
        if (!$exists) {
            return ['ok' => false, 'message' => 'این جاب ناموفق یافت نشد.'];
        }

        try {
            Artisan::call('queue:retry', ['id' => [$uuid]]);
        } catch (\Throwable $e) {
            report($e);
            return ['ok' => false, 'message' => 'تلاش دوباره انجام نشد.'];
        }

        return [
            'ok' => true,
            'message' => 'جاب برای اجرا دوباره در صف قرار گرفت.',
        ];
    }

    public function retryAllFailed(): array
    {
        if ($this->failedCount() === 0) {
            return ['ok' => true, 'message' => 'جاب ناموفقی برای تلاش دوباره وجود ندارد.'];
        }

        try {
            Artisan::call('queue:retry', ['id' => ['all']]);
        } catch (\Throwable $e) {
            report($e);
            return ['ok' => false, 'message' => 'تلاش دوباره همه جاب‌ها انجام نشد.'];
        }

        return [
            'ok' => true,
            'message' => 'همه جاب‌های ناموفق دوباره در صف قرار گرفتند.',
        ];
    }

    private function isValidFailedJobUuid(string $uuid): bool
    {
        return (bool) preg_match(
            '/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$/',
            $uuid
        );
    }

    private function jobNameFromPayload(?string $payload): string
    {
        if (!$payload) {
            return '—';
        }
        $data = json_decode($payload, true);
        if (!is_array($data)) {
            return '—';
        }
        $name = $data['displayName'] ?? ($data['data']['commandName'] ?? '');
        if ($name === '') {
            return '—';
        }

        return class_basename($name);
    }

    private function pidPath(): string
    {
        return storage_path('app/queue-worker.pid');
    }

    private function storedPid(): int
    {
        $path = $this->pidPath();
        if (!is_file($path)) {
            return 0;
        }

        return (int) trim((string) file_get_contents($path));
    }

    private function clearPidFile(): void
    {
        $path = $this->pidPath();
        if (is_file($path)) {
            @unlink($path);
        }
    }

    private function phpCliBinary(): string
    {
        $configured = trim((string) env('QUEUE_PHP_BINARY', ''));
        if ($configured !== '') {
            return $configured;
        }

        $binary = (string) PHP_BINARY;
        if ($binary !== '' && stripos($binary, 'php-cgi') === false && stripos($binary, 'php-fpm') === false) {
            return $binary;
        }

        $candidates = [];
        if (defined('PHP_BINDIR') && PHP_BINDIR) {
            $dir = rtrim((string) PHP_BINDIR, '/\\');
            $candidates[] = $dir . DIRECTORY_SEPARATOR . 'php';
            $candidates[] = $dir . DIRECTORY_SEPARATOR . 'php.exe';
        }
        $candidates[] = '/usr/bin/php8.1';
        $candidates[] = '/usr/bin/php';

        foreach ($candidates as $candidate) {
            if (is_file($candidate) && (PHP_OS_FAMILY === 'Windows' || is_executable($candidate))) {
                return $candidate;
            }
        }

        return PHP_OS_FAMILY === 'Windows' ? 'php.exe' : 'php';
    }

    private function startWindowsWorker(string $log): int
    {
        $php = $this->phpCliBinary();
        $artisan = base_path('artisan');
        $cwd = base_path();
        $argList = implode(',', [
            $this->psQuote($artisan),
            $this->psQuote('queue:work'),
            $this->psQuote('--sleep=3'),
            $this->psQuote('--tries=3'),
        ]);

        $command = 'Start-Process -FilePath ' . $this->psQuote($php)
            . ' -ArgumentList ' . $argList
            . ' -WorkingDirectory ' . $this->psQuote($cwd)
            . ' -WindowStyle Hidden'
            . ' -RedirectStandardOutput ' . $this->psQuote($log)
            . ' -RedirectStandardError ' . $this->psQuote($log)
            . ' -PassThru | Select-Object -ExpandProperty Id';

        $output = [];
        exec('powershell -NoProfile -NonInteractive -Command ' . escapeshellarg($command), $output);

        return isset($output[0]) ? (int) trim($output[0]) : 0;
    }

    private function startUnixWorker(string $log): int
    {
        $cmd = sprintf(
            'nohup %s %s queue:work --sleep=3 --tries=3 >> %s 2>&1 & echo $!',
            escapeshellarg($this->phpCliBinary()),
            escapeshellarg(base_path('artisan')),
            escapeshellarg($log)
        );
        $output = [];
        exec($cmd, $output);

        return isset($output[0]) ? (int) trim($output[0]) : 0;
    }

    private function findWorkerPids(): array
    {
        $pids = [];

        if (PHP_OS_FAMILY === 'Windows') {
            $output = [];
            exec(
                'powershell -NoProfile -NonInteractive -Command "Get-CimInstance Win32_Process | Where-Object { $_.CommandLine -like \'*queue:work*\' } | ForEach-Object { $_.ProcessId.ToString() + \'`t\' + $_.CommandLine }"',
                $output
            );
            foreach ($output as $line) {
                $parts = explode("\t", $line, 2);
                $pid = (int) trim($parts[0] ?? '');
                $commandLine = (string) ($parts[1] ?? '');
                if ($pid > 0 && $this->commandLineBelongsToThisApp($commandLine)) {
                    $pids[] = $pid;
                }
            }
        } else {
            $output = [];
            exec("ps -eo pid,args", $output);
            foreach ($output as $line) {
                if (!preg_match('/^\s*(\d+)\s+(.+)$/', $line, $m)) {
                    continue;
                }
                $pid = (int) $m[1];
                $commandLine = $m[2];
                if ($pid > 0 && $this->commandLineBelongsToThisApp($commandLine)) {
                    $pids[] = $pid;
                }
            }
        }

        return array_values(array_unique($pids));
    }

    private function pidBelongsToThisApp(int $pid): bool
    {
        if ($pid <= 0) {
            return false;
        }

        $commandLine = $this->processCommandLine($pid);

        return $this->commandLineBelongsToThisApp($commandLine);
    }

    private function processCommandLine(int $pid): string
    {
        $output = [];

        if (PHP_OS_FAMILY === 'Windows') {
            exec(
                'powershell -NoProfile -NonInteractive -Command ' . escapeshellarg(
                    'Get-CimInstance Win32_Process -Filter "ProcessId=' . $pid . '" | Select-Object -ExpandProperty CommandLine'
                ),
                $output
            );
        } else {
            exec('ps -p ' . $pid . ' -o args=', $output);
        }

        return trim(implode(' ', $output));
    }

    private function commandLineBelongsToThisApp(string $commandLine): bool
    {
        if ($commandLine === '' || stripos($commandLine, 'queue:work') === false) {
            return false;
        }

        $artisan = base_path('artisan');
        $markers = [
            $artisan,
            str_replace('/', '\\', $artisan),
            str_replace('\\', '/', $artisan),
        ];

        foreach ($markers as $marker) {
            if ($marker !== '' && stripos($commandLine, $marker) !== false) {
                return true;
            }
        }

        return false;
    }

    private function killPid(int $pid): void
    {
        if ($pid <= 0) {
            return;
        }

        if (PHP_OS_FAMILY === 'Windows') {
            exec('taskkill /PID ' . $pid . ' /F');
        } else {
            exec('kill ' . $pid);
        }
    }

    private function psQuote(string $value): string
    {
        return "'" . str_replace("'", "''", $value) . "'";
    }
}
