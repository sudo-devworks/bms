<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BackupLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'backup_job_id',
        'backup_system_id',
        'backup_storage_id',
        'status',
        'started_at',
        'finished_at',
        'duration_seconds',
        'backup_date',
        'file_name',
        'file_path',
        'file_size_bytes',
        'checksum',
        'message',
        'error_message',
        'created_by',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'backup_date' => 'date',
        'duration_seconds' => 'integer',
        'file_size_bytes' => 'integer',
    ];

    public const STATUS_SUCCESS = 'success';
    public const STATUS_FAILED = 'failed';
    public const STATUS_WARNING = 'warning';

    public static function statuses(): array
    {
        return [
            self::STATUS_SUCCESS => 'Success',
            self::STATUS_FAILED => 'Failed',
            self::STATUS_WARNING => 'Warning',
        ];
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

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function statusLabel(): string
    {
        return self::statuses()[$this->status] ?? $this->status;
    }

    public function durationLabel(): string
    {
        if ($this->duration_seconds === null) {
            return '-';
        }

        $hours = intdiv($this->duration_seconds, 3600);
        $minutes = intdiv($this->duration_seconds % 3600, 60);
        $seconds = $this->duration_seconds % 60;

        if ($hours > 0) {
            return sprintf('%d jam %d menit %d detik', $hours, $minutes, $seconds);
        }

        if ($minutes > 0) {
            return sprintf('%d menit %d detik', $minutes, $seconds);
        }

        return sprintf('%d detik', $seconds);
    }

    public function fileSizeLabel(): string
    {
        if ($this->file_size_bytes === null) {
            return '-';
        }

        $bytes = (float) $this->file_size_bytes;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $index = 0;

        while ($bytes >= 1024 && $index < count($units) - 1) {
            $bytes /= 1024;
            $index++;
        }

        return round($bytes, $index === 0 ? 0 : 2).' '.$units[$index];
    }
}
