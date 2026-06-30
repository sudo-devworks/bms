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
            <section class="w-full max-w-md">
                <div class="mb-8 flex justify-center">
                    <div class="flex items-center gap-4">
                        <div class="flex h-16 w-16 items-center justify-center rounded-2xl border border-cyan-400/30 bg-slate-900/80 shadow-xl shadow-cyan-500/10">
                            <img
                                src="{{ asset('images/iteam.png') }}"
                                alt="IT Team Logo"
                                class="max-h-11 max-w-12 object-contain"
                            >
                        </div>

                        <div>
                            <div class="text-4xl font-extrabold tracking-tight text-white">
                                BMS
                            </div>
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 0 0 2.22 0L21 8m-18 8h18a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2H3a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2Z" />
                            </svg>
                        </div>

                        <h2 class="text-2xl font-bold tracking-tight text-white">
                            Forgot Password?
                        </h2>

                        <p class="mt-3 text-sm leading-6 text-slate-400">
                            Tidak masalah. Masukkan email akun admin BMS, lalu sistem akan mengirimkan link reset password jika email terdaftar.
                        </p>
                    </div>

                    <x-auth-session-status
                        class="mb-5 rounded-2xl border border-emerald-400/20 bg-emerald-400/10 px-4 py-3 text-sm text-emerald-200"
                        :status="session('status')"
                    />

                    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
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
                                    placeholder="admin@company.com"
                                    class="block w-full rounded-2xl border border-slate-700 bg-slate-950/60 py-3 pl-12 pr-4 text-sm text-white placeholder:text-slate-500 shadow-sm outline-none transition focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/20"
                                >
                            </div>

                            <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-300" />
                        </div>

                        <button
                            type="submit"
                            class="group flex w-full items-center justify-center gap-2 rounded-2xl bg-cyan-500 px-5 py-3 text-sm font-bold text-slate-950 shadow-lg shadow-cyan-500/20 transition hover:bg-cyan-400 focus:outline-none focus:ring-2 focus:ring-cyan-300 focus:ring-offset-2 focus:ring-offset-slate-900"
                        >
                            <svg class="h-5 w-5 transition group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 0 0 2.22 0L21 8m0 0v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8m18 0-9 6-9-6" />
                            </svg>
                            Email Password Reset Link
                        </button>

                        <div class="text-center">
                            <a
                                href="{{ route('login') }}"
                                class="text-sm font-semibold text-cyan-300 transition hover:text-cyan-200"
                            >
                                Back to login
                            </a>
                        </div>
                    </form>
                </div>

                <p class="mt-6 text-center text-xs text-slate-500">
                    © {{ date('Y') }} Backup Monitoring System. IT Team.
                </p>
            </section>
        </main>
    </div>
</x-guest-layout>