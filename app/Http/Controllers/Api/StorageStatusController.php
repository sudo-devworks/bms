<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BackupStorage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StorageStatusController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $token = config('bms.api_token');

        if (! $token || $request->header('X-BMS-Token') !== $token) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.',
            ], 401);
        }

        $validated = $request->validate([
            'storage_id' => ['nullable', 'integer', 'exists:backup_storages,id'],
            'storage_name' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(array_keys(BackupStorage::checkStatuses()))],
            'total_space_bytes' => ['nullable', 'integer', 'min:0'],
            'used_space_bytes' => ['nullable', 'integer', 'min:0'],
            'free_space_bytes' => ['nullable', 'integer', 'min:0'],
            'message' => ['nullable', 'string', 'max:2000'],
        ]);

        if (empty($validated['storage_id']) && empty($validated['storage_name'])) {
            return response()->json([
                'success' => false,
                'message' => 'storage_id atau storage_name wajib diisi.',
            ], 422);
        }

        $storage = ! empty($validated['storage_id'])
            ? BackupStorage::query()->find($validated['storage_id'])
            : BackupStorage::query()
                ->where('name', $validated['storage_name'])
                ->first();

        if (! $storage) {
            return response()->json([
                'success' => false,
                'message' => 'Storage tidak ditemukan.',
            ], 404);
        }

        if (! $storage->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Storage tidak aktif.',
            ], 422);
        }

        $totalSpaceBytes = $validated['total_space_bytes'] ?? $storage->total_space_bytes;
        $usedSpaceBytes = $validated['used_space_bytes'] ?? null;
        $freeSpaceBytes = $validated['free_space_bytes'] ?? null;

        if ($usedSpaceBytes === null && $totalSpaceBytes !== null && $freeSpaceBytes !== null) {
            $usedSpaceBytes = max($totalSpaceBytes - $freeSpaceBytes, 0);
        }

        if ($freeSpaceBytes === null && $totalSpaceBytes !== null && $usedSpaceBytes !== null) {
            $freeSpaceBytes = max($totalSpaceBytes - $usedSpaceBytes, 0);
        }

        if ($totalSpaceBytes !== null && $usedSpaceBytes !== null && $usedSpaceBytes > $totalSpaceBytes) {
            return response()->json([
                'success' => false,
                'message' => 'used_space_bytes tidak boleh lebih besar dari total_space_bytes.',
            ], 422);
        }

        $usagePercent = $totalSpaceBytes > 0 && $usedSpaceBytes !== null
            ? round(($usedSpaceBytes / $totalSpaceBytes) * 100, 2)
            : null;

        $storage->update([
            'total_space_bytes' => $totalSpaceBytes,
            'used_space_bytes' => $usedSpaceBytes,
            'free_space_bytes' => $freeSpaceBytes,
            'usage_percent' => $usagePercent,
            'last_check_status' => $validated['status'],
            'last_check_message' => $validated['message'] ?? null,
            'last_checked_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Storage status updated.',
            'data' => [
                'storage_id' => $storage->id,
                'storage_name' => $storage->name,
                'status' => $storage->last_check_status,
                'usage_percent' => $storage->usage_percent,
                'last_checked_at' => optional($storage->last_checked_at)->toISOString(),
            ],
        ]);
    }
}