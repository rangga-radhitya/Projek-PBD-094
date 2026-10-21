<?php
// CUD tabel SATUAN: satuan(idsatuan, nama_satuan, status)
require __DIR__ . '/includes/functions.php';
$db = db();

$aksi  = $_GET['aksi'] ?? 'daftar';   // daftar | tambah | edit
$error = '';
$data  = ['idsatuan' => '', 'nama_satuan' => '', 'status' => '1'];
$post  = $_SERVER['REQUEST_METHOD'] === 'POST';

if ($post) {
    $mode = $_POST['mode'] ?? '';

    // ---------- DELETE ----------
    if ($mode === 'hapus') {
        $id = (int)($_POST['id'] ?? 0);
        try {
            $stmt = $db->prepare("DELETE FROM satuan WHERE idsatuan = ?");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            flash('ok', 'Satuan berhasil dihapus.');
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() === 1451) {
                flash('error', 'Satuan tidak bisa dihapus karena masih dipakai oleh data barang.');
            } else {
                flash('error', 'Gagal menghapus: ' . $e->getMessage());
            }
        }
        redirect('satuan.php');
    }

    if (!in_array($mode, ['tambah', 'edit'], true)) {
        redirect('satuan.php');
    }

    $aksi = $mode;
    $data = [
        'idsatuan'    => trim($_POST['idsatuan'] ?? ''),
        'nama_satuan' => trim($_POST['nama_satuan'] ?? ''),
        'status'      => $_POST['status'] ?? '1',
    ];

    if (!ctype_digit($data['idsatuan'])) {
        $error = 'ID satuan harus berupa angka.';
    } elseif ($data['nama_satuan'] === '') {
        $error = 'Nama satuan wajib diisi.';
    } elseif (mb_strlen($data['nama_satuan']) > 45) {
        $error = 'Nama satuan maksimal 45 karakter.';
    } elseif (!in_array($data['status'], ['0', '1'], true)) {
        $error = 'Status tidak valid.';
    } else {
        $id     = (int)$data['idsatuan'];
        $nama   = $data['nama_satuan'];
        $status = (int)$data['status'];
        try {
            if ($mode === 'tambah') {
                // ---------- CREATE ----------
                $stmt = $db->prepare("INSERT INTO satuan (idsatuan, nama_satuan, status) VALUES (?, ?, ?)");
                $stmt->bind_param('isi', $id, $nama, $status);
                $stmt->execute();
                flash('ok', 'Satuan berhasil ditambahkan.');
            } else {
                // ---------- UPDATE ----------
                $stmt = $db->prepare("UPDATE satuan SET nama_satuan = ?, status = ? WHERE idsatuan = ?");
                $stmt->bind_param('sii', $nama, $status, $id);
                $stmt->execute();
                flash('ok', 'Satuan berhasil diperbarui.');
            }
            redirect('satuan.php');
        } catch (mysqli_sql_exception $e) {
            $error = $e->getCode() === 1062
                ? 'ID satuan sudah dipakai, gunakan ID lain.'
                : 'Gagal menyimpan: ' . $e->getMessage();
        }
    }
}

if (!in_array($aksi, ['tambah', 'edit'], true)) {
    $aksi = 'daftar';
}

// Form tambah (pertama kali dibuka): usulkan ID berikutnya
if (!$post && $aksi === 'tambah') {
    $r = $db->query("SELECT COALESCE(MAX(idsatuan), 0) + 1 AS berikut FROM satuan")->fetch_assoc();
    $data['idsatuan'] = (string)$r['berikut'];
}

// Form edit (pertama kali dibuka): ambil data lama
if (!$post && $aksi === 'edit') {
    $id   = (int)($_GET['id'] ?? 0);
    $stmt = $db->prepare("SELECT idsatuan, nama_satuan, status FROM satuan WHERE idsatuan = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    if (!$row) {
        flash('error', 'Data satuan tidak ditemukan.');
        redirect('satuan.php');
    }
    $data = array_map('strval', $row);
}

// ---------- READ (dari VIEW) ----------
$daftar = null;
if ($aksi === 'daftar') {
    $daftar = $db->query("SELECT idsatuan, nama_satuan, status FROM v_satuan ORDER BY idsatuan");
}

tampil_header($aksi === 'tambah' ? 'Tambah Satuan' : ($aksi === 'edit' ? 'Edit Satuan' : 'Data Satuan'), 'satuan.php');
tampil_flash();
?>

<?php if ($aksi === 'daftar'): ?>

  <a class="tombol" href="satuan.php?aksi=tambah">Tambah satuan</a>
  <div class="tabel-bungkus">
  <table>
    <thead><tr><th class="angka">ID</th><th>Nama satuan</th><th>Status</th><th>Aksi</th></tr></thead>
    <tbody>
    <?php if ($daftar->num_rows === 0): ?>
      <tr><td colspan="4" class="kosong">Belum ada data satuan.</td></tr>
    <?php endif; ?>
    <?php while ($r = $daftar->fetch_assoc()): ?>
      <tr>
        <td class="angka"><?= (int)$r['idsatuan'] ?></td>
        <td><?= h($r['nama_satuan']) ?></td>
        <td><?= $r['status'] == 1 ? '<span class="lencana aktif">Aktif</span>' : '<span class="lencana nonaktif">Nonaktif</span>' ?></td>
        <td class="aksi">
          <a href="satuan.php?aksi=edit&id=<?= (int)$r['idsatuan'] ?>">Edit</a>
          <form method="post" onsubmit="return confirm('Hapus satuan <?= h($r['nama_satuan']) ?>?');">
            <input type="hidden" name="mode" value="hapus">
            <input type="hidden" name="id" value="<?= (int)$r['idsatuan'] ?>">
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

    <label for="idsatuan">ID satuan</label>
    <input type="text" id="idsatuan" name="idsatuan" inputmode="numeric" value="<?= h($data['idsatuan']) ?>"
           <?= $aksi === 'edit' ? 'readonly' : 'required' ?>>
    <?php if ($aksi === 'tambah'): ?><p class="petunjuk">Terisi otomatis dengan ID berikutnya, boleh diubah.</p><?php endif; ?>

    <label for="nama_satuan">Nama satuan</label>
    <input type="text" id="nama_satuan" name="nama_satuan" maxlength="45" value="<?= h($data['nama_satuan']) ?>" required>

    <label for="status">Status</label>
    <select id="status" name="status">
      <option value="1" <?= $data['status'] === '1' ? 'selected' : '' ?>>Aktif</option>
      <option value="0" <?= $data['status'] === '0' ? 'selected' : '' ?>>Nonaktif</option>
    </select>

    <div class="baris-tombol">
      <button type="submit" class="tombol"><?= $aksi === 'edit' ? 'Simpan perubahan' : 'Simpan satuan' ?></button>
      <a href="satuan.php">Batal</a>
    </div>
  </form>

<?php endif; ?>

<?php tampil_footer(); ?>
