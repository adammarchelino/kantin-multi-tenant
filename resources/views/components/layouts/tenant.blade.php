@props(['title' => 'Tenant'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="antialiased">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-zinc-50 text-zinc-900 dark:bg-zinc-950 dark:text-zinc-100">
        <div data-tenant-layout class="flex min-h-screen">
            <button type="button" data-tenant-sidebar-backdrop class="fixed inset-0 z-10 hidden bg-zinc-950/40 lg:hidden" aria-label="Tutup navigasi"></button>
            <aside id="tenant-sidebar" data-tenant-sidebar class="fixed inset-y-0 left-0 z-20 w-64 -translate-x-full transform border-r border-zinc-200 bg-white p-4 transition-transform duration-200 lg:static lg:translate-x-0 dark:border-zinc-800 dark:bg-zinc-900">
                <div class="mb-6 flex min-h-11 items-center justify-between text-lg font-semibold">
                    <span>Tenant</span>
                    <button type="button" data-tenant-sidebar-close class="inline-flex min-h-11 min-w-11 items-center justify-center rounded-lg lg:hidden" aria-label="Tutup navigasi">
                        <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-5"><path d="m6 6 12 12M18 6 6 18" /></svg>
                    </button>
                </div>
                <nav aria-label="Navigasi tenant" class="flex flex-col gap-1 text-sm">
                    <a href="#dashboard" class="rounded-lg bg-zinc-100 px-3 py-3 font-medium dark:bg-zinc-800">Dashboard</a>
                    <span class="px-3 py-3 text-zinc-400">Katalog (Modul 7)</span>
                    <span class="px-3 py-3 text-zinc-400">Pesanan / KDS (Modul 12)</span>
                </nav>
            </aside>
            <div class="flex min-w-0 flex-1 flex-col">
                <header class="flex min-h-14 items-center gap-3 border-b border-zinc-200 bg-white px-4 dark:border-zinc-800 dark:bg-zinc-900">
                    <button type="button" data-tenant-sidebar-toggle aria-controls="tenant-sidebar" aria-expanded="false" class="inline-flex min-h-11 min-w-11 items-center justify-center rounded-lg lg:hidden" aria-label="Buka navigasi">
                        <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-5"><path d="M4 6h16M4 12h16M4 18h16" /></svg>
                    </button>
                    <span class="font-semibold">{{ $title }}</span>
                    @isset($headerRight)
                        <div class="ms-auto">{{ $headerRight }}</div>
                    @endisset
                </header>
                <main id="dashboard" class="flex-1 p-6">{{ $slot }}</main>
            </div>
        </div>
        @fluxScripts
    </body>
</html>
