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
