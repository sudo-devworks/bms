<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', $title ?? 'Backup Monitoring System')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-100 text-slate-900 antialiased">
    <div class="min-h-screen md:flex">
        @include('layouts.partials.sidebar')

        <div class="min-w-0 flex-1">
            @include('layouts.partials.topbar')

            <main class="p-4 sm:p-6 lg:p-8">
                @hasSection('content')
                    @yield('content')
                @else
                    {{ $slot ?? '' }}
                @endif
            </main>
        </div>
    </div>


    <div id="bmsDeleteConfirmModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 p-4">
        <div class="w-full max-w-md overflow-hidden rounded-2xl bg-white shadow-2xl ring-1 ring-slate-900/10">
            <div class="border-b border-slate-100 p-6">
                <div class="flex items-start gap-4">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-red-50 text-red-600">
                        <i data-lucide="trash-2" class="h-5 w-5"></i>
                    </div>
                    <div>
                        <h2 id="bmsDeleteConfirmTitle" class="text-lg font-bold text-slate-900">Konfirmasi Hapus</h2>
                        <p id="bmsDeleteConfirmMessage" class="mt-2 text-sm leading-6 text-slate-600">
                            Data yang dihapus tidak dapat dikembalikan.
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-slate-50 px-6 py-4">
                <p class="flex items-start gap-2 text-xs leading-5 text-slate-500">
                    <i data-lucide="info" class="mt-0.5 h-4 w-4 shrink-0"></i>
                    Pastikan data yang dihapus memang salah input atau sudah tidak diperlukan untuk monitoring.
                </p>
            </div>

            <div class="flex flex-col-reverse gap-3 p-6 sm:flex-row sm:justify-end">
                <button type="button" id="bmsDeleteConfirmCancel" class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    <i data-lucide="x" class="h-4 w-4"></i>
                    Batal
                </button>
                <button type="button" id="bmsDeleteConfirmSubmit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-red-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-red-700">
                    <i data-lucide="trash-2" class="h-4 w-4"></i>
                    Ya, Hapus
                </button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modal = document.getElementById('bmsDeleteConfirmModal');
            const title = document.getElementById('bmsDeleteConfirmTitle');
            const message = document.getElementById('bmsDeleteConfirmMessage');
            const cancelButton = document.getElementById('bmsDeleteConfirmCancel');
            const submitButton = document.getElementById('bmsDeleteConfirmSubmit');
            let activeForm = null;

            function openDeleteModal(form) {
                activeForm = form;
                title.textContent = form.dataset.confirmTitle || 'Konfirmasi Hapus';
                message.textContent = form.dataset.confirmMessage || 'Data yang dihapus tidak dapat dikembalikan.';
                submitButton.lastChild.textContent = ' ' + (form.dataset.confirmButton || 'Ya, Hapus');
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.body.classList.add('overflow-hidden');

                if (window.lucide) {
                    window.lucide.createIcons();
                }
            }

            function closeDeleteModal() {
                activeForm = null;
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.classList.remove('overflow-hidden');
            }

            document.querySelectorAll('[data-confirm-delete]').forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    event.preventDefault();
                    openDeleteModal(form);
                });
            });

            cancelButton.addEventListener('click', closeDeleteModal);

            modal.addEventListener('click', function (event) {
                if (event.target === modal) {
                    closeDeleteModal();
                }
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
                    closeDeleteModal();
                }
            });

            submitButton.addEventListener('click', function () {
                if (!activeForm) {
                    return;
                }

                submitButton.disabled = true;
                submitButton.classList.add('cursor-not-allowed', 'opacity-75');
                activeForm.submit();
            });
        });
    </script>

    @stack('scripts')
</body>
</html>