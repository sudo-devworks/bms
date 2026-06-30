<?php

namespace App\Http\Controllers;

use App\Models\BackupStorage;
use App\Models\BackupSystem;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BackupSystemController extends Controller
{
    public function index(Request $request)
    {
        $query = BackupSystem::query()
            ->with('storage');

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('source_server', 'like', "%{$search}%")
                    ->orWhere('source_path', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('backup_storage_id')) {
            $query->where('backup_storage_id', $request->backup_storage_id);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->filled('storage_status')) {
            $query->whereHas('storage', function ($q) use ($request) {
                $q->where('is_active', $request->storage_status === 'active');
            });
        }

        $systems = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $summary = [
            'total' => BackupSystem::count(),
            'active' => BackupSystem::where('is_active', true)->count(),
            'inactive' => BackupSystem::where('is_active', false)->count(),
            'daily' => BackupSystem::where('expected_frequency', BackupSystem::FREQUENCY_DAILY)->count(),
        ];

        return view('backup-systems.index', [
            'systems' => $systems,
            'summary' => $summary,
            'storages' => BackupStorage::orderBy('name')->get(),
            'categories' => BackupSystem::categories(),
        ]);
    }

    public function create()
    {
        return view('backup-systems.create', [
            'system' => new BackupSystem([
                'is_active' => true,
                'expected_frequency' => BackupSystem::FREQUENCY_DAILY,
            ]),
            'storages' => $this->activeStorages(),
            'categories' => BackupSystem::categories(),
            'frequencies' => BackupSystem::frequencies(),
        ]);
    }

    public function store(Request $request)
    {
        $request->merge([
            'code' => $this->normalizeCode($request->input('code')),
        ]);

        $validated = $request->validate($this->rules(), $this->messages());

        $validated['is_active'] = $request->boolean('is_active');

        BackupSystem::create($validated);

        return redirect()
            ->route('backup-systems.index')
            ->with('success', 'Sistem backup berhasil ditambahkan.');
    }

    public function show(BackupSystem $backupSystem)
    {
        $backupSystem->load('storage');

        return view('backup-systems.show', [
            'system' => $backupSystem,
        ]);
    }

    public function edit(BackupSystem $backupSystem)
    {
        return view('backup-systems.edit', [
            'system' => $backupSystem,
            'storages' => $this->activeStorages($backupSystem->backup_storage_id),
            'categories' => BackupSystem::categories(),
            'frequencies' => BackupSystem::frequencies(),
        ]);
    }

    public function update(Request $request, BackupSystem $backupSystem)
    {
        $request->merge([
            'code' => $this->normalizeCode($request->input('code')),
        ]);

        $validated = $request->validate($this->rules($backupSystem), $this->messages());

        $validated['is_active'] = $request->boolean('is_active');

        $backupSystem->update($validated);

        return redirect()
            ->route('backup-systems.index')
            ->with('success', 'Sistem backup berhasil diperbarui.');
    }

    public function destroy(BackupSystem $backupSystem)
    {
        $backupSystem->delete();

        return redirect()
            ->route('backup-systems.index')
            ->with('success', 'Sistem backup berhasil dihapus.');
    }

    public function toggleStatus(BackupSystem $backupSystem)
    {
        $backupSystem->update([
            'is_active' => ! $backupSystem->is_active,
        ]);

        return redirect()
            ->route('backup-systems.index')
            ->with('success', 'Status sistem backup berhasil diperbarui.');
    }

    private function rules(?BackupSystem $backupSystem = null): array
    {
        return [
            'backup_storage_id' => [
                'required',
                Rule::exists('backup_storages', 'id')->where('is_active', true),
            ],
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'required',
                'string',
                'max:100',
                'regex:/^[A-Z0-9_]+$/',
                Rule::unique('backup_systems', 'code')->ignore($backupSystem?->id),
            ],
            'category' => ['required', Rule::in(array_keys(BackupSystem::categories()))],
            'source_server' => ['nullable', 'string', 'max:255'],
            'source_path' => ['nullable', 'string', 'max:500'],
            'backup_schedule' => ['nullable', 'string', 'max:255'],
            'expected_frequency' => ['required', Rule::in(array_keys(BackupSystem::frequencies()))],
            'is_active' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string'],
        ];
    }

    private function messages(): array
    {
        return [
            'backup_storage_id.required' => 'Storage tujuan wajib dipilih.',
            'backup_storage_id.exists' => 'Storage tujuan harus berasal dari storage yang masih aktif.',
            'name.required' => 'Nama sistem wajib diisi.',
            'code.required' => 'Kode sistem wajib diisi.',
            'code.unique' => 'Kode sistem sudah digunakan.',
            'code.regex' => 'Kode sistem hanya boleh berisi huruf kapital, angka, dan underscore. Contoh: TRACER_STUDY.',
            'category.required' => 'Kategori backup wajib dipilih.',
            'category.in' => 'Kategori backup tidak valid.',
            'expected_frequency.required' => 'Expected frequency wajib dipilih.',
            'expected_frequency.in' => 'Expected frequency tidak valid.',
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

    private function activeStorages(?int $currentStorageId = null)
    {
        return BackupStorage::query()
            ->where(function ($query) use ($currentStorageId) {
                $query->where('is_active', true);

                if ($currentStorageId) {
                    $query->orWhere('id', $currentStorageId);
                }
            })
            ->orderBy('name')
            ->get();
    }
}