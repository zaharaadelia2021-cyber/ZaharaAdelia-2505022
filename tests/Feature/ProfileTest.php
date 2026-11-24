<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

test('profile shows owner and shop identity and the shared stock target', function () {
    $user = User::factory()->create([
        'name' => 'Sari',
        'nama_warung' => 'Warung Melati',
        'email' => 'sari@example.test',
    ]);
    $user->pengaturan()->create(['hari_target_stok' => 5]);

    $this->actingAs($user)
        ->get(route('profile'))
        ->assertOk()
        ->assertSee('Warung Melati')
        ->assertSee('Sari')
        ->assertSee('sari@example.test')
        ->assertSee('value="5"', false)
        ->assertSee('rata-rata jumlah barang terjual per hari selama 7 hari terakhir')
        ->assertSee('Ubah data warung')
        ->assertSee('Ganti kata sandi')
        ->assertSee('Keluar');
});

test('profile target updates the same setting used by the shopping screen', function () {
    $user = User::factory()->create();
    $this->actingAs($user)
        ->post(route('profile.target.update'), ['hari_target_stok' => 6])
        ->assertRedirect(route('profile'))
        ->assertSessionHasNoErrors();

    expect($user->pengaturan()->value('hari_target_stok'))->toBe(6);

    $this->get(route('belanja'))
        ->assertOk()
        ->assertSee('value="6"', false);
});

test('profile updates store and owner identity and rejects an email used by another account', function () {
    $user = User::factory()->create(['email' => 'owner@example.test']);
    User::factory()->create(['email' => 'taken@example.test']);
    $this->actingAs($user);

    $this->put(route('profile.update'), [
        'nama_warung' => 'Warung Baru',
        'name' => 'Pemilik Baru',
        'email' => 'new@example.test',
    ])->assertRedirect(route('profile'))
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'nama_warung' => 'Warung Baru',
        'name' => 'Pemilik Baru',
        'email' => 'new@example.test',
    ]);

    $this->from(route('profile'))
        ->put(route('profile.update'), [
            'nama_warung' => 'Warung Baru',
            'name' => 'Pemilik Baru',
            'email' => 'taken@example.test',
        ])->assertRedirect(route('profile'))
        ->assertSessionHasErrors('email');
});

test('profile changes password only when the current password and confirmation are valid', function () {
    $user = User::factory()->create(['password' => 'password-lama']);
    $this->actingAs($user);

    $this->from(route('profile'))
        ->put(route('profile.password.update'), [
            'current_password' => 'salah',
            'password' => 'password-baru',
            'password_confirmation' => 'password-baru',
        ])->assertRedirect(route('profile'))
        ->assertSessionHasErrors('current_password');

    expect(Hash::check('password-lama', $user->fresh()->password))->toBeTrue();

    $this->put(route('profile.password.update'), [
        'current_password' => 'password-lama',
        'password' => 'password-baru',
        'password_confirmation' => 'tidak-sama',
    ])->assertSessionHasErrors('password');

    $this->put(route('profile.password.update'), [
        'current_password' => 'password-lama',
        'password' => 'password-baru',
        'password_confirmation' => 'password-baru',
    ])->assertRedirect(route('profile'))
        ->assertSessionHasNoErrors();

    expect(Hash::check('password-baru', $user->fresh()->password))->toBeTrue();
});
