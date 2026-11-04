@extends('layouts.app')

@section('judul', 'Detail Pengadaan #'.$pengadaan->idpengadaan)
@section('deskripsi', 'Informasi pesanan ke vendor beserta barang yang dipesan.')

@section('breadcrumb')
  <li>Pengadaan</li>
  <li><a href="{{ route('pengadaan.index') }}">Data Pengadaan</a></li>
  <li aria-current="page">Pengadaan #{{ $pengadaan->idpengadaan }}</li>
@endsection

@section('aksi')
  <a class="tombol sekunder" href="{{ route('pengadaan.index') }}">&laquo; Kembali</a>
@endsection

@section('isi')
  <section class="kartu">
    <h2>Informasi pengadaan</h2>
    <dl class="info-daftar">
      <div><dt>ID pengadaan</dt><dd>{{ $pengadaan->idpengadaan }}</dd></div>
      <div><dt>Tanggal</dt><dd>{{ $pengadaan->timestamp?->format('d/m/Y H:i') ?? '-' }}</dd></div>
      <div><dt>Vendor</dt><dd>{{ $pengadaan->vendor ? $pengadaan->vendor->nama_vendor : '-' }}</dd></div>
      <div><dt>Dibuat oleh</dt><dd>{{ $pengadaan->user?->username ?? '-' }}</dd></div>
      <div><dt>Status</dt><dd><x-badge :teks="$pengadaan->status ?? '-'" /> <small class="hint">(arti kode belum ditetapkan)</small></dd></div>
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
        @php $sudah = (int) $diterima->get($d->idbarang, 0); @endphp
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td>{{ $d->barang?->nama ?? 'Barang #'.$d->idbarang }}</td>
          <td>{{ $d->barang?->nama_satuan ?? '-' }}</td>
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
    <p class="hint">"Sudah diterima" dijumlahkan dari seluruh penerimaan untuk pengadaan ini; "Sisa" = jumlah dipesan − sudah diterima.</p>
  </section>

  <section class="kartu">
    <h2>Penerimaan terkait</h2>
    <div class="tabel-bungkus">
    <table>
      <thead><tr><th class="angka">ID penerimaan</th><th>Tanggal</th><th>Diterima oleh</th><th>Status</th><th class="aksi">Aksi</th></tr></thead>
      <tbody>
      @forelse ($pengadaan->penerimaan as $p)
        <tr>
          <td class="angka">{{ $p->idpenerimaan }}</td>
          <td>{{ $p->created_at?->format('d/m/Y H:i') ?? '-' }}</td>
          <td>{{ $p->user?->username ?? '-' }}</td>
          <td><x-badge :teks="$p->status ?? '-'" /></td>
          <td class="aksi"><a class="tombol sekunder kecil" href="{{ route('penerimaan.show', $p->idpenerimaan) }}">Detail</a></td>
        </tr>
      @empty
        <tr><td colspan="5" class="kosong">Belum ada penerimaan untuk pengadaan ini.</td></tr>
      @endforelse
      </tbody>
    </table>
    </div>
  </section>
@endsection
