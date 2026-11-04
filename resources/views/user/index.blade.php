@extends('layouts.app')

@section('judul', 'Data User')
@section('grup', 'Tabel Master')

@section('aksi')
  <button type="button" class="tombol" data-buka-form="m-form" data-judul="Tambah User"
          data-aksi="{{ route('user.store') }}" data-metode="POST">
    <x-ikon nama="tambah" :ukuran="16" />
    Tambah User
  </button>
@endsection

@section('isi')
  <x-filter-bar :reset="route('user')">
    <div class="grup-filter">
      <label for="q">Cari username</label>
      <input type="search" id="q" name="q" value="{{ $q }}" placeholder="Username">
    </div>
    <div class="grup-filter">
      <label for="idrole">Role</label>
      <select id="idrole" name="idrole">
        <option value="">Semua role</option>
        @foreach ($daftarRole as $role)
          <option value="{{ $role->idrole }}" @selected($idrole === (string) $role->idrole)>{{ $role->nama_role }}</option>
        @endforeach
      </select>
    </div>
  </x-filter-bar>

  <div class="tabel-bungkus">
  <table>
    <thead>
      <tr><th class="kode">ID</th><th>Username</th><th>Role</th><th class="aksi">Aksi</th></tr>
    </thead>
    <tbody>
    @forelse ($daftar as $r)
      <tr>
        <td class="kode">{{ $r->iduser }}</td>
        <td>{{ $r->username }}</td>
        <td>{{ $r->nama_role }}</td>
        <td class="aksi">
          <button type="button" class="tombol sekunder kecil" data-buka-form="m-form" data-judul="Edit User"
                  data-aksi="{{ route('user.update', $r->iduser) }}" data-metode="PUT"
                  data-nilai="{{ json_encode(['username' => $r->username, 'idrole' => $r->idrole]) }}">
            <x-ikon nama="edit" :ukuran="14" />
            Edit
          </button>
          <form method="POST" action="{{ route('user.destroy', $r->iduser) }}">
            @csrf
            @method('DELETE')
            <button type="button" class="tombol sekunder kecil"
                    data-konfirmasi="Hapus user &quot;{{ $r->username }}&quot;? User yang sudah dipakai pada transaksi tidak boleh dihapus.">
              <x-ikon nama="hapus" :ukuran="14" />
              Hapus
            </button>
          </form>
        </td>
      </tr>
    @empty
      <tr><td colspan="4" class="kosong">{{ ($q !== '' || $idrole !== '') ? 'Tidak ada user yang cocok dengan pencarian/filter.' : 'Belum ada data user.' }}</td></tr>
    @endforelse
    </tbody>
  </table>
  </div>
  {{ $daftar->links('components.pagination') }}

  @php
    $opsiRole = [];
    foreach ($daftarRole as $role) {
        $opsiRole[(string) $role->idrole] = $role->nama_role;
    }
  @endphp

  @push('modal')
    @include('partials.modal-form', ['id' => 'm-form', 'aksi' => route('user.store'), 'judul' => 'Tambah User', 'fields' => [
      ['label' => 'Username', 'name' => 'username', 'wajib' => true, 'hint' => 'Maksimal 45 karakter.'],
      ['label' => 'Password', 'name' => 'password', 'type' => 'password', 'hint' => 'Wajib saat tambah; kosongkan saat edit bila tidak diganti.'],
      ['label' => 'Role', 'name' => 'idrole', 'type' => 'select', 'wajib' => true, 'options' => $opsiRole],
    ]])
  @endpush
@endsection
