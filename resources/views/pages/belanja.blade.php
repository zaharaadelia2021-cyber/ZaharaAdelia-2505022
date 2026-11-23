<x-mobile-layout title="Belanja" active-tab="belanja">
    <div>
        <p class="text-sm font-medium text-brand-700">{{ auth()->user()->nama_warung }}</p>
        <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">Daftar belanja</h1>
        <p class="mt-1 text-sm text-slate-500">Rekomendasi restock berdasarkan penjualan 7 hari terakhir.</p>
    </div>

    @if (session('success'))
        <p role="status" class="mt-5 rounded-xl border border-brand-100 bg-brand-50 px-4 py-3 text-sm font-medium text-brand-800">{{ session('success') }}</p>
    @endif

    <section class="mt-5 rounded-2xl border border-slate-100 bg-white p-4 shadow-sm">
        <form method="POST" action="{{ route('belanja.target.update') }}">
            @csrf
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h2 class="text-sm font-semibold text-slate-800">Target stok</h2>
                    <p class="mt-0.5 text-xs text-slate-500">Perkiraan hari stok yang ingin dijaga</p>
                </div>
                <div class="flex items-center rounded-xl border border-slate-200">
                    <button type="button" data-target-step="-1" aria-label="Kurangi hari target" class="flex size-10 items-center justify-center rounded-l-xl text-slate-600 transition hover:bg-slate-50">
                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" d="M5 12h14" /></svg>
                    </button>
                    <input name="hari_target_stok" data-target-input type="number" value="{{ old('hari_target_stok', $hariTargetStok) }}" min="1" max="65535" step="1" required aria-label="Target stok dalam hari" class="w-12 border-0 bg-transparent p-0 text-center text-sm font-bold tabular-nums text-slate-900 outline-none focus:ring-0">
                    <span class="pr-1 text-xs text-slate-500">hari</span>
                    <button type="button" data-target-step="1" aria-label="Tambah hari target" class="flex size-10 items-center justify-center rounded-r-xl text-slate-600 transition hover:bg-slate-50">
                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" d="M12 5v14m-7-7h14" /></svg>
                    </button>
                </div>
            </div>
            @error('hari_target_stok') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
            <button type="submit" class="mt-3 w-full rounded-xl bg-slate-100 px-3 py-2.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-200">Simpan target</button>
        </form>
    </section>

    <section class="mt-4 rounded-2xl bg-brand-700 p-5 text-white shadow-lg shadow-brand-900/10">
        <div class="flex items-start justify-between gap-3">
            <div>
                <p class="text-sm font-medium text-brand-100">Estimasi belanja</p>
                <p class="mt-2 text-2xl font-bold tracking-tight tabular-nums" data-shopping-total>Rp{{ number_format($total_biaya, 0, ',', '.') }}</p>
                <p class="mt-1 text-xs text-brand-100" data-shopping-total-label>Total untuk {{ count($rekomendasi) }} barang</p>
            </div>
            <span class="flex size-11 shrink-0 items-center justify-center rounded-2xl bg-white/15">
                <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14l1 12H4L5 8Zm3 0V6a4 4 0 0 1 8 0v2m-4 4v4m-2-2h4" /></svg>
            </span>
        </div>
    </section>

    <div class="mt-6 flex items-end justify-between gap-3">
        <div>
            <h2 class="text-base font-bold text-slate-900">Perlu dibeli</h2>
            <p class="mt-0.5 text-xs text-slate-500">Centang barang yang sudah dibeli</p>
        </div>
        <span class="text-xs font-medium text-slate-400">{{ count($rekomendasi) }} barang</span>
    </div>

    @if ($rekomendasi === [])
        <div class="mt-3 rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-5 py-9 text-center">
            <span class="mx-auto flex size-11 items-center justify-center rounded-2xl bg-white text-brand-600 shadow-sm">
                <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6" /></svg>
            </span>
            <h3 class="mt-3 text-sm font-semibold text-slate-800">Stok masih mencukupi</h3>
            <p class="mt-1 text-xs text-slate-500">Belum ada rekomendasi restock untuk target {{ $hariTargetStok }} hari.</p>
        </div>
    @else
        <div data-shopping-list data-user="{{ auth()->id() }}" data-date="{{ now()->toDateString() }}" class="mt-3 space-y-3">
            @foreach ($rekomendasi as $item)
                <article data-shopping-item data-item-id="{{ $item['barang']->id }}" data-cost="{{ $item['biaya'] }}" class="rounded-2xl border border-slate-100 bg-white p-4 shadow-sm transition">
                    <div class="flex items-start gap-3">
                        <input type="checkbox" data-purchased class="mt-1 size-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500" aria-label="Sudah dibeli: {{ $item['barang']->nama }}">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <h3 data-item-name class="truncate text-sm font-semibold text-slate-900">{{ $item['barang']->nama }}</h3>
                                    <p class="mt-1 text-xs text-slate-500">{{ $item['alasan'] }}</p>
                                </div>
                                <x-status-chip :status="$item['barang']->status" />
                            </div>
                            <div class="mt-3 flex items-end justify-between border-t border-slate-100 pt-3">
                                <p class="text-xs text-slate-500">Beli
                                    <span class="font-semibold text-slate-800">{{ $item['jumlah_beli'] }} {{ $item['barang']->satuan }}</span>
                                </p>
                                <p class="text-sm font-bold tabular-nums text-slate-900">Rp{{ number_format($item['biaya'], 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                    <p data-purchased-label class="mt-2 hidden pl-7 text-xs font-medium text-brand-700">Sudah dibeli</p>
                </article>
            @endforeach
        </div>
    @endif

    <a href="{{ route('barang') }}" class="mt-5 flex items-center justify-center rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">Lihat semua barang</a>
</x-mobile-layout>
