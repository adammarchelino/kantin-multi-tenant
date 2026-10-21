<x-layouts.admin title="Meja & QR">
    @if (session('status'))<div class="mb-4 rounded-lg bg-green-100 px-4 py-2 text-sm text-green-800">{{ session('status') }}</div>@endif
    @if ($errors->any())<div class="mb-4 rounded-lg bg-red-100 px-4 py-2 text-sm text-red-800">{{ $errors->first() }}</div>@endif
    <h1 class="text-xl font-semibold">Meja — {{ $canteen->name }}</h1>
    <form method="POST" action="{{ route('admin.tables.store') }}" class="mt-4 flex flex-wrap items-end gap-2">@csrf
        <x-input name="code" label="Kode" required/><x-input name="label" label="Label" required/><x-input name="zone" label="Zona (opsional)"/><x-button type="submit">Tambah meja</x-button>
    </form>
    <div class="mt-6 overflow-x-auto rounded-xl border border-zinc-200 dark:border-zinc-800">
        <table class="w-full min-w-[48rem] text-left text-sm"><thead class="bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-200"><tr><th class="px-4 py-2">Kode</th><th class="px-4 py-2">Meja</th><th class="px-4 py-2">Zona</th><th class="px-4 py-2">Status</th><th class="px-4 py-2">QR</th><th class="px-4 py-2">Aksi</th></tr></thead>
            <tbody>@forelse($tables as $table)<tr class="border-t border-zinc-200 dark:border-zinc-800"><td class="px-4 py-3 font-mono">{{ $table->code }}</td><td class="px-4 py-3">{{ $table->label }}</td><td class="px-4 py-3">{{ $table->zone ?? '—' }}</td><td class="px-4 py-3">{{ $table->status === 'active' ? 'Aktif' : 'Nonaktif' }}</td><td class="px-4 py-3">{{ $table->activeToken ? 'Aktif' : 'Belum terbit' }}</td><td class="px-4 py-3"><div class="flex flex-wrap gap-2">
                <form method="POST" action="{{ route('admin.tables.rotate', $table) }}">@csrf<x-button variant="secondary" type="submit">{{ $table->activeToken ? 'Rotasi QR' : 'Terbitkan QR' }}</x-button></form>
                <form method="POST" action="{{ route('admin.tables.status', $table) }}">@csrf<input type="hidden" name="status" value="{{ $table->status === 'active' ? 'inactive' : 'active' }}"><x-button variant="secondary" type="submit">{{ $table->status === 'active' ? 'Nonaktifkan' : 'Aktifkan' }}</x-button></form>
            </div></td></tr>@empty<tr><td class="px-4 py-4" colspan="6">Belum ada meja.</td></tr>@endforelse</tbody>
        </table>
    </div>
</x-layouts.admin>
