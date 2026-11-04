@extends('layouts.app')

@section('judul', 'Dashboard')
@section('deskripsi', 'Ringkasan data aplikasi. Semua angka dihitung langsung dari database.')

@section('isi')
  <div class="ringkasan">
    <div class="kartu"><div class="judul">Penjualan hari ini</div><div class="nilai">{{ $ringkas['penjualan_hari_ini'] }}</div><div class="ket">transaksi · <x-rp :nilai="$ringkas['nilai_hari_ini']" /> <br>(tanggal {{ \Carbon\Carbon::parse($hari)->format('d/m/Y') }}, zona waktu aplikasi UTC)</div></div>
    <div class="kartu"><div class="judul">Margin aktif</div><div class="nilai">@if (! empty($marginAktif))@foreach ($marginAktif as $m)<x-persen :nilai="$m->persen" />@if (! $loop->last), @endif @endforeach @else - @endif</div><div class="ket"><a href="{{ route('margin_penjualan') }}">Lihat margin penjualan</a></div></div>
    <div class="kartu"><div class="judul">Barang aktif</div><div class="nilai">{{ $ringkas['barang_aktif'] }}</div><div class="ket"><a href="{{ route('barang') }}">Lihat barang</a></div></div>
    <div class="kartu"><div class="judul">Vendor aktif</div><div class="nilai">{{ $ringkas['vendor_aktif'] }}</div><div class="ket"><a href="{{ route('vendor') }}">Lihat vendor</a></div></div>
    <div class="kartu"><div class="judul">User</div><div class="nilai">{{ $ringkas['user'] }}</div><div class="ket"><a href="{{ route('user') }}">Lihat user</a></div></div>
  </div>

  <div class="ringkasan">
    <div class="kartu"><div class="judul">Total pengadaan</div><div class="nilai">{{ $ringkas['pengadaan'] }}</div><div class="ket"><a href="{{ route('pengadaan.index') }}">Data pengadaan</a></div></div>
    <div class="kartu"><div class="judul">Total penerimaan</div><div class="nilai">{{ $ringkas['penerimaan'] }}</div><div class="ket"><a href="{{ route('penerimaan.index') }}">Data penerimaan</a></div></div>
    <div class="kartu"><div class="judul">Total penjualan</div><div class="nilai">{{ $ringkas['penjualan'] }}</div><div class="ket"><a href="{{ route('penjualan.index') }}">Data penjualan</a></div></div>
    <div class="kartu"><div class="judul">Total retur</div><div class="nilai">{{ $ringkas['retur'] }}</div><div class="ket"><a href="{{ route('retur.index') }}">Data retur</a></div></div>
    <div class="kartu"><div class="judul">Catatan kartu stok</div><div class="nilai">{{ $ringkas['kartu_stok'] }}</div><div class="ket"><a href="{{ route('kartu_stok') }}">Kartu stok</a></div></div>
  </div>

  <div class="dua-kolom">
    <section class="kartu">
      <h2>Penjualan terakhir</h2>
      <div class="tabel-bungkus">
      <table>
        <thead><tr><th class="angka">ID</th><th>Tanggal</th><th>Kasir</th><th class="angka">Total</th></tr></thead>
        <tbody>
        @forelse ($penjualanTerakhir as $p)
          <tr>
            <td class="angka"><a href="{{ route('penjualan.show', $p->idpenjualan) }}">#{{ $p->idpenjualan }}</a></td>
            <td>{{ $p->tanggal ?? '-' }}</td>
            <td>{{ $p->username ?? '-' }}</td>
            <td class="angka"><x-rp :nilai="$p->total_nilai" /></td>
          </tr>
        @empty
          <tr><td colspan="4" class="kosong">Belum ada penjualan.</td></tr>
        @endforelse
        </tbody>
      </table>
      </div>
    </section>

    <section class="kartu">
      <h2>Pengadaan terakhir</h2>
      <div class="tabel-bungkus">
      <table>
        <thead><tr><th class="angka">ID</th><th>Tanggal</th><th>Vendor</th><th class="angka">Total</th></tr></thead>
        <tbody>
        @forelse ($pengadaanTerakhir as $p)
          <tr>
            <td class="angka"><a href="{{ route('pengadaan.show', $p->idpengadaan) }}">#{{ $p->idpengadaan }}</a></td>
            <td>{{ $p->tanggal ?? '-' }}</td>
            <td>{{ $p->nama_vendor ?? '-' }}</td>
            <td class="angka"><x-rp :nilai="$p->total_nilai" /></td>
          </tr>
        @empty
          <tr><td colspan="4" class="kosong">Belum ada pengadaan.</td></tr>
        @endforelse
        </tbody>
      </table>
      </div>
    </section>
  </div>

  <x-catatan>Stok berjalan per barang dapat dilihat di halaman <a href="{{ route('kartu_stok') }}">Kartu Stok</a>. Ringkasan stok rendah belum dihitung pada dashboard.</x-catatan>
@endsection
