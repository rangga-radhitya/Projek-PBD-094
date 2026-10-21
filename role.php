<?php
// CUD tabel ROLE: role(idrole, nama_role)
require __DIR__ . '/includes/functions.php';
$db = db();

$aksi  = $_GET['aksi'] ?? 'daftar';   // daftar | tambah | edit
$error = '';
$data  = ['idrole' => '', 'nama_role' => ''];
$post  = $_SERVER['REQUEST_METHOD'] === 'POST';

if ($post) {
    $mode = $_POST['mode'] ?? '';

    // ---------- DELETE ----------
    if ($mode === 'hapus') {
        $id = (int)($_POST['id'] ?? 0);
        try {
            $stmt = $db->prepare("DELETE FROM role WHERE idrole = ?");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            flash('ok', 'Role berhasil dihapus.');
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() === 1451) {
                flash('error', 'Role tidak bisa dihapus karena masih dipakai oleh data user.');
            } else {
                flash('error', 'Gagal menghapus: ' . $e->getMessage());
            }
        }
        redirect('role.php');
    }

    if (!in_array($mode, ['tambah', 'edit'], true)) {
        redirect('role.php');
    }

    $aksi = $mode;
    $data = [
        'idrole'    => trim($_POST['idrole'] ?? ''),
        'nama_role' => trim($_POST['nama_role'] ?? ''),
    ];

    if (!ctype_digit($data['idrole'])) {
        $error = 'ID role harus berupa angka.';
    } elseif ($data['nama_role'] === '') {
        $error = 'Nama role wajib diisi.';
    } elseif (mb_strlen($data['nama_role']) > 100) {
        $error = 'Nama role maksimal 100 karakter.';
    } else {
        $id   = (int)$data['idrole'];
        $nama = $data['nama_role'];
        try {
            if ($mode === 'tambah') {
                // ---------- CREATE ----------
                $stmt = $db->prepare("INSERT INTO role (idrole, nama_role) VALUES (?, ?)");
                $stmt->bind_param('is', $id, $nama);
                $stmt->execute();
                flash('ok', 'Role berhasil ditambahkan.');
            } else {
                // ---------- UPDATE ----------
                $stmt = $db->prepare("UPDATE role SET nama_role = ? WHERE idrole = ?");
                $stmt->bind_param('si', $nama, $id);
                $stmt->execute();
                flash('ok', 'Role berhasil diperbarui.');
            }
            redirect('role.php');
        } catch (mysqli_sql_exception $e) {
            $error = $e->getCode() === 1062
                ? 'ID role sudah dipakai, gunakan ID lain.'
                : 'Gagal menyimpan: ' . $e->getMessage();
        }
    }
}

if (!in_array($aksi, ['tambah', 'edit'], true)) {
    $aksi = 'daftar';
}

if (!$post && $aksi === 'tambah') {
    $r = $db->query("SELECT COALESCE(MAX(idrole), 0) + 1 AS berikut FROM role")->fetch_assoc();
    $data['idrole'] = (string)$r['berikut'];
}

if (!$post && $aksi === 'edit') {
    $id   = (int)($_GET['id'] ?? 0);
    $stmt = $db->prepare("SELECT idrole, nama_role FROM role WHERE idrole = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    if (!$row) {
        flash('error', 'Data role tidak ditemukan.');
        redirect('role.php');
    }
    $data = array_map('strval', $row);
}

// ---------- READ (dari VIEW) ----------
$daftar = null;
if ($aksi === 'daftar') {
    $daftar = $db->query("SELECT idrole, nama_role FROM v_role ORDER BY idrole");
}

tampil_header($aksi === 'tambah' ? 'Tambah Role' : ($aksi === 'edit' ? 'Edit Role' : 'Data Role'), 'role.php');
tampil_flash();
?>

<?php if ($aksi === 'daftar'): ?>

  <a class="tombol" href="role.php?aksi=tambah">Tambah role</a>
  <div class="tabel-bungkus">
  <table>
    <thead><tr><th class="angka">ID</th><th>Nama role</th><th>Aksi</th></tr></thead>
    <tbody>
    <?php if ($daftar->num_rows === 0): ?>
      <tr><td colspan="3" class="kosong">Belum ada data role.</td></tr>
    <?php endif; ?>
    <?php while ($r = $daftar->fetch_assoc()): ?>
      <tr>
        <td class="angka"><?= (int)$r['idrole'] ?></td>
        <td><?= h($r['nama_role']) ?></td>
        <td class="aksi">
          <a href="role.php?aksi=edit&id=<?= (int)$r['idrole'] ?>">Edit</a>
          <form method="post" onsubmit="return confirm('Hapus role <?= h($r['nama_role']) ?>?');">
            <input type="hidden" name="mode" value="hapus">
            <input type="hidden" name="id" value="<?= (int)$r['idrole'] ?>">
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
  <form method="post" class="isian">
    <input type="hidden" name="mode" value="<?= h($aksi) ?>">

    <label for="idrole">ID role</label>
    <input type="text" id="idrole" name="idrole" inputmode="numeric" value="<?= h($data['idrole']) ?>"
           <?= $aksi === 'edit' ? 'readonly' : 'required' ?>>
    <?php if ($aksi === 'tambah'): ?><p class="petunjuk">Terisi otomatis dengan ID berikutnya, boleh diubah.</p><?php endif; ?>

    <label for="nama_role">Nama role</label>
    <input type="text" id="nama_role" name="nama_role" maxlength="100" value="<?= h($data['nama_role']) ?>" required>

    <div class="baris-tombol">
      <button type="submit" class="tombol"><?= $aksi === 'edit' ? 'Simpan perubahan' : 'Simpan role' ?></button>
      <a href="role.php">Batal</a>
    </div>
  </form>

<?php endif; ?>

<?php tampil_footer(); ?>
