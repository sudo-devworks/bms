<?php

namespace App\Http\Controllers;

use App\Models\BackupJob;
use App\Models\BackupLog;
use App\Models\BackupStorage;
use App\Models\BackupSystem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Exports\BackupReportExport;
use Maatwebsite\Excel\Facades\Excel;

class BackupReportController extends Controller
{
    public function index(Request $request)
    {
        $query = $this->filteredQuery($request)
            ->with(['job', 'system', 'storage'])
            ->latest('backup_date')
            ->latest('started_at')
            ->latest('id');

        $summaryQuery = $this->filteredQuery($request);

        $summaryRow = $summaryQuery
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = "success" THEN 1 ELSE 0 END) as success,
                SUM(CASE WHEN status = "failed" THEN 1 ELSE 0 END) as failed,
                SUM(CASE WHEN status = "warning" THEN 1 ELSE 0 END) as warning,
                COALESCE(SUM(file_size_bytes), 0) as total_size_bytes,
                AVG(duration_seconds) as avg_duration_seconds
            ')
            ->first();

        $summary = [
            'total' => (int) ($summaryRow->total ?? 0),
            'success' => (int) ($summaryRow->success ?? 0),
            'failed' => (int) ($summaryRow->failed ?? 0),
            'warning' => (int) ($summaryRow->warning ?? 0),
            'total_size_label' => $this->formatBytes((int) ($summaryRow->total_size_bytes ?? 0)),
            'avg_duration_label' => $this->formatDuration($summaryRow->avg_duration_seconds),
        ];

        $pendingDate = $request->input('date_to', now()->toDateString());

        $pendingJobs = BackupJob::query()
            ->with(['system'])
            ->where('is_active', true)
            ->where('expected_frequency', '!=', BackupJob::FREQUENCY_MANUAL)
            ->whereHas('system', function (Builder $query) {
                $query->where('is_active', true);
            })
            ->whereDoesntHave('logs', function (Builder $query) use ($pendingDate) {
                $query->whereDate('backup_date', $pendingDate);
            })
            ->orderBy('expected_time')
            ->orderBy('name')
            ->get();

        return view('backup-reports.index', [
            'logs' => $query->paginate(25)->withQueryString(),
            'summary' => $summary,
            'systems' => BackupSystem::query()->orderBy('name')->get(),
            'jobs' => BackupJob::query()->orderBy('name')->get(),
            'storages' => BackupStorage::query()->orderBy('name')->get(),
            'statuses' => BackupLog::statuses(),
            'pendingDate' => $pendingDate,
            'pendingJobs' => $pendingJobs,
        ]);
    }

    public function export(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->toDateString());
        $dateTo = $request->input('date_to', now()->toDateString());

        $fileName = 'backup-report-'.$dateFrom.'-to-'.$dateTo.'.xlsx';

        $query = $this->filteredQuery($request)
            ->with(['job', 'system', 'storage'])
            ->latest('backup_date')
            ->latest('started_at')
            ->latest('id');

        $pendingJobs = BackupJob::query()
            ->with(['system'])
            ->where('is_active', true)
            ->where('expected_frequency', '!=', BackupJob::FREQUENCY_MANUAL)
            ->whereHas('system', function (Builder $query) {
                $query->where('is_active', true);
            })
            ->whereDoesntHave('logs', function (Builder $query) use ($dateTo) {
                $query->whereDate('backup_date', $dateTo);
            })
            ->orderBy('expected_time')
            ->orderBy('name')
            ->get();

        return Excel::download(
            new BackupReportExport(
                $query,
                [
                    'date_from' => $dateFrom,
                    'date_to' => $dateTo,
                ],
                $pendingJobs
            ),
            $fileName
        );
    }

    private function filteredQuery(Request $request): Builder
    {
        return BackupLog::query()
            ->when($request->filled('search'), function (Builder $query) use ($request) {
                $search = $request->string('search')->toString();

                $query->where(function (Builder $query) use ($search) {
                    $query
                        ->where('file_name', 'like', "%{$search}%")
                        ->orWhere('file_path', 'like', "%{$search}%")
                        ->orWhere('message', 'like', "%{$search}%")
                        ->orWhere('error_message', 'like', "%{$search}%")
                        ->orWhereHas('job', function (Builder $query) use ($search) {
                            $query
                                ->where('name', 'like', "%{$search}%")
                                ->orWhere('code', 'like', "%{$search}%");
                        })
                        ->orWhereHas('system', function (Builder $query) use ($search) {
                            $query
                                ->where('name', 'like', "%{$search}%")
                                ->orWhere('code', 'like', "%{$search}%");
                        })
                        ->orWhereHas('storage', function (Builder $query) use ($search) {
                            $query
                                ->where('name', 'like', "%{$search}%")
                                ->orWhere('base_path', 'like', "%{$search}%");
                        });
                });
            })
            ->whereDate('backup_date', '>=', $request->input('date_from', now()->toDateString()))
            ->whereDate('backup_date', '<=', $request->input('date_to', now()->toDateString())) 
            
            ->when($request->filled('backup_system_id'), function (Builder $query) use ($request) {
                $query->where('backup_system_id', $request->integer('backup_system_id'));
            })
            ->when($request->filled('backup_job_id'), function (Builder $query) use ($request) {
                $query->where('backup_job_id', $request->integer('backup_job_id'));
            })
            ->when($request->filled('backup_storage_id'), function (Builder $query) use ($request) {
                $query->where('backup_storage_id', $request->integer('backup_storage_id'));
            })
            ->when($request->filled('status'), function (Builder $query) use ($request) {
                $query->where('status', $request->string('status')->toString());
            });
    }

    private function formatBytes(?int $bytes): string
    {
        if (!$bytes) {
            return '-';
        }

        $size = (float) $bytes;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $index = 0;

        while ($size >= 1024 && $index < count($units) - 1) {
            $size /= 1024;
            $index++;
        }

        return round($size, $index === 0 ? 0 : 2).' '.$units[$index];
    }

    private function formatDuration($seconds): string
    {
        if ($seconds === null) {
            return '-';
        }

        $seconds = (int) round($seconds);

        $hours = intdiv($seconds, 3600);
        $minutes = intdiv($seconds % 3600, 60);
        $seconds = $seconds % 60;

        if ($hours > 0) {
            return sprintf('%d jam %d menit %d detik', $hours, $minutes, $seconds);
        }

        if ($minutes > 0) {
            return sprintf('%d menit %d detik', $minutes, $seconds);
        }

        return sprintf('%d detik', $seconds);
    }
}