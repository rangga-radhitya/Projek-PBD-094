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

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// ---------------------------------------------------------------
// Tabel master
// ---------------------------------------------------------------
Route::get('/role', [RoleController::class, 'index'])->name('role');
Route::post('/role', [RoleController::class, 'store'])->name('role.store');
Route::put('/role/{id}', [RoleController::class, 'update'])->whereNumber('id')->name('role.update');
Route::delete('/role/{id}', [RoleController::class, 'destroy'])->whereNumber('id')->name('role.destroy');

Route::get('/user', [UserController::class, 'index'])->name('user');
Route::post('/user', [UserController::class, 'store'])->name('user.store');
Route::put('/user/{id}', [UserController::class, 'update'])->whereNumber('id')->name('user.update');
Route::delete('/user/{id}', [UserController::class, 'destroy'])->whereNumber('id')->name('user.destroy');

Route::get('/satuan', [SatuanController::class, 'index'])->name('satuan');
Route::post('/satuan', [SatuanController::class, 'store'])->name('satuan.store');
Route::put('/satuan/{id}', [SatuanController::class, 'update'])->whereNumber('id')->name('satuan.update');
Route::patch('/satuan/{id}/status', [SatuanController::class, 'status'])->whereNumber('id')->name('satuan.status');
Route::delete('/satuan/{id}', [SatuanController::class, 'destroy'])->whereNumber('id')->name('satuan.destroy');

Route::get('/vendor', [VendorController::class, 'index'])->name('vendor');
Route::post('/vendor', [VendorController::class, 'store'])->name('vendor.store');
Route::put('/vendor/{id}', [VendorController::class, 'update'])->whereNumber('id')->name('vendor.update');
Route::patch('/vendor/{id}/status', [VendorController::class, 'status'])->whereNumber('id')->name('vendor.status');
Route::delete('/vendor/{id}', [VendorController::class, 'destroy'])->whereNumber('id')->name('vendor.destroy');

Route::get('/margin_penjualan', [MarginPenjualanController::class, 'index'])->name('margin_penjualan');
Route::post('/margin_penjualan', [MarginPenjualanController::class, 'store'])->name('margin_penjualan.store');
Route::put('/margin_penjualan/{id}', [MarginPenjualanController::class, 'update'])->whereNumber('id')->name('margin_penjualan.update');
Route::patch('/margin_penjualan/{id}/aktifkan', [MarginPenjualanController::class, 'aktifkan'])->whereNumber('id')->name('margin_penjualan.aktifkan');
Route::delete('/margin_penjualan/{id}', [MarginPenjualanController::class, 'destroy'])->whereNumber('id')->name('margin_penjualan.destroy');

Route::get('/barang', [BarangController::class, 'index'])->name('barang');
Route::post('/barang', [BarangController::class, 'store'])->name('barang.store');
Route::put('/barang/{id}', [BarangController::class, 'update'])->whereNumber('id')->name('barang.update');
Route::patch('/barang/{id}/status', [BarangController::class, 'status'])->whereNumber('id')->name('barang.status');
Route::delete('/barang/{id}', [BarangController::class, 'destroy'])->whereNumber('id')->name('barang.destroy');

// ---------------------------------------------------------------
// Transaksi: pengadaan
// ---------------------------------------------------------------
Route::get('/pengadaan/create', [PengadaanController::class, 'create'])->name('pengadaan.create');
Route::post('/pengadaan', [PengadaanController::class, 'store'])->name('pengadaan.store');
Route::get('/pengadaan', [PengadaanController::class, 'index'])->name('pengadaan.index');
Route::get('/pengadaan/{id}', [PengadaanController::class, 'show'])->whereNumber('id')->name('pengadaan.show');
Route::delete('/pengadaan/{id}', [PengadaanController::class, 'destroy'])->whereNumber('id')->name('pengadaan.destroy');

// ---------------------------------------------------------------
// Transaksi: penerimaan
// ---------------------------------------------------------------
Route::get('/penerimaan/create', [PenerimaanController::class, 'create'])->name('penerimaan.create');
Route::post('/penerimaan', [PenerimaanController::class, 'store'])->name('penerimaan.store');
Route::get('/penerimaan', [PenerimaanController::class, 'index'])->name('penerimaan.index');
Route::get('/penerimaan/{id}', [PenerimaanController::class, 'show'])->whereNumber('id')->name('penerimaan.show');
Route::delete('/penerimaan/{id}', [PenerimaanController::class, 'destroy'])->whereNumber('id')->name('penerimaan.destroy');

// ---------------------------------------------------------------
// Transaksi: penjualan (baca saja)
// ---------------------------------------------------------------
Route::get('/penjualan', [PenjualanController::class, 'index'])->name('penjualan.index');
Route::get('/penjualan/{id}', [PenjualanController::class, 'show'])->whereNumber('id')->name('penjualan.show');

// ---------------------------------------------------------------
// Transaksi: retur
// ---------------------------------------------------------------
Route::get('/retur/create', [ReturController::class, 'create'])->name('retur.create');
Route::post('/retur', [ReturController::class, 'store'])->name('retur.store');
Route::get('/retur', [ReturController::class, 'index'])->name('retur.index');
Route::get('/retur/{id}', [ReturController::class, 'show'])->whereNumber('id')->name('retur.show');
Route::delete('/retur/{id}', [ReturController::class, 'destroy'])->whereNumber('id')->name('retur.destroy');

// ---------------------------------------------------------------
// Persediaan
// ---------------------------------------------------------------
Route::get('/kartu_stok', [KartuStokController::class, 'index'])->name('kartu_stok');

// ---------------------------------------------------------------
// Laporan (halaman tetap ada, tidak tampil di sidebar)
// ---------------------------------------------------------------
Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
Route::get('/laporan/{jenis}', [LaporanController::class, 'show'])->name('laporan.show');
