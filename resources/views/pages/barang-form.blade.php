<x-mobile-layout :title="$mode === 'create' ? 'Tambah barang' : 'Ubah barang'" active-tab="barang">
    <a href="{{ route('barang') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-slate-500 transition hover:text-brand-700">
        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m15 18-6-6 6-6M9 12h11" /></svg>
        Kembali ke barang
    </a>
    <div class="mt-5">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">{{ $mode === 'create' ? 'Tambah barang' : 'Ubah barang' }}</h1>
        <p class="mt-1 text-sm text-slate-500">{{ $mode === 'create' ? 'Lengkapi informasi barang yang ingin dicatat.' : 'Perbarui informasi dan stok barang.' }}</p>
    </div>

    <form method="POST" action="{{ $mode === 'create' ? route('barang.store') : route('barang.update', $barang) }}" class="mt-6 space-y-4 rounded-2xl border border-slate-100 bg-white p-4 shadow-sm">
        @csrf
        @if ($mode === 'edit')
            @method('PUT')
        @endif

        <div>
            <label for="nama" class="mb-1.5 block text-sm font-medium text-slate-700">Nama barang</label>
            <input id="nama" name="nama" type="text" value="{{ old('nama', $barang->nama) }}" required maxlength="255" autofocus placeholder="Contoh: Minyak goreng" class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-brand-500 focus:ring-4 focus:ring-brand-50">
            @error('nama') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="satuan" class="mb-1.5 block text-sm font-medium text-slate-700">Satuan</label>
            <input id="satuan" name="satuan" type="text" value="{{ old('satuan', $barang->satuan) }}" required maxlength="255" placeholder="Contoh: botol, kg, pcs" class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-brand-500 focus:ring-4 focus:ring-brand-50">
            @error('satuan') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label for="harga_beli" class="mb-1.5 block text-sm font-medium text-slate-700">Harga beli (Rp)</label>
                <input id="harga_beli" name="harga_beli" type="number" value="{{ old('harga_beli', $barang->harga_beli) }}" required min="0" step="1" placeholder="0" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-3 text-sm text-slate-900 outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-50">
                @error('harga_beli') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="harga_jual" class="mb-1.5 block text-sm font-medium text-slate-700">Harga jual (Rp)</label>
                <input id="harga_jual" name="harga_jual" type="number" value="{{ old('harga_jual', $barang->harga_jual) }}" required min="0" step="1" placeholder="0" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-3 text-sm text-slate-900 outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-50">
                @error('harga_jual') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label for="stok" class="mb-1.5 block text-sm font-medium text-slate-700">Stok saat ini</label>
                <input id="stok" name="stok" type="number" value="{{ old('stok', $barang->stok ?? '0') }}" required min="0" max="9999999999.99" step="0.01" placeholder="0" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-3 text-sm text-slate-900 outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-50">
                @error('stok') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="stok_minimum" class="mb-1.5 block text-sm font-medium text-slate-700">Stok minimum</label>
                <input id="stok_minimum" name="stok_minimum" type="number" value="{{ old('stok_minimum', $barang->stok_minimum ?? '0') }}" required min="0" max="9999999999.99" step="0.01" placeholder="0" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-3 text-sm text-slate-900 outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-50">
                @error('stok_minimum') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-xl bg-brand-600 px-4 py-3.5 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700 focus:outline-none focus:ring-4 focus:ring-brand-100">
            {{ $mode === 'create' ? 'Simpan barang' : 'Simpan perubahan' }}
        </button>
    </form>
</x-mobile-layout>
