<?php
// Tampilan data (hanya baca) dari VIEW v_role
require __DIR__ . '/includes/functions.php';
$db = db();

// ---------- READ (dari VIEW) ----------
$daftar = $db->query("SELECT idrole, nama_role FROM v_role ORDER BY idrole");

tampil_header('Data Role', 'role.php');
?>

  <div class="tabel-bungkus">
  <table>
    <thead><tr><th class="angka">ID</th><th>Nama role</th></tr></thead>
    <tbody>
    <?php if ($daftar->num_rows === 0): ?>
      <tr><td colspan="2" class="kosong">Belum ada data role.</td></tr>
    <?php endif; ?>
    <?php while ($r = $daftar->fetch_assoc()): ?>
      <tr>
        <td class="angka"><?= (int)$r['idrole'] ?></td>
        <td><?= h($r['nama_role']) ?></td>
      </tr>
    <?php endwhile; ?>
    </tbody>
  </table>
  </div>

<?php tampil_footer(); ?>
