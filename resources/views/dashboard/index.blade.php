@extends('layouts.app')

@section('judul', 'Dashboard')

@section('isi')
  <div class="hero-grid">
    <section class="hero-penjualan">
      <div class="hero-label">
        <x-ikon nama="penjualan" :ukuran="15" />
        Penjualan hari ini
      </div>
      <div class="hero-nilai"><x-rp :nilai="$ringkas['nilai_hari_ini']" /></div>
      <div class="hero-meta">
        <span>{{ $ringkas['penjualan_hari_ini'] }} transaksi</span>
        <span aria-hidden="true">·</span>
        <span>{{ \Carbon\Carbon::parse($hari)->format('d/m/Y') }}</span>
        <span aria-hidden="true">·</span>
        <span title="Zona waktu aplikasi menggunakan UTC">UTC</span>
      </div>
      <a class="hero-tautan" href="{{ route('penjualan.index') }}">
        Lihat penjualan
        <x-ikon nama="panah" :ukuran="14" />
      </a>
    </section>

    <div class="hero-sisi">
      <section class="kartu kartu-sempit">
        <div class="hero-label">
          <x-ikon nama="margin" :ukuran="15" />
          Margin aktif
        </div>
        <div class="nilai-kecil">
          @if (! empty($marginAktif))
            @foreach ($marginAktif as $m)
              <x-persen :nilai="$m->persen" />@if (! $loop->last), @endif
            @endforeach
          @else
            -
          @endif
        </div>
        <div class="ket">
          Berlaku untuk transaksi penjualan.
          <a href="{{ route('margin_penjualan') }}">Kelola margin</a>
        </div>
      </section>
    </div>
  </div>

  <section class="kartu kpi-strip" aria-label="Ringkasan operasional">
    <a class="kpi" href="{{ route('pengadaan.index') }}">
      <span class="kpi-label">Pengadaan</span>
      <span class="kpi-nilai">{{ $ringkas['pengadaan'] }}</span>
      <span class="kpi-tautan">Data pengadaan <x-ikon nama="panah" :ukuran="13" /></span>
    </a>
    <a class="kpi" href="{{ route('penerimaan.index') }}">
      <span class="kpi-label">Penerimaan</span>
      <span class="kpi-nilai">{{ $ringkas['penerimaan'] }}</span>
      <span class="kpi-tautan">Data penerimaan <x-ikon nama="panah" :ukuran="13" /></span>
    </a>
    <a class="kpi" href="{{ route('penjualan.index') }}">
      <span class="kpi-label">Penjualan</span>
      <span class="kpi-nilai">{{ $ringkas['penjualan'] }}</span>
      <span class="kpi-tautan">Data penjualan <x-ikon nama="panah" :ukuran="13" /></span>
    </a>
    <a class="kpi" href="{{ route('retur.index') }}">
      <span class="kpi-label">Retur</span>
      <span class="kpi-nilai">{{ $ringkas['retur'] }}</span>
      <span class="kpi-tautan">Data retur <x-ikon nama="panah" :ukuran="13" /></span>
    </a>
    <a class="kpi" href="{{ route('kartu_stok') }}">
      <span class="kpi-label">Kartu stok</span>
      <span class="kpi-nilai">{{ $ringkas['kartu_stok'] }}</span>
      <span class="kpi-tautan">Riwayat stok <x-ikon nama="panah" :ukuran="13" /></span>
    </a>
    <a class="kpi" href="{{ route('barang') }}">
      <span class="kpi-label">Barang</span>
      <span class="kpi-nilai">{{ $ringkas['barang_aktif'] }}</span>
      <span class="kpi-tautan">Data barang <x-ikon nama="panah" :ukuran="13" /></span>
    </a>
    <a class="kpi" href="{{ route('vendor') }}">
      <span class="kpi-label">Vendor</span>
      <span class="kpi-nilai">{{ $ringkas['vendor_aktif'] }}</span>
      <span class="kpi-tautan">Data vendor <x-ikon nama="panah" :ukuran="13" /></span>
    </a>
    <a class="kpi" href="{{ route('user') }}">
      <span class="kpi-label">User</span>
      <span class="kpi-nilai">{{ $ringkas['user'] }}</span>
      <span class="kpi-tautan">Data user <x-ikon nama="panah" :ukuran="13" /></span>
    </a>
  </section>

  <div class="dua-kolom">
    <section class="kartu">
      <div class="panel-kepala">
        <h2>Penjualan terakhir</h2>
        <a class="panel-meta" href="{{ route('penjualan.index') }}">Lihat semua</a>
      </div>
      <div class="tabel-bungkus">
        <table>
          <thead><tr><th class="kode">ID</th><th>Tanggal</th><th>Kasir</th><th class="angka">Total</th></tr></thead>
          <tbody>
          @forelse ($penjualanTerakhir as $p)
            <tr>
              <td class="kode"><a href="{{ route('penjualan.show', $p->idpenjualan) }}">#{{ $p->idpenjualan }}</a></td>
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
      <div class="panel-kepala">
        <h2>Pengadaan terakhir</h2>
        <a class="panel-meta" href="{{ route('pengadaan.index') }}">Lihat semua</a>
      </div>
      <div class="tabel-bungkus">
        <table>
          <thead><tr><th class="kode">ID</th><th>Tanggal</th><th>Vendor</th><th class="angka">Total</th></tr></thead>
          <tbody>
          @forelse ($pengadaanTerakhir as $p)
            <tr>
              <td class="kode"><a href="{{ route('pengadaan.show', $p->idpengadaan) }}">#{{ $p->idpengadaan }}</a></td>
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

  <x-catatan ringkas judul="Catatan data">
    Stok berjalan per barang dapat dilihat di halaman <a href="{{ route('kartu_stok') }}">Kartu Stok</a>.
    Ringkasan stok rendah belum dihitung pada dashboard.
  </x-catatan>
@endsection
