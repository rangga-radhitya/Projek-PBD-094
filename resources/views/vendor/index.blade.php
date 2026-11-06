@extends('layouts.app')

@section('judul', 'Data Vendor')
@section('grup', 'Tabel Master')

@section('aksi')
  <button type="button" class="tombol" data-buka-form="m-form" data-judul="Tambah Vendor"
          data-aksi="{{ route('vendor.store') }}" data-metode="POST">
    <x-ikon nama="tambah" :ukuran="16" />
    Tambah Vendor
  </button>
@endsection

@section('isi')
  <x-filter-bar :reset="route('vendor')">
    <div class="grup-filter">
      <label for="q">Cari nama vendor</label>
      <input type="search" id="q" name="q" value="{{ $q }}" placeholder="Nama vendor">
    </div>
  </x-filter-bar>

  <div data-hasil-daftar>
  <div class="tabel-bungkus">
  <table>
    <thead>
      <tr><th class="kode">ID</th><th>Nama vendor</th><th>Badan hukum</th><th>Status</th><th class="aksi">Aksi</th></tr>
    </thead>
    <tbody>
    @forelse ($daftar as $r)
      <tr>
        <td class="kode">{{ $r->idvendor }}</td>
        <td>{{ $r->nama_vendor }}</td>
        <td>{{ $r->badan_hukum_teks }}</td>
        <td><x-badge :jenis="$r->status === 'A' ? 'sukses' : 'netral'" :teks="$r->status_teks" /></td>
        <td class="aksi">
          <button type="button" class="tombol sekunder kecil" data-buka-form="m-form" data-judul="Edit Vendor"
                  data-aksi="{{ route('vendor.update', $r->idvendor) }}" data-metode="PUT"
                  data-nilai="{{ json_encode(['nama_vendor' => $r->nama_vendor, 'badan_hukum' => $r->badan_hukum, 'status' => $r->status]) }}">
            <x-ikon nama="edit" :ukuran="14" />
            Edit
          </button>
          <form method="POST" action="{{ route('vendor.status', $r->idvendor) }}">
            @csrf
            @method('PATCH')
            <button type="button" class="tombol sekunder kecil"
                    data-konfirmasi="{{ $r->status === 'A' ? 'Nonaktifkan' : 'Aktifkan' }} vendor &quot;{{ $r->nama_vendor }}&quot;?">
              <x-ikon :nama="$r->status === 'A' ? 'tutup' : 'centang'" :ukuran="14" />
              {{ $r->status === 'A' ? 'Nonaktifkan' : 'Aktifkan' }}
            </button>
          </form>
        </td>
      </tr>
    @empty
      <tr><td colspan="5" class="kosong">{{ $q !== '' ? 'Tidak ada vendor yang cocok dengan pencarian.' : 'Belum ada data vendor.' }}</td></tr>
    @endforelse
    </tbody>
  </table>
  </div>
  {{ $daftar->links('components.pagination') }}
  </div>

  @push('modal')
    @include('partials.modal-form', ['id' => 'm-form', 'aksi' => route('vendor.store'), 'judul' => 'Tambah Vendor', 'fields' => [
      ['label' => 'Nama vendor', 'name' => 'nama_vendor', 'wajib' => true, 'hint' => 'Maksimal 100 karakter.'],
      ['label' => 'Badan hukum', 'name' => 'badan_hukum', 'type' => 'select', 'wajib' => true, 'options' => ['P' => 'PT', 'C' => 'CV', 'U' => 'UD']],
      ['label' => 'Status', 'name' => 'status', 'type' => 'select', 'wajib' => true, 'options' => ['A' => 'Aktif', 'N' => 'Nonaktif']],
    ]])
  @endpush
@endsection
