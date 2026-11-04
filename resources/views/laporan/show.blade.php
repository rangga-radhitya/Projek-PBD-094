@extends('layouts.app')

@section('judul', $laporan['judul'])
@section('grup', 'Laporan')
@section('deskripsi', $laporan['deskripsi'])

@section('aksi')
  <a class="tombol sekunder" href="{{ route('laporan.index') }}">&laquo; Semua laporan</a>
@endsection

@section('isi')
  @if ($jenis === 'penjualan')
    @include('laporan._penjualan')
  @else
  <x-catatan>Laporan ini <strong>belum tersedia</strong> (menunggu backend agregasi). Filter di bawah hanya contoh struktur dan dinonaktifkan.</x-catatan>

  <form class="filter" onsubmit="return false">
    @foreach ($laporan['filter'] as $f)
      <div class="grup-filter">
        <label>{{ $f }}</label>
        @if ($f === 'Periode')
          <input type="date" disabled aria-label="Dari tanggal"> <input type="date" disabled aria-label="Sampai tanggal">
        @else
          <select disabled><option>Semua</option></select>
        @endif
      </div>
    @endforeach
    <button type="button" class="tombol utama" disabled>Tampilkan</button>
    <button type="button" class="tombol sekunder" disabled>Cetak</button>
  </form>

  <section class="kartu">
    <h2>Hasil laporan</h2>
    <p class="hint">Sumber data yang akan dipakai: {{ $laporan['sumber'] }}.</p>
    <div class="tabel-bungkus">
      <table><tbody><tr><td class="kosong">Belum ada data laporan untuk ditampilkan.</td></tr></tbody></table>
    </div>
    <p>Sementara, data mentahnya dapat dilihat di <a href="{{ route($laporan['sementara']['route']) }}">{{ $laporan['sementara']['label'] }}</a>.</p>
  </section>
  @endif
@endsection
