@extends('layouts.app')

@section('judul', 'Data Penjualan')
@section('grup', 'Transaksi')

@section('isi')

  <x-filter-bar :reset="route('penjualan.index')">
    <div class="grup-filter">
      <label for="q">Cari nama / ID</label>
      <input type="search" id="q" name="q" value="{{ $q }}" placeholder="Username kasir atau ID penjualan">
    </div>
  </x-filter-bar>

  <div data-hasil-daftar>
  <div class="tabel-bungkus">
  <table>
    <thead>
      <tr><th class="kode">ID</th><th>Tanggal</th><th>Kasir</th><th class="angka">Margin</th><th class="angka">Jumlah item</th><th class="angka">Subtotal</th><th class="angka">PPN</th><th class="angka">Total</th><th class="aksi">Aksi</th></tr>
    </thead>
    <tbody>
    @forelse ($daftar as $r)
      <tr>
        <td class="kode">{{ $r->idpenjualan }}</td>
        <td>{{ $r->tanggal ?? '-' }}</td>
        <td>{{ $r->username ?? '-' }}</td>
        <td class="angka"><x-persen :nilai="$r->persen" /></td>
        <td class="angka">{{ $r->detail_count }}</td>
        <td class="angka"><x-rp :nilai="$r->subtotal_nilai" /></td>
        <td class="angka"><x-rp :nilai="$r->ppn" /></td>
        <td class="angka"><x-rp :nilai="$r->total_nilai" /></td>
        <td class="aksi"><a class="tombol sekunder kecil" href="{{ route('penjualan.show', $r->idpenjualan) }}">
          <x-ikon nama="info" :ukuran="14" />
          Detail
        </a></td>
      </tr>
    @empty
      <tr><td colspan="9" class="kosong">{{ $q !== '' ? 'Tidak ada penjualan yang cocok dengan pencarian.' : 'Belum ada data penjualan.' }}</td></tr>
    @endforelse
    </tbody>
  </table>
  </div>
  {{ $daftar->links('components.pagination') }}
  </div>
@endsection
