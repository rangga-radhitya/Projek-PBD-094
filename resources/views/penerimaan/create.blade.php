@extends('layouts.app')

@section('judul', 'Tambah Penerimaan')
@section('grup', 'Transaksi')

@section('breadcrumb')
  <li>Transaksi</li>
  <li><a href="{{ route('penerimaan.index') }}">Penerimaan</a></li>
  <li aria-current="page">Tambah</li>
@endsection

@section('aksi')
  <a class="tombol sekunder" href="{{ route('penerimaan.index') }}">
    <x-ikon nama="kiri" :ukuran="15" />
    Kembali
  </a>
  @if ($pengadaan !== null)
    <button type="submit" form="form-penerimaan" class="tombol utama">
      <x-ikon nama="centang" :ukuran="15" />
      Simpan Penerimaan
    </button>
  @endif
@endsection

@section('isi')
@include('partials.galat')

@php
  $lama = [];
  foreach ((array) old('items', []) as $baris) {
      if (isset($baris['idbarang'])) {
          $lama[(string) $baris['idbarang']] = $baris;
      }
  }
@endphp

@if ($pengadaan === null)
  <x-catatan jenis="info">Pilih pengadaan terlebih dahulu. Yang ditampilkan hanya pengadaan yang masih memiliki sisa barang belum diterima.</x-catatan>
@endif

<section class="kartu">
  <h2>Pilih pengadaan</h2>
  <div class="pilih-baris">
    <div class="grup-filter">
      <label for="pilih-pengadaan">Pengadaan</label>
      <select id="pilih-pengadaan">
        <option value="">— Pilih pengadaan —</option>
        @foreach ($daftarPengadaan as $p)
          <option value="{{ $p->idpengadaan }}" @selected((int) $idpengadaan === (int) $p->idpengadaan)>
            #{{ $p->idpengadaan }} — {{ $p->nama_vendor }} ({{ $p->tanggal }}, sisa {{ $p->sisa }})
          </option>
        @endforeach
      </select>
    </div>
    @if ($pengadaan !== null)
      <div class="grup-filter">
        <label>Vendor</label>
        <input type="text" value="{{ $pengadaan->nama_vendor }}" disabled>
      </div>
      <div class="grup-filter">
        <label>Tanggal pengadaan</label>
        <input type="text" value="{{ $pengadaan->tanggal }}" disabled>
      </div>
    @endif
  </div>
  @if ($daftarPengadaan === [])
    <x-catatan jenis="info">Semua pengadaan sudah diterima penuh. Buat pengadaan baru terlebih dahulu.</x-catatan>
  @endif
</section>

@if ($pengadaan !== null)
  <form id="form-penerimaan" class="form-transaksi" method="POST" action="{{ route('penerimaan.store') }}">
    @csrf
    <input type="hidden" name="idpengadaan" value="{{ $pengadaan->idpengadaan }}">

    <section class="kartu">
      <h2>Informasi penerimaan</h2>
      <div class="pilih-baris">
        <div class="grup-filter">
          <label for="status">Status <span class="wajib" aria-hidden="true">*</span></label>
          <select id="status" name="status" required>
            <option value="D" @selected(old('status', 'D') === 'D')>D — Draft</option>
            <option value="S" @selected(old('status') === 'S')>S — Selesai</option>
          </select>
        </div>
        <div class="grup-filter">
          <label>Diterima oleh</label>
          <input type="text" value="manajer01 (user id 1)" disabled>
        </div>
      </div>
    </section>

    <section class="kartu">
      <h2>Barang diterima</h2>
      <div class="tabel-bungkus">
        <table id="tabel-terima">
          <thead>
            <tr>
              <th class="kolom-aksi">No</th>
              <th>Barang</th>
              <th class="kolom-sempit angka">Dipesan</th>
              <th class="kolom-sempit angka">Sudah diterima</th>
              <th class="kolom-sempit angka">Sisa</th>
              <th class="kolom-sempit angka">Jumlah terima</th>
              <th class="kolom-sempit angka">Harga satuan</th>
              <th class="kolom-sempit angka">Sub total</th>
            </tr>
          </thead>
          <tbody>
          @foreach ($rincian as $i => $r)
            @php
              $idb = (string) $r->idbarang;
              $terima = isset($lama[$idb]) ? (string) $lama[$idb]['jumlah_terima'] : (string) $r->sisa;
              $harga = isset($lama[$idb]) ? (string) $lama[$idb]['harga_satuan_terima'] : (string) $r->harga_satuan;
              $nonaktif = (int) $r->sisa <= 0;
            @endphp
            <tr @class(['baris-kosong' => $nonaktif])>
              <td class="kolom-aksi">{{ $i + 1 }}</td>
              <td>
                {{ $r->nama }}
                <small class="hint">{{ $r->nama_satuan ?? '-' }}</small>
                <input type="hidden" name="items[{{ $i }}][idbarang]" value="{{ $idb }}">
              </td>
              <td class="angka">{{ $r->dipesan }}</td>
              <td class="angka">{{ $r->diterima }}</td>
              <td class="angka">{{ $r->sisa }}</td>
              <td>
                <input type="number" name="items[{{ $i }}][jumlah_terima]" value="{{ $nonaktif ? 0 : $terima }}"
                       min="0" max="{{ $r->sisa }}" step="1" @if ($nonaktif) readonly @endif required>
              </td>
              <td>
                <input type="number" name="items[{{ $i }}][harga_satuan_terima]" value="{{ $harga }}"
                       min="0" step="1" @if ($nonaktif) readonly @endif required>
              </td>
              <td class="angka"><output data-subtotal>0</output></td>
            </tr>
          @endforeach
          </tbody>
        </table>
      </div>

      <dl class="ringkas">
        <div><dt>Total nilai diterima</dt><dd><output id="ringkas-total">0</output></dd></div>
      </dl>
      <p class="hint">Jumlah terima tidak boleh melebihi sisa; aturan ini juga diperiksa oleh stored procedure. Stok masuk dicatat otomatis oleh trigger database.</p>
    </section>
  </form>
@endif
@endsection

@push('skrip')
<script>
(function () {
  'use strict';

  var pilih = document.getElementById('pilih-pengadaan');
  if (pilih) {
    pilih.addEventListener('change', function () {
      var base = @json(route('penerimaan.create'));
      window.location = pilih.value === '' ? base : base + '?idpengadaan=' + encodeURIComponent(pilih.value);
    });
  }

  var tabel = document.getElementById('tabel-terima');
  if (!tabel) return;

  function rupiah(n) {
    return Number(n || 0).toLocaleString('id-ID');
  }

  function hitung() {
    var total = 0;
    tabel.querySelectorAll('tbody tr').forEach(function (tr) {
      var jumlah = parseFloat(tr.querySelector('[name$="[jumlah_terima]"]').value) || 0;
      var harga = parseFloat(tr.querySelector('[name$="[harga_satuan_terima]"]').value) || 0;
      var sub = jumlah * harga;
      total += sub;
      var out = tr.querySelector('[data-subtotal]');
      if (out) out.textContent = rupiah(sub);
    });
    var outTotal = document.getElementById('ringkas-total');
    if (outTotal) outTotal.textContent = rupiah(total);
  }

  tabel.addEventListener('input', hitung);
  hitung();
})();
</script>
@endpush
