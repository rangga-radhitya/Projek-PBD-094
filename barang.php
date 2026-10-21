<?php
// CUD tabel BARANG: barang(idbarang, jenis, nama, idsatuan, status, harga)
// jenis: F = Food, D = Drink, N = Non-Food (tisu, dll)
require __DIR__ . '/includes/functions.php';
$db = db();

$jenisBarang = ['F' => 'Food', 'D' => 'Drink', 'N' => 'Non-Food'];

$aksi  = $_GET['aksi'] ?? 'daftar';   // daftar | tambah | edit
$error = '';
$data  = ['idbarang' => '', 'jenis' => 'F', 'nama' => '', 'idsatuan' => '', 'status' => '1', 'harga' => ''];
$post  = $_SERVER['REQUEST_METHOD'] === 'POST';

// Pilihan satuan untuk dropdown
$satuanList  = $db->query("SELECT idsatuan, nama_satuan FROM satuan ORDER BY idsatuan")->fetch_all(MYSQLI_ASSOC);
$satuanValid = array_map('intval', array_column($satuanList, 'idsatuan'));

if ($post) {
    $mode = $_POST['mode'] ?? '';

    // ---------- DELETE ----------
    if ($mode === 'hapus') {
        $id = (int)($_POST['id'] ?? 0);
        try {
            $stmt = $db->prepare("DELETE FROM barang WHERE idbarang = ?");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            flash('ok', 'Barang berhasil dihapus.');
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() === 1451) {
                flash('error', 'Barang tidak bisa dihapus karena sudah dipakai oleh data transaksi.');
            } else {
                flash('error', 'Gagal menghapus: ' . $e->getMessage());
            }
        }
        redirect('barang.php');
    }

    if (!in_array($mode, ['tambah', 'edit'], true)) {
        redirect('barang.php');
    }

    $aksi = $mode;
    $data = [
        'idbarang' => trim($_POST['idbarang'] ?? ''),
        'jenis'    => $_POST['jenis'] ?? '',
        'nama'     => trim($_POST['nama'] ?? ''),
        'idsatuan' => $_POST['idsatuan'] ?? '',
        'status'   => $_POST['status'] ?? '1',
        'harga'    => trim($_POST['harga'] ?? ''),
    ];

    if (!ctype_digit($data['idbarang'])) {
        $error = 'ID barang harus berupa angka.';
    } elseif (!isset($jenisBarang[$data['jenis']])) {
        $error = 'Jenis barang tidak valid.';
    } elseif ($data['nama'] === '') {
        $error = 'Nama barang wajib diisi.';
    } elseif (mb_strlen($data['nama']) > 45) {
        $error = 'Nama barang maksimal 45 karakter.';
    } elseif (!ctype_digit((string)$data['idsatuan']) || !in_array((int)$data['idsatuan'], $satuanValid, true)) {
        $error = 'Pilih satuan yang tersedia.';
    } elseif (!in_array($data['status'], ['0', '1'], true)) {
        $error = 'Status tidak valid.';
    } elseif (!ctype_digit($data['harga']) || (int)$data['harga'] > 2147483647) {
        $error = 'Harga harus berupa angka bulat (tanpa titik atau koma).';
    } else {
        $id       = (int)$data['idbarang'];
        $jenis    = $data['jenis'];
        $nama     = $data['nama'];
        $idsatuan = (int)$data['idsatuan'];
        $status   = (int)$data['status'];
        $harga    = (int)$data['harga'];
        try {
            if ($mode === 'tambah') {
                // ---------- CREATE ----------
                $stmt = $db->prepare("INSERT INTO barang (idbarang, jenis, nama, idsatuan, status, harga) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param('issiii', $id, $jenis, $nama, $idsatuan, $status, $harga);
                $stmt->execute();
                flash('ok', 'Barang berhasil ditambahkan.');
            } else {
                // ---------- UPDATE ----------
                $stmt = $db->prepare("UPDATE barang SET jenis = ?, nama = ?, idsatuan = ?, status = ?, harga = ? WHERE idbarang = ?");
                $stmt->bind_param('ssiiii', $jenis, $nama, $idsatuan, $status, $harga, $id);
                $stmt->execute();
                flash('ok', 'Barang berhasil diperbarui.');
            }
            redirect('barang.php');
        } catch (mysqli_sql_exception $e) {
            $error = $e->getCode() === 1062
                ? 'ID barang sudah dipakai, gunakan ID lain.'
                : 'Gagal menyimpan: ' . $e->getMessage();
        }
    }
}

if (!in_array($aksi, ['tambah', 'edit'], true)) {
    $aksi = 'daftar';
}

if (!$post && $aksi === 'tambah') {
    $r = $db->query("SELECT COALESCE(MAX(idbarang), 0) + 1 AS berikut FROM barang")->fetch_assoc();
    $data['idbarang'] = (string)$r['berikut'];
}

if (!$post && $aksi === 'edit') {
    $id   = (int)($_GET['id'] ?? 0);
    $stmt = $db->prepare("SELECT idbarang, jenis, nama, idsatuan, status, harga FROM barang WHERE idbarang = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    if (!$row) {
        flash('error', 'Data barang tidak ditemukan.');
        redirect('barang.php');
    }
    $data = array_map('strval', $row);
}

// ---------- READ (dari VIEW v_barang) ----------
$daftar = null;
if ($aksi === 'daftar') {
    $daftar = $db->query("SELECT idbarang, nama, jenis_teks, nama_satuan, harga, status FROM v_barang ORDER BY idbarang");
}

tampil_header($aksi === 'tambah' ? 'Tambah Barang' : ($aksi === 'edit' ? 'Edit Barang' : 'Data Barang'), 'barang.php');
tampil_flash();
?>

<?php if ($aksi === 'daftar'): ?>

  <a class="tombol" href="barang.php?aksi=tambah">Tambah barang</a>
  <div class="tabel-bungkus">
  <table>
    <thead>
      <tr><th class="angka">ID</th><th>Nama barang</th><th>Jenis</th><th>Satuan</th><th class="angka">Harga</th><th>Status</th><th>Aksi</th></tr>
    </thead>
    <tbody>
    <?php if ($daftar->num_rows === 0): ?>
      <tr><td colspan="7" class="kosong">Belum ada data barang.</td></tr>
    <?php endif; ?>
    <?php while ($r = $daftar->fetch_assoc()): ?>
      <tr>
        <td class="angka"><?= (int)$r['idbarang'] ?></td>
        <td><?= h($r['nama']) ?></td>
        <td><?= h($r['jenis_teks']) ?></td>
        <td><?= h($r['nama_satuan'] ?? '-') ?></td>
        <td class="angka">Rp <?= number_format((int)$r['harga'], 0, ',', '.') ?></td>
        <td><?= $r['status'] == 1 ? '<span class="lencana aktif">Aktif</span>' : '<span class="lencana nonaktif">Nonaktif</span>' ?></td>
        <td class="aksi">
          <a href="barang.php?aksi=edit&id=<?= (int)$r['idbarang'] ?>">Edit</a>
          <form method="post" onsubmit="return confirm('Hapus barang <?= h($r['nama']) ?>?');">
            <input type="hidden" name="mode" value="hapus">
            <input type="hidden" name="id" value="<?= (int)$r['idbarang'] ?>">
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
  <?php if (!$satuanList): ?><p class="pesan error">Belum ada data satuan. Tambahkan satuan dulu sebelum membuat barang.</p><?php endif; ?>
  <form method="post" class="isian">
    <input type="hidden" name="mode" value="<?= h($aksi) ?>">

    <label for="idbarang">ID barang</label>
    <input type="text" id="idbarang" name="idbarang" inputmode="numeric" value="<?= h($data['idbarang']) ?>"
           <?= $aksi === 'edit' ? 'readonly' : 'required' ?>>
    <?php if ($aksi === 'tambah'): ?><p class="petunjuk">Terisi otomatis dengan ID berikutnya, boleh diubah.</p><?php endif; ?>

    <label for="nama">Nama barang</label>
    <input type="text" id="nama" name="nama" maxlength="45" value="<?= h($data['nama']) ?>" required>

    <label for="jenis">Jenis</label>
    <select id="jenis" name="jenis">
      <?php foreach ($jenisBarang as $kode => $nama): ?>
        <option value="<?= h($kode) ?>" <?= $data['jenis'] === $kode ? 'selected' : '' ?>><?= h($nama) ?></option>
      <?php endforeach; ?>
    </select>

    <label for="idsatuan">Satuan</label>
    <select id="idsatuan" name="idsatuan" required>
      <option value="">- pilih satuan -</option>
      <?php foreach ($satuanList as $s): ?>
        <option value="<?= (int)$s['idsatuan'] ?>" <?= (string)$data['idsatuan'] === (string)$s['idsatuan'] ? 'selected' : '' ?>><?= h($s['nama_satuan']) ?></option>
      <?php endforeach; ?>
    </select>

    <label for="harga">Harga (Rp)</label>
    <input type="text" id="harga" name="harga" inputmode="numeric" value="<?= h($data['harga']) ?>" required>
    <p class="petunjuk">Angka bulat saja, contoh: 3500</p>

    <label for="status">Status</label>
    <select id="status" name="status">
      <option value="1" <?= $data['status'] === '1' ? 'selected' : '' ?>>Aktif</option>
      <option value="0" <?= $data['status'] === '0' ? 'selected' : '' ?>>Nonaktif</option>
    </select>

    <div class="baris-tombol">
      <button type="submit" class="tombol"><?= $aksi === 'edit' ? 'Simpan perubahan' : 'Simpan barang' ?></button>
      <a href="barang.php">Batal</a>
    </div>
  </form>

<?php endif; ?>

<?php tampil_footer(); ?>
