<?php
// Tampilan data (hanya baca) dari VIEW v_user
require __DIR__ . '/includes/functions.php';
$db = db();

// ---------- READ (dari VIEW) ----------
$daftar = $db->query("SELECT iduser, username, idrole, nama_role FROM v_user ORDER BY iduser");

tampil_header('Data User', 'user.php');
?>

  <div class="tabel-bungkus">
  <table>
    <thead><tr><th class="angka">ID</th><th>Username</th><th>Role</th></tr></thead>
    <tbody>
    <?php if ($daftar->num_rows === 0): ?>
      <tr><td colspan="3" class="kosong">Belum ada data user.</td></tr>
    <?php endif; ?>
    <?php while ($r = $daftar->fetch_assoc()): ?>
      <tr>
        <td class="angka"><?= (int)$r['iduser'] ?></td>
        <td><?= h($r['username']) ?></td>
        <td><?= h($r['nama_role'] ?? '-') ?></td>
      </tr>
    <?php endwhile; ?>
    </tbody>
  </table>
  </div>

<?php tampil_footer(); ?>
