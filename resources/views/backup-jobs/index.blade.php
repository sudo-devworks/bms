@extends('layouts.admin')

@section('title', 'Backup Job')
@section('pageTitle', 'Backup Job')
@section('pageSubtitle', 'Kelola definisi pekerjaan backup yang dimonitor oleh BMS.')

@section('content')
    <div class="mx-auto w-full max-w-[96rem] space-y-6 px-2">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-5 md:flex-row md:items-center md:justify-between">
                <div>
                    <div class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                        <i data-lucide="workflow" class="h-3.5 w-3.5"></i>
                        Operational Data
                    </div>
                    <h1 class="mt-4 text-2xl font-bold tracking-tight text-slate-900">Backup Job</h1>
                    <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-500">
                        Definisi pekerjaan backup yang seharusnya berjalan. Modul ini belum menjalankan backup, hanya menjadi dasar pencatatan log dan monitoring.
                    </p>
                </div>
                <a href="{{ route('backup-jobs.create') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700">
                    <i data-lucide="plus" class="h-4 w-4"></i>
                    Tambah Job
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">{{ session('success') }}</div>
        @endif

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><div class="flex items-center justify-between"><div><p class="text-sm font-medium text-slate-500">Total Job</p><p class="mt-2 text-3xl font-bold text-slate-900">{{ $summary['total'] }}</p></div><div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-blue-600"><i data-lucide="workflow" class="h-5 w-5"></i></div></div></div>
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><div class="flex items-center justify-between"><div><p class="text-sm font-medium text-slate-500">Job Aktif</p><p class="mt-2 text-3xl font-bold text-slate-900">{{ $summary['active'] }}</p></div><div class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600"><i data-lucide="check-circle-2" class="h-5 w-5"></i></div></div></div>
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><div class="flex items-center justify-between"><div><p class="text-sm font-medium text-slate-500">Job Nonaktif</p><p class="mt-2 text-3xl font-bold text-slate-900">{{ $summary['inactive'] }}</p></div><div class="flex h-11 w-11 items-center justify-center rounded-xl bg-slate-100 text-slate-600"><i data-lucide="pause-circle" class="h-5 w-5"></i></div></div></div>
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><div class="flex items-center justify-between"><div><p class="text-sm font-medium text-slate-500">Backup Harian</p><p class="mt-2 text-3xl font-bold text-slate-900">{{ $summary['daily'] }}</p></div><div class="flex h-11 w-11 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600"><i data-lucide="calendar-days" class="h-5 w-5"></i></div></div></div>
        </div>

        @php
            $hasAdvancedFilter = request()->filled('backup_system_id') || request()->filled('backup_type') || request()->filled('expected_frequency') || request()->filled('status');
        @endphp

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-6 py-5">
                <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                    <div><h2 class="text-base font-semibold text-slate-900">Daftar Backup Job</h2><p class="mt-1 text-sm text-slate-500">Job aktif akan dihitung sebagai job wajib backup pada dashboard monitoring berikutnya.</p></div>
                    <div class="text-sm text-slate-500">Ditampilkan: <span class="font-semibold text-slate-900">{{ $jobs->total() }}</span> job</div>
                </div>
            </div>

            <form method="GET" action="{{ route('backup-jobs.index') }}" x-data="{ open: {{ $hasAdvancedFilter ? 'true' : 'false' }} }" class="border-b border-slate-200 bg-slate-50 px-6 py-5">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-end">
                    <div class="flex-1">
                        <label for="search" class="block text-xs font-bold uppercase tracking-wider text-slate-500">Cari Job</label>
                        <div class="mt-2 flex rounded-xl shadow-sm"><span class="inline-flex items-center rounded-l-xl border border-r-0 border-slate-300 bg-white px-3 text-slate-400"><i data-lucide="search" class="h-4 w-4"></i></span><input type="text" id="search" name="search" value="{{ request('search') }}" class="block w-full rounded-r-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Nama, kode, jadwal, atau sistem"></div>
                    </div>
                    <div class="flex flex-col gap-2 sm:flex-row">
                        <button type="button" x-on:click="open = !open" class="inline-flex h-10 items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"><i data-lucide="sliders-horizontal" class="h-4 w-4"></i>Filter Detail @if ($hasAdvancedFilter)<span class="ml-1 inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-blue-600 px-1.5 text-[11px] font-bold text-white">{{ collect([request()->filled('backup_system_id'), request()->filled('backup_type'), request()->filled('expected_frequency'), request()->filled('status')])->filter()->count() }}</span>@endif</button>
                        <a href="{{ route('backup-jobs.index') }}" class="inline-flex h-10 items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"><i data-lucide="rotate-ccw" class="h-4 w-4"></i>Reset</a>
                        <button type="submit" class="inline-flex h-10 items-center justify-center gap-2 rounded-xl bg-slate-900 px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800"><i data-lucide="filter" class="h-4 w-4"></i>Terapkan</button>
                    </div>
                </div>

                <div x-show="open" x-cloak class="mt-5 rounded-2xl border border-slate-200 bg-white p-4">
                    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                        <div><label class="block text-xs font-bold uppercase tracking-wider text-slate-500">Sistem</label><select name="backup_system_id" class="mt-2 block w-full rounded-xl border-slate-300 text-sm"><option value="">Semua sistem</option>@foreach($systems as $system)<option value="{{ $system->id }}" @selected(request('backup_system_id') == $system->id)>{{ $system->name }}</option>@endforeach</select></div>
                        <div><label class="block text-xs font-bold uppercase tracking-wider text-slate-500">Backup Type</label><select name="backup_type" class="mt-2 block w-full rounded-xl border-slate-300 text-sm"><option value="">Semua type</option>@foreach($backupTypes as $value => $label)<option value="{{ $value }}" @selected(request('backup_type') === $value)>{{ $label }}</option>@endforeach</select></div>
                        <div><label class="block text-xs font-bold uppercase tracking-wider text-slate-500">Frequency</label><select name="expected_frequency" class="mt-2 block w-full rounded-xl border-slate-300 text-sm"><option value="">Semua frequency</option>@foreach($frequencies as $value => $label)<option value="{{ $value }}" @selected(request('expected_frequency') === $value)>{{ $label }}</option>@endforeach</select></div>
                        <div><label class="block text-xs font-bold uppercase tracking-wider text-slate-500">Status</label><select name="status" class="mt-2 block w-full rounded-xl border-slate-300 text-sm"><option value="">Semua status</option><option value="active" @selected(request('status') === 'active')>Aktif</option><option value="inactive" @selected(request('status') === 'inactive')>Nonaktif</option></select></div>
                    </div>
                </div>
            </form>

            @if ($jobs->count())
                <div class="overflow-x-auto">
                    <table class="min-w-[1320px] divide-y divide-slate-200 text-left text-sm">
                        <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500"><tr><th class="px-4 py-3">Job</th><th class="px-4 py-3">Sistem</th><th class="px-4 py-3">Type</th><th class="px-4 py-3">Jadwal</th><th class="px-4 py-3">Alert Pending</th><th class="px-4 py-3">Log Terakhir</th><th class="px-4 py-3">Status</th><th class="px-4 py-3 text-right">Aksi</th></tr></thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach($jobs as $job)
                                <tr class="hover:bg-slate-50/70">
                                    <td class="px-4 py-3 align-top"><div class="font-semibold text-slate-900">{{ $job->name }}</div><div class="mt-1 font-mono text-xs text-slate-500">{{ $job->code }}</div></td>
                                    <td class="px-4 py-3 align-top"><div class="font-semibold text-slate-900">{{ $job->system?->name ?? '-' }}</div><div class="mt-1 text-xs text-slate-500">{{ $job->system?->storage?->name ?? '-' }}</div></td>
                                    <td class="px-4 py-3 align-top"><span class="inline-flex rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700 ring-1 ring-blue-100">{{ $job->backupTypeLabel() }}</span></td>
                                    <td class="px-4 py-3 align-top"><div class="text-sm font-semibold text-slate-700">{{ $job->frequencyLabel() }}</div><div class="mt-1 text-xs text-slate-500">{{ optional($job->expected_time)->format('H:i') ?? '-' }}</div><div class="mt-1 max-w-xs text-xs text-slate-500">{{ $job->schedule_text }}</div></td>
                                    <td class="px-4 py-3 align-top">
                                        <div class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700 ring-1 ring-amber-100">
                                            <i data-lucide="bell-ring" class="h-3.5 w-3.5"></i>
                                            {{ $job->expectedRunTimeLabel() }}
                                        </div>

                                        <div class="mt-1 text-xs text-slate-500">
                                            Toleransi: {{ $job->alertAfterMinutesLabel() }}
                                        </div>

                                        <div class="mt-1 text-xs text-slate-500">
                                            Pending mulai: {{ $job->pendingAlertTimeLabel() }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 align-top">@if($job->latestLog)<span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold ring-1 {{ $job->latestLog->status === 'success' ? 'bg-emerald-50 text-emerald-700 ring-emerald-200' : ($job->latestLog->status === 'warning' ? 'bg-amber-50 text-amber-700 ring-amber-200' : 'bg-red-50 text-red-700 ring-red-200') }}">{{ $job->latestLog->statusLabel() }}</span><div class="mt-1 text-xs text-slate-500">{{ $job->latestLog->backup_date?->format('d M Y') }}</div>@else<span class="text-xs text-slate-400">Belum ada log</span>@endif</td>
                                    <td class="px-4 py-3 align-top">@if($job->is_active)<span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-emerald-200"><span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>Aktif</span>@else<span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600 ring-1 ring-slate-200"><span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>Nonaktif</span>@endif</td>
                                    <td class="px-4 py-3 align-top"><div class="flex items-center justify-end gap-1.5 whitespace-nowrap"><a href="{{ route('backup-jobs.show', $job) }}" class="inline-flex items-center gap-1.5 rounded-lg border border-blue-200 px-2.5 py-1.5 text-xs font-semibold text-blue-700 hover:bg-blue-50"><i data-lucide="eye" class="h-3.5 w-3.5"></i>Detail</a><form action="{{ route('backup-jobs.toggle-status', $job) }}" method="POST">@csrf @method('PATCH')<button class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 px-2.5 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100"><i data-lucide="{{ $job->is_active ? 'pause-circle' : 'play-circle' }}" class="h-3.5 w-3.5"></i>{{ $job->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</button></form><a href="{{ route('backup-jobs.edit', $job) }}" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 px-2.5 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100"><i data-lucide="pencil" class="h-3.5 w-3.5"></i>Edit</a><form action="{{ route('backup-jobs.destroy', $job) }}" method="POST" data-confirm-delete data-confirm-title="Hapus Backup Job?" data-confirm-message="Backup job {{ $job->name }} akan dihapus. Job yang sudah memiliki log tetap akan ditolak oleh sistem." data-confirm-button="Ya, Hapus Job">@csrf @method('DELETE')<button class="inline-flex items-center gap-1.5 rounded-lg border border-red-200 px-2.5 py-1.5 text-xs font-semibold text-red-600 hover:bg-red-50"><i data-lucide="trash-2" class="h-3.5 w-3.5"></i>Hapus</button></form></div></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($jobs->hasPages())<div class="border-t border-slate-200 px-6 py-4">{{ $jobs->links() }}</div>@endif
            @else
                <div class="px-6 py-16 text-center"><div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100"><i data-lucide="workflow" class="h-7 w-7 text-slate-400"></i></div><h3 class="mt-5 text-base font-semibold text-slate-900">Belum ada backup job</h3><p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">Tambahkan job pertama untuk sistem backup yang sudah terdaftar.</p><a href="{{ route('backup-jobs.create') }}" class="mt-6 inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700"><i data-lucide="plus" class="h-4 w-4"></i>Tambah Job</a></div>
            @endif
        </div>
    </div>
@endsection
