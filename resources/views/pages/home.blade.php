<x-mobile-layout title="Beranda" active-tab="home">
    <div>
        <p class="text-sm font-medium text-brand-700">Halo, {{ auth()->user()->name }} 👋</p>
        <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">{{ auth()->user()->nama_warung }}</h1>
        <p class="mt-1 text-sm text-slate-500">Ringkasan stok dan kebutuhan warungmu.</p>
    </div>

    <a href="{{ route('belanja') }}" class="mt-5 block rounded-2xl bg-brand-700 p-5 text-white shadow-lg shadow-brand-900/10 transition hover:bg-brand-800">
        <div class="flex items-start justify-between gap-3">
            <div>
                <p class="text-sm font-semibold text-brand-100">Rekomendasi belanja</p>
                <p class="mt-2 text-2xl font-bold tracking-tight tabular-nums">Rp{{ number_format($total_biaya, 0, ',', '.') }}</p>
                <p class="mt-1 text-xs text-brand-100">{{ count($rekomendasi) }} barang · target {{ $hariTargetStok }} hari</p>
            </div>
            <span class="flex size-11 shrink-0 items-center justify-center rounded-2xl bg-white/15">
                <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14l1 12H4L5 8Zm3 0V6a4 4 0 0 1 8 0v2" /></svg>
            </span>
        </div>
        <span class="mt-4 inline-flex items-center gap-1 text-xs font-semibold text-white">
            Lihat daftar belanja
            <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6" /></svg>
        </span>
    </a>

    <div class="mt-3 grid grid-cols-2 gap-3">
        <a href="{{ route('barang', ['status' => 'menipis']) }}" class="rounded-2xl border border-amber-100 bg-amber-50 p-4 transition hover:bg-amber-100">
            <span class="flex size-9 items-center justify-center rounded-xl bg-white text-amber-600">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3 2.8 19h18.4L12 3Zm0 6v4m0 3h.01" /></svg>
            </span>
            <p class="mt-3 text-2xl font-bold tabular-nums text-amber-800">{{ $counts['menipis'] }}</p>
            <p class="text-xs font-medium text-amber-700">Barang menipis</p>
        </a>
        <a href="{{ route('barang', ['status' => 'habis']) }}" class="rounded-2xl border border-red-100 bg-red-50 p-4 transition hover:bg-red-100">
            <span class="flex size-9 items-center justify-center rounded-xl bg-white text-red-600">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="12" r="8.5" /><path stroke-linecap="round" d="M8.5 8.5 15.5 15.5m0-7-7 7" /></svg>
            </span>
            <p class="mt-3 text-2xl font-bold tabular-nums text-red-800">{{ $counts['habis'] }}</p>
            <p class="text-xs font-medium text-red-700">Barang habis</p>
        </a>
    </div>

    <a href="{{ route('catat') }}" class="mt-4 flex w-full items-center justify-center gap-2 rounded-xl bg-brand-600 px-4 py-3.5 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700">
        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="12" r="9" /><path stroke-linecap="round" d="M12 8v8m-4-4h8" /></svg>
        Catat transaksi
    </a>

    <section class="mt-7">
        <div class="flex items-end justify-between">
            <div>
                <h2 class="text-base font-bold text-slate-900">Perlu perhatian</h2>
                <p class="mt-0.5 text-xs text-slate-500">Stok paling mendesak untuk diperiksa</p>
            </div>
            <a href="{{ route('barang', ['status' => 'menipis']) }}" class="text-xs font-semibold text-brand-700">Lihat barang</a>
        </div>

        @if ($perhatian === [])
            <div class="mt-3 rounded-2xl border border-slate-100 bg-white px-4 py-6 text-center">
                <p class="text-sm font-semibold text-slate-800">Semua stok aman</p>
                <p class="mt-1 text-xs text-slate-500">Belum ada barang yang perlu perhatian.</p>
            </div>
        @else
            <div class="mt-3 space-y-2.5">
                @foreach ($perhatian as $item)
                    <a href="{{ route('barang.edit', $item['barang']) }}" class="flex items-center gap-3 rounded-2xl border border-slate-100 bg-white p-3.5 transition hover:border-slate-200">
                        <span class="flex size-10 shrink-0 items-center justify-center rounded-xl {{ $item['status'] === 'Habis' ? 'bg-red-50 text-red-600' : 'bg-amber-50 text-amber-700' }}">
                            <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m12 3 8.5 4.5v9L12 21l-8.5-4.5v-9L12 3Z" /><path stroke-linecap="round" stroke-linejoin="round" d="m3.8 7.7 8.2 4.5 8.2-4.5M12 12.2V21" /></svg>
                        </span>
                        <span class="min-w-0 flex-1">
                            <span class="block truncate text-sm font-semibold text-slate-800">{{ $item['barang']->nama }}</span>
                            <span class="mt-0.5 block text-xs text-slate-500">Sisa {{ rtrim(rtrim(number_format((float) $item['barang']->stok, 2, ',', '.'), '0'), ',') }} {{ $item['barang']->satuan }}</span>
                        </span>
                        <x-status-chip :status="$item['status']" />
                    </a>
                @endforeach
            </div>
        @endif
    </section>
</x-mobile-layout>
