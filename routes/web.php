<?php

use App\Http\Controllers\BarangController;
use App\Http\Controllers\MarginPenjualanController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SatuanController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VendorController;
use Illuminate\Support\Facades\Route;

// Halaman awal langsung diarahkan ke menu pertama (tidak ada beranda)
Route::redirect('/', '/role');

// Semua halaman hanya menampilkan data (read-only) dari VIEW di MySQL
Route::get('/role', [RoleController::class, 'index'])->name('role');
Route::get('/user', [UserController::class, 'index'])->name('user');
Route::get('/satuan', [SatuanController::class, 'index'])->name('satuan');
Route::get('/vendor', [VendorController::class, 'index'])->name('vendor');
Route::get('/margin_penjualan', [MarginPenjualanController::class, 'index'])->name('margin_penjualan');
Route::get('/barang', [BarangController::class, 'index'])->name('barang');
