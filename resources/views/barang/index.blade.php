@extends('layouts.app')

@section('judul', 'Data Barang')
@section('grup', 'Tabel Master')

@section('aksi')
  <button type="button" class="tombol" data-buka-form="m-form" data-judul="Tambah Barang"
          data-aksi="{{ route('barang.store') }}" data-metode="POST">
    <x-ikon nama="tambah" :ukuran="16" />
    Tambah Barang
  </button>
@endsection

@section('isi')
  <x-filter-bar :reset="route('barang')">
    <div class="grup-filter">
      <label for="q">Cari nama barang</label>
      <input type="search" id="q" name="q" value="{{ $q }}" placeholder="Nama barang">
    </div>
  </x-filter-bar>

  <div data-hasil-daftar>
  <div class="tabel-bungkus">
  <table>
    <thead>
      <tr><th class="kode">ID</th><th>Nama barang</th><th>Jenis</th><th>Satuan</th><th class="angka">Harga</th><th class="angka">Stok</th><th>Status</th><th class="aksi">Aksi</th></tr>
    </thead>
    <tbody>
    @forelse ($daftar as $r)
      <tr>
        <td class="kode">{{ $r->idbarang }}</td>
        <td>{{ $r->nama }}</td>
        <td>{{ $r->jenis_teks }}</td>
        <td>{{ $r->nama_satuan ?? '-' }}</td>
        <td class="angka"><x-rp :nilai="$r->harga" /></td>
        <td class="angka">{{ $r->stok }}</td>
        <td><x-badge :jenis="$r->status == 1 ? 'sukses' : 'netral'" :teks="$r->status == 1 ? 'Aktif' : 'Nonaktif'" /></td>
        <td class="aksi">
          <button type="button" class="tombol sekunder kecil" data-buka-form="m-form" data-judul="Edit Barang"
                  data-aksi="{{ route('barang.update', $r->idbarang) }}" data-metode="PUT"
                  data-nilai="{{ json_encode(['nama' => $r->nama, 'jenis' => $r->jenis, 'idsatuan' => $r->idsatuan, 'harga' => $r->harga, 'status' => $r->status]) }}">
            <x-ikon nama="edit" :ukuran="14" />
            Edit
          </button>
          <form method="POST" action="{{ route('barang.status', $r->idbarang) }}">
            @csrf
            @method('PATCH')
            <button type="button" class="tombol sekunder kecil"
                    data-konfirmasi="{{ $r->status == 1 ? 'Nonaktifkan' : 'Aktifkan' }} barang &quot;{{ $r->nama }}&quot;?">
              <x-ikon :nama="$r->status == 1 ? 'tutup' : 'centang'" :ukuran="14" />
              {{ $r->status == 1 ? 'Nonaktifkan' : 'Aktifkan' }}
            </button>
          </form>
        </td>
      </tr>
    @empty
      <tr><td colspan="8" class="kosong">{{ $q !== '' ? 'Tidak ada barang yang cocok dengan pencarian.' : 'Belum ada data barang.' }}</td></tr>
    @endforelse
    </tbody>
  </table>
  </div>
  {{ $daftar->links('components.pagination') }}
  </div>

  @push('modal')
    @include('partials.modal-form', ['id' => 'm-form', 'aksi' => route('barang.store'), 'judul' => 'Tambah Barang', 'fields' => [
      ['label' => 'Nama barang', 'name' => 'nama', 'wajib' => true, 'hint' => 'Maksimal 45 karakter.'],
      ['label' => 'Jenis', 'name' => 'jenis', 'type' => 'select', 'wajib' => true, 'options' => ['B' => 'Bread', 'D' => 'Drink', 'C' => 'Cake', 'P' => 'Pastry']],
      ['label' => 'Satuan', 'name' => 'idsatuan', 'type' => 'select', 'wajib' => true, 'options' => $opsiSatuan],
      ['label' => 'Harga jual (Rp)', 'name' => 'harga', 'type' => 'number', 'min' => 0, 'wajib' => true],
      ['label' => 'Status', 'name' => 'status', 'type' => 'select', 'wajib' => true, 'options' => ['1' => 'Aktif', '0' => 'Nonaktif']],
    ]])
  @endpush
@endsection
