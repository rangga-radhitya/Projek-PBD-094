# DATABASE_OBJECTS_IMPLEMENTATION_REPORT — Daily Bun Bakery Management System

Tahap: implementasi tepat **satu Stored Procedure, satu Function, dan satu Trigger**.
Objek: `sp_laporan_penjualan_periode`, `fn_hitung_total_penjualan`, `trg_detail_penerimaan_after_insert`.
Tidak ada objek database lain, tidak ada perubahan tabel/kolom/data, tidak ada migration, tidak ada commit/push.

---

## 0. Hasil Audit Sebelum Implementasi

| Pemeriksaan | Temuan (dari kode & SQL aktual) |
|---|---|
| Struktur database | 15 tabel + 6 view dari `dml_ddl.sql` / `views.sql`; dimuat ulang di MySQL 8.0.46 uji dan cocok dengan dokumentasi (`PROJECT_CONTEXT.md`) |
| Migration | Hanya 3 migration bawaan Laravel (`users`, `cache`, `jobs`); tidak ada tabel Bakery di migration. Skema Bakery dibuat dari file SQL |
| SP/Function/Trigger yang sudah ada | **Tidak ada** (SQL project, migration, dan `information_schema` database uji) |
| Perhitungan total penjualan di Laravel | **Belum ada** (aplikasi hanya membaca; tidak ada kode pembuat penjualan). Kolom `penjualan.subtotal_nilai/ppn/total_nilai` dan `detail_penjualan.subtotal` hanya kolom tersimpan |
| Perubahan stok / kartu stok di Laravel | **Tidak ada.** Pencarian `insert/update/delete/save/increment/decrement` dan `DB::insert/update/delete/statement` di `app/` → nol hasil; `routes/web.php` hanya berisi route GET. `KartuStok` hanya dibaca (`KartuStokController`, `DashboardController`) |
| Kolom stok pada `barang` | **Tidak ada** (`barang`: idbarang, jenis, nama, idsatuan, status, harga). Stok hanya ada sebagai saldo berjalan `kartu_stok.stock` |
| Diskon / komponen lain di penjualan | Tidak ada kolom diskon. PPN hanya tersimpan sebagai nilai (`penjualan.ppn`), tanpa tarif |
| Tabel detail penerimaan yang dipakai | `detail_penerimaan` (kolom `idpenerimaan`, `barang_idbarang`, `jumlah_terima`, `harga_satuan_terima`, `sub_total_terima`) |

**Penyesuaian karena struktur aktual** (sesuai ketentuan "jangan memaksakan kode"):
1. *"Memperbarui stok barang"* — karena `barang` tidak punya kolom stok dan skema tidak boleh diubah, **stok = saldo `kartu_stok.stock` terakhir per barang**. Trigger mencatat baris baru dengan saldo baru; itulah pembaruan stok.
2. Kode `kartu_stok.jenis_transaksi` tidak didefinisikan skema. Trigger memakai **`'M'` (masuk/penerimaan)** — ini keputusan yang perlu dikonfirmasi (satu literal di `routines.sql`).
3. Pernyataan di prompt tidak sepenuhnya cocok dengan project aktual: `vendor/` mensyaratkan **PHP ≥ 8.4.1** (bukan 8.3), dan Vite/Tailwind terpasang tetapi **tidak dipakai** (tampilan memakai `public/css/style.css`). Tidak ada yang diubah karena hal itu.

---

## 1. Function — `fn_hitung_total_penjualan`

**Tujuan:** menghitung total satu transaksi penjualan dari detail transaksinya.

- Parameter: `p_idpenjualan INT` → `DECIMAL(15,2)`.
- Total = `SUM(jumlah × harga_satuan)` dari `detail_penjualan` (`penjualan_idpenjualan = p_idpenjualan`).
- Memakai `DECIMAL` (bukan FLOAT) dan perkalian dalam DECIMAL, sehingga **tanpa kesalahan pembulatan** dan tidak overflow walau hasil melebihi batas INT (diuji 2.000.000.000 × 3 = 6.000.000.000).
- Transaksi ada tetapi tanpa detail → `0.00`. Transaksi tidak ada / parameter NULL → `NULL` (dapat dibedakan dari 0).
- Diskon: tidak ada di skema, sehingga tidak dihitung. PPN tidak termasuk (tersimpan di `penjualan.ppn`, tarifnya tidak ada di database).
- Karena Laravel belum punya aturan total penjualan, fungsi ini menjadi satu-satunya mekanisme perhitungan di sisi database; aplikasi memakainya apa adanya (tidak ada rumus ganda).

## 2. Stored Procedure — `sp_laporan_penjualan_periode`

**Tujuan:** rekap penjualan pada rentang tanggal.

- Parameter: `IN p_tanggal_awal DATE`, `IN p_tanggal_akhir DATE` (kedua ujung termasuk; transaksi pukul 23:59:59 pada tanggal akhir ikut terhitung).
- Mengembalikan **dua result set**:
  1. Rincian per transaksi: `idpenjualan, created_at, kasir, jumlah_item, subtotal_hitung, ppn, total_nilai`.
  2. Rekap: `jumlah_transaksi, total_pendapatan, total_ppn, total_nilai_tercatat`.
- `total_pendapatan` = jumlah hasil `fn_hitung_total_penjualan` (sebelum PPN); `total_nilai_tercatat` = jumlah `penjualan.total_nilai` di header. Keduanya ditampilkan agar selisih antara perhitungan dan nilai tersimpan terlihat.
- **Rentang tidak valid** → `SIGNAL SQLSTATE '45000'` dengan pesan jelas: parameter NULL, tanggal `0000-00-00`, atau awal > akhir. Tanggal yang mustahil (mis. 30 Feb, teks bukan tanggal) ditolak MySQL mode strict (error 1292) sebelum prosedur berjalan; pada mode SQL longgar tanggal itu menjadi `0000-00-00` dan ditangkap prosedur (diuji).
- **Transaksi yang belum memenuhi ketentuan pencatatan tidak dihitung**: ditafsirkan sebagai *header tanpa satu pun baris `detail_penjualan`* (tidak ada barang terjual). Skema tidak punya status transaksi, sehingga tidak ada kriteria lain yang bisa dipakai tanpa mengarang. **[KONFIRMASI]** bila ada aturan lain.

## 3. Trigger — `trg_detail_penerimaan_after_insert`

**Event:** `AFTER INSERT ON detail_penerimaan`, untuk setiap baris.

Langkah:
1. Menolak (SIGNAL 45000) baris dengan `barang_idbarang` kosong atau `jumlah_terima` kosong/≤ 0 → INSERT detail ikut dibatalkan.
2. Mengunci baris `barang` terkait (`SELECT ... FOR UPDATE`) agar penghitungan saldo per barang berurutan.
3. Membaca saldo terakhir `kartu_stok.stock` untuk barang itu (0 bila belum ada).
4. Menyisipkan ke `kartu_stok`: `jenis_transaksi='M'`, `masuk=jumlah_terima`, `keluar=0`, `stock=saldo+jumlah_terima`, `created_at=NOW()`, `idtransaksi=idpenerimaan`, `idbarang`.

Batasan yang disengaja (di luar lingkup "tepat satu trigger AFTER INSERT"): `UPDATE`/`DELETE` pada `detail_penerimaan` **tidak** menyesuaikan stok; koreksi sebaiknya lewat retur. `kartu_stok.idtransaksi` bertipe INT sedangkan `penerimaan.idpenerimaan` BIGINT (tidak masalah selama id < 2.147.483.647; di luar itu MySQL strict menolak).

## 4. Alasan Pemilihan Ketiga Objek

| Objek | Alasan |
|---|---|
| Function | Total transaksi adalah rumus kecil yang dipakai berulang (laporan, halaman detail, verifikasi). Satu definisi di database mencegah rumus berbeda-beda |
| Stored Procedure | Rekap periode adalah agregasi satu-kali-jalan yang mengembalikan beberapa result set; efisien dijalankan di database dan memakai function di atas |
| Trigger | Stok masuk harus selalu tercatat bersama penerimaan dan tidak boleh bergantung pada ingatan aplikasi; trigger menjamin atomisitas (satu transaksi dengan INSERT-nya) |

## 5. Struktur dan Parameter

| Objek | Jenis | Parameter | Keluaran | Karakteristik |
|---|---|---|---|---|
| `fn_hitung_total_penjualan` | FUNCTION | `p_idpenjualan INT` | `DECIMAL(15,2)` | `READS SQL DATA` |
| `sp_laporan_penjualan_periode` | PROCEDURE | `IN p_tanggal_awal DATE`, `IN p_tanggal_akhir DATE` | 2 result set | `READS SQL DATA`; `SIGNAL 45000` untuk rentang salah |
| `trg_detail_penerimaan_after_insert` | TRIGGER | — (memakai `NEW.*`) | baris `kartu_stok` | `AFTER INSERT` pada `detail_penerimaan` |

`READS SQL DATA` membuat objek dapat dibuat pada MySQL dengan binary log aktif tanpa mengubah `log_bin_trust_function_creators` (diuji pada MySQL 8.0.46 dengan pengaturan bawaan: binlog ON, trust OFF).

## 6. Source Code SQL

File: `database/sql/routines.sql` (dijalankan setelah `dml_ddl.sql` dan `views.sql`; aman dijalankan ulang).

```sql
-- =====================================================
-- Daily Bun - Database objects (database projek_pbd_094)
-- Jalankan SETELAH dml_ddl.sql dan views.sql. Aman dijalankan ulang (DROP ... IF EXISTS).
-- Isi file ini TEPAT tiga objek:
--   1. FUNCTION  fn_hitung_total_penjualan
--   2. PROCEDURE sp_laporan_penjualan_periode
--   3. TRIGGER   trg_detail_penerimaan_after_insert
-- Tidak mengubah tabel, kolom, atau data. Memakai DELIMITER (HeidiSQL / klien mysql).
-- =====================================================
USE projek_pbd_094;

DROP TRIGGER   IF EXISTS trg_detail_penerimaan_after_insert;
DROP PROCEDURE IF EXISTS sp_laporan_penjualan_periode;
DROP FUNCTION  IF EXISTS fn_hitung_total_penjualan;

DELIMITER $$

-- -----------------------------------------------------
-- 1. FUNCTION fn_hitung_total_penjualan
--    Total satu transaksi penjualan = SUM(jumlah x harga_satuan) dari detail_penjualan.
--    - Memakai DECIMAL (bukan FLOAT) sehingga tidak ada kesalahan pembulatan.
--    - Transaksi ada tetapi tanpa detail  -> 0.00
--    - Transaksi tidak ada / parameter NULL -> NULL (dapat dibedakan dari 0.00)
--    - Tidak ada kolom diskon di skema; PPN tidak ikut (PPN tersimpan di penjualan.ppn).
-- -----------------------------------------------------
CREATE FUNCTION fn_hitung_total_penjualan(p_idpenjualan INT)
RETURNS DECIMAL(15,2)
READS SQL DATA
BEGIN
    DECLARE v_ada   INT DEFAULT 0;
    DECLARE v_total DECIMAL(15,2) DEFAULT 0;

    IF p_idpenjualan IS NULL THEN
        RETURN NULL;
    END IF;

    SELECT COUNT(*) INTO v_ada FROM penjualan WHERE idpenjualan = p_idpenjualan;
    IF v_ada = 0 THEN
        RETURN NULL;
    END IF;

    SELECT COALESCE(SUM(CAST(jumlah AS DECIMAL(15,2)) * CAST(harga_satuan AS DECIMAL(15,2))), 0)
      INTO v_total
      FROM detail_penjualan
     WHERE penjualan_idpenjualan = p_idpenjualan;

    RETURN v_total;
END$$

-- -----------------------------------------------------
-- 2. PROCEDURE sp_laporan_penjualan_periode
--    Rekap penjualan pada rentang tanggal (kedua ujung termasuk).
--    Mengembalikan DUA result set:
--      (1) rincian per transaksi
--      (2) rekap: jumlah_transaksi, total_pendapatan, total_ppn, total_nilai_tercatat
--    Aturan:
--      - Parameter NULL, tanggal 0000-00-00, atau awal > akhir -> SIGNAL SQLSTATE '45000'.
--      - Hanya transaksi yang punya minimal satu baris detail_penjualan yang dihitung
--        (header tanpa detail dianggap belum tercatat lengkap).
--      - total_pendapatan = jumlah fn_hitung_total_penjualan (sebelum PPN).
--        total_nilai_tercatat = jumlah penjualan.total_nilai (nilai yang tersimpan di header).
-- -----------------------------------------------------
CREATE PROCEDURE sp_laporan_penjualan_periode(
    IN p_tanggal_awal  DATE,
    IN p_tanggal_akhir DATE
)
READS SQL DATA
BEGIN
    IF p_tanggal_awal IS NULL OR p_tanggal_akhir IS NULL THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Tanggal awal dan tanggal akhir wajib diisi.';
    END IF;

    IF YEAR(p_tanggal_awal) = 0 OR YEAR(p_tanggal_akhir) = 0 THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Tanggal tidak valid.';
    END IF;

    IF p_tanggal_awal > p_tanggal_akhir THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Tanggal awal tidak boleh lebih besar dari tanggal akhir.';
    END IF;

    -- (1) Rincian per transaksi
    SELECT p.idpenjualan,
           p.created_at,
           u.username                                   AS kasir,
           (SELECT COUNT(*) FROM detail_penjualan d
             WHERE d.penjualan_idpenjualan = p.idpenjualan) AS jumlah_item,
           fn_hitung_total_penjualan(p.idpenjualan)     AS subtotal_hitung,
           p.ppn,
           p.total_nilai
      FROM penjualan p
      LEFT JOIN `user` u ON u.iduser = p.iduser
     WHERE p.created_at >= p_tanggal_awal
       AND p.created_at <  DATE_ADD(p_tanggal_akhir, INTERVAL 1 DAY)
       AND EXISTS (SELECT 1 FROM detail_penjualan d
                    WHERE d.penjualan_idpenjualan = p.idpenjualan)
     ORDER BY p.created_at, p.idpenjualan;

    -- (2) Rekap
    SELECT COUNT(*)                              AS jumlah_transaksi,
           COALESCE(SUM(t.subtotal_hitung), 0)   AS total_pendapatan,
           COALESCE(SUM(t.ppn), 0)               AS total_ppn,
           COALESCE(SUM(t.total_nilai), 0)       AS total_nilai_tercatat
      FROM (SELECT fn_hitung_total_penjualan(p.idpenjualan) AS subtotal_hitung,
                   p.ppn,
                   p.total_nilai
              FROM penjualan p
             WHERE p.created_at >= p_tanggal_awal
               AND p.created_at <  DATE_ADD(p_tanggal_akhir, INTERVAL 1 DAY)
               AND EXISTS (SELECT 1 FROM detail_penjualan d
                            WHERE d.penjualan_idpenjualan = p.idpenjualan)) t;
END$$

-- -----------------------------------------------------
-- 3. TRIGGER trg_detail_penerimaan_after_insert
--    Setiap baris baru di detail_penerimaan otomatis menambah stok lewat kartu_stok.
--    - Tabel barang TIDAK punya kolom stok; stok = kolom kartu_stok.stock baris terakhir
--      per barang (saldo berjalan). Trigger mencatat baris baru dengan
--      stock = saldo sebelumnya + jumlah_terima.
--    - jenis_transaksi = 'M' (masuk / penerimaan). Skema tidak mendefinisikan kode ini;
--      'M' adalah KEPUTUSAN yang perlu dikonfirmasi pemilik project.
--    - idtransaksi = idpenerimaan (identitas transaksi penerimaan).
--    - Baris dengan barang kosong atau jumlah_terima <= 0 DITOLAK (SIGNAL 45000), sehingga
--      INSERT detail-nya ikut dibatalkan dan tidak ada stok setengah jadi.
--    - Satu-satunya pihak yang mengisi kartu_stok untuk penerimaan. Aplikasi (Laravel)
--      TIDAK BOLEH menulis kartu_stok untuk penerimaan agar tidak terjadi stok ganda.
--    - Berjalan di transaksi yang sama dengan INSERT pemicunya: ROLLBACK membatalkan keduanya.
--    - Baris barang dikunci (FOR UPDATE) agar penghitungan saldo per barang berurutan.
-- -----------------------------------------------------
CREATE TRIGGER trg_detail_penerimaan_after_insert
AFTER INSERT ON detail_penerimaan
FOR EACH ROW
BEGIN
    DECLARE v_kunci INT;
    DECLARE v_saldo INT DEFAULT 0;

    IF NEW.barang_idbarang IS NULL OR NEW.jumlah_terima IS NULL OR NEW.jumlah_terima <= 0 THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Detail penerimaan tidak valid: barang wajib diisi dan jumlah_terima harus lebih dari 0.';
    END IF;

    SELECT idbarang INTO v_kunci
      FROM barang
     WHERE idbarang = NEW.barang_idbarang
       FOR UPDATE;

    SELECT stock INTO v_saldo
      FROM kartu_stok
     WHERE idbarang = NEW.barang_idbarang
     ORDER BY idkartu_stok DESC
     LIMIT 1;

    IF v_saldo IS NULL THEN
        SET v_saldo = 0;
    END IF;

    INSERT INTO kartu_stok (jenis_transaksi, masuk, keluar, stock, created_at, idtransaksi, idbarang)
    VALUES ('M', NEW.jumlah_terima, 0, v_saldo + NEW.jumlah_terima, NOW(), NEW.idpenerimaan, NEW.barang_idbarang);
END$$

DELIMITER ;
```

## 7. Integrasi dengan Laravel

| Komponen | Perubahan |
|---|---|
| `app/Services/PenjualanService.php` (baru) | `laporanPeriode($awal,$akhir)` memanggil `CALL sp_laporan_penjualan_periode(?, ?)` lewat PDO koneksi Laravel dengan **parameter terikat**, membaca 2 result set (`nextRowset`), mengubah `SQLSTATE 45000` menjadi `InvalidArgumentException` berpesan dari prosedur. `totalPenjualan($id)` memanggil `SELECT fn_hitung_total_penjualan(?)`. `objekBelumTerpasang()` mengenali error 1305 |
| `LaporanController::show` (diubah) | Untuk `/laporan/penjualan` membaca `?dari=&sampai=`, memvalidasi format, memanggil service, menampilkan galat dengan jelas. Empat laporan lain tidak berubah |
| `resources/views/laporan/_penjualan.blade.php` (baru), `laporan/show.blade.php`, `laporan/index.blade.php` (diubah) | Form periode, kartu rekap, tabel rincian dengan tautan ke detail; catatan bila objek belum terpasang |
| `PenjualanController::show` + `penjualan/show.blade.php` (diubah) | Baris pembanding "Subtotal dihitung dari detail (fn_hitung_total_penjualan)" dengan lencana *sesuai / berbeda dari subtotal tercatat* terhadap `penjualan.subtotal_nilai`. Disembunyikan bila fungsi belum terpasang |
| Trigger | Tidak ada kode Laravel — memang tidak boleh ada (lihat §11) |

Tidak ada route baru (memakai `/laporan/{jenis}` dan `/penjualan/{id}` yang sudah ada). Fitur lain tidak berubah (regresi 58 URL lulus). Halaman tidak error bila `routines.sql` belum dijalankan.

## 8. Hasil Pengujian

Lingkungan: **MySQL 8.0.46** (sandbox, pengaturan bawaan ketat: `ONLY_FULL_GROUP_BY`, `STRICT_TRANS_TABLES`, binlog ON). Database uji `projek_pbd_094_uji` dibuat dari `dml_ddl.sql` + `views.sql` + `routines.sql` (hanya nama database diganti); **database asli Anda tidak disentuh**. Skrip uji: `database/sql/routines_test.sql` (berjalan di dalam transaksi + ROLLBACK).

| Objek | Skenario | Hasil |
|---|---|---|
| Function | Satu detail (12000 × 2 = 24000) | LULUS |
| Function | Tiga detail (12000 + 24000 + 30000 = 66000) | LULUS |
| Function | Tanpa detail → 0.00; transaksi tidak ada → NULL; parameter NULL → NULL | LULUS |
| Function | Nilai besar 2.000.000.000 × 3 = 6.000.000.000 (tanpa overflow/pembulatan) | LULUS |
| SP | Periode ada transaksi (Okt: 2 transaksi, pendapatan 90.000, PPN 9.900, tercatat 99.900); transaksi 23:59:59 pada tanggal akhir ikut; header tanpa detail dan transaksi di luar periode tidak ikut | LULUS |
| SP | Satu hari saja; periode tanpa transaksi (rincian kosong, rekap 0) | LULUS |
| SP | Tidak valid: awal > akhir, parameter NULL (SQLSTATE 45000 + pesan); 30 Feb / teks / `0000-00-00` (error 1292 mode strict); mode SQL longgar → ditangkap prosedur | LULUS |
| Trigger | Penerimaan 1 jenis barang: masuk 15, saldo = awal + 15, `idtransaksi` = idpenerimaan, jenis `M` | LULUS |
| Trigger | Beberapa jenis barang dalam satu INSERT (barang 2 × 10, barang 3 × 7) | LULUS |
| Trigger | Saldo kumulatif (15 lalu 5 → 20); `stock` = SUM(masuk − keluar) untuk tiap barang | LULUS |
| Trigger | Tidak ada stok ganda: jumlah baris detail = jumlah baris kartu stok (4 = 4) | LULUS |
| Trigger | ROLLBACK: detail dan kartu stok sama-sama hilang; setelah ROLLBACK akhir tidak ada data uji tersisa | LULUS |
| Trigger | Penolakan: jumlah 0, negatif, NULL, barang NULL (error 1644); INSERT 2 baris dengan baris ke-2 tidak valid → seluruh statement batal (detail & kartu baru = 0) | LULUS |
| Trigger | Dua sesi menerima barang yang sama bersamaan: saldo 5 lalu 13 (= 5 + 8), tanpa deadlock pada percobaan ini | LULUS |
| Service PHP | `PenjualanService` pada MySQL asli, `EMULATE_PREPARES` false & true: 2 result set terbaca, rentang tidak valid → `InvalidArgumentException`, koneksi tetap sehat, input berisi `DROP TABLE` hanya ditolak sebagai tanggal (tabel utuh), objek belum terpasang → kode 1305 dikenali | LULUS |
| Laravel (route/view) | 58 URL (semua halaman + varian laporan penjualan) tanpa error; halaman `/laporan/penjualan` & `/penjualan/{id}` diuji untuk: belum mencari, tanggal tidak valid, satu tanggal, SP menolak, objek belum terpasang, gagal umum, sukses, periode kosong, fungsi sesuai/berbeda | LULUS |

**Batas pengujian (jujur):**
- Lapisan Laravel (controller + view) diuji dengan runtime PHP 8.4 dan database SQLite yang **tidak punya objek**, serta *service tiruan* untuk jalur sukses, karena runtime PHP 8.4 di sandbox tidak bisa terhubung ke MySQL. Alur lengkap Laravel → MySQL asli **belum dijalankan end-to-end**; yang diuji pada MySQL asli adalah `PenjualanService` (PHP 8.3 CLI + PDO) dan objek SQL.
- MariaDB (bawaan Laragon versi lama) tidak diuji; sintaks yang dipakai standar, tetapi uji ulang bila memakai MariaDB.
- Beban/konkurensi tinggi tidak diuji. Pada konkurensi, MySQL dapat membatalkan salah satu transaksi dengan deadlock (error 1213); aplikasi sebaiknya mengulang transaksi tersebut.

### Keluaran uji `routines_test.sql` (MySQL)

```text
+---------------------------------------------+---------------------------------+----------------------------------+-----------------------------------+-------+
| uji                                         | kartu_bertambah_dalam_transaksi | kartu_bertambah_setelah_rollback | detail_bertambah_setelah_rollback | hasil |
+---------------------------------------------+---------------------------------+----------------------------------+-----------------------------------+-------+
| A. ROLLBACK membatalkan detail + kartu stok |                               1 |                                0 |                                 0 | LULUS |
+---------------------------------------------+---------------------------------+----------------------------------+-----------------------------------+-------+
+------------------------------------------------+---------------+------------+-------+
| uji                                            | nilai         | diharapkan | hasil |
+------------------------------------------------+---------------+------------+-------+
| B1. satu detail (12000 x 2)                    |      24000.00 |      24000 | LULUS |
| B2. tiga detail (12000+24000+30000)            |      66000.00 |      66000 | LULUS |
| B3. tanpa detail -> 0.00                       |          0.00 |          0 | LULUS |
| B4. transaksi tidak ada -> NULL                |          NULL |       NULL | LULUS |
| B5. parameter NULL -> NULL                     |          NULL |       NULL | LULUS |
| B6. nilai besar tanpa overflow (6.000.000.000) | 6000000000.00 | 6000000000 | LULUS |
+------------------------------------------------+---------------+------------+-------+
+-------------+---------------------+---------+-------------+-----------------+------+-------------+
| idpenjualan | created_at          | kasir   | jumlah_item | subtotal_hitung | ppn  | total_nilai |
+-------------+---------------------+---------+-------------+-----------------+------+-------------+
|           1 | 2026-10-10 12:00:00 | kasir01 |           1 |        24000.00 | 2640 |       26640 |
|           2 | 2026-10-12 23:59:59 | kasir01 |           3 |        66000.00 | 7260 |       73260 |
+-------------+---------------------+---------+-------------+-----------------+------+-------------+
+------------------+------------------+-----------+----------------------+
| jumlah_transaksi | total_pendapatan | total_ppn | total_nilai_tercatat |
+------------------+------------------+-----------+----------------------+
|                2 |         90000.00 |      9900 |                99900 |
+------------------+------------------+-----------+----------------------+
+-------------+---------------------+---------+-------------+-----------------+------+-------------+
| idpenjualan | created_at          | kasir   | jumlah_item | subtotal_hitung | ppn  | total_nilai |
+-------------+---------------------+---------+-------------+-----------------+------+-------------+
|           2 | 2026-10-12 23:59:59 | kasir01 |           3 |        66000.00 | 7260 |       73260 |
+-------------+---------------------+---------+-------------+-----------------+------+-------------+
+------------------+------------------+-----------+----------------------+
| jumlah_transaksi | total_pendapatan | total_ppn | total_nilai_tercatat |
+------------------+------------------+-----------+----------------------+
|                1 |         66000.00 |      7260 |                73260 |
+------------------+------------------+-----------+----------------------+
+------------------+------------------+-----------+----------------------+
| jumlah_transaksi | total_pendapatan | total_ppn | total_nilai_tercatat |
+------------------+------------------+-----------+----------------------+
|                0 |             0.00 |         0 |                    0 |
+------------------+------------------+-----------+----------------------+
+-------------+---------------------+---------+-------------+-----------------+------+-------------+
| idpenjualan | created_at          | kasir   | jumlah_item | subtotal_hitung | ppn  | total_nilai |
+-------------+---------------------+---------+-------------+-----------------+------+-------------+
|           5 | 2026-09-01 08:00:00 | kasir01 |           1 |   6000000000.00 | NULL |        NULL |
+-------------+---------------------+---------+-------------+-----------------+------+-------------+
+------------------+------------------+-----------+----------------------+
| jumlah_transaksi | total_pendapatan | total_ppn | total_nilai_tercatat |
+------------------+------------------+-----------+----------------------+
|                1 |    6000000000.00 |         0 |                    0 |
+------------------+------------------+-----------+----------------------+
+-----------------------------------------------------------------------------------+-------+--------+-------+-------------+-----------------+-------+
| uji                                                                               | masuk | keluar | stock | idtransaksi | jenis_transaksi | hasil |
+-----------------------------------------------------------------------------------+-------+--------+-------+-------------+-----------------+-------+
| D1. satu barang: masuk 15, keluar 0, saldo = awal+15, ref = idpenerimaan, jenis M |    15 |      0 |    15 |           2 | M               | LULUS |
+-----------------------------------------------------------------------------------+-------+--------+-------+-------------+-----------------+-------+
+-----------------------------------+-------+-------+-------+
| uji                               | masuk | stock | hasil |
+-----------------------------------+-------+-------+-------+
| D2. barang 2 : masuk 10, saldo 10 |    10 |    10 | LULUS |
| D2. barang 3 : masuk 7, saldo 7   |     7 |     7 | LULUS |
+-----------------------------------+-------+-------+-------+
+----------------------------------------------+----------------+------------+-------+
| uji                                          | saldo_terakhir | diharapkan | hasil |
+----------------------------------------------+----------------+------------+-------+
| D3. saldo kumulatif barang 1 = awal + 15 + 5 |             20 |         20 | LULUS |
+----------------------------------------------+----------------+------------+-------+
+-------------------------------------------------------------+---------------+--------------+-------+
| uji                                                         | jumlah_detail | jumlah_kartu | hasil |
+-------------------------------------------------------------+---------------+--------------+-------+
| D4. satu detail = tepat satu baris kartu stok (tanpa ganda) |             4 |            4 | LULUS |
+-------------------------------------------------------------+---------------+--------------+-------+
+--------------------------------+----------------+------------+-------+
| uji                            | saldo_terakhir | sum_mutasi | hasil |
+--------------------------------+----------------+------------+-------+
| D5. konsistensi saldo barang 1 |             20 |         20 | LULUS |
| D5. konsistensi saldo barang 2 |             10 |         10 | LULUS |
| D5. konsistensi saldo barang 3 |              7 |          7 | LULUS |
+--------------------------------+----------------+------------+-------+
+-------------------------------------------------------+------------+-------------------+-------+
| uji                                                   | kartu_stok | detail_penerimaan | hasil |
+-------------------------------------------------------+------------+-------------------+-------+
| E. setelah ROLLBACK akhir: tidak ada data uji tersisa |          0 |                 0 | LULUS |
+-------------------------------------------------------+------------+-------------------+-------+
```

### Keluaran uji penolakan

```text
| uji             |
| F1 awal > akhir |
ERROR 1644 (45000) at line 2: Tanggal awal tidak boleh lebih besar dari tanggal akhir.
| uji               |
| F2 parameter NULL |
ERROR 1644 (45000) at line 3: Tanggal awal dan tanggal akhir wajib diisi.
| uji            |
| F2b akhir NULL |
ERROR 1644 (45000) at line 4: Tanggal awal dan tanggal akhir wajib diisi.
| uji                                       |
| F3 tanggal tidak ada di kalender (30 Feb) |
ERROR 1292 (22007) at line 5: Incorrect date value: '2026-02-30' for column 'p_tanggal_awal' at row 1
| uji             |
| F3b tanggal nol |
ERROR 1292 (22007) at line 6: Incorrect date value: '0000-00-00' for column 'p_tanggal_awal' at row 1
| uji                    |
| F3c teks bukan tanggal |
ERROR 1292 (22007) at line 7: Incorrect date value: 'abc' for column 'p_tanggal_awal' at row 1
| uji                  |
| F4 jumlah_terima = 0 |
ERROR 1644 (45000) at line 15: Detail penerimaan tidak valid: barang wajib diisi dan jumlah_terima harus lebih dari 0.
| uji                       |
| F4b jumlah_terima negatif |
ERROR 1644 (45000) at line 17: Detail penerimaan tidak valid: barang wajib diisi dan jumlah_terima harus lebih dari 0.
| uji                    |
| F4c jumlah_terima NULL |
ERROR 1644 (45000) at line 19: Detail penerimaan tidak valid: barang wajib diisi dan jumlah_terima harus lebih dari 0.
| uji            |
| F5 barang NULL |
ERROR 1644 (45000) at line 21: Detail penerimaan tidak valid: barang wajib diisi dan jumlah_terima harus lebih dari 0.
| uji                                       |
| F6 INSERT 2 baris, baris ke-2 tidak valid |
ERROR 1644 (45000) at line 23: Detail penerimaan tidak valid: barang wajib diisi dan jumlah_terima harus lebih dari 0.
| uji                                                                         | detail_baru | kartu_baru | hasil |
| setelah semua penolakan (dalam transaksi): detail & kartu stok baru harus 0 |           0 |          0 | LULUS |
```

### Keluaran uji `PenjualanService` (PDO, MySQL asli)

```text
=== EMULATE_PREPARES=false
periode Okt: rincian=2 rekap={"jumlah_transaksi":2,"total_pendapatan":"90000.00","total_ppn":"9900","total_nilai_tercatat":"99900"}
  id pertama: {"idpenjualan":1,"created_at":"2026-10-10 12:00:00","kasir":"kasir01","jumlah_item":1,"subtotal_hitung":"24000.00","ppn":2640,"total_nilai":26640}
periode kosong: rincian=0 rekap={"jumlah_transaksi":0,"total_pendapatan":"0.00","total_ppn":"0","total_nilai_tercatat":"0"}
tidak valid -> InvalidArgumentException: Tanggal awal tidak boleh lebih besar dari tanggal akhir.
tanggal mustahil -> PDOException SQLSTATE 22007 (1292)
fn total #1='24000.00' #2='66000.00' #3(tanpa detail)='0.00' #999=NULL
query lanjutan setelah CALL/error: 2
input jahat ditolak sebagai tanggal: PDOException
tabel penjualan masih ada: 3 baris
=== EMULATE_PREPARES=true
periode Okt: rincian=2 rekap={"jumlah_transaksi":2,"total_pendapatan":"90000.00","total_ppn":"9900","total_nilai_tercatat":"99900"}
  id pertama: {"idpenjualan":1,"created_at":"2026-10-10 12:00:00","kasir":"kasir01","jumlah_item":1,"subtotal_hitung":"24000.00","ppn":2640,"total_nilai":26640}
periode kosong: rincian=0 rekap={"jumlah_transaksi":0,"total_pendapatan":"0.00","total_ppn":"0","total_nilai_tercatat":"0"}
tidak valid -> InvalidArgumentException: Tanggal awal tidak boleh lebih besar dari tanggal akhir.
tanggal mustahil -> PDOException SQLSTATE 22007 (1292)
fn total #1='24000.00' #2='66000.00' #3(tanpa detail)='0.00' #999=NULL
query lanjutan setelah CALL/error: 2
input jahat ditolak sebagai tanggal: PDOException
tabel penjualan masih ada: 3 baris
SP belum terpasang -> objekBelumTerpasang=true (1305)
fn belum terpasang -> objekBelumTerpasang=true (1305)
```

## 9. Contoh Input dan Output

```sql
-- Function
SELECT fn_hitung_total_penjualan(2);
--> 66000.00      (12000x1 + 8000x3 + 15000x2)

-- Stored procedure
CALL sp_laporan_penjualan_periode('2026-10-01', '2026-10-31');
--> rincian : (1, 2026-10-10 12:00:00, kasir01, 1 item, 24000.00, ppn 2640, total 26640)
             (2, 2026-10-12 23:59:59, kasir01, 3 item, 66000.00, ppn 7260, total 73260)
--> rekap   : jumlah_transaksi=2, total_pendapatan=90000.00, total_ppn=9900, total_nilai_tercatat=99900

CALL sp_laporan_penjualan_periode('2026-10-31', '2026-10-01');
--> ERROR 1644 (45000): Tanggal awal tidak boleh lebih besar dari tanggal akhir.

-- Trigger
INSERT INTO detail_penerimaan (idpenerimaan, barang_idbarang, jumlah_terima, harga_satuan_terima, sub_total_terima)
VALUES (2, 1, 15, 5000, 75000);
--> kartu_stok: jenis_transaksi='M', masuk=15, keluar=0, stock=saldo_sebelumnya+15, idtransaksi=2, idbarang=1
```

Di aplikasi: buka **Laporan → Laporan Penjualan**, isi tanggal, tekan *Cari*. Halaman *Detail Penjualan* menampilkan baris pembanding dari fungsi.

## 10. Bukti Eksekusi

Bukti berupa keluaran lengkap pada §8 (tabel LULUS/GAGAL, hasil CALL, pesan error asli MySQL). Tampilan halaman laporan penjualan (desktop & mobile) ikut dikirim sebagai gambar terpisah (`laporan_penjualan_desktop.png`); gambar dirender dengan data tiruan berbentuk keluaran SP karena Laravel tidak terhubung ke MySQL di sandbox.

## 11. Pencegahan Perubahan Stok Ganda

1. **Satu penanggung jawab.** Audit membuktikan Laravel tidak menulis stok maupun `kartu_stok` sama sekali (tanpa kode tulis, tanpa route non-GET). Trigger menjadi satu-satunya mekanisme perubahan stok untuk penerimaan.
2. **Aturan untuk pengembangan berikutnya:** saat fitur penerimaan dibuat, aplikasi hanya melakukan `INSERT` ke `penerimaan` dan `detail_penerimaan` di dalam satu transaksi. Aplikasi **tidak boleh** menambah/menyisipkan `kartu_stok` untuk penerimaan.
3. **Satu baris detail → tepat satu baris kartu stok** (terbukti di uji D4: 4 detail = 4 kartu). Trigger berjalan sekali per baris yang di-INSERT.
4. **Atomik.** Trigger berjalan dalam transaksi yang sama dengan INSERT detail; ROLLBACK atau kegagalan validasi membatalkan keduanya, sehingga stok tidak pernah setengah tercatat.
5. **Saldo konsisten.** Baris barang dikunci saat saldo dihitung, sehingga dua penerimaan bersamaan untuk barang yang sama menghasilkan saldo berurutan (uji dua sesi: 5 → 13).
6. **Perubahan di luar trigger tidak dilindungi:** `UPDATE`/`DELETE` langsung pada `detail_penerimaan` atau `kartu_stok` tidak menyesuaikan saldo. Hindari edit manual; koreksi lewat transaksi baru (retur).

---

## Daftar File

**Dibuat**
- `database/sql/routines.sql` — tiga objek database
- `database/sql/routines_test.sql` — skrip uji (transaksi + ROLLBACK, untuk database uji)
- `app/Services/PenjualanService.php`
- `resources/views/laporan/_penjualan.blade.php`
- `DATABASE_OBJECTS_IMPLEMENTATION_REPORT.md`

**Diubah**
- `app/Http/Controllers/LaporanController.php`, `app/Http/Controllers/PenjualanController.php`
- `resources/views/laporan/show.blade.php`, `resources/views/laporan/index.blade.php`, `resources/views/penjualan/show.blade.php`
- `PROJECT_CONTEXT.md` (pernyataan "tidak ada SP/Function/Trigger" diperbarui)

**Tidak diubah:** `dml_ddl.sql`, `views.sql`, migration, model, route, konfigurasi, `.env`.

## Cara Menjalankan dan Menguji

1. Backup database. Di HeidiSQL, pilih database `projek_pbd_094` lalu jalankan `database/sql/routines.sql` (setelah `dml_ddl.sql` dan `views.sql`). Verifikasi:
   `SELECT routine_type, routine_name FROM information_schema.routines WHERE routine_schema = 'projek_pbd_094';` dan
   `SELECT trigger_name FROM information_schema.triggers WHERE trigger_schema = 'projek_pbd_094';` → tepat 2 routine dan 1 trigger.
2. Uji objek: buat database uji (jalankan ketiga file SQL dengan nama database diganti `projek_pbd_094_uji`), lalu jalankan `database/sql/routines_test.sql`; semua baris `hasil` harus `LULUS`. Bagian "UJI PENOLAKAN" di akhir file dijalankan satu per satu.
3. Uji aplikasi (PHP 8.4+): `php artisan serve`, buka `/laporan/penjualan`, isi periode, tekan Cari. Buka `/penjualan/{id}` untuk melihat baris pembanding fungsi. Data penjualan harus ada (tabel transaksi masih kosong pada data dummy bawaan).
4. Untuk melepas objek: `DROP TRIGGER trg_detail_penerimaan_after_insert; DROP PROCEDURE sp_laporan_penjualan_periode; DROP FUNCTION fn_hitung_total_penjualan;` (aplikasi tetap berjalan; bagian terkait menampilkan catatan "belum terpasang").

## Kendala dan Bagian yang Belum Selesai

1. **Kode `jenis_transaksi = 'M'`** adalah keputusan sementara; perlu konfirmasi (dan kode untuk penjualan/retur kelak).
2. **Stok keluar belum ada.** Penjualan dan retur belum mengurangi stok; ini di luar tiga objek yang diminta.
3. **Definisi "transaksi belum memenuhi ketentuan"** ditafsirkan sebagai penjualan tanpa detail (skema tidak punya status).
4. **Tidak ada diskon/PPN-rate** di skema, jadi total memakai harga × jumlah saja; PPN hanya ditampilkan sebagaimana tersimpan.
5. **Uji end-to-end Laravel → MySQL** belum dijalankan (lihat batas pengujian). Disarankan Anda mencoba langkah 3 di Laragon.
6. **Persyaratan PHP 8.4+** (vendor) berbeda dari yang tertulis di `composer.json`/README; belum diubah.
7. Perubahan belum di-commit (sesuai instruksi); repo lokal berisi perubahan ini sebagai working tree.
