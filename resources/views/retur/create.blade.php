@extends('layouts.app')

@section('judul', 'Tambah Retur')
@section('grup', 'Transaksi')

@section('breadcrumb')
  <li>Transaksi</li>
  <li><a href="{{ route('retur.index') }}">Retur</a></li>
  <li aria-current="page">Tambah</li>
@endsection

@section('aksi')
  <a class="tombol sekunder" href="{{ route('retur.index') }}">
    <x-ikon nama="kiri" :ukuran="15" />
    Kembali
  </a>
  @if ($penerimaan !== null)
    <button type="submit" form="form-retur" class="tombol utama">
      <x-ikon nama="centang" :ukuran="15" />
      Simpan Retur
    </button>
  @endif
@endsection

@section('isi')
@include('partials.galat')

@php
  $lama = [];
  foreach ((array) old('items', []) as $baris) {
      if (isset($baris['iddetail_penerimaan'])) {
          $lama[(string) $baris['iddetail_penerimaan']] = $baris;
      }
  }
@endphp

@if ($penerimaan === null)
  <x-catatan jenis="info">Pilih penerimaan terlebih dahulu. Yang ditampilkan hanya penerimaan yang masih memiliki barang bisa diretur.</x-catatan>
@endif

<section class="kartu">
  <h2>Pilih penerimaan</h2>
  <div class="pilih-baris">
    <div class="grup-filter">
      <label for="pilih-penerimaan">Penerimaan</label>
      <select id="pilih-penerimaan">
        <option value="">— Pilih penerimaan —</option>
        @foreach ($daftarPenerimaan as $p)
          <option value="{{ $p->idpenerimaan }}" @selected((int) $idpenerimaan === (int) $p->idpenerimaan)>
            #{{ $p->idpenerimaan }} — {{ $p->nama_vendor }} ({{ $p->tanggal }}, sisa {{ $p->sisa }})
          </option>
        @endforeach
      </select>
    </div>
    @if ($penerimaan !== null)
      <div class="grup-filter">
        <label>Vendor</label>
        <input type="text" value="{{ $penerimaan->nama_vendor }}" disabled>
      </div>
      <div class="grup-filter">
        <label>Tanggal penerimaan</label>
        <input type="text" value="{{ $penerimaan->tanggal }}" disabled>
      </div>
    @endif
  </div>
  @if ($daftarPenerimaan === [])
    <x-catatan jenis="info">Semua penerimaan sudah diretur penuh. Tidak ada barang yang bisa diretur.</x-catatan>
  @endif
</section>

@if ($penerimaan !== null)
  <form id="form-retur" class="form-transaksi" method="POST" action="{{ route('retur.store') }}">
    @csrf
    <input type="hidden" name="idpenerimaan" value="{{ $penerimaan->idpenerimaan }}">

    <section class="kartu">
      <h2>Barang diretur</h2>
      <div class="tabel-bungkus">
        <table id="tabel-retur">
          <thead>
            <tr>
              <th class="kolom-aksi">No</th>
              <th>Barang</th>
              <th class="kolom-sempit angka">Diterima</th>
              <th class="kolom-sempit angka">Sudah diretur</th>
              <th class="kolom-sempit angka">Sisa</th>
              <th class="kolom-sempit angka">Jumlah retur</th>
              <th>Alasan</th>
            </tr>
          </thead>
          <tbody>
          @foreach ($rincian as $i => $r)
            @php
              $idd = (string) $r->iddetail_penerimaan;
              $jumlah = isset($lama[$idd]) ? (string) $lama[$idd]['jumlah'] : (string) $r->sisa;
              $alasan = isset($lama[$idd]) ? (string) ($lama[$idd]['alasan'] ?? '') : '';
              $nonaktif = (int) $r->sisa <= 0;
            @endphp
            <tr @class(['baris-kosong' => $nonaktif])>
              <td class="kolom-aksi">{{ $i + 1 }}</td>
              <td>
                {{ $r->nama }}
                <small class="hint">{{ $r->nama_satuan ?? '-' }}</small>
                <input type="hidden" name="items[{{ $i }}][iddetail_penerimaan]" value="{{ $idd }}">
              </td>
              <td class="angka">{{ $r->jumlah_terima }}</td>
              <td class="angka">{{ $r->sudah_retur }}</td>
              <td class="angka">{{ $r->sisa }}</td>
              <td>
                <input type="number" name="items[{{ $i }}][jumlah]" value="{{ $nonaktif ? 0 : $jumlah }}"
                       min="0" max="{{ $r->sisa }}" step="1" @if ($nonaktif) readonly @endif required>
              </td>
              <td><input type="text" name="items[{{ $i }}][alasan]" value="{{ $alasan }}" maxlength="200" placeholder="Opsional"></td>
            </tr>
          @endforeach
          </tbody>
        </table>
      </div>
      <p class="hint">Jumlah retur tidak boleh melebihi sisa yang belum diretur; aturan ini juga diperiksa oleh stored procedure. Stok keluar dicatat otomatis oleh trigger database.</p>
    </section>
  </form>
@endif
@endsection

@push('skrip')
<script>
(function () {
  'use strict';

  var pilih = document.getElementById('pilih-penerimaan');
  if (pilih) {
    pilih.addEventListener('change', function () {
      var base = @json(route('retur.create'));
      window.location = pilih.value === '' ? base : base + '?idpenerimaan=' + encodeURIComponent(pilih.value);
    });
  }
})();
</script>
@endpush
