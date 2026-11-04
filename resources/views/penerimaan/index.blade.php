@extends('layouts.app')

@section('judul', 'Data Penerimaan')
@section('grup', 'Penerimaan')
@section('deskripsi', 'Penerimaan barang berdasarkan pengadaan. Data dibaca dari tabel penerimaan.')

@section('isi')
  <x-catatan>Kode <strong>status</strong> ditampilkan apa adanya karena arti kodenya belum ditetapkan. Pencatatan penerimaan baru belum tersedia pada tahap ini.</x-catatan>

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
      <tr><th class="angka">ID</th><th>Tanggal</th><th>Pengadaan</th><th>Vendor</th><th>Diterima oleh</th><th>Status</th><th class="angka">Jumlah item</th><th class="aksi">Aksi</th></tr>
    </thead>
    <tbody>
    @forelse ($daftar as $r)
      <tr>
        <td class="angka">{{ $r->idpenerimaan }}</td>
        <td>{{ $r->created_at?->format('d/m/Y H:i') ?? '-' }}</td>
        <td>@if ($r->idpengadaan)<a href="{{ route('pengadaan.show', $r->idpengadaan) }}">#{{ $r->idpengadaan }}</a>@else - @endif</td>
        <td>{{ $r->pengadaan?->vendor?->nama_vendor ?? '-' }}</td>
        <td>{{ $r->user?->username ?? '-' }}</td>
        <td><x-badge :teks="$r->status ?? '-'" /></td>
        <td class="angka">{{ $r->detail_count }}</td>
        <td class="aksi"><a class="tombol sekunder kecil" href="{{ route('penerimaan.show', $r->idpenerimaan) }}">Detail</a></td>
      </tr>
    @empty
      <tr><td colspan="8" class="kosong">{{ request()->hasAny(['q','idvendor','status','dari','sampai']) ? 'Tidak ada penerimaan yang cocok dengan pencarian/filter.' : 'Belum ada data penerimaan.' }}</td></tr>
    @endforelse
    </tbody>
  </table>
  </div>
  {{ $daftar->links('components.pagination') }}
@endsection
