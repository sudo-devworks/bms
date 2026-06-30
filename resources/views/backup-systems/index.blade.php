@extends('layouts.admin')

@section('title', 'Sistem Backup')
@section('pageTitle', 'Sistem Backup')
@section('pageSubtitle', 'Kelola sistem, aplikasi, database, file, atau layanan yang memiliki proses backup.')

@section('content')
    <div class="mx-auto w-full max-w-[96rem] space-y-6 px-2">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-5 md:flex-row md:items-center md:justify-between">
                <div>
                    <div class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                        <i data-lucide="server" class="h-3.5 w-3.5"></i>
                        Master Data
                    </div>

                    <h1 class="mt-4 text-2xl font-bold tracking-tight text-slate-900">
                        Sistem Backup
                    </h1>

                    <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-500">
                        Kelola daftar sistem, aplikasi, database, file, atau layanan yang memiliki proses backup.
                        Data ini akan menjadi dasar Backup Job, Backup Log, dashboard monitoring, dan laporan audit.
                    </p>
                </div>

                <div class="flex shrink-0">
                    <a href="{{ route('backup-systems.create') }}"
                       class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700">
                        <i data-lucide="plus" class="h-4 w-4"></i>
                        Tambah Sistem
                    </a>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-sm font-medium text-slate-500">Total Sistem</p>
                        <p class="mt-2 text-3xl font-bold text-slate-900">{{ $summary['total'] }}</p>
                    </div>
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                        <i data-lucide="server" class="h-5 w-5"></i>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-sm font-medium text-slate-500">Sistem Aktif</p>
                        <p class="mt-2 text-3xl font-bold text-slate-900">{{ $summary['active'] }}</p>
                    </div>
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                        <i data-lucide="check-circle-2" class="h-5 w-5"></i>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-sm font-medium text-slate-500">Sistem Nonaktif</p>
                        <p class="mt-2 text-3xl font-bold text-slate-900">{{ $summary['inactive'] }}</p>
                    </div>
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-600">
                        <i data-lucide="pause-circle" class="h-5 w-5"></i>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-sm font-medium text-slate-500">Backup Harian</p>
                        <p class="mt-2 text-3xl font-bold text-slate-900">{{ $summary['daily'] }}</p>
                    </div>
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
                        <i data-lucide="calendar-days" class="h-5 w-5"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-6 py-5">
                <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h2 class="text-base font-semibold text-slate-900">
                            Daftar Sistem Backup
                        </h2>
                        <p class="mt-1 text-sm text-slate-500">
                            Semua sistem yang terdaftar sebagai objek monitoring backup.
                        </p>
                    </div>

                    <div class="text-sm text-slate-500">
                        Ditampilkan:
                        <span class="font-semibold text-slate-900">{{ $systems->total() }}</span>
                        sistem
                    </div>
                </div>
            </div>

            @php
                $hasAdvancedFilter = request()->filled('category')
                    || request()->filled('backup_storage_id')
                    || request()->filled('status')
                    || request()->filled('storage_status');
            @endphp

            <form method="GET"
                action="{{ route('backup-systems.index') }}"
                x-data="{ open: {{ $hasAdvancedFilter ? 'true' : 'false' }} }"
                class="border-b border-slate-200 bg-slate-50 px-6 py-5">

                <div class="flex flex-col gap-3 lg:flex-row lg:items-end">
                    <div class="flex-1">
                        <label for="search" class="block text-xs font-bold uppercase tracking-wider text-slate-500">
                            Cari Sistem
                        </label>

                        <div class="mt-2 flex rounded-xl shadow-sm">
                            <span class="inline-flex items-center rounded-l-xl border border-r-0 border-slate-300 bg-white px-3 text-slate-400">
                                <i data-lucide="search" class="h-4 w-4"></i>
                            </span>

                            <input type="text"
                                id="search"
                                name="search"
                                value="{{ request('search') }}"
                                class="block w-full rounded-r-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Nama, kode, server, atau path">
                        </div>
                    </div>

                    <div class="flex flex-col gap-2 sm:flex-row">
                        <button type="button"
                                x-on:click="open = !open"
                                class="inline-flex h-10 items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                            <i data-lucide="sliders-horizontal" class="h-4 w-4"></i>
                            Filter Detail
                            @if ($hasAdvancedFilter)
                                <span class="ml-1 inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-blue-600 px-1.5 text-[11px] font-bold text-white">
                                    {{ collect([
                                        request()->filled('category'),
                                        request()->filled('backup_storage_id'),
                                        request()->filled('status'),
                                        request()->filled('storage_status'),
                                    ])->filter()->count() }}
                                </span>
                            @endif
                        </button>

                        <a href="{{ route('backup-systems.index') }}"
                        class="inline-flex h-10 items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                            <i data-lucide="rotate-ccw" class="h-4 w-4"></i>
                            Reset
                        </a>

                        <button type="submit"
                                class="inline-flex h-10 items-center justify-center gap-2 rounded-xl bg-slate-900 px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800">
                            <i data-lucide="filter" class="h-4 w-4"></i>
                            Terapkan
                        </button>
                    </div>
                </div>

                @if (request()->filled('search') || $hasAdvancedFilter)
                    <div class="mt-4 flex flex-wrap items-center gap-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">
                            Filter aktif:
                        </span>

                        @if (request()->filled('search'))
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700 ring-1 ring-slate-200">
                                <i data-lucide="search" class="h-3.5 w-3.5"></i>
                                {{ request('search') }}
                            </span>
                        @endif

                        @if (request()->filled('category'))
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700 ring-1 ring-indigo-100">
                                <i data-lucide="tags" class="h-3.5 w-3.5"></i>
                                {{ $categories[request('category')] ?? request('category') }}
                            </span>
                        @endif

                        @if (request()->filled('backup_storage_id'))
                            @php
                                $selectedStorage = $storages->firstWhere('id', (int) request('backup_storage_id'));
                            @endphp

                            <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700 ring-1 ring-amber-100">
                                <i data-lucide="hard-drive" class="h-3.5 w-3.5"></i>
                                {{ $selectedStorage?->name ?? 'Storage terpilih' }}
                            </span>
                        @endif

                        @if (request()->filled('status'))
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-emerald-100">
                                @if (request('status') === 'active')
                                    <i data-lucide="check-circle-2" class="h-3.5 w-3.5"></i>
                                    Sistem aktif
                                @else
                                    <i data-lucide="pause-circle" class="h-3.5 w-3.5"></i>
                                    Sistem nonaktif
                                @endif
                            </span>
                        @endif

                        @if (request()->filled('storage_status'))
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700 ring-1 ring-blue-100">
                                @if (request('storage_status') === 'active')
                                    <i data-lucide="hard-drive" class="h-3.5 w-3.5"></i>
                                    Storage aktif
                                @else
                                    <i data-lucide="triangle-alert" class="h-3.5 w-3.5"></i>
                                    Storage nonaktif
                                @endif
                            </span>
                        @endif

                        <a href="{{ route('backup-systems.index') }}"
                        class="inline-flex items-center gap-1.5 rounded-full bg-white px-3 py-1 text-xs font-semibold text-red-600 ring-1 ring-red-100 transition hover:bg-red-50">
                            <i data-lucide="x" class="h-3.5 w-3.5"></i>
                            Bersihkan
                        </a>
                    </div>
                @endif

                <div x-show="open"
                    x-cloak
                    class="mt-5 rounded-2xl border border-slate-200 bg-white p-4">
                    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                        <div>
                            <label for="category" class="block text-xs font-bold uppercase tracking-wider text-slate-500">
                                Kategori
                            </label>
                            <select id="category"
                                    name="category"
                                    class="mt-2 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Semua kategori</option>
                                @foreach ($categories as $value => $label)
                                    <option value="{{ $value }}" @selected(request('category') === $value)>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="backup_storage_id" class="block text-xs font-bold uppercase tracking-wider text-slate-500">
                                Storage
                            </label>
                            <select id="backup_storage_id"
                                    name="backup_storage_id"
                                    class="mt-2 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Semua storage</option>
                                @foreach ($storages as $storage)
                                    <option value="{{ $storage->id }}" @selected((string) request('backup_storage_id') === (string) $storage->id)>
                                        {{ $storage->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="status" class="block text-xs font-bold uppercase tracking-wider text-slate-500">
                                Status Sistem
                            </label>
                            <select id="status"
                                    name="status"
                                    class="mt-2 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Semua status</option>
                                <option value="active" @selected(request('status') === 'active')>Aktif</option>
                                <option value="inactive" @selected(request('status') === 'inactive')>Nonaktif</option>
                            </select>
                        </div>

                        <div>
                            <label for="storage_status" class="block text-xs font-bold uppercase tracking-wider text-slate-500">
                                Status Storage
                            </label>
                            <select id="storage_status"
                                    name="storage_status"
                                    class="mt-2 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Semua storage</option>
                                <option value="active" @selected(request('storage_status') === 'active')>Storage aktif</option>
                                <option value="inactive" @selected(request('storage_status') === 'inactive')>Storage nonaktif</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-4 flex items-start gap-2 rounded-xl bg-slate-50 px-3 py-2 text-xs leading-5 text-slate-500">
                        <i data-lucide="info" class="mt-0.5 h-4 w-4 shrink-0"></i>
                        <span>
                            Filter detail dipakai untuk mempersempit daftar sistem berdasarkan kategori, storage tujuan, status sistem, dan status storage.
                        </span>
                    </div>
                </div>
            </form>

            @if ($systems->count())
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Sistem</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Kategori</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Server Sumber</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Storage Tujuan</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Frekuensi</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Status</th>
                                <th class="w-[360px] px-4 py-3 text-right text-xs font-bold uppercase tracking-wider text-slate-500">Aksi</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-200 bg-white">
                            @foreach ($systems as $system)
                                <tr class="transition hover:bg-slate-50">
                                    <td class="px-4 py-3 align-top">
                                        <div class="font-semibold text-slate-900">
                                            {{ $system->name }}
                                        </div>

                                        <div class="mt-1 text-xs font-medium uppercase tracking-wide text-slate-400">
                                            {{ $system->code }}
                                        </div>

                                        @if ($system->source_path)
                                            <code class="mt-2 block max-w-xs truncate rounded-lg bg-slate-100 px-3 py-2 text-xs text-slate-700"
                                                title="{{ $system->source_path }}">
                                                {{ $system->source_path }}
                                            </code>
                                        @endif

                                        @if ($system->notes)
                                            <div class="mt-2 max-w-xs text-xs leading-5 text-slate-500">
                                                {{ $system->notes }}
                                            </div>
                                        @endif
                                    </td>

                                    <td class="px-4 py-3 align-top">
                                        <span class="inline-flex rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700 ring-1 ring-indigo-100">
                                            {{ $system->categoryLabel() }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-3 align-top text-sm text-slate-600">
                                        {{ $system->source_server ?: '-' }}
                                    </td>

                                    <td class="px-4 py-3 align-top">
                                        @if ($system->storage)
                                            <div class="flex flex-wrap items-center gap-2">
                                                <div class="text-sm font-semibold text-slate-900">
                                                    {{ $system->storage->name }}
                                                </div>

                                                @if ($system->storage->is_active)
                                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-0.5 text-[11px] font-semibold text-emerald-700 ring-1 ring-emerald-200">
                                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                                        Aktif
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2 py-0.5 text-[11px] font-semibold text-amber-700 ring-1 ring-amber-200">
                                                        <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                                        Storage nonaktif
                                                    </span>
                                                @endif
                                            </div>

                                            <div class="mt-1 text-xs text-slate-500">
                                                {{ $system->storage->osTypeLabel() }} / {{ $system->storage->accessTypeLabel() }}
                                            </div>
                                        @else
                                            <span class="inline-flex items-center gap-1 rounded-full bg-red-50 px-2.5 py-1 text-xs font-semibold text-red-700 ring-1 ring-red-200">
                                                <i data-lucide="triangle-alert" class="h-3.5 w-3.5"></i>
                                                Storage hilang
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-4 py-3 align-top">
                                        <div class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">
                                            {{ $system->frequencyLabel() }}
                                        </div>

                                        @if ($system->backup_schedule)
                                            <div class="mt-2 max-w-xs text-xs leading-5 text-slate-500">
                                                {{ $system->backup_schedule }}
                                            </div>
                                        @endif
                                    </td>

                                    <td class="px-4 py-3 align-top">
                                        @if ($system->is_active)
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

                                    <td class="px-4 py-3 align-top">
                                        <div class="flex items-center justify-end gap-1.5 whitespace-nowrap">
                                            <a href="{{ route('backup-systems.show', $system) }}"
                                            class="inline-flex items-center gap-1.5 rounded-lg border border-blue-200 px-2.5 py-1.5 text-xs font-semibold text-blue-700 transition hover:bg-blue-50">
                                                <i data-lucide="eye" class="h-3.5 w-3.5"></i>
                                                Detail
                                            </a>

                                            <form action="{{ route('backup-systems.toggle-status', $system) }}" method="POST">
                                                @csrf
                                                @method('PATCH')

                                                <button type="submit"
                                                        class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 px-2.5 py-1.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-100">
                                                    @if ($system->is_active)
                                                        <i data-lucide="pause-circle" class="h-3.5 w-3.5"></i>
                                                        Nonaktifkan
                                                    @else
                                                        <i data-lucide="play-circle" class="h-3.5 w-3.5"></i>
                                                        Aktifkan
                                                    @endif
                                                </button>
                                            </form>

                                            <a href="{{ route('backup-systems.edit', $system) }}"
                                            class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 px-2.5 py-1.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-100">
                                                <i data-lucide="pencil" class="h-3.5 w-3.5"></i>
                                                Edit
                                            </a>

                                            <form action="{{ route('backup-systems.destroy', $system) }}"
                                                method="POST"
                                                onsubmit="return confirm('Yakin ingin menghapus sistem backup ini?')">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                        class="inline-flex items-center gap-1.5 rounded-lg border border-red-200 px-2.5 py-1.5 text-xs font-semibold text-red-600 transition hover:bg-red-50">
                                                    <i data-lucide="trash-2" class="h-3.5 w-3.5"></i>
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

                @if ($systems->hasPages())
                    <div class="border-t border-slate-200 px-6 py-4">
                        {{ $systems->links() }}
                    </div>
                @endif
            @else
                <div class="px-6 py-16 text-center">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100">
                        <i data-lucide="server" class="h-7 w-7 text-slate-400"></i>
                    </div>

                    <h3 class="mt-5 text-base font-semibold text-slate-900">
                        Belum ada sistem backup
                    </h3>

                    <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">
                        Tambahkan sistem pertama seperti Tracer Study, Keuangan, EPrala, OJS, Zimbra, PMB, atau sistem lain yang memiliki proses backup.
                    </p>

                    <a href="{{ route('backup-systems.create') }}"
                       class="mt-6 inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700">
                        <i data-lucide="plus" class="h-4 w-4"></i>
                        Tambah Sistem
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection