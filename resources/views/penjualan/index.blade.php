@extends('layouts.app')

@section('judul', 'Data Penjualan')
@section('grup', 'Penjualan')
@section('deskripsi', 'Riwayat transaksi penjualan. Data dibaca dari tabel penjualan.')

@section('isi')
  <x-catatan>Pembuatan transaksi penjualan (kasir) belum tersedia pada tahap ini.</x-catatan>

  <x-filter-bar :reset="route('penjualan.index')">
    <div class="grup-filter">
      <label for="q">Cari ID penjualan</label>
      <input type="search" id="q" name="q" value="{{ $q }}" placeholder="ID penjualan">
    </div>
    <div class="grup-filter">
      <label for="iduser">Kasir</label>
      <select id="iduser" name="iduser">
        <option value="">Semua user</option>
        @foreach ($daftarUser as $u)
          <option value="{{ $u->iduser }}" @selected($iduser === (string) $u->iduser)>{{ $u->username }}</option>
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
      <tr><th class="angka">ID</th><th>Tanggal</th><th>Kasir</th><th class="angka">Margin</th><th class="angka">Jumlah item</th><th class="angka">Subtotal</th><th class="angka">PPN</th><th class="angka">Total</th><th class="aksi">Aksi</th></tr>
    </thead>
    <tbody>
    @forelse ($daftar as $r)
      <tr>
        <td class="angka">{{ $r->idpenjualan }}</td>
        <td>{{ $r->created_at?->format('d/m/Y H:i') ?? '-' }}</td>
        <td>{{ $r->user?->username ?? '-' }}</td>
        <td class="angka"><x-persen :nilai="$r->margin?->persen" /></td>
        <td class="angka">{{ $r->detail_count }}</td>
        <td class="angka"><x-rp :nilai="$r->subtotal_nilai" /></td>
        <td class="angka"><x-rp :nilai="$r->ppn" /></td>
        <td class="angka"><x-rp :nilai="$r->total_nilai" /></td>
        <td class="aksi"><a class="tombol sekunder kecil" href="{{ route('penjualan.show', $r->idpenjualan) }}">Detail</a></td>
      </tr>
    @empty
      <tr><td colspan="9" class="kosong">{{ request()->hasAny(['q','iduser','dari','sampai']) ? 'Tidak ada penjualan yang cocok dengan pencarian/filter.' : 'Belum ada data penjualan.' }}</td></tr>
    @endforelse
    </tbody>
  </table>
  </div>
  {{ $daftar->links('components.pagination') }}
@endsection
