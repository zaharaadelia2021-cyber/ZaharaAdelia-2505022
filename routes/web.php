<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\BelanjaController;
use App\Http\Controllers\CatatController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route(Auth::check() ? 'home' : 'register');
});

Route::middleware('guest')->group(function () {
    Route::get('/daftar', [AuthController::class, 'create'])->name('register');
    Route::post('/daftar', [AuthController::class, 'store'])->name('register.store');
    Route::get('/masuk', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/masuk', [AuthController::class, 'login'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/keluar', [AuthController::class, 'logout'])->name('logout');
    Route::get('/profil', [ProfileController::class, 'show'])->name('profile');
    Route::put('/profil', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profil/kata-sandi', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::post('/profil/target-stok', [ProfileController::class, 'updateTarget'])->name('profile.target.update');
    Route::get('/beranda', [HomeController::class, 'index'])->name('home');
    Route::get('/barang', [BarangController::class, 'index'])->name('barang');
    Route::get('/barang/tambah', [BarangController::class, 'create'])->name('barang.create');
    Route::post('/barang', [BarangController::class, 'store'])->name('barang.store');
    Route::get('/barang/{barang}/ubah', [BarangController::class, 'edit'])->name('barang.edit');
    Route::put('/barang/{barang}', [BarangController::class, 'update'])->name('barang.update');
    Route::delete('/barang/{barang}', [BarangController::class, 'destroy'])->name('barang.destroy');
    Route::get('/catat', [CatatController::class, 'create'])->name('catat');
    Route::post('/catat', [CatatController::class, 'store'])->name('catat.store');
    Route::get('/belanja', [BelanjaController::class, 'index'])->name('belanja');
    Route::post('/belanja/target-stok', [BelanjaController::class, 'updateTarget'])->name('belanja.target.update');
});
