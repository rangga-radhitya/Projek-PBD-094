<?php
// Tampilan data (hanya baca) dari VIEW v_barang
require __DIR__ . '/includes/functions.php';
$db = db();

// ---------- READ (dari VIEW) ----------
$daftar = $db->query("SELECT idbarang, nama, jenis_teks, nama_satuan, harga, status FROM v_barang ORDER BY idbarang");

tampil_header('Data Barang', 'barang.php');
?>

  <div class="tabel-bungkus">
  <table>
    <thead>
      <tr><th class="angka">ID</th><th>Nama barang</th><th>Jenis</th><th>Satuan</th><th class="angka">Harga</th><th>Status</th></tr>
    </thead>
    <tbody>
    <?php if ($daftar->num_rows === 0): ?>
      <tr><td colspan="6" class="kosong">Belum ada data barang.</td></tr>
    <?php endif; ?>
    <?php while ($r = $daftar->fetch_assoc()): ?>
      <tr>
        <td class="angka"><?= (int)$r['idbarang'] ?></td>
        <td><?= h($r['nama']) ?></td>
        <td><?= h($r['jenis_teks']) ?></td>
        <td><?= h($r['nama_satuan'] ?? '-') ?></td>
        <td class="angka">Rp <?= number_format((int)$r['harga'], 0, ',', '.') ?></td>
        <td><?= $r['status'] == 1 ? '<span class="lencana aktif">Aktif</span>' : '<span class="lencana nonaktif">Nonaktif</span>' ?></td>
      </tr>
    <?php endwhile; ?>
    </tbody>
  </table>
  </div>

<?php tampil_footer(); ?>
