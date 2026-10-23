DROP DATABASE projek_pbd_094;
CREATE DATABASE projek_pbd_094;
USE projek_pbd_094;

-- 1. role
CREATE TABLE role (
    idrole INT NOT NULL,
    nama_role VARCHAR(100),
    PRIMARY KEY (idrole)
);

-- 2. Satuan
CREATE TABLE satuan (
    idsatuan INT NOT NULL,
    nama_satuan VARCHAR(45),
    status TINYINT,
    PRIMARY KEY (idsatuan)
);

-- 3. Vendor
CREATE TABLE vendor (
    idvendor INT NOT NULL,
    nama_vendor VARCHAR(100),
    badan_hukum CHAR(1),
    status CHAR(1),
    PRIMARY KEY (idvendor)
);

-- 4. user
CREATE TABLE user (
    iduser INT NOT NULL,
    username VARCHAR(45),
    password VARCHAR(100),
    idrole INT,
    PRIMARY KEY (iduser),
    CONSTRAINT fk_user_role
        FOREIGN KEY (idrole) REFERENCES role (idrole)
);

-- 5. margin_penjualan
CREATE TABLE margin_penjualan (
    idmargin_penjualan INT NOT NULL,
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
    idbarang INT NOT NULL,
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
    idpengadaan BIGINT NOT NULL,
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
    iddetail_pengadaan BIGINT NOT NULL,
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
    idpenerimaan BIGINT NOT NULL,
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
    iddetail_penerimaan BIGINT NOT NULL,
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
    idpenjualan INT NOT NULL,
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
    iddetail_penjualan BIGINT NOT NULL,
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
    idretur BIGINT NOT NULL,
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
    iddetail_retur INT NOT NULL,
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
    idkartu_stok BIGINT NOT NULL,
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

-- DML : data dummy 6 tabel master 

-- 1. role
INSERT INTO role (idrole, nama_role) VALUES
    (101, 'Manajer'),
    (102, 'Admin'),
    (103, 'Kasir'),
    (104, 'Staff Gudang'),
    (105, 'Staff Pembelian');

-- 2. satuan
INSERT INTO satuan (idsatuan, nama_satuan, status) VALUES
    (201, 'Pcs', 1),
    (202, 'Cup', 1),
    (203, 'Box', 1),
    (204, 'Pack', 1),
    (205, 'Set', 1);

-- 3. vendor (badan_hukum: P = PT, C = CV, U = UD; status: A = aktif)
INSERT INTO vendor (idvendor, nama_vendor, badan_hukum, status) VALUES
    (301, 'PT Roti Nusantara', 'P', 'A'),
    (302, 'CV Bakery Sejahtera', 'C', 'A'),
    (303, 'UD Pastry Mandiri', 'U', 'A'),
    (304, 'PT Minuman Prima', 'P', 'A'),
    (305, 'CV Cake Berkah', 'C', 'A');

-- 4. user 
INSERT INTO user (iduser, username, password, idrole) VALUES
    (401, 'manajer', SHA2('manajer123', 256), 101),
    (402, 'admin01', SHA2('admin123', 256), 102),
    (403, 'kasir01', SHA2('kasir123', 256), 103),
    (404, 'staffgudang01', SHA2('gudang123', 256), 104),
    (405, 'staffpembelian01', SHA2('pembelian123', 256), 105);

-- 5. margin_penjualan (hanya 1 margin aktif: 503)
INSERT INTO margin_penjualan (idmargin_penjualan, created_at, persen, status, iduser) VALUES
    (501, '2026-10-08 08:00:00', 10, 0, 402),
    (502, '2026-10-08 09:00:00', 15, 0, 402),
    (503, '2026-10-08 10:00:00', 20, 1, 402),
    (504, '2026-10-08 11:00:00', 25, 0, 401),
    (505, '2026-10-08 12:00:00', 30, 0, 401);

-- 6. barang (jenis: B = Bread, D = Drink, C = Cake, P = Pastry)
--    harga = harga jual final (margin tidak ditambahkan lagi)
INSERT INTO barang (idbarang, jenis, nama, idsatuan, status, harga) VALUES
    (1001, 'B', 'Coffee Bun',              201, 1, 12000),
    (1002, 'B', 'Chocolate Bread',         201, 1,  8000),
    (1003, 'D', 'Iced Coffee',             202, 1, 15000),
    (1004, 'C', 'Chocolate Cake Slice',    201, 1, 18000),
    (1005, 'P', 'Croissant Butter',        201, 1, 14000),
    (1006, 'D', 'Matcha Latte',            202, 1, 18000),
    (1007, 'C', 'Brownies Slice',          201, 1, 12000),
    (1008, 'P', 'Pain au Chocolat',        201, 1, 16000),
    (1009, 'B', 'Cheese Bread',            201, 1,  9000),
    (1010, 'D', 'Americano',               202, 1, 12000),
    (1011, 'B', 'Sausage Bread',           201, 1, 10000),
    (1012, 'D', 'Caramel Macchiato',       202, 1, 20000),
    (1013, 'C', 'Red Velvet Slice',        201, 1, 20000),
    (1014, 'P', 'Almond Croissant',        201, 1, 17000),
    (1015, 'B', 'Whole Wheat Bread',       204, 1, 15000),
    (1016, 'D', 'Cappuccino',              202, 1, 16000),
    (1017, 'C', 'Cheesecake Slice',        201, 1, 22000),
    (1018, 'P', 'Danish Pastry',           201, 1, 15000),
    (1019, 'B', 'Sugar Donut Box (6 pcs)', 203, 1, 30000),
    (1020, 'D', 'Chocolate Latte',         202, 1, 17000),
    (1021, 'B', 'Dinner Roll Pack (6 pcs)',     204, 1, 25000),
    (1022, 'C', 'Mini Cupcake Pack (6 pcs)',    204, 1, 35000),
    (1023, 'P', 'Mini Croissant Pack (4 pcs)',  204, 1, 28000),
    (1024, 'D', 'Coffee Tasting Set (3 cups)',  205, 1, 45000),
    (1025, 'B', 'Breakfast Bread Set (3 types)', 205, 1, 40000);


-- Cek isi tabel
SELECT * FROM role;
SELECT * FROM satuan;
SELECT * FROM vendor;
SELECT * FROM user;
SELECT * FROM margin_penjualan;
SELECT * FROM barang;
