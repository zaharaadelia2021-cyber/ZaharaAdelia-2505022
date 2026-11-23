<?php

use App\Models\Barang;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function barangData(array $overrides = []): array
{
    return array_merge([
        'nama' => 'Beras',
        'satuan' => 'kg',
        'harga_beli' => 10000,
        'harga_jual' => 12000,
        'stok' => 5,
        'stok_minimum' => 2,
    ], $overrides);
}

test('barang status is derived from stock and minimum stock', function () {
    expect((new Barang(barangData(['stok' => 0, 'stok_minimum' => 2])))->status)->toBe('Habis')
        ->and((new Barang(barangData(['stok' => 2, 'stok_minimum' => 2])))->status)->toBe('Menipis')
        ->and((new Barang(barangData(['stok' => 0.5, 'stok_minimum' => 2])))->status)->toBe('Menipis')
        ->and((new Barang(barangData(['stok' => 3, 'stok_minimum' => 2])))->status)->toBe('Aman');
});

test('barang screen lists only the signed-in user and filters with total status counts', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $user->barangs()->create(barangData(['nama' => 'Beras', 'stok' => 2, 'stok_minimum' => 2]));
    $user->barangs()->create(barangData(['nama' => 'Kopi', 'stok' => 0]));
    $user->barangs()->create(barangData(['nama' => 'Gula', 'stok' => 8]));
    $otherUser->barangs()->create(barangData(['nama' => 'Barang Rahasia']));

    $this->actingAs($user)
        ->get(route('barang'))
        ->assertOk()
        ->assertSee('Beras')
        ->assertSee('Kopi')
        ->assertSee('Gula')
        ->assertDontSee('Barang Rahasia')
        ->assertSeeInOrder(['Semua', '3', 'Menipis', '1', 'Habis', '1'])
        ->assertSee('Menipis')
        ->assertSee('Habis')
        ->assertSee('Aman');

    $this->get(route('barang', ['status' => 'menipis', 'q' => 'beras']))
        ->assertOk()
        ->assertSee('Beras')
        ->assertDontSee('Kopi')
        ->assertDontSee('Gula')
        ->assertDontSee('Barang Rahasia');
});

test('owner can create update and delete their barang', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->post(route('barang.store'), barangData(['nama' => 'Minyak']))
        ->assertRedirect(route('barang'))
        ->assertSessionHasNoErrors();

    $barang = $user->barangs()->where('nama', 'Minyak')->firstOrFail();
    $this->assertDatabaseHas('barang', ['id' => $barang->id, 'user_id' => $user->id]);

    $this->put(route('barang.update', $barang), barangData(['nama' => 'Minyak baru', 'stok' => 0]))
        ->assertRedirect(route('barang'))
        ->assertSessionHasNoErrors();
    $this->assertDatabaseHas('barang', ['id' => $barang->id, 'nama' => 'Minyak baru']);

    $this->delete(route('barang.destroy', $barang))
        ->assertRedirect(route('barang'));
    $this->assertDatabaseMissing('barang', ['id' => $barang->id]);
});

test('barang forms validate input and enforce names unique per user', function () {
    $user = User::factory()->create();
    $user->barangs()->create(barangData());

    $this->actingAs($user)
        ->from(route('barang.create'))
        ->post(route('barang.store'), barangData())
        ->assertRedirect(route('barang.create'))
        ->assertSessionHasErrors('nama');

    $this->post(route('barang.store'), barangData(['nama' => 'Sabun', 'stok' => -1]))
        ->assertSessionHasErrors('stok');

    $this->post(route('barang.store'), barangData(['nama' => 'Sabun', 'stok' => '1.234']))
        ->assertSessionHasErrors('stok');
});

test('users cannot view edit update or delete another users barang', function () {
    $owner = User::factory()->create();
    $barang = $owner->barangs()->create(barangData());
    $this->actingAs(User::factory()->create());

    $this->get(route('barang.edit', $barang))->assertForbidden();
    $this->put(route('barang.update', $barang), barangData(['nama' => 'Diubah']))->assertForbidden();
    $this->delete(route('barang.destroy', $barang))->assertForbidden();

    $this->assertDatabaseHas('barang', ['id' => $barang->id, 'nama' => 'Beras']);
});
