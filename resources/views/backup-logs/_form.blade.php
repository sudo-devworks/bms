@csrf

@php
    $oldStartedAt = old('started_at', optional($log->started_at)->format('Y-m-d\TH:i'));
    $oldFinishedAt = old('finished_at', optional($log->finished_at)->format('Y-m-d\TH:i'));
@endphp

<div
    x-data="{
        status: @js(old('status', $log->status)),
        startedAt: @js($oldStartedAt),
        finishedAt: @js($oldFinishedAt),
        fileSizeMb: @js(old('file_size_mb')),
        fileSizeBytes: @js(old('file_size_bytes', $log->file_size_bytes)),
        durationSeconds() {
            if (!this.startedAt || !this.finishedAt) return null;

            const started = new Date(this.startedAt);
            const finished = new Date(this.finishedAt);

            if (Number.isNaN(started.getTime()) || Number.isNaN(finished.getTime()) || finished < started) return null;

            return Math.floor((finished - started) / 1000);
        },
        durationLabel() {
            const total = this.durationSeconds();
            if (total === null) return 'Durasi otomatis akan muncul jika waktu mulai dan selesai diisi.';

            const hours = Math.floor(total / 3600);
            const minutes = Math.floor((total % 3600) / 60);
            const seconds = total % 60;

            if (hours > 0) return `${hours} jam ${minutes} menit ${seconds} detik`;
            if (minutes > 0) return `${minutes} menit ${seconds} detik`;
            return `${seconds} detik`;
        },
        fileSizeLabel() {
            let bytes = null;

            if (this.fileSizeBytes !== null && this.fileSizeBytes !== '') {
                bytes = Number(this.fileSizeBytes);
            } else if (this.fileSizeMb !== null && this.fileSizeMb !== '') {
                bytes = Number(String(this.fileSizeMb).replace(',', '.')) * 1024 * 1024;
            }

            if (!bytes || Number.isNaN(bytes) || bytes < 0) return 'Preview ukuran file akan muncul setelah ukuran diisi.';

            const units = ['B', 'KB', 'MB', 'GB', 'TB'];
            let index = 0;

            while (bytes >= 1024 && index < units.length - 1) {
                bytes = bytes / 1024;
                index++;
            }

            return `${Number(bytes.toFixed(index === 0 ? 0 : 2))} ${units[index]}`;
        }
    }"
    class="space-y-6"
>
    <div class="rounded-2xl border border-blue-100 bg-blue-50/70 p-4 text-sm leading-6 text-blue-800">
        <div class="flex gap-3">
            <i data-lucide="info" class="mt-0.5 h-5 w-5 shrink-0"></i>
            <div>
                <p class="font-semibold">Log manual hanya mencatat hasil backup.</p>
                <p class="mt-1">BMS tidak menjalankan backup, tidak membuat scheduler, dan tidak melakukan scan folder. Sistem dan storage akan otomatis disalin dari Backup Job sebagai snapshot riwayat.</p>
            </div>
        </div>
    </div>

    <div class="grid gap-5 lg:grid-cols-2">
        <div class="lg:col-span-2">
            <label for="backup_job_id" class="block text-sm font-semibold text-slate-700">Backup Job</label>
            <select id="backup_job_id" name="backup_job_id" class="mt-2 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">Pilih backup job</option>
                @foreach ($jobs as $job)
                    <option value="{{ $job->id }}" @selected(old('backup_job_id', request('backup_job_id', $log->backup_job_id)) == $job->id)>
                        {{ $job->name }} — {{ $job->code }} / {{ $job->system?->name }} / {{ $job->system?->storage?->name }}
                    </option>
                @endforeach
            </select>
            <p class="mt-1 text-xs text-slate-500">Hanya job aktif yang bisa dipilih untuk input log manual.</p>
            @error('backup_job_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="status" class="block text-sm font-semibold text-slate-700">Status</label>
            <select id="status" name="status" x-model="status" class="mt-2 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @foreach ($statuses as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
            <p x-show="status === 'success'" class="mt-1 text-xs text-emerald-700">Backup selesai normal.</p>
            <p x-show="status === 'warning'" class="mt-1 text-xs text-amber-700">Backup selesai tapi ada catatan. Field Pesan wajib diisi.</p>
            <p x-show="status === 'failed'" class="mt-1 text-xs text-red-700">Backup gagal. Field Error Message wajib diisi.</p>
            @error('status') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="backup_date" class="block text-sm font-semibold text-slate-700">Tanggal Backup</label>
            <input type="date" id="backup_date" name="backup_date" value="{{ old('backup_date', optional($log->backup_date)->format('Y-m-d') ?? $log->backup_date) }}" class="mt-2 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
            @error('backup_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="started_at" class="block text-sm font-semibold text-slate-700">Waktu Mulai</label>
            <input type="datetime-local" id="started_at" name="started_at" x-model="startedAt" value="{{ $oldStartedAt }}" class="mt-2 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
            @error('started_at') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="finished_at" class="block text-sm font-semibold text-slate-700">Waktu Selesai</label>
            <input type="datetime-local" id="finished_at" name="finished_at" x-model="finishedAt" value="{{ $oldFinishedAt }}" class="mt-2 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
            @error('finished_at') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="duration_seconds" class="block text-sm font-semibold text-slate-700">Durasi Manual / Detik</label>
            <input type="number" min="0" id="duration_seconds" name="duration_seconds" value="{{ old('duration_seconds', $log->duration_seconds) }}" class="mt-2 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Opsional jika waktu mulai/selesai kosong">
            <p class="mt-1 text-xs text-slate-500" x-text="durationLabel()"></p>
            @error('duration_seconds') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="file_size_mb" class="block text-sm font-semibold text-slate-700">Ukuran File / MB</label>
            <input type="number" step="0.01" min="0" id="file_size_mb" name="file_size_mb" x-model="fileSizeMb" value="{{ old('file_size_mb') }}" class="mt-2 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Contoh: 153.75">
            <p class="mt-1 text-xs text-slate-500">Lebih mudah untuk input manual. Akan dikonversi ke bytes saat disimpan.</p>
            @error('file_size_mb') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="file_size_bytes" class="block text-sm font-semibold text-slate-700">Ukuran File / Bytes</label>
            <input type="number" min="0" id="file_size_bytes" name="file_size_bytes" x-model="fileSizeBytes" value="{{ old('file_size_bytes', $log->file_size_bytes) }}" class="mt-2 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Opsional, untuk angka presisi dari script">
            <p class="mt-1 text-xs text-slate-500" x-text="fileSizeLabel()"></p>
            @error('file_size_bytes') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="file_name" class="block text-sm font-semibold text-slate-700">Nama File</label>
            <input type="text" id="file_name" name="file_name" value="{{ old('file_name', $log->file_name) }}" class="mt-2 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="backup_2026_06_23.zip">
            @error('file_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="lg:col-span-2">
            <label for="file_path" class="block text-sm font-semibold text-slate-700">File Path</label>
            <input type="text" id="file_path" name="file_path" value="{{ old('file_path', $log->file_path) }}" class="mt-2 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="/backup/tracer/2026/06/backup.zip">
            @error('file_path') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="lg:col-span-2">
            <label for="checksum" class="block text-sm font-semibold text-slate-700">Checksum</label>
            <input type="text" id="checksum" name="checksum" value="{{ old('checksum', $log->checksum) }}" class="mt-2 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Opsional: md5/sha256 dari file backup">
            @error('checksum') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="message" class="block text-sm font-semibold text-slate-700">Pesan</label>
            <textarea id="message" name="message" rows="4" class="mt-2 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Contoh: Backup selesai dan file berhasil dipindahkan ke storage.">{{ old('message', $log->message) }}</textarea>
            @error('message') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="error_message" class="block text-sm font-semibold text-slate-700">Error Message</label>
            <textarea id="error_message" name="error_message" rows="4" class="mt-2 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Isi jika backup gagal.">{{ old('error_message', $log->error_message) }}</textarea>
            @error('error_message') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="flex items-center justify-end gap-3 border-t border-slate-200 pt-6">
        <a href="{{ route('backup-logs.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"><i data-lucide="arrow-left" class="h-4 w-4"></i>Batal</a>
        <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700"><i data-lucide="save" class="h-4 w-4"></i>Simpan Log</button>
    </div>
</div>
