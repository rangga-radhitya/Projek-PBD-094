@extends('layouts.app')

@section('judul', 'Data Role')

@section('isi')
  <div class="tabel-bungkus">
  <table>
    <thead>
      <tr><th class="angka">ID</th><th>Nama role</th></tr>
    </thead>
    <tbody>
    @forelse ($daftar as $r)
      <tr>
        <td class="angka">{{ $r->idrole }}</td>
        <td>{{ $r->nama_role }}</td>
      </tr>
    @empty
      <tr><td colspan="2" class="kosong">Belum ada data role.</td></tr>
    @endforelse
    </tbody>
  </table>
  </div>
@endsection
