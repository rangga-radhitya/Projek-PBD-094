<?php
// CUD tabel VENDOR: vendor(idvendor, nama_vendor, badan_hukum, status)
// badan_hukum: P = PT, C = CV, U = UD   |   status: A = Aktif, N = Nonaktif
require __DIR__ . '/includes/functions.php';
$db = db();

$badanHukum = ['P' => 'PT', 'C' => 'CV', 'U' => 'UD'];
$statusVendor = ['A' => 'Aktif', 'N' => 'Nonaktif'];

$aksi  = $_GET['aksi'] ?? 'daftar';   // daftar | tambah | edit
$error = '';
$data  = ['idvendor' => '', 'nama_vendor' => '', 'badan_hukum' => 'P', 'status' => 'A'];
$post  = $_SERVER['REQUEST_METHOD'] === 'POST';

if ($post) {
    $mode = $_POST['mode'] ?? '';

    // ---------- DELETE ----------
    if ($mode === 'hapus') {
        $id = (int)($_POST['id'] ?? 0);
        try {
            $stmt = $db->prepare("DELETE FROM vendor WHERE idvendor = ?");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            flash('ok', 'Vendor berhasil dihapus.');
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() === 1451) {
                flash('error', 'Vendor tidak bisa dihapus karena masih dipakai oleh data pengadaan.');
            } else {
                flash('error', 'Gagal menghapus: ' . $e->getMessage());
            }
        }
        redirect('vendor.php');
    }

    if (!in_array($mode, ['tambah', 'edit'], true)) {
        redirect('vendor.php');
    }

    $aksi = $mode;
    $data = [
        'idvendor'    => trim($_POST['idvendor'] ?? ''),
        'nama_vendor' => trim($_POST['nama_vendor'] ?? ''),
        'badan_hukum' => $_POST['badan_hukum'] ?? '',
        'status'      => $_POST['status'] ?? '',
    ];

    if (!ctype_digit($data['idvendor'])) {
        $error = 'ID vendor harus berupa angka.';
    } elseif ($data['nama_vendor'] === '') {
        $error = 'Nama vendor wajib diisi.';
    } elseif (mb_strlen($data['nama_vendor']) > 100) {
        $error = 'Nama vendor maksimal 100 karakter.';
    } elseif (!isset($badanHukum[$data['badan_hukum']])) {
        $error = 'Badan hukum tidak valid.';
    } elseif (!isset($statusVendor[$data['status']])) {
        $error = 'Status tidak valid.';
    } else {
        $id     = (int)$data['idvendor'];
        $nama   = $data['nama_vendor'];
        $badan  = $data['badan_hukum'];
        $status = $data['status'];
        try {
            if ($mode === 'tambah') {
                // ---------- CREATE ----------
                $stmt = $db->prepare("INSERT INTO vendor (idvendor, nama_vendor, badan_hukum, status) VALUES (?, ?, ?, ?)");
                $stmt->bind_param('isss', $id, $nama, $badan, $status);
                $stmt->execute();
                flash('ok', 'Vendor berhasil ditambahkan.');
            } else {
                // ---------- UPDATE ----------
                $stmt = $db->prepare("UPDATE vendor SET nama_vendor = ?, badan_hukum = ?, status = ? WHERE idvendor = ?");
                $stmt->bind_param('sssi', $nama, $badan, $status, $id);
                $stmt->execute();
                flash('ok', 'Vendor berhasil diperbarui.');
            }
            redirect('vendor.php');
        } catch (mysqli_sql_exception $e) {
            $error = $e->getCode() === 1062
                ? 'ID vendor sudah dipakai, gunakan ID lain.'
                : 'Gagal menyimpan: ' . $e->getMessage();
        }
    }
}

if (!in_array($aksi, ['tambah', 'edit'], true)) {
    $aksi = 'daftar';
}

if (!$post && $aksi === 'tambah') {
    $r = $db->query("SELECT COALESCE(MAX(idvendor), 0) + 1 AS berikut FROM vendor")->fetch_assoc();
    $data['idvendor'] = (string)$r['berikut'];
}

if (!$post && $aksi === 'edit') {
    $id   = (int)($_GET['id'] ?? 0);
    $stmt = $db->prepare("SELECT idvendor, nama_vendor, badan_hukum, status FROM vendor WHERE idvendor = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    if (!$row) {
        flash('error', 'Data vendor tidak ditemukan.');
        redirect('vendor.php');
    }
    $data = array_map('strval', $row);
}

// ---------- READ (dari VIEW) ----------
$daftar = null;
if ($aksi === 'daftar') {
    $daftar = $db->query("SELECT idvendor, nama_vendor, badan_hukum_teks, status FROM v_vendor ORDER BY idvendor");
}

tampil_header($aksi === 'tambah' ? 'Tambah Vendor' : ($aksi === 'edit' ? 'Edit Vendor' : 'Data Vendor'), 'vendor.php');
tampil_flash();
?>

<?php if ($aksi === 'daftar'): ?>

  <a class="tombol" href="vendor.php?aksi=tambah">Tambah vendor</a>
  <div class="tabel-bungkus">
  <table>
    <thead><tr><th class="angka">ID</th><th>Nama vendor</th><th>Badan hukum</th><th>Status</th><th>Aksi</th></tr></thead>
    <tbody>
    <?php if ($daftar->num_rows === 0): ?>
      <tr><td colspan="5" class="kosong">Belum ada data vendor.</td></tr>
    <?php endif; ?>
    <?php while ($r = $daftar->fetch_assoc()): ?>
      <tr>
        <td class="angka"><?= (int)$r['idvendor'] ?></td>
        <td><?= h($r['nama_vendor']) ?></td>
        <td><?= h($r['badan_hukum_teks']) ?></td>
        <td><?= $r['status'] === 'A' ? '<span class="lencana aktif">Aktif</span>' : '<span class="lencana nonaktif">Nonaktif</span>' ?></td>
        <td class="aksi">
          <a href="vendor.php?aksi=edit&id=<?= (int)$r['idvendor'] ?>">Edit</a>
          <form method="post" onsubmit="return confirm('Hapus vendor <?= h($r['nama_vendor']) ?>?');">
            <input type="hidden" name="mode" value="hapus">
            <input type="hidden" name="id" value="<?= (int)$r['idvendor'] ?>">
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

    <label for="idvendor">ID vendor</label>
    <input type="text" id="idvendor" name="idvendor" inputmode="numeric" value="<?= h($data['idvendor']) ?>"
           <?= $aksi === 'edit' ? 'readonly' : 'required' ?>>
    <?php if ($aksi === 'tambah'): ?><p class="petunjuk">Terisi otomatis dengan ID berikutnya, boleh diubah.</p><?php endif; ?>

    <label for="nama_vendor">Nama vendor</label>
    <input type="text" id="nama_vendor" name="nama_vendor" maxlength="100" value="<?= h($data['nama_vendor']) ?>" required>

    <label for="badan_hukum">Badan hukum</label>
    <select id="badan_hukum" name="badan_hukum">
      <?php foreach ($badanHukum as $kode => $nama): ?>
        <option value="<?= h($kode) ?>" <?= $data['badan_hukum'] === $kode ? 'selected' : '' ?>><?= h($nama) ?></option>
      <?php endforeach; ?>
    </select>

    <label for="status">Status</label>
    <select id="status" name="status">
      <?php foreach ($statusVendor as $kode => $nama): ?>
        <option value="<?= h($kode) ?>" <?= $data['status'] === $kode ? 'selected' : '' ?>><?= h($nama) ?></option>
      <?php endforeach; ?>
    </select>

    <div class="baris-tombol">
      <button type="submit" class="tombol"><?= $aksi === 'edit' ? 'Simpan perubahan' : 'Simpan vendor' ?></button>
      <a href="vendor.php">Batal</a>
    </div>
  </form>

<?php endif; ?>

<?php tampil_footer(); ?>
