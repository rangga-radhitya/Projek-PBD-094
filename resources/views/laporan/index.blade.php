@extends('layouts.app')

@section('judul', 'Laporan')
@section('deskripsi', 'Daftar laporan yang direncanakan. Isi laporan belum tersedia pada tahap ini.')

@section('isi')
  <x-catatan>Laporan membutuhkan query agregasi di backend yang belum dibuat. Setiap halaman laporan saat ini hanya menampilkan struktur dan jalan pintas ke data yang sudah ada.</x-catatan>

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
