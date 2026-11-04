-- =====================================================
-- Daily Bun - Objek database untuk CRUD transaksi
-- Jalankan di HeidiSQL (tab Query) SETELAH dml_ddl.sql, views.sql, routines.sql.
-- Aman dijalankan ulang (DROP ... IF EXISTS di awal).
--
-- Isi file:
--   1. sp_tambah_pengadaan            + sp_tambah_detail_pengadaan
--   2. sp_tambah_penerimaan           + sp_tambah_detail_penerimaan
--   3. sp_tambah_retur                + sp_tambah_detail_retur
--   4. trg_detail_retur_after_insert  (stok keluar saat retur)
--   5. sp_hapus_penerimaan            (pembalik stok + hapus)
--   6. sp_hapus_retur                 (pembalik stok + hapus)
--
-- Catatan: trigger trg_detail_penerimaan_after_insert sudah ada di routines.sql
-- dan dipakai apa adanya (stok masuk otomatis saat detail penerimaan disimpan).
-- File ini juga membersihkan dua objek lama yang tidak dipakai aplikasi.
-- =====================================================
USE projek_pbd_094;

DROP TRIGGER   IF EXISTS trg_detail_retur_after_insert;
DROP PROCEDURE IF EXISTS sp_hapus_retur;
DROP PROCEDURE IF EXISTS sp_hapus_penerimaan;
DROP PROCEDURE IF EXISTS sp_tambah_detail_retur;
DROP PROCEDURE IF EXISTS sp_tambah_retur;
DROP PROCEDURE IF EXISTS sp_tambah_detail_penerimaan;
DROP PROCEDURE IF EXISTS sp_tambah_penerimaan;
DROP PROCEDURE IF EXISTS sp_tambah_detail_pengadaan;
DROP PROCEDURE IF EXISTS sp_tambah_pengadaan;

-- Objek lama yang tidak dipakai aplikasi
DROP PROCEDURE IF EXISTS sp_barang_per_jenis;
DROP FUNCTION  IF EXISTS fn_kategori_harga;

DELIMITER $$

-- -----------------------------------------------------
-- 1. Pengadaan
-- -----------------------------------------------------
CREATE PROCEDURE sp_tambah_pengadaan(
    IN p_iduser INT,
    IN p_idvendor INT,
    IN p_status CHAR(1)
)
BEGIN
    INSERT INTO pengadaan (timestamp, user_iduser, status, vendor_idvendor, subtotal_nilai, ppn, total_nilai)
    VALUES (NOW(), p_iduser, p_status, p_idvendor, 0, 0, 0);
END$$

CREATE PROCEDURE sp_tambah_detail_pengadaan(
    IN p_idpengadaan BIGINT,
    IN p_idbarang INT,
    IN p_harga_satuan INT,
    IN p_jumlah INT,
    IN p_ppn_persen INT
)
BEGIN
    DECLARE v_subtotal INT DEFAULT 0;
    DECLARE v_ppn INT DEFAULT 0;

    IF p_jumlah IS NULL OR p_jumlah <= 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Jumlah barang pengadaan harus lebih dari 0.';
    END IF;

    IF p_harga_satuan IS NULL OR p_harga_satuan < 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Harga satuan tidak boleh negatif.';
    END IF;

    INSERT INTO detail_pengadaan (harga_satuan, jumlah, sub_total, idbarang, idpengadaan)
    VALUES (p_harga_satuan, p_jumlah, p_harga_satuan * p_jumlah, p_idbarang, p_idpengadaan);

    SELECT COALESCE(SUM(sub_total), 0) INTO v_subtotal
      FROM detail_pengadaan
     WHERE idpengadaan = p_idpengadaan;

    SET v_ppn = ROUND(v_subtotal * p_ppn_persen / 100);

    UPDATE pengadaan
       SET subtotal_nilai = v_subtotal,
           ppn = v_ppn,
           total_nilai = v_subtotal + v_ppn
     WHERE idpengadaan = p_idpengadaan;
END$$

-- -----------------------------------------------------
-- 2. Penerimaan
-- -----------------------------------------------------
CREATE PROCEDURE sp_tambah_penerimaan(
    IN p_idpengadaan BIGINT,
    IN p_iduser INT,
    IN p_status CHAR(1)
)
BEGIN
    INSERT INTO penerimaan (created_at, status, idpengadaan, iduser)
    VALUES (NOW(), p_status, p_idpengadaan, p_iduser);
END$$

CREATE PROCEDURE sp_tambah_detail_penerimaan(
    IN p_idpenerimaan BIGINT,
    IN p_idbarang INT,
    IN p_jumlah_terima INT,
    IN p_harga_satuan_terima INT
)
BEGIN
    DECLARE v_idpengadaan BIGINT DEFAULT NULL;
    DECLARE v_dipesan INT DEFAULT NULL;
    DECLARE v_diterima INT DEFAULT 0;

    IF p_jumlah_terima IS NULL OR p_jumlah_terima <= 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Jumlah terima harus lebih dari 0.';
    END IF;

    SELECT idpengadaan INTO v_idpengadaan
      FROM penerimaan WHERE idpenerimaan = p_idpenerimaan;

    IF v_idpengadaan IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Penerimaan tidak ditemukan.';
    END IF;

    SELECT jumlah INTO v_dipesan
      FROM detail_pengadaan
     WHERE idpengadaan = v_idpengadaan AND idbarang = p_idbarang
     LIMIT 1;

    SELECT COALESCE(SUM(dp.jumlah_terima), 0) INTO v_diterima
      FROM detail_penerimaan dp
      JOIN penerimaan p ON p.idpenerimaan = dp.idpenerimaan
     WHERE p.idpengadaan = v_idpengadaan AND dp.barang_idbarang = p_idbarang;

    IF v_dipesan IS NULL OR v_diterima + p_jumlah_terima > v_dipesan THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Jumlah terima melebihi jumlah yang dipesan.';
    END IF;

    INSERT INTO detail_penerimaan (idpenerimaan, barang_idbarang, jumlah_terima, harga_satuan_terima, sub_total_terima)
    VALUES (p_idpenerimaan, p_idbarang, p_jumlah_terima, p_harga_satuan_terima, p_harga_satuan_terima * p_jumlah_terima);
END$$

-- -----------------------------------------------------
-- 3. Retur
-- -----------------------------------------------------
CREATE PROCEDURE sp_tambah_retur(
    IN p_idpenerimaan BIGINT,
    IN p_iduser INT
)
BEGIN
    DECLARE v_ada INT DEFAULT 0;

    SELECT COUNT(*) INTO v_ada FROM penerimaan WHERE idpenerimaan = p_idpenerimaan;

    IF v_ada = 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Penerimaan tidak ditemukan.';
    END IF;

    INSERT INTO retur (created_at, idpenerimaan, iduser)
    VALUES (NOW(), p_idpenerimaan, p_iduser);
END$$

CREATE PROCEDURE sp_tambah_detail_retur(
    IN p_idretur BIGINT,
    IN p_iddetail_penerimaan BIGINT,
    IN p_jumlah INT,
    IN p_alasan VARCHAR(200)
)
BEGIN
    DECLARE v_terima INT DEFAULT NULL;
    DECLARE v_idpenerimaan_detail BIGINT DEFAULT NULL;
    DECLARE v_idpenerimaan_retur BIGINT DEFAULT NULL;
    DECLARE v_diretur INT DEFAULT 0;

    IF p_jumlah IS NULL OR p_jumlah <= 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Jumlah retur harus lebih dari 0.';
    END IF;

    SELECT idpenerimaan INTO v_idpenerimaan_retur
      FROM retur WHERE idretur = p_idretur;

    IF v_idpenerimaan_retur IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Retur tidak ditemukan.';
    END IF;

    SELECT jumlah_terima, idpenerimaan INTO v_terima, v_idpenerimaan_detail
      FROM detail_penerimaan
     WHERE iddetail_penerimaan = p_iddetail_penerimaan;

    IF v_terima IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Detail penerimaan tidak ditemukan.';
    END IF;

    IF v_idpenerimaan_detail <> v_idpenerimaan_retur THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Detail penerimaan bukan milik penerimaan pada retur ini.';
    END IF;

    SELECT COALESCE(SUM(jumlah), 0) INTO v_diretur
      FROM detail_retur
     WHERE iddetail_penerimaan = p_iddetail_penerimaan;

    IF v_diretur + p_jumlah > v_terima THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Jumlah retur melebihi jumlah yang diterima.';
    END IF;

    INSERT INTO detail_retur (jumlah, alasan, idretur, iddetail_penerimaan)
    VALUES (p_jumlah, p_alasan, p_idretur, p_iddetail_penerimaan);
END$$

-- -----------------------------------------------------
-- 4. Trigger retur: stok keluar
-- -----------------------------------------------------
CREATE TRIGGER trg_detail_retur_after_insert
AFTER INSERT ON detail_retur
FOR EACH ROW
BEGIN
    DECLARE v_idbarang INT DEFAULT NULL;
    DECLARE v_saldo INT DEFAULT 0;

    SELECT dp.barang_idbarang INTO v_idbarang
      FROM detail_penerimaan dp
     WHERE dp.iddetail_penerimaan = NEW.iddetail_penerimaan;

    IF v_idbarang IS NOT NULL THEN
        SELECT COALESCE(
            (SELECT stock FROM kartu_stok
              WHERE idbarang = v_idbarang
              ORDER BY idkartu_stok DESC LIMIT 1), 0
        ) INTO v_saldo;

        INSERT INTO kartu_stok (jenis_transaksi, masuk, keluar, stock, created_at, idtransaksi, idbarang)
        VALUES ('R', 0, NEW.jumlah, v_saldo - NEW.jumlah, NOW(), NEW.idretur, v_idbarang);
    END IF;
END$$

-- -----------------------------------------------------
-- 5. Hapus penerimaan + pembalik stok
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
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Penerimaan tidak bisa dihapus karena sudah memiliki retur.';
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
-- 6. Hapus retur + pembalik stok
-- -----------------------------------------------------
CREATE PROCEDURE sp_hapus_retur(IN p_idretur BIGINT)
BEGIN
    DECLARE v_idbarang INT DEFAULT NULL;
    DECLARE v_jumlah INT DEFAULT 0;
    DECLARE v_saldo INT DEFAULT 0;
    DECLARE selesai INT DEFAULT 0;

    DECLARE kursor CURSOR FOR
        SELECT dp.barang_idbarang, d.jumlah
          FROM detail_retur d
          JOIN detail_penerimaan dp ON dp.iddetail_penerimaan = d.iddetail_penerimaan
         WHERE d.idretur = p_idretur;

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET selesai = 1;

    OPEN kursor;

    baca: LOOP
        FETCH kursor INTO v_idbarang, v_jumlah;
        IF selesai = 1 THEN
            LEAVE baca;
        END IF;

        SELECT COALESCE(
            (SELECT stock FROM kartu_stok
              WHERE idbarang = v_idbarang
              ORDER BY idkartu_stok DESC LIMIT 1), 0
        ) INTO v_saldo;

        INSERT INTO kartu_stok (jenis_transaksi, masuk, keluar, stock, created_at, idtransaksi, idbarang)
        VALUES ('M', v_jumlah, 0, v_saldo + v_jumlah, NOW(), p_idretur, v_idbarang);
    END LOOP;

    CLOSE kursor;

    DELETE FROM detail_retur WHERE idretur = p_idretur;
    DELETE FROM retur WHERE idretur = p_idretur;
END$$

DELIMITER ;

-- Daftar objek setelah pemasangan
SELECT ROUTINE_TYPE, ROUTINE_NAME
  FROM information_schema.ROUTINES
 WHERE ROUTINE_SCHEMA = 'projek_pbd_094'
 ORDER BY ROUTINE_TYPE, ROUTINE_NAME;

SELECT TRIGGER_NAME, EVENT_MANIPULATION, EVENT_OBJECT_TABLE
  FROM information_schema.TRIGGERS
 WHERE TRIGGER_SCHEMA = 'projek_pbd_094'
 ORDER BY TRIGGER_NAME;
