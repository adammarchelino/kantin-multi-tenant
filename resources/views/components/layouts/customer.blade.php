@props(['title' => 'Pelanggan'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="antialiased">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-zinc-50 text-zinc-900 dark:bg-zinc-950 dark:text-zinc-100">
        <div class="flex min-h-screen w-full flex-col">
            <header class="sticky top-0 z-10 border-b border-zinc-200 bg-white/90 px-4 backdrop-blur sm:px-6 lg:px-8 dark:border-zinc-800 dark:bg-zinc-900/90">
                <div class="mx-auto flex min-h-14 w-full max-w-7xl items-center gap-2">
                    <span class="text-base font-semibold">{{ $title }}</span>
                    @isset($headerRight)
                        <div class="ms-auto">{{ $headerRight }}</div>
                    @endisset
                </div>
            </header>
            <main class="mx-auto w-full max-w-7xl flex-1 px-4 py-6 sm:px-6 lg:px-8">{{ $slot }}</main>
            <footer class="border-t border-zinc-200 px-4 py-4 text-center text-xs text-zinc-500 sm:px-6 lg:px-8 dark:border-zinc-800 dark:text-zinc-400">
                <div class="mx-auto w-full max-w-7xl">{{ config('app.name') }}</div>
            </footer>
        </div>
        @fluxScripts
    </body>
</html>
