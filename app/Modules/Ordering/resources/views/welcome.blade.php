<x-layouts.customer :title="$canteen->name">
    @if ($table)
        <x-slot:headerRight><span class="text-sm font-semibold">{{ $table->label }}</span></x-slot:headerRight>
        <div class="text-sm font-semibold text-green-700">Sesi pemesanan aktif</div>
        <h1 class="mt-2 text-2xl font-bold">Selamat datang, {{ $table->label }}@if($table->zone) — {{ $table->zone }}@endif</h1>
        <p class="mt-1 text-sm text-zinc-600">Pilih tenant yang buka untuk mulai melihat menu.</p>
    @else<x-empty-state title="Pindai QR meja untuk memulai" description="Sesi pelanggan dibuat setelah QR meja yang aktif dipindai."/>@endif
    <ul class="mt-5 divide-y overflow-hidden rounded-xl border bg-white" data-test="tenant-list">
        @foreach($tenants as $tenant) @php($open = $hours->isOpen($tenant))
            <li class="flex items-center justify-between px-4 py-3 {{ $open ? '' : 'opacity-60' }}"><div><div class="font-semibold">{{ $tenant->display_name }}</div><div class="text-xs text-zinc-500">@if($open)Buka{{ $hours->todayLabel($tenant) ? ' · '.$hours->todayLabel($tenant) : '' }}@else Tutup{{ $hours->opensAtToday($tenant) ? ' · Buka pukul '.$hours->opensAtToday($tenant) : ' hari ini' }}@endif</div></div>@unless($open)<span class="rounded border px-2 py-1 text-xs">TUTUP</span>@endunless</li>
        @endforeach
    </ul>
    @if($table)<a href="{{ route('customer.home', ['canteen' => $canteen->slug]) }}" class="mt-6 flex min-h-12 items-center justify-center rounded-lg bg-red-600 font-semibold text-white">Lihat semua menu →</a>@endif
</x-layouts.customer>
