<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BackupJob extends Model
{
    use HasFactory;

    protected $fillable = [
        'backup_system_id',
        'name',
        'code',
        'backup_type',
        'schedule_text',
        'expected_frequency',
        'expected_time',
        'is_active',
        'notes',
        'expected_run_time',
        'alert_after_minutes',
    ];

    protected $casts = [
        'expected_time' => 'datetime:H:i',
        'is_active' => 'boolean',
        'alert_after_minutes' => 'integer',
    ];

    public const TYPE_DATABASE = 'database';
    public const TYPE_FILE = 'file';
    public const TYPE_APPLICATION = 'application';
    public const TYPE_MAIL = 'mail';
    public const TYPE_MIXED = 'mixed';
    public const TYPE_OTHER = 'other';

    public const FREQUENCY_DAILY = 'daily';
    public const FREQUENCY_WEEKLY = 'weekly';
    public const FREQUENCY_MONTHLY = 'monthly';
    public const FREQUENCY_MANUAL = 'manual';

    public static function backupTypes(): array
    {
        return [
            self::TYPE_DATABASE => 'Database',
            self::TYPE_FILE => 'File',
            self::TYPE_APPLICATION => 'Application',
            self::TYPE_MAIL => 'Mail',
            self::TYPE_MIXED => 'Mixed',
            self::TYPE_OTHER => 'Other',
        ];
    }

    public static function frequencies(): array
    {
        return [
            self::FREQUENCY_DAILY => 'Daily',
            self::FREQUENCY_WEEKLY => 'Weekly',
            self::FREQUENCY_MONTHLY => 'Monthly',
            self::FREQUENCY_MANUAL => 'Manual',
        ];
    }

    public function system(): BelongsTo
    {
        return $this->belongsTo(BackupSystem::class, 'backup_system_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(BackupLog::class);
    }

    public function latestLog()
    {
        return $this->hasOne(BackupLog::class)->latestOfMany('backup_date');
    }

    public function backupTypeLabel(): string
    {
        return self::backupTypes()[$this->backup_type] ?? $this->backup_type;
    }

    public function frequencyLabel(): string
    {
        return self::frequencies()[$this->expected_frequency] ?? $this->expected_frequency;
    }

    public function storage()
    {
        return $this->belongsTo(BackupStorage::class, 'backup_storage_id');
    }

    public function expectedRunTimeLabel(): string
    {
        return $this->expected_run_time
            ? substr($this->expected_run_time, 0, 5)
            : '-';
    }

    public function alertAfterMinutesLabel(): string
    {
        $minutes = (int) ($this->alert_after_minutes ?? 60);

        if ($minutes === 0) {
            return 'Langsung saat jam backup terlewati';
        }

        if ($minutes < 60) {
            return $minutes . ' menit';
        }

        $hours = intdiv($minutes, 60);
        $remainingMinutes = $minutes % 60;

        if ($remainingMinutes === 0) {
            return $hours . ' jam';
        }

        return $hours . ' jam ' . $remainingMinutes . ' menit';
    }

    public function pendingAlertTimeLabel(): string
    {
        if (! $this->expected_run_time) {
            return '-';
        }

        $time = now()
            ->setTimeFromTimeString($this->expected_run_time)
            ->addMinutes((int) ($this->alert_after_minutes ?? 60));

        return $time->format('H:i');
    }

    public function isPendingAlertDue(): bool
    {
        if (! $this->expected_run_time) {
            return false;
        }

        $dueAt = now()
            ->setTimeFromTimeString($this->expected_run_time)
            ->addMinutes((int) ($this->alert_after_minutes ?? 60));

        return now()->greaterThanOrEqualTo($dueAt);
    }
}
