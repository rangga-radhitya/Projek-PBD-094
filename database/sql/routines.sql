-- =====================================================
-- Daily Bun - Objek database programming
-- Jalankan di HeidiSQL (tab Query) SETELAH dml_ddl.sql dan views.sql.
-- Aman dijalankan ulang (DROP ... IF EXISTS di awal).
--
-- Tepat tiga objek:
--   1. FUNCTION  fn_hitung_subtotal(harga_satuan, jumlah)
--        Menghitung sub total satu baris detail pengadaan
--        (harga_satuan x jumlah). Dipakai saat menyimpan pengadaan.
--   2. PROCEDURE sp_aktifkan_margin_penjualan(idmargin)
--        Menonaktifkan margin yang sedang aktif, lalu mengaktifkan
--        margin yang dipilih. Dipakai tombol Aktifkan di /margin_penjualan.
--   3. TRIGGER   trg_detail_penerimaan_after_insert
--        Setiap detail penerimaan baru otomatis menambah stok
--        (kartu_stok jenis 'M').
--
-- Catatan:
-- - Validasi data penerimaan (jumlah terima > 0, barang wajib ada)
--   dilakukan aplikasi di PenerimaanController; trigger hanya menulis
--   kartu stok.
-- - Penghapusan penerimaan (koreksi stok 'K') ditangani aplikasi
--   di PenerimaanController::destroy().
-- - Stok terkini barang dihitung dengan subquery ke kartu_stok.
-- - Penjualan dan retur TIDAK memakai objek di file ini.
-- =====================================================
USE projek_pbd_094;

DROP TRIGGER   IF EXISTS trg_detail_penerimaan_after_insert;
DROP PROCEDURE IF EXISTS sp_aktifkan_margin_penjualan;
DROP FUNCTION  IF EXISTS fn_hitung_subtotal;

-- Sisa objek versi lama (bila masih ada).
DROP PROCEDURE IF EXISTS sp_hitung_ulang_total_pengadaan;
DROP PROCEDURE IF EXISTS sp_hapus_penerimaan;
DROP FUNCTION  IF EXISTS fn_hitung_ppn;
DROP FUNCTION  IF EXISTS fn_stok_barang;

DELIMITER $$

-- -----------------------------------------------------
-- 1. FUNCTION fn_hitung_subtotal
--    - Sub total = harga_satuan x jumlah.
--    - Nilai kosong/negatif dianggap 0 memakai CASE.
--    - DETERMINISTIC + NO SQL: hasil hanya bergantung pada parameter.
-- -----------------------------------------------------
CREATE FUNCTION fn_hitung_subtotal(p_harga INT, p_jumlah INT)
RETURNS INT
DETERMINISTIC
NO SQL
RETURN CASE WHEN p_harga > 0 THEN p_harga ELSE 0 END
     * CASE WHEN p_jumlah > 0 THEN p_jumlah ELSE 0 END$$

-- -----------------------------------------------------
-- 2. PROCEDURE sp_aktifkan_margin_penjualan
--    - Semua margin yang sedang aktif dinonaktifkan.
--    - Margin pilihan diaktifkan dan updated_at diperbarui.
--    - Hanya satu margin yang aktif setelah pemanggilan.
-- -----------------------------------------------------
CREATE PROCEDURE sp_aktifkan_margin_penjualan(IN p_idmargin INT)
BEGIN
    UPDATE margin_penjualan
       SET status = 0
     WHERE status = 1
       AND idmargin_penjualan <> p_idmargin;

    UPDATE margin_penjualan
       SET status = 1,
           updated_at = NOW()
     WHERE idmargin_penjualan = p_idmargin;
END$$

-- -----------------------------------------------------
-- 3. TRIGGER trg_detail_penerimaan_after_insert
--    - Stok masuk otomatis saat detail penerimaan dibuat (jenis 'M').
--    - Baris barang dikunci (FOR UPDATE) agar saldo per barang berurutan.
--    - Satu-satunya penulis kartu_stok untuk penerimaan; aplikasi tidak
--      boleh menulis kartu_stok untuk penerimaan agar tidak dobel.
-- -----------------------------------------------------
CREATE TRIGGER trg_detail_penerimaan_after_insert
AFTER INSERT ON detail_penerimaan
FOR EACH ROW
BEGIN
    DECLARE v_kunci INT;
    DECLARE v_saldo INT DEFAULT 0;

    SELECT idbarang INTO v_kunci
      FROM barang
     WHERE idbarang = NEW.barang_idbarang
       FOR UPDATE;

    SELECT stock INTO v_saldo
      FROM kartu_stok
     WHERE idbarang = NEW.barang_idbarang
     ORDER BY idkartu_stok DESC
     LIMIT 1;

    INSERT INTO kartu_stok (jenis_transaksi, masuk, keluar, stock, created_at, idtransaksi, idbarang)
    VALUES ('M', NEW.jumlah_terima, 0, COALESCE(v_saldo, 0) + NEW.jumlah_terima, NOW(), NEW.idpenerimaan, NEW.barang_idbarang);
END$$

DELIMITER ;

-- Daftar objek setelah pemasangan: harus 1 function, 1 procedure, 1 trigger.
SELECT ROUTINE_TYPE, ROUTINE_NAME
  FROM information_schema.ROUTINES
 WHERE ROUTINE_SCHEMA = 'projek_pbd_094'
 ORDER BY ROUTINE_TYPE, ROUTINE_NAME;

SELECT TRIGGER_NAME, EVENT_MANIPULATION, EVENT_OBJECT_TABLE
  FROM information_schema.TRIGGERS
 WHERE TRIGGER_SCHEMA = 'projek_pbd_094'
 ORDER BY TRIGGER_NAME;
