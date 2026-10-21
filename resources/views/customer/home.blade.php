<x-layouts.customer title="Kantin">
    <section class="space-y-5 md:grid md:grid-cols-[minmax(0,1fr)_minmax(18rem,0.8fr)] md:items-center md:gap-10 md:space-y-0" aria-labelledby="customer-heading">
        <div class="max-w-2xl">
            <p class="text-sm font-medium text-emerald-700 dark:text-emerald-400">Kantin Multi-Tenant</p>
            <h1 id="customer-heading" class="mt-1 text-2xl font-bold">Selamat datang</h1>
            <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">Pilih menu favoritmu dan pesan dengan mudah.</p>
        </div>
        <x-empty-state title="Menu akan segera tersedia" description="Katalog menu akan ditampilkan di sini." class="min-h-48 md:min-h-64" />
    </section>
</x-layouts.customer>
