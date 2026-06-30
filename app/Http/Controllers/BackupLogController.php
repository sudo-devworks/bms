<?php

namespace App\Http\Controllers;

use App\Models\BackupJob;
use App\Models\BackupLog;
use App\Models\BackupSystem;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class BackupLogController extends Controller
{
    public function index(Request $request)
    {
        $query = BackupLog::query()
            ->with(['job', 'system', 'storage', 'creator']);

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('file_name', 'like', "%{$search}%")
                    ->orWhere('file_path', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%")
                    ->orWhere('error_message', 'like', "%{$search}%")
                    ->orWhereHas('job', function ($jobQuery) use ($search) {
                        $jobQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%");
                    })
                    ->orWhereHas('system', function ($systemQuery) use ($search) {
                        $systemQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('backup_date_from')) {
            $query->whereDate('backup_date', '>=', $request->backup_date_from);
        }

        if ($request->filled('backup_date_to')) {
            $query->whereDate('backup_date', '<=', $request->backup_date_to);
        }

        if ($request->filled('backup_system_id')) {
            $query->where('backup_system_id', $request->backup_system_id);
        }

        if ($request->filled('backup_job_id')) {
            $query->where('backup_job_id', $request->backup_job_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $logs = $query
            ->latest('backup_date')
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $summary = [
            'total' => BackupLog::count(),
            'success' => BackupLog::where('status', BackupLog::STATUS_SUCCESS)->count(),
            'failed' => BackupLog::where('status', BackupLog::STATUS_FAILED)->count(),
            'warning' => BackupLog::where('status', BackupLog::STATUS_WARNING)->count(),
        ];

        return view('backup-logs.index', [
            'logs' => $logs,
            'summary' => $summary,
            'systems' => BackupSystem::orderBy('name')->get(),
            'jobs' => BackupJob::orderBy('name')->get(),
            'statuses' => BackupLog::statuses(),
        ]);
    }

    public function create(Request $request)
    {
        $selectedJob = null;

        if ($request->filled('backup_job_id')) {
            $selectedJob = BackupJob::query()
                ->where('is_active', true)
                ->with(['system.storage'])
                ->find($request->backup_job_id);
        }

        return view('backup-logs.create', [
            'log' => new BackupLog([
                'backup_job_id' => $selectedJob?->id,
                'backup_date' => now()->toDateString(),
                'status' => BackupLog::STATUS_SUCCESS,
            ]),
            'jobs' => $this->activeJobs(),
            'selectedJob' => $selectedJob,
            'statuses' => BackupLog::statuses(),
        ]);
    }

    public function store(Request $request)
    {
        $request->merge([
            'file_size_bytes' => $this->normalizeFileSizeBytes(
                $request->input('file_size_bytes'),
                $request->input('file_size_mb'),
            ),
        ]);

        $validator = Validator::make(
            $request->all(),
            $this->rules(),
            $this->messages(),
        );

        $validator->after(function ($validator) use ($request) {
            $status = $request->input('status');
            $hasFileInfo = filled($request->input('file_name'))
                || filled($request->input('file_path'))
                || filled($request->input('file_size_bytes'))
                || filled($request->input('file_size_mb'));

            if ($status === BackupLog::STATUS_SUCCESS && ! $hasFileInfo && ! filled($request->input('message'))) {
                $validator->errors()->add('message', 'Untuk status success tanpa informasi file, isi Pesan sebagai bukti/keterangan backup.');
            }

            if ($status === BackupLog::STATUS_FAILED && (filled($request->input('file_name')) || filled($request->input('file_path'))) && ! filled($request->input('message'))) {
                $validator->errors()->add('message', 'Jika status failed tetapi ada informasi file, isi Pesan untuk menjelaskan kondisi file tersebut.');
            }
        });

        $validated = $validator->validate();

        unset($validated['file_size_mb']);

        $job = BackupJob::with('system.storage')->findOrFail($validated['backup_job_id']);

        $validated['backup_system_id'] = $job->backup_system_id;
        $validated['backup_storage_id'] = $job->system->backup_storage_id;
        $validated['created_by'] = auth()->id();
        $validated['duration_seconds'] = $this->calculateDuration(
            $validated['started_at'] ?? null,
            $validated['finished_at'] ?? null,
            $validated['duration_seconds'] ?? null,
        );

        BackupLog::create($validated);

        if ($request->boolean('return_to_job') || $request->input('redirect_to') === 'job') {
            return redirect()
                ->route('backup-jobs.show', $job)
                ->with('success', 'Backup log berhasil ditambahkan untuk job '.$job->name.'.');
        }

        return redirect()
            ->route('backup-logs.index')
            ->with('success', 'Backup log berhasil ditambahkan.');
    }

    public function show(BackupLog $backupLog)
    {
        $backupLog->load(['job', 'system', 'storage', 'creator']);

        return view('backup-logs.show', [
            'log' => $backupLog,
        ]);
    }

    public function destroy(BackupLog $backupLog)
    {
        $backupLog->delete();

        return redirect()
            ->route('backup-logs.index')
            ->with('success', 'Backup log berhasil dihapus.');
    }

    private function rules(): array
    {
        return [
            'backup_job_id' => [
                'required',
                Rule::exists('backup_jobs', 'id')->where('is_active', true),
            ],
            'status' => ['required', Rule::in(array_keys(BackupLog::statuses()))],
            'started_at' => ['nullable', 'date'],
            'finished_at' => ['nullable', 'date', 'after_or_equal:started_at'],
            'duration_seconds' => ['nullable', 'integer', 'min:0'],
            'backup_date' => ['required', 'date', 'before_or_equal:today'],
            'file_name' => ['nullable', 'string', 'max:255'],
            'file_path' => ['nullable', 'string', 'max:1000'],
            'file_size_mb' => ['nullable', 'numeric', 'min:0'],
            'file_size_bytes' => ['nullable', 'integer', 'min:0'],
            'checksum' => ['nullable', 'string', 'max:255'],
            'message' => ['nullable', 'string', 'required_if:status,warning'],
            'error_message' => ['nullable', 'string', 'required_if:status,failed'],
        ];
    }

    private function messages(): array
    {
        return [
            'backup_job_id.required' => 'Backup job wajib dipilih.',
            'backup_job_id.exists' => 'Backup job harus berasal dari job yang masih aktif.',
            'status.required' => 'Status backup wajib dipilih.',
            'status.in' => 'Status backup tidak valid.',
            'backup_date.required' => 'Tanggal backup wajib diisi.',
            'backup_date.date' => 'Tanggal backup tidak valid.',
            'backup_date.before_or_equal' => 'Tanggal backup tidak boleh melebihi hari ini.',
            'finished_at.after_or_equal' => 'Waktu selesai tidak boleh lebih awal dari waktu mulai.',
            'file_size_mb.numeric' => 'Ukuran file MB harus berupa angka.',
            'file_size_mb.min' => 'Ukuran file MB tidak boleh minus.',
            'message.required_if' => 'Pesan wajib diisi jika status backup warning.',
            'error_message.required_if' => 'Error message wajib diisi jika status backup failed.',
        ];
    }

    private function activeJobs()
    {
        return BackupJob::query()
            ->where('is_active', true)
            ->with(['system.storage'])
            ->orderBy('name')
            ->get();
    }

    private function calculateDuration(?string $startedAt, ?string $finishedAt, ?int $manualDuration): ?int
    {
        if ($startedAt && $finishedAt) {
            return Carbon::parse($startedAt)->diffInSeconds(Carbon::parse($finishedAt));
        }

        return $manualDuration;
    }

    private function normalizeFileSizeBytes($bytes, $megabytes): ?int
    {
        if ($bytes !== null && $bytes !== '') {
            return (int) $bytes;
        }

        if ($megabytes !== null && $megabytes !== '') {
            return (int) round(((float) str_replace(',', '.', $megabytes)) * 1024 * 1024);
        }

        return null;
    }
}
