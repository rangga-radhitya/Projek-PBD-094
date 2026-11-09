@extends('layouts.app')

@section('judul', 'Data Pengadaan')
@section('grup', 'Transaksi')

@section('aksi')
  <a class="tombol" href="{{ route('pengadaan.create') }}">
    <x-ikon nama="tambah" :ukuran="16" />
    Tambah Pengadaan
  </a>
@endsection

@section('isi')
  <x-filter-bar :reset="route('pengadaan.index')">
    <div class="grup-filter">
      <label for="q">Cari nama / ID</label>
      <input type="search" id="q" name="q" value="{{ $q }}" placeholder="Nama vendor atau ID pengadaan">
    </div>
  </x-filter-bar>

  <div data-hasil-daftar>
  <div class="tabel-bungkus">
  <table>
    <thead>
      <tr><th class="kode">ID</th><th>Tanggal</th><th>Vendor</th><th>Dibuat oleh</th><th class="angka">Subtotal</th><th class="angka">PPN</th><th class="angka">Total</th><th class="aksi">Aksi</th></tr>
    </thead>
    <tbody>
    @forelse ($daftar as $r)
      <tr>
        <td class="kode">{{ $r->idpengadaan }}</td>
        <td>{{ $r->tanggal ?? '-' }}</td>
        <td>{{ $r->nama_vendor ?? '-' }}</td>
        <td>{{ $r->username ?? '-' }}</td>
        <td class="angka"><x-rp :nilai="$r->subtotal_nilai" /></td>
        <td class="angka"><x-rp :nilai="$r->ppn" /></td>
        <td class="angka"><x-rp :nilai="$r->total_nilai" /></td>
        <td class="aksi">
          <a class="tombol sekunder kecil" href="{{ route('pengadaan.show', $r->idpengadaan) }}">
            <x-ikon nama="info" :ukuran="14" />
            Detail
          </a>
          <form method="POST" action="{{ route('pengadaan.destroy', $r->idpengadaan) }}">
            @csrf
            @method('DELETE')
            <button type="button" class="tombol sekunder kecil"
                    data-konfirmasi="Hapus pengadaan ID {{ $r->idpengadaan }}? Pengadaan yang sudah memiliki penerimaan tidak boleh dihapus.">
              <x-ikon nama="hapus" :ukuran="14" />
              Hapus
            </button>
          </form>
        </td>
      </tr>
    @empty
      <tr><td colspan="8" class="kosong">{{ $q !== '' ? 'Tidak ada pengadaan yang cocok dengan pencarian.' : 'Belum ada data pengadaan.' }}</td></tr>
    @endforelse
    </tbody>
  </table>
  </div>
  {{ $daftar->links('components.pagination') }}
  </div>
@endsection
