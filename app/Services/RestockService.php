<?php

namespace App\Services;

use App\Models\Transaksi;
use App\Models\User;

class RestockService
{
    public function overview(User $user, int $hariTargetStok): array
    {
        $today = now();
        $from = $today->copy()->subDays(6)->toDateString();
        $today = $today->toDateString();
        $totalKeluar = Transaksi::query()
            ->join('barang', 'transaksi.barang_id', '=', 'barang.id')
            ->where('barang.user_id', $user->id)
            ->where('transaksi.jenis', 'keluar')
            ->whereDate('transaksi.tanggal', '>=', $from)
            ->whereDate('transaksi.tanggal', '<=', $today)
            ->groupBy('transaksi.barang_id')
            ->selectRaw('transaksi.barang_id, SUM(transaksi.jumlah) AS total_keluar')
            ->pluck('total_keluar', 'transaksi.barang_id');

        $recommendations = [];
        $attention = [];
        $counts = ['menipis' => 0, 'habis' => 0];

        foreach ($user->barangs()->orderBy('nama')->get() as $barang) {
            $averageDaily = (float) ($totalKeluar[$barang->id] ?? 0) / 7;
            $stock = (float) $barang->stok;
            $status = $barang->status;

            if ($status === 'Menipis') {
                $counts['menipis']++;
            } elseif ($status === 'Habis') {
                $counts['habis']++;
            }

            if ($status !== 'Aman') {
                $attention[] = [
                    'barang' => $barang,
                    'status' => $status,
                    'hari_tersisa' => $stock <= 0
                        ? 0
                        : ($averageDaily > 0 ? $stock / $averageDaily : PHP_FLOAT_MAX),
                ];
            }

            $jumlahBeli = (int) max(0, ceil(($averageDaily * $hariTargetStok) - $stock - 1e-10));

            if ($jumlahBeli === 0) {
                continue;
            }

            $recommendations[] = [
                'barang' => $barang,
                'rata_rata_harian' => $averageDaily,
                'jumlah_beli' => $jumlahBeli,
                'biaya' => $jumlahBeli * $barang->harga_beli,
                'alasan' => sprintf(
                    'Rata-rata terjual %s/hari, sisa %s',
                    $this->formatNumber($averageDaily),
                    $this->formatNumber($stock),
                ),
                'hari_tersisa' => $stock <= 0
                    ? 0
                    : ($averageDaily > 0 ? $stock / $averageDaily : PHP_FLOAT_MAX),
            ];
        }

        usort($recommendations, fn (array $a, array $b): int => $a['hari_tersisa'] <=> $b['hari_tersisa']
            ?: strcmp($a['barang']->nama, $b['barang']->nama));
        usort($attention, fn (array $a, array $b): int => $a['hari_tersisa'] <=> $b['hari_tersisa']
            ?: strcmp($a['barang']->nama, $b['barang']->nama));

        return [
            'rekomendasi' => $recommendations,
            'total_biaya' => array_sum(array_column($recommendations, 'biaya')),
            'counts' => $counts,
            'perhatian' => array_slice($attention, 0, 3),
        ];
    }

    private function formatNumber(float $number): string
    {
        return rtrim(rtrim(number_format($number, 2, ',', '.'), '0'), ',');
    }
}
