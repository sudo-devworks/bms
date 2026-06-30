@extends('layouts.admin')

@section('title', 'Tambah Backup Log')
@section('pageTitle', 'Tambah Backup Log')
@section('pageSubtitle', 'Catat hasil backup secara manual untuk sementara sebelum API receiver dibuat.')

@section('content')
    <div class="mx-auto w-full max-w-5xl space-y-6">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                <div>
                    <div class="inline-flex items-center gap-2 rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                        <i data-lucide="clipboard-list" class="h-3.5 w-3.5"></i>
                        Backup Log
                    </div>
                    <h1 class="mt-4 text-2xl font-bold text-slate-900">Tambah Backup Log</h1>
                    <p class="mt-2 text-sm leading-6 text-slate-500">Log manual hanya untuk BMS-04. Integrasi otomatis script backup masuk BMS-05.</p>
                </div>

                @if($selectedJob)
                    <a href="{{ route('backup-jobs.show', $selectedJob) }}" class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                        <i data-lucide="arrow-left" class="h-4 w-4"></i>
                        Kembali ke Job
                    </a>
                @endif
            </div>
        </div>

        @if($selectedJob)
            <div class="rounded-2xl border border-blue-100 bg-blue-50/70 p-5 shadow-sm">
                <div class="flex gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-600 text-white">
                        <i data-lucide="workflow" class="h-5 w-5"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-bold uppercase tracking-wider text-blue-700">Log untuk job terpilih</p>
                        <h2 class="mt-1 truncate text-base font-bold text-slate-900">{{ $selectedJob->name }}</h2>
                        <div class="mt-2 flex flex-wrap gap-2 text-xs text-slate-600">
                            <span class="rounded-lg bg-white/80 px-2.5 py-1 font-mono font-semibold">{{ $selectedJob->code }}</span>
                            <span class="rounded-lg bg-white/80 px-2.5 py-1">Sistem: {{ $selectedJob->system?->name ?? '-' }}</span>
                            <span class="rounded-lg bg-white/80 px-2.5 py-1">Storage: {{ $selectedJob->system?->storage?->name ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('backup-logs.store') }}" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            @if($selectedJob)
                <input type="hidden" name="return_to_job" value="1">
                <input type="hidden" name="redirect_to" value="job">
            @endif
            @include('backup-logs._form')
        </form>
    </div>
@endsection
