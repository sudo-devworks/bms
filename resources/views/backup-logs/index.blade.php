@extends('layouts.admin')

@section('title', 'Backup Log')
@section('pageTitle', 'Backup Log')
@section('pageSubtitle', 'Riwayat hasil backup manual dan otomatis dari setiap job.')

@section('content')
    <div class="mx-auto w-full max-w-[100rem] space-y-6 px-2">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-5 md:flex-row md:items-center md:justify-between">
                <div>
                    <div class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                        <i data-lucide="clipboard-list" class="h-3.5 w-3.5"></i>
                        Backup History
                    </div>
                    <h1 class="mt-4 text-2xl font-bold tracking-tight text-slate-900">Backup Log</h1>
                    <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-500">
                        Catatan hasil backup per job. Log menyimpan snapshot sistem dan storage agar riwayat tetap terbaca walaupun konfigurasi berubah.
                    </p>
                </div>
                <a href="{{ route('backup-logs.create') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700">
                    <i data-lucide="plus" class="h-4 w-4"></i>
                    Tambah Log
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <a href="{{ route('backup-logs.index') }}" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-500">Total Log</p>
                        <p class="mt-2 text-3xl font-bold text-slate-900">{{ $summary['total'] }}</p>
                    </div>
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                        <i data-lucide="clipboard-list" class="h-5 w-5"></i>
                    </div>
                </div>
            </a>
            <a href="{{ route('backup-logs.index', ['status' => 'success']) }}" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-500">Success</p>
                        <p class="mt-2 text-3xl font-bold text-slate-900">{{ $summary['success'] }}</p>
                    </div>
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                        <i data-lucide="check-circle-2" class="h-5 w-5"></i>
                    </div>
                </div>
            </a>
            <a href="{{ route('backup-logs.index', ['status' => 'warning']) }}" class="rounded-2xl border border-amber-200 bg-amber-50/50 p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-amber-700">Warning</p>
                        <p class="mt-2 text-3xl font-bold text-slate-900">{{ $summary['warning'] }}</p>
                    </div>
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-100 text-amber-700">
                        <i data-lucide="triangle-alert" class="h-5 w-5"></i>
                    </div>
                </div>
            </a>
            <a href="{{ route('backup-logs.index', ['status' => 'failed']) }}" class="rounded-2xl border border-red-200 bg-red-50/50 p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-red-700">Failed</p>
                        <p class="mt-2 text-3xl font-bold text-slate-900">{{ $summary['failed'] }}</p>
                    </div>
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-red-100 text-red-700">
                        <i data-lucide="x-circle" class="h-5 w-5"></i>
                    </div>
                </div>
            </a>
        </div>

        @php
            $hasAdvancedFilter = request()->filled('backup_date_from') || request()->filled('backup_date_to') || request()->filled('backup_system_id') || request()->filled('backup_job_id') || request()->filled('status');
            $activeFilterCount = collect([
                request()->filled('search'),
                request()->filled('backup_date_from'),
                request()->filled('backup_date_to'),
                request()->filled('backup_system_id'),
                request()->filled('backup_job_id'),
                request()->filled('status'),
            ])->filter()->count();
            $selectedSystem = $systems->firstWhere('id', (int) request('backup_system_id'));
            $selectedJob = $jobs->firstWhere('id', (int) request('backup_job_id'));
        @endphp

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-6 py-5">
                <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                    <div>
                        <h2 class="text-base font-semibold text-slate-900">Riwayat Backup Log</h2>
                        <p class="mt-1 text-sm text-slate-500">Filter panjang dibuat compact agar list tetap lebar dan rapi.</p>
                    </div>
                    <div class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1.5 text-sm text-slate-600">
                        <i data-lucide="database" class="h-4 w-4"></i>
                        Ditampilkan: <span class="font-semibold text-slate-900">{{ $logs->total() }}</span> log
                    </div>
                </div>

                @if ($activeFilterCount > 0)
                    <div class="mt-4 flex flex-wrap items-center gap-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Filter aktif</span>
                        @if(request()->filled('search'))
                            <span class="inline-flex items-center gap-1 rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700 ring-1 ring-blue-100"><i data-lucide="search" class="h-3.5 w-3.5"></i>{{ request('search') }}</span>
                        @endif
                        @if(request()->filled('backup_date_from'))
                            <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700 ring-1 ring-slate-200">Dari {{ \Carbon\Carbon::parse(request('backup_date_from'))->format('d M Y') }}</span>
                        @endif
                        @if(request()->filled('backup_date_to'))
                            <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700 ring-1 ring-slate-200">Sampai {{ \Carbon\Carbon::parse(request('backup_date_to'))->format('d M Y') }}</span>
                        @endif
                        @if($selectedSystem)
                            <span class="inline-flex items-center gap-1 rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700 ring-1 ring-indigo-100"><i data-lucide="server" class="h-3.5 w-3.5"></i>{{ $selectedSystem->name }}</span>
                        @endif
                        @if($selectedJob)
                            <span class="inline-flex items-center gap-1 rounded-full bg-cyan-50 px-3 py-1 text-xs font-semibold text-cyan-700 ring-1 ring-cyan-100"><i data-lucide="briefcase-business" class="h-3.5 w-3.5"></i>{{ $selectedJob->name }}</span>
                        @endif
                        @if(request()->filled('status'))
                            <span class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-semibold ring-1 {{ request('status') === 'success' ? 'bg-emerald-50 text-emerald-700 ring-emerald-100' : (request('status') === 'warning' ? 'bg-amber-50 text-amber-700 ring-amber-100' : 'bg-red-50 text-red-700 ring-red-100') }}">
                                <i data-lucide="{{ request('status') === 'success' ? 'check-circle-2' : (request('status') === 'warning' ? 'triangle-alert' : 'x-circle') }}" class="h-3.5 w-3.5"></i>
                                {{ $statuses[request('status')] ?? request('status') }}
                            </span>
                        @endif
                        <a href="{{ route('backup-logs.index') }}" class="inline-flex items-center gap-1 rounded-full bg-white px-3 py-1 text-xs font-semibold text-slate-600 ring-1 ring-slate-200 hover:bg-slate-50">
                            <i data-lucide="x" class="h-3.5 w-3.5"></i>
                            Bersihkan
                        </a>
                    </div>
                @endif
            </div>

            <form method="GET" action="{{ route('backup-logs.index') }}" x-data="{ open: {{ $hasAdvancedFilter ? 'true' : 'false' }} }" class="border-b border-slate-200 bg-slate-50 px-6 py-5">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-end">
                    <div class="flex-1">
                        <label for="search" class="block text-xs font-bold uppercase tracking-wider text-slate-500">Cari Log</label>
                        <div class="mt-2 flex rounded-xl shadow-sm">
                            <span class="inline-flex items-center rounded-l-xl border border-r-0 border-slate-300 bg-white px-3 text-slate-400">
                                <i data-lucide="search" class="h-4 w-4"></i>
                            </span>
                            <input type="text" id="search" name="search" value="{{ request('search') }}" class="block w-full rounded-r-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500" placeholder="File, path, pesan, error, job, atau sistem">
                        </div>
                    </div>
                    <div class="flex flex-col gap-2 sm:flex-row">
                        <button type="button" x-on:click="open = !open" class="inline-flex h-10 items-center justify-center gap-2 rounded-xl border px-4 text-sm font-semibold transition {{ $hasAdvancedFilter ? 'border-blue-200 bg-blue-50 text-blue-700 hover:bg-blue-100' : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50' }}">
                            <i data-lucide="sliders-horizontal" class="h-4 w-4"></i>
                            Filter Detail
                            @if($hasAdvancedFilter)
                                <span class="ml-1 inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-blue-600 px-1.5 text-[11px] font-bold text-white">{{ $activeFilterCount }}</span>
                            @endif
                        </button>
                        <a href="{{ route('backup-logs.index') }}" class="inline-flex h-10 items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                            <i data-lucide="rotate-ccw" class="h-4 w-4"></i>
                            Reset
                        </a>
                        <button type="submit" class="inline-flex h-10 items-center justify-center gap-2 rounded-xl bg-slate-900 px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800">
                            <i data-lucide="filter" class="h-4 w-4"></i>
                            Terapkan
                        </button>
                    </div>
                </div>

                <div x-show="open" x-cloak class="mt-5 rounded-2xl border border-slate-200 bg-white p-4">
                    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500">Dari Tanggal</label>
                            <input type="date" name="backup_date_from" value="{{ request('backup_date_from') }}" class="mt-2 block w-full rounded-xl border-slate-300 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500">Sampai Tanggal</label>
                            <input type="date" name="backup_date_to" value="{{ request('backup_date_to') }}" class="mt-2 block w-full rounded-xl border-slate-300 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500">Sistem</label>
                            <select name="backup_system_id" class="mt-2 block w-full rounded-xl border-slate-300 text-sm">
                                <option value="">Semua sistem</option>
                                @foreach($systems as $system)
                                    <option value="{{ $system->id }}" @selected(request('backup_system_id') == $system->id)>{{ $system->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500">Job</label>
                            <select name="backup_job_id" class="mt-2 block w-full rounded-xl border-slate-300 text-sm">
                                <option value="">Semua job</option>
                                @foreach($jobs as $job)
                                    <option value="{{ $job->id }}" @selected(request('backup_job_id') == $job->id)>{{ $job->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500">Status</label>
                            <select name="status" class="mt-2 block w-full rounded-xl border-slate-300 text-sm">
                                <option value="">Semua status</option>
                                @foreach($statuses as $value => $label)
                                    <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </form>

            @if($logs->count())
                <div class="overflow-x-auto">
                    <table class="min-w-[1440px] divide-y divide-slate-200 text-left text-sm">
                        <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                            <tr>
                                <th class="px-4 py-3">Tanggal</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3">Job / Sistem</th>
                                <th class="px-4 py-3">Storage</th>
                                <th class="px-4 py-3">File</th>
                                <th class="px-4 py-3">Waktu</th>
                                <th class="px-4 py-3">Pesan</th>
                                <th class="px-4 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach($logs as $log)
                                @php
                                    $statusClass = $log->status === 'success'
                                        ? 'bg-emerald-50 text-emerald-700 ring-emerald-200'
                                        : ($log->status === 'warning'
                                            ? 'bg-amber-50 text-amber-700 ring-amber-200'
                                            : 'bg-red-50 text-red-700 ring-red-200');
                                    $statusIcon = $log->status === 'success' ? 'check-circle-2' : ($log->status === 'warning' ? 'triangle-alert' : 'x-circle');
                                @endphp
                                <tr class="hover:bg-slate-50/70">
                                    <td class="px-4 py-3 align-top">
                                        <div class="font-semibold text-slate-900">{{ $log->backup_date?->format('d M Y') }}</div>
                                        <div class="mt-1 text-xs text-slate-400">Dicatat {{ $log->created_at?->format('d M Y H:i') }}</div>
                                    </td>
                                    <td class="px-4 py-3 align-top">
                                        <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold ring-1 {{ $statusClass }}">
                                            <i data-lucide="{{ $statusIcon }}" class="h-3.5 w-3.5"></i>
                                            {{ $log->statusLabel() }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 align-top">
                                        <div class="font-semibold text-slate-900">{{ $log->job?->name ?? '-' }}</div>
                                        <div class="mt-1 flex flex-wrap items-center gap-2 text-xs text-slate-500">
                                            <span class="inline-flex items-center gap-1"><i data-lucide="server" class="h-3.5 w-3.5"></i>{{ $log->system?->name ?? '-' }}</span>
                                            @if($log->job)
                                                <a href="{{ route('backup-jobs.show', $log->job) }}" class="inline-flex items-center gap-1 font-semibold text-blue-700 hover:text-blue-800">
                                                    <i data-lucide="external-link" class="h-3.5 w-3.5"></i>
                                                    Detail Job
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 align-top">
                                        <div class="text-sm font-medium text-slate-700">{{ $log->storage?->name ?? '-' }}</div>
                                    </td>
                                    <td class="px-4 py-3 align-top">
                                        <div class="max-w-sm truncate font-medium text-slate-900">{{ $log->file_name ?: '-' }}</div>
                                        <div class="mt-1 text-xs text-slate-500">{{ $log->fileSizeLabel() }}</div>
                                        @if($log->file_path)
                                            <div class="mt-1 max-w-sm truncate text-xs text-slate-400">{{ $log->file_path }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 align-top">
                                        <div class="text-xs text-slate-500">Mulai: {{ $log->started_at?->format('H:i') ?? '-' }}</div>
                                        <div class="text-xs text-slate-500">Selesai: {{ $log->finished_at?->format('H:i') ?? '-' }}</div>
                                        <div class="mt-1 inline-flex items-center gap-1 rounded-full bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-700">
                                            <i data-lucide="timer" class="h-3.5 w-3.5"></i>
                                            {{ $log->durationLabel() }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 align-top">
                                        <div class="max-w-sm truncate text-xs {{ $log->status === 'failed' ? 'font-semibold text-red-700' : 'text-slate-600' }}">
                                            {{ $log->status === 'failed' ? ($log->error_message ?: '-') : ($log->message ?: '-') }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 align-top">
                                        <div class="flex items-center justify-end gap-1.5 whitespace-nowrap">
                                            <a href="{{ route('backup-logs.show', $log) }}" class="inline-flex items-center gap-1.5 rounded-lg border border-blue-200 px-2.5 py-1.5 text-xs font-semibold text-blue-700 hover:bg-blue-50">
                                                <i data-lucide="eye" class="h-3.5 w-3.5"></i>
                                                Detail Log
                                            </a>
                                            @if($log->job)
                                                <a href="{{ route('backup-jobs.show', $log->job) }}" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 px-2.5 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                                    <i data-lucide="briefcase-business" class="h-3.5 w-3.5"></i>
                                                    Job
                                                </a>
                                            @endif
                                            <form action="{{ route('backup-logs.destroy', $log) }}" method="POST" data-confirm-delete data-confirm-title="Hapus Backup Log?" data-confirm-message="Log backup tanggal {{ $log->backup_date?->format('d M Y') }} untuk {{ $log->job?->name ?? 'job ini' }} akan dihapus dari riwayat monitoring." data-confirm-button="Ya, Hapus Log">
                                                @csrf
                                                @method('DELETE')
                                                <button class="inline-flex items-center gap-1.5 rounded-lg border border-red-200 px-2.5 py-1.5 text-xs font-semibold text-red-600 hover:bg-red-50">
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
                @if($logs->hasPages())
                    <div class="border-t border-slate-200 px-6 py-4">{{ $logs->links() }}</div>
                @endif
            @else
                <div class="px-6 py-16 text-center">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100">
                        <i data-lucide="clipboard-list" class="h-7 w-7 text-slate-400"></i>
                    </div>
                    <h3 class="mt-5 text-base font-semibold text-slate-900">Belum ada backup log</h3>
                    <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">Tambahkan log manual dari hasil backup yang sudah berjalan.</p>
                    <a href="{{ route('backup-logs.create') }}" class="mt-6 inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700">
                        <i data-lucide="plus" class="h-4 w-4"></i>
                        Tambah Log
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection
