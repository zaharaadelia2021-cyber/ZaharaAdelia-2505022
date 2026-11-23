<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCatatanRequest;
use App\Models\Barang;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CatatController extends Controller
{
    public function create(): View
    {
        return view('pages.catat', [
            'barangs' => auth()->user()->barangs()->orderBy('nama')->get(),
            'tanggal' => now()->toDateString(),
        ]);
    }

    public function store(StoreCatatanRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $user = $request->user();

        DB::transaction(function () use ($validated, $user): void {
            $barangIds = array_column($validated['items'], 'barang_id');
            $barangs = $user->barangs()
                ->whereKey($barangIds)
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            if ($barangs->count() !== count($barangIds)) {
                throw ValidationException::withMessages([
                    'items' => 'Salah satu barang sudah tidak tersedia. Muat ulang halaman lalu coba lagi.',
                ]);
            }

            foreach ($validated['items'] as $item) {
                /** @var Barang $barang */
                $barang = $barangs->get($item['barang_id']);
                $jumlah = $item['jumlah'];
                $jenis = $validated['jenis'] === 'kulakan' ? 'masuk' : 'keluar';

                if ($jenis === 'keluar') {
                    $updated = Barang::query()
                        ->whereKey($barang->id)
                        ->where('user_id', $user->id)
                        ->where('stok', '>=', $jumlah)
                        ->decrement('stok', $jumlah);

                    if ($updated === 0) {
                        throw ValidationException::withMessages([
                            'items' => sprintf(
                                'Stok %s tidak cukup. Tersedia %s, tetapi yang dicatat %s.',
                                $barang->nama,
                                $this->formatQuantity($barang->stok),
                                $this->formatQuantity($jumlah),
                            ),
                        ]);
                    }
                } else {
                    $updated = Barang::query()
                        ->whereKey($barang->id)
                        ->where('user_id', $user->id)
                        ->whereRaw('stok + ? <= ?', [$jumlah, '9999999999.99'])
                        ->increment('stok', $jumlah);

                    if ($updated === 0) {
                        throw ValidationException::withMessages([
                            'items' => sprintf('Penambahan stok %s melebihi kapasitas yang diperbolehkan.', $barang->nama),
                        ]);
                    }
                }

                $barang->transaksis()->create([
                    'jenis' => $jenis,
                    'jumlah' => $jumlah,
                    'tanggal' => $validated['tanggal'],
                ]);
            }
        });

        return redirect()->route('catat')->with('success', 'Catatan berhasil disimpan dan stok telah diperbarui.');
    }

    private function formatQuantity(string|int|float $quantity): string
    {
        return rtrim(rtrim(number_format((float) $quantity, 2, ',', '.'), '0'), ',');
    }
}
