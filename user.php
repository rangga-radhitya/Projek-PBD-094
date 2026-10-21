<?php
// CUD tabel USER: user(iduser, username, password, idrole)
// Password disimpan dengan SHA2(..., 256), sama seperti DML di laporan.
require __DIR__ . '/includes/functions.php';
$db = db();

$aksi  = $_GET['aksi'] ?? 'daftar';   // daftar | tambah | edit
$error = '';
$data  = ['iduser' => '', 'username' => '', 'idrole' => ''];
$post  = $_SERVER['REQUEST_METHOD'] === 'POST';

// Pilihan role untuk dropdown
$roleList = $db->query("SELECT idrole, nama_role FROM role ORDER BY idrole")->fetch_all(MYSQLI_ASSOC);
$roleValid = array_column($roleList, 'idrole');

if ($post) {
    $mode = $_POST['mode'] ?? '';

    // ---------- DELETE ----------
    if ($mode === 'hapus') {
        $id = (int)($_POST['id'] ?? 0);
        try {
            $stmt = $db->prepare("DELETE FROM `user` WHERE iduser = ?");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            flash('ok', 'User berhasil dihapus.');
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() === 1451) {
                flash('error', 'User tidak bisa dihapus karena masih dipakai oleh data transaksi.');
            } else {
                flash('error', 'Gagal menghapus: ' . $e->getMessage());
            }
        }
        redirect('user.php');
    }

    if (!in_array($mode, ['tambah', 'edit'], true)) {
        redirect('user.php');
    }

    $aksi     = $mode;
    $password = $_POST['password'] ?? '';
    $data = [
        'iduser'   => trim($_POST['iduser'] ?? ''),
        'username' => trim($_POST['username'] ?? ''),
        'idrole'   => $_POST['idrole'] ?? '',
    ];

    if (!ctype_digit($data['iduser'])) {
        $error = 'ID user harus berupa angka.';
    } elseif ($data['username'] === '') {
        $error = 'Username wajib diisi.';
    } elseif (mb_strlen($data['username']) > 45) {
        $error = 'Username maksimal 45 karakter.';
    } elseif ($mode === 'tambah' && $password === '') {
        $error = 'Password wajib diisi.';
    } elseif (mb_strlen($password) > 60) {
        $error = 'Password maksimal 60 karakter.';
    } elseif (!ctype_digit((string)$data['idrole']) || !in_array((int)$data['idrole'], array_map('intval', $roleValid), true)) {
        $error = 'Pilih role yang tersedia.';
    } else {
        $id       = (int)$data['iduser'];
        $username = $data['username'];
        $idrole   = (int)$data['idrole'];
        try {
            if ($mode === 'tambah') {
                // ---------- CREATE ----------
                $stmt = $db->prepare("INSERT INTO `user` (iduser, username, password, idrole) VALUES (?, ?, SHA2(?, 256), ?)");
                $stmt->bind_param('issi', $id, $username, $password, $idrole);
                $stmt->execute();
                flash('ok', 'User berhasil ditambahkan.');
            } else {
                // ---------- UPDATE ----------
                if ($password !== '') {
                    $stmt = $db->prepare("UPDATE `user` SET username = ?, password = SHA2(?, 256), idrole = ? WHERE iduser = ?");
                    $stmt->bind_param('ssii', $username, $password, $idrole, $id);
                } else {
                    // password dikosongkan = tidak diubah
                    $stmt = $db->prepare("UPDATE `user` SET username = ?, idrole = ? WHERE iduser = ?");
                    $stmt->bind_param('sii', $username, $idrole, $id);
                }
                $stmt->execute();
                flash('ok', 'User berhasil diperbarui.');
            }
            redirect('user.php');
        } catch (mysqli_sql_exception $e) {
            $error = $e->getCode() === 1062
                ? 'ID user sudah dipakai, gunakan ID lain.'
                : 'Gagal menyimpan: ' . $e->getMessage();
        }
    }
}

if (!in_array($aksi, ['tambah', 'edit'], true)) {
    $aksi = 'daftar';
}

if (!$post && $aksi === 'tambah') {
    $r = $db->query("SELECT COALESCE(MAX(iduser), 0) + 1 AS berikut FROM `user`")->fetch_assoc();
    $data['iduser'] = (string)$r['berikut'];
}

if (!$post && $aksi === 'edit') {
    $id   = (int)($_GET['id'] ?? 0);
    $stmt = $db->prepare("SELECT iduser, username, idrole FROM `user` WHERE iduser = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    if (!$row) {
        flash('error', 'Data user tidak ditemukan.');
        redirect('user.php');
    }
    $data = array_map('strval', $row);
}

// ---------- READ (dari VIEW v_user; password tidak ditampilkan) ----------
$daftar = null;
if ($aksi === 'daftar') {
    $daftar = $db->query("SELECT iduser, username, idrole, nama_role FROM v_user ORDER BY iduser");
}

tampil_header($aksi === 'tambah' ? 'Tambah User' : ($aksi === 'edit' ? 'Edit User' : 'Data User'), 'user.php');
tampil_flash();
?>

<?php if ($aksi === 'daftar'): ?>

  <a class="tombol" href="user.php?aksi=tambah">Tambah user</a>
  <div class="tabel-bungkus">
  <table>
    <thead><tr><th class="angka">ID</th><th>Username</th><th>Role</th><th>Aksi</th></tr></thead>
    <tbody>
    <?php if ($daftar->num_rows === 0): ?>
      <tr><td colspan="4" class="kosong">Belum ada data user.</td></tr>
    <?php endif; ?>
    <?php while ($r = $daftar->fetch_assoc()): ?>
      <tr>
        <td class="angka"><?= (int)$r['iduser'] ?></td>
        <td><?= h($r['username']) ?></td>
        <td><?= h($r['nama_role'] ?? '-') ?></td>
        <td class="aksi">
          <a href="user.php?aksi=edit&id=<?= (int)$r['iduser'] ?>">Edit</a>
          <form method="post" onsubmit="return confirm('Hapus user <?= h($r['username']) ?>?');">
            <input type="hidden" name="mode" value="hapus">
            <input type="hidden" name="id" value="<?= (int)$r['iduser'] ?>">
            <button type="submit" class="hapus">Hapus</button>
          </form>
        </td>
      </tr>
    <?php endwhile; ?>
    </tbody>
  </table>
  </div>

<?php else: ?>

  <?php if ($error): ?><p class="pesan error" role="alert"><?= h($error) ?></p><?php endif; ?>
  <?php if (!$roleList): ?><p class="pesan error">Belum ada data role. Tambahkan role dulu sebelum membuat user.</p><?php endif; ?>
  <form method="post" class="isian">
    <input type="hidden" name="mode" value="<?= h($aksi) ?>">

    <label for="iduser">ID user</label>
    <input type="text" id="iduser" name="iduser" inputmode="numeric" value="<?= h($data['iduser']) ?>"
           <?= $aksi === 'edit' ? 'readonly' : 'required' ?>>
    <?php if ($aksi === 'tambah'): ?><p class="petunjuk">Terisi otomatis dengan ID berikutnya, boleh diubah.</p><?php endif; ?>

    <label for="username">Username</label>
    <input type="text" id="username" name="username" maxlength="45" value="<?= h($data['username']) ?>" required>

    <label for="password">Password</label>
    <input type="password" id="password" name="password" maxlength="60" autocomplete="new-password"
           <?= $aksi === 'tambah' ? 'required' : '' ?>>
    <?php if ($aksi === 'edit'): ?><p class="petunjuk">Kosongkan jika tidak ingin mengubah password.</p><?php endif; ?>

    <label for="idrole">Role</label>
    <select id="idrole" name="idrole" required>
      <option value="">- pilih role -</option>
      <?php foreach ($roleList as $rl): ?>
        <option value="<?= (int)$rl['idrole'] ?>" <?= (string)$data['idrole'] === (string)$rl['idrole'] ? 'selected' : '' ?>><?= h($rl['nama_role']) ?></option>
      <?php endforeach; ?>
    </select>

    <div class="baris-tombol">
      <button type="submit" class="tombol"><?= $aksi === 'edit' ? 'Simpan perubahan' : 'Simpan user' ?></button>
      <a href="user.php">Batal</a>
    </div>
  </form>

<?php endif; ?>

<?php tampil_footer(); ?>
