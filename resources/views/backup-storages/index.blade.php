@extends('layouts.admin')

@section('title', 'Storage Backup')
@section('pageTitle', 'Storage Backup')
@section('pageSubtitle', 'Kelola lokasi penyimpanan backup.')

@section('content')
    <div class="mx-auto max-w-7xl space-y-6">

        {{-- Page Header --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-5 md:flex-row md:items-center md:justify-between">
                <div>
                    <div class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                        <i data-lucide="database" class="h-3.5 w-3.5"></i>
                        Master Data
                    </div>

                    <h1 class="mt-4 text-2xl font-bold tracking-tight text-slate-900">
                        Storage Backup
                    </h1>

                    <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-500">
                        Kelola lokasi penyimpanan backup, baik storage sementara di Windows maupun server backup Linux.
                        Data ini akan dipakai untuk mapping sistem backup pada milestone berikutnya.
                    </p>
                </div>

                <div class="flex shrink-0">
                    <a href="{{ route('backup-storages.create') }}"
                       class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700">
                        <i data-lucide="plus" class="h-4 w-4"></i>
                        Tambah Storage
                    </a>
                </div>
            </div>
        </div>

        {{-- Alert --}}
        @if (session('success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        {{-- Content --}}
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-col gap-2 border-b border-slate-200 px-6 py-5 md:flex-row md:items-center md:justify-between">
                <div>
                    <h2 class="text-base font-semibold text-slate-900">
                        Daftar Storage
                    </h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Semua lokasi penyimpanan backup yang tersedia.
                    </p>
                </div>

                <div class="text-sm text-slate-500">
                    Total: <span class="font-semibold text-slate-900">{{ $storages->total() }}</span> storage
                </div>
            </div>

            @if ($storages->count())
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Nama</th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">OS</th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Akses</th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Host</th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Base Path</th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-bold uppercase tracking-wider text-slate-500">Aksi</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-200 bg-white">
                            @foreach ($storages as $storage)
                                <tr class="transition hover:bg-slate-50">
                                    <td class="px-6 py-4 align-top">
                                        <div class="font-semibold text-slate-900">
                                            {{ $storage->name }}
                                        </div>

                                        @if ($storage->notes)
                                            <div class="mt-1 max-w-xs text-xs leading-5 text-slate-500">
                                                {{ $storage->notes }}
                                            </div>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 align-top">
                                        <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">
                                            {{ $storage->osTypeLabel() }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-4 align-top">
                                        <span class="inline-flex rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700 ring-1 ring-indigo-100">
                                            {{ $storage->accessTypeLabel() }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-4 align-top text-sm text-slate-600">
                                        {{ $storage->host ?: '-' }}
                                    </td>

                                    <td class="px-6 py-4 align-top">
                                        <code class="block max-w-sm rounded-lg bg-slate-100 px-3 py-2 text-xs text-slate-700">
                                            {{ $storage->base_path }}
                                        </code>
                                    </td>

                                    <td class="px-6 py-4 align-top">
                                        @if ($storage->is_active)
                                            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-emerald-200">
                                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                                Aktif
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600 ring-1 ring-slate-200">
                                                <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>
                                                Nonaktif
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 align-top">
                                        <div class="flex items-center justify-end gap-2">
                                            <form action="{{ route('backup-storages.toggle-status', $storage) }}" method="POST">
                                                @csrf
                                                @method('PATCH')

                                                <button type="submit"
                                                        class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-100">
                                                    {{ $storage->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                                </button>
                                            </form>

                                            <a href="{{ route('backup-storages.edit', $storage) }}"
                                               class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-100">
                                                Edit
                                            </a>

                                            <form action="{{ route('backup-storages.destroy', $storage) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('Yakin ingin menghapus storage ini?')">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                        class="rounded-lg border border-red-200 px-3 py-2 text-xs font-semibold text-red-600 transition hover:bg-red-50">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($storages->hasPages())
                    <div class="border-t border-slate-200 px-6 py-4">
                        {{ $storages->links() }}
                    </div>
                @endif
            @else
                <div class="px-6 py-16 text-center">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100">
                        <i data-lucide="hard-drive" class="h-7 w-7 text-slate-400"></i>
                    </div>

                    <h3 class="mt-5 text-base font-semibold text-slate-900">
                        Belum ada storage backup
                    </h3>

                    <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">
                        Tambahkan lokasi penyimpanan backup pertama. Contohnya storage Windows sementara,
                        mount Linux, SMB share, atau server backup via SSH.
                    </p>

                    <a href="{{ route('backup-storages.create') }}"
                       class="mt-6 inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700">
                        <i data-lucide="plus" class="h-4 w-4"></i>
                        Tambah Storage
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection