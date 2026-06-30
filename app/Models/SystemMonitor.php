<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemMonitor extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'name',
        'status',
        'message',
        'meta',
        'last_run_at',
    ];

    protected $casts = [
        'meta' => 'array',
        'last_run_at' => 'datetime',
    ];

    public const KEY_ALERT_CHECKER = 'alert_checker';

    public const STATUS_OK = 'ok';
    public const STATUS_WARNING = 'warning';
    public const STATUS_FAILED = 'failed';
    public const STATUS_UNKNOWN = 'unknown';

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_OK => 'Normal',
            self::STATUS_WARNING => 'Perhatian',
            self::STATUS_FAILED => 'Gagal',
            default => 'Unknown',
        };
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            self::STATUS_OK => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
            self::STATUS_WARNING => 'bg-amber-50 text-amber-700 ring-amber-200',
            self::STATUS_FAILED => 'bg-rose-50 text-rose-700 ring-rose-200',
            default => 'bg-slate-50 text-slate-700 ring-slate-200',
        };
    }

    public function lastRunLabel(): string
    {
        return $this->last_run_at
            ? $this->last_run_at->format('d M Y H:i:s')
            : '-';
    }

    public static function updateAlertChecker(string $status, string $message, array $meta = []): self
    {
        return self::updateOrCreate(
            ['key' => self::KEY_ALERT_CHECKER],
            [
                'name' => 'BMS Alert Checker',
                'status' => $status,
                'message' => $message,
                'meta' => $meta,
                'last_run_at' => now(),
            ]
        );
    }
}