<?php

namespace App\Http\Controllers;

use App\Models\BackupAlert;
use App\Models\NotificationSetting;
use App\Models\SystemMonitor;
use Illuminate\Http\Request;
use App\Mail\BackupAlertMail;
use Illuminate\Support\Facades\Mail;
use Throwable;

class BackupAlertController extends Controller
{
    public function index(Request $request)
    {
        $query = BackupAlert::query()
            ->with(['log', 'job', 'system', 'storage'])
            ->latest('triggered_at')
            ->latest('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($subQuery) use ($search) {
                $subQuery
                    ->where('title', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%")
                    ->orWhereHas('job', function ($jobQuery) use ($search) {
                        $jobQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%");
                    })
                    ->orWhereHas('system', function ($systemQuery) use ($search) {
                        $systemQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%");
                    })
                    ->orWhereHas('storage', function ($storageQuery) use ($search) {
                        $storageQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $alerts = $query
            ->paginate(10)
            ->withQueryString();

        $summary = [
            'total' => BackupAlert::count(),
            'new' => BackupAlert::where('status', BackupAlert::STATUS_NEW)->count(),
            'sent' => BackupAlert::where('status', BackupAlert::STATUS_SENT)->count(),
            'failed' => BackupAlert::where('status', BackupAlert::STATUS_FAILED)->count(),
            'resolved' => BackupAlert::where('status', BackupAlert::STATUS_RESOLVED)->count(),
            'ignored' => BackupAlert::where('status', BackupAlert::STATUS_IGNORED)->count(),
            'critical' => BackupAlert::where('severity', BackupAlert::SEVERITY_CRITICAL)
                ->whereNotIn('status', [
                    BackupAlert::STATUS_RESOLVED,
                    BackupAlert::STATUS_IGNORED,
                ])
                ->count(),
        ];

        $notificationSettings = NotificationSetting::query()
            ->orderByDesc('is_active')
            ->orderBy('recipient_name')
            ->orderBy('recipient_email')
            ->get();

        $alertCheckerMonitor = SystemMonitor::query()
            ->where('key', SystemMonitor::KEY_ALERT_CHECKER)
            ->first();

        return view('backup-alerts.index', [
            'alerts' => $alerts,
            'summary' => $summary,
            'types' => BackupAlert::types(),
            'severities' => BackupAlert::severities(),
            'statuses' => BackupAlert::statuses(),
            'filters' => $request->only(['status', 'severity', 'type', 'search']),
            'notificationSettings' => $notificationSettings,
            'alertCheckerMonitor' => $alertCheckerMonitor,
        ]);
    }

    public function storeRecipient(Request $request)
    {
        $validated = $request->validate([
            'recipient_name' => ['nullable', 'string', 'max:255'],
            'recipient_email' => ['required', 'email', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        NotificationSetting::updateOrCreate(
            [
                'channel' => NotificationSetting::CHANNEL_EMAIL,
                'recipient_email' => $validated['recipient_email'],
            ],
            [
                'recipient_name' => $validated['recipient_name'] ?? null,
                'is_active' => $request->boolean('is_active'),
            ]
        );

        return back()->with('success', 'Penerima notifikasi berhasil disimpan.');
    }

    public function toggleRecipient(NotificationSetting $notificationSetting)
    {
        $notificationSetting->update([
            'is_active' => ! $notificationSetting->is_active,
        ]);

        return back()->with('success', 'Status penerima notifikasi berhasil diperbarui.');
    }

    public function deleteRecipient(NotificationSetting $notificationSetting)
    {
        $notificationSetting->delete();

        return back()->with('success', 'Penerima notifikasi berhasil dihapus.');
    }

    public function sendTestEmail()
    {
        $recipients = NotificationSetting::query()
            ->where('channel', NotificationSetting::CHANNEL_EMAIL)
            ->where('is_active', true)
            ->get();

        if ($recipients->isEmpty()) {
            return back()->with('error', 'Belum ada penerima email aktif.');
        }

        try {
            foreach ($recipients as $recipient) {
                Mail::to($recipient->recipient_email)
                    ->send(new BackupAlertMail(null, true));
            }

            return back()->with('success', 'Test email berhasil dikirim ke ' . $recipients->count() . ' penerima aktif.');
        } catch (Throwable $e) {
            return back()->with('error', 'Test email gagal dikirim: ' . $e->getMessage());
        }
    }

    public function sendPendingEmails()
    {
        return $this->sendAlertEmailsByStatus(
            BackupAlert::STATUS_NEW,
            'Pending alert email berhasil diproses.'
        );
    }

    public function retryFailedEmails()
    {
        return $this->sendAlertEmailsByStatus(
            BackupAlert::STATUS_FAILED,
            'Failed alert email berhasil dicoba ulang.'
        );
    }

    private function sendAlertEmailsByStatus(string $status, string $successMessage)
    {
        $recipients = NotificationSetting::query()
            ->where('channel', NotificationSetting::CHANNEL_EMAIL)
            ->where('is_active', true)
            ->get();

        if ($recipients->isEmpty()) {
            return back()->with('error', 'Belum ada penerima email aktif.');
        }

        $alerts = BackupAlert::query()
            ->with(['log', 'job', 'system', 'storage'])
            ->where('status', $status)
            ->latest('triggered_at')
            ->latest('created_at')
            ->get();

        if ($alerts->isEmpty()) {
            return back()->with('success', 'Tidak ada alert yang perlu dikirim.');
        }

        $sentCount = 0;
        $failedCount = 0;

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

                $sentCount++;
            } catch (Throwable $e) {
                $alert->update([
                    'status' => BackupAlert::STATUS_FAILED,
                ]);

                $failedCount++;
            }
        }

        if ($failedCount > 0) {
            return back()->with(
                'error',
                "{$successMessage} Berhasil: {$sentCount}, gagal: {$failedCount}."
            );
        }

        return back()->with(
            'success',
            "{$successMessage} Berhasil: {$sentCount}, gagal: {$failedCount}."
        );
    }

    public function resolve(BackupAlert $backupAlert)
    {
        $backupAlert->update([
            'status' => BackupAlert::STATUS_RESOLVED,
            'resolved_at' => now(),
        ]);

        return back()->with('success', 'Alert berhasil ditandai resolved.');
    }

    public function ignore(BackupAlert $backupAlert)
    {
        $backupAlert->update([
            'status' => BackupAlert::STATUS_IGNORED,
            'resolved_at' => now(),
        ]);

        return back()->with('success', 'Alert berhasil diabaikan.');
    }
}