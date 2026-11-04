<?php

use App\Http\Controllers\BarangController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KartuStokController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\MarginPenjualanController;
use App\Http\Controllers\PenerimaanController;
use App\Http\Controllers\PengadaanController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\ReturController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SatuanController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VendorController;
use Illuminate\Support\Facades\Route;

// Halaman awal diarahkan ke dashboard
Route::redirect('/', '/dashboard');

// Semua halaman pada tahap ini hanya MENAMPILKAN data (read-only); belum ada proses simpan.
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Master data
Route::get('/role', [RoleController::class, 'index'])->name('role');
Route::get('/user', [UserController::class, 'index'])->name('user');
Route::get('/satuan', [SatuanController::class, 'index'])->name('satuan');
Route::get('/vendor', [VendorController::class, 'index'])->name('vendor');
Route::get('/margin_penjualan', [MarginPenjualanController::class, 'index'])->name('margin_penjualan');
Route::get('/barang', [BarangController::class, 'index'])->name('barang');

// Transaksi (daftar + detail)
Route::get('/pengadaan', [PengadaanController::class, 'index'])->name('pengadaan.index');
Route::get('/pengadaan/{id}', [PengadaanController::class, 'show'])->whereNumber('id')->name('pengadaan.show');

Route::get('/penerimaan', [PenerimaanController::class, 'index'])->name('penerimaan.index');
Route::get('/penerimaan/{id}', [PenerimaanController::class, 'show'])->whereNumber('id')->name('penerimaan.show');

Route::get('/penjualan', [PenjualanController::class, 'index'])->name('penjualan.index');
Route::get('/penjualan/{id}', [PenjualanController::class, 'show'])->whereNumber('id')->name('penjualan.show');

Route::get('/retur', [ReturController::class, 'index'])->name('retur.index');
Route::get('/retur/{id}', [ReturController::class, 'show'])->whereNumber('id')->name('retur.show');

// Persediaan
Route::get('/kartu_stok', [KartuStokController::class, 'index'])->name('kartu_stok');

// Laporan (struktur halaman; isi menunggu backend)
Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
Route::get('/laporan/{jenis}', [LaporanController::class, 'show'])->name('laporan.show');
