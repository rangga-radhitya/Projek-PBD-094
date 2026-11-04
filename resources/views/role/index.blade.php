@extends('layouts.app')

@section('judul', 'Data Role')
@section('grup', 'Master Data')
@section('deskripsi', 'Peran pengguna aplikasi. Data dibaca dari view v_role.')

@section('aksi')
  <button type="button" class="tombol" data-buka-form="m-form" data-judul="Tambah Role">+ Tambah Role</button>
@endsection

@section('isi')
  <x-filter-bar :reset="route('role')">
    <div class="grup-filter">
      <label for="q">Cari nama role</label>
      <input type="search" id="q" name="q" value="{{ $q }}" placeholder="Nama role">
    </div>
  </x-filter-bar>

  <div class="tabel-bungkus">
  <table>
    <thead>
      <tr><th class="angka">ID</th><th>Nama role</th><th class="aksi">Aksi</th></tr>
    </thead>
    <tbody>
    @forelse ($daftar as $r)
      <tr>
        <td class="angka">{{ $r->idrole }}</td>
        <td>{{ $r->nama_role }}</td>
        <td class="aksi">
          <button type="button" class="tombol sekunder kecil" data-buka-form="m-form" data-judul="Edit Role" data-nilai="{{ json_encode(['nama_role' => $r->nama_role]) }}">Edit</button>
          <button type="button" class="tombol sekunder kecil" data-konfirmasi="Hapus role &quot;{{ $r->nama_role }}&quot;? Role yang masih dipakai user tidak boleh dihapus.">Hapus</button>
        </td>
      </tr>
    @empty
      <tr><td colspan="3" class="kosong">{{ $q !== '' ? 'Tidak ada role yang cocok dengan pencarian.' : 'Belum ada data role.' }}</td></tr>
    @endforelse
    </tbody>
  </table>
  </div>
  {{ $daftar->links('components.pagination') }}

  @push('modal')
    @include('partials.modal-form', ['id' => 'm-form', 'fields' => [
      ['label' => 'Nama role', 'name' => 'nama_role', 'wajib' => true, 'hint' => 'Maksimal 100 karakter.'],
    ]])
  @endpush
@endsection
