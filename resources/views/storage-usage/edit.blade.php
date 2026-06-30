@extends('layouts.admin')

@section('title', 'Update Kapasitas Storage')

@section('content')
    <div class="space-y-6">
        <div class="overflow-hidden rounded-[2rem] bg-slate-950 shadow-sm ring-1 ring-slate-900/10">
            <div class="relative isolate p-6 sm:p-8">
                <div class="absolute inset-0 -z-10 bg-[radial-gradient(circle_at_top_right,rgba(34,211,238,0.24),transparent_35%),radial-gradient(circle_at_bottom_left,rgba(59,130,246,0.18),transparent_35%)]"></div>

                <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <div class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 text-xs font-semibold text-cyan-100 ring-1 ring-white/15">
                            <i data-lucide="pencil" class="h-3.5 w-3.5"></i>
                            Manual Storage Update
                        </div>

                        <h1 class="mt-4 text-2xl font-bold tracking-tight text-white sm:text-3xl">
                            Update Kapasitas Storage
                        </h1>

                        <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-300">
                            Perbarui data kapasitas storage secara manual. Data ini akan disimpan ke database dan dibaca oleh halaman Storage Usage.
                        </p>
                    </div>

                    <a href="{{ route('storage-usage.index') }}"
                       class="inline-flex items-center justify-center gap-2 rounded-xl bg-white/10 px-4 py-2.5 text-sm font-semibold text-white ring-1 ring-white/15 hover:bg-white/15">
                        <i data-lucide="arrow-left" class="h-4 w-4"></i>
                        Kembali
                    </a>
                </div>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2">
                <form action="{{ route('storage-usage.update', $storage) }}" method="POST" class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                    @csrf
                    @method('PATCH')

                    <div class="border-b border-slate-100 pb-5">
                        <h2 class="text-lg font-bold text-slate-900">Form Kapasitas</h2>
                        <p class="mt-1 text-sm text-slate-500">
                            Masukkan total capacity dan used space dengan unit yang sama.
                        </p>
                    </div>

                    <div class="mt-5 grid gap-5 sm:grid-cols-2">
                        <div>
                            <label for="total_space" class="block text-sm font-semibold text-slate-700">
                                Total Capacity
                            </label>
                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                name="total_space"
                                id="total_space"
                                value="{{ old('total_space', $totalEditable['value']) }}"
                                class="mt-2 block w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-cyan-500 focus:outline-none focus:ring-4 focus:ring-cyan-100"
                                placeholder="Contoh: 2"
                                required
                            >
                            @error('total_space')
                                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="used_space" class="block text-sm font-semibold text-slate-700">
                                Used Space
                            </label>
                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                name="used_space"
                                id="used_space"
                                value="{{ old('used_space', $usedEditable['value']) }}"
                                class="mt-2 block w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-cyan-500 focus:outline-none focus:ring-4 focus:ring-cyan-100"
                                placeholder="Contoh: 1.35"
                                required
                            >
                            @error('used_space')
                                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="unit" class="block text-sm font-semibold text-slate-700">
                                Unit
                            </label>
                            <select
                                name="unit"
                                id="unit"
                                class="mt-2 block w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-cyan-500 focus:outline-none focus:ring-4 focus:ring-cyan-100"
                                required
                            >
                                <option value="tb" @selected(old('unit', $defaultUnit) === 'tb')>TB</option>
                                <option value="gb" @selected(old('unit', $defaultUnit) === 'gb')>GB</option>
                                <option value="mb" @selected(old('unit', $defaultUnit) === 'mb')>MB</option>
                            </select>
                            @error('unit')
                                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="last_check_status" class="block text-sm font-semibold text-slate-700">
                                Status Check
                            </label>
                            <select
                                name="last_check_status"
                                id="last_check_status"
                                class="mt-2 block w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-cyan-500 focus:outline-none focus:ring-4 focus:ring-cyan-100"
                                required
                            >
                                @foreach (\App\Models\BackupStorage::checkStatuses() as $value => $label)
                                    <option value="{{ $value }}" @selected(old('last_check_status', $storage->last_check_status ?? 'unknown') === $value)>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('last_check_status')
                                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label for="last_check_message" class="block text-sm font-semibold text-slate-700">
                                Pesan Check / Catatan
                            </label>
                            <textarea
                                name="last_check_message"
                                id="last_check_message"
                                rows="4"
                                class="mt-2 block w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-cyan-500 focus:outline-none focus:ring-4 focus:ring-cyan-100"
                                placeholder="Contoh: Update manual dari pengecekan drive K: storage backup."
                            >{{ old('last_check_message', $storage->last_check_message) }}</textarea>
                            @error('last_check_message')
                                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6 flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:items-center sm:justify-end">
                        <a href="{{ route('storage-usage.index') }}"
                           class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                            Batal
                        </a>

                        <button type="submit"
                                class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">
                            <i data-lucide="save" class="h-4 w-4"></i>
                            Simpan Update
                        </button>
                    </div>
                </form>
            </div>

            <div class="space-y-4">
                <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                    <h2 class="text-lg font-bold text-slate-900">{{ $storage->name }}</h2>

                    <div class="mt-4 space-y-3 text-sm">
                        <div class="flex items-center justify-between gap-3">
                            <span class="text-slate-500">Status</span>
                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 {{ $storage->checkStatusBadgeClass() }}">
                                {{ $storage->checkStatusLabel() }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between gap-3">
                            <span class="text-slate-500">Total</span>
                            <span class="font-semibold text-slate-900">{{ $storage->totalSpaceLabel() }}</span>
                        </div>

                        <div class="flex items-center justify-between gap-3">
                            <span class="text-slate-500">Used</span>
                            <span class="font-semibold text-slate-900">{{ $storage->usedSpaceLabel() }}</span>
                        </div>

                        <div class="flex items-center justify-between gap-3">
                            <span class="text-slate-500">Free</span>
                            <span class="font-semibold text-slate-900">{{ $storage->freeSpaceLabel() }}</span>
                        </div>

                        <div class="flex items-center justify-between gap-3">
                            <span class="text-slate-500">Usage</span>
                            <span class="font-semibold text-slate-900">{{ $storage->usagePercentLabel() }}</span>
                        </div>

                        <div class="flex items-center justify-between gap-3">
                            <span class="text-slate-500">Last Checked</span>
                            <span class="text-right font-semibold text-slate-900">{{ $storage->lastCheckedLabel() }}</span>
                        </div>
                    </div>
                </div>

                <div class="rounded-3xl bg-cyan-50 p-5 text-sm leading-6 text-cyan-800 ring-1 ring-cyan-100">
                    <div class="flex items-start gap-3">
                        <i data-lucide="info" class="mt-0.5 h-5 w-5 shrink-0"></i>
                        <div>
                            <p class="font-bold">Catatan MVP</p>
                            <p class="mt-1">
                                Update manual ini tidak melakukan scan folder storage. Admin hanya mengisi hasil pengecekan kapasitas, lalu BMS menyimpannya ke database.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection