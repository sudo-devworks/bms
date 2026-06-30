@extends('layouts.admin')

@section('title', 'Detail Sistem Backup')
@section('pageTitle', 'Detail Sistem Backup')
@section('pageSubtitle', 'Dokumentasi master sistem backup.')

@section('content')
    <div class="mx-auto max-w-7xl space-y-4">
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-6 py-5">
                <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                <i data-lucide="server" class="h-3.5 w-3.5"></i>
                                Detail Sistem Backup
                            </span>

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
                        </div>

                        <h1 class="mt-3 text-2xl font-bold tracking-tight text-slate-900">
                            {{ $system->name }}
                        </h1>

                        <p class="mt-1 max-w-3xl text-sm leading-6 text-slate-500">
                            Data master sistem backup untuk mapping Backup Job, Backup Log, dashboard monitoring, dan laporan audit.
                        </p>
                    </div>

                    <div class="flex shrink-0 items-center gap-2">
                        <a href="{{ route('backup-systems.index') }}"
                           class="inline-flex h-10 items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                            <i data-lucide="arrow-left" class="h-4 w-4"></i>
                            Kembali
                        </a>

                        <a href="{{ route('backup-systems.edit', $system) }}"
                           class="inline-flex h-10 items-center justify-center gap-2 rounded-xl bg-blue-600 px-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700">
                            <i data-lucide="pencil" class="h-4 w-4"></i>
                            Edit
                        </a>
                    </div>
                </div>
            </div>

            <div class="border-b border-slate-200 bg-slate-50 px-6 py-4">
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-indigo-100 text-indigo-700">
                            <i data-lucide="tags" class="h-4 w-4"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Kategori</p>
                            <p class="truncate text-sm font-bold text-slate-900">{{ $system->categoryLabel() }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
                            <i data-lucide="calendar-clock" class="h-4 w-4"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Frequency</p>
                            <p class="truncate text-sm font-bold text-slate-900">{{ $system->frequencyLabel() }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-blue-100 text-blue-700">
                            <i data-lucide="hash" class="h-4 w-4"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Kode</p>
                            <p class="truncate text-sm font-bold text-slate-900">{{ $system->code }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-700">
                            <i data-lucide="hard-drive" class="h-4 w-4"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Storage</p>
                            <p class="truncate text-sm font-bold text-slate-900">{{ $system->storage?->name ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid gap-0 lg:grid-cols-3">
                <div class="lg:col-span-2 lg:border-r lg:border-slate-200">
                    <div class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-slate-700">
                                <i data-lucide="info" class="h-4 w-4"></i>
                            </div>
                            <div>
                                <h2 class="text-sm font-bold text-slate-900">Informasi Backup</h2>
                                <p class="text-xs text-slate-500">Server sumber, path sumber, jadwal, dan catatan.</p>
                            </div>
                        </div>
                    </div>

                    <div class="divide-y divide-slate-100 border-t border-slate-100">
                        <div class="grid gap-2 px-6 py-3 md:grid-cols-12">
                            <div class="text-xs font-bold uppercase tracking-wider text-slate-400 md:col-span-3">
                                Server Sumber
                            </div>
                            <div class="text-sm font-semibold text-slate-900 md:col-span-9">
                                {{ $system->source_server ?: '-' }}
                            </div>
                        </div>

                        <div class="grid gap-2 px-6 py-3 md:grid-cols-12">
                            <div class="text-xs font-bold uppercase tracking-wider text-slate-400 md:col-span-3">
                                Source Path
                            </div>
                            <div class="md:col-span-9">
                                @if ($system->source_path)
                                    <code class="block max-h-24 overflow-auto rounded-xl bg-slate-100 px-3 py-2 text-xs leading-5 text-slate-800">
                                        {{ $system->source_path }}
                                    </code>
                                @else
                                    <span class="text-sm text-slate-500">-</span>
                                @endif
                            </div>
                        </div>

                        <div class="grid gap-2 px-6 py-3 md:grid-cols-12">
                            <div class="text-xs font-bold uppercase tracking-wider text-slate-400 md:col-span-3">
                                Jadwal Backup
                            </div>
                            <div class="text-sm font-semibold text-slate-900 md:col-span-9">
                                {{ $system->backup_schedule ?: '-' }}
                            </div>
                        </div>

                        <div class="grid gap-2 px-6 py-3 md:grid-cols-12">
                            <div class="text-xs font-bold uppercase tracking-wider text-slate-400 md:col-span-3">
                                Catatan
                            </div>
                            <div class="text-sm leading-6 text-slate-900 md:col-span-9">
                                {{ $system->notes ?: '-' }}
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                                <i data-lucide="hard-drive" class="h-4 w-4"></i>
                            </div>
                            <div>
                                <h2 class="text-sm font-bold text-slate-900">Storage & Metadata</h2>
                                <p class="text-xs text-slate-500">Tujuan backup dan riwayat data.</p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4 border-t border-slate-100 px-6 py-4">
                        @if ($system->storage)
                            <div>
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-bold text-slate-900">
                                            {{ $system->storage->name }}
                                        </p>
                                        <p class="mt-1 text-xs text-slate-500">
                                            {{ $system->storage->osTypeLabel() }} / {{ $system->storage->accessTypeLabel() }}
                                        </p>
                                    </div>

                                    @if ($system->storage->is_active)
                                        <span class="shrink-0 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-emerald-200">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="shrink-0 rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600 ring-1 ring-slate-200">
                                            Nonaktif
                                        </span>
                                    @endif
                                </div>

                                @if ($system->storage->base_path)
                                    <div class="mt-3">
                                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Base Path</p>
                                        <code class="mt-2 block max-h-20 overflow-auto rounded-xl bg-slate-100 px-3 py-2 text-xs leading-5 text-slate-800">
                                            {{ $system->storage->base_path }}
                                        </code>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="rounded-xl border border-red-100 bg-red-50 p-3 text-sm text-red-700">
                                Storage tujuan tidak ditemukan.
                            </div>
                        @endif

                        <div class="border-t border-slate-100 pt-4">
                            <div class="flex items-center gap-2 text-sm font-bold text-slate-900">
                                <i data-lucide="clock" class="h-4 w-4 text-slate-500"></i>
                                Metadata
                            </div>

                            <div class="mt-3 space-y-2 text-sm">
                                <div class="flex items-center justify-between gap-3">
                                    <span class="text-slate-500">Dibuat</span>
                                    <span class="font-semibold text-slate-900">
                                        {{ $system->created_at?->format('d M Y H:i') }}
                                    </span>
                                </div>

                                <div class="flex items-center justify-between gap-3">
                                    <span class="text-slate-500">Diupdate</span>
                                    <span class="font-semibold text-slate-900">
                                        {{ $system->updated_at?->format('d M Y H:i') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-xl border border-amber-100 bg-amber-50 p-3">
                            <div class="flex items-start gap-2">
                                <i data-lucide="triangle-alert" class="mt-0.5 h-4 w-4 shrink-0 text-amber-600"></i>
                                <p class="text-xs leading-5 text-amber-800">
                                    BMS tidak menjalankan backup dari halaman ini. Data ini hanya acuan monitoring dan audit.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <form action="{{ route('backup-systems.toggle-status', $system) }}" method="POST">
                @csrf
                @method('PATCH')

                <button type="submit"
                        class="inline-flex h-10 items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                    @if ($system->is_active)
                        <i data-lucide="pause-circle" class="h-4 w-4"></i>
                        Nonaktifkan Sistem
                    @else
                        <i data-lucide="play-circle" class="h-4 w-4"></i>
                        Aktifkan Sistem
                    @endif
                </button>
            </form>
        </div>
    </div>
@endsection