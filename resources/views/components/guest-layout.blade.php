<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ $title }} · {{ config('app.name', 'Breeze') }}</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen">
        <main class="mx-auto flex min-h-screen w-full max-w-md flex-col justify-center px-5 py-8 sm:px-8">
            <a href="{{ route('register') }}" class="mb-7 flex items-center justify-center gap-2.5 self-center" aria-label="Breeze">
                <span class="flex size-10 items-center justify-center rounded-2xl bg-brand-600 text-white">
                    <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 20V9l8-5 8 5v11M8 20v-6h8v6M3 20h18" />
                    </svg>
                </span>
                <span class="text-xl font-bold tracking-tight text-slate-900">breeze<span class="text-brand-600">.</span></span>
            </a>
            {{ $slot }}
            <p class="mt-6 text-center text-xs text-slate-400">Biar urusan warung terasa lebih ringan.</p>
        </main>
    </body>
</html>
