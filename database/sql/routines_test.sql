-- =====================================================
-- Daily Bun - Uji fn_stok_barang, sp_hapus_penerimaan,
--             trg_detail_penerimaan_after_insert
--
-- JALANKAN HANYA DI DATABASE UJI, bukan database asli:
--   1. Salin dml_ddl.sql, views.sql, routines.sql, ganti nama database
--      'projek_pbd_094' menjadi 'projek_pbd_094_uji' (cari-ganti), lalu jalankan.
--   2. Jalankan file ini. Seluruh data uji dibuat di dalam transaksi dan
--      di-ROLLBACK di akhir, jadi tidak ada data tersisa
--      (nomor AUTO_INCREMENT tetap maju; itu normal di MySQL).
--
-- Kolom 'hasil' bernilai LULUS / GAGAL.
-- Bagian "UJI PENOLAKAN" di akhir sengaja menghasilkan ERROR; jalankan satu
-- per satu (HeidiSQL: blok per blok; klien mysql: opsi --force).
-- =====================================================
USE projek_pbd_094_uji;

SET @iduser   = (SELECT iduser FROM `user` ORDER BY iduser LIMIT 1);
SET @idvendor = (SELECT idvendor FROM vendor ORDER BY idvendor LIMIT 1);
SET @b1       = (SELECT idbarang FROM barang ORDER BY idbarang LIMIT 1);
SET @b2       = (SELECT idbarang FROM barang ORDER BY idbarang LIMIT 1 OFFSET 1);

SET @kartu_awal  = (SELECT COUNT(*) FROM kartu_stok);
SET @detail_awal = (SELECT COUNT(*) FROM detail_penerimaan);
SET @awal1       = fn_stok_barang(@b1);
SET @awal2       = fn_stok_barang(@b2);

START TRANSACTION;

-- ---------- A. FUNCTION ----------
SELECT 'A1. stok awal barang pertama' AS uji, @awal1 AS nilai,
       COALESCE((SELECT stock FROM kartu_stok WHERE idbarang = @b1
                  ORDER BY idkartu_stok DESC LIMIT 1), 0) AS diharapkan,
       IF(@awal1 = COALESCE((SELECT stock FROM kartu_stok WHERE idbarang = @b1
                              ORDER BY idkartu_stok DESC LIMIT 1), 0), 'LULUS', 'GAGAL') AS hasil
UNION ALL
SELECT 'A2. barang tanpa pergerakan stok -> 0', fn_stok_barang(999999999), 0,
       IF(fn_stok_barang(999999999) = 0, 'LULUS', 'GAGAL')
UNION ALL
SELECT 'A3. parameter NULL -> 0', fn_stok_barang(NULL), 0,
       IF(fn_stok_barang(NULL) = 0, 'LULUS', 'GAGAL');

-- ---------- B. TRIGGER ----------
INSERT INTO pengadaan (`timestamp`, user_iduser, status, vendor_idvendor, subtotal_nilai, ppn, total_nilai)
     VALUES (NOW(), @iduser, 'S', @idvendor, 0, 0, 0);
SET @pgd = LAST_INSERT_ID();

INSERT INTO penerimaan (created_at, status, idpengadaan, iduser)
     VALUES (NOW(), 'S', @pgd, @iduser);
SET @pen = LAST_INSERT_ID();

INSERT INTO detail_penerimaan (idpenerimaan, barang_idbarang, jumlah_terima, harga_satuan_terima, sub_total_terima)
     VALUES (@pen, @b1, 15, 5000, 75000), (@pen, @b2, 7, 10000, 70000);

SELECT 'B1. trigger menulis kartu_stok M untuk tiap detail' AS uji,
       (SELECT COUNT(*) FROM detail_penerimaan WHERE idpenerimaan = @pen) AS jumlah_detail,
       (SELECT COUNT(*) FROM kartu_stok WHERE idtransaksi = @pen AND jenis_transaksi = 'M') AS jumlah_kartu,
       IF((SELECT COUNT(*) FROM detail_penerimaan WHERE idpenerimaan = @pen) =
          (SELECT COUNT(*) FROM kartu_stok WHERE idtransaksi = @pen AND jenis_transaksi = 'M'), 'LULUS', 'GAGAL') AS hasil;

SELECT CONCAT('B2. saldo barang ', k.idbarang, ' = awal + ', k.masuk) AS uji,
       k.masuk, k.keluar, k.stock,
       IF((k.idbarang = @b1 AND k.masuk = 15 AND k.keluar = 0 AND k.stock = @awal1 + 15) OR
          (k.idbarang = @b2 AND k.masuk = 7  AND k.keluar = 0 AND k.stock = @awal2 + 7), 'LULUS', 'GAGAL') AS hasil
  FROM kartu_stok k
 WHERE k.idtransaksi = @pen AND k.jenis_transaksi = 'M'
 ORDER BY k.idbarang;

SELECT 'B3. fn_stok_barang membaca saldo terbaru' AS uji,
       fn_stok_barang(@b1), @awal1 + 15,
       IF(fn_stok_barang(@b1) = @awal1 + 15 AND fn_stok_barang(@b2) = @awal2 + 7, 'LULUS', 'GAGAL') AS hasil;

SELECT CONCAT('B4. konsistensi saldo barang ', t.idbarang) AS uji, t.saldo_terakhir, t.sum_mutasi,
       IF(t.saldo_terakhir = t.sum_mutasi, 'LULUS', 'GAGAL') AS hasil
  FROM (SELECT b.idbarang,
               fn_stok_barang(b.idbarang) AS saldo_terakhir,
               COALESCE((SELECT SUM(masuk - keluar) FROM kartu_stok WHERE idbarang = b.idbarang), 0) AS sum_mutasi
          FROM barang b WHERE b.idbarang IN (@b1, @b2)) t;

-- ---------- C. STORED PROCEDURE ----------
-- C1: penerimaan tanpa detail -> header saja yang dihapus, tanpa kartu K.
INSERT INTO penerimaan (created_at, status, idpengadaan, iduser)
     VALUES (NOW(), 'S', @pgd, @iduser);
SET @pen_kosong = LAST_INSERT_ID();
CALL sp_hapus_penerimaan(@pen_kosong);
SELECT 'C1. penerimaan tanpa detail terhapus' AS uji,
       (SELECT COUNT(*) FROM penerimaan WHERE idpenerimaan = @pen_kosong) AS sisa_header,
       (SELECT COUNT(*) FROM kartu_stok WHERE idtransaksi = @pen_kosong)  AS sisa_kartu,
       IF((SELECT COUNT(*) FROM penerimaan WHERE idpenerimaan = @pen_kosong) = 0
          AND (SELECT COUNT(*) FROM kartu_stok WHERE idtransaksi = @pen_kosong) = 0, 'LULUS', 'GAGAL') AS hasil;

-- C2: hapus penerimaan berisi 2 barang -> kartu K per barang, stok kembali.
CALL sp_hapus_penerimaan(@pen);

SELECT 'C2. kartu_stok K per barang + stok kembali' AS uji,
       (SELECT COUNT(*) FROM kartu_stok WHERE idtransaksi = @pen AND jenis_transaksi = 'K') AS jumlah_k,
       fn_stok_barang(@b1), @awal1,
       IF((SELECT COUNT(*) FROM kartu_stok WHERE idtransaksi = @pen AND jenis_transaksi = 'K') = 2
          AND fn_stok_barang(@b1) = @awal1
          AND fn_stok_barang(@b2) = @awal2, 'LULUS', 'GAGAL') AS hasil;

SELECT 'C3. header + detail penerimaan terhapus' AS uji,
       (SELECT COUNT(*) FROM penerimaan WHERE idpenerimaan = @pen)        AS sisa_header,
       (SELECT COUNT(*) FROM detail_penerimaan WHERE idpenerimaan = @pen) AS sisa_detail,
       IF((SELECT COUNT(*) FROM penerimaan WHERE idpenerimaan = @pen) = 0
          AND (SELECT COUNT(*) FROM detail_penerimaan WHERE idpenerimaan = @pen) = 0, 'LULUS', 'GAGAL') AS hasil;

ROLLBACK;

-- ---------- D. ROLLBACK ----------
SELECT 'D. setelah ROLLBACK: tidak ada data uji tersisa' AS uji,
       (SELECT COUNT(*) FROM kartu_stok)        AS kartu_stok,
       (SELECT COUNT(*) FROM detail_penerimaan) AS detail_penerimaan,
       IF((SELECT COUNT(*) FROM kartu_stok) = @kartu_awal
          AND (SELECT COUNT(*) FROM detail_penerimaan) = @detail_awal, 'LULUS', 'GAGAL') AS hasil;

-- =====================================================
-- UJI PENOLAKAN (sengaja menghasilkan ERROR). Hapus tanda komentar satu per satu.
-- =====================================================
-- E1. Hapus penerimaan yang sudah punya retur
--     -> ERROR 1644 'Penerimaan tidak bisa dihapus karena sudah memiliki retur.'
-- START TRANSACTION;
--   INSERT INTO pengadaan (`timestamp`, user_iduser, status, vendor_idvendor, subtotal_nilai, ppn, total_nilai)
--        VALUES (NOW(), @iduser, 'S', @idvendor, 0, 0, 0);
--   SET @pgd2 = LAST_INSERT_ID();
--   INSERT INTO penerimaan (created_at, status, idpengadaan, iduser) VALUES (NOW(), 'S', @pgd2, @iduser);
--   SET @pen2 = LAST_INSERT_ID();
--   INSERT INTO detail_penerimaan (idpenerimaan, barang_idbarang, jumlah_terima, harga_satuan_terima, sub_total_terima)
--        VALUES (@pen2, @b1, 5, 1000, 5000);
--   SET @det2 = LAST_INSERT_ID();
--   INSERT INTO retur (created_at, idpenerimaan, iduser) VALUES (NOW(), @pen2, @iduser);
--   SET @retur2 = LAST_INSERT_ID();
--   INSERT INTO detail_retur (jumlah, alasan, idretur, iddetail_penerimaan)
--        VALUES (1, 'uji', @retur2, @det2);
--   CALL sp_hapus_penerimaan(@pen2);
-- ROLLBACK;

-- E2. Detail penerimaan tidak valid
--     -> ERROR 1644 'Detail penerimaan tidak valid ...'; tidak ada kartu stok
-- START TRANSACTION;
--   INSERT INTO detail_penerimaan (idpenerimaan, barang_idbarang, jumlah_terima)
--        VALUES (@pen, @b1, 0);
-- ROLLBACK;
