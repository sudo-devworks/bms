@extends('layouts.admin')

@section('title', 'Edit Backup Job')
@section('pageTitle', 'Edit Backup Job')
@section('pageSubtitle', 'Perbarui definisi pekerjaan backup.')

@section('content')
    <div class="mx-auto w-full max-w-5xl space-y-6">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="inline-flex items-center gap-2 rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700">
                <i data-lucide="pencil" class="h-3.5 w-3.5"></i>
                Edit Job
            </div>
            <h1 class="mt-4 text-2xl font-bold text-slate-900">{{ $job->name }}</h1>
            <p class="mt-2 text-sm leading-6 text-slate-500">Kode: <span class="font-semibold text-slate-700">{{ $job->code }}</span></p>
        </div>

        <form method="POST" action="{{ route('backup-jobs.update', $job) }}" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            @method('PUT')
            @include('backup-jobs._form')
        </form>
    </div>
@endsection
