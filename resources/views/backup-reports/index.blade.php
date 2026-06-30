@extends('layouts.admin')

@section('title', 'Backup Reports')
@section('pageTitle', 'Backup Reports')
@section('pageSubtitle', 'Laporan backup untuk monitoring, audit, dan pelaporan internal.')

@section('content')
    @php
        $hasAdvancedFilter = request()->filled('date_from')
            || request()->filled('date_to')
            || request()->filled('backup_system_id')
            || request()->filled('backup_job_id')
            || request()->filled('backup_storage_id')
            || request()->filled('status');

        $activeFilterCount = collect([
            request()->filled('search'),
            request()->filled('date_from'),
            request()->filled('date_to'),
            request()->filled('backup_system_id'),
            request()->filled('backup_job_id'),
            request()->filled('backup_storage_id'),
            request()->filled('status'),
        ])->filter()->count();

        $selectedSystem = $systems->firstWhere('id', (int) request('backup_system_id'));
        $selectedJob = $jobs->firstWhere('id', (int) request('backup_job_id'));
        $selectedStorage = $storages->firstWhere('id', (int) request('backup_storage_id'));

        $statusStyles = [
            'success' => [
                'badge' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
                'icon' => 'check-circle-2',
            ],
            'warning' => [
                'badge' => 'bg-amber-50 text-amber-700 ring-amber-200',
                'icon' => 'triangle-alert',
            ],
            'failed' => [
                'badge' => 'bg-red-50 text-red-700 ring-red-200',
                'icon' => 'x-circle',
            ],
        ];
    @endphp

    <div class="mx-auto w-full max-w-[100rem] space-y-6 px-2">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <div class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                        <i data-lucide="file-spreadsheet" class="h-3.5 w-3.5"></i>
                        Reporting & Audit
                    </div>
                    <h1 class="mt-4 text-2xl font-bold tracking-tight text-slate-900">Backup Reports</h1>
                    <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-500">
                        Laporan ini membaca data dari database backup logs. Halaman ini tidak scan folder backup, tidak menjalankan backup, dan tidak membuat scheduler.
                    </p>
                    <div class="mt-3 inline-flex items-center gap-2 rounded-full bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700 ring-1 ring-blue-100">
                        <i data-lucide="calendar-days" class="h-3.5 w-3.5"></i>
                        Periode:
                        {{ \Carbon\Carbon::parse(request('date_from', now()->toDateString()))->format('d M Y') }}
                        -
                        {{ \Carbon\Carbon::parse(request('date_to', now()->toDateString()))->format('d M Y') }}
                    </div>
                </div>

                <div class="flex flex-col items-start gap-1">
                    <a href="{{ route('backup-reports.export', request()->query()) }}"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700">
                        <i data-lucide="download" class="h-4 w-4"></i>
                        Export XLSX
                    </a>

                    <span class="inline-flex items-center gap-1 text-xs text-slate-500">
                        <i data-lucide="info" class="h-3.5 w-3.5"></i>
                        Export mengikuti filter aktif.
                    </span>
                </div>
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-7">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-medium text-slate-500">Total Log</p>
                        <p class="mt-2 text-2xl font-bold text-slate-900">{{ $summary['total'] }}</p>
                    </div>
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                        <i data-lucide="database" class="h-5 w-5"></i>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-emerald-200 bg-emerald-50/60 p-5 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-medium text-emerald-700">Success</p>
                        <p class="mt-2 text-2xl font-bold text-slate-900">{{ $summary['success'] }}</p>
                    </div>
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
                        <i data-lucide="check-circle-2" class="h-5 w-5"></i>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-amber-200 bg-amber-50/60 p-5 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-medium text-amber-700">Warning</p>
                        <p class="mt-2 text-2xl font-bold text-slate-900">{{ $summary['warning'] }}</p>
                    </div>
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-100 text-amber-700">
                        <i data-lucide="triangle-alert" class="h-5 w-5"></i>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-red-200 bg-red-50/60 p-5 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-medium text-red-700">Failed</p>
                        <p class="mt-2 text-2xl font-bold text-slate-900">{{ $summary['failed'] }}</p>
                    </div>
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-red-100 text-red-700">
                        <i data-lucide="x-circle" class="h-5 w-5"></i>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-medium text-slate-500">Pending</p>
                        <p class="mt-2 text-2xl font-bold text-slate-900">{{ $pendingJobs->count() }}</p>
                    </div>
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-slate-100 text-slate-600">
                        <i data-lucide="clock-alert" class="h-5 w-5"></i>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-medium text-slate-500">Total Ukuran</p>
                        <p class="mt-2 text-2xl font-bold text-slate-900">{{ $summary['total_size_label'] }}</p>
                    </div>
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-slate-100 text-slate-600">
                        <i data-lucide="hard-drive" class="h-5 w-5"></i>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-medium text-slate-500">Rata-rata Durasi</p>
                        <p class="mt-2 text-2xl font-bold text-slate-900">{{ $summary['avg_duration_label'] }}</p>
                    </div>
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-slate-100 text-slate-600">
                        <i data-lucide="timer" class="h-5 w-5"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-6 py-5">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                    <div>
                        <div class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                            <i data-lucide="clock-alert" class="h-3.5 w-3.5"></i>
                            Pending Check
                        </div>

                        <h2 class="mt-3 text-base font-semibold text-slate-900">
                            Job Belum Ada Log Pada {{ \Carbon\Carbon::parse($pendingDate)->format('d M Y') }}
                        </h2>

                        <p class="mt-1 text-sm leading-6 text-slate-500">
                            Pending di sini bukan log aktual. Ini adalah job aktif dengan sistem aktif yang belum memiliki backup log pada tanggal cek.
                        </p>
                    </div>

                    <div class="inline-flex items-center gap-2 rounded-full {{ $pendingJobs->count() ? 'bg-amber-50 text-amber-700 ring-amber-200' : 'bg-emerald-50 text-emerald-700 ring-emerald-200' }} px-3 py-1.5 text-sm font-semibold ring-1">
                        <i data-lucide="{{ $pendingJobs->count() ? 'triangle-alert' : 'check-circle-2' }}" class="h-4 w-4"></i>
                        {{ $pendingJobs->count() }} pending
                    </div>
                </div>
            </div>

            @if($pendingJobs->count())
                <div class="overflow-x-auto">
                    <table class="min-w-[900px] divide-y divide-slate-200 text-left text-sm">
                        <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                            <tr>
                                <th class="px-4 py-3">No</th>
                                <th class="px-4 py-3">Sistem</th>
                                <th class="px-4 py-3">Job</th>
                                <th class="px-4 py-3">Tipe</th>
                                <th class="px-4 py-3">Frekuensi</th>
                                <th class="px-4 py-3">Jam Ekspektasi</th>
                                <th class="px-4 py-3">Catatan</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach($pendingJobs as $job)
                                <tr class="hover:bg-slate-50/70">
                                    <td class="px-4 py-3 align-top text-xs font-semibold text-slate-400">
                                        {{ $loop->iteration }}
                                    </td>

                                    <td class="px-4 py-3 align-top">
                                        <div class="font-semibold text-slate-900">
                                            {{ $job->system?->name ?? '-' }}
                                        </div>
                                        <div class="mt-1 text-xs text-slate-500">
                                            {{ $job->system?->code ?? '-' }}
                                        </div>
                                    </td>

                                    <td class="px-4 py-3 align-top">
                                        <div class="font-semibold text-slate-900">
                                            {{ $job->name }}
                                        </div>
                                        <div class="mt-1 text-xs text-slate-500">
                                            {{ $job->code }}
                                        </div>
                                    </td>

                                    <td class="px-4 py-3 align-top">
                                        <span class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700 ring-1 ring-blue-100">
                                            {{ $job->backupTypeLabel() }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-3 align-top">
                                        <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700 ring-1 ring-slate-200">
                                            {{ $job->frequencyLabel() }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-3 align-top text-sm text-slate-600">
                                        {{ $job->expected_time ? $job->expected_time->format('H:i') : '-' }}
                                    </td>

                                    <td class="px-4 py-3 align-top">
                                        <div class="max-w-md text-xs leading-5 text-slate-500">
                                            Belum ada log masuk ke BMS pada tanggal {{ \Carbon\Carbon::parse($pendingDate)->format('d M Y') }}.
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="px-6 py-8">
                    <div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-emerald-200 bg-emerald-50/60 px-6 py-8 text-center">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
                            <i data-lucide="check-circle-2" class="h-6 w-6"></i>
                        </div>
                        <h3 class="mt-4 text-sm font-semibold text-emerald-900">Tidak ada pending job</h3>
                        <p class="mt-1 max-w-xl text-sm leading-6 text-emerald-700">
                            Semua job aktif dengan sistem aktif sudah memiliki log pada tanggal {{ \Carbon\Carbon::parse($pendingDate)->format('d M Y') }}.
                        </p>
                    </div>
                </div>
            @endif
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-6 py-5">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                    <div>
                        <h2 class="text-base font-semibold text-slate-900">Tabel Audit Backup</h2>
                        <p class="mt-1 text-sm text-slate-500">
                            Gunakan filter untuk melihat laporan harian, bulanan, per sistem, per job, storage, atau status tertentu.
                        </p>
                    </div>

                    <div class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1.5 text-sm text-slate-600">
                        <i data-lucide="list-filter" class="h-4 w-4"></i>
                        Ditampilkan:
                        <span class="font-semibold text-slate-900">{{ $logs->total() }}</span>
                        log
                    </div>
                </div>

                @if ($activeFilterCount > 0)
                    <div class="mt-4 flex flex-wrap items-center gap-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Filter aktif</span>

                        @if(request()->filled('search'))
                            <span class="inline-flex items-center gap-1 rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700 ring-1 ring-blue-100">
                                <i data-lucide="search" class="h-3.5 w-3.5"></i>
                                {{ request('search') }}
                            </span>
                        @endif

                        @if(request()->filled('date_from'))
                            <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700 ring-1 ring-slate-200">
                                Dari {{ \Carbon\Carbon::parse(request('date_from'))->format('d M Y') }}
                            </span>
                        @endif

                        @if(request()->filled('date_to'))
                            <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700 ring-1 ring-slate-200">
                                Sampai {{ \Carbon\Carbon::parse(request('date_to'))->format('d M Y') }}
                            </span>
                        @endif

                        @if($selectedSystem)
                            <span class="inline-flex items-center gap-1 rounded-full bg-violet-50 px-3 py-1 text-xs font-semibold text-violet-700 ring-1 ring-violet-100">
                                <i data-lucide="server" class="h-3.5 w-3.5"></i>
                                {{ $selectedSystem->name }}
                            </span>
                        @endif

                        @if($selectedJob)
                            <span class="inline-flex items-center gap-1 rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700 ring-1 ring-indigo-100">
                                <i data-lucide="workflow" class="h-3.5 w-3.5"></i>
                                {{ $selectedJob->name }}
                            </span>
                        @endif

                        @if($selectedStorage)
                            <span class="inline-flex items-center gap-1 rounded-full bg-sky-50 px-3 py-1 text-xs font-semibold text-sky-700 ring-1 ring-sky-100">
                                <i data-lucide="hard-drive" class="h-3.5 w-3.5"></i>
                                {{ $selectedStorage->name }}
                            </span>
                        @endif

                        @if(request()->filled('status'))
                            @php
                                $currentStatus = request('status');
                                $currentStatusStyle = $statusStyles[$currentStatus] ?? $statusStyles['warning'];
                            @endphp
                            <span class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-semibold ring-1 {{ $currentStatusStyle['badge'] }}">
                                <i data-lucide="{{ $currentStatusStyle['icon'] }}" class="h-3.5 w-3.5"></i>
                                {{ $statuses[$currentStatus] ?? $currentStatus }}
                            </span>
                        @endif

                        <a href="{{ route('backup-reports.index') }}"
                           class="inline-flex items-center gap-1 rounded-full bg-white px-3 py-1 text-xs font-semibold text-slate-600 ring-1 ring-slate-200 hover:bg-slate-50">
                            <i data-lucide="x" class="h-3.5 w-3.5"></i>
                            Bersihkan
                        </a>
                    </div>
                @endif
            </div>

            <form method="GET"
                  action="{{ route('backup-reports.index') }}"
                  x-data="{ open: {{ $hasAdvancedFilter ? 'true' : 'false' }} }"
                  class="border-b border-slate-200 bg-slate-50 px-6 py-5">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-end">
                    <div class="flex-1">
                        <label for="search" class="block text-xs font-bold uppercase tracking-wider text-slate-500">Cari Laporan</label>
                        <div class="mt-2 flex rounded-xl shadow-sm">
                            <span class="inline-flex items-center rounded-l-xl border border-r-0 border-slate-300 bg-white px-3 text-slate-400">
                                <i data-lucide="search" class="h-4 w-4"></i>
                            </span>
                            <input type="text"
                                   id="search"
                                   name="search"
                                   value="{{ request('search') }}"
                                   class="block w-full rounded-r-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500"
                                   placeholder="File, path, pesan, error, job, sistem, atau storage">
                        </div>
                    </div>

                    <div class="flex flex-col gap-2 sm:flex-row">
                        <button type="button"
                                x-on:click="open = !open"
                                class="inline-flex h-10 items-center justify-center gap-2 rounded-xl border px-4 text-sm font-semibold transition {{ $hasAdvancedFilter ? 'border-blue-200 bg-blue-50 text-blue-700 hover:bg-blue-100' : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50' }}">
                            <i data-lucide="sliders-horizontal" class="h-4 w-4"></i>
                            Filter Detail
                            @if($hasAdvancedFilter)
                                <span class="ml-1 inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-blue-600 px-1.5 text-[11px] font-bold text-white">
                                    {{ $activeFilterCount }}
                                </span>
                            @endif
                        </button>

                        <a href="{{ route('backup-reports.index') }}"
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

                <div x-show="open" x-cloak class="mt-5 rounded-2xl border border-slate-200 bg-white p-4">
                    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-6">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500">Tanggal Mulai</label>
                            <input type="date" name="date_from" value="{{ request('date_from', now()->toDateString()) }}" class="mt-2 block w-full rounded-xl border-slate-300 text-sm">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500">Tanggal Selesai</label>
                            <input type="date" name="date_to" value="{{ request('date_to', now()->toDateString()) }}" class="mt-2 block w-full rounded-xl border-slate-300 text-sm">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500">Sistem</label>
                            <select name="backup_system_id" class="mt-2 block w-full rounded-xl border-slate-300 text-sm">
                                <option value="">Semua sistem</option>
                                @foreach($systems as $system)
                                    <option value="{{ $system->id }}" @selected(request('backup_system_id') == $system->id)>
                                        {{ $system->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500">Job</label>
                            <select name="backup_job_id" class="mt-2 block w-full rounded-xl border-slate-300 text-sm">
                                <option value="">Semua job</option>
                                @foreach($jobs as $job)
                                    <option value="{{ $job->id }}" @selected(request('backup_job_id') == $job->id)>
                                        {{ $job->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500">Storage</label>
                            <select name="backup_storage_id" class="mt-2 block w-full rounded-xl border-slate-300 text-sm">
                                <option value="">Semua storage</option>
                                @foreach($storages as $storage)
                                    <option value="{{ $storage->id }}" @selected(request('backup_storage_id') == $storage->id)>
                                        {{ $storage->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500">Status</label>
                            <select name="status" class="mt-2 block w-full rounded-xl border-slate-300 text-sm">
                                <option value="">Semua status</option>
                                @foreach($statuses as $value => $label)
                                    <option value="{{ $value }}" @selected(request('status') === $value)>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </form>

            @if($logs->count())
                <div class="overflow-x-auto">
                    <table class="min-w-[1700px] divide-y divide-slate-200 text-left text-sm">
                        <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                            <tr>
                                <th class="px-4 py-3">No</th>
                                <th class="px-4 py-3">Tanggal</th>
                                <th class="px-4 py-3">Sistem</th>
                                <th class="px-4 py-3">Job</th>
                                <th class="px-4 py-3">Storage</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3">File</th>
                                <th class="px-4 py-3">Ukuran</th>
                                <th class="px-4 py-3">Durasi</th>
                                <th class="px-4 py-3">Waktu</th>
                                <th class="px-4 py-3">Pesan</th>
                                <th class="px-4 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach($logs as $log)
                                @php
                                    $style = $statusStyles[$log->status] ?? $statusStyles['warning'];
                                    $message = $log->status === 'failed'
                                        ? ($log->error_message ?: $log->message)
                                        : ($log->message ?: $log->error_message);
                                @endphp

                                <tr class="hover:bg-slate-50/70">
                                    <td class="px-4 py-3 align-top text-xs font-semibold text-slate-400">
                                        {{ $logs->firstItem() + $loop->index }}
                                    </td>

                                    <td class="px-4 py-3 align-top">
                                        <div class="font-semibold text-slate-900">
                                            {{ $log->backup_date?->format('d M Y') }}
                                        </div>
                                        <div class="mt-1 text-xs text-slate-400">
                                            Input {{ $log->created_at?->format('d M Y H:i') }}
                                        </div>
                                    </td>

                                    <td class="px-4 py-3 align-top">
                                        <div class="font-semibold text-slate-900">
                                            {{ $log->system?->name ?? '-' }}
                                        </div>
                                        <div class="mt-1 text-xs text-slate-500">
                                            {{ $log->system?->code ?? '-' }}
                                        </div>
                                    </td>

                                    <td class="px-4 py-3 align-top">
                                        <div class="font-semibold text-slate-900">
                                            {{ $log->job?->name ?? '-' }}
                                        </div>
                                        <div class="mt-1 text-xs text-slate-500">
                                            {{ $log->job?->code ?? '-' }}
                                        </div>
                                    </td>

                                    <td class="px-4 py-3 align-top">
                                        <div class="font-medium text-slate-700">
                                            {{ $log->storage?->name ?? '-' }}
                                        </div>
                                    </td>

                                    <td class="px-4 py-3 align-top">
                                        <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold ring-1 {{ $style['badge'] }}">
                                            <i data-lucide="{{ $style['icon'] }}" class="h-3.5 w-3.5"></i>
                                            {{ $log->statusLabel() }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-3 align-top">
                                        <div class="max-w-sm truncate font-medium text-slate-900">
                                            {{ $log->file_name ?: '-' }}
                                        </div>

                                        @if($log->file_path)
                                            <div class="mt-1 max-w-sm rounded-lg bg-slate-50 px-2 py-1 text-xs text-slate-500 ring-1 ring-slate-200">
                                                <span class="block truncate">{{ $log->file_path }}</span>
                                            </div>
                                        @endif
                                    </td>

                                    <td class="px-4 py-3 align-top">
                                        <div class="font-medium text-slate-700">
                                            {{ $log->fileSizeLabel() }}
                                        </div>
                                    </td>

                                    <td class="px-4 py-3 align-top">
                                        <div class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-700">
                                            <i data-lucide="timer" class="h-3.5 w-3.5"></i>
                                            {{ $log->durationLabel() }}
                                        </div>
                                    </td>

                                    <td class="px-4 py-3 align-top">
                                        <div class="text-xs text-slate-500">
                                            Mulai: {{ $log->started_at?->format('d M Y H:i') ?? '-' }}
                                        </div>
                                        <div class="mt-1 text-xs text-slate-500">
                                            Selesai: {{ $log->finished_at?->format('d M Y H:i') ?? '-' }}
                                        </div>
                                    </td>

                                    <td class="px-4 py-3 align-top">
                                        <div class="max-w-sm truncate text-xs {{ $log->status === 'failed' ? 'font-semibold text-red-700' : 'text-slate-600' }}">
                                            {{ $message ?: '-' }}
                                        </div>
                                    </td>

                                    <td class="px-4 py-3 align-top">
                                        <div class="flex justify-end">
                                            <a href="{{ route('backup-logs.show', $log) }}"
                                               class="inline-flex items-center gap-1.5 rounded-lg border border-blue-200 px-2.5 py-1.5 text-xs font-semibold text-blue-700 hover:bg-blue-50">
                                                <i data-lucide="eye" class="h-3.5 w-3.5"></i>
                                                Detail
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($logs->hasPages())
                    <div class="border-t border-slate-200 px-6 py-4">
                        {{ $logs->links() }}
                    </div>
                @endif
            @else
                <div class="px-6 py-16 text-center">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100">
                        <i data-lucide="file-search" class="h-7 w-7 text-slate-400"></i>
                    </div>
                    <h3 class="mt-5 text-base font-semibold text-slate-900">Tidak ada data laporan</h3>
                    <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">
                        Belum ada backup log yang cocok dengan filter laporan saat ini.
                    </p>
                    <a href="{{ route('backup-reports.index') }}"
                       class="mt-6 inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-slate-800">
                        <i data-lucide="rotate-ccw" class="h-4 w-4"></i>
                        Reset Filter
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection