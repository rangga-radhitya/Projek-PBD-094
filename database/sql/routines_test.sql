-- =====================================================
-- Daily Bun - Uji fn_hitung_total_penjualan, sp_laporan_penjualan_periode,
--             trg_detail_penerimaan_after_insert
--
-- JALANKAN HANYA DI DATABASE UJI, bukan database asli:
--   1. Salin dml_ddl.sql, views.sql, routines.sql, ganti nama database 'projek_pbd_094'
--      menjadi 'projek_pbd_094_uji' (cari-ganti), lalu jalankan ketiganya.
--   2. Jalankan file ini. Seluruh data uji dibuat di dalam transaksi dan di-ROLLBACK di akhir,
--      jadi tidak ada data tersisa (nomor AUTO_INCREMENT tetap maju; itu normal di MySQL).
--
-- Kolom 'hasil' bernilai LULUS / GAGAL. Hasil hitung manual tertulis di komentar.
-- Bagian "UJI PENOLAKAN" di akhir sengaja menghasilkan ERROR; jalankan satu per satu
-- (HeidiSQL: blok per blok; klien mysql: opsi --force).
-- =====================================================
USE projek_pbd_094_uji;

-- ---------- A. Uji ROLLBACK trigger (transaksi sendiri) ----------
SET @kartu_awal  = (SELECT COUNT(*) FROM kartu_stok);
SET @detail_awal = (SELECT COUNT(*) FROM detail_penerimaan);
START TRANSACTION;
  INSERT INTO pengadaan (`timestamp`, user_iduser, status, vendor_idvendor, subtotal_nilai, ppn, total_nilai)
       VALUES (NOW(), 5, 'S', 1, 5000, 550, 5550);
  SET @pgd_rb = LAST_INSERT_ID();
  INSERT INTO penerimaan (created_at, status, idpengadaan, iduser) VALUES (NOW(), 'S', @pgd_rb, 4);
  SET @pen_rb = LAST_INSERT_ID();
  INSERT INTO detail_penerimaan (idpenerimaan, barang_idbarang, jumlah_terima, harga_satuan_terima, sub_total_terima)
       VALUES (@pen_rb, 1, 5, 1000, 5000);
  SET @kartu_dalam_tx = (SELECT COUNT(*) FROM kartu_stok);
ROLLBACK;
SELECT 'A. ROLLBACK membatalkan detail + kartu stok' AS uji,
       @kartu_dalam_tx - @kartu_awal                AS kartu_bertambah_dalam_transaksi,   -- harus 1
       (SELECT COUNT(*) FROM kartu_stok) - @kartu_awal       AS kartu_bertambah_setelah_rollback, -- harus 0
       (SELECT COUNT(*) FROM detail_penerimaan) - @detail_awal AS detail_bertambah_setelah_rollback, -- harus 0
       IF(@kartu_dalam_tx - @kartu_awal = 1
          AND (SELECT COUNT(*) FROM kartu_stok) = @kartu_awal
          AND (SELECT COUNT(*) FROM detail_penerimaan) = @detail_awal, 'LULUS', 'GAGAL') AS hasil;

-- ---------- Data uji penjualan (di dalam transaksi utama) ----------
START TRANSACTION;

-- P1: satu detail   -> 12000 x 2                    = 24000
INSERT INTO penjualan (created_at, subtotal_nilai, ppn, total_nilai, iduser, idmargin_penjualan)
     VALUES ('2026-10-10 12:00:00', 24000, 2640, 26640, 3, 3);
SET @p1 = LAST_INSERT_ID();
INSERT INTO detail_penjualan (harga_satuan, jumlah, subtotal, penjualan_idpenjualan, idbarang)
     VALUES (12000, 2, 24000, @p1, 1);

-- P2: tiga detail   -> 12000x1 + 8000x3 + 15000x2   = 12000 + 24000 + 30000 = 66000
INSERT INTO penjualan (created_at, subtotal_nilai, ppn, total_nilai, iduser, idmargin_penjualan)
     VALUES ('2026-10-12 23:59:59', 66000, 7260, 73260, 3, 3);
SET @p2 = LAST_INSERT_ID();
INSERT INTO detail_penjualan (harga_satuan, jumlah, subtotal, penjualan_idpenjualan, idbarang) VALUES
     (12000, 1, 12000, @p2, 1), (8000, 3, 24000, @p2, 2), (15000, 2, 30000, @p2, 3);

-- P3: header tanpa detail -> fungsi 0.00; TIDAK ikut laporan
INSERT INTO penjualan (created_at, subtotal_nilai, ppn, total_nilai, iduser, idmargin_penjualan)
     VALUES ('2026-10-11 09:00:00', 0, 0, 0, 3, 3);
SET @p3 = LAST_INSERT_ID();

-- P4: di luar periode uji (20 Nov) -> 5000
INSERT INTO penjualan (created_at, subtotal_nilai, ppn, total_nilai, iduser, idmargin_penjualan)
     VALUES ('2026-11-20 10:00:00', 5000, 550, 5550, 3, 3);
SET @p4 = LAST_INSERT_ID();
INSERT INTO detail_penjualan (harga_satuan, jumlah, subtotal, penjualan_idpenjualan, idbarang)
     VALUES (5000, 1, 5000, @p4, 2);

-- P5: nilai besar (di atas batas INT) -> 2.000.000.000 x 3 = 6.000.000.000 (membuktikan tanpa overflow)
INSERT INTO penjualan (created_at, subtotal_nilai, ppn, total_nilai, iduser, idmargin_penjualan)
     VALUES ('2026-09-01 08:00:00', NULL, NULL, NULL, 3, 3);
SET @p5 = LAST_INSERT_ID();
INSERT INTO detail_penjualan (harga_satuan, jumlah, subtotal, penjualan_idpenjualan, idbarang)
     VALUES (2000000000, 3, NULL, @p5, 1);

-- ---------- B. Uji FUNCTION ----------
SELECT 'B1. satu detail (12000 x 2)'      AS uji, fn_hitung_total_penjualan(@p1) AS nilai, 24000          AS diharapkan,
       IF(fn_hitung_total_penjualan(@p1) = 24000, 'LULUS', 'GAGAL') AS hasil
UNION ALL
SELECT 'B2. tiga detail (12000+24000+30000)', fn_hitung_total_penjualan(@p2), 66000,
       IF(fn_hitung_total_penjualan(@p2) = 66000, 'LULUS', 'GAGAL')
UNION ALL
SELECT 'B3. tanpa detail -> 0.00',            fn_hitung_total_penjualan(@p3), 0,
       IF(fn_hitung_total_penjualan(@p3) = 0 AND fn_hitung_total_penjualan(@p3) IS NOT NULL, 'LULUS', 'GAGAL')
UNION ALL
SELECT 'B4. transaksi tidak ada -> NULL',     fn_hitung_total_penjualan(999999999), NULL,
       IF(fn_hitung_total_penjualan(999999999) IS NULL, 'LULUS', 'GAGAL')
UNION ALL
SELECT 'B5. parameter NULL -> NULL',          fn_hitung_total_penjualan(NULL), NULL,
       IF(fn_hitung_total_penjualan(NULL) IS NULL, 'LULUS', 'GAGAL')
UNION ALL
SELECT 'B6. nilai besar tanpa overflow (6.000.000.000)', fn_hitung_total_penjualan(@p5), 6000000000,
       IF(fn_hitung_total_penjualan(@p5) = 6000000000, 'LULUS', 'GAGAL');

-- ---------- C. Uji STORED PROCEDURE ----------
-- C1: periode 2026-10-01 s/d 2026-10-31 -> hanya P1 dan P2 (P3 tanpa detail, P4/P5 di luar periode)
--     Diharapkan: rincian 2 baris (P1, P2); rekap jumlah_transaksi=2, total_pendapatan=90000.00,
--                 total_ppn=9900, total_nilai_tercatat=99900
--     Catatan: P2 dibuat pukul 23:59:59 pada 12 Okt -> membuktikan batas akhir hari ikut terhitung.
CALL sp_laporan_penjualan_periode('2026-10-01', '2026-10-31');

-- C2: rentang satu hari saja (12 Okt) -> hanya P2 (66000)
CALL sp_laporan_penjualan_periode('2026-10-12', '2026-10-12');

-- C3: rentang tanpa transaksi -> rincian kosong; rekap 0 transaksi, 0 pendapatan
CALL sp_laporan_penjualan_periode('2025-01-01', '2025-01-31');

-- C4: periode September -> P5 saja (6000000000.00; header NULL -> total_nilai_tercatat 0)
CALL sp_laporan_penjualan_periode('2026-09-01', '2026-09-30');

-- ---------- D. Uji TRIGGER ----------
INSERT INTO pengadaan (`timestamp`, user_iduser, status, vendor_idvendor, subtotal_nilai, ppn, total_nilai)
     VALUES (NOW(), 5, 'S', 1, 0, 0, 0);
SET @pgd = LAST_INSERT_ID();
INSERT INTO penerimaan (created_at, status, idpengadaan, iduser) VALUES (NOW(), 'S', @pgd, 4);
SET @pen = LAST_INSERT_ID();

-- saldo awal tiap barang sebelum penerimaan (0 bila belum ada kartu stok)
SET @s1 = COALESCE((SELECT stock FROM kartu_stok WHERE idbarang = 1 ORDER BY idkartu_stok DESC LIMIT 1), 0);
SET @s2 = COALESCE((SELECT stock FROM kartu_stok WHERE idbarang = 2 ORDER BY idkartu_stok DESC LIMIT 1), 0);
SET @s3 = COALESCE((SELECT stock FROM kartu_stok WHERE idbarang = 3 ORDER BY idkartu_stok DESC LIMIT 1), 0);

-- D1: penerimaan SATU jenis barang (barang 1, 15 pcs)
INSERT INTO detail_penerimaan (idpenerimaan, barang_idbarang, jumlah_terima, harga_satuan_terima, sub_total_terima)
     VALUES (@pen, 1, 15, 5000, 75000);
SELECT 'D1. satu barang: masuk 15, keluar 0, saldo = awal+15, ref = idpenerimaan, jenis M' AS uji,
       k.masuk, k.keluar, k.stock, k.idtransaksi, k.jenis_transaksi,
       IF(k.masuk = 15 AND k.keluar = 0 AND k.stock = @s1 + 15 AND k.idtransaksi = @pen
          AND k.jenis_transaksi = 'M' AND k.idbarang = 1, 'LULUS', 'GAGAL') AS hasil
  FROM kartu_stok k WHERE k.idtransaksi = @pen AND k.idbarang = 1;

-- D2: penerimaan BEBERAPA jenis barang dalam satu INSERT (barang 2 x10, barang 3 x7)
INSERT INTO detail_penerimaan (idpenerimaan, barang_idbarang, jumlah_terima, harga_satuan_terima, sub_total_terima) VALUES
     (@pen, 2, 10, 10000, 100000), (@pen, 3, 7, 5000, 35000);
SELECT CONCAT('D2. barang ', k.idbarang, ' : masuk ', k.masuk, ', saldo ', k.stock) AS uji,
       k.masuk, k.stock,
       IF((k.idbarang = 2 AND k.masuk = 10 AND k.stock = @s2 + 10) OR
          (k.idbarang = 3 AND k.masuk = 7  AND k.stock = @s3 + 7), 'LULUS', 'GAGAL') AS hasil
  FROM kartu_stok k WHERE k.idtransaksi = @pen AND k.idbarang IN (2, 3) ORDER BY k.idbarang;

-- D3: penerimaan kedua untuk barang 1 -> saldo kumulatif = awal + 15 + 5
INSERT INTO detail_penerimaan (idpenerimaan, barang_idbarang, jumlah_terima, harga_satuan_terima, sub_total_terima)
     VALUES (@pen, 1, 5, 5000, 25000);
SELECT 'D3. saldo kumulatif barang 1 = awal + 15 + 5' AS uji,
       (SELECT stock FROM kartu_stok WHERE idbarang = 1 ORDER BY idkartu_stok DESC LIMIT 1) AS saldo_terakhir,
       @s1 + 20 AS diharapkan,
       IF((SELECT stock FROM kartu_stok WHERE idbarang = 1 ORDER BY idkartu_stok DESC LIMIT 1) = @s1 + 20, 'LULUS', 'GAGAL') AS hasil;

-- D4: TIDAK ADA stok ganda -> jumlah baris kartu stok (jenis M) = jumlah detail penerimaan
SELECT 'D4. satu detail = tepat satu baris kartu stok (tanpa ganda)' AS uji,
       (SELECT COUNT(*) FROM detail_penerimaan WHERE idpenerimaan = @pen)                         AS jumlah_detail,
       (SELECT COUNT(*) FROM kartu_stok WHERE idtransaksi = @pen AND jenis_transaksi = 'M')        AS jumlah_kartu,
       IF((SELECT COUNT(*) FROM detail_penerimaan WHERE idpenerimaan = @pen) =
          (SELECT COUNT(*) FROM kartu_stok WHERE idtransaksi = @pen AND jenis_transaksi = 'M'), 'LULUS', 'GAGAL') AS hasil;

-- D5: konsistensi saldo -> untuk tiap barang, stock = SUM(masuk - keluar) dalam kartu stok
SELECT CONCAT('D5. konsistensi saldo barang ', t.idbarang) AS uji, t.saldo_terakhir, t.sum_mutasi,
       IF(t.saldo_terakhir = t.sum_mutasi, 'LULUS', 'GAGAL') AS hasil
  FROM (SELECT b.idbarang,
               (SELECT stock FROM kartu_stok WHERE idbarang = b.idbarang ORDER BY idkartu_stok DESC LIMIT 1) AS saldo_terakhir,
               (SELECT SUM(masuk - keluar) FROM kartu_stok WHERE idbarang = b.idbarang) AS sum_mutasi
          FROM barang b WHERE b.idbarang IN (1, 2, 3)) t;

-- Semua data uji dibatalkan
ROLLBACK;
SELECT 'E. setelah ROLLBACK akhir: tidak ada data uji tersisa' AS uji,
       (SELECT COUNT(*) FROM kartu_stok)        AS kartu_stok,        -- harus sama dengan @kartu_awal
       (SELECT COUNT(*) FROM detail_penerimaan) AS detail_penerimaan, -- harus sama dengan @detail_awal
       IF((SELECT COUNT(*) FROM kartu_stok) = @kartu_awal
          AND (SELECT COUNT(*) FROM detail_penerimaan) = @detail_awal, 'LULUS', 'GAGAL') AS hasil;

-- =====================================================
-- UJI PENOLAKAN (sengaja menghasilkan ERROR). Hapus tanda komentar satu per satu.
-- =====================================================
-- F1. Rentang tidak valid (awal > akhir)      -> ERROR 1644 'Tanggal awal tidak boleh lebih besar dari tanggal akhir.'
-- CALL sp_laporan_penjualan_periode('2026-10-31', '2026-10-01');
-- F2. Parameter kosong                        -> ERROR 1644 'Tanggal awal dan tanggal akhir wajib diisi.'
-- CALL sp_laporan_penjualan_periode(NULL, '2026-10-01');
-- F3. Tanggal tidak ada di kalender           -> ERROR 1292 'Incorrect DATE value' (ditolak MySQL mode strict)
-- CALL sp_laporan_penjualan_periode('2026-02-30', '2026-03-01');
-- F4. jumlah_terima = 0                       -> ERROR 1644 'Detail penerimaan tidak valid ...'; tidak ada kartu stok
-- INSERT INTO detail_penerimaan (idpenerimaan, barang_idbarang, jumlah_terima) VALUES (<idpenerimaan yang ada>, 1, 0);
-- F5. barang NULL                             -> ERROR 1644 yang sama
-- INSERT INTO detail_penerimaan (idpenerimaan, barang_idbarang, jumlah_terima) VALUES (<idpenerimaan yang ada>, NULL, 5);
-- F6. INSERT banyak baris, baris ke-2 tidak valid -> seluruh statement dibatalkan (baris ke-1 dan kartu stoknya juga batal)
-- INSERT INTO detail_penerimaan (idpenerimaan, barang_idbarang, jumlah_terima) VALUES (<id>, 1, 5), (<id>, 2, 0);
