@extends('layouts.admin')

@section('title', 'Detail Backup Log')
@section('pageTitle', 'Detail Backup Log')
@section('pageSubtitle', 'Detail hasil backup yang tercatat di BMS.')

@section('content')
    @php
        $statusStyles = [
            'success' => [
                'badge' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
                'card' => 'border-emerald-200 bg-emerald-50',
                'icon' => 'check-circle-2',
                'text' => 'text-emerald-700',
                'label' => 'Backup berhasil',
            ],
            'warning' => [
                'badge' => 'bg-amber-50 text-amber-700 ring-amber-200',
                'card' => 'border-amber-200 bg-amber-50',
                'icon' => 'triangle-alert',
                'text' => 'text-amber-700',
                'label' => 'Backup warning',
            ],
            'failed' => [
                'badge' => 'bg-red-50 text-red-700 ring-red-200',
                'card' => 'border-red-200 bg-red-50',
                'icon' => 'x-circle',
                'text' => 'text-red-700',
                'label' => 'Backup gagal',
            ],
        ];
        $style = $statusStyles[$log->status] ?? $statusStyles['warning'];
    @endphp

    <div class="mx-auto w-full max-w-7xl space-y-6 px-2">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                <div>
                    <div class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold ring-1 {{ $style['badge'] }}">
                        <i data-lucide="{{ $style['icon'] }}" class="h-3.5 w-3.5"></i>
                        {{ $log->statusLabel() }}
                    </div>
                    <h1 class="mt-4 text-2xl font-bold text-slate-900">{{ $log->job?->name ?? 'Backup Log' }}</h1>
                    <p class="mt-2 text-sm leading-6 text-slate-500">
                        Bukti riwayat backup tanggal
                        <span class="font-semibold text-slate-700">{{ $log->backup_date?->format('d M Y') }}</span>.
                        Data ini adalah catatan monitoring, bukan proses eksekusi backup.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    @if($log->job)
                        <a href="{{ route('backup-jobs.show', $log->job) }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                            <i data-lucide="briefcase-business" class="h-4 w-4"></i>
                            Detail Job
                        </a>
                    @endif
                    <a href="{{ route('backup-logs.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                        <i data-lucide="arrow-left" class="h-4 w-4"></i>
                        Kembali
                    </a>
                </div>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-2xl border p-5 shadow-sm {{ $style['card'] }}">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Status</p>
                        <p class="mt-2 text-lg font-bold {{ $style['text'] }}">{{ $style['label'] }}</p>
                    </div>
                    <i data-lucide="{{ $style['icon'] }}" class="h-8 w-8 {{ $style['text'] }}"></i>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Durasi</p>
                <p class="mt-2 text-lg font-bold text-slate-900">{{ $log->durationLabel() }}</p>
                <p class="mt-1 text-xs text-slate-500">{{ $log->duration_seconds !== null ? number_format($log->duration_seconds) . ' detik' : 'Belum ada durasi' }}</p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Ukuran File</p>
                <p class="mt-2 text-lg font-bold text-slate-900">{{ $log->fileSizeLabel() }}</p>
                <p class="mt-1 text-xs text-slate-500">{{ $log->file_size_bytes !== null ? number_format($log->file_size_bytes) . ' bytes' : 'Belum ada ukuran file' }}</p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Dicatat</p>
                <p class="mt-2 text-lg font-bold text-slate-900">{{ $log->created_at?->format('d M Y') }}</p>
                <p class="mt-1 text-xs text-slate-500">{{ $log->created_at?->format('H:i') }} oleh {{ $log->creator?->name ?? 'API / System' }}</p>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-3">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm xl:col-span-2">
                <div class="flex items-center gap-2">
                    <i data-lucide="clipboard-list" class="h-5 w-5 text-blue-600"></i>
                    <h2 class="text-base font-semibold text-slate-900">Informasi Log</h2>
                </div>

                <dl class="mt-5 grid gap-4 sm:grid-cols-2">
                    <div class="rounded-xl bg-slate-50 p-4">
                        <dt class="text-xs font-bold uppercase tracking-wider text-slate-400">Job</dt>
                        <dd class="mt-1 text-sm font-semibold text-slate-900">{{ $log->job?->name ?? '-' }}</dd>
                        <dd class="mt-1 font-mono text-xs text-slate-500">{{ $log->job?->code ?? '-' }}</dd>
                    </div>
                    <div class="rounded-xl bg-slate-50 p-4">
                        <dt class="text-xs font-bold uppercase tracking-wider text-slate-400">Sistem Snapshot</dt>
                        <dd class="mt-1 text-sm font-semibold text-slate-900">{{ $log->system?->name ?? '-' }}</dd>
                        <dd class="mt-1 font-mono text-xs text-slate-500">{{ $log->system?->code ?? '-' }}</dd>
                    </div>
                    <div class="rounded-xl bg-slate-50 p-4">
                        <dt class="text-xs font-bold uppercase tracking-wider text-slate-400">Storage Snapshot</dt>
                        <dd class="mt-1 text-sm font-semibold text-slate-900">{{ $log->storage?->name ?? '-' }}</dd>
                        <dd class="mt-1 text-xs text-slate-500">{{ $log->storage?->storage_type ? strtoupper($log->storage->storage_type) : '-' }}</dd>
                    </div>
                    <div class="rounded-xl bg-slate-50 p-4">
                        <dt class="text-xs font-bold uppercase tracking-wider text-slate-400">Tanggal Backup</dt>
                        <dd class="mt-1 text-sm font-semibold text-slate-900">{{ $log->backup_date?->format('d M Y') }}</dd>
                        <dd class="mt-1 text-xs text-slate-500">{{ $log->backup_date?->diffForHumans() }}</dd>
                    </div>
                </dl>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-center gap-2">
                    <i data-lucide="clock-3" class="h-5 w-5 text-blue-600"></i>
                    <h2 class="text-base font-semibold text-slate-900">Timeline Backup</h2>
                </div>

                <div class="mt-5 space-y-4">
                    <div class="flex gap-3">
                        <div class="mt-1 h-3 w-3 rounded-full bg-blue-500"></div>
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Mulai</p>
                            <p class="mt-1 text-sm font-semibold text-slate-900">{{ $log->started_at?->format('d M Y H:i:s') ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <div class="mt-1 h-3 w-3 rounded-full bg-slate-400"></div>
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Selesai</p>
                            <p class="mt-1 text-sm font-semibold text-slate-900">{{ $log->finished_at?->format('d M Y H:i:s') ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Durasi Tercatat</p>
                        <p class="mt-1 text-sm font-semibold text-slate-900">{{ $log->durationLabel() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-3">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm xl:col-span-2">
                <div class="flex items-center gap-2">
                    <i data-lucide="file-archive" class="h-5 w-5 text-blue-600"></i>
                    <h2 class="text-base font-semibold text-slate-900">Informasi File Backup</h2>
                </div>

                <dl class="mt-5 space-y-4">
                    <div>
                        <dt class="text-xs font-bold uppercase tracking-wider text-slate-400">Nama File</dt>
                        <dd class="mt-1 break-all text-sm font-semibold text-slate-900">{{ $log->file_name ?: '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold uppercase tracking-wider text-slate-400">File Path</dt>
                        <dd class="mt-2 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 break-all">{{ $log->file_path ?: '-' }}</dd>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <dt class="text-xs font-bold uppercase tracking-wider text-slate-400">Ukuran File</dt>
                            <dd class="mt-1 text-sm text-slate-700">{{ $log->fileSizeLabel() }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-bold uppercase tracking-wider text-slate-400">Checksum</dt>
                            <dd class="mt-1 break-all font-mono text-xs text-slate-700">{{ $log->checksum ?: '-' }}</dd>
                        </div>
                    </div>
                </dl>
            </div>

            <div class="rounded-2xl border border-blue-100 bg-blue-50 p-6 shadow-sm">
                <div class="flex items-center gap-2">
                    <i data-lucide="shield-check" class="h-5 w-5 text-blue-700"></i>
                    <h2 class="text-base font-semibold text-blue-950">Catatan Audit</h2>
                </div>
                <p class="mt-4 text-sm leading-6 text-blue-900">
                    Log menyimpan snapshot job, sistem, dan storage tujuan saat backup dilaporkan. Ini menjaga riwayat tetap terbaca walaupun konfigurasi job berubah di masa depan.
                </p>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-center gap-2">
                    <i data-lucide="message-square-text" class="h-5 w-5 text-blue-600"></i>
                    <h2 class="text-base font-semibold text-slate-900">Pesan</h2>
                </div>
                <p class="mt-4 whitespace-pre-line rounded-xl bg-slate-50 p-4 text-sm leading-6 text-slate-700">{{ $log->message ?: '-' }}</p>
            </div>
            <div class="rounded-2xl border {{ $log->error_message ? 'border-red-200 bg-red-50' : 'border-slate-200 bg-white' }} p-6 shadow-sm">
                <div class="flex items-center gap-2">
                    <i data-lucide="bug" class="h-5 w-5 {{ $log->error_message ? 'text-red-700' : 'text-slate-500' }}"></i>
                    <h2 class="text-base font-semibold {{ $log->error_message ? 'text-red-950' : 'text-slate-900' }}">Error Message</h2>
                </div>
                <p class="mt-4 whitespace-pre-line rounded-xl p-4 text-sm leading-6 {{ $log->error_message ? 'bg-white text-red-800' : 'bg-slate-50 text-slate-700' }}">{{ $log->error_message ?: '-' }}</p>
            </div>
        </div>
    </div>
@endsection
