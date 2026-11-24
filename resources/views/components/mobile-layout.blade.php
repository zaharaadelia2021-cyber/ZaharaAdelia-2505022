@props(['title', 'activeTab'])

<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ $title }} · Breeze</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-[#f7f8f5]">
        <div class="mx-auto min-h-screen w-full max-w-md bg-white shadow-[0_0_60px_-30px_rgba(15,23,42,0.28)]">
            <header class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                <a href="{{ route('home') }}" class="flex items-center gap-2.5" aria-label="Breeze, ke Beranda">
                    <span class="flex size-9 items-center justify-center rounded-xl bg-brand-600 text-white">
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4 20V9l8-5 8 5v11M8 20v-6h8v6M3 20h18" /></svg>
                    </span>
                    <span class="text-lg font-bold tracking-tight">breeze<span class="text-brand-600">.</span></span>
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="rounded-lg px-3 py-2 text-xs font-semibold text-slate-500 transition hover:bg-slate-50 hover:text-slate-800">Keluar</button>
                </form>
            </header>

            <main class="min-h-[calc(100vh-9rem)] px-5 pb-28 pt-7">
                {{ $slot }}
            </main>

            <nav aria-label="Navigasi utama" class="fixed bottom-0 left-1/2 z-10 w-full max-w-md -translate-x-1/2 border-t border-slate-100 bg-white/95 px-2 pt-2 shadow-[0_-8px_24px_-22px_rgba(15,23,42,0.3)] backdrop-blur" style="padding-bottom: max(env(safe-area-inset-bottom), 0.5rem)">
                <div class="grid grid-cols-5">
                    @php
                        $tabs = [
                            ['route' => 'home', 'label' => 'Beranda', 'icon' => 'home'],
                            ['route' => 'barang', 'label' => 'Barang', 'icon' => 'box'],
                            ['route' => 'catat', 'label' => 'Catat', 'icon' => 'plus'],
                            ['route' => 'belanja', 'label' => 'Belanja', 'icon' => 'bag'],
                            ['route' => 'profile', 'label' => 'Profil', 'icon' => 'profile'],
                        ];
                    @endphp
                    @foreach ($tabs as $tab)
                        <a href="{{ route($tab['route']) }}" @if ($activeTab === $tab['route']) aria-current="page" @endif class="flex min-h-14 flex-col items-center justify-center gap-1 rounded-xl text-[10px] font-medium transition {{ $activeTab === $tab['route'] ? 'text-brand-700' : 'text-slate-400 hover:text-slate-600' }}">
                            @if ($tab['icon'] === 'home')
                                <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m3.5 10 8.5-7 8.5 7v10a1 1 0 0 1-1 1h-5v-6h-5v6h-5a1 1 0 0 1-1-1V10Z" /></svg>
                            @elseif ($tab['icon'] === 'box')
                                <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m12 3 8.5 4.5v9L12 21l-8.5-4.5v-9L12 3Z" /><path stroke-linecap="round" stroke-linejoin="round" d="m3.8 7.7 8.2 4.5 8.2-4.5M12 12.2V21m4.2-15.5-8.4 4.6" /></svg>
                            @elseif ($tab['icon'] === 'plus')
                                <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><circle cx="12" cy="12" r="9" /><path stroke-linecap="round" d="M12 8v8m-4-4h8" /></svg>
                            @elseif ($tab['icon'] === 'bag')
                                <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14l1 12H4L5 8Zm3 0V6a4 4 0 0 1 8 0v2" /></svg>
                            @else
                                <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><circle cx="12" cy="8" r="3.5" /><path stroke-linecap="round" stroke-linejoin="round" d="M5 20v-1a7 7 0 0 1 14 0v1H5Z" /></svg>
                            @endif
                            <span>{{ $tab['label'] }}</span>
                        </a>
                    @endforeach
                </div>
            </nav>
        </div>
    </body>
</html>
