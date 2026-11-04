@extends('layouts.app')

@section('judul', 'Data Vendor')
@section('grup', 'Master Data')
@section('deskripsi', 'Pemasok barang. Data dibaca dari view v_vendor.')

@section('aksi')
  <button type="button" class="tombol" data-buka-form="m-form" data-judul="Tambah Vendor">+ Tambah Vendor</button>
@endsection

@section('isi')
  <x-filter-bar :reset="route('vendor')">
    <div class="grup-filter">
      <label for="q">Cari nama vendor</label>
      <input type="search" id="q" name="q" value="{{ $q }}" placeholder="Nama vendor">
    </div>
    <div class="grup-filter">
      <label for="badan_hukum">Badan hukum</label>
      <select id="badan_hukum" name="badan_hukum">
        <option value="">Semua</option>
        <option value="P" @selected($badan === 'P')>PT</option>
        <option value="C" @selected($badan === 'C')>CV</option>
        <option value="U" @selected($badan === 'U')>UD</option>
      </select>
    </div>
    <div class="grup-filter">
      <label for="status">Status</label>
      <select id="status" name="status">
        <option value="">Semua status</option>
        <option value="A" @selected($status === 'A')>Aktif</option>
        <option value="N" @selected($status === 'N')>Nonaktif</option>
      </select>
    </div>
  </x-filter-bar>

  <div class="tabel-bungkus">
  <table>
    <thead>
      <tr><th class="angka">ID</th><th>Nama vendor</th><th>Badan hukum</th><th>Status</th><th class="aksi">Aksi</th></tr>
    </thead>
    <tbody>
    @forelse ($daftar as $r)
      <tr>
        <td class="angka">{{ $r->idvendor }}</td>
        <td>{{ $r->nama_vendor }}</td>
        <td>{{ $r->badan_hukum_teks }}</td>
        <td><x-badge :aktif="$r->status === 'A'" :teks="$r->status_teks" /></td>
        <td class="aksi">
          <button type="button" class="tombol sekunder kecil" data-buka-form="m-form" data-judul="Edit Vendor" data-nilai="{{ json_encode(['nama_vendor' => $r->nama_vendor, 'badan_hukum' => $r->badan_hukum, 'status' => $r->status]) }}">Edit</button>
          <button type="button" class="tombol sekunder kecil" data-konfirmasi="{{ $r->status === 'A' ? 'Nonaktifkan' : 'Aktifkan' }} vendor &quot;{{ $r->nama_vendor }}&quot;?">{{ $r->status === 'A' ? 'Nonaktifkan' : 'Aktifkan' }}</button>
        </td>
      </tr>
    @empty
      <tr><td colspan="5" class="kosong">{{ ($q !== '' || $badan !== '' || $status !== '') ? 'Tidak ada vendor yang cocok dengan pencarian/filter.' : 'Belum ada data vendor.' }}</td></tr>
    @endforelse
    </tbody>
  </table>
  </div>
  {{ $daftar->links('components.pagination') }}

  @push('modal')
    @include('partials.modal-form', ['id' => 'm-form', 'fields' => [
      ['label' => 'Nama vendor', 'name' => 'nama_vendor', 'wajib' => true, 'hint' => 'Maksimal 100 karakter.'],
      ['label' => 'Badan hukum', 'name' => 'badan_hukum', 'type' => 'select', 'wajib' => true, 'options' => ['P' => 'PT', 'C' => 'CV', 'U' => 'UD']],
      ['label' => 'Status', 'name' => 'status', 'type' => 'select', 'wajib' => true, 'options' => ['A' => 'Aktif'], 'hint' => 'Kode untuk status nonaktif belum ditetapkan (perlu konfirmasi).'],
    ]])
  @endpush
@endsection
