@extends('layouts.admin')

@section('title', 'Dashboard')
@section('pageTitle', 'Dashboard Monitoring')
@section('pageSubtitle', 'Monitoring hasil backup otomatis hari ini.')

@section('content')
    @php
        $statusMeta = [
            'success' => [
                'label' => 'Success',
                'icon' => 'check-circle-2',
                'badge' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
                'soft' => 'bg-emerald-50 text-emerald-700',
                'border' => 'border-emerald-200',
            ],
            'failed' => [
                'label' => 'Failed',
                'icon' => 'x-circle',
                'badge' => 'bg-red-50 text-red-700 ring-red-600/20',
                'soft' => 'bg-red-50 text-red-700',
                'border' => 'border-red-200',
            ],
            'warning' => [
                'label' => 'Warning',
                'icon' => 'alert-triangle',
                'badge' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
                'soft' => 'bg-amber-50 text-amber-700',
                'border' => 'border-amber-200',
            ],
            'pending' => [
                'label' => 'Pending',
                'icon' => 'clock-3',
                'badge' => 'bg-slate-100 text-slate-700 ring-slate-500/20',
                'soft' => 'bg-slate-100 text-slate-700',
                'border' => 'border-slate-200',
            ],
        ];

        $overallIcon = match ($overallStatus) {
            'success' => 'shield-check',
            'failed' => 'shield-alert',
            'warning' => 'alert-triangle',
            'pending' => 'clock-3',
            default => 'activity',
        };

        $overallClass = match ($overallStatus) {
            'success' => 'bg-emerald-500/15 text-emerald-200 ring-emerald-400/20',
            'failed' => 'bg-red-500/15 text-red-200 ring-red-400/20',
            'warning' => 'bg-amber-500/15 text-amber-200 ring-amber-400/20',
            'pending' => 'bg-slate-500/20 text-slate-200 ring-slate-400/20',
            default => 'bg-white/10 text-white ring-white/10',
        };

        $statusBadgeClass = fn (?string $status) => $statusMeta[$status]['badge'] ?? 'bg-slate-100 text-slate-700 ring-slate-500/20';
        $statusIcon = fn (?string $status) => $statusMeta[$status]['icon'] ?? 'circle';
        $statusLabel = fn (?string $status) => $statusMeta[$status]['label'] ?? '-';
        $lastRefreshedAt = now();
        $lastRefreshedAt = now();

        $emptyBoxClass = 'rounded-xl border border-dashed border-slate-200 bg-slate-50 px-4 py-5 text-center';
    @endphp

    <div class="space-y-6">
        {{-- Compact Hero --}}
        <section class="rounded-2xl bg-slate-950 p-5 text-white shadow-sm">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <div class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 text-xs font-semibold text-cyan-100 ring-1 ring-white/10">
                        <i data-lucide="activity" class="h-4 w-4"></i>
                        Dashboard Monitoring
                    </div>

                    <h1 class="mt-3 text-xl font-bold sm:text-2xl">
                        Kondisi Backup Hari Ini
                    </h1>

                    <p class="mt-1 text-sm text-slate-300">
                        Berdasarkan log yang masuk ke database pada
                        <span class="font-semibold text-white">{{ $today->format('d M Y') }}</span>.
                    </p>
                    <div class="mt-4 inline-flex max-w-xl items-start gap-3 rounded-2xl px-4 py-3 ring-1 {{ $overallClass }}">
                        <i data-lucide="{{ $overallIcon }}" class="mt-0.5 h-5 w-5 shrink-0"></i>
                        <div>
                            <div class="text-sm font-bold">{{ $overallStatusLabel }}</div>
                            <div class="mt-0.5 text-xs opacity-90">{{ $overallStatusMessage }}</div>
                        </div>
                    </div>
                </div>

                <div class="lg:w-[460px]">
                    <div class="mb-4 flex items-center justify-between gap-4">
                        <div class="rounded-xl bg-white/5 px-3 py-2 text-xs text-slate-300 ring-1 ring-white/10">
                            Terakhir refresh:
                            <span class="font-semibold text-white">{{ $lastRefreshedAt->format('H:i:s') }}</span>
                        </div>

                        <a href="{{ route('dashboard') }}"
                        class="inline-flex shrink-0 items-center gap-2 rounded-xl bg-white/10 px-3.5 py-2 text-xs font-semibold text-white ring-1 ring-white/10 hover:bg-white/15">
                            <i data-lucide="refresh-cw" class="h-4 w-4"></i>
                            Refresh
                        </a>
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <div class="rounded-2xl bg-white/10 p-4 ring-1 ring-white/10">
                            <div class="text-xs text-slate-300">Success</div>
                            <div class="mt-1 text-2xl font-bold text-emerald-300">{{ $stats['success_today'] }}</div>
                        </div>

                        <div class="rounded-2xl bg-white/10 p-4 ring-1 ring-white/10">
                            <div class="text-xs text-slate-300">Failed</div>
                            <div class="mt-1 text-2xl font-bold text-red-300">{{ $stats['failed_today'] }}</div>
                        </div>

                        <div class="rounded-2xl bg-white/10 p-4 ring-1 ring-white/10">
                            <div class="text-xs text-slate-300">Pending</div>
                            <div class="mt-1 text-2xl font-bold text-amber-300">{{ $stats['pending_today'] }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-3 flex flex-wrap items-center gap-2 text-xs text-slate-400">
                <span class="inline-flex items-center gap-1.5">
                    <i data-lucide="database" class="h-3.5 w-3.5"></i>
                    Source: backup_logs
                </span>

                <span class="text-slate-600">•</span>

                <span class="inline-flex items-center gap-1.5">
                    <i data-lucide="ban" class="h-3.5 w-3.5"></i>
                    Tidak scan folder storage
                </span>

                <span class="text-slate-600">•</span>

                <span class="inline-flex items-center gap-1.5">
                    <i data-lucide="shield-check" class="h-3.5 w-3.5"></i>
                    Monitoring only
                </span>
            </div>
        </section>

        {{-- Stat Cards --}}
        <section class="grid grid-cols-2 gap-3 lg:grid-cols-6">
            @php
                $statCards = [
                    ['label' => 'Job Aktif', 'value' => $stats['active_jobs'], 'icon' => 'workflow', 'class' => 'bg-slate-100 text-slate-700', 'valueClass' => 'text-slate-900'],
                    ['label' => 'Sistem Aktif', 'value' => $stats['active_systems'], 'icon' => 'server', 'class' => 'bg-slate-100 text-slate-700', 'valueClass' => 'text-slate-900'],
                    ['label' => 'Success', 'value' => $stats['success_today'], 'icon' => 'check-circle-2', 'class' => 'bg-emerald-50 text-emerald-700', 'valueClass' => 'text-emerald-600'],
                    ['label' => 'Failed', 'value' => $stats['failed_today'], 'icon' => 'x-circle', 'class' => 'bg-red-50 text-red-700', 'valueClass' => 'text-red-600'],
                    ['label' => 'Warning', 'value' => $stats['warning_today'], 'icon' => 'alert-triangle', 'class' => 'bg-amber-50 text-amber-700', 'valueClass' => 'text-amber-600'],
                    ['label' => 'Pending', 'value' => $stats['pending_today'], 'icon' => 'clock-3', 'class' => 'bg-slate-100 text-slate-700', 'valueClass' => 'text-slate-700'],
                ];
            @endphp

            @foreach ($statCards as $card)
                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $card['label'] }}</div>
                            <div class="mt-1 text-2xl font-bold {{ $card['valueClass'] }}">
                                {{ $card['value'] }}
                            </div>
                        </div>

                        <div class="flex h-9 w-9 items-center justify-center rounded-xl {{ $card['class'] }}">
                            <i data-lucide="{{ $card['icon'] }}" class="h-4 w-4"></i>
                        </div>
                    </div>
                </div>
            @endforeach
        </section>

        {{-- Quick Actions --}}
        <section class="rounded-2xl border border-slate-200 bg-white px-5 py-4 shadow-sm">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <div class="text-sm font-bold text-slate-900">Aksi Cepat</div>
                    <div class="mt-1 text-xs text-slate-500">
                        Navigasi cepat ke modul backup.
                    </div>
                </div>

                <div class="flex flex-wrap gap-2">

                    <a href="{{ route('storage-usage.index') }}"
                    class="inline-flex items-center gap-2 rounded-xl border border-cyan-200 bg-cyan-50 px-3 py-2 text-xs font-semibold text-cyan-700 hover:bg-cyan-100">
                        <i data-lucide="hard-drive" class="h-4 w-4"></i>
                        Storage Usage
                    </a>

                    <a href="{{ route('backup-logs.index') }}"
                    class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                        <i data-lucide="list-checks" class="h-4 w-4"></i>
                        Lihat Log
                    </a>

                    <a href="{{ route('backup-jobs.index') }}"
                    class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                        <i data-lucide="workflow" class="h-4 w-4"></i>
                        Kelola Job
                    </a>

                    <a href="{{ route('backup-logs.create') }}"
                    class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-3 py-2 text-xs font-semibold text-white hover:bg-slate-800">
                        <i data-lucide="plus-circle" class="h-4 w-4"></i>
                        Tambah Log Manual
                    </a>
                </div>
            </div>
        </section>

        {{-- Storage Monitoring Summary --}}
        <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-col gap-3 border-b border-slate-100 px-5 py-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h2 class="font-bold text-slate-900">Storage Monitoring</h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Ringkasan kapasitas storage aktif berdasarkan data terakhir di database.
                    </p>
                </div>

                <a href="{{ route('storage-usage.index') }}"
                class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                    <i data-lucide="hard-drive" class="h-4 w-4"></i>
                    Detail Storage
                </a>
            </div>

            <div class="grid gap-4 p-5 lg:grid-cols-4">
                <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Storage Aktif</p>
                            <p class="mt-1 text-2xl font-bold text-slate-900">{{ $storageSummary['active'] }}</p>
                        </div>
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white text-slate-700 ring-1 ring-slate-200">
                            <i data-lucide="hard-drive" class="h-5 w-5"></i>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl bg-emerald-50 p-4 ring-1 ring-emerald-100">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">Online</p>
                            <p class="mt-1 text-2xl font-bold text-emerald-700">{{ $storageSummary['online'] }}</p>
                        </div>
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white text-emerald-700 ring-1 ring-emerald-100">
                            <i data-lucide="wifi" class="h-5 w-5"></i>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl bg-rose-50 p-4 ring-1 ring-rose-100">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-rose-700">Perlu Dicek</p>
                            <p class="mt-1 text-2xl font-bold text-rose-700">{{ $storageSummary['need_attention'] }}</p>
                        </div>
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white text-rose-700 ring-1 ring-rose-100">
                            <i data-lucide="siren" class="h-5 w-5"></i>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl bg-cyan-50 p-4 ring-1 ring-cyan-100">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-cyan-700">Overall Usage</p>
                            <p class="mt-1 text-2xl font-bold text-cyan-700">
                                {{ $storageSummary['overall_usage_percent'] !== null ? number_format($storageSummary['overall_usage_percent'], 1) . '%' : '-' }}
                            </p>
                        </div>
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white text-cyan-700 ring-1 ring-cyan-100">
                            <i data-lucide="gauge" class="h-5 w-5"></i>
                        </div>
                    </div>

                    @php
                        $dashboardStorageProgressWidth = $storageSummary['overall_usage_percent'] !== null
                            ? min(max((float) $storageSummary['overall_usage_percent'], 0), 100)
                            : 0;
                    @endphp

                    <div class="mt-3 h-3 overflow-hidden rounded-full bg-white/70 ring-1 ring-cyan-100">
                        <div
                            class="h-full rounded-full bg-cyan-500 transition-all duration-500"
                            style="width: {{ $dashboardStorageProgressWidth }}%;"
                        ></div>
                    </div>
                </div>
            </div>

            <div class="grid gap-4 border-t border-slate-100 p-5 lg:grid-cols-2">
                <div class="rounded-2xl border border-slate-200 bg-white p-4">
                    <div class="grid grid-cols-3 gap-3 text-sm">
                        <div>
                            <p class="text-xs font-medium text-slate-500">Total</p>
                            <p class="mt-1 font-bold text-slate-900">{{ $storageSummary['total_label'] }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-slate-500">Used</p>
                            <p class="mt-1 font-bold text-slate-900">{{ $storageSummary['used_label'] }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-slate-500">Free</p>
                            <p class="mt-1 font-bold text-slate-900">{{ $storageSummary['free_label'] }}</p>
                        </div>
                    </div>

                    <div class="mt-4 flex flex-wrap items-center gap-2 text-xs text-slate-500">
                        <span class="inline-flex items-center gap-1.5">
                            <i data-lucide="database" class="h-3.5 w-3.5"></i>
                            Source: backup_storages
                        </span>
                        <span class="text-slate-300">•</span>
                        <span class="inline-flex items-center gap-1.5">
                            <i data-lucide="ban" class="h-3.5 w-3.5"></i>
                            Tidak scan folder storage
                        </span>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-4">
                    <div class="mb-3 flex items-center justify-between gap-3">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900">Storage Perlu Perhatian</h3>
                            <p class="mt-1 text-xs text-slate-500">Offline, kritis, atau mendekati penuh.</p>
                        </div>
                        <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">
                            {{ $attentionStorages->count() }} item
                        </span>
                    </div>

                    <div class="space-y-2">
                        @forelse ($attentionStorages as $storage)
                            <div class="flex items-center justify-between gap-3 rounded-xl bg-slate-50 px-3 py-2">
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-semibold text-slate-900">{{ $storage->name }}</p>
                                    <p class="mt-0.5 text-xs text-slate-500">
                                        {{ $storage->checkStatusLabel() }} · {{ $storage->lastCheckedLabel() }}
                                    </p>
                                </div>

                                <span class="shrink-0 rounded-full px-2.5 py-1 text-xs font-semibold ring-1 {{ $storage->healthBadgeClass() }}">
                                    {{ $storage->healthLabel() }}
                                </span>
                            </div>
                        @empty
                            <div class="{{ $emptyBoxClass }}">
                                <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-full bg-white text-emerald-600 ring-1 ring-slate-200">
                                    <i data-lucide="shield-check" class="h-5 w-5"></i>
                                </div>
                                <div class="mt-3 text-sm font-semibold text-slate-700">
                                    Storage aman
                                </div>
                                <div class="mt-1 text-xs text-slate-500">
                                    Tidak ada storage offline, kritis, atau warning.
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </section>

        {{-- Attention Center --}}
        <section class="grid grid-cols-1 gap-5 xl:grid-cols-2">
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                    <div>
                        <h2 class="font-bold text-slate-900">Perlu Dicek</h2>
                        <p class="mt-1 text-sm text-slate-500">Failed / warning terbaru.</p>
                    </div>

                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-red-50 text-red-700">
                        <i data-lucide="siren" class="h-5 w-5"></i>
                    </div>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse ($attentionProblemLogs as $log)
                        <div class="flex items-start justify-between gap-4 px-5 py-4">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold ring-1 {{ $statusBadgeClass($log->status) }}">
                                        <i data-lucide="{{ $statusIcon($log->status) }}" class="h-3.5 w-3.5"></i>
                                        {{ $log->statusLabel() }}
                                    </span>

                                    <span class="text-xs text-slate-400">
                                        {{ $log->finished_at?->format('d M H:i') ?? '-' }}
                                    </span>
                                </div>

                                <div class="mt-2 truncate font-semibold text-slate-900">
                                    {{ $log->job?->name ?? '-' }}
                                </div>

                                <div class="mt-1 truncate text-sm text-slate-500">
                                    {{ $log->message ?: ($log->error_message ?: 'Tidak ada pesan.') }}
                                </div>
                            </div>

                            <a href="{{ route('backup-logs.show', $log) }}"
                               class="shrink-0 rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                Detail
                            </a>
                        </div>
                    @empty
                        <div class="p-5">
                            <div class="{{ $emptyBoxClass }}">
                                <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-full bg-white text-emerald-600 ring-1 ring-slate-200">
                                    <i data-lucide="shield-check" class="h-5 w-5"></i>
                                </div>
                                <div class="mt-3 text-sm font-semibold text-slate-700">
                                    Tidak ada masalah terbaru
                                </div>
                                <div class="mt-1 text-xs text-slate-500">
                                    Belum ada failed atau warning yang perlu dicek.
                                </div>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                    <div>
                        <h2 class="font-bold text-slate-900">Belum Berjalan Hari Ini</h2>
                        <p class="mt-1 text-sm text-slate-500">Prioritas job yang belum kirim log.</p>
                    </div>

                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-amber-700">
                        <i data-lucide="clock-3" class="h-5 w-5"></i>
                    </div>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse ($attentionPendingRows as $row)
                        @php $job = $row['job']; @endphp

                        <div class="px-5 py-4">
                            <div class="flex items-start justify-between gap-4">
                                <div class="min-w-0">
                                    <div class="truncate font-semibold text-slate-900">
                                        {{ $job->name }}
                                    </div>

                                    <div class="mt-1 truncate text-sm text-slate-500">
                                        {{ $job->system?->name ?? '-' }} · {{ $job->frequencyLabel() }}
                                    </div>

                                    <div class="mt-1 truncate text-xs text-slate-400">
                                        {{ $job->code }} · {{ $job->schedule_text ?? '-' }}
                                    </div>
                                </div>

                                <span class="shrink-0 rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">
                                    Pending
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="p-5">
                            <div class="{{ $emptyBoxClass }}">
                                <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-full bg-white text-emerald-600 ring-1 ring-slate-200">
                                    <i data-lucide="check-circle-2" class="h-5 w-5"></i>
                                </div>
                                <div class="mt-3 text-sm font-semibold text-slate-700">
                                    Semua job sudah berjalan
                                </div>
                                <div class="mt-1 text-xs text-slate-500">
                                    Semua job aktif sudah memiliki log backup hari ini.
                                </div>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

        {{-- Status Board --}}
        <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-col gap-3 border-b border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="font-bold text-slate-900">Status Job Board</h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Ringkasan job aktif berdasarkan status log terbaru hari ini.
                    </p>
                </div>

                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('backup-jobs.index') }}"
                       class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                        <i data-lucide="workflow" class="h-4 w-4"></i>
                        Semua Job
                    </a>

                    <a href="{{ route('backup-logs.index') }}"
                       class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                        <i data-lucide="list-checks" class="h-4 w-4"></i>
                        Semua Log
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 divide-y divide-slate-100 md:grid-cols-2 md:divide-x md:divide-y-0 xl:grid-cols-4">
                @foreach (['failed', 'warning', 'pending', 'success'] as $statusKey)
                    @php
                        $rows = $groupedJobStatusRows[$statusKey] ?? collect();
                        $meta = $statusMeta[$statusKey];
                    @endphp

                    <div class="p-5">
                        <div class="mb-4 flex items-center justify-between gap-3">
                            <div class="flex items-center gap-2">
                                <span class="flex h-9 w-9 items-center justify-center rounded-xl {{ $meta['soft'] }}">
                                    <i data-lucide="{{ $meta['icon'] }}" class="h-4 w-4"></i>
                                </span>
                                <div>
                                    <div class="font-bold text-slate-900">{{ $meta['label'] }}</div>
                                    <div class="text-xs text-slate-500">{{ $rows->count() }} job</div>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-2">
                            @forelse ($rows->take(5) as $row)
                                @php
                                    $job = $row['job'];
                                    $latestLog = $row['latestLog'];
                                @endphp

                                <div class="rounded-xl border {{ $meta['border'] }} bg-white px-3 py-2">
                                    <div class="truncate text-sm font-semibold text-slate-900">
                                        {{ $job->name }}
                                    </div>
                                    <div class="mt-1 flex items-center justify-between gap-3 text-xs text-slate-500">
                                        <span class="truncate">{{ $job->system?->name ?? '-' }}</span>
                                        <span class="shrink-0">{{ $latestLog?->finished_at?->format('H:i') ?? '-' }}</span>
                                    </div>
                                </div>
                            @empty
                                <div class="{{ $emptyBoxClass }}">
                                    <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-full bg-white text-slate-400 ring-1 ring-slate-200">
                                        <i data-lucide="inbox" class="h-5 w-5"></i>
                                    </div>
                                    <div class="mt-3 text-sm font-semibold text-slate-700">Tidak ada job</div>
                                    <div class="mt-1 text-xs text-slate-500">
                                        Belum ada job dengan status {{ strtolower($meta['label']) }}.
                                    </div>
                                </div>
                            @endforelse

                            @if ($rows->count() > 5)
                                <div class="text-xs font-semibold text-slate-400">
                                    +{{ $rows->count() - 5 }} job lainnya
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Monitoring Detail Tabs --}}
        <section
            x-data="{ tab: 'pending' }"
            class="rounded-2xl border border-slate-200 bg-white shadow-sm"
        >
            <div class="flex flex-col gap-4 border-b border-slate-100 px-5 py-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h2 class="font-bold text-slate-900">Monitoring Detail</h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Detail ringkas tanpa membuat dashboard terlalu panjang.
                    </p>
                </div>

                <div class="flex flex-wrap gap-2">
                    <button
                        type="button"
                        x-on:click="tab = 'pending'"
                        class="inline-flex items-center rounded-xl px-3 py-2 text-xs font-semibold ring-1 transition"
                        x-bind:class="tab === 'pending' ? 'bg-slate-900 text-white ring-slate-900' : 'bg-white text-slate-700 ring-slate-200 hover:bg-slate-50'"
                    >
                        Belum Berjalan
                        <span class="ml-1 rounded-full bg-white/20 px-1.5 py-0.5 text-[10px]">
                            {{ $pendingRows->count() }}
                        </span>
                    </button>

                    <button
                        type="button"
                        x-on:click="tab = 'problem'"
                        class="inline-flex items-center rounded-xl px-3 py-2 text-xs font-semibold ring-1 transition"
                        x-bind:class="tab === 'problem' ? 'bg-slate-900 text-white ring-slate-900' : 'bg-white text-slate-700 ring-slate-200 hover:bg-slate-50'"
                    >
                        Masalah
                        <span class="ml-1 rounded-full bg-white/20 px-1.5 py-0.5 text-[10px]">
                            {{ $problemLogs->count() }}
                        </span>
                    </button>

                    <button
                        type="button"
                        x-on:click="tab = 'activity'"
                        class="inline-flex items-center rounded-xl px-3 py-2 text-xs font-semibold ring-1 transition"
                        x-bind:class="tab === 'activity' ? 'bg-slate-900 text-white ring-slate-900' : 'bg-white text-slate-700 ring-slate-200 hover:bg-slate-50'"
                    >
                        Aktivitas
                        <span class="ml-1 rounded-full bg-white/20 px-1.5 py-0.5 text-[10px]">
                            {{ $latestActivities->count() }}
                        </span>
                    </button>
                </div>
            </div>

            <div class="p-5">
                {{-- Pending Tab --}}
                <div x-show="tab === 'pending'" x-cloak>
                    <div class="overflow-hidden rounded-xl border border-slate-200">
                        <table class="min-w-full divide-y divide-slate-100">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Job</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Sistem</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Jadwal</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Storage</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse ($pendingRows as $row)
                                    @php $job = $row['job']; @endphp

                                    <tr>
                                        <td class="px-4 py-3">
                                            <div class="font-semibold text-slate-900">{{ $job->name }}</div>
                                            <div class="text-xs text-slate-500">{{ $job->code }}</div>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-slate-700">{{ $job->system?->name ?? '-' }}</td>
                                        <td class="px-4 py-3 text-sm text-slate-700">
                                            {{ $job->frequencyLabel() }}
                                            <div class="text-xs text-slate-400">{{ $job->schedule_text ?? '-' }}</div>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-slate-700">{{ $job->system?->storage?->name ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-8">
                                            <div class="{{ $emptyBoxClass }}">
                                                <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-full bg-white text-emerald-600 ring-1 ring-slate-200">
                                                    <i data-lucide="check-circle-2" class="h-5 w-5"></i>
                                                </div>
                                                <div class="mt-3 text-sm font-semibold text-slate-700">
                                                    Semua job sudah berjalan
                                                </div>
                                                <div class="mt-1 text-xs text-slate-500">
                                                    Tidak ada job aktif yang pending hari ini.
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Problem Tab --}}
                <div x-show="tab === 'problem'" x-cloak>
                    <div class="overflow-hidden rounded-xl border border-slate-200">
                        <table class="min-w-full divide-y divide-slate-100">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Job</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Pesan</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Selesai</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse ($problemLogs as $log)
                                    <tr>
                                        <td class="px-4 py-3">
                                            <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold ring-1 {{ $statusBadgeClass($log->status) }}">
                                                <i data-lucide="{{ $statusIcon($log->status) }}" class="h-3.5 w-3.5"></i>
                                                {{ $log->statusLabel() }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="font-semibold text-slate-900">{{ $log->job?->name ?? '-' }}</div>
                                            <div class="text-xs text-slate-500">{{ $log->system?->name ?? '-' }}</div>
                                        </td>
                                        <td class="max-w-md px-4 py-3 text-sm text-slate-700">
                                            <div class="truncate">
                                                {{ $log->message ?: ($log->error_message ?: '-') }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-slate-700">
                                            {{ $log->finished_at?->format('d M Y H:i') ?? '-' }}
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <a href="{{ route('backup-logs.show', $log) }}"
                                               class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                                Detail
                                                <i data-lucide="arrow-right" class="h-3.5 w-3.5"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-8">
                                            <div class="{{ $emptyBoxClass }}">
                                                <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-full bg-white text-emerald-600 ring-1 ring-slate-200">
                                                    <i data-lucide="shield-check" class="h-5 w-5"></i>
                                                </div>
                                                <div class="mt-3 text-sm font-semibold text-slate-700">
                                                    Tidak ada masalah terbaru
                                                </div>
                                                <div class="mt-1 text-xs text-slate-500">
                                                    Failed dan warning terbaru belum ditemukan.
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Activity Tab --}}
                <div x-show="tab === 'activity'" x-cloak>
                    <div class="overflow-hidden rounded-xl border border-slate-200">
                        <table class="min-w-full divide-y divide-slate-100">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Job</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">File</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Durasi</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Selesai</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse ($latestActivities as $log)
                                    <tr>
                                        <td class="px-4 py-3">
                                            <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold ring-1 {{ $statusBadgeClass($log->status) }}">
                                                <i data-lucide="{{ $statusIcon($log->status) }}" class="h-3.5 w-3.5"></i>
                                                {{ $log->statusLabel() }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="font-semibold text-slate-900">{{ $log->job?->name ?? '-' }}</div>
                                            <div class="text-xs text-slate-500">{{ $log->system?->name ?? '-' }}</div>
                                        </td>
                                        <td class="max-w-xs px-4 py-3 text-sm text-slate-700">
                                            <div class="truncate">{{ $log->file_name ?? '-' }}</div>
                                            <div class="text-xs text-slate-400">{{ $log->fileSizeLabel() }}</div>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-slate-700">{{ $log->durationLabel() }}</td>
                                        <td class="px-4 py-3 text-sm text-slate-700">
                                            {{ $log->finished_at?->format('d M Y H:i') ?? '-' }}
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <a href="{{ route('backup-logs.show', $log) }}"
                                               class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                                Detail
                                                <i data-lucide="arrow-right" class="h-3.5 w-3.5"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-8">
                                            <div class="{{ $emptyBoxClass }}">
                                                <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-full bg-white text-slate-400 ring-1 ring-slate-200">
                                                    <i data-lucide="history" class="h-5 w-5"></i>
                                                </div>
                                                <div class="mt-3 text-sm font-semibold text-slate-700">
                                                    Belum ada aktivitas backup
                                                </div>
                                                <div class="mt-1 text-xs text-slate-500">
                                                    Log backup akan muncul setelah script mengirim data ke BMS.
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection