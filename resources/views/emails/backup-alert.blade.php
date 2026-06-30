<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $isTest ? 'Test Email Notification' : 'Backup Alert' }}</title>
</head>
<body style="margin:0; padding:0; background:#f1f5f9; font-family:Arial, Helvetica, sans-serif; color:#0f172a;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f1f5f9; padding:24px;">
        <tr>
            <td align="center">
                <table width="640" cellpadding="0" cellspacing="0" style="max-width:640px; background:#ffffff; border-radius:16px; overflow:hidden; border:1px solid #e2e8f0;">
                    <tr>
                        <td style="padding:20px 24px; background:#0f172a; color:#ffffff;">
                            <div style="font-size:12px; letter-spacing:1.5px; text-transform:uppercase; color:#93c5fd; font-weight:bold;">
                                Backup Monitoring System
                            </div>
                            <div style="margin-top:6px; font-size:22px; line-height:1.3; font-weight:bold;">
                                {{ $isTest ? 'Test Email Notification' : ($alert?->title ?? 'Backup Alert') }}
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:24px;">
                            @if ($isTest)
                                <div style="padding:14px 16px; border-radius:12px; background:#ecfdf5; border:1px solid #bbf7d0; color:#047857; font-weight:bold;">
                                    Email test berhasil dikirim dari Backup Monitoring System.
                                </div>

                                <p style="margin:18px 0 0; color:#475569; line-height:1.6;">
                                    Jika email ini diterima, berarti konfigurasi SMTP Laravel dan daftar recipient aktif sudah bisa digunakan untuk notifikasi BMS.
                                </p>
                            @else
                                @php
                                    $severityColor = match ($alert?->severity) {
                                        'critical' => '#be123c',
                                        'warning' => '#b45309',
                                        default => '#2563eb',
                                    };

                                    $severityBg = match ($alert?->severity) {
                                        'critical' => '#fff1f2',
                                        'warning' => '#fffbeb',
                                        default => '#eff6ff',
                                    };

                                    $severityBorder = match ($alert?->severity) {
                                        'critical' => '#fecdd3',
                                        'warning' => '#fde68a',
                                        default => '#bfdbfe',
                                    };
                                @endphp

                                <div style="padding:14px 16px; border-radius:12px; background:{{ $severityBg }}; border:1px solid {{ $severityBorder }}; color:{{ $severityColor }}; font-weight:bold;">
                                    Severity: {{ $alert?->severityLabel() ?? '-' }}
                                </div>

                                <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:18px;">
                                    <tr>
                                        <td style="padding:10px 0; width:150px; color:#64748b; font-size:14px;">Type</td>
                                        <td style="padding:10px 0; color:#0f172a; font-size:14px; font-weight:bold;">
                                            {{ $alert?->typeLabel() ?? '-' }}
                                        </td>
                                    </tr>

                                    <tr>
                                        <td style="padding:10px 0; color:#64748b; font-size:14px;">Status</td>
                                        <td style="padding:10px 0; color:#0f172a; font-size:14px; font-weight:bold;">
                                            {{ $alert?->statusLabel() ?? '-' }}
                                        </td>
                                    </tr>

                                    <tr>
                                        <td style="padding:10px 0; color:#64748b; font-size:14px;">Triggered At</td>
                                        <td style="padding:10px 0; color:#0f172a; font-size:14px; font-weight:bold;">
                                            {{ $alert?->triggeredAtLabel() ?? '-' }}
                                        </td>
                                    </tr>
                                </table>

                                @if ($alert?->message)
                                    <div style="margin-top:18px;">
                                        <div style="margin-bottom:8px; color:#64748b; font-size:14px; font-weight:bold;">
                                            Detail
                                        </div>
                                        <div style="white-space:pre-line; padding:14px 16px; border-radius:12px; background:#f8fafc; border:1px solid #e2e8f0; color:#334155; font-size:14px; line-height:1.6;">
                                            {{ $alert->message }}
                                        </div>
                                    </div>
                                @endif
                            @endif

                            <div style="margin-top:24px; padding-top:16px; border-top:1px solid #e2e8f0; color:#64748b; font-size:12px; line-height:1.5;">
                                Email ini dikirim otomatis oleh Backup Monitoring System. BMS hanya memonitor data backup yang sudah masuk ke database.
                            </div>
                        </td>
                    </tr>
                </table>

                <div style="margin-top:16px; color:#94a3b8; font-size:12px;">
                    Backup Monitoring System
                </div>
            </td>
        </tr>
    </table>
</body>
</html>