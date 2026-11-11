@extends('layouts.app')

@section('judul', 'Detail Penerimaan #'.$penerimaan->idpenerimaan)
@section('grup', 'Transaksi')

@section('breadcrumb')
  <li>Transaksi</li>
  <li><a href="{{ route('penerimaan.index') }}">Penerimaan</a></li>
  <li aria-current="page">Penerimaan #{{ $penerimaan->idpenerimaan }}</li>
@endsection

@section('aksi')
  <a class="tombol sekunder" href="{{ route('penerimaan.index') }}">
    <x-ikon nama="kiri" :ukuran="15" />
    Kembali
  </a>
  <form method="POST" action="{{ route('penerimaan.destroy', $penerimaan->idpenerimaan) }}">
    @csrf
    @method('DELETE')
    <button type="button" class="tombol bahaya"
            data-konfirmasi="Hapus penerimaan #{{ $penerimaan->idpenerimaan }}? Stok yang sudah masuk akan dikoreksi otomatis.">
      <x-ikon nama="hapus" :ukuran="15" />
      Hapus
    </button>
  </form>
@endsection

@section('isi')
  @php $totalTerima = array_sum(array_map(fn ($d) => (int) $d->sub_total_terima, $penerimaan->detail)); @endphp

  <section class="kartu">
    <h2>Informasi penerimaan</h2>
    <dl class="info-daftar">
      <div><dt>ID penerimaan</dt><dd>{{ $penerimaan->idpenerimaan }}</dd></div>
      <div><dt>Tanggal</dt><dd>{{ $penerimaan->tanggal ?? '-' }}</dd></div>
      <div><dt>Pengadaan asal</dt><dd>@if ($penerimaan->idpengadaan)<a href="{{ route('pengadaan.show', $penerimaan->idpengadaan) }}">Pengadaan #{{ $penerimaan->idpengadaan }}</a>@else - @endif</dd></div>
      <div><dt>Vendor</dt><dd>{{ $penerimaan->nama_vendor ?? '-' }}</dd></div>
      <div><dt>Diterima oleh</dt><dd>{{ $penerimaan->username ?? '-' }}</dd></div>
    </dl>
  </section>

  <section class="kartu">
    <h2>Barang yang diterima</h2>
    <div class="tabel-bungkus">
    <table>
      <thead>
        <tr><th>No</th><th>Barang</th><th>Satuan</th><th class="angka">Jumlah terima</th><th class="angka">Harga satuan terima</th><th class="angka">Sub total terima</th></tr>
      </thead>
      <tbody>
      @forelse ($penerimaan->detail as $d)
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td>{{ $d->nama ?? 'Barang #'.$d->barang_idbarang }}</td>
          <td>{{ $d->nama_satuan ?? '-' }}</td>
          <td class="angka">{{ $d->jumlah_terima }}</td>
          <td class="angka"><x-rp :nilai="$d->harga_satuan_terima" /></td>
          <td class="angka"><x-rp :nilai="$d->sub_total_terima" /></td>
        </tr>
      @empty
        <tr><td colspan="6" class="kosong">Penerimaan ini belum memiliki detail barang.</td></tr>
      @endforelse
      </tbody>
      @if (! empty($penerimaan->detail))
      <tfoot>
        <tr><td colspan="5" class="angka">Total</td><td class="angka"><x-rp :nilai="$totalTerima" /></td></tr>
      </tfoot>
      @endif
    </table>
    </div>
  </section>

  <section class="kartu">
    <h2>Retur terkait</h2>
    <div class="tabel-bungkus">
    <table>
      <thead><tr><th class="kode">ID retur</th><th>Tanggal</th><th>Dibuat oleh</th><th class="aksi">Aksi</th></tr></thead>
      <tbody>
      @forelse ($penerimaan->retur as $rt)
        <tr>
          <td class="kode">{{ $rt->idretur }}</td>
          <td>{{ $rt->tanggal ?? '-' }}</td>
          <td>{{ $rt->username ?? '-' }}</td>
          <td class="aksi"><a class="tombol sekunder kecil" href="{{ route('retur.show', $rt->idretur) }}">
            <x-ikon nama="info" :ukuran="14" />
            Detail
          </a></td>
        </tr>
      @empty
        <tr><td colspan="4" class="kosong">Belum ada retur untuk penerimaan ini.</td></tr>
      @endforelse
      </tbody>
    </table>
    </div>
  </section>
@endsection
