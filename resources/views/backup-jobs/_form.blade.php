@csrf

<div class="grid gap-5 lg:grid-cols-2">
    <div>
        <label for="backup_system_id" class="block text-sm font-semibold text-slate-700">Sistem Backup</label>
        <select id="backup_system_id" name="backup_system_id" class="mt-2 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <option value="">Pilih sistem backup</option>
            @foreach ($systems as $system)
                <option value="{{ $system->id }}" @selected(old('backup_system_id', $job->backup_system_id) == $system->id)>
                    {{ $system->name }} — {{ $system->code }}
                    @if ($system->storage)
                        ({{ $system->storage->name }})
                    @endif
                </option>
            @endforeach
        </select>
        @error('backup_system_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="name" class="block text-sm font-semibold text-slate-700">Nama Job</label>
        <input type="text" id="name" name="name" value="{{ old('name', $job->name) }}" class="mt-2 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Backup Database Tracer Study Harian">
        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="code" class="block text-sm font-semibold text-slate-700">Kode Job</label>
        <input type="text" id="code" name="code" value="{{ old('code', $job->code) }}" class="mt-2 block w-full rounded-xl border-slate-300 text-sm uppercase shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="TRACER_STUDY_DB_DAILY">
        <p class="mt-1 text-xs text-slate-500">Otomatis dinormalisasi menjadi huruf kapital dan underscore.</p>
        @error('code') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="backup_type" class="block text-sm font-semibold text-slate-700">Backup Type</label>
        <select id="backup_type" name="backup_type" class="mt-2 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <option value="">Pilih type</option>
            @foreach ($backupTypes as $value => $label)
                <option value="{{ $value }}" @selected(old('backup_type', $job->backup_type) === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('backup_type') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="expected_frequency" class="block text-sm font-semibold text-slate-700">Expected Frequency</label>
        <select id="expected_frequency" name="expected_frequency" class="mt-2 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
            @foreach ($frequencies as $value => $label)
                <option value="{{ $value }}" @selected(old('expected_frequency', $job->expected_frequency) === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('expected_frequency') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="expected_time" class="block text-sm font-semibold text-slate-700">Jam Jadwal Dokumentasi</label>
        <input type="time" id="expected_time" name="expected_time" value="{{ old('expected_time', optional($job->expected_time)->format('H:i') ?? $job->expected_time) }}" class="mt-2 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
        @error('expected_time') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="expected_run_time" class="block text-sm font-semibold text-slate-700">
            Jam Backup Normal
        </label>

        <input
            type="time"
            id="expected_run_time"
            name="expected_run_time"
            value="{{ old('expected_run_time', $job->expected_run_time ? substr($job->expected_run_time, 0, 5) : '') }}"
            class="mt-2 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">

        <p class="mt-1 text-xs text-slate-500">
            Jam normal backup/pull selesai atau mulai dianggap relevan untuk dipantau pending.
        </p>

        @error('expected_run_time')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="alert_after_minutes" class="block text-sm font-semibold text-slate-700">
            Alert Pending Setelah
        </label>

        <div class="mt-2 flex rounded-xl shadow-sm">
            <input
                type="number"
                id="alert_after_minutes"
                name="alert_after_minutes"
                min="0"
                max="1440"
                value="{{ old('alert_after_minutes', $job->alert_after_minutes ?? 60) }}"
                class="block w-full rounded-l-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">

            <span class="inline-flex items-center rounded-r-xl border border-l-0 border-slate-300 bg-slate-50 px-3 text-sm font-semibold text-slate-500">
                menit
            </span>
        </div>

        <p class="mt-1 text-xs text-slate-500">
            Contoh: jam backup 01:15 + 60 menit, maka pending baru muncul mulai 02:15.
        </p>

        @error('alert_after_minutes')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="lg:col-span-2">
        <label for="schedule_text" class="block text-sm font-semibold text-slate-700">Jadwal</label>
        <input type="text" id="schedule_text" name="schedule_text" value="{{ old('schedule_text', $job->schedule_text) }}" class="mt-2 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Setiap hari pukul 00:30 / Manual jika diperlukan">
        @error('schedule_text') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="lg:col-span-2">
        <label for="notes" class="block text-sm font-semibold text-slate-700">Catatan</label>
        <textarea id="notes" name="notes" rows="4" class="mt-2 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('notes', $job->notes) }}</textarea>
        @error('notes') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="lg:col-span-2">
        <label class="inline-flex items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
            <input type="checkbox" name="is_active" value="1" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500" @checked(old('is_active', $job->is_active ?? true))>
            <span class="text-sm font-semibold text-slate-700">Job aktif dan dihitung sebagai job wajib backup</span>
        </label>
    </div>
</div>

<div class="mt-6 flex items-center justify-end gap-3 border-t border-slate-200 pt-6">
    <a href="{{ route('backup-jobs.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
        <i data-lucide="arrow-left" class="h-4 w-4"></i>
        Batal
    </a>
    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700">
        <i data-lucide="save" class="h-4 w-4"></i>
        Simpan Job
    </button>
</div>
