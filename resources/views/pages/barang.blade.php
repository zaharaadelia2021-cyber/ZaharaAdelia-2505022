<x-mobile-layout title="Barang" active-tab="barang">
    <div class="flex items-start justify-between gap-3">
        <div>
            <p class="text-sm font-medium text-brand-700">{{ auth()->user()->nama_warung }}</p>
            <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">Barang</h1>
            <p class="mt-1 text-sm text-slate-500">Pantau stok barang warungmu.</p>
        </div>
        <span class="rounded-xl bg-brand-50 px-3 py-2 text-xs font-semibold text-brand-700">{{ $counts['semua'] }} barang</span>
    </div>

    @if (session('success'))
        <p role="status" class="mt-5 rounded-xl border border-brand-100 bg-brand-50 px-4 py-3 text-sm font-medium text-brand-800">{{ session('success') }}</p>
    @endif

    <form method="GET" action="{{ route('barang') }}" class="mt-5">
        @if ($status !== 'semua')
            <input type="hidden" name="status" value="{{ $status }}">
        @endif
        <label for="q" class="sr-only">Cari barang</label>
        <div class="relative">
            <svg class="pointer-events-none absolute left-3.5 top-1/2 size-5 -translate-y-1/2 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.8" /><path stroke-linecap="round" d="m16 16 4.5 4.5" /></svg>
            <input id="q" name="q" type="search" value="{{ $search }}" placeholder="Cari nama barang..." class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3 pl-11 pr-4 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-50">
        </div>
    </form>

    <div class="mt-4 flex gap-2 overflow-x-auto pb-1" aria-label="Filter status barang">
        @foreach ([
            'semua' => ['Semua', $counts['semua']],
            'menipis' => ['Menipis', $counts['menipis']],
            'habis' => ['Habis', $counts['habis']],
        ] as $key => [$label, $count])
            <a href="{{ route('barang', array_filter(['status' => $key === 'semua' ? null : $key, 'q' => $search ?: null])) }}"
                @if ($status === $key) aria-current="true" @endif
                class="inline-flex shrink-0 items-center gap-1.5 rounded-full px-3.5 py-2 text-xs font-semibold transition {{ $status === $key ? 'bg-brand-700 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                {{ $label }}
                <span class="{{ $status === $key ? 'text-brand-100' : 'text-slate-400' }}">{{ $count }}</span>
            </a>
        @endforeach
    </div>

    @if ($barangs->isEmpty())
        <div class="mt-5 rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-5 py-10 text-center">
            <span class="mx-auto flex size-12 items-center justify-center rounded-2xl bg-white text-slate-400 shadow-sm">
                <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m12 3 8.5 4.5v9L12 21l-8.5-4.5v-9L12 3Z" /><path stroke-linecap="round" stroke-linejoin="round" d="m3.8 7.7 8.2 4.5 8.2-4.5M12 12.2V21" /></svg>
            </span>
            <h2 class="mt-4 text-sm font-semibold text-slate-800">
                {{ $search !== '' ? 'Barang tidak ditemukan' : ($status === 'semua' ? 'Belum ada barang' : 'Tidak ada barang '.$status) }}
            </h2>
            <p class="mt-1 text-xs leading-5 text-slate-500">
                {{ $search !== '' ? 'Coba kata kunci lain.' : ($status === 'semua' ? 'Tambahkan barang pertama untuk mulai mencatat stok.' : 'Stok barangmu dalam kondisi baik.') }}
            </p>
            @if ($search !== '')
                <a href="{{ route('barang', array_filter(['status' => $status === 'semua' ? null : $status])) }}" class="mt-3 inline-block text-xs font-semibold text-brand-700">Hapus pencarian</a>
            @endif
        </div>
    @else
        <div class="mt-4 space-y-3">
            @foreach ($barangs as $barang)
                <x-barang-card :barang="$barang" />
            @endforeach
        </div>
    @endif

    <div class="fixed bottom-20 left-1/2 z-20 w-full max-w-md -translate-x-1/2 px-5 pb-2 text-right pointer-events-none">
        <a href="{{ route('barang.create') }}" class="pointer-events-auto inline-flex items-center gap-2 rounded-full bg-brand-600 px-5 py-3.5 text-sm font-semibold text-white shadow-lg shadow-brand-600/25 transition hover:bg-brand-700 focus:outline-none focus:ring-4 focus:ring-brand-100">
            <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" d="M12 5v14m-7-7h14" /></svg>
            Tambah barang
        </a>
    </div>
</x-mobile-layout>
