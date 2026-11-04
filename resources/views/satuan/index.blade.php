@extends('layouts.app')

@section('judul', 'Data Satuan')
@section('grup', 'Tabel Master')

@section('aksi')
  <button type="button" class="tombol" data-buka-form="m-form" data-judul="Tambah Satuan"
          data-aksi="{{ route('satuan.store') }}" data-metode="POST">
    <x-ikon nama="tambah" :ukuran="16" />
    Tambah Satuan
  </button>
@endsection

@section('isi')
  <x-filter-bar :reset="route('satuan')">
    <div class="grup-filter">
      <label for="q">Cari nama satuan</label>
      <input type="search" id="q" name="q" value="{{ $q }}" placeholder="Nama satuan">
    </div>
    <div class="grup-filter">
      <label for="status">Status</label>
      <select id="status" name="status">
        <option value="">Semua status</option>
        <option value="1" @selected($status === '1')>Aktif</option>
        <option value="0" @selected($status === '0')>Nonaktif</option>
      </select>
    </div>
  </x-filter-bar>

  <div class="tabel-bungkus">
  <table>
    <thead>
      <tr><th class="kode">ID</th><th>Nama satuan</th><th>Status</th><th class="aksi">Aksi</th></tr>
    </thead>
    <tbody>
    @forelse ($daftar as $r)
      <tr>
        <td class="kode">{{ $r->idsatuan }}</td>
        <td>{{ $r->nama_satuan }}</td>
        <td><x-badge :jenis="$r->status == 1 ? 'sukses' : 'netral'" :teks="$r->status_teks" /></td>
        <td class="aksi">
          <button type="button" class="tombol sekunder kecil" data-buka-form="m-form" data-judul="Edit Satuan"
                  data-aksi="{{ route('satuan.update', $r->idsatuan) }}" data-metode="PUT"
                  data-nilai="{{ json_encode(['nama_satuan' => $r->nama_satuan, 'status' => $r->status]) }}">
            <x-ikon nama="edit" :ukuran="14" />
            Edit
          </button>
          <form method="POST" action="{{ route('satuan.status', $r->idsatuan) }}">
            @csrf
            @method('PATCH')
            <button type="button" class="tombol sekunder kecil"
                    data-konfirmasi="{{ $r->status == 1 ? 'Nonaktifkan' : 'Aktifkan' }} satuan &quot;{{ $r->nama_satuan }}&quot;?">
              <x-ikon :nama="$r->status == 1 ? 'tutup' : 'centang'" :ukuran="14" />
              {{ $r->status == 1 ? 'Nonaktifkan' : 'Aktifkan' }}
            </button>
          </form>
        </td>
      </tr>
    @empty
      <tr><td colspan="4" class="kosong">{{ ($q !== '' || $status !== '') ? 'Tidak ada satuan yang cocok dengan pencarian/filter.' : 'Belum ada data satuan.' }}</td></tr>
    @endforelse
    </tbody>
  </table>
  </div>
  {{ $daftar->links('components.pagination') }}

  @push('modal')
    @include('partials.modal-form', ['id' => 'm-form', 'aksi' => route('satuan.store'), 'judul' => 'Tambah Satuan', 'fields' => [
      ['label' => 'Nama satuan', 'name' => 'nama_satuan', 'wajib' => true, 'hint' => 'Maksimal 45 karakter.'],
      ['label' => 'Status', 'name' => 'status', 'type' => 'select', 'wajib' => true, 'options' => ['1' => 'Aktif', '0' => 'Nonaktif']],
    ]])
  @endpush
@endsection
