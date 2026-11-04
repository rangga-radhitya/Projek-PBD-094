@extends('layouts.app')

@section('judul', 'Detail Penjualan #'.$penjualan->idpenjualan)
@section('deskripsi', 'Informasi transaksi penjualan beserta barang yang dijual.')

@section('breadcrumb')
  <li>Penjualan</li>
  <li><a href="{{ route('penjualan.index') }}">Data Penjualan</a></li>
  <li aria-current="page">Penjualan #{{ $penjualan->idpenjualan }}</li>
@endsection

@section('aksi')
  <a class="tombol sekunder" href="{{ route('penjualan.index') }}">&laquo; Kembali</a>
  <button type="button" class="tombol sekunder" onclick="window.print()">Cetak</button>
@endsection

@section('isi')
  <section class="kartu">
    <h2>Informasi penjualan</h2>
    <dl class="info-daftar">
      <div><dt>ID penjualan</dt><dd>{{ $penjualan->idpenjualan }}</dd></div>
      <div><dt>Tanggal</dt><dd>{{ $penjualan->tanggal ?? '-' }}</dd></div>
      <div><dt>Kasir</dt><dd>{{ $penjualan->username ?? '-' }}</dd></div>
      <div><dt>Margin tercatat</dt><dd>@if ($penjualan->persen !== null)<x-persen :nilai="$penjualan->persen" /> (ID {{ $penjualan->idmargin_penjualan }})@else - @endif</dd></div>
    </dl>
  </section>

  <section class="kartu">
    <h2>Barang yang dijual</h2>
    <div class="tabel-bungkus">
    <table>
      <thead>
        <tr><th>No</th><th>Barang</th><th>Satuan</th><th class="angka">Harga satuan</th><th class="angka">Jumlah</th><th class="angka">Subtotal</th></tr>
      </thead>
      <tbody>
      @forelse ($penjualan->detail as $d)
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td>{{ $d->nama ?? 'Barang #'.$d->idbarang }}</td>
          <td>{{ $d->nama_satuan ?? '-' }}</td>
          <td class="angka"><x-rp :nilai="$d->harga_satuan" /></td>
          <td class="angka">{{ $d->jumlah }}</td>
          <td class="angka"><x-rp :nilai="$d->subtotal" /></td>
        </tr>
      @empty
        <tr><td colspan="6" class="kosong">Penjualan ini belum memiliki detail barang.</td></tr>
      @endforelse
      </tbody>
      <tfoot>
        @if ($totalFungsi !== null)
          <tr>
            <td colspan="5" class="angka">Subtotal dihitung dari detail (fungsi database <code>fn_hitung_total_penjualan</code>)</td>
            <td class="angka">
              <x-rp :nilai="$totalFungsi" />
              @if ($penjualan->subtotal_nilai !== null)
                <x-badge :aktif="(float) $totalFungsi === (float) $penjualan->subtotal_nilai" :teks="(float) $totalFungsi === (float) $penjualan->subtotal_nilai ? 'sesuai' : 'berbeda dari subtotal tercatat'" />
              @endif
            </td>
          </tr>
        @endif
        <tr><td colspan="5" class="angka">Subtotal</td><td class="angka"><x-rp :nilai="$penjualan->subtotal_nilai" /></td></tr>
        <tr><td colspan="5" class="angka">PPN</td><td class="angka"><x-rp :nilai="$penjualan->ppn" /></td></tr>
        <tr><td colspan="5" class="angka">Total</td><td class="angka"><x-rp :nilai="$penjualan->total_nilai" /></td></tr>
      </tfoot>
    </table>
    </div>
  </section>
@endsection
