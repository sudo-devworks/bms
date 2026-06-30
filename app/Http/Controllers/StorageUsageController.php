<?php

namespace App\Http\Controllers;

use App\Models\BackupStorage;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StorageUsageController extends Controller
{
    public function index()
    {
        $storages = BackupStorage::query()
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->get();

        $totalStorages = $storages->count();
        $activeStorages = $storages->where('is_active', true)->count();

        $onlineStorages = $storages
            ->where('last_check_status', BackupStorage::CHECK_ONLINE)
            ->count();

        $offlineStorages = $storages
            ->where('last_check_status', BackupStorage::CHECK_OFFLINE)
            ->count();

        $unknownStorages = $storages
            ->where('last_check_status', BackupStorage::CHECK_UNKNOWN)
            ->count();

        $totalSpaceBytes = $storages->sum(fn ($storage) => (int) ($storage->total_space_bytes ?? 0));
        $usedSpaceBytes = $storages->sum(fn ($storage) => (int) ($storage->used_space_bytes ?? 0));
        $freeSpaceBytes = $storages->sum(fn ($storage) => (int) ($storage->free_space_bytes ?? 0));

        $overallUsagePercent = $totalSpaceBytes > 0
            ? round(($usedSpaceBytes / $totalSpaceBytes) * 100, 2)
            : null;

        return view('storage-usage.index', [
            'storages' => $storages,
            'totalStorages' => $totalStorages,
            'activeStorages' => $activeStorages,
            'onlineStorages' => $onlineStorages,
            'offlineStorages' => $offlineStorages,
            'unknownStorages' => $unknownStorages,
            'totalSpaceLabel' => $this->formatBytes($totalSpaceBytes),
            'usedSpaceLabel' => $this->formatBytes($usedSpaceBytes),
            'freeSpaceLabel' => $this->formatBytes($freeSpaceBytes),
            'overallUsagePercent' => $overallUsagePercent,
        ]);
    }

    public function edit(BackupStorage $backupStorage)
    {
        $totalEditable = $this->bytesToEditableValue($backupStorage->total_space_bytes);
        $usedEditable = $this->bytesToEditableValue($backupStorage->used_space_bytes);

        $defaultUnit = $totalEditable['unit'] ?? 'tb';

        return view('storage-usage.edit', [
            'storage' => $backupStorage,
            'totalEditable' => $totalEditable,
            'usedEditable' => $usedEditable,
            'defaultUnit' => $defaultUnit,
        ]);
    }

    public function update(Request $request, BackupStorage $backupStorage)
    {
        $validated = $request->validate([
            'total_space' => ['required', 'numeric', 'min:0'],
            'used_space' => ['required', 'numeric', 'min:0'],
            'unit' => ['required', Rule::in(['mb', 'gb', 'tb'])],
            'last_check_status' => ['required', Rule::in(array_keys(BackupStorage::checkStatuses()))],
            'last_check_message' => ['nullable', 'string', 'max:2000'],
        ]);

        $totalSpaceBytes = $this->toBytes((float) $validated['total_space'], $validated['unit']);
        $usedSpaceBytes = $this->toBytes((float) $validated['used_space'], $validated['unit']);

        if ($usedSpaceBytes > $totalSpaceBytes) {
            return back()
                ->withInput()
                ->withErrors([
                    'used_space' => 'Used space tidak boleh lebih besar dari total capacity.',
                ]);
        }

        $freeSpaceBytes = $totalSpaceBytes - $usedSpaceBytes;

        $usagePercent = $totalSpaceBytes > 0
            ? round(($usedSpaceBytes / $totalSpaceBytes) * 100, 2)
            : 0;

        $backupStorage->update([
            'total_space_bytes' => $totalSpaceBytes,
            'used_space_bytes' => $usedSpaceBytes,
            'free_space_bytes' => $freeSpaceBytes,
            'usage_percent' => $usagePercent,
            'last_check_status' => $validated['last_check_status'],
            'last_check_message' => $validated['last_check_message'] ?? null,
            'last_checked_at' => now(),
        ]);

        return redirect()
            ->route('storage-usage.index')
            ->with('success', 'Data kapasitas storage berhasil diperbarui.');
    }

    private function bytesToEditableValue(?int $bytes): array
    {
        if ($bytes === null || $bytes <= 0) {
            return [
                'value' => null,
                'unit' => 'tb',
            ];
        }

        $tb = 1024 * 1024 * 1024 * 1024;
        $gb = 1024 * 1024 * 1024;
        $mb = 1024 * 1024;

        if ($bytes >= $tb) {
            return [
                'value' => round($bytes / $tb, 2),
                'unit' => 'tb',
            ];
        }

        if ($bytes >= $gb) {
            return [
                'value' => round($bytes / $gb, 2),
                'unit' => 'gb',
            ];
        }

        return [
            'value' => round($bytes / $mb, 2),
            'unit' => 'mb',
        ];
    }

    private function toBytes(float $value, string $unit): int
    {
        return match ($unit) {
            'tb' => (int) round($value * 1024 * 1024 * 1024 * 1024),
            'gb' => (int) round($value * 1024 * 1024 * 1024),
            'mb' => (int) round($value * 1024 * 1024),
            default => 0,
        };
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
}