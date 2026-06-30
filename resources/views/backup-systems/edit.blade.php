@extends('layouts.admin')

@section('title', 'Edit Sistem Backup')
@section('pageTitle', 'Edit Sistem Backup')
@section('pageSubtitle', 'Perbarui data sistem backup.')

@section('content')
    <div class="mx-auto max-w-5xl space-y-6">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-5 md:flex-row md:items-center md:justify-between">
                <div>
                    <div class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                        <i data-lucide="server" class="h-3.5 w-3.5"></i>
                        Master Data
                    </div>

                    <h1 class="mt-4 text-2xl font-bold tracking-tight text-slate-900">
                        Edit Sistem Backup
                    </h1>

                    <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-500">
                        Perbarui data sistem backup, storage tujuan, kategori, server sumber, jadwal, dan frekuensi backup.
                    </p>
                </div>

                <a href="{{ route('backup-systems.index') }}"
                   class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                    <i data-lucide="arrow-left" class="h-4 w-4"></i>
                    Kembali
                </a>
            </div>
        </div>

        <form action="{{ route('backup-systems.update', $system) }}" method="POST" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            @csrf
            @method('PUT')

            @include('backup-systems._form')

            <div class="mt-8 flex items-center justify-end gap-3 border-t border-slate-200 pt-6">
                <a href="{{ route('backup-systems.index') }}"
                   class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                    Batal
                </a>

                <button type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700">
                    <i data-lucide="save" class="h-4 w-4"></i>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
@endsection