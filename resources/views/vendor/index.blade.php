@extends('layouts.app')

@section('judul', 'Data Vendor')

@section('isi')
  <div class="tabel-bungkus">
  <table>
    <thead>
      <tr><th class="angka">ID</th><th>Nama vendor</th><th>Badan hukum</th><th>Status</th></tr>
    </thead>
    <tbody>
    @forelse ($daftar as $r)
      <tr>
        <td class="angka">{{ $r->idvendor }}</td>
        <td>{{ $r->nama_vendor }}</td>
        <td>{{ $r->badan_hukum_teks }}</td>
        <td><span class="lencana {{ $r->status === 'A' ? 'aktif' : 'nonaktif' }}">{{ $r->status_teks }}</span></td>
      </tr>
    @empty
      <tr><td colspan="4" class="kosong">Belum ada data vendor.</td></tr>
    @endforelse
    </tbody>
  </table>
  </div>
@endsection
