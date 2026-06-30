<?php

namespace App\Http\Controllers;

use App\Models\BackupJob;
use App\Models\BackupLog;
use App\Models\BackupSystem;
use App\Models\BackupStorage;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = today();

        $activeJobs = BackupJob::query()
            ->with(['system.storage'])
            ->where('is_active', true)
            ->whereHas('system', function ($query) {
                $query->where('is_active', true);
            })
            ->orderBy('name')
            ->get();

        $activeJobIds = $activeJobs->pluck('id');

        $todayLogs = BackupLog::query()
            ->with(['job', 'system', 'storage'])
            ->whereDate('backup_date', $today)
            ->whereIn('backup_job_id', $activeJobIds)
            ->orderByDesc('finished_at')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();

        $latestLogByJob = $todayLogs
            ->unique('backup_job_id')
            ->keyBy('backup_job_id');

        $jobStatusRows = $activeJobs->map(function (BackupJob $job) use ($latestLogByJob) {
            $latestLog = $latestLogByJob->get($job->id);

            return [
                'job' => $job,
                'latestLog' => $latestLog,
                'status' => $latestLog?->status ?? 'pending',
            ];
        });

        $pendingRows = $jobStatusRows
            ->where('status', 'pending')
            ->values();

        $problemLogs = BackupLog::query()
            ->with(['job', 'system', 'storage'])
            ->whereIn('status', [
                BackupLog::STATUS_FAILED,
                BackupLog::STATUS_WARNING,
            ])
            ->orderByDesc('finished_at')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        $latestActivities = BackupLog::query()
            ->with(['job', 'system', 'storage'])
            ->orderByDesc('finished_at')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        $storages = BackupStorage::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $totalStorageBytes = $storages->sum(fn ($storage) => (int) ($storage->total_space_bytes ?? 0));
        $usedStorageBytes = $storages->sum(fn ($storage) => (int) ($storage->used_space_bytes ?? 0));
        $freeStorageBytes = $storages->sum(fn ($storage) => (int) ($storage->free_space_bytes ?? 0));

        $storageOverallUsagePercent = $totalStorageBytes > 0
            ? round(($usedStorageBytes / $totalStorageBytes) * 100, 2)
            : null;

        $storageOfflineCount = $storages
            ->where('last_check_status', BackupStorage::CHECK_OFFLINE)
            ->count();

        $storageOnlineCount = $storages
            ->where('last_check_status', BackupStorage::CHECK_ONLINE)
            ->count();

        $storageCriticalCount = $storages
            ->filter(fn ($storage) => $storage->last_check_status !== BackupStorage::CHECK_OFFLINE
                && $storage->usage_percent !== null
                && (float) $storage->usage_percent >= 90)
            ->count();

        $storageWarningCount = $storages
            ->filter(fn ($storage) => $storage->last_check_status !== BackupStorage::CHECK_OFFLINE
                && $storage->usage_percent !== null
                && (float) $storage->usage_percent >= 75
                && (float) $storage->usage_percent < 90)
            ->count();

        $storageNeedAttentionCount = $storageOfflineCount + $storageCriticalCount;

        $attentionStorages = $storages
            ->filter(fn ($storage) => $storage->last_check_status === BackupStorage::CHECK_OFFLINE
                || (
                    $storage->usage_percent !== null
                    && (float) $storage->usage_percent >= 75
                ))
            ->sortByDesc(fn ($storage) => (float) ($storage->usage_percent ?? 0))
            ->take(3)
            ->values();

        $storageSummary = [
            'active' => $storages->count(),
            'online' => $storageOnlineCount,
            'offline' => $storageOfflineCount,
            'critical' => $storageCriticalCount,
            'warning' => $storageWarningCount,
            'need_attention' => $storageNeedAttentionCount,
            'total_label' => $this->formatBytes($totalStorageBytes),
            'used_label' => $this->formatBytes($usedStorageBytes),
            'free_label' => $this->formatBytes($freeStorageBytes),
            'overall_usage_percent' => $storageOverallUsagePercent,
        ];

        $stats = [
            'active_jobs' => $activeJobs->count(),
            'active_systems' => BackupSystem::query()
                ->where('is_active', true)
                ->count(),
            'success_today' => $jobStatusRows
                ->where('status', BackupLog::STATUS_SUCCESS)
                ->count(),
            'failed_today' => $jobStatusRows
                ->where('status', BackupLog::STATUS_FAILED)
                ->count(),
            'warning_today' => $jobStatusRows
                ->where('status', BackupLog::STATUS_WARNING)
                ->count(),
            'pending_today' => $pendingRows->count(),
        ];

        $overallStatus = 'success';
        $overallStatusLabel = 'Backup hari ini aman';
        $overallStatusMessage = 'Tidak ada failed, warning, atau pending pada job aktif hari ini.';

        if ($stats['failed_today'] > 0) {
            $overallStatus = 'failed';
            $overallStatusLabel = 'Ada backup gagal';
            $overallStatusMessage = 'Ada backup failed yang perlu segera dicek.';
        } elseif ($stats['warning_today'] > 0) {
            $overallStatus = 'warning';
            $overallStatusLabel = 'Ada backup warning';
            $overallStatusMessage = 'Ada backup warning yang perlu diperiksa.';
        } elseif ($stats['pending_today'] > 0) {
            $overallStatus = 'pending';
            $overallStatusLabel = 'Ada backup belum berjalan';
            $overallStatusMessage = 'Ada job aktif yang belum mengirim log backup hari ini.';
        }   

        $groupedJobStatusRows = [
            'success' => $jobStatusRows->where('status', BackupLog::STATUS_SUCCESS)->values(),
            'failed' => $jobStatusRows->where('status', BackupLog::STATUS_FAILED)->values(),
            'warning' => $jobStatusRows->where('status', BackupLog::STATUS_WARNING)->values(),
            'pending' => $jobStatusRows->where('status', 'pending')->values(),
        ];

        $attentionProblemLogs = $problemLogs->take(3);
        $attentionPendingRows = $pendingRows->take(3);

        return view('dashboard', [
            'today' => $today,
            'stats' => $stats,
            'jobStatusRows' => $jobStatusRows,
            'groupedJobStatusRows' => $groupedJobStatusRows,
            'pendingRows' => $pendingRows,
            'problemLogs' => $problemLogs,
            'latestActivities' => $latestActivities,
            'attentionProblemLogs' => $attentionProblemLogs,
            'attentionPendingRows' => $attentionPendingRows,
            'overallStatus' => $overallStatus,
            'overallStatusLabel' => $overallStatusLabel,
            'overallStatusMessage' => $overallStatusMessage,
            'storageSummary' => $storageSummary,
            'attentionStorages' => $attentionStorages,
        ]);
    }

    private function formatBytes(?int $bytes): string
    {
        if ($bytes === null) {
            return '-';
        }

        if ($bytes <= 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        $power = min((int) floor(log($bytes, 1024)), count($units) - 1);
        $value = $bytes / (1024 ** $power);

        return number_format($value, $value >= 10 ? 0 : 1) . ' ' . $units[$power];
    }
}