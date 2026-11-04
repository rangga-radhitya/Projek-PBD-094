@extends('layouts.app')

@section('judul', 'Kartu Stok')
@section('grup', 'Persediaan')
@section('deskripsi', 'Riwayat pergerakan stok barang (masuk, keluar, saldo). Data dibaca dari tabel kartu_stok.')

@section('isi')
  <x-catatan>Halaman ini hanya menampilkan riwayat. Arti kode <strong>jenis transaksi</strong> belum ditetapkan, sehingga kode ditampilkan apa adanya dan kolom referensi belum berupa tautan.</x-catatan>

  <x-filter-bar :reset="route('kartu_stok')">
    <div class="grup-filter">
      <label for="idbarang">Barang</label>
      <select id="idbarang" name="idbarang">
        <option value="">Semua barang</option>
        @foreach ($daftarBarang as $b)
          <option value="{{ $b->idbarang }}" @selected($idbarang === (string) $b->idbarang)>{{ $b->nama }}</option>
        @endforeach
      </select>
    </div>
    <div class="grup-filter">
      <label for="jenis">Jenis transaksi</label>
      <select id="jenis" name="jenis">
        <option value="">Semua jenis</option>
        @foreach ($daftarJenis as $j)
          <option value="{{ $j }}" @selected($jenis === (string) $j)>{{ $j }}</option>
        @endforeach
      </select>
    </div>
    <div class="grup-filter">
      <label for="q">ID transaksi</label>
      <input type="search" id="q" name="q" value="{{ $q }}" placeholder="ID transaksi">
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
      <tr><th>Tanggal</th><th>Barang</th><th>Jenis transaksi</th><th class="angka">Masuk</th><th class="angka">Keluar</th><th class="angka">Saldo (stock)</th><th class="angka">ID transaksi</th></tr>
    </thead>
    <tbody>
    @forelse ($daftar as $r)
      <tr>
        <td>{{ $r->created_at?->format('d/m/Y H:i') ?? '-' }}</td>
        <td>{{ $r->barang?->nama ?? ($r->idbarang ? 'Barang #'.$r->idbarang : '-') }}</td>
        <td><x-badge :teks="$r->jenis_transaksi ?? '-'" /></td>
        <td class="angka">{{ $r->masuk ?? 0 }}</td>
        <td class="angka">{{ $r->keluar ?? 0 }}</td>
        <td class="angka">{{ $r->stock ?? '-' }}</td>
        <td class="angka">{{ $r->idtransaksi ?? '-' }}</td>
      </tr>
    @empty
      <tr><td colspan="7" class="kosong">{{ request()->hasAny(['q','idbarang','jenis','dari','sampai']) ? 'Tidak ada riwayat stok yang cocok dengan pencarian/filter.' : 'Belum ada riwayat stok (tabel kartu_stok masih kosong).' }}</td></tr>
    @endforelse
    </tbody>
  </table>
  </div>
  {{ $daftar->links('components.pagination') }}
@endsection
