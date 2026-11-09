@extends('layouts.app')

@section('judul', 'Detail Pengadaan #'.$pengadaan->idpengadaan)
@section('grup', 'Transaksi')

@section('breadcrumb')
  <li>Transaksi</li>
  <li><a href="{{ route('pengadaan.index') }}">Pengadaan</a></li>
  <li aria-current="page">Pengadaan #{{ $pengadaan->idpengadaan }}</li>
@endsection

@section('aksi')
  <a class="tombol sekunder" href="{{ route('pengadaan.index') }}">
    <x-ikon nama="kiri" :ukuran="15" />
    Kembali
  </a>
  @if (empty($pengadaan->penerimaan))
    <form method="POST" action="{{ route('pengadaan.destroy', $pengadaan->idpengadaan) }}">
      @csrf
      @method('DELETE')
      <button type="button" class="tombol bahaya" data-konfirmasi="Hapus pengadaan #{{ $pengadaan->idpengadaan }}?">
        <x-ikon nama="hapus" :ukuran="15" />
        Hapus
      </button>
    </form>
  @endif
@endsection

@section('isi')
  <section class="kartu">
    <h2>Informasi pengadaan</h2>
    <dl class="info-daftar">
      <div><dt>ID pengadaan</dt><dd>{{ $pengadaan->idpengadaan }}</dd></div>
      <div><dt>Tanggal</dt><dd>{{ $pengadaan->tanggal ?? '-' }}</dd></div>
      <div><dt>Vendor</dt><dd>{{ $pengadaan->nama_vendor ?? '-' }}</dd></div>
      <div><dt>Dibuat oleh</dt><dd>{{ $pengadaan->username ?? '-' }}</dd></div>
    </dl>
  </section>

  <section class="kartu">
    <h2>Barang yang dipesan</h2>
    <div class="tabel-bungkus">
    <table>
      <thead>
        <tr><th>No</th><th>Barang</th><th>Satuan</th><th class="angka">Harga satuan</th><th class="angka">Jumlah</th><th class="angka">Sub total</th><th class="angka">Sudah diterima</th><th class="angka">Sisa</th></tr>
      </thead>
      <tbody>
      @forelse ($pengadaan->detail as $d)
        @php $sudah = (int) ($diterima[$d->idbarang] ?? 0); @endphp
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td>{{ $d->nama ?? 'Barang #'.$d->idbarang }}</td>
          <td>{{ $d->nama_satuan ?? '-' }}</td>
          <td class="angka"><x-rp :nilai="$d->harga_satuan" /></td>
          <td class="angka">{{ $d->jumlah }}</td>
          <td class="angka"><x-rp :nilai="$d->sub_total" /></td>
          <td class="angka">{{ $sudah }}</td>
          <td class="angka">{{ (int) $d->jumlah - $sudah }}</td>
        </tr>
      @empty
        <tr><td colspan="8" class="kosong">Pengadaan ini belum memiliki detail barang.</td></tr>
      @endforelse
      </tbody>
      <tfoot>
        <tr><td colspan="5" class="angka">Subtotal</td><td class="angka"><x-rp :nilai="$pengadaan->subtotal_nilai" /></td><td colspan="2"></td></tr>
        <tr><td colspan="5" class="angka">PPN</td><td class="angka"><x-rp :nilai="$pengadaan->ppn" /></td><td colspan="2"></td></tr>
        <tr><td colspan="5" class="angka">Total</td><td class="angka"><x-rp :nilai="$pengadaan->total_nilai" /></td><td colspan="2"></td></tr>
      </tfoot>
    </table>
    </div>
    <x-catatan ringkas judul="Cara hitung" class="catatan-tabel">
      "Sudah diterima" dijumlahkan dari seluruh penerimaan untuk pengadaan ini;
      "Sisa" = jumlah dipesan − sudah diterima.
    </x-catatan>
  </section>

  <section class="kartu">
    <h2>Penerimaan terkait</h2>
    <div class="tabel-bungkus">
    <table>
      <thead><tr><th class="kode">ID</th><th>Tanggal</th><th>Diterima oleh</th><th class="aksi">Aksi</th></tr></thead>
      <tbody>
      @forelse ($pengadaan->penerimaan as $p)
        <tr>
          <td class="kode">{{ $p->idpenerimaan }}</td>
          <td>{{ $p->tanggal ?? '-' }}</td>
          <td>{{ $p->username ?? '-' }}</td>
          <td class="aksi"><a class="tombol sekunder kecil" href="{{ route('penerimaan.show', $p->idpenerimaan) }}">
            <x-ikon nama="info" :ukuran="14" />
            Detail
          </a></td>
        </tr>
      @empty
        <tr><td colspan="4" class="kosong">Belum ada penerimaan untuk pengadaan ini.</td></tr>
      @endforelse
      </tbody>
    </table>
    </div>
  </section>
@endsection
