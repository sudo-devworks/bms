<div class="grid gap-6 lg:grid-cols-2">
    <div>
        <label for="name" class="block text-sm font-semibold text-slate-700">
            Nama Sistem <span class="text-red-500">*</span>
        </label>
        <input type="text"
               id="name"
               name="name"
               value="{{ old('name', $system->name) }}"
               class="mt-2 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
               placeholder="Contoh: Tracer Study">
        @error('name')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="code" class="block text-sm font-semibold text-slate-700">
            Kode Sistem <span class="text-red-500">*</span>
        </label>

        <div class="mt-2 flex rounded-xl shadow-sm">
            <span class="inline-flex items-center rounded-l-xl border border-r-0 border-slate-300 bg-slate-50 px-3 text-sm font-semibold text-slate-500">
                <i data-lucide="hash" class="h-4 w-4"></i>
            </span>
            <input type="text"
                id="code"
                name="code"
                value="{{ old('code', $system->code) }}"
                class="block w-full rounded-r-xl border-slate-300 text-sm uppercase focus:border-blue-500 focus:ring-blue-500"
                placeholder="Contoh: TRACER_STUDY">
        </div>

        <p class="mt-2 text-xs leading-5 text-slate-500">
            Kode akan otomatis distandarkan menjadi huruf kapital dan underscore.
        </p>

        @error('code')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="backup_storage_id" class="block text-sm font-semibold text-slate-700">
            Storage Tujuan <span class="text-red-500">*</span>
        </label>
        <select id="backup_storage_id"
                name="backup_storage_id"
                class="mt-2 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <option value="">Pilih storage tujuan</option>
            @foreach ($storages as $storage)
                <option value="{{ $storage->id }}" @selected(old('backup_storage_id', $system->backup_storage_id) == $storage->id)>
                    {{ $storage->name }}
                    @unless ($storage->is_active)
                        (Nonaktif)
                    @endunless
                </option>
            @endforeach
        </select>
        @error('backup_storage_id')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror

        @if ($storages->isEmpty())
            <p class="mt-2 text-sm text-amber-600">
                Belum ada storage aktif. Aktifkan atau tambahkan storage backup terlebih dahulu.
            </p>
        @endif
    </div>

    <div>
        <label for="category" class="block text-sm font-semibold text-slate-700">
            Kategori <span class="text-red-500">*</span>
        </label>
        <select id="category"
                name="category"
                class="mt-2 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <option value="">Pilih kategori</option>
            @foreach ($categories as $value => $label)
                <option value="{{ $value }}" @selected(old('category', $system->category) === $value)>
                    {{ $label }}
                </option>
            @endforeach
        </select>
        @error('category')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="source_server" class="block text-sm font-semibold text-slate-700">
            Server Sumber
        </label>
        <input type="text"
               id="source_server"
               name="source_server"
               value="{{ old('source_server', $system->source_server) }}"
               class="mt-2 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
               placeholder="Contoh: 192.168.1.10 / server-keuangan">
        @error('source_server')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="expected_frequency" class="block text-sm font-semibold text-slate-700">
            Expected Frequency <span class="text-red-500">*</span>
        </label>
        <select id="expected_frequency"
                name="expected_frequency"
                class="mt-2 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <option value="">Pilih frekuensi</option>
            @foreach ($frequencies as $value => $label)
                <option value="{{ $value }}" @selected(old('expected_frequency', $system->expected_frequency) === $value)>
                    {{ $label }}
                </option>
            @endforeach
        </select>
        @error('expected_frequency')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="lg:col-span-2">
        <label for="source_path" class="block text-sm font-semibold text-slate-700">
            Source Path
        </label>
        <input type="text"
               id="source_path"
               name="source_path"
               value="{{ old('source_path', $system->source_path) }}"
               class="mt-2 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
               placeholder="Contoh: /home/trb/firebird/EPRALA.FDB">
        @error('source_path')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="lg:col-span-2">
        <label for="backup_schedule" class="block text-sm font-semibold text-slate-700">
            Jadwal Backup
        </label>
        <input type="text"
               id="backup_schedule"
               name="backup_schedule"
               value="{{ old('backup_schedule', $system->backup_schedule) }}"
               class="mt-2 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
               placeholder="Contoh: Setiap hari pukul 00:00">
        @error('backup_schedule')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="lg:col-span-2">
        <label for="notes" class="block text-sm font-semibold text-slate-700">
            Catatan
        </label>
        <textarea id="notes"
                  name="notes"
                  rows="4"
                  class="mt-2 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                  placeholder="Catatan tambahan terkait sistem backup ini">{{ old('notes', $system->notes) }}</textarea>
        @error('notes')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="lg:col-span-2">
        <label class="inline-flex items-center gap-3">
            <input type="checkbox"
                   name="is_active"
                   value="1"
                   @checked(old('is_active', $system->is_active))
                   class="rounded border-slate-300 text-blue-600 shadow-sm focus:ring-blue-500">
            <span class="text-sm font-semibold text-slate-700">
                Sistem backup aktif
            </span>
        </label>

        <p class="mt-2 text-sm text-slate-500">
            Sistem nonaktif tidak akan dihitung sebagai sistem wajib backup pada monitoring berikutnya.
        </p>
    </div>

    <div class="mt-8 grid gap-4 lg:grid-cols-3">
        <div class="rounded-2xl border border-blue-100 bg-blue-50 p-4">
            <div class="flex items-start gap-3">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white text-blue-600 shadow-sm">
                    <i data-lucide="database" class="h-4 w-4"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-blue-950">
                        Kategori Backup
                    </h3>
                    <p class="mt-1 text-xs leading-5 text-blue-800">
                        Gunakan Database untuk FDB/MySQL, File untuk folder/arsip, Mail untuk email server, dan Mixed untuk aplikasi + database.
                    </p>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-emerald-100 bg-emerald-50 p-4">
            <div class="flex items-start gap-3">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white text-emerald-600 shadow-sm">
                    <i data-lucide="calendar-clock" class="h-4 w-4"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-emerald-950">
                        Expected Frequency
                    </h3>
                    <p class="mt-1 text-xs leading-5 text-emerald-800">
                        Frekuensi dipakai untuk membandingkan apakah backup seharusnya berjalan pada periode tertentu.
                    </p>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-amber-100 bg-amber-50 p-4">
            <div class="flex items-start gap-3">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white text-amber-600 shadow-sm">
                    <i data-lucide="hard-drive" class="h-4 w-4"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-amber-950">
                        Storage Tujuan
                    </h3>
                    <p class="mt-1 text-xs leading-5 text-amber-800">
                        Sistem hanya boleh diarahkan ke storage aktif agar monitoring berikutnya tetap valid.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const codeInput = document.getElementById('code');

            if (!codeInput) {
                return;
            }

            codeInput.addEventListener('input', function () {
                this.value = this.value
                    .toUpperCase()
                    .replace(/[^A-Z0-9]+/g, '_')
                    .replace(/_+/g, '_')
                    .replace(/^_+|_+$/g, '');
            });
        });
    </script>
@endpush