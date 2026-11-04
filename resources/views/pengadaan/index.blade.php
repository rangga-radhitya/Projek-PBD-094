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
      <label for="q">Cari ID / vendor</label>
      <input type="search" id="q" name="q" value="{{ $q }}" placeholder="ID pengadaan atau nama vendor">
    </div>
    <div class="grup-filter">
      <label for="idvendor">Vendor</label>
      <select id="idvendor" name="idvendor">
        <option value="">Semua vendor</option>
        @foreach ($daftarVendor as $v)
          <option value="{{ $v->idvendor }}" @selected($idvendor === (string) $v->idvendor)>{{ $v->nama_vendor }}</option>
        @endforeach
      </select>
    </div>
    <div class="grup-filter">
      <label for="status">Status</label>
      <select id="status" name="status">
        <option value="">Semua status</option>
        @foreach ($daftarStatus as $s)
          <option value="{{ $s }}" @selected($status === (string) $s)>{{ $s }}</option>
        @endforeach
      </select>
    </div>
    <div class="grup-filter">
      <label for="dari">Dari tanggal</label>
      <input type="date" id="dari" name="dari" value="{{ $dari }}">
    </div>
    <div class="grup-filter">
      <label for="sampai">Sampai tanggal</label>
      <input type="date" id="sampai" name="sampai" value="{{ $sampai }}">
    </div>
  </x-filter-bar>

  <div class="tabel-bungkus">
  <table>
    <thead>
      <tr><th class="kode">ID</th><th>Tanggal</th><th>Vendor</th><th>Dibuat oleh</th><th>Status</th><th class="angka">Subtotal</th><th class="angka">PPN</th><th class="angka">Total</th><th class="aksi">Aksi</th></tr>
    </thead>
    <tbody>
    @forelse ($daftar as $r)
      <tr>
        <td class="kode">{{ $r->idpengadaan }}</td>
        <td>{{ $r->tanggal ?? '-' }}</td>
        <td>{{ $r->nama_vendor ?? '-' }}</td>
        <td>{{ $r->username ?? '-' }}</td>
        <td>
          <x-badge :jenis="$r->status === 'S' ? 'sukses' : 'netral'"
                   :teks="$r->status ?? '-'"
                   :title="$r->status === 'D' ? 'Draft' : ($r->status === 'S' ? 'Selesai' : 'Status belum diketahui')" />
        </td>
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
                    data-konfirmasi="Hapus pengadaan #{{ $r->idpengadaan }}? Pengadaan yang sudah memiliki penerimaan tidak boleh dihapus.">
              <x-ikon nama="hapus" :ukuran="14" />
              Hapus
            </button>
          </form>
        </td>
      </tr>
    @empty
      <tr><td colspan="9" class="kosong">{{ request()->hasAny(['q','idvendor','status','dari','sampai']) ? 'Tidak ada pengadaan yang cocok dengan pencarian/filter.' : 'Belum ada data pengadaan.' }}</td></tr>
    @endforelse
    </tbody>
  </table>
  </div>
  {{ $daftar->links('components.pagination') }}
@endsection
