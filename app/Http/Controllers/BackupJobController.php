<?php

namespace App\Http\Controllers;

use App\Models\BackupJob;
use App\Models\BackupSystem;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BackupJobController extends Controller
{
    public function index(Request $request)
    {
        $query = BackupJob::query()
            ->with(['system.storage', 'latestLog']);

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('schedule_text', 'like', "%{$search}%")
                    ->orWhereHas('system', function ($systemQuery) use ($search) {
                        $systemQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('backup_system_id')) {
            $query->where('backup_system_id', $request->backup_system_id);
        }

        if ($request->filled('backup_type')) {
            $query->where('backup_type', $request->backup_type);
        }

        if ($request->filled('expected_frequency')) {
            $query->where('expected_frequency', $request->expected_frequency);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $jobs = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $summary = [
            'total' => BackupJob::count(),
            'active' => BackupJob::where('is_active', true)->count(),
            'inactive' => BackupJob::where('is_active', false)->count(),
            'daily' => BackupJob::where('expected_frequency', BackupJob::FREQUENCY_DAILY)->count(),
        ];

        return view('backup-jobs.index', [
            'jobs' => $jobs,
            'summary' => $summary,
            'systems' => BackupSystem::orderBy('name')->get(),
            'backupTypes' => BackupJob::backupTypes(),
            'frequencies' => BackupJob::frequencies(),
        ]);
    }

    public function create()
    {
        return view('backup-jobs.create', [
            'job' => new BackupJob([
                'is_active' => true,
                'expected_frequency' => BackupJob::FREQUENCY_DAILY,
                'alert_after_minutes' => 60,
            ]),
            'systems' => $this->activeSystems(),
            'backupTypes' => BackupJob::backupTypes(),
            'frequencies' => BackupJob::frequencies(),
        ]);
    }

    public function store(Request $request)
    {
        $request->merge([
            'code' => $this->normalizeCode($request->input('code')),
        ]);

        $validated = $request->validate($this->rules(), $this->messages());
        $validated['is_active'] = $request->boolean('is_active');

        BackupJob::create($validated);

        return redirect()
            ->route('backup-jobs.index')
            ->with('success', 'Backup job berhasil ditambahkan.');
    }

    public function show(BackupJob $backupJob)
    {
        $backupJob->load(['system.storage', 'logs' => function ($query) {
            $query->latest('backup_date')->latest()->limit(10);
        }]);

        return view('backup-jobs.show', [
            'job' => $backupJob,
        ]);
    }

    public function edit(BackupJob $backupJob)
    {
        return view('backup-jobs.edit', [
            'job' => $backupJob,
            'systems' => $this->activeSystems($backupJob->backup_system_id),
            'backupTypes' => BackupJob::backupTypes(),
            'frequencies' => BackupJob::frequencies(),
        ]);
    }

    public function update(Request $request, BackupJob $backupJob)
    {
        $request->merge([
            'code' => $this->normalizeCode($request->input('code')),
        ]);

        $validated = $request->validate($this->rules($backupJob), $this->messages());
        $validated['is_active'] = $request->boolean('is_active');

        $backupJob->update($validated);

        return redirect()
            ->route('backup-jobs.index')
            ->with('success', 'Backup job berhasil diperbarui.');
    }

    public function destroy(BackupJob $backupJob)
    {
        if ($backupJob->logs()->exists()) {
            return redirect()
                ->route('backup-jobs.index')
                ->with('success', 'Backup job tidak bisa dihapus karena sudah memiliki backup log. Nonaktifkan job jika tidak digunakan lagi.');
        }

        $backupJob->delete();

        return redirect()
            ->route('backup-jobs.index')
            ->with('success', 'Backup job berhasil dihapus.');
    }

    public function toggleStatus(BackupJob $backupJob)
    {
        $backupJob->update([
            'is_active' => ! $backupJob->is_active,
        ]);

        return redirect()
            ->route('backup-jobs.index')
            ->with('success', 'Status backup job berhasil diperbarui.');
    }

    private function rules(?BackupJob $backupJob = null): array
    {
        return [
            'backup_system_id' => [
                'required',
                Rule::exists('backup_systems', 'id')->where('is_active', true),
            ],
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'required',
                'string',
                'max:100',
                'regex:/^[A-Z0-9_]+$/',
                Rule::unique('backup_jobs', 'code')->ignore($backupJob?->id),
            ],
            'backup_type' => ['required', Rule::in(array_keys(BackupJob::backupTypes()))],
            'schedule_text' => ['nullable', 'string', 'max:255'],
            'expected_frequency' => ['required', Rule::in(array_keys(BackupJob::frequencies()))],
            'expected_time' => ['nullable', 'date_format:H:i'],
            'expected_run_time' => ['nullable', 'date_format:H:i'],
            'alert_after_minutes' => ['required', 'integer', 'min:0', 'max:1440'],
            'is_active' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string'],
        ];
    }

    private function messages(): array
    {
        return [
            'backup_system_id.required' => 'Sistem backup wajib dipilih.',
            'backup_system_id.exists' => 'Sistem backup harus berasal dari sistem yang masih aktif.',
            'name.required' => 'Nama job wajib diisi.',
            'code.required' => 'Kode job wajib diisi.',
            'code.unique' => 'Kode job sudah digunakan.',
            'code.regex' => 'Kode job hanya boleh berisi huruf kapital, angka, dan underscore. Contoh: TRACER_STUDY_DB_DAILY.',
            'backup_type.required' => 'Backup type wajib dipilih.',
            'backup_type.in' => 'Backup type tidak valid.',
            'expected_frequency.required' => 'Expected frequency wajib dipilih.',
            'expected_frequency.in' => 'Expected frequency tidak valid.',
            'expected_time.date_format' => 'Expected time harus memakai format jam:menit.',
            'expected_run_time.date_format' => 'Jam backup normal harus memakai format jam:menit.',
            'alert_after_minutes.required' => 'Toleransi alert wajib diisi.',
            'alert_after_minutes.integer' => 'Toleransi alert harus berupa angka menit.',
            'alert_after_minutes.min' => 'Toleransi alert minimal 0 menit.',
            'alert_after_minutes.max' => 'Toleransi alert maksimal 1440 menit.',
        ];
    }

    private function normalizeCode(?string $code): ?string
    {
        if (! $code) {
            return null;
        }

        return str($code)
            ->upper()
            ->replaceMatches('/[^A-Z0-9]+/', '_')
            ->replaceMatches('/_+/', '_')
            ->trim('_')
            ->toString();
    }

    private function activeSystems(?int $currentSystemId = null)
    {
        return BackupSystem::query()
            ->where(function ($query) use ($currentSystemId) {
                $query->where('is_active', true);

                if ($currentSystemId) {
                    $query->orWhere('id', $currentSystemId);
                }
            })
            ->with('storage')
            ->orderBy('name')
            ->get();
    }
}
