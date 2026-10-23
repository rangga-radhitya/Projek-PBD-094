@extends('layouts.app')

@section('judul', 'Data Satuan')

@section('isi')
  <div class="tabel-bungkus">
  <table>
    <thead>
      <tr><th class="angka">ID</th><th>Nama satuan</th><th>Status</th></tr>
    </thead>
    <tbody>
    @forelse ($daftar as $r)
      <tr>
        <td class="angka">{{ $r->idsatuan }}</td>
        <td>{{ $r->nama_satuan }}</td>
        <td><span class="lencana {{ $r->status == 1 ? 'aktif' : 'nonaktif' }}">{{ $r->status_teks }}</span></td>
      </tr>
    @empty
      <tr><td colspan="3" class="kosong">Belum ada data satuan.</td></tr>
    @endforelse
    </tbody>
  </table>
  </div>
@endsection
