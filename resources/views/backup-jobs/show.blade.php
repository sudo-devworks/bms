@extends('layouts.admin')

@section('title', 'Detail Backup Job')
@section('pageTitle', 'Detail Backup Job')
@section('pageSubtitle', 'Informasi ringkas job dan riwayat log terakhir.')

@section('content')
    <div class="mx-auto w-full max-w-6xl space-y-6 px-2">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                <div>
                    <div class="inline-flex items-center gap-2 rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700"><i data-lucide="workflow" class="h-3.5 w-3.5"></i>Backup Job</div>
                    <h1 class="mt-4 text-2xl font-bold text-slate-900">{{ $job->name }}</h1>
                    <p class="mt-2 font-mono text-sm text-slate-500">{{ $job->code }}</p>
                </div>
                <div class="flex gap-2"><a href="{{ route('backup-jobs.edit', $job) }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50"><i data-lucide="pencil" class="h-4 w-4"></i>Edit</a><a href="{{ route('backup-logs.create', ['backup_job_id' => $job->id]) }}" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"><i data-lucide="plus" class="h-4 w-4"></i>Tambah Log</a></div>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm lg:col-span-2">
                <h2 class="text-base font-semibold text-slate-900">Informasi Job</h2>
                <dl class="mt-5 grid gap-4 sm:grid-cols-2">
                    <div><dt class="text-xs font-bold uppercase tracking-wider text-slate-400">Sistem</dt><dd class="mt-1 text-sm font-semibold text-slate-900">{{ $job->system?->name ?? '-' }}</dd></div>
                    <div><dt class="text-xs font-bold uppercase tracking-wider text-slate-400">Storage</dt><dd class="mt-1 text-sm font-semibold text-slate-900">{{ $job->system?->storage?->name ?? '-' }}</dd></div>
                    <div><dt class="text-xs font-bold uppercase tracking-wider text-slate-400">Backup Type</dt><dd class="mt-1 text-sm text-slate-700">{{ $job->backupTypeLabel() }}</dd></div>
                    <div><dt class="text-xs font-bold uppercase tracking-wider text-slate-400">Expected Frequency</dt><dd class="mt-1 text-sm text-slate-700">{{ $job->frequencyLabel() }}</dd></div>
                    <div><dt class="text-xs font-bold uppercase tracking-wider text-slate-400">Expected Time</dt><dd class="mt-1 text-sm text-slate-700">{{ optional($job->expected_time)->format('H:i') ?? '-' }}</dd></div>
                    <div>
                        <dt class="text-xs font-bold uppercase tracking-wider text-slate-400">Jam Backup Normal</dt>
                        <dd class="mt-1 text-sm text-slate-700">{{ $job->expectedRunTimeLabel() }}</dd>
                    </div>

                    <div>
                        <dt class="text-xs font-bold uppercase tracking-wider text-slate-400">Alert Pending Setelah</dt>
                        <dd class="mt-1 text-sm text-slate-700">
                            {{ $job->alertAfterMinutesLabel() }}
                            <span class="text-slate-400">/ mulai {{ $job->pendingAlertTimeLabel() }}</span>
                        </dd>
                    </div>
                    <div><dt class="text-xs font-bold uppercase tracking-wider text-slate-400">Status</dt><dd class="mt-1 text-sm text-slate-700">{{ $job->is_active ? 'Aktif' : 'Nonaktif' }}</dd></div>
                    <div class="sm:col-span-2"><dt class="text-xs font-bold uppercase tracking-wider text-slate-400">Jadwal</dt><dd class="mt-1 text-sm text-slate-700">{{ $job->schedule_text ?: '-' }}</dd></div>
                    <div class="sm:col-span-2"><dt class="text-xs font-bold uppercase tracking-wider text-slate-400">Catatan</dt><dd class="mt-1 whitespace-pre-line text-sm text-slate-700">{{ $job->notes ?: '-' }}</dd></div>
                </dl>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-base font-semibold text-slate-900">Catatan Scope</h2>
                <div class="mt-4 space-y-3 text-sm leading-6 text-slate-600">
                    <p>BMS hanya memonitor hasil backup.</p>
                    <p>Job ini tidak menjalankan backup dan tidak membuat scheduler.</p>
                    <p>Log otomatis dari script baru akan masuk pada BMS-05.</p>
                </div>
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-6 py-5"><h2 class="text-base font-semibold text-slate-900">10 Log Terakhir</h2></div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500"><tr><th class="px-4 py-3">Tanggal</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">File</th><th class="px-4 py-3">Durasi</th><th class="px-4 py-3 text-right">Aksi</th></tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($job->logs as $log)
                            <tr><td class="px-4 py-3">{{ $log->backup_date?->format('d M Y') }}</td><td class="px-4 py-3">{{ $log->statusLabel() }}</td><td class="px-4 py-3">{{ $log->file_name ?: '-' }}</td><td class="px-4 py-3">{{ $log->durationLabel() }}</td><td class="px-4 py-3 text-right"><a href="{{ route('backup-logs.show', $log) }}" class="text-sm font-semibold text-blue-700 hover:text-blue-800">Detail</a></td></tr>
                        @empty
                            <tr><td colspan="5" class="px-4 py-10 text-center text-sm text-slate-500">Belum ada log untuk job ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
