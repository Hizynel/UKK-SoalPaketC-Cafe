<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Kasir\AkunPelangganController;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', fn () => redirect()->route('login'));
Route::view('/login', 'auth.login')->name('login');
Route::post('/login', fn () => redirect()->route('kasir.dashboard'));
Route::post('/logout', fn () => redirect()->route('login'))->name('logout');

Route::prefix('kasir')->name('kasir.')->group(function () {
    Route::view('/dashboard', 'kasir.dashboard')->name('dashboard');
    Route::view('/input-pesanan', 'kasir.input')->name('input');
    Route::view('/data-pesanan', 'kasir.data')->name('data');
    Route::view('/akun-pelanggan', 'kasir.akun')->name('akun');
});
