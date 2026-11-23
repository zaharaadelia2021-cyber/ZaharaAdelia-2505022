<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function catatanBarangData(array $overrides = []): array
{
    return array_merge([
        'nama' => 'Beras',
        'satuan' => 'kg',
        'harga_beli' => 10000,
        'harga_jual' => 12000,
        'stok' => 10,
        'stok_minimum' => 2,
    ], $overrides);
}

test('catat screen shows owned items and defaults the date to today', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $user->barangs()->create(catatanBarangData());
    $otherUser->barangs()->create(catatanBarangData(['nama' => 'Rahasia']));

    $this->actingAs($user)
        ->get(route('catat'))
        ->assertOk()
        ->assertSee('Terjual')
        ->assertSee('Kulakan')
        ->assertSee('Tambah barang lain')
        ->assertSee('Simpan catatan')
        ->assertSee('value="'.now()->toDateString().'"', false)
        ->assertSee('Beras')
        ->assertDontSee('Rahasia');
});

test('kulakan adds stock and creates one incoming transaction per selected item', function () {
    $user = User::factory()->create();
    $beras = $user->barangs()->create(catatanBarangData());
    $kopi = $user->barangs()->create(catatanBarangData(['nama' => 'Kopi', 'stok' => 3]));

    $this->actingAs($user)
        ->post(route('catat.store'), [
            'jenis' => 'kulakan',
            'tanggal' => '2026-11-22',
            'items' => [
                ['barang_id' => $beras->id, 'jumlah' => '2'],
                ['barang_id' => $kopi->id, 'jumlah' => '1.50'],
            ],
        ])
        ->assertRedirect(route('catat'))
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('barang', ['id' => $beras->id, 'stok' => '12.00']);
    $this->assertDatabaseHas('barang', ['id' => $kopi->id, 'stok' => '4.50']);
    $this->assertDatabaseHas('transaksi', [
        'barang_id' => $beras->id,
        'jenis' => 'masuk',
        'jumlah' => '2.00',
        'tanggal' => '2026-11-22 00:00:00',
    ]);
    $this->assertDatabaseHas('transaksi', [
        'barang_id' => $kopi->id,
        'jenis' => 'masuk',
        'jumlah' => '1.50',
        'tanggal' => '2026-11-22 00:00:00',
    ]);
    $this->assertDatabaseCount('transaksi', 2);
});

test('terjual subtracts stock and creates an outgoing transaction', function () {
    $user = User::factory()->create();
    $barang = $user->barangs()->create(catatanBarangData());

    $this->actingAs($user)
        ->post(route('catat.store'), [
            'jenis' => 'terjual',
            'tanggal' => '2026-11-23',
            'items' => [
                ['barang_id' => $barang->id, 'jumlah' => '2.5'],
            ],
        ])
        ->assertRedirect(route('catat'))
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('barang', ['id' => $barang->id, 'stok' => '7.50']);
    $this->assertDatabaseHas('transaksi', [
        'barang_id' => $barang->id,
        'jenis' => 'keluar',
        'jumlah' => '2.50',
        'tanggal' => '2026-11-23 00:00:00',
    ]);
});

test('terjual subtracts stock and rejects insufficient stock without partial records', function () {
    $user = User::factory()->create();
    $beras = $user->barangs()->create(catatanBarangData());
    $kopi = $user->barangs()->create(catatanBarangData(['nama' => 'Kopi', 'stok' => 1]));

    $this->actingAs($user)
        ->from(route('catat'))
        ->post(route('catat.store'), [
            'jenis' => 'terjual',
            'tanggal' => '2026-11-23',
            'items' => [
                ['barang_id' => $beras->id, 'jumlah' => '2'],
                ['barang_id' => $kopi->id, 'jumlah' => '2'],
            ],
        ])
        ->assertRedirect(route('catat'))
        ->assertSessionHasErrors('items');

    $this->assertDatabaseHas('barang', ['id' => $beras->id, 'stok' => '10.00']);
    $this->assertDatabaseHas('barang', ['id' => $kopi->id, 'stok' => '1.00']);
    $this->assertDatabaseCount('transaksi', 0);

    $this->get(route('catat'))->assertSee('Stok Kopi tidak cukup');
});

test('catatan validation rejects other users items and duplicate barang selections', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $barang = $user->barangs()->create(catatanBarangData());
    $otherBarang = $otherUser->barangs()->create(catatanBarangData(['nama' => 'Punya orang lain']));

    $this->actingAs($user)
        ->from(route('catat'))
        ->post(route('catat.store'), [
            'jenis' => 'terjual',
            'tanggal' => '2026-11-23',
            'items' => [
                ['barang_id' => $otherBarang->id, 'jumlah' => '1'],
            ],
        ])
        ->assertRedirect(route('catat'))
        ->assertSessionHasErrors('items.0.barang_id');

    $this->post(route('catat.store'), [
        'jenis' => 'terjual',
        'tanggal' => '2026-11-23',
        'items' => [
            ['barang_id' => $barang->id, 'jumlah' => '1'],
            ['barang_id' => $barang->id, 'jumlah' => '1'],
        ],
    ])->assertSessionHasErrors('items.1.barang_id');

    $this->assertDatabaseCount('transaksi', 0);
});
