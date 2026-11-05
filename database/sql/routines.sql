-- =====================================================
-- Daily Bun - Objek database programming
-- Jalankan di HeidiSQL (tab Query) SETELAH dml_ddl.sql dan views.sql.
-- Aman dijalankan ulang (DROP ... IF EXISTS di awal).
--
-- Tepat tiga objek:
--   1. FUNCTION  fn_stok_barang(idbarang)
--        Stok terkini satu barang, dibaca dari baris kartu_stok terakhir.
--        Dipakai halaman Barang (kolom Stok).
--   2. PROCEDURE sp_hapus_penerimaan(idpenerimaan)
--        Hapus penerimaan + detailnya, sekaligus mencatat koreksi stok
--        (kartu_stok jenis 'K'). Menolak bila penerimaan sudah punya retur.
--        Dipakai tombol Hapus di halaman Penerimaan.
--   3. TRIGGER   trg_detail_penerimaan_after_insert
--        Setiap detail penerimaan baru otomatis menambah stok
--        (kartu_stok jenis 'M').
--
-- Catatan: penjualan dan retur TIDAK memakai objek di file ini.
-- =====================================================
USE projek_pbd_094;

DROP TRIGGER   IF EXISTS trg_detail_penerimaan_after_insert;
DROP PROCEDURE IF EXISTS sp_hapus_penerimaan;
DROP FUNCTION  IF EXISTS fn_stok_barang;

DELIMITER $$

-- -----------------------------------------------------
-- 1. FUNCTION fn_stok_barang
--    - Tabel barang tidak punya kolom stok; stok = kolom kartu_stok.stock
--      pada baris terakhir per barang (saldo berjalan).
--    - Mengembalikan 0 bila barang belum punya pergerakan stok.
-- -----------------------------------------------------
CREATE FUNCTION fn_stok_barang(p_idbarang INT)
RETURNS INT
READS SQL DATA
BEGIN
    DECLARE v_stok INT DEFAULT 0;

    SELECT COALESCE(
        (SELECT stock FROM kartu_stok
          WHERE idbarang = p_idbarang
          ORDER BY idkartu_stok DESC LIMIT 1), 0
    ) INTO v_stok;

    RETURN v_stok;
END$$

-- -----------------------------------------------------
-- 2. PROCEDURE sp_hapus_penerimaan
--    - Tolak (SIGNAL 45000) bila penerimaan sudah memiliki retur.
--    - Untuk tiap barang: catat kartu_stok jenis 'K' sebesar total diterima.
--    - Hapus detail_penerimaan lalu penerimaan (satu transaksi dengan pemanggil).
-- -----------------------------------------------------
CREATE PROCEDURE sp_hapus_penerimaan(IN p_idpenerimaan BIGINT)
BEGIN
    DECLARE v_ada_retur INT DEFAULT 0;
    DECLARE v_idbarang INT DEFAULT NULL;
    DECLARE v_total INT DEFAULT 0;
    DECLARE v_saldo INT DEFAULT 0;
    DECLARE selesai INT DEFAULT 0;

    DECLARE kursor CURSOR FOR
        SELECT barang_idbarang, SUM(jumlah_terima)
          FROM detail_penerimaan
         WHERE idpenerimaan = p_idpenerimaan
         GROUP BY barang_idbarang;

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET selesai = 1;

    SELECT COUNT(*) INTO v_ada_retur FROM retur WHERE idpenerimaan = p_idpenerimaan;

    IF v_ada_retur > 0 THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Penerimaan tidak bisa dihapus karena sudah memiliki retur.';
    END IF;

    OPEN kursor;

    baca: LOOP
        FETCH kursor INTO v_idbarang, v_total;
        IF selesai = 1 THEN
            LEAVE baca;
        END IF;

        SELECT COALESCE(
            (SELECT stock FROM kartu_stok
              WHERE idbarang = v_idbarang
              ORDER BY idkartu_stok DESC LIMIT 1), 0
        ) INTO v_saldo;

        INSERT INTO kartu_stok (jenis_transaksi, masuk, keluar, stock, created_at, idtransaksi, idbarang)
        VALUES ('K', 0, v_total, v_saldo - v_total, NOW(), p_idpenerimaan, v_idbarang);
    END LOOP;

    CLOSE kursor;

    DELETE FROM detail_penerimaan WHERE idpenerimaan = p_idpenerimaan;
    DELETE FROM penerimaan WHERE idpenerimaan = p_idpenerimaan;
END$$

-- -----------------------------------------------------
-- 3. TRIGGER trg_detail_penerimaan_after_insert
--    - Stok masuk otomatis saat detail penerimaan dibuat (jenis 'M').
--    - Menolak barang kosong / jumlah_terima <= 0 (SIGNAL 45000), sehingga
--      INSERT detail ikut dibatalkan dan tidak ada stok setengah jadi.
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

-- Daftar objek setelah pemasangan: harus 1 function, 1 procedure, 1 trigger.
SELECT ROUTINE_TYPE, ROUTINE_NAME
  FROM information_schema.ROUTINES
 WHERE ROUTINE_SCHEMA = 'projek_pbd_094'
 ORDER BY ROUTINE_TYPE, ROUTINE_NAME;

SELECT TRIGGER_NAME, EVENT_MANIPULATION, EVENT_OBJECT_TABLE
  FROM information_schema.TRIGGERS
 WHERE TRIGGER_SCHEMA = 'projek_pbd_094'
 ORDER BY TRIGGER_NAME;
