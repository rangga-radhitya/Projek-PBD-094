@extends('layouts.app')

@section('judul', 'Laporan')
@section('deskripsi', 'Daftar laporan Daily Bun. Laporan Penjualan sudah tersedia; laporan lain masih berupa struktur halaman.')

@section('isi')
  <x-catatan><strong>Laporan Penjualan</strong> sudah memakai Stored Procedure <code>sp_laporan_penjualan_periode</code>. Laporan lainnya membutuhkan query agregasi di backend yang belum dibuat dan saat ini hanya menampilkan struktur serta jalan pintas ke data yang sudah ada.</x-catatan>

  <div class="pilihan-laporan">
    @foreach ($laporan as $kunci => $l)
      <a href="{{ route('laporan.show', $kunci) }}">
        <div class="kartu">
          <h2>{{ $l['judul'] }}</h2>
          <p>{{ $l['deskripsi'] }}</p>
          <p class="hint">Sumber data: {{ $l['sumber'] }}</p>
        </div>
      </a>
    @endforeach
  </div>
@endsection
