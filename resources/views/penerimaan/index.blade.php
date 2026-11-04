@extends('layouts.app')

@section('judul', 'Data Penerimaan')
@section('grup', 'Transaksi')

@section('aksi')
  <a class="tombol" href="{{ route('penerimaan.create') }}">
    <x-ikon nama="tambah" :ukuran="16" />
    Tambah Penerimaan
  </a>
@endsection

@section('isi')
  <x-filter-bar :reset="route('penerimaan.index')">
    <div class="grup-filter">
      <label for="q">Cari ID / vendor</label>
      <input type="search" id="q" name="q" value="{{ $q }}" placeholder="ID penerimaan/pengadaan atau vendor">
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
      <tr><th class="kode">ID</th><th>Tanggal</th><th>Pengadaan</th><th>Vendor</th><th>Diterima oleh</th><th>Status</th><th class="angka">Jumlah item</th><th class="aksi">Aksi</th></tr>
    </thead>
    <tbody>
    @forelse ($daftar as $r)
      <tr>
        <td class="kode">{{ $r->idpenerimaan }}</td>
        <td>{{ $r->tanggal ?? '-' }}</td>
        <td>@if ($r->idpengadaan)<a href="{{ route('pengadaan.show', $r->idpengadaan) }}">#{{ $r->idpengadaan }}</a>@else - @endif</td>
        <td>{{ $r->nama_vendor ?? '-' }}</td>
        <td>{{ $r->username ?? '-' }}</td>
        <td>
          <x-badge :jenis="$r->status === 'S' ? 'sukses' : 'netral'"
                   :teks="$r->status ?? '-'"
                   :title="$r->status === 'D' ? 'Draft' : ($r->status === 'S' ? 'Selesai' : 'Status belum diketahui')" />
        </td>
        <td class="angka">{{ $r->detail_count }}</td>
        <td class="aksi">
          <a class="tombol sekunder kecil" href="{{ route('penerimaan.show', $r->idpenerimaan) }}">
            <x-ikon nama="info" :ukuran="14" />
            Detail
          </a>
          <form method="POST" action="{{ route('penerimaan.destroy', $r->idpenerimaan) }}">
            @csrf
            @method('DELETE')
            <button type="button" class="tombol sekunder kecil"
                    data-konfirmasi="Hapus penerimaan #{{ $r->idpenerimaan }}? Stok yang sudah masuk akan dikoreksi otomatis.">
              <x-ikon nama="hapus" :ukuran="14" />
              Hapus
            </button>
          </form>
        </td>
      </tr>
    @empty
      <tr><td colspan="8" class="kosong">{{ request()->hasAny(['q','idvendor','status','dari','sampai']) ? 'Tidak ada penerimaan yang cocok dengan pencarian/filter.' : 'Belum ada data penerimaan.' }}</td></tr>
    @endforelse
    </tbody>
  </table>
  </div>
  {{ $daftar->links('components.pagination') }}
@endsection
