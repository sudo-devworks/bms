<?php

namespace App\Console\Commands;

use App\Mail\BackupAlertMail;
use App\Models\BackupAlert;
use App\Models\BackupJob;
use App\Models\BackupLog;
use App\Models\BackupStorage;
use App\Models\NotificationSetting;
use App\Models\SystemMonitor;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Throwable;

class CheckBackupAlerts extends Command
{
    protected $signature = 'bms:check-alerts {--no-email : Only create alert records without sending email}';

    protected $description = 'Check backup logs and storage status, then create alert records and send notifications.';

    public function handle(): int
    {
        try {
            $today = now()->toDateString();

            $createdCount = 0;
            $resolvedCount = 0;

            $resolvedCount += $this->autoResolveRecoveredAlerts($today);

            $createdCount += $this->createBackupLogAlerts($today);
            $createdCount += $this->createPendingJobAlerts($today);
            $createdCount += $this->createStorageAlerts();

            $this->info("Alert check selesai. Alert baru dibuat: {$createdCount}. Alert auto-resolved: {$resolvedCount}");

            if ($this->option('no-email')) {
                SystemMonitor::updateAlertChecker(
                    SystemMonitor::STATUS_OK,
                    "Alert checker selesai. Alert baru dibuat: {$createdCount}. Alert auto-resolved: {$resolvedCount}. Email dilewati karena option --no-email.",
                    [
                        'created_alerts' => $createdCount,
                        'resolved_alerts' => $resolvedCount,
                        'email_sent' => 0,
                        'email_failed' => 0,
                        'email_skipped' => 0,
                        'no_email' => true,
                    ]
                );

                $this->warn('Pengiriman email dilewati karena option --no-email digunakan.');

                return self::SUCCESS;
            }

            $emailResult = $this->sendNewAlertEmails();

            $this->info("Email alert terkirim: {$emailResult['sent']}");
            $this->warn("Email alert gagal: {$emailResult['failed']}");
            $this->line("Email alert dilewati: {$emailResult['skipped']}");

            $monitorStatus = $emailResult['failed'] > 0
                ? SystemMonitor::STATUS_WARNING
                : SystemMonitor::STATUS_OK;

            SystemMonitor::updateAlertChecker(
                $monitorStatus,
                "Alert checker selesai. Alert baru dibuat: {$createdCount}. Email terkirim: {$emailResult['sent']}, gagal: {$emailResult['failed']}, dilewati: {$emailResult['skipped']}.",
                [
                    'created_alerts' => $createdCount,
                    'email_sent' => $emailResult['sent'],
                    'email_failed' => $emailResult['failed'],
                    'email_skipped' => $emailResult['skipped'],
                    'no_email' => false,
                ]
            );

            return self::SUCCESS;
        } catch (Throwable $e) {
            SystemMonitor::updateAlertChecker(
                SystemMonitor::STATUS_FAILED,
                'Alert checker gagal: ' . $e->getMessage(),
                [
                    'error' => $e->getMessage(),
                ]
            );

            $this->error('Alert checker gagal: ' . $e->getMessage());

            return self::FAILURE;
        }
    }

    private function autoResolveRecoveredAlerts(string $today): int
    {
        $resolvedCount = 0;

        $resolvedCount += $this->autoResolvePendingJobAlerts($today);
        $resolvedCount += $this->autoResolveStorageAlerts();
        $resolvedCount += $this->autoResolveBackupLogAlerts($today);

        return $resolvedCount;
    }

    private function createBackupLogAlerts(string $today): int
    {
        $createdCount = 0;

        $logs = BackupLog::query()
            ->with(['job', 'system', 'storage'])
            ->whereDate('backup_date', $today)
            ->whereIn('status', [
                BackupLog::STATUS_FAILED,
                BackupLog::STATUS_WARNING,
            ])
            ->latest('finished_at')
            ->latest('created_at')
            ->get();

        foreach ($logs as $log) {
            $type = $log->status === BackupLog::STATUS_FAILED
                ? BackupAlert::TYPE_BACKUP_FAILED
                : BackupAlert::TYPE_BACKUP_WARNING;

            $severity = $log->status === BackupLog::STATUS_FAILED
                ? BackupAlert::SEVERITY_CRITICAL
                : BackupAlert::SEVERITY_WARNING;

            $exists = BackupAlert::query()
                ->where('type', $type)
                ->where('backup_log_id', $log->id)
                ->exists();

            if ($exists) {
                continue;
            }

            BackupAlert::create([
                'type' => $type,
                'severity' => $severity,
                'title' => $log->status === BackupLog::STATUS_FAILED
                    ? 'Backup gagal'
                    : 'Backup warning',
                'message' => $this->buildBackupLogMessage($log),
                'backup_log_id' => $log->id,
                'backup_job_id' => $log->backup_job_id,
                'backup_system_id' => $log->backup_system_id,
                'backup_storage_id' => $log->backup_storage_id,
                'status' => BackupAlert::STATUS_NEW,
                'triggered_at' => $log->finished_at ?? $log->created_at ?? now(),
            ]);

            $createdCount++;
        }

        return $createdCount;
    }

    private function createPendingJobAlerts(string $today): int
    {
        $createdCount = 0;

        $activeJobs = BackupJob::query()
            ->with(['system', 'storage'])
            ->where('is_active', true)
            ->get();

        foreach ($activeJobs as $job) {
            if (! $job->isPendingAlertDue()) {
                continue;
            }
            $hasLogToday = BackupLog::query()
                ->where('backup_job_id', $job->id)
                ->whereDate('backup_date', $today)
                ->exists();

            if ($hasLogToday) {
                continue;
            }

            $alreadyOpen = BackupAlert::query()
                ->where('type', BackupAlert::TYPE_JOB_PENDING)
                ->where('backup_job_id', $job->id)
                ->whereDate('triggered_at', $today)
                ->whereNotIn('status', [
                    BackupAlert::STATUS_RESOLVED,
                    BackupAlert::STATUS_IGNORED,
                ])
                ->exists();

            if ($alreadyOpen) {
                continue;
            }

            BackupAlert::create([
                'type' => BackupAlert::TYPE_JOB_PENDING,
                'severity' => BackupAlert::SEVERITY_WARNING,
                'title' => 'Backup job belum memiliki log hari ini',
                'message' => $this->buildPendingJobMessage($job),
                'backup_job_id' => $job->id,
                'backup_system_id' => $job->backup_system_id,
                'backup_storage_id' => $job->backup_storage_id,
                'status' => BackupAlert::STATUS_NEW,
                'triggered_at' => now(),
            ]);

            $createdCount++;
        }

        return $createdCount;
    }

    private function createStorageAlerts(): int
    {
        $createdCount = 0;

        $storages = BackupStorage::query()
            ->where('is_active', true)
            ->get();

        foreach ($storages as $storage) {
            $health = $storage->healthLabel();

            if (! in_array($health, ['Kritis', 'Offline'], true)) {
                continue;
            }

            $type = $health === 'Offline'
                ? BackupAlert::TYPE_STORAGE_OFFLINE
                : BackupAlert::TYPE_STORAGE_CRITICAL;

            $alreadyOpen = BackupAlert::query()
                ->where('type', $type)
                ->where('backup_storage_id', $storage->id)
                ->whereNotIn('status', [
                    BackupAlert::STATUS_RESOLVED,
                    BackupAlert::STATUS_IGNORED,
                ])
                ->exists();

            if ($alreadyOpen) {
                continue;
            }

            BackupAlert::create([
                'type' => $type,
                'severity' => BackupAlert::SEVERITY_CRITICAL,
                'title' => $health === 'Offline'
                    ? 'Storage backup offline'
                    : 'Storage backup kritis',
                'message' => $this->buildStorageMessage($storage, $health),
                'backup_storage_id' => $storage->id,
                'status' => BackupAlert::STATUS_NEW,
                'triggered_at' => now(),
            ]);

            $createdCount++;
        }

        return $createdCount;
    }

    private function sendNewAlertEmails(): array
    {
        $result = [
            'sent' => 0,
            'failed' => 0,
            'skipped' => 0,
        ];

        $recipients = NotificationSetting::query()
            ->where('channel', NotificationSetting::CHANNEL_EMAIL)
            ->where('is_active', true)
            ->get();

        if ($recipients->isEmpty()) {
            $newAlertsCount = BackupAlert::query()
                ->where('status', BackupAlert::STATUS_NEW)
                ->count();

            $result['skipped'] = $newAlertsCount;

            $this->warn('Tidak ada recipient email aktif. Alert tetap berstatus new.');

            return $result;
        }

        $alerts = BackupAlert::query()
            ->with(['log', 'job', 'system', 'storage'])
            ->where('status', BackupAlert::STATUS_NEW)
            ->latest('triggered_at')
            ->latest('created_at')
            ->get();

        foreach ($alerts as $alert) {
            try {
                foreach ($recipients as $recipient) {
                    Mail::to($recipient->recipient_email)
                        ->send(new BackupAlertMail($alert));
                }

                $alert->update([
                    'status' => BackupAlert::STATUS_SENT,
                    'sent_at' => now(),
                ]);

                $result['sent']++;
            } catch (Throwable $e) {
                $alert->update([
                    'status' => BackupAlert::STATUS_FAILED,
                ]);

                $this->error('Gagal kirim email alert ID ' . $alert->id . ': ' . $e->getMessage());

                $result['failed']++;
            }
        }

        return $result;
    }

    private function buildBackupLogMessage(BackupLog $log): string
    {
        $parts = [];

        $parts[] = 'Job: ' . ($log->job->name ?? '-');
        $parts[] = 'Sistem: ' . ($log->system->name ?? '-');
        $parts[] = 'Storage: ' . ($log->storage->name ?? '-');
        $parts[] = 'Status: ' . $log->statusLabel();

        if ($log->finished_at) {
            $parts[] = 'Waktu selesai: ' . $log->finished_at->format('d M Y H:i');
        }

        if ($log->file_size_bytes) {
            $parts[] = 'Ukuran file: ' . $log->fileSizeLabel();
        }

        $message = $log->error_message ?: $log->message;

        if ($message) {
            $parts[] = 'Pesan: ' . $message;
        }

        return implode("\n", $parts);
    }

    private function buildPendingJobMessage(BackupJob $job): string
    {
        $parts = [];

        $parts[] = 'Job: ' . $job->name;
        $parts[] = 'Kode: ' . $job->code;
        $parts[] = 'Sistem: ' . ($job->system->name ?? '-');
        $parts[] = 'Storage: ' . ($job->storage->name ?? '-');
        $parts[] = 'Keterangan: Belum ada log backup yang masuk ke BMS untuk hari ini.';
        $parts[] = 'Jam backup normal: ' . $job->expectedRunTimeLabel();
        $parts[] = 'Alert pending setelah: ' . $job->pendingAlertTimeLabel();
        $parts[] = 'Toleransi: ' . ($job->alert_after_minutes ?? 60) . ' menit';

        return implode("\n", $parts);
    }

    private function buildStorageMessage(BackupStorage $storage, string $health): string
    {
        $parts = [];

        $parts[] = 'Storage: ' . $storage->name;
        $parts[] = 'Health: ' . $health;
        $parts[] = 'Usage: ' . $storage->usagePercentLabel();

        if (method_exists($storage, 'capacitySummaryLabel')) {
            $parts[] = 'Kapasitas: ' . $storage->capacitySummaryLabel();
        }

        if ($storage->last_checked_at) {
            $parts[] = 'Last checked: ' . $storage->last_checked_at->format('d M Y H:i');
        }

        if ($storage->last_check_message) {
            $parts[] = 'Pesan: ' . $storage->last_check_message;
        }

        return implode("\n", $parts);
    }

    private function autoResolvePendingJobAlerts(string $today): int
    {
        $resolvedCount = 0;

        $alerts = BackupAlert::query()
            ->where('type', BackupAlert::TYPE_JOB_PENDING)
            ->whereNotIn('status', [
                BackupAlert::STATUS_RESOLVED,
                BackupAlert::STATUS_IGNORED,
            ])
            ->whereNotNull('backup_job_id')
            ->get();

        foreach ($alerts as $alert) {
            $hasLogToday = BackupLog::query()
                ->where('backup_job_id', $alert->backup_job_id)
                ->whereDate('backup_date', $today)
                ->exists();

            if (! $hasLogToday) {
                continue;
            }

            $alert->update([
                'status' => BackupAlert::STATUS_RESOLVED,
                'resolved_at' => now(),
                'message' => trim(($alert->message ?? '') . "\n\nAuto resolved: log backup sudah masuk ke BMS."),
            ]);

            $resolvedCount++;
        }

        return $resolvedCount;
    }

    private function autoResolveStorageAlerts(): int
    {
        $resolvedCount = 0;

        $alerts = BackupAlert::query()
            ->with('storage')
            ->whereIn('type', [
                BackupAlert::TYPE_STORAGE_CRITICAL,
                BackupAlert::TYPE_STORAGE_OFFLINE,
            ])
            ->whereNotIn('status', [
                BackupAlert::STATUS_RESOLVED,
                BackupAlert::STATUS_IGNORED,
            ])
            ->whereNotNull('backup_storage_id')
            ->get();

        foreach ($alerts as $alert) {
            $storage = $alert->storage;

            if (! $storage) {
                continue;
            }

            $health = $storage->healthLabel();

            if (in_array($health, ['Kritis', 'Offline'], true)) {
                continue;
            }

            $alert->update([
                'status' => BackupAlert::STATUS_RESOLVED,
                'resolved_at' => now(),
                'message' => trim(($alert->message ?? '') . "\n\nAuto resolved: status storage sekarang {$health}."),
            ]);

            $resolvedCount++;
        }

        return $resolvedCount;
    }

    private function autoResolveBackupLogAlerts(string $today): int
    {
        $resolvedCount = 0;

        $alerts = BackupAlert::query()
            ->with('log')
            ->whereIn('type', [
                BackupAlert::TYPE_BACKUP_FAILED,
                BackupAlert::TYPE_BACKUP_WARNING,
            ])
            ->whereNotIn('status', [
                BackupAlert::STATUS_RESOLVED,
                BackupAlert::STATUS_IGNORED,
            ])
            ->whereNotNull('backup_job_id')
            ->get();

        foreach ($alerts as $alert) {
            $alertLog = $alert->log;

            $successQuery = BackupLog::query()
                ->where('backup_job_id', $alert->backup_job_id)
                ->whereDate('backup_date', $today)
                ->where('status', BackupLog::STATUS_SUCCESS);

            if ($alertLog) {
                $baseTime = $alertLog->finished_at ?? $alertLog->created_at;

                if ($baseTime) {
                    $successQuery->where(function ($query) use ($baseTime) {
                        $query
                            ->where('finished_at', '>', $baseTime)
                            ->orWhere(function ($subQuery) use ($baseTime) {
                                $subQuery
                                    ->whereNull('finished_at')
                                    ->where('created_at', '>', $baseTime);
                            });
                    });
                }
            }

            $hasNewerSuccess = $successQuery->exists();

            if (! $hasNewerSuccess) {
                continue;
            }

            $alert->update([
                'status' => BackupAlert::STATUS_RESOLVED,
                'resolved_at' => now(),
                'message' => trim(($alert->message ?? '') . "\n\nAuto resolved: job yang sama sudah memiliki log success yang lebih baru."),
            ]);

            $resolvedCount++;
        }

        return $resolvedCount;
    }
}