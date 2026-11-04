@extends('layouts.app')

@section('judul', 'Laporan')

@section('isi')
  <div class="pilihan-laporan">
    @foreach ($laporan as $kunci => $l)
      <a href="{{ route('laporan.show', $kunci) }}">
        <div class="kartu">
          <h2>{{ $l['judul'] }}</h2>
          <p>{{ $l['deskripsi'] }}</p>
          <x-badge :jenis="$kunci === 'penjualan' ? 'sukses' : 'netral'"
                   :teks="$kunci === 'penjualan' ? 'Tersedia' : 'Belum tersedia'" />
          <span class="kartu-panah">
            Buka laporan
            <x-ikon nama="panah" :ukuran="14" />
          </span>
        </div>
      </a>
    @endforeach
  </div>

  <x-catatan ringkas judul="Status laporan" class="catatan-laporan">
    Laporan Penjualan memakai stored procedure <code>sp_laporan_penjualan_periode</code>.
    Laporan lain menunggu query agregasi di backend.
  </x-catatan>
@endsection
