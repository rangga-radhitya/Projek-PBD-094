{{-- Laporan Penjualan: rekap dari tabel penjualan dan detail_penjualan (baca saja). --}}
<x-filter-bar :reset="route('laporan.show', 'penjualan')">
  <div class="grup-filter">
    <label for="dari">Dari tanggal</label>
    <input type="date" id="dari" name="dari" value="{{ $dari }}" required>
  </div>
  <div class="grup-filter">
    <label for="sampai">Sampai tanggal</label>
    <input type="date" id="sampai" name="sampai" value="{{ $sampai }}" required>
  </div>
</x-filter-bar>

@if ($galat)
  <div class="peringatan gagal" role="alert">{{ $galat }}</div>
@elseif ($rekap === null)
  <x-catatan>Pilih rentang tanggal lalu tekan <strong>Cari</strong> untuk menampilkan laporan penjualan.</x-catatan>
@else
  <div class="ringkasan">
    <div class="kartu"><div class="judul">Jumlah transaksi</div><div class="nilai">{{ $rekap['jumlah_transaksi'] }}</div><div class="ket">{{ \Carbon\Carbon::parse($dari)->format('d/m/Y') }} – {{ \Carbon\Carbon::parse($sampai)->format('d/m/Y') }}</div></div>
    <div class="kartu"><div class="judul">Total pendapatan</div><div class="nilai"><x-rp :nilai="$rekap['total_pendapatan']" /></div><div class="ket">jumlah subtotal dari detail (sebelum PPN)</div></div>
    <div class="kartu"><div class="judul">Total PPN tercatat</div><div class="nilai"><x-rp :nilai="$rekap['total_ppn']" /></div><div class="ket">dari kolom ppn di header penjualan</div></div>
    <div class="kartu"><div class="judul">Total nilai tercatat</div><div class="nilai"><x-rp :nilai="$rekap['total_nilai_tercatat']" /></div><div class="ket">dari kolom total_nilai di header penjualan</div></div>
  </div>

  <section class="kartu">
    <div class="panel-kepala">
      <h2>Rincian transaksi</h2>
      <button type="button" class="tombol sekunder kecil" onclick="window.print()">
        <x-ikon nama="cetak" :ukuran="14" />
        Cetak
      </button>
    </div>
    <div class="tabel-bungkus">
    <table>
      <thead>
        <tr><th class="kode">ID</th><th>Tanggal</th><th>Kasir</th><th class="angka">Item</th><th class="angka">Subtotal (dihitung)</th><th class="angka">PPN</th><th class="angka">Total tercatat</th><th>Aksi</th></tr>
      </thead>
      <tbody>
      @forelse ($rincian as $r)
        <tr>
          <td class="kode">{{ $r['idpenjualan'] }}</td>
          <td>{{ \Carbon\Carbon::parse($r['created_at'])->format('d/m/Y H:i') }}</td>
          <td>{{ $r['kasir'] ?? '-' }}</td>
          <td class="angka">{{ $r['jumlah_item'] }}</td>
          <td class="angka"><x-rp :nilai="$r['subtotal_hitung']" /></td>
          <td class="angka"><x-rp :nilai="$r['ppn']" /></td>
          <td class="angka"><x-rp :nilai="$r['total_nilai']" /></td>
          <td class="aksi"><a class="tombol sekunder kecil" href="{{ route('penjualan.show', $r['idpenjualan']) }}">
            <x-ikon nama="info" :ukuran="14" />
            Detail
          </a></td>
        </tr>
      @empty
        <tr><td colspan="8" class="kosong">Tidak ada transaksi penjualan pada periode ini.</td></tr>
      @endforelse
      </tbody>
    </table>
    </div>
    <x-catatan ringkas judul="Cara hitung" class="catatan-tabel">
      Hanya transaksi yang memiliki minimal satu detail barang yang dihitung.
      Subtotal dihitung dari detail (jumlah × harga satuan).
    </x-catatan>
  </section>
@endif
