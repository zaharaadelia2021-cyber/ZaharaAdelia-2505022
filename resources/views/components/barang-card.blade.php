@props(['barang'])

<article class="rounded-2xl border border-slate-100 bg-white p-4 shadow-sm">
    <div class="flex items-start justify-between gap-3">
        <div class="min-w-0">
            <h2 class="truncate text-base font-semibold text-slate-900">{{ $barang->nama }}</h2>
            <p class="mt-1 text-xs text-slate-500">Rp{{ number_format($barang->harga_jual, 0, ',', '.') }} / {{ $barang->satuan }}</p>
        </div>
        <x-status-chip :status="$barang->status" />
    </div>

    <div class="mt-4 flex items-end justify-between border-t border-slate-100 pt-3">
        <div>
            <p class="text-xs text-slate-500">Stok tersedia</p>
            <p class="mt-0.5 text-lg font-bold tabular-nums text-slate-900">
                {{ rtrim(rtrim(number_format((float) $barang->stok, 2, ',', '.'), '0'), ',') }}
                <span class="text-xs font-medium text-slate-500">{{ $barang->satuan }}</span>
            </p>
            <p class="mt-0.5 text-[11px] text-slate-400">Minimum {{ rtrim(rtrim(number_format((float) $barang->stok_minimum, 2, ',', '.'), '0'), ',') }} {{ $barang->satuan }}</p>
        </div>
        <div class="flex items-center gap-1">
            <a href="{{ route('barang.edit', $barang) }}" class="rounded-lg px-3 py-2 text-xs font-semibold text-brand-700 transition hover:bg-brand-50">Ubah</a>
            <form method="POST" action="{{ route('barang.destroy', $barang) }}" onsubmit="return confirm('Hapus barang ini dari daftar?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="rounded-lg px-3 py-2 text-xs font-semibold text-red-600 transition hover:bg-red-50">Hapus</button>
            </form>
        </div>
    </div>
</article>
