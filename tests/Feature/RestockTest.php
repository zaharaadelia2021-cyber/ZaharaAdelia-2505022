<?php

use App\Models\User;
use App\Services\RestockService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('seeded sales produce the six expected restock recommendations and total', function () {
    $this->seed();
    $user = User::where('email', 'sari@warungberkah.test')->firstOrFail();

    $overview = app(RestockService::class)->overview($user, 3);
    $recommendations = collect($overview['rekomendasi'])
        ->keyBy(fn (array $item): string => $item['barang']->nama);

    expect($recommendations)->toHaveCount(6)
        ->and($overview['total_biaya'])->toBe(513800)
        ->and($recommendations['Indomie Goreng']['jumlah_beli'])->toBe(30)
        ->and($recommendations['Telur Ayam']['jumlah_beli'])->toBe(5)
        ->and($recommendations['Minyak Goreng 1 L']['jumlah_beli'])->toBe(7)
        ->and($recommendations['Gula Pasir']['jumlah_beli'])->toBe(4)
        ->and($recommendations['Kopi Sachet']['jumlah_beli'])->toBe(5)
        ->and($recommendations['Aqua 600 ml']['jumlah_beli'])->toBe(18)
        ->and($recommendations['Indomie Goreng']['alasan'])->toBe('Rata-rata terjual 12/hari, sisa 6')
        ->and($recommendations->has('Beras 5 kg'))->toBeFalse()
        ->and($recommendations->has('Gas LPG 3 kg'))->toBeFalse();
});

test('shopping screen shows recommendations and its target can be updated', function () {
    $this->seed();
    $user = User::where('email', 'sari@warungberkah.test')->firstOrFail();

    $this->actingAs($user)
        ->get(route('belanja'))
        ->assertOk()
        ->assertSee('Estimasi belanja')
        ->assertSee('Rp513.800')
        ->assertSee('30 pcs')
        ->assertSee('5 kg')
        ->assertSee('7 liter')
        ->assertSee('4 kg')
        ->assertSee('5 renceng')
        ->assertSee('18 botol')
        ->assertSee('Sudah dibeli');

    $this->post(route('belanja.target.update'), ['hari_target_stok' => 4])
        ->assertRedirect(route('belanja'))
        ->assertSessionHasNoErrors();

    expect($user->pengaturan()->value('hari_target_stok'))->toBe(4);
});

test('home shows restock summary and the three most critical low stock items', function () {
    $this->seed();
    $user = User::where('email', 'sari@warungberkah.test')->firstOrFail();

    $this->actingAs($user)
        ->get(route('home'))
        ->assertOk()
        ->assertSee('Halo, Bu Sari')
        ->assertSee('Rekomendasi belanja')
        ->assertSee('Rp513.800')
        ->assertSee('>5</p>', false)
        ->assertSee('>1</p>', false)
        ->assertSee('Barang menipis')
        ->assertSee('Barang habis')
        ->assertSee('Catat transaksi')
        ->assertSee('Perlu perhatian')
        ->assertSee('Aqua 600 ml')
        ->assertSee('Indomie Goreng')
        ->assertSee('Kopi Sachet')
        ->assertDontSee('Gula Pasir');
});

test('restock average uses only outgoing transactions from the last seven calendar days', function () {
    $user = User::factory()->create();
    $barang = $user->barangs()->create([
        'nama' => 'Tepung',
        'satuan' => 'kg',
        'harga_beli' => 100,
        'harga_jual' => 150,
        'stok' => 2,
        'stok_minimum' => 1,
    ]);

    $barang->transaksis()->create([
        'jenis' => 'keluar',
        'jumlah' => 14,
        'tanggal' => now()->subDays(6)->toDateString(),
    ]);
    $barang->transaksis()->create([
        'jenis' => 'keluar',
        'jumlah' => 700,
        'tanggal' => now()->subDays(7)->toDateString(),
    ]);
    $barang->transaksis()->create([
        'jenis' => 'masuk',
        'jumlah' => 700,
        'tanggal' => now()->toDateString(),
    ]);
    $barang->transaksis()->create([
        'jenis' => 'keluar',
        'jumlah' => 700,
        'tanggal' => now()->addDay()->toDateString(),
    ]);

    $recommendation = app(RestockService::class)->overview($user, 3)['rekomendasi'][0];

    expect($recommendation['rata_rata_harian'])->toBe(2.0)
        ->and($recommendation['jumlah_beli'])->toBe(4)
        ->and($recommendation['biaya'])->toBe(400);
});

test('target stok must be a positive whole number within the settings column range', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->from(route('belanja'))
        ->post(route('belanja.target.update'), ['hari_target_stok' => 0])
        ->assertRedirect(route('belanja'))
        ->assertSessionHasErrors('hari_target_stok');

    expect($user->pengaturan()->exists())->toBeFalse();
});
