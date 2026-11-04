@extends('layouts.app')

@section('judul', 'Kartu Stok')
@section('grup', 'Persediaan')

@section('isi')
  <x-catatan ringkas judul="Arti kode jenis transaksi">
    <p><strong>M</strong> = stok masuk (penerimaan atau pembatalan retur). <strong>K</strong> = stok keluar/koreksi (pembatalan penerimaan). <strong>R</strong> = retur ke vendor.</p>
    <p>Saldo (<code>stock</code>) adalah saldo berjalan per barang.</p>
  </x-catatan>

  @php
    $lencanaJenis = [
        'M' => ['kelas' => 'sukses', 'arti' => 'Stok masuk'],
        'K' => ['kelas' => 'bahaya', 'arti' => 'Stok keluar/koreksi'],
        'R' => ['kelas' => 'proses', 'arti' => 'Retur ke vendor'],
    ];
  @endphp

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
      <tr><th>Tanggal</th><th>Barang</th><th>Jenis transaksi</th><th class="angka">Masuk</th><th class="angka">Keluar</th><th class="angka">Saldo (stock)</th><th class="kode">ID transaksi</th></tr>
    </thead>
    <tbody>
    @forelse ($daftar as $r)
      <tr>
        <td>{{ $r->tanggal ?? '-' }}</td>
        <td>{{ $r->nama_barang ?? ($r->idbarang ? 'Barang #'.$r->idbarang : '-') }}</td>
        <td>
          <x-badge :jenis="$lencanaJenis[$r->jenis_transaksi]['kelas'] ?? 'netral'"
                   :teks="$r->jenis_transaksi ?? '-'"
                   :title="$lencanaJenis[$r->jenis_transaksi]['arti'] ?? 'Arti kode belum diketahui'" />
        </td>
        <td class="angka">{{ $r->masuk ?? 0 }}</td>
        <td class="angka">{{ $r->keluar ?? 0 }}</td>
        <td class="angka">{{ $r->stock ?? '-' }}</td>
        <td class="kode">{{ $r->idtransaksi ?? '-' }}</td>
      </tr>
    @empty
      <tr><td colspan="7" class="kosong">{{ request()->hasAny(['q','idbarang','jenis','dari','sampai']) ? 'Tidak ada riwayat stok yang cocok dengan pencarian/filter.' : 'Belum ada riwayat stok (tabel kartu_stok masih kosong).' }}</td></tr>
    @endforelse
    </tbody>
  </table>
  </div>
  {{ $daftar->links('components.pagination') }}
@endsection
