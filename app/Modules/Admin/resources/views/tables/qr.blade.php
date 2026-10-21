<x-layouts.admin title="QR Meja {{ $table->code }}">
    <style>
        .table-qr-image svg { display: block; width: 100%; height: auto; max-width: 22rem; margin-inline: auto; }

        @media print {
            body { background: #fff !important; color: #111 !important; }
            body > div > header,
            .table-qr-no-print,
            footer { display: none !important; }
            .table-qr-print { max-width: none !important; padding: 0 !important; }
            .table-qr-card { border: 0 !important; box-shadow: none !important; }
            .table-qr-image svg { width: 70mm; max-width: 70mm; }
        }
    </style>

    <section class="table-qr-print mx-auto max-w-5xl">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <p class="text-sm font-medium text-zinc-500">Meja {{ $table->code }}</p>
                <h1 class="mt-1 text-2xl font-bold tracking-tight">QR {{ $table->label }}</h1>
                @if ($table->zone)<p class="mt-1 text-sm text-zinc-500">{{ $table->zone }}</p>@endif
            </div>
            <a class="table-qr-no-print text-sm font-medium text-zinc-600 underline hover:text-zinc-900" href="{{ route('admin.tables.index') }}">← Kembali ke daftar meja</a>
        </div>

        @if ($url && $svg)
            <div class="table-qr-no-print mt-5 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm leading-6 text-amber-950">
                QR ini hanya ditampilkan saat diterbitkan. Unduh atau cetak sekarang. Jika QR hilang atau tersebar, rotasi dari daftar meja.
            </div>

            <div class="mt-6 grid items-start gap-5 md:grid-cols-[minmax(0,24rem)_minmax(0,1fr)]">
                <div class="table-qr-card rounded-2xl border border-zinc-200 bg-white p-4 shadow-sm sm:p-5">
                    <div class="table-qr-image overflow-hidden rounded-lg bg-white p-2">
                        {!! $svg !!}
                    </div>
                    <div class="table-qr-no-print mt-4 flex flex-wrap gap-3">
                        <a class="inline-flex min-h-11 items-center justify-center rounded-lg bg-red-600 px-4 text-sm font-semibold text-white hover:bg-red-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:ring-offset-2"
                            download="qr-{{ $table->code }}.svg"
                            href="data:image/svg+xml;base64,{{ base64_encode($svg) }}">Unduh QR (SVG)</a>
                        <button class="inline-flex min-h-11 items-center justify-center rounded-lg border border-zinc-300 bg-white px-4 text-sm font-semibold text-zinc-800 hover:bg-zinc-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-zinc-500 focus-visible:ring-offset-2" type="button" onclick="window.print()">Cetak</button>
                    </div>
                </div>

                <div class="table-qr-no-print min-w-0 rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm">
                    <h2 class="text-sm font-semibold text-zinc-900">Tautan QR</h2>
                    <p class="mt-1 text-sm text-zinc-500">Gunakan tautan ini untuk mengecek QR sebelum dicetak.</p>
                    <p class="mt-4 break-all rounded-lg bg-zinc-100 p-3 font-mono text-sm leading-6 text-zinc-800">{{ $url }}</p>
                    <p class="mt-3 text-xs leading-5 text-zinc-500">Token hanya muncul pada halaman penerbitan ini. Setelah halaman ditinggalkan, token tidak bisa ditampilkan lagi.</p>
                </div>
            </div>
        @else
            <div class="mt-6 rounded-2xl border border-zinc-200 bg-white p-6">
                <x-empty-state title="Tidak ada QR baru" description="Terbitkan atau rotasi QR dari daftar meja untuk menampilkan QR baru." />
                <a class="mt-4 inline-flex text-sm font-medium text-red-700 underline" href="{{ route('admin.tables.index') }}">Kembali ke daftar meja</a>
            </div>
        @endif
    </section>
</x-layouts.admin>
