<?php

namespace App\Http\Controllers;

use App\Models\BackupStorage;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BackupStorageController extends Controller
{
    public function index(Request $request)
    {
        $query = BackupStorage::query();

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('code', 'like', '%' . $search . '%')
                    ->orWhere('host', 'like', '%' . $search . '%')
                    ->orWhere('base_path', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('storage_type')) {
            $query->where('storage_type', $request->storage_type);
        }

        if ($request->filled('os_type')) {
            $query->where('os_type', $request->os_type);
        }

        if ($request->filled('connection_type')) {
            $query->where('connection_type', $request->connection_type);
        }

        if ($request->filled('storage_status')) {
            $query->where('is_active', $request->storage_status === 'active');
        }

        $storages = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('backup-storages.index', compact('storages'));
    }

    public function create()
    {
        return view('backup-storages.create', [
            'storage' => new BackupStorage(),
            'osTypes' => BackupStorage::osTypes(),
            'accessTypes' => BackupStorage::accessTypes(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'os_type' => ['required', Rule::in(array_keys(BackupStorage::osTypes()))],
            'access_type' => ['required', Rule::in(array_keys(BackupStorage::accessTypes()))],
            'host' => ['nullable', 'string', 'max:255'],
            'base_path' => ['required', 'string', 'max:500'],
            'is_active' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        BackupStorage::create($validated);

        return redirect()
            ->route('backup-storages.index')
            ->with('success', 'Storage backup berhasil ditambahkan.');
    }

    public function show(BackupStorage $backupStorage)
    {
        return redirect()->route('backup-storages.edit', $backupStorage);
    }

    public function edit(BackupStorage $backupStorage)
    {
        return view('backup-storages.edit', [
            'storage' => $backupStorage,
            'osTypes' => BackupStorage::osTypes(),
            'accessTypes' => BackupStorage::accessTypes(),
        ]);
    }

    public function update(Request $request, BackupStorage $backupStorage)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'os_type' => ['required', Rule::in(array_keys(BackupStorage::osTypes()))],
            'access_type' => ['required', Rule::in(array_keys(BackupStorage::accessTypes()))],
            'host' => ['nullable', 'string', 'max:255'],
            'base_path' => ['required', 'string', 'max:500'],
            'is_active' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $backupStorage->update($validated);

        return redirect()
            ->route('backup-storages.index')
            ->with('success', 'Storage backup berhasil diperbarui.');
    }

    public function destroy(BackupStorage $backupStorage)
    {
        if ($backupStorage->backupSystems()->exists()) {
            return redirect()
                ->route('backup-storages.index')
                ->with('success', 'Storage backup tidak bisa dihapus karena masih digunakan oleh sistem backup.');
        }

        $backupStorage->delete();

        return redirect()
            ->route('backup-storages.index')
            ->with('success', 'Storage backup berhasil dihapus.');
    }

    public function toggleStatus(BackupStorage $backupStorage)
    {
        $backupStorage->update([
            'is_active' => ! $backupStorage->is_active,
        ]);

        return redirect()
            ->route('backup-storages.index')
            ->with('success', 'Status storage backup berhasil diperbarui.');
    }
}