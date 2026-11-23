<?php

use App\Http\Controllers\AuthController;
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
    Route::view('/beranda', 'pages.home')->name('home');
    Route::view('/barang', 'pages.barang')->name('barang');
    Route::view('/catat', 'pages.catat')->name('catat');
    Route::view('/belanja', 'pages.belanja')->name('belanja');
});
