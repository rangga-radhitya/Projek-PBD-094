<?php
// Fungsi bantu yang dipakai semua halaman.
// Tidak ada query builder di sini: semua SQL ditulis langsung di tiap halaman.

require __DIR__ . '/koneksi.php';

session_start();

// Error MySQL dilempar sebagai exception supaya bisa ditangkap dengan try/catch
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

function db(): mysqli
{
    static $koneksi = null;
    if ($koneksi === null) {
        try {
            $koneksi = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            $koneksi->set_charset('utf8mb4');
        } catch (mysqli_sql_exception $e) {
            die('Koneksi database gagal. Pastikan MySQL menyala, database "' . DB_NAME
                . '" sudah dibuat, dan user/password di includes/koneksi.php benar.');
        }
    }
    return $koneksi;
}

// Escape output HTML
function h($teks): string
{
    return htmlspecialchars((string)$teks, ENT_QUOTES, 'UTF-8');
}

function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

// Pesan sekali tampil (setelah simpan / hapus)
function flash(string $jenis, string $teks): void
{
    $_SESSION['flash'] = [$jenis, $teks];
}

function tampil_flash(): void
{
    if (!empty($_SESSION['flash'])) {
        [$jenis, $teks] = $_SESSION['flash'];
        unset($_SESSION['flash']);
        echo '<p class="pesan ' . h($jenis) . '" role="status">' . h($teks) . '</p>';
    }
}

function tampil_header(string $judul, string $aktif = ''): void
{
    $menu = [
        'role.php'             => 'Role',
        'user.php'             => 'User',
        'satuan.php'           => 'Satuan',
        'vendor.php'           => 'Vendor',
        'margin_penjualan.php' => 'Margin Penjualan',
        'barang.php'           => 'Barang',
    ];
    ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= h($judul) ?> - Proyek PBD</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header class="atas">
  <div class="isi">
    <span class="merek">Proyek PBD</span>
    <nav>
      <?php foreach ($menu as $file => $label): ?>
        <a href="<?= h($file) ?>"<?= $file === $aktif ? ' class="aktif" aria-current="page"' : '' ?>><?= h($label) ?></a>
      <?php endforeach; ?>
    </nav>
  </div>
</header>
<main>
  <h1><?= h($judul) ?></h1>
<?php
}

function tampil_footer(): void
{
    echo "</main>\n</body>\n</html>\n";
}
