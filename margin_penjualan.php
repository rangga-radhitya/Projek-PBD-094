<?php
// CUD tabel MARGIN_PENJUALAN:
// margin_penjualan(idmargin_penjualan, created_at, persen, status, iduser, updated_at)
// created_at diisi NOW() saat tambah, updated_at diisi NOW() saat ubah.
require __DIR__ . '/includes/functions.php';
$db = db();

$aksi  = $_GET['aksi'] ?? 'daftar';   // daftar | tambah | edit
$error = '';
$data  = ['idmargin_penjualan' => '', 'persen' => '', 'status' => '0', 'iduser' => ''];
$post  = $_SERVER['REQUEST_METHOD'] === 'POST';

// Pilihan user untuk dropdown
$userList  = $db->query("SELECT iduser, username FROM `user` ORDER BY iduser")->fetch_all(MYSQLI_ASSOC);
$userValid = array_map('intval', array_column($userList, 'iduser'));

if ($post) {
    $mode = $_POST['mode'] ?? '';

    // ---------- DELETE ----------
    if ($mode === 'hapus') {
        $id = (int)($_POST['id'] ?? 0);
        try {
            $stmt = $db->prepare("DELETE FROM margin_penjualan WHERE idmargin_penjualan = ?");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            flash('ok', 'Margin penjualan berhasil dihapus.');
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() === 1451) {
                flash('error', 'Margin tidak bisa dihapus karena sudah dipakai oleh data penjualan.');
            } else {
                flash('error', 'Gagal menghapus: ' . $e->getMessage());
            }
        }
        redirect('margin_penjualan.php');
    }

    if (!in_array($mode, ['tambah', 'edit'], true)) {
        redirect('margin_penjualan.php');
    }

    $aksi = $mode;
    $data = [
        'idmargin_penjualan' => trim($_POST['idmargin_penjualan'] ?? ''),
        'persen'             => str_replace(',', '.', trim($_POST['persen'] ?? '')),
        'status'             => $_POST['status'] ?? '0',
        'iduser'             => $_POST['iduser'] ?? '',
    ];

    if (!ctype_digit($data['idmargin_penjualan'])) {
        $error = 'ID margin harus berupa angka.';
    } elseif (!is_numeric($data['persen']) || (float)$data['persen'] < 0 || (float)$data['persen'] > 100) {
        $error = 'Persen harus berupa angka antara 0 sampai 100.';
    } elseif (!in_array($data['status'], ['0', '1'], true)) {
        $error = 'Status tidak valid.';
    } elseif (!ctype_digit((string)$data['iduser']) || !in_array((int)$data['iduser'], $userValid, true)) {
        $error = 'Pilih user yang tersedia.';
    } else {
        $id     = (int)$data['idmargin_penjualan'];
        $persen = (float)$data['persen'];
        $status = (int)$data['status'];
        $iduser = (int)$data['iduser'];
        try {
            if ($mode === 'tambah') {
                // ---------- CREATE ----------
                $stmt = $db->prepare("INSERT INTO margin_penjualan (idmargin_penjualan, created_at, persen, status, iduser) VALUES (?, NOW(), ?, ?, ?)");
                $stmt->bind_param('idii', $id, $persen, $status, $iduser);
                $stmt->execute();
                flash('ok', 'Margin penjualan berhasil ditambahkan.');
            } else {
                // ---------- UPDATE ----------
                $stmt = $db->prepare("UPDATE margin_penjualan SET persen = ?, status = ?, iduser = ?, updated_at = NOW() WHERE idmargin_penjualan = ?");
                $stmt->bind_param('diii', $persen, $status, $iduser, $id);
                $stmt->execute();
                flash('ok', 'Margin penjualan berhasil diperbarui.');
            }
            redirect('margin_penjualan.php');
        } catch (mysqli_sql_exception $e) {
            $error = $e->getCode() === 1062
                ? 'ID margin sudah dipakai, gunakan ID lain.'
                : 'Gagal menyimpan: ' . $e->getMessage();
        }
    }
}

if (!in_array($aksi, ['tambah', 'edit'], true)) {
    $aksi = 'daftar';
}

if (!$post && $aksi === 'tambah') {
    $r = $db->query("SELECT COALESCE(MAX(idmargin_penjualan), 0) + 1 AS berikut FROM margin_penjualan")->fetch_assoc();
    $data['idmargin_penjualan'] = (string)$r['berikut'];
}

if (!$post && $aksi === 'edit') {
    $id   = (int)($_GET['id'] ?? 0);
    $stmt = $db->prepare("SELECT idmargin_penjualan, persen, status, iduser FROM margin_penjualan WHERE idmargin_penjualan = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    if (!$row) {
        flash('error', 'Data margin tidak ditemukan.');
        redirect('margin_penjualan.php');
    }
    $data = array_map('strval', $row);
}

// ---------- READ (dari VIEW v_margin_penjualan) ----------
$daftar = null;
if ($aksi === 'daftar') {
    $daftar = $db->query("SELECT idmargin_penjualan, created_at, persen, status, updated_at, username FROM v_margin_penjualan ORDER BY idmargin_penjualan");
}

tampil_header($aksi === 'tambah' ? 'Tambah Margin Penjualan' : ($aksi === 'edit' ? 'Edit Margin Penjualan' : 'Data Margin Penjualan'), 'margin_penjualan.php');
tampil_flash();
?>

<?php if ($aksi === 'daftar'): ?>

  <a class="tombol" href="margin_penjualan.php?aksi=tambah">Tambah margin</a>
  <div class="tabel-bungkus">
  <table>
    <thead>
      <tr><th class="angka">ID</th><th class="angka">Persen</th><th>Status</th><th>Dibuat oleh</th><th>Dibuat</th><th>Diubah</th><th>Aksi</th></tr>
    </thead>
    <tbody>
    <?php if ($daftar->num_rows === 0): ?>
      <tr><td colspan="7" class="kosong">Belum ada data margin penjualan.</td></tr>
    <?php endif; ?>
    <?php while ($r = $daftar->fetch_assoc()): ?>
      <tr>
        <td class="angka"><?= (int)$r['idmargin_penjualan'] ?></td>
        <td class="angka"><?= h(rtrim(rtrim(number_format((float)$r['persen'], 2, '.', ''), '0'), '.')) ?>%</td>
        <td><?= $r['status'] == 1 ? '<span class="lencana aktif">Aktif</span>' : '<span class="lencana nonaktif">Nonaktif</span>' ?></td>
        <td><?= h($r['username'] ?? '-') ?></td>
        <td><?= h($r['created_at'] ?? '-') ?></td>
        <td><?= h($r['updated_at'] ?? '-') ?></td>
        <td class="aksi">
          <a href="margin_penjualan.php?aksi=edit&id=<?= (int)$r['idmargin_penjualan'] ?>">Edit</a>
          <form method="post" onsubmit="return confirm('Hapus margin ID <?= (int)$r['idmargin_penjualan'] ?>?');">
            <input type="hidden" name="mode" value="hapus">
            <input type="hidden" name="id" value="<?= (int)$r['idmargin_penjualan'] ?>">
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
  <?php if (!$userList): ?><p class="pesan error">Belum ada data user. Tambahkan user dulu sebelum membuat margin.</p><?php endif; ?>
  <form method="post" class="isian">
    <input type="hidden" name="mode" value="<?= h($aksi) ?>">

    <label for="idmargin_penjualan">ID margin</label>
    <input type="text" id="idmargin_penjualan" name="idmargin_penjualan" inputmode="numeric"
           value="<?= h($data['idmargin_penjualan']) ?>" <?= $aksi === 'edit' ? 'readonly' : 'required' ?>>
    <?php if ($aksi === 'tambah'): ?><p class="petunjuk">Terisi otomatis dengan ID berikutnya, boleh diubah.</p><?php endif; ?>

    <label for="persen">Persen margin (%)</label>
    <input type="text" id="persen" name="persen" inputmode="decimal" value="<?= h($data['persen']) ?>" required>
    <p class="petunjuk">Contoh: 20 atau 25.5</p>

    <label for="status">Status</label>
    <select id="status" name="status">
      <option value="1" <?= $data['status'] === '1' ? 'selected' : '' ?>>Aktif</option>
      <option value="0" <?= $data['status'] === '0' ? 'selected' : '' ?>>Nonaktif</option>
    </select>

    <label for="iduser">User</label>
    <select id="iduser" name="iduser" required>
      <option value="">- pilih user -</option>
      <?php foreach ($userList as $u): ?>
        <option value="<?= (int)$u['iduser'] ?>" <?= (string)$data['iduser'] === (string)$u['iduser'] ? 'selected' : '' ?>><?= h($u['username']) ?></option>
      <?php endforeach; ?>
    </select>

    <div class="baris-tombol">
      <button type="submit" class="tombol"><?= $aksi === 'edit' ? 'Simpan perubahan' : 'Simpan margin' ?></button>
      <a href="margin_penjualan.php">Batal</a>
    </div>
  </form>

<?php endif; ?>

<?php tampil_footer(); ?>
