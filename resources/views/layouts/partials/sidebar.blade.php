@php
    $menus = [
        [
            'label' => 'Dashboard',
            'route' => 'dashboard',
            'active' => 'dashboard',
            'icon' => 'layout-dashboard',
            'disabled' => false,
        ],
        [
            'label' => 'Storage Backup',
            'route' => 'backup-storages.index',
            'active' => 'backup-storages.*',
            'icon' => 'hard-drive',
            'disabled' => false,
        ],
        [
            'label' => 'Sistem Backup',
            'route' => 'backup-systems.index',
            'active' => 'backup-systems.*',
            'icon' => 'server',
            'disabled' => false,
        ],
        [
            'label' => 'Job Backup',
            'route' => 'backup-jobs.index',
            'active' => 'backup-jobs.*',
            'icon' => 'workflow',
            'disabled' => false,
        ],
        [
            'label' => 'Log Backup',
            'route' => 'backup-logs.index',
            'active' => 'backup-logs.*',
            'icon' => 'clipboard-list',
            'disabled' => false,
        ],
        [
            'label' => 'Backup Reports',
            'route' => 'backup-reports.index',
            'active' => 'backup-reports.*',
            'icon' => 'file-spreadsheet',
            'disabled' => false,
        ],
        [
            'label' => 'Backup Alerts',
            'route' => 'backup-alerts.index',
            'active' => 'backup-alerts.*',
            'icon' => 'bell-ring',
            'disabled' => false,
        ],
        [
            'label' => 'Storage Usage',
            'route' => 'storage-usage.index',
            'active' => 'storage-usage.*',
            'icon' => 'pie-chart',
            'disabled' => false,
        ],
    ];
@endphp

<aside class="hidden w-72 shrink-0 bg-slate-950 text-white md:flex md:flex-col">
    <div class="border-b border-white/10 px-6 py-5">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-600/20 text-blue-300 ring-1 ring-blue-500/20">
                <i data-lucide="database-backup" class="h-5 w-5"></i>
            </div>

            <div>
                <div class="text-base font-bold leading-tight text-white">
                    Backup Monitor
                </div>
                <div class="text-xs text-slate-400">
                    Internal Backup Dashboard
                </div>
            </div>
        </div>
    </div>

    <nav class="flex-1 space-y-1 px-4 py-5 text-sm">
        @foreach ($menus as $menu)
            @php
                $isActive = request()->routeIs($menu['active']);
            @endphp

            @if ($menu['disabled'])
                <div class="flex cursor-not-allowed items-center justify-between rounded-xl px-4 py-3 font-medium text-slate-500">
                    <div class="flex items-center gap-3">
                        <i data-lucide="{{ $menu['icon'] }}" class="h-5 w-5"></i>
                        <span>{{ $menu['label'] }}</span>
                    </div>
                </div>
            @else
                <a href="{{ route($menu['route']) }}"
                   class="flex items-center gap-3 rounded-xl px-4 py-3 font-medium transition
                          {{ $isActive
                              ? 'bg-white/10 text-white shadow-sm ring-1 ring-white/10'
                              : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                    <i data-lucide="{{ $menu['icon'] }}" class="h-5 w-5"></i>
                    <span>{{ $menu['label'] }}</span>
                </a>
            @endif
        @endforeach
    </nav>
</aside>