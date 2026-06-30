<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BackupAlert extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'severity',
        'title',
        'message',
        'backup_log_id',
        'backup_job_id',
        'backup_system_id',
        'backup_storage_id',
        'status',
        'triggered_at',
        'sent_at',
        'resolved_at',
    ];

    protected $casts = [
        'triggered_at' => 'datetime',
        'sent_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public const TYPE_BACKUP_FAILED = 'backup_failed';
    public const TYPE_BACKUP_WARNING = 'backup_warning';
    public const TYPE_JOB_PENDING = 'job_pending';
    public const TYPE_STORAGE_CRITICAL = 'storage_critical';
    public const TYPE_STORAGE_OFFLINE = 'storage_offline';

    public const SEVERITY_INFO = 'info';
    public const SEVERITY_WARNING = 'warning';
    public const SEVERITY_CRITICAL = 'critical';

    public const STATUS_NEW = 'new';
    public const STATUS_SENT = 'sent';
    public const STATUS_FAILED = 'failed';
    public const STATUS_RESOLVED = 'resolved';
    public const STATUS_IGNORED = 'ignored';

    public static function types(): array
    {
        return [
            self::TYPE_BACKUP_FAILED => 'Backup Failed',
            self::TYPE_BACKUP_WARNING => 'Backup Warning',
            self::TYPE_JOB_PENDING => 'Job Pending',
            self::TYPE_STORAGE_CRITICAL => 'Storage Critical',
            self::TYPE_STORAGE_OFFLINE => 'Storage Offline',
        ];
    }

    public static function severities(): array
    {
        return [
            self::SEVERITY_INFO => 'Info',
            self::SEVERITY_WARNING => 'Warning',
            self::SEVERITY_CRITICAL => 'Critical',
        ];
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_NEW => 'New',
            self::STATUS_SENT => 'Sent',
            self::STATUS_FAILED => 'Failed',
            self::STATUS_RESOLVED => 'Resolved',
            self::STATUS_IGNORED => 'Ignored',
        ];
    }

    public function log(): BelongsTo
    {
        return $this->belongsTo(BackupLog::class, 'backup_log_id');
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(BackupJob::class, 'backup_job_id');
    }

    public function system(): BelongsTo
    {
        return $this->belongsTo(BackupSystem::class, 'backup_system_id');
    }

    public function storage(): BelongsTo
    {
        return $this->belongsTo(BackupStorage::class, 'backup_storage_id');
    }

    public function typeLabel(): string
    {
        return self::types()[$this->type] ?? $this->type;
    }

    public function severityLabel(): string
    {
        return self::severities()[$this->severity] ?? $this->severity;
    }

    public function statusLabel(): string
    {
        return self::statuses()[$this->status] ?? $this->status;
    }

    public function severityBadgeClass(): string
    {
        return match ($this->severity) {
            self::SEVERITY_CRITICAL => 'bg-rose-50 text-rose-700 ring-rose-200',
            self::SEVERITY_WARNING => 'bg-amber-50 text-amber-700 ring-amber-200',
            default => 'bg-blue-50 text-blue-700 ring-blue-200',
        };
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            self::STATUS_SENT => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
            self::STATUS_FAILED => 'bg-rose-50 text-rose-700 ring-rose-200',
            self::STATUS_RESOLVED => 'bg-slate-100 text-slate-700 ring-slate-200',
            self::STATUS_IGNORED => 'bg-zinc-100 text-zinc-700 ring-zinc-200',
            default => 'bg-blue-50 text-blue-700 ring-blue-200',
        };
    }

    public function triggeredAtLabel(): string
    {
        return $this->triggered_at
            ? $this->triggered_at->format('d M Y H:i')
            : '-';
    }
}