@csrf

<div class="grid gap-6 lg:grid-cols-2">
    <div>
        <label for="name" class="mb-2 block text-sm font-semibold text-slate-700">
            Nama Storage <span class="text-red-500">*</span>
        </label>

        <input
            type="text"
            id="name"
            name="name"
            value="{{ old('name', $storage->name) }}"
            placeholder="Contoh: PC Backup Sementara"
            class="block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
            required
        >

        @error('name')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="host" class="mb-2 block text-sm font-semibold text-slate-700">
            Host / IP
        </label>

        <input
            type="text"
            id="host"
            name="host"
            value="{{ old('host', $storage->host) }}"
            placeholder="Contoh: 192.168.1.10 atau backup-server.local"
            class="block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
        >

        <p class="mt-2 text-xs text-slate-500">
            Boleh dikosongkan untuk storage local.
        </p>

        @error('host')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="os_type" class="mb-2 block text-sm font-semibold text-slate-700">
            Jenis OS <span class="text-red-500">*</span>
        </label>

        <select
            id="os_type"
            name="os_type"
            class="block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
            required
        >
            <option value="">Pilih OS</option>
            @foreach ($osTypes as $value => $label)
                <option value="{{ $value }}" @selected(old('os_type', $storage->os_type) === $value)>
                    {{ $label }}
                </option>
            @endforeach
        </select>

        @error('os_type')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="access_type" class="mb-2 block text-sm font-semibold text-slate-700">
            Metode Akses <span class="text-red-500">*</span>
        </label>

        <select
            id="access_type"
            name="access_type"
            class="block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
            required
        >
            <option value="">Pilih metode akses</option>
            @foreach ($accessTypes as $value => $label)
                <option value="{{ $value }}" @selected(old('access_type', $storage->access_type) === $value)>
                    {{ $label }}
                </option>
            @endforeach
        </select>

        @error('access_type')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="lg:col-span-2">
        <label for="base_path" class="mb-2 block text-sm font-semibold text-slate-700">
            Base Path <span class="text-red-500">*</span>
        </label>

        <input
            type="text"
            id="base_path"
            name="base_path"
            value="{{ old('base_path', $storage->base_path) }}"
            placeholder="Contoh: K:\Backup atau /mnt/backup atau \\192.168.1.10\Backup"
            class="block w-full rounded-xl border-slate-300 font-mono text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
            required
        >

        <p class="mt-2 text-xs text-slate-500">
            Isi path utama lokasi backup. Path ini nanti jadi acuan mapping sistem backup.
        </p>

        @error('base_path')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="lg:col-span-2">
        <label for="notes" class="mb-2 block text-sm font-semibold text-slate-700">
            Catatan
        </label>

        <textarea
            id="notes"
            name="notes"
            rows="4"
            placeholder="Contoh: Storage sementara di PC admin, rencana akan dipindah ke server backup Linux."
            class="block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
        >{{ old('notes', $storage->notes) }}</textarea>

        @error('notes')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="lg:col-span-2">
        <label class="flex items-start gap-3 rounded-xl border border-slate-200 bg-slate-50 p-4">
            <input
                type="checkbox"
                name="is_active"
                value="1"
                class="mt-1 rounded border-slate-300 text-blue-600 shadow-sm focus:ring-blue-500"
                @checked(old('is_active', $storage->exists ? $storage->is_active : true))
            >

            <span>
                <span class="block text-sm font-semibold text-slate-800">
                    Storage aktif
                </span>
                <span class="mt-1 block text-sm text-slate-500">
                    Storage aktif dapat dipilih pada mapping sistem backup di milestone berikutnya.
                </span>
            </span>
        </label>

        @error('is_active')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>