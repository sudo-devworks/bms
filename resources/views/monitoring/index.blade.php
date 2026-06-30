<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Backup Monitoring Display</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <meta http-equiv="refresh" content="60">
</head>
<body class="min-h-screen bg-gradient-to-br from-sky-50 via-white to-emerald-50 text-slate-900">
    <div class="min-h-screen px-6 py-6 lg:px-10">
        <header class="mb-6 overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-xl shadow-slate-200/60">
            <div class="relative p-6 lg:p-7">
                <div class="absolute inset-x-0 top-0 h-2 bg-gradient-to-r from-sky-500 via-emerald-500 to-amber-400"></div>

                <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex items-center gap-4">
                        <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-sky-500 to-blue-600 text-white shadow-lg shadow-sky-200">
                            <i data-lucide="database-backup" class="h-9 w-9"></i>
                        </div>

                        <div>
                            <div class="text-sm font-bold uppercase tracking-[0.35em] text-sky-700">
                                Backup Monitoring System
                            </div>
                            <h1 class="mt-1 text-3xl font-black tracking-tight text-slate-950 lg:text-4xl">
                                Public Monitoring Display
                            </h1>
                            <p class="mt-1 text-sm text-slate-500">
                                Monitoring-only dashboard. Tidak menjalankan backup, tidak scan folder, dan tidak menampilkan data sensitif.
                            </p>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-slate-50 px-5 py-4 text-left lg:text-right">
                        <div class="text-sm font-semibold text-slate-500">
                            Last refreshed
                        </div>
                        <div class="text-2xl font-black text-slate-950">
                            {{ $lastRefreshedAt->format('d M Y H:i:s') }}
                        </div>
                        <div class="text-sm text-slate-500">
                            Auto refresh setiap 60 detik
                        </div>
                    </div>

                    <div class="mt-3 rounded-xl bg-white px-3 py-2 text-left ring-1 ring-slate-200">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <div class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                    Alert checker
                                </div>
                                <div class="mt-1 text-sm font-black text-slate-800">
                                    {{ $alertCheckerMonitor?->lastRunLabel() ?? 'Belum pernah jalan' }}
                                </div>
                            </div>

                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold ring-1 {{ $alertCheckerMonitor?->statusBadgeClass() ?? 'bg-slate-50 text-slate-700 ring-slate-200' }}">
                                {{ $alertCheckerMonitor?->statusLabel() ?? 'Unknown' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        @if ($hasProblem)
            <section class="mb-6 overflow-hidden rounded-3xl border border-rose-200 bg-white shadow-xl shadow-rose-100/70">
                <div class="flex flex-col gap-4 border-l-8 border-rose-500 p-5 lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex items-center gap-4">
                        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-rose-100 text-rose-700">
                            <i data-lucide="triangle-alert" class="h-8 w-8"></i>
                        </div>

                        <div>
                            <div class="text-xl font-black text-rose-800">
                                Perhatian: Ada kondisi backup/storage yang perlu dicek
                            </div>
                            <div class="mt-1 text-sm font-medium text-rose-600">
                                Failed: {{ $summary['failed'] }},
                                Warning: {{ $summary['warning'] }},
                                Pending: {{ $summary['pending'] }},
                                Storage Kritis: {{ $storageSummary['critical'] }},
                                Storage Offline: {{ $storageSummary['offline'] }},
                                Active Alert: {{ $activeAlertSummary['total'] }}
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl bg-rose-50 px-4 py-3 text-sm font-bold text-rose-700 ring-1 ring-rose-200">
                        Butuh pengecekan
                    </div>
                </div>
            </section>
        @else
            <section class="mb-6 overflow-hidden rounded-3xl border border-emerald-200 bg-white shadow-xl shadow-emerald-100/70">
                <div class="flex flex-col gap-4 border-l-8 border-emerald-500 p-5 lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex items-center gap-4">
                        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-700">
                            <i data-lucide="circle-check-big" class="h-8 w-8"></i>
                        </div>

                        <div>
                            <div class="text-xl font-black text-emerald-800">
                                Kondisi backup hari ini aman
                            </div>
                            <div class="mt-1 text-sm font-medium text-emerald-600">
                                Tidak ada failed, warning, pending, storage kritis, atau storage offline.
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-700 ring-1 ring-emerald-200">
                        Monitoring normal
                    </div>
                </div>
            </section>
        @endif

        <section class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-6">
            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-lg shadow-slate-200/70">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-bold text-slate-500">Total Job</span>
                    <div class="rounded-xl bg-slate-100 p-2 text-slate-600">
                        <i data-lucide="workflow" class="h-5 w-5"></i>
                    </div>
                </div>
                <div class="mt-3 text-4xl font-black text-slate-950">{{ $summary['total_jobs'] }}</div>
                <div class="mt-1 text-xs font-medium text-slate-400">Job aktif dipantau</div>
            </div>

            <div class="rounded-3xl border border-emerald-200 bg-emerald-50 p-5 shadow-lg shadow-emerald-100/70">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-bold text-emerald-700">Success</span>
                    <div class="rounded-xl bg-emerald-100 p-2 text-emerald-700">
                        <i data-lucide="circle-check" class="h-5 w-5"></i>
                    </div>
                </div>
                <div class="mt-3 text-4xl font-black text-emerald-800">{{ $summary['success'] }}</div>
                <div class="mt-1 text-xs font-medium text-emerald-600">Backup berhasil</div>
            </div>

            <div class="rounded-3xl border border-amber-200 bg-amber-50 p-5 shadow-lg shadow-amber-100/70">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-bold text-amber-700">Warning</span>
                    <div class="rounded-xl bg-amber-100 p-2 text-amber-700">
                        <i data-lucide="circle-alert" class="h-5 w-5"></i>
                    </div>
                </div>
                <div class="mt-3 text-4xl font-black text-amber-800">{{ $summary['warning'] }}</div>
                <div class="mt-1 text-xs font-medium text-amber-600">Perlu perhatian</div>
            </div>

            <div class="rounded-3xl border border-rose-200 bg-rose-50 p-5 shadow-lg shadow-rose-100/70">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-bold text-rose-700">Failed</span>
                    <div class="rounded-xl bg-rose-100 p-2 text-rose-700">
                        <i data-lucide="circle-x" class="h-5 w-5"></i>
                    </div>
                </div>
                <div class="mt-3 text-4xl font-black text-rose-800">{{ $summary['failed'] }}</div>
                <div class="mt-1 text-xs font-medium text-rose-600">Backup gagal</div>
            </div>

            <div class="rounded-3xl border border-sky-200 bg-sky-50 p-5 shadow-lg shadow-sky-100/70">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-bold text-sky-700">Pending</span>
                    <div class="rounded-xl bg-sky-100 p-2 text-sky-700">
                        <i data-lucide="clock" class="h-5 w-5"></i>
                    </div>
                </div>
                <div class="mt-3 text-4xl font-black text-sky-800">{{ $summary['pending'] }}</div>
                <div class="mt-1 text-xs font-medium text-sky-600">Belum ada log hari ini</div>
            </div>

            <div class="rounded-3xl border border-fuchsia-200 bg-fuchsia-50 p-5 shadow-lg shadow-fuchsia-100/70">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-bold text-fuchsia-700">Active Alerts</span>
                    <div class="rounded-xl bg-fuchsia-100 p-2 text-fuchsia-700">
                        <i data-lucide="bell-ring" class="h-5 w-5"></i>
                    </div>
                </div>
                <div class="mt-3 text-4xl font-black text-fuchsia-800">{{ $activeAlertSummary['total'] }}</div>
                <div class="mt-1 text-xs font-medium text-fuchsia-600">
                    {{ $activeAlertSummary['critical'] }} critical /
                    {{ $activeAlertSummary['warning'] }} warning
                </div>
            </div>
        </section>

        <main class="grid gap-6 xl:grid-cols-3">
            <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-xl shadow-slate-200/70 xl:col-span-2">
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-black text-slate-950">Status Job Hari Ini</h2>
                        <p class="text-sm font-medium text-slate-500">{{ $today->format('d M Y') }}</p>
                    </div>

                    <div class="rounded-full bg-slate-100 px-4 py-2 text-sm font-bold text-slate-600">
                        {{ $jobBoards->count() }} job aktif
                    </div>
                </div>

                <div class="grid gap-3 lg:grid-cols-2">
                    @forelse ($jobBoards as $item)
                        @php
                            $statusClass = match ($item['status']) {
                                'success' => 'border-emerald-200 bg-emerald-50 text-emerald-800',
                                'warning' => 'border-amber-200 bg-amber-50 text-amber-800',
                                'failed' => 'border-rose-200 bg-rose-50 text-rose-800',
                                default => 'border-sky-200 bg-sky-50 text-sky-800',
                            };

                            $icon = match ($item['status']) {
                                'success' => 'circle-check',
                                'warning' => 'circle-alert',
                                'failed' => 'circle-x',
                                default => 'clock',
                            };
                        @endphp

                        <div class="rounded-2xl border p-4 {{ $statusClass }}">
                            <div class="flex items-start justify-between gap-4">
                                <div class="min-w-0">
                                    <div class="truncate text-base font-black">
                                        {{ $item['job']->name }}
                                    </div>
                                    <div class="mt-1 truncate text-xs opacity-75">
                                        {{ $item['system']->name ?? 'Tanpa sistem' }}
                                    </div>
                                </div>

                                <div class="flex shrink-0 items-center gap-2 rounded-full bg-white/70 px-3 py-1 text-xs font-black uppercase tracking-wide shadow-sm ring-1 ring-black/5">
                                    <i data-lucide="{{ $icon }}" class="h-4 w-4"></i>
                                    {{ $item['status_label'] }}
                                </div>
                            </div>

                            <div class="mt-4 flex items-center justify-between gap-3 text-sm opacity-80">
                                <span>Update terakhir</span>
                                <span class="font-bold">{{ $item['time_label'] }}</span>
                            </div>

                            <div class="mt-2 line-clamp-2 text-xs opacity-70">
                                {{ $item['message'] }}
                            </div>
                        </div>
                    @empty
                        <div class="rounded-2xl border border-white/10 bg-white/5 p-6 text-center text-slate-400 lg:col-span-2">
                            Belum ada job aktif.
                        </div>
                    @endforelse
                </div>
            </section>

            <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-xl shadow-slate-200/70">
                <div class="mb-4">
                    <h2 class="text-xl font-black text-slate-950">Storage Health</h2>
                    <p class="text-sm font-medium text-slate-500">{{ $storageSummary['total'] }} storage aktif</p>
                </div>

                <div class="space-y-3">
                    @forelse ($storages as $storage)
                        @php
                            $health = $storage->healthLabel();

                            $storageClass = match ($health) {
                                'Sehat' => 'border-emerald-200 bg-emerald-50 text-emerald-800',
                                'Perhatian' => 'border-amber-200 bg-amber-50 text-amber-800',
                                'Kritis', 'Offline' => 'border-rose-200 bg-rose-50 text-rose-800',
                                default => 'border-slate-200 bg-slate-50 text-slate-800',
                            };
                        @endphp

                        <div class="rounded-2xl border p-4 {{ $storageClass }}">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="truncate font-black">{{ $storage->name }}</div>
                                    <div class="mt-1 text-xs opacity-75">
                                        {{ $storage->lastCheckedLabel() }}
                                    </div>
                                </div>

                                <div class="rounded-full bg-white/70 px-3 py-1 text-xs font-black shadow-sm ring-1 ring-black/5">
                                    {{ $health }}
                                </div>
                            </div>

                            <div class="mt-4">
                                <div class="mb-1 flex items-center justify-between text-xs opacity-80">
                                    <span>Usage</span>
                                    <span>{{ $storage->usagePercentLabel() }}</span>
                                </div>

                                <div class="h-2 overflow-hidden rounded-full bg-white/80 ring-1 ring-black/5">
                                    <div
                                        class="h-full rounded-full bg-current"
                                        style="width: {{ min((float) ($storage->usage_percent ?? 0), 100) }}%">
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-2xl border border-white/10 bg-white/5 p-6 text-center text-slate-400">
                            Belum ada storage aktif.
                        </div>
                    @endforelse
                </div>
            </section>

            <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-xl shadow-slate-200/70 xl:col-span-3">
                <div class="mb-4 flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h2 class="text-xl font-black text-slate-950">Active Alerts</h2>
                        <p class="text-sm font-medium text-slate-500">
                            Alert yang masih aktif dan belum ditandai resolved atau ignored.
                        </p>
                    </div>

                    <div class="rounded-full bg-fuchsia-50 px-4 py-2 text-sm font-black text-fuchsia-700 ring-1 ring-fuchsia-200">
                        {{ $activeAlertSummary['total'] }} alert aktif
                    </div>
                </div>

                <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-3">
                    @forelse ($activeAlerts as $alert)
                        @php
                            $alertClass = match ($alert->severity) {
                                'critical' => 'border-rose-200 bg-rose-50 text-rose-800',
                                'warning' => 'border-amber-200 bg-amber-50 text-amber-800',
                                default => 'border-blue-200 bg-blue-50 text-blue-800',
                            };

                            $alertIcon = match ($alert->severity) {
                                'critical' => 'triangle-alert',
                                'warning' => 'circle-alert',
                                default => 'bell',
                            };
                        @endphp

                        <div class="rounded-2xl border p-4 {{ $alertClass }}">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <i data-lucide="{{ $alertIcon }}" class="h-5 w-5 shrink-0"></i>
                                        <div class="truncate text-base font-black">
                                            {{ $alert->title }}
                                        </div>
                                    </div>

                                    <div class="mt-1 text-xs font-bold uppercase tracking-wide opacity-70">
                                        {{ $alert->typeLabel() }} • {{ $alert->statusLabel() }}
                                    </div>
                                </div>

                                <div class="shrink-0 rounded-full bg-white/70 px-3 py-1 text-xs font-black shadow-sm ring-1 ring-black/5">
                                    {{ $alert->severityLabel() }}
                                </div>
                            </div>

                            <div class="mt-4 space-y-1 text-sm opacity-85">
                                @if ($alert->job)
                                    <div class="flex items-center gap-2">
                                        <i data-lucide="workflow" class="h-4 w-4"></i>
                                        <span class="truncate">{{ $alert->job->name }}</span>
                                    </div>
                                @endif

                                @if ($alert->system)
                                    <div class="flex items-center gap-2">
                                        <i data-lucide="server" class="h-4 w-4"></i>
                                        <span class="truncate">{{ $alert->system->name }}</span>
                                    </div>
                                @endif

                                @if ($alert->storage)
                                    <div class="flex items-center gap-2">
                                        <i data-lucide="hard-drive" class="h-4 w-4"></i>
                                        <span class="truncate">{{ $alert->storage->name }}</span>
                                    </div>
                                @endif
                            </div>

                            @if ($alert->triggered_at)
                                <div class="mt-4 rounded-xl bg-white/60 px-3 py-2 text-xs font-bold ring-1 ring-black/5">
                                    Triggered: {{ $alert->triggered_at->format('d M Y H:i') }}
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-6 text-center text-emerald-700 md:col-span-2 xl:col-span-3">
                            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-100">
                                <i data-lucide="bell-off" class="h-6 w-6"></i>
                            </div>
                            <div class="mt-3 text-base font-black">
                                Tidak ada active alert
                            </div>
                            <div class="mt-1 text-sm font-medium text-emerald-600">
                                Semua alert sudah resolved/ignored atau belum ada alert baru.
                            </div>
                        </div>
                    @endforelse
                </div>
            </section>

            <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-xl shadow-slate-200/70 xl:col-span-3">
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-black text-slate-950">Aktivitas Backup Terbaru</h2>
                        <p class="text-sm font-medium text-slate-500">Menampilkan log terbaru tanpa detail path sensitif.</p>
                    </div>
                </div>

                <div class="overflow-hidden rounded-2xl border border-slate-200">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500">
                            <tr>
                                <th class="px-4 py-3">Waktu</th>
                                <th class="px-4 py-3">Job</th>
                                <th class="px-4 py-3">Sistem</th>
                                <th class="px-4 py-3">Storage</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3">Ukuran</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse ($latestActivities as $log)
                                @php
                                    $logStatusClass = match ($log->status) {
                                        'success' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
                                        'warning' => 'bg-amber-50 text-amber-700 ring-amber-200',
                                        'failed' => 'bg-rose-50 text-rose-700 ring-rose-200',
                                        default => 'bg-slate-50 text-slate-700 ring-slate-200',
                                    };
                                @endphp

                                <tr class="text-slate-700">
                                    <td class="whitespace-nowrap px-4 py-3">
                                        {{ optional($log->finished_at ?? $log->created_at)->format('d M H:i') }}
                                    </td>
                                    <td class="px-4 py-3 font-semibold">
                                        {{ $log->job->name ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        {{ $log->system->name ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        {{ $log->storage->name ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold ring-1 {{ $logStatusClass }}">
                                            {{ $log->statusLabel() }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3">
                                        {{ $log->fileSizeLabel() }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-slate-400">
                                        Belum ada aktivitas backup.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (window.lucide) {
                window.lucide.createIcons();
            }
        });
    </script>
</body>
</html>