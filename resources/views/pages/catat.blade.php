<x-mobile-layout title="Catat" active-tab="catat">
    <div>
        <p class="text-sm font-medium text-brand-700">{{ auth()->user()->nama_warung }}</p>
        <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">Catat transaksi</h1>
        <p class="mt-1 text-sm text-slate-500">Catat barang terjual atau kulakan.</p>
    </div>

    @if (session('success'))
        <p role="status" class="mt-5 rounded-xl border border-brand-100 bg-brand-50 px-4 py-3 text-sm font-medium text-brand-800">{{ session('success') }}</p>
    @endif

    @if ($barangs->isEmpty())
        <div class="mt-6 rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-5 py-10 text-center">
            <h2 class="text-sm font-semibold text-slate-800">Belum ada barang untuk dicatat</h2>
            <p class="mt-1 text-xs leading-5 text-slate-500">Tambahkan barang terlebih dahulu agar stok dan transaksinya bisa dicatat.</p>
            <a href="{{ route('barang.create') }}" class="mt-4 inline-flex rounded-xl bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-700">Tambah barang</a>
        </div>
    @else
        @php
            $items = old('items');
            if (! is_array($items) || $items === []) {
                $items = [['barang_id' => '', 'jumlah' => '1']];
            }
        @endphp

        <form id="catatan-form" data-catatan-form method="POST" action="{{ route('catat.store') }}" class="mt-5 space-y-5">
            @csrf

            <section class="rounded-2xl border border-slate-100 bg-white p-4 shadow-sm">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="jenis" class="mb-1.5 block text-xs font-semibold text-slate-600">Jenis catatan</label>
                        <select id="jenis" name="jenis" data-jenis required class="w-full rounded-xl border border-slate-200 bg-white px-3 py-3 text-sm font-medium text-slate-800 outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-50">
                            <option value="terjual" @selected(old('jenis', 'terjual') === 'terjual')>Terjual</option>
                            <option value="kulakan" @selected(old('jenis') === 'kulakan')>Kulakan</option>
                        </select>
                        @error('jenis') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="tanggal" class="mb-1.5 block text-xs font-semibold text-slate-600">Tanggal</label>
                        <input id="tanggal" name="tanggal" type="date" value="{{ old('tanggal', $tanggal) }}" required class="w-full rounded-xl border border-slate-200 bg-white px-3 py-3 text-sm text-slate-800 outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-50">
                        @error('tanggal') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </section>

            @error('items')
                <p role="alert" class="rounded-xl border border-red-100 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">{{ $message }}</p>
            @enderror

            <section>
                <div class="mb-3 flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-slate-800">Daftar barang</h2>
                    <span class="text-xs text-slate-400">Pilih barang dan jumlah</span>
                </div>

                <div data-items class="space-y-3">
                    @foreach ($items as $index => $item)
                        <article data-item-row class="rounded-2xl border border-slate-100 bg-white p-4 shadow-sm">
                            <div class="flex items-start gap-3">
                                <div class="min-w-0 flex-1">
                                    <label for="barang-{{ $index }}" class="mb-1.5 block text-xs font-semibold text-slate-600">Nama barang</label>
                                    <select id="barang-{{ $index }}" data-field="barang_id" name="items[{{ $index }}][barang_id]" required class="w-full rounded-xl border border-slate-200 bg-white px-3 py-3 text-sm text-slate-800 outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-50">
                                        <option value="">Pilih barang</option>
                                        @foreach ($barangs as $barang)
                                            <option value="{{ $barang->id }}" data-harga-beli="{{ $barang->harga_beli }}" data-harga-jual="{{ $barang->harga_jual }}" @selected((string) ($item['barang_id'] ?? '') === (string) $barang->id)>
                                                {{ $barang->nama }} · stok {{ rtrim(rtrim(number_format((float) $barang->stok, 2, ',', '.'), '0'), ',') }} {{ $barang->satuan }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error("items.$index.barang_id") <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>
                                <button type="button" data-remove-item aria-label="Hapus baris barang" class="mt-6 hidden rounded-lg p-2 text-slate-400 transition hover:bg-red-50 hover:text-red-600">
                                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" d="M5 12h14" /></svg>
                                </button>
                            </div>
                            <div class="mt-4 flex items-center justify-between gap-3 border-t border-slate-100 pt-3">
                                <div>
                                    <label for="jumlah-{{ $index }}" class="block text-xs font-medium text-slate-500">Jumlah</label>
                                    @error("items.$index.jumlah") <p class="mb-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>
                                <div class="flex items-center rounded-xl border border-slate-200 bg-white">
                                    <button type="button" data-quantity-step="-1" aria-label="Kurangi jumlah" class="flex size-10 items-center justify-center rounded-l-xl text-slate-600 transition hover:bg-slate-50">
                                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" d="M5 12h14" /></svg>
                                    </button>
                                    <input id="jumlah-{{ $index }}" data-field="jumlah" name="items[{{ $index }}][jumlah]" type="number" value="{{ $item['jumlah'] ?? '1' }}" required min="0.01" step="0.01" inputmode="decimal" class="w-16 border-0 bg-transparent px-1 py-2 text-center text-sm font-semibold tabular-nums text-slate-900 outline-none focus:ring-0">
                                    <button type="button" data-quantity-step="1" aria-label="Tambah jumlah" class="flex size-10 items-center justify-center rounded-r-xl text-slate-600 transition hover:bg-slate-50">
                                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" d="M12 5v14m-7-7h14" /></svg>
                                    </button>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <button type="button" data-add-item class="mt-3 inline-flex w-full items-center justify-center gap-2 rounded-xl border border-dashed border-brand-300 bg-brand-50/50 px-4 py-3 text-sm font-semibold text-brand-700 transition hover:bg-brand-50">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" d="M12 5v14m-7-7h14" /></svg>
                    Tambah barang lain
                </button>
            </section>

            <section class="rounded-2xl border border-brand-100 bg-brand-50/70 p-4">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-xs font-medium text-brand-800">Total nilai</p>
                        <p data-total-hint class="mt-0.5 text-[11px] text-brand-700">Berdasarkan harga jual</p>
                    </div>
                    <p data-total class="text-xl font-bold tabular-nums text-brand-800" aria-live="polite">Rp0</p>
                </div>
            </section>

            <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-xl bg-brand-600 px-4 py-3.5 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700 focus:outline-none focus:ring-4 focus:ring-brand-100">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6" /></svg>
                Simpan catatan
            </button>
        </form>

        <template id="catatan-item-template">
            <article data-item-row class="rounded-2xl border border-slate-100 bg-white p-4 shadow-sm">
                <div class="flex items-start gap-3">
                    <div class="min-w-0 flex-1">
                        <label class="mb-1.5 block text-xs font-semibold text-slate-600">Nama barang</label>
                        <select data-field="barang_id" required class="w-full rounded-xl border border-slate-200 bg-white px-3 py-3 text-sm text-slate-800 outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-50">
                            <option value="">Pilih barang</option>
                            @foreach ($barangs as $barang)
                                <option value="{{ $barang->id }}" data-harga-beli="{{ $barang->harga_beli }}" data-harga-jual="{{ $barang->harga_jual }}">
                                    {{ $barang->nama }} · stok {{ rtrim(rtrim(number_format((float) $barang->stok, 2, ',', '.'), '0'), ',') }} {{ $barang->satuan }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="button" data-remove-item aria-label="Hapus baris barang" class="mt-6 rounded-lg p-2 text-slate-400 transition hover:bg-red-50 hover:text-red-600">
                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" d="M5 12h14" /></svg>
                    </button>
                </div>
                <div class="mt-4 flex items-center justify-between gap-3 border-t border-slate-100 pt-3">
                    <label class="text-xs font-medium text-slate-500">Jumlah</label>
                    <div class="flex items-center rounded-xl border border-slate-200 bg-white">
                        <button type="button" data-quantity-step="-1" aria-label="Kurangi jumlah" class="flex size-10 items-center justify-center rounded-l-xl text-slate-600 transition hover:bg-slate-50">
                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" d="M5 12h14" /></svg>
                        </button>
                        <input data-field="jumlah" type="number" value="1" required min="0.01" step="0.01" inputmode="decimal" class="w-16 border-0 bg-transparent px-1 py-2 text-center text-sm font-semibold tabular-nums text-slate-900 outline-none focus:ring-0">
                        <button type="button" data-quantity-step="1" aria-label="Tambah jumlah" class="flex size-10 items-center justify-center rounded-r-xl text-slate-600 transition hover:bg-slate-50">
                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" d="M12 5v14m-7-7h14" /></svg>
                        </button>
                    </div>
                </div>
            </article>
        </template>
    @endif
</x-mobile-layout>
