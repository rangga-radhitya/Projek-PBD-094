<?php
// Tampilan data (hanya baca) dari VIEW v_vendor
require __DIR__ . '/includes/functions.php';
$db = db();

// ---------- READ (dari VIEW) ----------
$daftar = $db->query("SELECT idvendor, nama_vendor, badan_hukum_teks, status FROM v_vendor ORDER BY idvendor");

tampil_header('Data Vendor', 'vendor.php');
?>

  <div class="tabel-bungkus">
  <table>
    <thead><tr><th class="angka">ID</th><th>Nama vendor</th><th>Badan hukum</th><th>Status</th></tr></thead>
    <tbody>
    <?php if ($daftar->num_rows === 0): ?>
      <tr><td colspan="4" class="kosong">Belum ada data vendor.</td></tr>
    <?php endif; ?>
    <?php while ($r = $daftar->fetch_assoc()): ?>
      <tr>
        <td class="angka"><?= (int)$r['idvendor'] ?></td>
        <td><?= h($r['nama_vendor']) ?></td>
        <td><?= h($r['badan_hukum_teks']) ?></td>
        <td><?= $r['status'] === 'A' ? '<span class="lencana aktif">Aktif</span>' : '<span class="lencana nonaktif">Nonaktif</span>' ?></td>
      </tr>
    <?php endwhile; ?>
    </tbody>
  </table>
  </div>

<?php tampil_footer(); ?>
