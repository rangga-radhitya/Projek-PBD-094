@extends('layouts.app')

@section('judul', 'Data User')

@section('isi')
  <div class="tabel-bungkus">
  <table>
    <thead>
      <tr><th class="angka">ID</th><th>Username</th><th>Role</th></tr>
    </thead>
    <tbody>
    @forelse ($daftar as $r)
      <tr>
        <td class="angka">{{ $r->iduser }}</td>
        <td>{{ $r->username }}</td>
        <td>{{ $r->nama_role }}</td>
      </tr>
    @empty
      <tr><td colspan="3" class="kosong">Belum ada data user.</td></tr>
    @endforelse
    </tbody>
  </table>
  </div>
@endsection
