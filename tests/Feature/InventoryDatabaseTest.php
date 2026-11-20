<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('database seeder creates the warung and 14-day sales history idempotently', function () {
    $this->seed();

    $user = User::where('email', 'sari@warungberkah.test')->firstOrFail();

    expect($user->nama_warung)->toBe('Warung Berkah')
        ->and($user->barangs)->toHaveCount(8)
        ->and($user->pengaturan->hari_target_stok)->toBe(3);

    $rataRataKeluarHarian = [
        'Indomie Goreng' => 12,
        'Telur Ayam' => 2,
        'Minyak Goreng 1 L' => 3,
        'Gula Pasir' => 1.5,
        'Kopi Sachet' => 2,
        'Aqua 600 ml' => 6,
    ];

    foreach ($rataRataKeluarHarian as $nama => $rataRata) {
        $transaksi = $user->barangs()
            ->where('nama', $nama)
            ->firstOrFail()
            ->transaksis()
            ->where('jenis', 'keluar');

        expect($transaksi->count())->toBe(14)
            ->and((float) $transaksi->avg('jumlah'))->toBe((float) $rataRata);
    }

    $this->seed();

    expect($user->barangs()->count())->toBe(8)
        ->and($user->barangs()->withCount('transaksis')->get()->sum('transaksis_count'))->toBe(84);
});
