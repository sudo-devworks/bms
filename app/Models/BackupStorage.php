<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BackupStorage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'os_type',
        'access_type',
        'host',
        'base_path',
        'is_active',
        'notes',

        // Storage monitoring
        'total_space_bytes',
        'used_space_bytes',
        'free_space_bytes',
        'usage_percent',
        'last_check_status',
        'last_check_message',
        'last_checked_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'total_space_bytes' => 'integer',
        'used_space_bytes' => 'integer',
        'free_space_bytes' => 'integer',
        'usage_percent' => 'decimal:2',
        'last_checked_at' => 'datetime',
    ];

    public const OS_WINDOWS = 'windows';
    public const OS_LINUX = 'linux';

    public const ACCESS_LOCAL = 'local';
    public const ACCESS_SMB = 'smb';
    public const ACCESS_SSH = 'ssh';

    public const CHECK_ONLINE = 'online';
    public const CHECK_OFFLINE = 'offline';
    public const CHECK_UNKNOWN = 'unknown';

    public static function osTypes(): array
    {
        return [
            self::OS_WINDOWS => 'Windows',
            self::OS_LINUX => 'Linux',
        ];
    }

    public static function accessTypes(): array
    {
        return [
            self::ACCESS_LOCAL => 'Local',
            self::ACCESS_SMB => 'SMB',
            self::ACCESS_SSH => 'SSH',
        ];
    }

    public static function checkStatuses(): array
    {
        return [
            self::CHECK_ONLINE => 'Online',
            self::CHECK_OFFLINE => 'Offline',
            self::CHECK_UNKNOWN => 'Unknown',
        ];
    }

    public function osTypeLabel(): string
    {
        return self::osTypes()[$this->os_type] ?? $this->os_type;
    }

    public function accessTypeLabel(): string
    {
        return self::accessTypes()[$this->access_type] ?? $this->access_type;
    }

    public function checkStatusLabel(): string
    {
        return self::checkStatuses()[$this->last_check_status] ?? 'Unknown';
    }

    public function checkStatusBadgeClass(): string
    {
        return match ($this->last_check_status) {
            self::CHECK_ONLINE => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
            self::CHECK_OFFLINE => 'bg-rose-50 text-rose-700 ring-rose-200',
            default => 'bg-slate-50 text-slate-600 ring-slate-200',
        };
    }

    public function usageBadgeClass(): string
    {
        $usage = (float) ($this->usage_percent ?? 0);

        return match (true) {
            $usage >= 90 => 'bg-rose-50 text-rose-700 ring-rose-200',
            $usage >= 75 => 'bg-amber-50 text-amber-700 ring-amber-200',
            $this->usage_percent === null => 'bg-slate-50 text-slate-600 ring-slate-200',
            default => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
        };
    }

    public function healthLabel(): string
    {
        if ($this->last_check_status === self::CHECK_OFFLINE) {
            return 'Offline';
        }

        if ($this->usage_percent === null) {
            return 'Belum dicek';
        }

        $usage = (float) $this->usage_percent;

        return match (true) {
            $usage >= 90 => 'Kritis',
            $usage >= 75 => 'Perhatian',
            default => 'Sehat',
        };
    }

    public function healthBadgeClass(): string
    {
        if ($this->last_check_status === self::CHECK_OFFLINE) {
            return 'bg-rose-50 text-rose-700 ring-rose-200';
        }

        if ($this->usage_percent === null) {
            return 'bg-slate-50 text-slate-600 ring-slate-200';
        }

        $usage = (float) $this->usage_percent;

        return match (true) {
            $usage >= 90 => 'bg-rose-50 text-rose-700 ring-rose-200',
            $usage >= 75 => 'bg-amber-50 text-amber-700 ring-amber-200',
            default => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
        };
    }

    public function totalSpaceLabel(): string
    {
        return $this->formatBytes($this->total_space_bytes);
    }

    public function usedSpaceLabel(): string
    {
        return $this->formatBytes($this->used_space_bytes);
    }

    public function freeSpaceLabel(): string
    {
        return $this->formatBytes($this->free_space_bytes);
    }

    public function usagePercentLabel(): string
    {
        if ($this->usage_percent === null) {
            return '-';
        }

        return number_format((float) $this->usage_percent, 1) . '%';
    }

    public function lastCheckedLabel(): string
    {
        if (! $this->last_checked_at) {
            return 'Belum pernah dicek';
        }

        return $this->last_checked_at->format('d M Y H:i');
    }

    public function hasStorageMetric(): bool
    {
        return $this->total_space_bytes !== null
            || $this->used_space_bytes !== null
            || $this->free_space_bytes !== null
            || $this->usage_percent !== null;
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

    public function backupSystems(): HasMany
    {
        return $this->hasMany(BackupSystem::class);
    }

    public function backupLogs(): HasMany
    {
        return $this->hasMany(BackupLog::class);
    }
}