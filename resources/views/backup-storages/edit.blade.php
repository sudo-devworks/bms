@extends('layouts.admin')

@section('title', 'Edit Storage Backup')
@section('pageTitle', 'Edit Storage Backup')
@section('pageSubtitle', 'Perbarui informasi lokasi penyimpanan backup.')

@section('content')
    <div class="mx-auto max-w-5xl space-y-6">
        <div class="flex items-center justify-between gap-4">
            <div>
                <a href="{{ route('backup-storages.index') }}"
                   class="inline-flex items-center gap-2 text-sm font-semibold text-slate-500 transition hover:text-slate-900">
                    <i data-lucide="arrow-left" class="h-4 w-4"></i>
                    Kembali ke daftar storage
                </a>
            </div>
        </div>

        <form action="{{ route('backup-storages.update', $storage) }}" method="POST" class="space-y-6">
            @method('PUT')

            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-6 py-5">
                    <h2 class="text-base font-semibold text-slate-900">
                        Informasi Storage
                    </h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Perubahan data storage akan memengaruhi mapping backup pada milestone berikutnya.
                    </p>
                </div>

                <div class="p-6">
                    @include('backup-storages._form')
                </div>
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('backup-storages.index') }}"
                   class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
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