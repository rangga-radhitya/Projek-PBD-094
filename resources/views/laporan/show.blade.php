@extends('layouts.app')

@section('judul', $laporan['judul'])
@section('grup', 'Laporan')

@section('aksi')
  <a class="tombol sekunder" href="{{ route('laporan.index') }}">
    <x-ikon nama="kiri" :ukuran="15" />
    Semua laporan
  </a>
@endsection

@section('isi')
  @if ($jenis === 'penjualan')
    @include('laporan._penjualan')
  @else
  <x-catatan ringkas judul="Status laporan">
    Laporan ini belum tersedia (menunggu backend agregasi). Filter di bawah hanya contoh struktur dan dinonaktifkan.
  </x-catatan>

  <form class="filter" onsubmit="return false">
    <div class="filter-isi">
      @foreach ($laporan['filter'] as $f)
        <div class="grup-filter">
          <label>{{ $f }}</label>
          @if ($f === 'Periode')
            <input type="date" disabled aria-label="Dari tanggal">
            <input type="date" disabled aria-label="Sampai tanggal">
          @else
            <select disabled><option>Semua</option></select>
          @endif
        </div>
      @endforeach
    </div>
    <div class="filter-aksi">
      <button type="button" class="tombol utama" disabled>
        <x-ikon nama="cari" :ukuran="15" />
        Tampilkan
      </button>
      <button type="button" class="tombol sekunder" disabled>
        <x-ikon nama="cetak" :ukuran="15" />
        Cetak
      </button>
    </div>
  </form>

  <section class="kartu">
    <div class="panel-kepala">
      <h2>Hasil laporan</h2>
      <span class="panel-meta">Sumber data: {{ $laporan['sumber'] }}</span>
    </div>
    <div class="kosong-besar">
      <x-ikon nama="info" :ukuran="28" />
      <p>Belum ada data laporan untuk ditampilkan.</p>
      <p>Sementara, data mentahnya dapat dilihat di
        <a href="{{ route($laporan['sementara']['route']) }}">{{ $laporan['sementara']['label'] }}</a>.
      </p>
    </div>
  </section>
  @endif
@endsection
