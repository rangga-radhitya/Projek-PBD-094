@extends('layouts.app')

@section('judul', 'Tambah Pengadaan')
@section('grup', 'Transaksi')

@section('breadcrumb')
  <li>Transaksi</li>
  <li><a href="{{ route('pengadaan.index') }}">Pengadaan</a></li>
  <li aria-current="page">Tambah</li>
@endsection

@section('aksi')
  <a class="tombol sekunder" href="{{ route('pengadaan.index') }}">
    <x-ikon nama="kiri" :ukuran="15" />
    Kembali
  </a>
  <button type="submit" form="form-pengadaan" class="tombol utama">
    <x-ikon nama="centang" :ukuran="15" />
    Simpan Pengadaan
  </button>
@endsection

@section('isi')
@include('partials.galat')

@php
  $barisLama = old('items', []);
  $barisLama = is_array($barisLama) && $barisLama !== [] ? array_values($barisLama) : [['idbarang' => '', 'harga_satuan' => '', 'jumlah' => 1]];
@endphp

<form id="form-pengadaan" class="form-transaksi" method="POST" action="{{ route('pengadaan.store') }}">
  @csrf

  <section class="kartu">
    <h2>Informasi pengadaan</h2>
    <div class="pilih-baris">
      <div class="grup-filter">
        <label for="idvendor">Vendor <span class="wajib" aria-hidden="true">*</span></label>
        <select id="idvendor" name="idvendor" required>
          <option value="">Pilih vendor</option>
          @foreach ($daftarVendor as $v)
            <option value="{{ $v->idvendor }}" @selected((string) old('idvendor') === (string) $v->idvendor)>{{ $v->nama_vendor }}</option>
          @endforeach
        </select>
      </div>
      <div class="grup-filter">
        <label>Dibuat oleh</label>
        <input type="text" value="manajer01" disabled>
      </div>
    </div>
  </section>

  <section class="kartu">
    <h2>Barang yang dipesan</h2>
    <div class="tabel-bungkus">
      <table id="tabel-barang">
        <thead>
          <tr>
            <th class="kolom-aksi">No</th>
            <th>Barang</th>
            <th class="kolom-sempit angka">Harga satuan</th>
            <th class="kolom-sempit angka">Jumlah</th>
            <th class="kolom-sempit angka">Sub total</th>
            <th class="kolom-aksi">Hapus</th>
          </tr>
        </thead>
        <tbody id="isi-barang">
        @foreach ($barisLama as $i => $b)
          @php
            $idb = (string) ($b['idbarang'] ?? '');
            $harga = (string) ($b['harga_satuan'] ?? '');
            $jumlah = (string) ($b['jumlah'] ?? '1');
          @endphp
          <tr>
            <td class="kolom-aksi">{{ $i + 1 }}</td>
            <td>
              <select name="items[{{ $i }}][idbarang]" required>
                <option value="">Pilih barang</option>
                @foreach ($daftarBarang as $br)
                  <option value="{{ $br->idbarang }}" data-harga="{{ $br->harga }}" @selected($idb === (string) $br->idbarang)>{{ $br->nama }}</option>
                @endforeach
              </select>
            </td>
            <td><input type="number" name="items[{{ $i }}][harga_satuan]" value="{{ $harga }}" min="0" step="1" required></td>
            <td><input type="number" name="items[{{ $i }}][jumlah]" value="{{ $jumlah }}" min="1" step="1" required></td>
            <td class="angka"><output data-subtotal>0</output></td>
            <td class="kolom-aksi">
              <button type="button" class="tombol sekunder kecil" data-hapus-baris>
                <x-ikon nama="hapus" :ukuran="14" />
                Hapus
              </button>
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>

    <p><button type="button" class="tombol sekunder kecil" id="tambah-baris">
      <x-ikon nama="tambah" :ukuran="14" />
      Tambah barang
    </button></p>

    <dl class="ringkas">
      <div><dt>Subtotal</dt><dd><output id="ringkas-subtotal">0</output></dd></div>
      <div><dt>PPN ({{ \App\Http\Controllers\Controller::PPN_PERSEN }}%)</dt><dd><output id="ringkas-ppn">0</output></dd></div>
      <div><dt>Total</dt><dd><output id="ringkas-total">0</output></dd></div>
    </dl>
  </section>
</form>

<template id="pola-baris">
  <tr>
    <td class="kolom-aksi">__NO__</td>
    <td>
      <select name="items[__INDEX__][idbarang]" required>
        <option value="">Pilih barang</option>
        @foreach ($daftarBarang as $br)
          <option value="{{ $br->idbarang }}" data-harga="{{ $br->harga }}">{{ $br->nama }}</option>
        @endforeach
      </select>
    </td>
    <td><input type="number" name="items[__INDEX__][harga_satuan]" value="" min="0" step="1" required></td>
    <td><input type="number" name="items[__INDEX__][jumlah]" value="1" min="1" step="1" required></td>
    <td class="angka"><output data-subtotal>0</output></td>
    <td class="kolom-aksi">
      <button type="button" class="tombol sekunder kecil" data-hapus-baris>
        <x-ikon nama="hapus" :ukuran="14" />
        Hapus
      </button>
    </td>
  </tr>
</template>@endsection

@push('skrip')
<script>
(function () {
  'use strict';

  var PPN = {{ \App\Http\Controllers\Controller::PPN_PERSEN }};
  var body = document.getElementById('isi-barang');
  var pola = document.getElementById('pola-baris');
  var tombolTambah = document.getElementById('tambah-baris');
  if (!body || !pola) return;

  function rupiah(n) {
    return Number(n || 0).toLocaleString('id-ID');
  }

  function nomori() {
    var baris = body.querySelectorAll('tr');
    baris.forEach(function (tr, i) {
      tr.querySelector('td').textContent = i + 1;
      tr.querySelectorAll('[name]').forEach(function (el) {
        el.name = el.name.replace(/items\[\d+\]/, 'items[' + i + ']');
      });
    });
  }

  function hitung() {
    var subtotal = 0;
    body.querySelectorAll('tr').forEach(function (tr) {
      var harga = parseFloat(tr.querySelector('[name$="[harga_satuan]"]').value) || 0;
      var jumlah = parseFloat(tr.querySelector('[name$="[jumlah]"]').value) || 0;
      var sub = harga * jumlah;
      subtotal += sub;
      var out = tr.querySelector('[data-subtotal]');
      if (out) out.textContent = rupiah(sub);
    });
    var ppn = Math.round(subtotal * PPN / 100);
    var r1 = document.getElementById('ringkas-subtotal');
    var r2 = document.getElementById('ringkas-ppn');
    var r3 = document.getElementById('ringkas-total');
    if (r1) r1.textContent = rupiah(subtotal);
    if (r2) r2.textContent = rupiah(ppn);
    if (r3) r3.textContent = rupiah(subtotal + ppn);
  }

  body.addEventListener('input', function (e) {
    var el = e.target;
    if (!(el instanceof Element)) return;
    if (el.matches('[name$="[idbarang]"]')) {
      var opt = el.options[el.selectedIndex];
      var harga = opt ? opt.getAttribute('data-harga') : null;
      var inputHarga = el.closest('tr').querySelector('[name$="[harga_satuan]"]');
      if (harga !== null && inputHarga && inputHarga.value === '') {
        inputHarga.value = harga;
      }
    }
    hitung();
  });

  body.addEventListener('click', function (e) {
    var t = e.target;
    if (!(t instanceof Element)) return;
    if (!t.closest('[data-hapus-baris]')) return;
    var baris = body.querySelectorAll('tr');
    if (baris.length <= 1) {
      return;
    }
    t.closest('tr').remove();
    nomori();
    hitung();
  });

  if (tombolTambah) {
    tombolTambah.addEventListener('click', function () {
      var index = body.querySelectorAll('tr').length;
      var html = pola.innerHTML.replace(/__INDEX__/g, index).replace(/__NO__/g, index + 1);
      body.insertAdjacentHTML('beforeend', html);
      hitung();
    });
  }

  hitung();
})();
</script>
@endpush
