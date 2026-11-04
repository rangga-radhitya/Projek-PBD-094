@extends('layouts.app')

@section('judul', 'Detail Retur #'.$retur->idretur)
@section('deskripsi', 'Informasi retur beserta barang yang diretur.')

@section('breadcrumb')
  <li>Retur</li>
  <li><a href="{{ route('retur.index') }}">Data Retur</a></li>
  <li aria-current="page">Retur #{{ $retur->idretur }}</li>
@endsection

@section('aksi')
  <a class="tombol sekunder" href="{{ route('retur.index') }}">&laquo; Kembali</a>
@endsection

@section('isi')
  <section class="kartu">
    <h2>Informasi retur</h2>
    <dl class="info-daftar">
      <div><dt>ID retur</dt><dd>{{ $retur->idretur }}</dd></div>
      <div><dt>Tanggal</dt><dd>{{ $retur->created_at?->format('d/m/Y H:i') ?? '-' }}</dd></div>
      <div><dt>Penerimaan asal</dt><dd>@if ($retur->idpenerimaan)<a href="{{ route('penerimaan.show', $retur->idpenerimaan) }}">Penerimaan #{{ $retur->idpenerimaan }}</a>@else - @endif</dd></div>
      <div><dt>Vendor</dt><dd>{{ $retur->penerimaan?->pengadaan?->vendor ? $retur->penerimaan->pengadaan->vendor->nama_vendor : '-' }}</dd></div>
      <div><dt>Dibuat oleh</dt><dd>{{ $retur->user?->username ?? '-' }}</dd></div>
    </dl>
  </section>

  <section class="kartu">
    <h2>Barang yang diretur</h2>
    <div class="tabel-bungkus">
    <table>
      <thead>
        <tr><th>No</th><th>Barang</th><th>Satuan</th><th class="angka">Jumlah retur</th><th class="angka">Jumlah diterima (penerimaan asal)</th><th>Alasan</th></tr>
      </thead>
      <tbody>
      @forelse ($retur->detail as $d)
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td>{{ $d->detailPenerimaan?->barang?->nama ?? ($d->detailPenerimaan ? 'Barang #'.$d->detailPenerimaan->barang_idbarang : '-') }}</td>
          <td>{{ $d->detailPenerimaan?->barang?->nama_satuan ?? '-' }}</td>
          <td class="angka">{{ $d->jumlah }}</td>
          <td class="angka">{{ $d->detailPenerimaan?->jumlah_terima ?? '-' }}</td>
          <td>{{ $d->alasan ?? '-' }}</td>
        </tr>
      @empty
        <tr><td colspan="6" class="kosong">Retur ini belum memiliki detail barang.</td></tr>
      @endforelse
      </tbody>
    </table>
    </div>
  </section>
@endsection
