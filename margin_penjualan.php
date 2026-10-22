<?php
// Tampilan data (hanya baca) dari VIEW v_margin_penjualan
require __DIR__ . '/includes/functions.php';
$db = db();

// ---------- READ (dari VIEW) ----------
$daftar = $db->query("SELECT idmargin_penjualan, created_at, persen, status, updated_at, username FROM v_margin_penjualan ORDER BY idmargin_penjualan");

tampil_header('Data Margin Penjualan', 'margin_penjualan.php');
?>

  <div class="tabel-bungkus">
  <table>
    <thead>
      <tr><th class="angka">ID</th><th class="angka">Persen</th><th>Status</th><th>Dibuat oleh</th><th>Dibuat</th><th>Diubah</th></tr>
    </thead>
    <tbody>
    <?php if ($daftar->num_rows === 0): ?>
      <tr><td colspan="6" class="kosong">Belum ada data margin penjualan.</td></tr>
    <?php endif; ?>
    <?php while ($r = $daftar->fetch_assoc()): ?>
      <tr>
        <td class="angka"><?= (int)$r['idmargin_penjualan'] ?></td>
        <td class="angka"><?= h(rtrim(rtrim(number_format((float)$r['persen'], 2, '.', ''), '0'), '.')) ?>%</td>
        <td><?= $r['status'] == 1 ? '<span class="lencana aktif">Aktif</span>' : '<span class="lencana nonaktif">Nonaktif</span>' ?></td>
        <td><?= h($r['username'] ?? '-') ?></td>
        <td><?= h($r['created_at'] ?? '-') ?></td>
        <td><?= h($r['updated_at'] ?? '-') ?></td>
      </tr>
    <?php endwhile; ?>
    </tbody>
  </table>
  </div>

<?php tampil_footer(); ?>
