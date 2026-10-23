@extends('layouts.app')

@section('judul', 'Data Barang')

@section('isi')
  <div class="tabel-bungkus">
  <table>
    <thead>
      <tr><th class="angka">ID</th><th>Nama barang</th><th>Jenis</th><th>Satuan</th><th class="angka">Harga</th><th>Status</th></tr>
    </thead>
    <tbody>
    @forelse ($daftar as $r)
      <tr>
        <td class="angka">{{ $r->idbarang }}</td>
        <td>{{ $r->nama }}</td>
        <td>{{ $r->jenis_teks }}</td>
        <td>{{ $r->nama_satuan ?? '-' }}</td>
        <td class="angka">Rp {{ number_format($r->harga, 0, ',', '.') }}</td>
        <td><span class="lencana {{ $r->status == 1 ? 'aktif' : 'nonaktif' }}">{{ $r->status == 1 ? 'Aktif' : 'Nonaktif' }}</span></td>
      </tr>
    @empty
      <tr><td colspan="6" class="kosong">Belum ada data barang.</td></tr>
    @endforelse
    </tbody>
  </table>
  </div>
@endsection
