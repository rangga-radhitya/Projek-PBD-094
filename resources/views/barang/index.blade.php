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
    <div class="grup-filter">
      <label for="jenis">Jenis</label>
      <select id="jenis" name="jenis">
        <option value="">Semua jenis</option>
        <option value="B" @selected($jenis === 'B')>Bread</option>
        <option value="D" @selected($jenis === 'D')>Drink</option>
        <option value="C" @selected($jenis === 'C')>Cake</option>
        <option value="P" @selected($jenis === 'P')>Pastry</option>
      </select>
    </div>
    <div class="grup-filter">
      <label for="idsatuan">Satuan</label>
      <select id="idsatuan" name="idsatuan">
        <option value="">Semua satuan</option>
        @foreach ($daftarSatuan as $s)
          <option value="{{ $s->idsatuan }}" @selected($idsatuan === (string) $s->idsatuan)>{{ $s->nama_satuan }}</option>
        @endforeach
      </select>
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
      <tr><th class="kode">ID</th><th>Nama barang</th><th>Jenis</th><th>Satuan</th><th class="angka">Harga</th><th>Status</th><th class="aksi">Aksi</th></tr>
    </thead>
    <tbody>
    @forelse ($daftar as $r)
      <tr>
        <td class="kode">{{ $r->idbarang }}</td>
        <td>{{ $r->nama }}</td>
        <td>{{ $r->jenis_teks }}</td>
        <td>{{ $r->nama_satuan ?? '-' }}</td>
        <td class="angka"><x-rp :nilai="$r->harga" /></td>
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
      <tr><td colspan="7" class="kosong">{{ ($q !== '' || $jenis !== '' || $idsatuan !== '' || $status !== '') ? 'Tidak ada barang yang cocok dengan pencarian/filter.' : 'Belum ada data barang.' }}</td></tr>
    @endforelse
    </tbody>
  </table>
  </div>
  {{ $daftar->links('components.pagination') }}

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
