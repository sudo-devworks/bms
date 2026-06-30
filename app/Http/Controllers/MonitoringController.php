<?php

namespace App\Http\Controllers;

use App\Models\BackupJob;
use App\Models\BackupLog;
use App\Models\BackupStorage;
use App\Models\BackupAlert;
use App\Models\SystemMonitor;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class MonitoringController extends Controller
{
    public function index(Request $request)
    {
        $today = now()->toDateString();

        $activeJobs = BackupJob::query()
            ->with([
                'system',
                'logs' => function ($query) use ($today) {
                    $query
                        ->whereDate('backup_date', $today)
                        ->latest('finished_at')
                        ->latest('created_at');
                },
            ])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $jobBoards = $activeJobs->map(function (BackupJob $job) {
            $latestTodayLog = $job->logs->first();

            return [
                'job' => $job,
                'system' => $job->system,
                'latest_log' => $latestTodayLog,
                'status' => $latestTodayLog?->status ?? 'pending',
                'status_label' => $latestTodayLog?->statusLabel() ?? 'Pending',
                'time_label' => $latestTodayLog?->finished_at?->format('H:i') ?? '-',
                'message' => $latestTodayLog?->error_message
                    ?: $latestTodayLog?->message
                    ?: 'Belum ada log backup hari ini.',
            ];
        });

        $summary = [
            'total_jobs' => $jobBoards->count(),
            'success' => $jobBoards->where('status', BackupLog::STATUS_SUCCESS)->count(),
            'warning' => $jobBoards->where('status', BackupLog::STATUS_WARNING)->count(),
            'failed' => $jobBoards->where('status', BackupLog::STATUS_FAILED)->count(),
            'pending' => $jobBoards->where('status', 'pending')->count(),
        ];

        $latestActivities = BackupLog::query()
            ->with(['job', 'system', 'storage'])
            ->latest('finished_at')
            ->latest('created_at')
            ->limit(8)
            ->get();

        $storages = BackupStorage::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $storageSummary = [
            'total' => $storages->count(),
            'healthy' => $storages->filter(fn ($storage) => $storage->healthLabel() === 'Sehat')->count(),
            'attention' => $storages->filter(fn ($storage) => $storage->healthLabel() === 'Perhatian')->count(),
            'critical' => $storages->filter(fn ($storage) => $storage->healthLabel() === 'Kritis')->count(),
            'offline' => $storages->filter(fn ($storage) => $storage->healthLabel() === 'Offline')->count(),
        ];

        $activeAlerts = BackupAlert::query()
            ->with(['job', 'system', 'storage'])
            ->whereNotIn('status', [
                BackupAlert::STATUS_RESOLVED,
                BackupAlert::STATUS_IGNORED,
            ])
            ->latest('triggered_at')
            ->latest('created_at')
            ->limit(6)
            ->get();

        $activeAlertSummary = [
            'total' => $activeAlerts->count(),
            'critical' => $activeAlerts
                ->where('severity', BackupAlert::SEVERITY_CRITICAL)
                ->count(),
            'warning' => $activeAlerts
                ->where('severity', BackupAlert::SEVERITY_WARNING)
                ->count(),
        ];

        $hasProblem = $summary['failed'] > 0
            || $summary['warning'] > 0
            || $summary['pending'] > 0
            || $storageSummary['critical'] > 0
            || $storageSummary['offline'] > 0
            || $activeAlertSummary['total'] > 0;

        $alertCheckerMonitor = SystemMonitor::query()
            ->where('key', SystemMonitor::KEY_ALERT_CHECKER)
            ->first();

        return view('monitoring.index', [
            'today' => Carbon::parse($today),
            'summary' => $summary,
            'jobBoards' => $jobBoards,
            'latestActivities' => $latestActivities,
            'storages' => $storages,
            'storageSummary' => $storageSummary,
            'hasProblem' => $hasProblem,
            'lastRefreshedAt' => now(),
            'activeAlerts' => $activeAlerts,
            'activeAlertSummary' => $activeAlertSummary,
            'alertCheckerMonitor' => $alertCheckerMonitor,
        ]);
    }
}