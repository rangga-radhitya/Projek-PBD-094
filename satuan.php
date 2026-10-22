<?php
// Tampilan data (hanya baca) dari VIEW v_satuan
require __DIR__ . '/includes/functions.php';
$db = db();

// ---------- READ (dari VIEW) ----------
$daftar = $db->query("SELECT idsatuan, nama_satuan, status FROM v_satuan ORDER BY idsatuan");

tampil_header('Data Satuan', 'satuan.php');
?>

  <div class="tabel-bungkus">
  <table>
    <thead><tr><th class="angka">ID</th><th>Nama satuan</th><th>Status</th></tr></thead>
    <tbody>
    <?php if ($daftar->num_rows === 0): ?>
      <tr><td colspan="3" class="kosong">Belum ada data satuan.</td></tr>
    <?php endif; ?>
    <?php while ($r = $daftar->fetch_assoc()): ?>
      <tr>
        <td class="angka"><?= (int)$r['idsatuan'] ?></td>
        <td><?= h($r['nama_satuan']) ?></td>
        <td><?= $r['status'] == 1 ? '<span class="lencana aktif">Aktif</span>' : '<span class="lencana nonaktif">Nonaktif</span>' ?></td>
      </tr>
    <?php endwhile; ?>
    </tbody>
  </table>
  </div>

<?php tampil_footer(); ?>
