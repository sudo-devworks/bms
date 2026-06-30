@extends('layouts.admin')

@section('title', 'Backup Alerts')
@section('pageTitle', 'Backup Alerts')
@section('pageSubtitle', 'Laporan backup untuk monitoring, audit, dan pelaporan internal.')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm md:flex-row md:items-center md:justify-between">
            <div>
                <div class="flex items-center gap-2 text-sm font-semibold text-rose-600">
                    <i data-lucide="bell-ring" class="h-4 w-4"></i>
                    Alert & Notification
                </div>

                <h1 class="mt-2 text-2xl font-bold text-slate-900">
                    Backup Alerts
                </h1>

                <p class="mt-1 text-sm text-slate-500">
                    Riwayat peringatan backup dan storage berdasarkan data yang sudah tercatat di database.
                    Gunakan tombol kirim manual jika email alert perlu dikirim ulang tanpa akses terminal.
                </p>
            </div>

            <div class="flex flex-wrap items-center justify-end gap-2">
                <form method="POST" action="{{ route('backup-alerts.send-pending-emails') }}">
                    @csrf

                    <button type="submit"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-3.5 py-2 text-xs font-bold text-white transition hover:bg-blue-700">
                        <i data-lucide="send" class="h-4 w-4"></i>
                        Send Pending
                    </button>
                </form>

                <form method="POST" action="{{ route('backup-alerts.retry-failed-emails') }}">
                    @csrf

                    <button type="submit"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-rose-600 px-3.5 py-2 text-xs font-bold text-white transition hover:bg-rose-700">
                        <i data-lucide="refresh-cw" class="h-4 w-4"></i>
                        Retry Failed
                    </button>
                </form>

                <a href="{{ route('monitoring.index') }}"
                target="_blank"
                class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-bold text-slate-700 transition hover:bg-slate-50">
                    <i data-lucide="monitor-up" class="h-4 w-4"></i>
                    Monitoring
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700">
                {{ session('error') }}
            </div>
        @endif

        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <div class="flex items-center gap-2 text-sm font-semibold text-slate-500">
                        <i data-lucide="activity" class="h-4 w-4"></i>
                        Alert Checker Status
                    </div>

                    <h2 class="mt-2 text-lg font-bold text-slate-900">
                        {{ $alertCheckerMonitor?->name ?? 'BMS Alert Checker' }}
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        {{ $alertCheckerMonitor?->message ?? 'Command alert checker belum pernah berjalan.' }}
                    </p>
                </div>

                <div class="flex flex-col gap-2 md:items-end">
                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold ring-1 {{ $alertCheckerMonitor?->statusBadgeClass() ?? 'bg-slate-50 text-slate-700 ring-slate-200' }}">
                        {{ $alertCheckerMonitor?->statusLabel() ?? 'Unknown' }}
                    </span>

                    <div class="text-sm font-semibold text-slate-500">
                        Last run:
                        <span class="text-slate-800">
                            {{ $alertCheckerMonitor?->lastRunLabel() ?? '-' }}
                        </span>
                    </div>
                </div>
            </div>
        </section>

        <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-7">
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-semibold text-slate-500">Total</span>
                    <i data-lucide="inbox" class="h-5 w-5 text-slate-400"></i>
                </div>
                <div class="mt-3 text-3xl font-black text-slate-900">{{ $summary['total'] }}</div>
            </div>

            <div class="rounded-2xl border border-blue-200 bg-blue-50 p-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-semibold text-blue-700">New</span>
                    <i data-lucide="sparkles" class="h-5 w-5 text-blue-500"></i>
                </div>
                <div class="mt-3 text-3xl font-black text-blue-800">{{ $summary['new'] }}</div>
            </div>

            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-semibold text-emerald-700">Sent</span>
                    <i data-lucide="send" class="h-5 w-5 text-emerald-500"></i>
                </div>
                <div class="mt-3 text-3xl font-black text-emerald-800">{{ $summary['sent'] }}</div>
            </div>

            <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-semibold text-rose-700">Failed Send</span>
                    <i data-lucide="mail-x" class="h-5 w-5 text-rose-500"></i>
                </div>
                <div class="mt-3 text-3xl font-black text-rose-800">{{ $summary['failed'] }}</div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-semibold text-slate-500">Resolved</span>
                    <i data-lucide="circle-check-big" class="h-5 w-5 text-slate-400"></i>
                </div>
                <div class="mt-3 text-3xl font-black text-slate-900">{{ $summary['resolved'] }}</div>
            </div>

            <div class="rounded-2xl border border-zinc-200 bg-zinc-50 p-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-semibold text-zinc-600">Ignored</span>
                    <i data-lucide="archive-x" class="h-5 w-5 text-zinc-400"></i>
                </div>
                <div class="mt-3 text-3xl font-black text-zinc-800">{{ $summary['ignored'] }}</div>
            </div>

            <div class="rounded-2xl border border-red-200 bg-red-50 p-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-semibold text-red-700">Critical Active</span>
                    <i data-lucide="triangle-alert" class="h-5 w-5 text-red-500"></i>
                </div>
                <div class="mt-3 text-3xl font-black text-red-800">{{ $summary['critical'] }}</div>
            </div>
        </section>

        <section class="grid gap-4 lg:grid-cols-3">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm lg:col-span-1">
                <div class="flex items-center gap-2 text-sm font-semibold text-blue-600">
                    <i data-lucide="mail-plus" class="h-4 w-4"></i>
                    Email Recipient
                </div>

                <h2 class="mt-2 text-lg font-bold text-slate-900">
                    Penerima Notifikasi
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Email alert akan dikirim ke penerima aktif. SMTP tetap mengikuti konfigurasi Laravel di file <code class="rounded bg-slate-100 px-1 py-0.5 text-xs">.env</code>.
                </p>

                <form method="POST" action="{{ route('backup-alerts.recipients.store') }}" class="mt-4 space-y-3">
                    @csrf

                    <div>
                        <label for="recipient_name" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Nama Penerima
                        </label>
                        <input
                            type="text"
                            id="recipient_name"
                            name="recipient_name"
                            value="{{ old('recipient_name') }}"
                            placeholder="Contoh: Admin TI"
                            class="w-full rounded-xl border-slate-200 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('recipient_name')
                            <div class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label for="recipient_email" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Email
                        </label>
                        <input
                            type="email"
                            id="recipient_email"
                            name="recipient_email"
                            value="{{ old('recipient_email') }}"
                            placeholder="admin@example.com"
                            class="w-full rounded-xl border-slate-200 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('recipient_email')
                            <div class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</div>
                        @enderror
                    </div>

                    <label class="flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-semibold text-slate-600">
                        <input
                            type="checkbox"
                            name="is_active"
                            value="1"
                            checked
                            class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                        Aktifkan penerima ini
                    </label>

                    <button type="submit"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700">
                        <i data-lucide="save" class="h-4 w-4"></i>
                        Simpan Penerima
                    </button>
                </form>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm lg:col-span-2">
                <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">
                            Daftar Penerima Email
                        </h2>
                        <p class="mt-1 text-sm text-slate-500">
                            Hanya penerima aktif yang akan menerima alert.
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <form method="POST" action="{{ route('backup-alerts.test-email') }}">
                            @csrf

                            <button type="submit"
                                    class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-3 py-2 text-xs font-bold text-white transition hover:bg-emerald-700">
                                <i data-lucide="send" class="h-4 w-4"></i>
                                Test Email
                            </button>
                        </form>

                        <div class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">
                            {{ $notificationSettings->where('is_active', true)->count() }} aktif /
                            {{ $notificationSettings->count() }} total
                        </div>
                    </div>
                </div>

                <div class="mt-4 overflow-hidden rounded-xl border border-slate-200">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                            <tr>
                                <th class="px-4 py-3">Penerima</th>
                                <th class="px-4 py-3">Channel</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse ($notificationSettings as $recipient)
                                <tr>
                                    <td class="px-4 py-3">
                                        <div class="font-bold text-slate-900">
                                            {{ $recipient->recipient_name ?: 'Tanpa nama' }}
                                        </div>
                                        <div class="mt-0.5 text-sm text-slate-500">
                                            {{ $recipient->recipient_email }}
                                        </div>
                                    </td>

                                    <td class="px-4 py-3">
                                        <span class="inline-flex rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-blue-700 ring-1 ring-blue-200">
                                            {{ $recipient->channelLabel() }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-3">
                                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold ring-1 {{ $recipient->statusBadgeClass() }}">
                                            {{ $recipient->statusLabel() }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-3 text-right">
                                        <div class="flex justify-end gap-2">
                                            <form method="POST" action="{{ route('backup-alerts.recipients.toggle', $recipient) }}">
                                                @csrf
                                                @method('PATCH')

                                                <button type="submit"
                                                        class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-50">
                                                    <i data-lucide="{{ $recipient->is_active ? 'pause-circle' : 'play-circle' }}" class="h-4 w-4"></i>
                                                    {{ $recipient->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                                </button>
                                            </form>

                                            <form method="POST" action="{{ route('backup-alerts.recipients.delete', $recipient) }}"
                                                onsubmit="return confirm('Hapus penerima notifikasi ini?')">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                        class="inline-flex items-center gap-1.5 rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-700 transition hover:bg-rose-100">
                                                    <i data-lucide="trash-2" class="h-4 w-4"></i>
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center">
                                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                                            <i data-lucide="mail-x" class="h-6 w-6"></i>
                                        </div>
                                        <div class="mt-3 text-sm font-bold text-slate-800">
                                            Belum ada penerima notifikasi
                                        </div>
                                        <div class="mt-1 text-sm text-slate-500">
                                            Tambahkan minimal satu email penerima sebelum fitur kirim alert digunakan.
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <form method="GET" action="{{ route('backup-alerts.index') }}" class="space-y-4">
                <div class="grid gap-3 md:grid-cols-5">
                    <div class="md:col-span-2">
                        <label for="search" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Search
                        </label>
                        <div class="relative">
                            <i data-lucide="search" class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"></i>
                            <input
                                type="text"
                                id="search"
                                name="search"
                                value="{{ $filters['search'] ?? '' }}"
                                placeholder="Cari title, pesan, job, sistem, storage..."
                                class="w-full rounded-xl border-slate-200 pl-9 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>

                    <div>
                        <label for="status" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Status
                        </label>
                        <select
                            id="status"
                            name="status"
                            class="w-full rounded-xl border-slate-200 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Semua status</option>
                            @foreach ($statuses as $value => $label)
                                <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="severity" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Severity
                        </label>
                        <select
                            id="severity"
                            name="severity"
                            class="w-full rounded-xl border-slate-200 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Semua severity</option>
                            @foreach ($severities as $value => $label)
                                <option value="{{ $value }}" @selected(($filters['severity'] ?? '') === $value)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="type" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Type
                        </label>
                        <select
                            id="type"
                            name="type"
                            class="w-full rounded-xl border-slate-200 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Semua type</option>
                            @foreach ($types as $value => $label)
                                <option value="{{ $value }}" @selected(($filters['type'] ?? '') === $value)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <button type="submit"
                            class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700">
                        <i data-lucide="filter" class="h-4 w-4"></i>
                        Terapkan Filter
                    </button>

                    <a href="{{ route('backup-alerts.index') }}"
                       class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                        <i data-lucide="rotate-ccw" class="h-4 w-4"></i>
                        Reset
                    </a>
                </div>
            </form>
        </section>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-5 py-4">
                <h2 class="text-base font-bold text-slate-900">
                    Riwayat Alert
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    Alert yang dibuat dari hasil pengecekan log backup, pending job, dan status storage.
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-5 py-3">Waktu</th>
                            <th class="px-5 py-3">Alert</th>
                            <th class="px-5 py-3">Relasi</th>
                            <th class="px-5 py-3">Severity</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse ($alerts as $alert)
                            <tr class="align-top">
                                <td class="whitespace-nowrap px-5 py-4 text-slate-600">
                                    {{ $alert->triggeredAtLabel() }}
                                </td>

                                <td class="max-w-md px-5 py-4">
                                    <div class="font-bold text-slate-900">
                                        {{ $alert->title }}
                                    </div>

                                    <div class="mt-1 text-xs font-semibold uppercase tracking-wide text-slate-400">
                                        {{ $alert->typeLabel() }}
                                    </div>

                                    @if ($alert->message)
                                        <div class="mt-2 line-clamp-2 text-sm text-slate-500">
                                            {{ $alert->message }}
                                        </div>
                                    @endif
                                </td>

                                <td class="px-5 py-4 text-slate-600">
                                    <div class="space-y-1">
                                        @if ($alert->job)
                                            <div class="flex items-center gap-2">
                                                <i data-lucide="workflow" class="h-4 w-4 text-slate-400"></i>
                                                <span>{{ $alert->job->name }}</span>
                                            </div>
                                        @endif

                                        @if ($alert->system)
                                            <div class="flex items-center gap-2">
                                                <i data-lucide="server" class="h-4 w-4 text-slate-400"></i>
                                                <span>{{ $alert->system->name }}</span>
                                            </div>
                                        @endif

                                        @if ($alert->storage)
                                            <div class="flex items-center gap-2">
                                                <i data-lucide="hard-drive" class="h-4 w-4 text-slate-400"></i>
                                                <span>{{ $alert->storage->name }}</span>
                                            </div>
                                        @endif

                                        @if (! $alert->job && ! $alert->system && ! $alert->storage)
                                            <span class="text-slate-400">-</span>
                                        @endif
                                    </div>
                                </td>

                                <td class="whitespace-nowrap px-5 py-4">
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold ring-1 {{ $alert->severityBadgeClass() }}">
                                        {{ $alert->severityLabel() }}
                                    </span>
                                </td>

                                <td class="whitespace-nowrap px-5 py-4">
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold ring-1 {{ $alert->statusBadgeClass() }}">
                                        {{ $alert->statusLabel() }}
                                    </span>
                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-right">
                                    @if (! in_array($alert->status, [\App\Models\BackupAlert::STATUS_RESOLVED, \App\Models\BackupAlert::STATUS_IGNORED], true))
                                        <div class="flex justify-end gap-2">
                                            <form method="POST" action="{{ route('backup-alerts.resolve', $alert) }}">
                                                @csrf
                                                @method('PATCH')

                                                <button type="submit"
                                                        class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-emerald-700">
                                                    <i data-lucide="circle-check" class="h-4 w-4"></i>
                                                    Resolve
                                                </button>
                                            </form>

                                            <form method="POST" action="{{ route('backup-alerts.ignore', $alert) }}">
                                                @csrf
                                                @method('PATCH')

                                                <button type="submit"
                                                        class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-50">
                                                    <i data-lucide="archive-x" class="h-4 w-4"></i>
                                                    Ignore
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-xs font-semibold text-slate-400">
                                            Tidak ada aksi
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-12 text-center">
                                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                                        <i data-lucide="bell-off" class="h-7 w-7"></i>
                                    </div>
                                    <div class="mt-4 text-base font-bold text-slate-800">
                                        Belum ada alert
                                    </div>
                                    <div class="mt-1 text-sm text-slate-500">
                                        Alert akan muncul setelah command pengecekan alert dibuat dan dijalankan.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($alerts->hasPages())
                <div class="border-t border-slate-200 px-5 py-4">
                    {{ $alerts->links() }}
                </div>
            @endif
        </section>
    </div>
@endsection