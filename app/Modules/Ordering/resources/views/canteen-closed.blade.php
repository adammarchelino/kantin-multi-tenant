<x-layouts.customer :title="$canteen->name">
    <div class="rounded-xl border border-amber-300 bg-amber-50 p-4" data-test="canteen-closed"><h1 class="text-lg font-bold">Kantin sedang tutup</h1><p class="mt-1 text-sm">QR {{ $table->label }} valid, tetapi belum ada tenant yang beroperasi. Sesi pemesanan belum dibuat.</p></div>
    <ul class="mt-4 divide-y rounded-xl border bg-white">@foreach($tenants as $tenant)<li class="flex justify-between px-4 py-3 text-sm"><span>{{ $tenant->display_name }}</span><span>{{ $hours->todayLabel($tenant) ?? 'Libur hari ini' }}</span></li>@endforeach</ul>
</x-layouts.customer>
