<header class="sticky top-0 z-20 border-b border-slate-200 bg-white/90 px-6 py-4 backdrop-blur">
    <div class="flex items-center justify-between gap-4">
        <div class="min-w-0">
            <h1 class="truncate text-xl font-bold text-slate-900">
                @yield('pageTitle', $pageTitle ?? 'Dashboard')
            </h1>

            <p class="mt-0.5 truncate text-sm text-slate-500">
                @yield('pageSubtitle', $pageSubtitle ?? 'Monitoring hasil backup otomatis.')
            </p>
        </div>

        <div class="flex items-center gap-4">
            <div class="hidden text-right sm:block">
                <div class="text-sm font-semibold text-slate-900">
                    {{ auth()->user()->name }}
                </div>
                <div class="text-xs text-slate-500">
                    Administrator
                </div>
            </div>

            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 text-sm font-bold text-slate-700">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-50 hover:text-slate-900">
                    <i data-lucide="log-out" class="h-4 w-4"></i>
                    Logout
                </button>
            </form>
        </div>
    </div>
</header>