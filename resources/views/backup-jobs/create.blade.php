@extends('layouts.admin')

@section('title', 'Tambah Backup Job')
@section('pageTitle', 'Tambah Backup Job')
@section('pageSubtitle', 'Definisikan pekerjaan backup yang akan dimonitor oleh BMS.')

@section('content')
    <div class="mx-auto w-full max-w-5xl space-y-6">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="inline-flex items-center gap-2 rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                <i data-lucide="workflow" class="h-3.5 w-3.5"></i>
                Backup Job
            </div>
            <h1 class="mt-4 text-2xl font-bold text-slate-900">Tambah Backup Job</h1>
            <p class="mt-2 text-sm leading-6 text-slate-500">Job hanya definisi monitoring. BMS tidak menjalankan proses backup dari dashboard.</p>
        </div>

        <form method="POST" action="{{ route('backup-jobs.store') }}" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            @include('backup-jobs._form')
        </form>
    </div>
@endsection
