@extends('layouts.app')

@section('judul', 'Detail Penerimaan #'.$penerimaan->idpenerimaan)
@section('deskripsi', 'Informasi penerimaan barang beserta barang yang diterima.')

@section('breadcrumb')
  <li>Penerimaan</li>
  <li><a href="{{ route('penerimaan.index') }}">Data Penerimaan</a></li>
  <li aria-current="page">Penerimaan #{{ $penerimaan->idpenerimaan }}</li>
@endsection

@section('aksi')
  <a class="tombol sekunder" href="{{ route('penerimaan.index') }}">&laquo; Kembali</a>
@endsection

@section('isi')
  <section class="kartu">
    <h2>Informasi penerimaan</h2>
    <dl class="info-daftar">
      <div><dt>ID penerimaan</dt><dd>{{ $penerimaan->idpenerimaan }}</dd></div>
      <div><dt>Tanggal</dt><dd>{{ $penerimaan->created_at?->format('d/m/Y H:i') ?? '-' }}</dd></div>
      <div><dt>Pengadaan asal</dt><dd>@if ($penerimaan->idpengadaan)<a href="{{ route('pengadaan.show', $penerimaan->idpengadaan) }}">Pengadaan #{{ $penerimaan->idpengadaan }}</a>@else - @endif</dd></div>
      <div><dt>Vendor</dt><dd>{{ $penerimaan->pengadaan?->vendor ? $penerimaan->pengadaan->vendor->nama_vendor : '-' }}</dd></div>
      <div><dt>Diterima oleh</dt><dd>{{ $penerimaan->user?->username ?? '-' }}</dd></div>
      <div><dt>Status</dt><dd><x-badge :teks="$penerimaan->status ?? '-'" /> <small class="hint">(arti kode belum ditetapkan)</small></dd></div>
    </dl>
  </section>

  <section class="kartu">
    <h2>Barang yang diterima</h2>
    <div class="tabel-bungkus">
    <table>
      <thead>
        <tr><th>No</th><th>Barang</th><th>Satuan</th><th class="angka">Jumlah terima</th><th class="angka">Harga satuan terima</th><th class="angka">Sub total terima</th></tr>
      </thead>
      <tbody>
      @forelse ($penerimaan->detail as $d)
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td>{{ $d->barang?->nama ?? 'Barang #'.$d->barang_idbarang }}</td>
          <td>{{ $d->barang?->nama_satuan ?? '-' }}</td>
          <td class="angka">{{ $d->jumlah_terima }}</td>
          <td class="angka"><x-rp :nilai="$d->harga_satuan_terima" /></td>
          <td class="angka"><x-rp :nilai="$d->sub_total_terima" /></td>
        </tr>
      @empty
        <tr><td colspan="6" class="kosong">Penerimaan ini belum memiliki detail barang.</td></tr>
      @endforelse
      </tbody>
      @if ($penerimaan->detail->isNotEmpty())
      <tfoot>
        <tr><td colspan="5" class="angka">Total (dijumlahkan dari sub total terima)</td><td class="angka"><x-rp :nilai="$penerimaan->detail->sum('sub_total_terima')" /></td></tr>
      </tfoot>
      @endif
    </table>
    </div>
  </section>

  <section class="kartu">
    <h2>Retur terkait</h2>
    <div class="tabel-bungkus">
    <table>
      <thead><tr><th class="angka">ID retur</th><th>Tanggal</th><th>Dibuat oleh</th><th class="aksi">Aksi</th></tr></thead>
      <tbody>
      @forelse ($penerimaan->retur as $rt)
        <tr>
          <td class="angka">{{ $rt->idretur }}</td>
          <td>{{ $rt->created_at?->format('d/m/Y H:i') ?? '-' }}</td>
          <td>{{ $rt->user?->username ?? '-' }}</td>
          <td class="aksi"><a class="tombol sekunder kecil" href="{{ route('retur.show', $rt->idretur) }}">Detail</a></td>
        </tr>
      @empty
        <tr><td colspan="4" class="kosong">Belum ada retur untuk penerimaan ini.</td></tr>
      @endforelse
      </tbody>
    </table>
    </div>
  </section>
@endsection
