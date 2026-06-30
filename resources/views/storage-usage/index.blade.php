@extends('layouts.admin')

@section('title', 'Storage Usage')

@section('content')
    <div class="space-y-6">
        <div class="overflow-hidden rounded-2xl bg-slate-950 shadow-sm ring-1 ring-slate-900/10">
            <div class="relative isolate p-6 sm:p-8">
                <div class="absolute inset-0 -z-10 bg-[radial-gradient(circle_at_top_right,rgba(34,211,238,0.24),transparent_35%),radial-gradient(circle_at_bottom_left,rgba(59,130,246,0.18),transparent_35%)]"></div>

                <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <div class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 text-xs font-semibold text-cyan-100 ring-1 ring-white/15">
                            <i data-lucide="hard-drive" class="h-3.5 w-3.5"></i>
                            Storage Monitoring
                        </div>

                        <h1 class="mt-4 text-2xl font-bold tracking-tight text-white sm:text-3xl">
                            Storage Usage
                        </h1>

                        <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-300">
                            Pantau kapasitas, status online/offline, dan waktu pengecekan terakhir storage backup.
                            Data di halaman ini dibaca dari database, bukan hasil scan folder saat halaman dibuka.
                        </p>
                    </div>

                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4 lg:min-w-[520px]">
                        <div class="rounded-2xl bg-white/10 p-4 ring-1 ring-white/15">
                            <p class="text-xs font-medium text-slate-300">Total Storage</p>
                            <p class="mt-2 text-2xl font-bold text-white">{{ $totalStorages }}</p>
                        </div>

                        <div class="rounded-2xl bg-white/10 p-4 ring-1 ring-white/15">
                            <p class="text-xs font-medium text-slate-300">Aktif</p>
                            <p class="mt-2 text-2xl font-bold text-white">{{ $activeStorages }}</p>
                        </div>

                        <div class="rounded-2xl bg-emerald-400/10 p-4 ring-1 ring-emerald-300/20">
                            <p class="text-xs font-medium text-emerald-100">Online</p>
                            <p class="mt-2 text-2xl font-bold text-white">{{ $onlineStorages }}</p>
                        </div>

                        <div class="rounded-2xl bg-rose-400/10 p-4 ring-1 ring-rose-300/20">
                            <p class="text-xs font-medium text-rose-100">Offline</p>
                            <p class="mt-2 text-2xl font-bold text-white">{{ $offlineStorages }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid gap-4 lg:grid-cols-4">
            <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-sm font-medium text-slate-500">Total Capacity</p>
                        <p class="mt-2 text-2xl font-bold text-slate-900">{{ $totalSpaceLabel }}</p>
                    </div>
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-100 text-slate-700">
                        <i data-lucide="database" class="h-5 w-5"></i>
                    </div>
                </div>
            </div>

            <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-sm font-medium text-slate-500">Used Space</p>
                        <p class="mt-2 text-2xl font-bold text-slate-900">{{ $usedSpaceLabel }}</p>
                    </div>
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-amber-50 text-amber-700">
                        <i data-lucide="archive" class="h-5 w-5"></i>
                    </div>
                </div>
            </div>

            <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-sm font-medium text-slate-500">Free Space</p>
                        <p class="mt-2 text-2xl font-bold text-slate-900">{{ $freeSpaceLabel }}</p>
                    </div>
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-700">
                        <i data-lucide="circle-check" class="h-5 w-5"></i>
                    </div>
                </div>
            </div>

            <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-sm font-medium text-slate-500">Overall Usage</p>
                        <p class="mt-2 text-2xl font-bold text-slate-900">
                            {{ $overallUsagePercent !== null ? number_format($overallUsagePercent, 1) . '%' : '-' }}
                        </p>
                    </div>
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-cyan-50 text-cyan-700">
                        <i data-lucide="gauge" class="h-5 w-5"></i>
                    </div>
                </div>

                @php
                    $overallProgressWidth = $overallUsagePercent !== null
                        ? min(max((float) $overallUsagePercent, 0), 100)
                        : 0;
                @endphp

                <div class="mt-4">
                    <div class="mb-2 flex items-center justify-between text-xs">
                        <span class="font-semibold text-slate-500">Progress penggunaan</span>
                        <span class="font-bold text-slate-700">
                            {{ $overallUsagePercent !== null ? number_format($overallUsagePercent, 1) . '%' : '-' }}
                        </span>
                    </div>

                    <div class="h-3 overflow-hidden rounded-full bg-slate-100 ring-1 ring-slate-200">
                        <div
                            class="h-full rounded-full bg-cyan-500 transition-all duration-500"
                            style="width: {{ $overallProgressWidth }}%;"
                        ></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="rounded-3xl bg-white shadow-sm ring-1 ring-slate-200">
            <div class="flex flex-col gap-3 border-b border-slate-100 p-5 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Daftar Storage</h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Ringkasan kapasitas dan status terakhir setiap storage backup.
                    </p>
                </div>

                <a href="{{ route('backup-storages.index') }}"
                   class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    <i data-lucide="settings-2" class="h-4 w-4"></i>
                    Kelola Storage
                </a>
            </div>

            @if ($storages->isEmpty())
                <div class="p-10 text-center">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-500">
                        <i data-lucide="hard-drive" class="h-7 w-7"></i>
                    </div>
                    <h3 class="mt-4 text-base font-bold text-slate-900">Belum ada storage</h3>
                    <p class="mt-2 text-sm text-slate-500">
                        Tambahkan storage backup terlebih dahulu sebelum monitoring kapasitas.
                    </p>
                    <a href="{{ route('backup-storages.create') }}"
                       class="mt-5 inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">
                        <i data-lucide="plus" class="h-4 w-4"></i>
                        Tambah Storage
                    </a>
                </div>
            @else
                <div class="grid gap-4 p-5 lg:grid-cols-2">
                    @foreach ($storages as $storage)
                        @php
                            $usagePercent = $storage->usage_percent !== null
                                ? (float) $storage->usage_percent
                                : null;

                            $progressWidth = $usagePercent !== null
                                ? min(max($usagePercent, 0), 100)
                                : 0;

                            $progressClass = match (true) {
                                $usagePercent === null => 'bg-slate-300',
                                $usagePercent >= 90 => 'bg-rose-500',
                                $usagePercent >= 75 => 'bg-amber-500',
                                default => 'bg-emerald-500',
                            };
                        @endphp

                        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h3 class="truncate text-base font-bold text-slate-900">
                                            {{ $storage->name }}
                                        </h3>

                                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 {{ $storage->checkStatusBadgeClass() }}">
                                            {{ $storage->checkStatusLabel() }}
                                        </span>

                                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 {{ $storage->healthBadgeClass() }}">
                                            {{ $storage->healthLabel() }}
                                        </span>

                                        @if (! $storage->is_active)
                                            <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600 ring-1 ring-slate-200">
                                                Nonaktif
                                            </span>
                                        @endif
                                    </div>

                                    <div class="mt-2 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-slate-500">
                                        <span class="inline-flex items-center gap-1">
                                            <i data-lucide="monitor" class="h-3.5 w-3.5"></i>
                                            {{ $storage->osTypeLabel() }}
                                        </span>
                                        <span class="inline-flex items-center gap-1">
                                            <i data-lucide="network" class="h-3.5 w-3.5"></i>
                                            {{ $storage->accessTypeLabel() }}
                                        </span>
                                        <span class="inline-flex items-center gap-1">
                                            <i data-lucide="clock" class="h-3.5 w-3.5"></i>
                                            {{ $storage->lastCheckedLabel() }}
                                        </span>
                                    </div>
                                </div>

                                <div class="flex shrink-0 flex-wrap items-center gap-2">
                                    <a href="{{ route('storage-usage.edit', $storage) }}"
                                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-cyan-50 px-3 py-2 text-xs font-semibold text-cyan-700 ring-1 ring-cyan-100 hover:bg-cyan-100">
                                        <i data-lucide="pencil" class="h-3.5 w-3.5"></i>
                                        Update
                                    </a>

                                    <a href="{{ route('backup-storages.show', $storage) }}"
                                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                        Detail
                                        <i data-lucide="arrow-right" class="h-3.5 w-3.5"></i>
                                    </a>
                                </div>
                            </div>

                            <div class="mt-5 grid grid-cols-3 gap-3">
                                <div class="rounded-2xl bg-slate-50 p-3">
                                    <p class="text-xs font-medium text-slate-500">Total</p>
                                    <p class="mt-1 text-sm font-bold text-slate-900">{{ $storage->totalSpaceLabel() }}</p>
                                </div>

                                <div class="rounded-2xl bg-slate-50 p-3">
                                    <p class="text-xs font-medium text-slate-500">Used</p>
                                    <p class="mt-1 text-sm font-bold text-slate-900">{{ $storage->usedSpaceLabel() }}</p>
                                </div>

                                <div class="rounded-2xl bg-slate-50 p-3">
                                    <p class="text-xs font-medium text-slate-500">Free</p>
                                    <p class="mt-1 text-sm font-bold text-slate-900">{{ $storage->freeSpaceLabel() }}</p>
                                </div>
                            </div>

                            <div class="mt-5">
                                <div class="mb-2 flex items-center justify-between gap-3">
                                    <div>
                                        <p class="text-sm font-semibold text-slate-700">Usage</p>
                                        <p class="text-xs text-slate-500">{{ $storage->healthLabel() }}</p>
                                    </div>

                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 {{ $storage->usageBadgeClass() }}">
                                        {{ $storage->usagePercentLabel() }}
                                    </span>
                                </div>

                                <div class="h-3 overflow-hidden rounded-full bg-slate-100 ring-1 ring-slate-200">
                                    <div
                                        class="h-full rounded-full {{ $progressClass }} transition-all duration-500"
                                        style="width: {{ $progressWidth }}%;"
                                    ></div>
                                </div>
                            </div>

                            @if ($storage->last_check_message)
                                <div class="mt-4 rounded-2xl bg-slate-50 p-3 text-xs leading-5 text-slate-600">
                                    <div class="mb-1 flex items-center gap-2 font-semibold text-slate-700">
                                        <i data-lucide="message-circle" class="h-3.5 w-3.5"></i>
                                        Pesan Check
                                    </div>
                                    {{ $storage->last_check_message }}
                                </div>
                            @endif

                            <div class="mt-4 rounded-2xl bg-cyan-50 p-3 text-xs leading-5 text-cyan-800 ring-1 ring-cyan-100">
                                <div class="flex items-start gap-2">
                                    <i data-lucide="info" class="mt-0.5 h-3.5 w-3.5 shrink-0"></i>
                                    <span>
                                        Data kapasitas storage ini berasal dari database. Update otomatis via script/API akan kita siapkan setelah update manual aman.
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection