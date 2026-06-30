<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'channel',
        'recipient_name',
        'recipient_email',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public const CHANNEL_EMAIL = 'email';

    public static function channels(): array
    {
        return [
            self::CHANNEL_EMAIL => 'Email',
        ];
    }

    public function channelLabel(): string
    {
        return self::channels()[$this->channel] ?? $this->channel;
    }

    public function statusLabel(): string
    {
        return $this->is_active ? 'Aktif' : 'Nonaktif';
    }

    public function statusBadgeClass(): string
    {
        return $this->is_active
            ? 'bg-emerald-50 text-emerald-700 ring-emerald-200'
            : 'bg-slate-50 text-slate-600 ring-slate-200';
    }

    public function recipientLabel(): string
    {
        if ($this->recipient_name) {
            return $this->recipient_name . ' <' . $this->recipient_email . '>';
        }

        return $this->recipient_email;
    }
}