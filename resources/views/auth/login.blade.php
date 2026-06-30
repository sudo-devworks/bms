<x-guest-layout>
    <div class="relative min-h-screen overflow-hidden bg-slate-950">
        {{-- Background glow --}}
        <div class="absolute inset-0">
            <div class="absolute -left-32 top-10 h-80 w-80 rounded-full bg-cyan-500/20 blur-3xl"></div>
            <div class="absolute right-0 top-32 h-96 w-96 rounded-full bg-blue-600/20 blur-3xl"></div>
            <div class="absolute bottom-0 left-1/3 h-72 w-72 rounded-full bg-emerald-500/10 blur-3xl"></div>
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(14,165,233,0.18),transparent_32%),linear-gradient(to_bottom,rgba(15,23,42,0.5),rgba(2,6,23,1))]"></div>
        </div>

        {{-- Grid pattern --}}
        <div class="absolute inset-0 opacity-[0.08]"
             style="background-image: linear-gradient(rgba(255,255,255,.18) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.18) 1px, transparent 1px); background-size: 42px 42px;">
        </div>

        <main class="relative z-10 flex min-h-screen items-center justify-center px-4 py-8 sm:px-6 lg:px-8">
            <div class="grid w-full max-w-7xl items-center gap-10 lg:grid-cols-2">

                {{-- Left panel --}}
                <section class="hidden lg:block">
                    <div class="max-w-xl">
                        <div class="mb-8 flex items-center gap-5">
                            <div class="flex h-20 w-20 items-center justify-center rounded-3xl border border-cyan-400/30 bg-slate-900/80 shadow-2xl shadow-cyan-500/10">
                                <img
                                    src="{{ asset('images/iteam.png') }}"
                                    alt="IT Team Logo"
                                    class="max-h-14 max-w-16 object-contain"
                                >
                            </div>

                            <div>
                                <div class="text-6xl font-extrabold tracking-tight text-white">
                                    BMS
                                </div>
                                <div class="mt-1 text-sm font-bold uppercase tracking-[0.28em] text-cyan-300">
                                    Backup Monitoring System
                                </div>
                            </div>
                        </div>

                        <div class="mb-6 h-1 w-16 rounded-full bg-cyan-400"></div>

                        <h1 class="text-4xl font-extrabold leading-tight tracking-tight text-white">
                            Monitoring backup internal yang aman, rapi, dan terpusat.
                        </h1>

                        <p class="mt-5 text-base leading-8 text-slate-300">
                            BMS adalah platform monitoring-only untuk melihat status backup,
                            kesehatan storage, laporan audit, dan alert notifikasi tanpa menjalankan
                            proses backup langsung dari dashboard.
                        </p>

                        {{-- Mock dashboard card --}}
                        <div class="mt-10 rounded-3xl border border-white/10 bg-white/[0.06] p-5 shadow-2xl shadow-cyan-950/40 backdrop-blur">
                            <div class="mb-5 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-cyan-400/10 text-cyan-300">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 13h8V3H3v10Zm10 8h8V3h-8v18ZM3 21h8v-6H3v6Z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-white">Operations Snapshot</div>
                                        <div class="text-xs text-slate-400">Static login preview</div>
                                    </div>
                                </div>

                                <span class="inline-flex items-center gap-2 rounded-full border border-emerald-400/20 bg-emerald-400/10 px-3 py-1 text-xs font-semibold text-emerald-300">
                                    <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                                    Online
                                </span>
                            </div>

                            <div class="grid grid-cols-3 gap-3">
                                <div class="rounded-2xl border border-white/10 bg-slate-950/50 p-4">
                                    <div class="mb-3 flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-400/10 text-emerald-300">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5 13 4 4L19 7" />
                                        </svg>
                                    </div>
                                    <div class="text-2xl font-bold text-white">OK</div>
                                    <div class="mt-1 text-xs text-slate-400">Backup Jobs</div>
                                </div>

                                <div class="rounded-2xl border border-white/10 bg-slate-950/50 p-4">
                                    <div class="mb-3 flex h-9 w-9 items-center justify-center rounded-xl bg-cyan-400/10 text-cyan-300">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7c0-1.657 3.582-3 8-3s8 1.343 8 3-3.582 3-8 3-8-1.343-8-3Zm0 0v10c0 1.657 3.582 3 8 3s8-1.343 8-3V7" />
                                        </svg>
                                    </div>
                                    <div class="text-2xl font-bold text-white">72%</div>
                                    <div class="mt-1 text-xs text-slate-400">Storage Used</div>
                                </div>

                                <div class="rounded-2xl border border-white/10 bg-slate-950/50 p-4">
                                    <div class="mb-3 flex h-9 w-9 items-center justify-center rounded-xl bg-amber-400/10 text-amber-300">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0 1 18 14.158V11a6 6 0 1 0-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0a3 3 0 1 1-6 0m6 0H9" />
                                        </svg>
                                    </div>
                                    <div class="text-2xl font-bold text-white">Alert</div>
                                    <div class="mt-1 text-xs text-slate-400">Checker Active</div>
                                </div>
                            </div>

                            <div class="mt-5 rounded-2xl border border-cyan-400/10 bg-cyan-400/5 p-4">
                                <div class="mb-2 flex items-center justify-between text-xs">
                                    <span class="font-semibold text-cyan-200">Storage Watch</span>
                                    <span class="text-slate-400">Healthy</span>
                                </div>
                                <div class="h-2 overflow-hidden rounded-full bg-slate-800">
                                    <div class="h-full w-3/4 rounded-full bg-cyan-400"></div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 grid grid-cols-3 gap-4 text-sm">
                            <div class="flex items-start gap-3">
                                <span class="mt-1 h-2 w-2 rounded-full bg-emerald-400"></span>
                                <div>
                                    <div class="font-semibold text-white">API Receiver</div>
                                    <div class="text-xs text-slate-400">Log backup masuk via API.</div>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <span class="mt-1 h-2 w-2 rounded-full bg-cyan-400"></span>
                                <div>
                                    <div class="font-semibold text-white">Storage Watch</div>
                                    <div class="text-xs text-slate-400">Pantau kapasitas storage.</div>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <span class="mt-1 h-2 w-2 rounded-full bg-amber-400"></span>
                                <div>
                                    <div class="font-semibold text-white">Alert Checker</div>
                                    <div class="text-xs text-slate-400">Deteksi backup bermasalah.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- Right login card --}}
                <section class="mx-auto w-full max-w-md">
                    <div class="mb-8 flex justify-center lg:hidden">
                        <div class="flex items-center gap-4">
                            <div class="flex h-16 w-16 items-center justify-center rounded-2xl border border-cyan-400/30 bg-slate-900/80 shadow-xl shadow-cyan-500/10">
                                <img
                                    src="{{ asset('images/iteam.png') }}"
                                    alt="IT Team Logo"
                                    class="max-h-11 max-w-12 object-contain"
                                >
                            </div>
                            <div>
                                <div class="text-4xl font-extrabold tracking-tight text-white">BMS</div>
                                <div class="text-xs font-bold uppercase tracking-[0.22em] text-cyan-300">
                                    Backup Monitoring
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-3xl border border-white/10 bg-slate-900/80 p-6 shadow-2xl shadow-cyan-950/50 backdrop-blur sm:p-8">
                        <div class="mb-8 text-center">
                            <div class="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-2xl border border-cyan-400/30 bg-cyan-400/10 text-cyan-300">
                                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c1.657 0 3-1.343 3-3S13.657 5 12 5 9 6.343 9 8s1.343 3 3 3Zm0 2c-2.761 0-5 1.79-5 4v1h10v-1c0-2.21-2.239-4-5-4Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z" />
                                </svg>
                            </div>

                            <h2 class="text-2xl font-bold tracking-tight text-white">
                                Administrator Login
                            </h2>
                            <p class="mt-2 text-sm text-slate-400">
                                Masuk untuk mengakses dashboard BMS.
                            </p>
                        </div>

                        <x-auth-session-status class="mb-4 rounded-2xl border border-emerald-400/20 bg-emerald-400/10 px-4 py-3 text-sm text-emerald-200" :status="session('status')" />

                        <form method="POST" action="{{ route('login') }}" class="space-y-5">
                            @csrf

                            <div>
                                <label for="email" class="mb-2 block text-sm font-semibold text-slate-200">
                                    Email Address
                                </label>

                                <div class="relative">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-500">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 0 0 2.22 0L21 8m-18 8h18a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2H3a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2Z" />
                                        </svg>
                                    </div>

                                    <input
                                        id="email"
                                        type="email"
                                        name="email"
                                        value="{{ old('email') }}"
                                        required
                                        autofocus
                                        autocomplete="username"
                                        placeholder="admin@company.com"
                                        class="block w-full rounded-2xl border border-slate-700 bg-slate-950/60 py-3 pl-12 pr-4 text-sm text-white placeholder:text-slate-500 shadow-sm outline-none transition focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/20"
                                    >
                                </div>

                                <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-300" />
                            </div>

                            <div>
                                <label for="password" class="mb-2 block text-sm font-semibold text-slate-200">
                                    Password
                                </label>

                                <div class="relative">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-500">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11v4m-4-4V8a4 4 0 1 1 8 0v3m-9 0h10a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2Z" />
                                        </svg>
                                    </div>

                                    <input
                                        id="password"
                                        type="password"
                                        name="password"
                                        required
                                        autocomplete="current-password"
                                        placeholder="Enter your password"
                                        class="block w-full rounded-2xl border border-slate-700 bg-slate-950/60 py-3 pl-12 pr-4 text-sm text-white placeholder:text-slate-500 shadow-sm outline-none transition focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/20"
                                    >
                                </div>

                                <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-300" />
                            </div>

                            <div class="flex items-center justify-between gap-4">
                                <label for="remember_me" class="inline-flex items-center">
                                    <input
                                        id="remember_me"
                                        type="checkbox"
                                        class="rounded border-slate-600 bg-slate-950 text-cyan-500 shadow-sm focus:ring-cyan-500 focus:ring-offset-slate-900"
                                        name="remember"
                                    >
                                    <span class="ms-2 text-sm text-slate-300">Remember me</span>
                                </label>

                                @if (Route::has('password.request'))
                                    <a
                                        class="text-sm font-semibold text-cyan-300 transition hover:text-cyan-200"
                                        href="{{ route('password.request') }}"
                                    >
                                        Forgot password?
                                    </a>
                                @endif
                            </div>

                            <button
                                type="submit"
                                class="group flex w-full items-center justify-center gap-2 rounded-2xl bg-cyan-500 px-5 py-3 text-sm font-bold text-slate-950 shadow-lg shadow-cyan-500/20 transition hover:bg-cyan-400 focus:outline-none focus:ring-2 focus:ring-cyan-300 focus:ring-offset-2 focus:ring-offset-slate-900"
                            >
                                <svg class="h-5 w-5 transition group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H3m12 0-4-4m4 4-4 4m5-12h3a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-3" />
                                </svg>
                                Log In
                            </button>
                        </form>

                        <div class="mt-8 rounded-2xl border border-cyan-400/10 bg-cyan-400/5 p-4">
                            <div class="flex gap-3">
                                <div class="mt-0.5 text-cyan-300">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3 4 6v6c0 5 3.5 8 8 9 4.5-1 8-4 8-9V6l-8-3Z" />
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-slate-200">
                                        Internal monitoring system
                                    </div>
                                    <div class="mt-1 text-xs leading-5 text-slate-400">
                                        BMS hanya memonitor status backup, reporting, storage health,
                                        dan alert. Unauthorized access is prohibited.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <p class="mt-6 text-center text-xs text-slate-500">
                        © {{ date('Y') }} Backup Monitoring System. IT Team.
                    </p>
                </section>
            </div>
        </main>
    </div>
</x-guest-layout>