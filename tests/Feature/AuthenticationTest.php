<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('registration and login screens render their expected forms', function () {
    $this->get(route('register'))
        ->assertOk()
        ->assertSee('Nama warung')
        ->assertSee('Nama pemilik')
        ->assertSee('Ulangi kata sandi')
        ->assertSee('Daftar dan mulai');

    $this->get(route('login'))
        ->assertOk()
        ->assertSee('Masuk ke Breeze')
        ->assertSee('Kata sandi');
});

test('registration saves the warung name and signs the owner in to the home page', function () {
    $response = $this->post(route('register.store'), [
        'nama_warung' => 'Warung Melati',
        'name' => 'Sari',
        'email' => 'sari@example.test',
        'password' => 'rahasia123',
        'password_confirmation' => 'rahasia123',
    ]);

    $user = User::where('email', 'sari@example.test')->firstOrFail();

    $response->assertRedirect(route('home'))
        ->assertSessionHasNoErrors();
    $this->assertAuthenticatedAs($user);
    expect($user->nama_warung)->toBe('Warung Melati');
});

test('registration requires matching password confirmation', function () {
    $this->from(route('register'))
        ->post(route('register.store'), [
            'nama_warung' => 'Warung Melati',
            'name' => 'Sari',
            'email' => 'sari@example.test',
            'password' => 'rahasia123',
            'password_confirmation' => 'kata-lain',
        ])
        ->assertRedirect(route('register'))
        ->assertSessionHasErrors('password');

    $this->assertDatabaseMissing('users', ['email' => 'sari@example.test']);
});

test('login sends an authenticated owner to the home page', function () {
    $user = User::factory()->create([
        'email' => 'sari@example.test',
        'password' => 'rahasia123',
    ]);

    $this->post(route('login.store'), [
        'email' => 'sari@example.test',
        'password' => 'rahasia123',
    ])->assertRedirect(route('home'));

    $this->assertAuthenticatedAs($user);
});

test('each main tab requires authentication and renders its active navigation state', function () {
    $this->get(route('home'))->assertRedirect(route('login'));
    $user = User::factory()->create(['nama_warung' => 'Warung Melati']);

    $this->actingAs($user)
        ->get(route('barang'))
        ->assertOk()
        ->assertSee('Barang')
        ->assertSee('aria-current="page"', false)
        ->assertSee('Beranda')
        ->assertSee('Catat')
        ->assertSee('Belanja');
});
