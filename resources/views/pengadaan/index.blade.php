@extends('layouts.app')

@section('judul', 'Data Pengadaan')
@section('grup', 'Pengadaan')
@section('deskripsi', 'Pesanan barang ke vendor. Data dibaca dari tabel pengadaan.')

@section('isi')
  <x-catatan>Kode <strong>status</strong> ditampilkan apa adanya karena arti kodenya belum ditetapkan. Pembuatan pengadaan baru belum tersedia pada tahap ini.</x-catatan>

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
      <tr><th class="angka">ID</th><th>Tanggal</th><th>Vendor</th><th>Dibuat oleh</th><th>Status</th><th class="angka">Subtotal</th><th class="angka">PPN</th><th class="angka">Total</th><th class="aksi">Aksi</th></tr>
    </thead>
    <tbody>
    @forelse ($daftar as $r)
      <tr>
        <td class="angka">{{ $r->idpengadaan }}</td>
        <td>{{ $r->timestamp?->format('d/m/Y H:i') ?? '-' }}</td>
        <td>{{ $r->vendor?->nama_vendor ?? '-' }}</td>
        <td>{{ $r->user?->username ?? '-' }}</td>
        <td><x-badge :teks="$r->status ?? '-'" /></td>
        <td class="angka"><x-rp :nilai="$r->subtotal_nilai" /></td>
        <td class="angka"><x-rp :nilai="$r->ppn" /></td>
        <td class="angka"><x-rp :nilai="$r->total_nilai" /></td>
        <td class="aksi"><a class="tombol sekunder kecil" href="{{ route('pengadaan.show', $r->idpengadaan) }}">Detail</a></td>
      </tr>
    @empty
      <tr><td colspan="9" class="kosong">{{ request()->hasAny(['q','idvendor','status','dari','sampai']) ? 'Tidak ada pengadaan yang cocok dengan pencarian/filter.' : 'Belum ada data pengadaan.' }}</td></tr>
    @endforelse
    </tbody>
  </table>
  </div>
  {{ $daftar->links('components.pagination') }}
@endsection
