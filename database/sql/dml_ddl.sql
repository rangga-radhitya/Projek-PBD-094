-- =====================================================
-- Daily Bun - DDL + DML (database projek_pbd_094)
-- Jalankan SEKALI di HeidiSQL (tab Query), lalu jalankan views.sql
-- Jika database projek_pbd_094 versi lama sudah ada, hapus dulu (data lama ikut hilang):
--   DROP DATABASE projek_pbd_094;
-- =====================================================
CREATE DATABASE IF NOT EXISTS projek_pbd_094 CHARACTER SET utf8mb4;
USE projek_pbd_094;

-- =====================================================
-- DDL : 15 tabel. Kolom ID bersifat AUTO_INCREMENT: saat INSERT, kolom id
--       boleh dihilangkan dan akan terisi otomatis (nomor berikutnya = ID tertinggi + 1).
-- =====================================================
-- 1. role
CREATE TABLE role (
    idrole INT NOT NULL AUTO_INCREMENT,
    nama_role VARCHAR(100),
    PRIMARY KEY (idrole)
);

-- 2. Satuan
CREATE TABLE satuan (
    idsatuan INT NOT NULL AUTO_INCREMENT,
    nama_satuan VARCHAR(45),
    status TINYINT,
    PRIMARY KEY (idsatuan)
);

-- 3. Vendor
CREATE TABLE vendor (
    idvendor INT NOT NULL AUTO_INCREMENT,
    nama_vendor VARCHAR(100),
    badan_hukum CHAR(1),
    status CHAR(1),
    PRIMARY KEY (idvendor)
);

-- 4. user
CREATE TABLE user (
    iduser INT NOT NULL AUTO_INCREMENT,
    username VARCHAR(45),
    password VARCHAR(100),
    idrole INT,
    PRIMARY KEY (iduser),
    CONSTRAINT fk_user_role
        FOREIGN KEY (idrole) REFERENCES role (idrole)
);

-- 5. margin_penjualan
CREATE TABLE margin_penjualan (
    idmargin_penjualan INT NOT NULL AUTO_INCREMENT,
    created_at TIMESTAMP,
    persen DOUBLE,
    status TINYINT,
    iduser INT,
    updated_at TIMESTAMP,
    PRIMARY KEY (idmargin_penjualan),
    CONSTRAINT fk_margin_user
        FOREIGN KEY (iduser) REFERENCES user (iduser)
);

-- 6. barang
CREATE TABLE barang (
    idbarang INT NOT NULL AUTO_INCREMENT,
    jenis CHAR(1),
    nama VARCHAR(45),
    idsatuan INT,
    status TINYINT,
    harga INT,
    PRIMARY KEY (idbarang),
    CONSTRAINT fk_barang_satuan
        FOREIGN KEY (idsatuan) REFERENCES satuan (idsatuan)
);

-- 7. pengadaan
CREATE TABLE pengadaan (
    idpengadaan BIGINT NOT NULL AUTO_INCREMENT,
    timestamp TIMESTAMP,
    user_iduser INT,
    status CHAR(1),
    vendor_idvendor INT,
    subtotal_nilai INT,
    ppn INT,
    total_nilai INT,
    PRIMARY KEY (idpengadaan),
    CONSTRAINT fk_pengadaan_user
        FOREIGN KEY (user_iduser) REFERENCES user (iduser),
    CONSTRAINT fk_pengadaan_vendor
        FOREIGN KEY (vendor_idvendor) REFERENCES vendor (idvendor)
);

-- 8. detail_pengadaan
CREATE TABLE detail_pengadaan (
    iddetail_pengadaan BIGINT NOT NULL AUTO_INCREMENT,
    harga_satuan INT,
    jumlah INT,
    sub_total INT,
    idbarang INT,
    idpengadaan BIGINT,
    PRIMARY KEY (iddetail_pengadaan),
    CONSTRAINT fk_detpengadaan_barang
        FOREIGN KEY (idbarang) REFERENCES barang (idbarang),
    CONSTRAINT fk_detpengadaan_pengadaan
        FOREIGN KEY (idpengadaan) REFERENCES pengadaan (idpengadaan)
);

-- 9. penerimaan
CREATE TABLE penerimaan (
    idpenerimaan BIGINT NOT NULL AUTO_INCREMENT,
    created_at TIMESTAMP,
    status CHAR(1),
    idpengadaan BIGINT,
    iduser INT,
    PRIMARY KEY (idpenerimaan),
    CONSTRAINT fk_penerimaan_pengadaan
        FOREIGN KEY (idpengadaan) REFERENCES pengadaan (idpengadaan),
    CONSTRAINT fk_penerimaan_user
        FOREIGN KEY (iduser) REFERENCES user (iduser)
);

-- 10. detail_penerimaan
CREATE TABLE detail_penerimaan (
    iddetail_penerimaan BIGINT NOT NULL AUTO_INCREMENT,
    idpenerimaan BIGINT,
    barang_idbarang INT,
    jumlah_terima INT,
    harga_satuan_terima INT,
    sub_total_terima INT,
    PRIMARY KEY (iddetail_penerimaan),
    CONSTRAINT fk_detpenerimaan_penerimaan
        FOREIGN KEY (idpenerimaan) REFERENCES penerimaan (idpenerimaan),
    CONSTRAINT fk_detpenerimaan_barang
        FOREIGN KEY (barang_idbarang) REFERENCES barang (idbarang)
);

-- 11. penjualan
CREATE TABLE penjualan (
    idpenjualan INT NOT NULL AUTO_INCREMENT,
    created_at TIMESTAMP,
    subtotal_nilai INT,
    ppn INT,
    total_nilai INT,
    iduser INT,
    idmargin_penjualan INT,
    PRIMARY KEY (idpenjualan),
    CONSTRAINT fk_penjualan_user
        FOREIGN KEY (iduser) REFERENCES user (iduser),
    CONSTRAINT fk_penjualan_margin
        FOREIGN KEY (idmargin_penjualan) REFERENCES margin_penjualan (idmargin_penjualan)
);

-- 12. detail_penjualan
CREATE TABLE detail_penjualan (
    iddetail_penjualan BIGINT NOT NULL AUTO_INCREMENT,
    harga_satuan INT,
    jumlah INT,
    subtotal INT,
    penjualan_idpenjualan INT,
    idbarang INT,
    PRIMARY KEY (iddetail_penjualan),
    CONSTRAINT fk_detpenjualan_penjualan
        FOREIGN KEY (penjualan_idpenjualan) REFERENCES penjualan (idpenjualan),
    CONSTRAINT fk_detpenjualan_barang
        FOREIGN KEY (idbarang) REFERENCES barang (idbarang)
);

-- 13. retur
CREATE TABLE retur (
    idretur BIGINT NOT NULL AUTO_INCREMENT,
    created_at TIMESTAMP,
    idpenerimaan BIGINT,
    iduser INT,
    PRIMARY KEY (idretur),
    CONSTRAINT fk_retur_penerimaan
        FOREIGN KEY (idpenerimaan) REFERENCES penerimaan (idpenerimaan),
    CONSTRAINT fk_retur_user
        FOREIGN KEY (iduser) REFERENCES user (iduser)
);

-- 14. detail_retur
CREATE TABLE detail_retur (
    iddetail_retur INT NOT NULL AUTO_INCREMENT,
    jumlah INT,
    alasan VARCHAR(200),
    idretur BIGINT,
    iddetail_penerimaan BIGINT,
    PRIMARY KEY (iddetail_retur),
    CONSTRAINT fk_detretur_retur
        FOREIGN KEY (idretur) REFERENCES retur (idretur),
    CONSTRAINT fk_detretur_detpenerimaan
        FOREIGN KEY (iddetail_penerimaan) REFERENCES detail_penerimaan (iddetail_penerimaan)
);

-- 15. kartu_stok
CREATE TABLE kartu_stok (
    idkartu_stok BIGINT NOT NULL AUTO_INCREMENT,
    jenis_transaksi CHAR(1),
    masuk INT,
    keluar INT,
    stock INT,
    created_at TIMESTAMP,
    idtransaksi INT,
    idbarang INT,
    PRIMARY KEY (idkartu_stok),
    CONSTRAINT fk_kartustok_barang
        FOREIGN KEY (idbarang) REFERENCES barang (idbarang)
);

-- =====================================================
-- DML : data dummy 6 tabel master (sesuai dokumen)
-- Kolom ID tidak ditulis: terisi otomatis 1, 2, 3, ... (AUTO_INCREMENT).
-- Urutan insert mengikuti foreign key, jadi nomor ID di kolom FK di bawah
-- merujuk urutan baris tabel induknya (user.idrole -> role, margin_penjualan.iduser -> user,
-- barang.idsatuan -> satuan).
-- =====================================================

-- 1. role
INSERT INTO role (nama_role) VALUES
    ('Manajer'),  -- id 1
    ('Admin'),  -- id 2
    ('Kasir'),  -- id 3
    ('Staff Gudang'),  -- id 4
    ('Staff Pembelian');  -- id 5

-- 2. satuan
INSERT INTO satuan (nama_satuan, status) VALUES
    ('Pcs', 1),  -- id 1
    ('Cup', 1),  -- id 2
    ('Box', 1),  -- id 3
    ('Pack', 1),  -- id 4
    ('Set', 1);  -- id 5

-- 3. vendor (badan_hukum: P = PT, C = CV, U = UD; status: A = aktif)
INSERT INTO vendor (nama_vendor, badan_hukum, status) VALUES
    ('PT Roti Nusantara', 'P', 'A'),  -- id 1
    ('CV Bakery Sejahtera', 'C', 'A'),  -- id 2
    ('UD Pastry Mandiri', 'U', 'A'),  -- id 3
    ('PT Minuman Prima', 'P', 'A'),  -- id 4
    ('CV Cake Berkah', 'C', 'A');  -- id 5

-- 4. user (password = hash bcrypt dari password dummy: username tanpa angka + 123,
--    contoh manajer01 -> manajer123. Hanya untuk data uji.)
INSERT INTO user (username, password, idrole) VALUES
    ('manajer01', '$2y$10$aGjV7BoWaU.WpxocBLCw0uU5z29UzlsP6zXCPnTydOpP85SC1iqUS', 1),  -- id 1
    ('admin01', '$2y$10$YFSyiPaYsnBvnwB1ViXshOB4PPssmMSqssg.DeiWv9gPMm7h1BZxq', 2),  -- id 2
    ('kasir01', '$2y$10$kKwYxb5jvOCwrJ3HWWSM3uAuBhOarJjZ2Ej.EMgnqbdvXwu2fU/V2', 3),  -- id 3
    ('gudang01', '$2y$10$hwIE8SU8v2x3XZRK9FZBPeJdVewD3a/1gPHUI/UX1x6n.8uEGnkFu', 4),  -- id 4
    ('supervisor01', '$2y$10$/asoEswD4OiV5GWv0OeYP.G.JQWR.S7oE2BeV/ken2YK6z8gs.v4.', 5);  -- id 5

-- 5. margin_penjualan (hanya 1 margin aktif: 503)
INSERT INTO margin_penjualan (created_at, persen, status, iduser) VALUES
    ('2026-10-08 08:00:00', 10, 0, 2),  -- id 1
    ('2026-10-08 09:00:00', 15, 0, 2),  -- id 2
    ('2026-10-08 10:00:00', 20, 1, 2),  -- id 3
    ('2026-10-08 11:00:00', 25, 0, 1),  -- id 4
    ('2026-10-08 12:00:00', 30, 0, 1);  -- id 5

-- 6. barang (jenis: B = Bread, D = Drink, C = Cake, P = Pastry)
--    harga = harga jual final (margin tidak ditambahkan lagi)
INSERT INTO barang (jenis, nama, idsatuan, status, harga) VALUES
    ('B', 'Coffee Bun', 1, 1, 12000),  -- id 1
    ('B', 'Chocolate Bread', 1, 1, 8000),  -- id 2
    ('D', 'Iced Coffee', 2, 1, 15000),  -- id 3
    ('C', 'Chocolate Cake Slice', 1, 1, 18000),  -- id 4
    ('P', 'Croissant Butter', 1, 1, 14000),  -- id 5
    ('D', 'Matcha Latte', 2, 1, 18000),  -- id 6
    ('C', 'Brownies Slice', 1, 1, 12000),  -- id 7
    ('P', 'Pain au Chocolat', 1, 1, 16000),  -- id 8
    ('B', 'Cheese Bread', 1, 1, 9000),  -- id 9
    ('D', 'Americano', 2, 1, 12000),  -- id 10
    ('B', 'Sausage Bread', 1, 1, 10000),  -- id 11
    ('D', 'Caramel Macchiato', 2, 1, 20000),  -- id 12
    ('C', 'Red Velvet Slice', 1, 1, 20000),  -- id 13
    ('P', 'Almond Croissant', 1, 1, 17000),  -- id 14
    ('B', 'Whole Wheat Bread', 4, 1, 15000),  -- id 15
    ('D', 'Cappuccino', 2, 1, 16000),  -- id 16
    ('C', 'Cheesecake Slice', 1, 1, 22000),  -- id 17
    ('P', 'Danish Pastry', 1, 1, 15000),  -- id 18
    ('B', 'Sugar Donut Box (6 pcs)', 3, 1, 30000),  -- id 19
    ('D', 'Chocolate Latte', 2, 1, 17000),  -- id 20
    ('B', 'Dinner Roll Pack (6 pcs)', 4, 1, 25000),  -- id 21
    ('C', 'Mini Cupcake Pack (6 pcs)', 4, 1, 35000),  -- id 22
    ('P', 'Mini Croissant Pack (4 pcs)', 4, 1, 28000),  -- id 23
    ('D', 'Coffee Tasting Set (3 cups)', 5, 1, 45000),  -- id 24
    ('B', 'Breakfast Bread Set (3 types)', 5, 1, 40000);  -- id 25

-- Cek isi tabel
SELECT * FROM role;
SELECT * FROM satuan;
SELECT * FROM vendor;
SELECT * FROM user;
SELECT * FROM margin_penjualan;
SELECT * FROM barang;
