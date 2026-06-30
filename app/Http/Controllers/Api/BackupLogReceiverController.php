<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BackupJob;
use App\Models\BackupLog;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class BackupLogReceiverController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $token = $request->header('X-BMS-Token');
        $expectedToken = config('bms.api_token');

        if (
            blank($expectedToken) ||
            blank($token) ||
            ! hash_equals((string) $expectedToken, (string) $token)
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Token API tidak valid.',
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'job_code' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::in([
                BackupLog::STATUS_SUCCESS,
                BackupLog::STATUS_FAILED,
                BackupLog::STATUS_WARNING,
            ])],
            'backup_date' => ['required', 'date', 'before_or_equal:today'],
            'started_at' => ['nullable', 'date'],
            'finished_at' => ['nullable', 'date', 'after_or_equal:started_at'],
            'file_name' => ['nullable', 'string', 'max:255'],
            'file_path' => ['nullable', 'string', 'max:1000'],
            'file_size_bytes' => ['nullable', 'integer', 'min:0'],
            'checksum' => ['nullable', 'string', 'max:255'],
            'message' => ['nullable', 'string', 'required_if:status,warning'],
            'error_message' => ['nullable', 'string', 'required_if:status,failed'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Payload tidak valid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        $job = BackupJob::query()
            ->with(['system.storage'])
            ->where('code', $data['job_code'])
            ->first();

        if (! $job) {
            return response()->json([
                'success' => false,
                'message' => 'Backup job tidak ditemukan.',
            ], 404);
        }

        if (! $job->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Backup job tidak aktif.',
            ], 403);
        }

        if (! $job->system) {
            return response()->json([
                'success' => false,
                'message' => 'Sistem backup terkait job tidak ditemukan.',
            ], 422);
        }

        if (! $job->system->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Sistem backup terkait job tidak aktif.',
            ], 403);
        }

        if (! $job->system->storage) {
            return response()->json([
                'success' => false,
                'message' => 'Storage backup terkait sistem tidak ditemukan.',
            ], 422);
        }

        $durationSeconds = null;

        if (! empty($data['started_at']) && ! empty($data['finished_at'])) {
            $startedAt = Carbon::parse($data['started_at']);
            $finishedAt = Carbon::parse($data['finished_at']);

            $durationSeconds = $startedAt->diffInSeconds($finishedAt);
        }

        $log = BackupLog::create([
            'backup_job_id' => $job->id,
            'backup_system_id' => $job->backup_system_id,
            'backup_storage_id' => $job->system->backup_storage_id,
            'status' => $data['status'],
            'backup_date' => $data['backup_date'],
            'started_at' => $data['started_at'] ?? null,
            'finished_at' => $data['finished_at'] ?? null,
            'duration_seconds' => $durationSeconds,
            'file_name' => $data['file_name'] ?? null,
            'file_path' => $data['file_path'] ?? null,
            'file_size_bytes' => $data['file_size_bytes'] ?? null,
            'checksum' => $data['checksum'] ?? null,
            'message' => $data['message'] ?? null,
            'error_message' => $data['error_message'] ?? null,
            'created_by' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Backup log berhasil diterima.',
            'data' => [
                'log_id' => $log->id,
                'job_code' => $job->code,
                'status' => $log->status,
            ],
        ]);
    }
}