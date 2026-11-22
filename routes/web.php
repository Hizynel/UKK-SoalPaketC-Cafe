<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Kasir\AkunPelangganController;
use App\Http\Controllers\Kasir\MenuController;
use App\Http\Controllers\Kasir\PesananController;

// Route::get('/', function () {
//     return view('welcome');
// });

    Route::get('/', fn () => redirect()->route('login'));
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::prefix('kasir')->name('kasir.')->middleware(['auth', 'role:kasir'])->group(function () {
    Route::get('/dashboard', [PesananController::class, 'dashboard'])->name('dashboard');
    Route::get('/input-pesanan', [PesananController::class, 'input'])->name('input');
    Route::post('/input-pesanan', [PesananController::class, 'store'])->name('input.store');
    Route::get('/data-pesanan', [PesananController::class, 'data'])->name('data');
    Route::post('/pesanan/{pesanan}/proses', [PesananController::class, 'proses'])->name('proses');
    Route::post('/pesanan/{pesanan}/siap', [PesananController::class, 'siap'])->name('siap');
    Route::post('/pesanan/{pesanan}/selesai', [PesananController::class, 'selesai'])->name('selesai');
    Route::post('/pesanan/{pesanan}/batal', [PesananController::class, 'batal'])->name('batal');
    Route::get('/pesanan/{pesanan}/struk', [PesananController::class, 'struk'])->name('struk');

    Route::get('/akun-pelanggan', [AkunPelangganController::class, 'index'])->name('akun');
    Route::post('/akun-pelanggan/{user}/reset', [AkunPelangganController::class, 'reset'])->name('akun.reset');

    Route::get('/menu', [MenuController::class, 'index'])->name('menu');
});
