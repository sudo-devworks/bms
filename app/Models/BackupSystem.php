<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BackupSystem extends Model
{
    use HasFactory;

    protected $fillable = [
        'backup_storage_id',
        'name',
        'code',
        'category',
        'source_server',
        'source_path',
        'backup_schedule',
        'expected_frequency',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public const CATEGORY_DATABASE = 'database';
    public const CATEGORY_FILE = 'file';
    public const CATEGORY_APPLICATION = 'application';
    public const CATEGORY_MAIL = 'mail';
    public const CATEGORY_MIXED = 'mixed';
    public const CATEGORY_OTHER = 'other';

    public const FREQUENCY_DAILY = 'daily';
    public const FREQUENCY_WEEKLY = 'weekly';
    public const FREQUENCY_MONTHLY = 'monthly';
    public const FREQUENCY_MANUAL = 'manual';

    public static function categories(): array
    {
        return [
            self::CATEGORY_DATABASE => 'Database',
            self::CATEGORY_FILE => 'File',
            self::CATEGORY_APPLICATION => 'Application',
            self::CATEGORY_MAIL => 'Mail',
            self::CATEGORY_MIXED => 'Mixed',
            self::CATEGORY_OTHER => 'Other',
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

    public function storage(): BelongsTo
    {
        return $this->belongsTo(BackupStorage::class, 'backup_storage_id');
    }

    public function backupJobs(): HasMany
    {
        return $this->hasMany(BackupJob::class);
    }

    public function backupLogs(): HasMany
    {
        return $this->hasMany(BackupLog::class);
    }

    public function categoryLabel(): string
    {
        return self::categories()[$this->category] ?? $this->category;
    }

    public function frequencyLabel(): string
    {
        return self::frequencies()[$this->expected_frequency] ?? $this->expected_frequency;
    }
}