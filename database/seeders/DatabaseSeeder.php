<?php

namespace Database\Seeders;

use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'sari@warungberkah.test'],
            [
                'name' => 'Bu Sari',
                'nama_warung' => 'Warung Berkah',
                'password' => Hash::make('password'),
            ],
        );

        $barangData = [
            ['nama' => 'Indomie Goreng', 'satuan' => 'pcs', 'harga_beli' => 2800, 'harga_jual' => 3500, 'stok' => 6, 'stok_minimum' => 10],
            ['nama' => 'Telur Ayam', 'satuan' => 'kg', 'harga_beli' => 28000, 'harga_jual' => 31000, 'stok' => 1, 'stok_minimum' => 2],
            ['nama' => 'Minyak Goreng 1 L', 'satuan' => 'liter', 'harga_beli' => 17000, 'harga_jual' => 19000, 'stok' => 2, 'stok_minimum' => 3],
            ['nama' => 'Gula Pasir', 'satuan' => 'kg', 'harga_beli' => 16000, 'harga_jual' => 18000, 'stok' => 1, 'stok_minimum' => 2],
            ['nama' => 'Kopi Sachet', 'satuan' => 'renceng', 'harga_beli' => 12000, 'harga_jual' => 14000, 'stok' => 1, 'stok_minimum' => 2],
            ['nama' => 'Aqua 600 ml', 'satuan' => 'botol', 'harga_beli' => 2600, 'harga_jual' => 3500, 'stok' => 0, 'stok_minimum' => 6],
            ['nama' => 'Beras 5 kg', 'satuan' => 'karung', 'harga_beli' => 65000, 'harga_jual' => 72000, 'stok' => 8, 'stok_minimum' => 3],
            ['nama' => 'Gas LPG 3 kg', 'satuan' => 'tabung', 'harga_beli' => 22000, 'harga_jual' => 24000, 'stok' => 10, 'stok_minimum' => 3],
        ];

        $barang = [];
        foreach ($barangData as $data) {
            $barang[$data['nama']] = $user->barangs()->updateOrCreate(
                ['nama' => $data['nama']],
                $data,
            );
        }

        $user->pengaturan()->firstOrCreate([], ['hari_target_stok' => 3]);

        $rataRataKeluarHarian = [
            'Indomie Goreng' => 12,
            'Telur Ayam' => 2,
            'Minyak Goreng 1 L' => 3,
            'Gula Pasir' => 1.5,
            'Kopi Sachet' => 2,
            'Aqua 600 ml' => 6,
        ];

        foreach ($rataRataKeluarHarian as $nama => $rataRata) {
            for ($hari = 0; $hari < 14; $hari++) {
                $jumlah = $nama === 'Gula Pasir'
                    ? ($hari % 2 === 0 ? 1 : 2)
                    : $rataRata;
                $tanggal = now()->startOfDay()->subDays($hari);

                Transaksi::updateOrCreate(
                    [
                        'barang_id' => $barang[$nama]->id,
                        'jenis' => 'keluar',
                        'tanggal' => $tanggal,
                    ],
                    ['jumlah' => $jumlah],
                );
            }
        }
    }
}
