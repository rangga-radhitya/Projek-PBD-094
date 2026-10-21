CREATE DATABASE projek_pbd_094
USE projek_pbd_094;

-- DML 
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

SHOW TABLES;

-- DDL
-- 1. role
INSERT INTO role (idrole, nama_role) VALUES
    (101, 'Manajer'),
    (102, 'Admin'),
    (103, 'Kasir'),
    (104, 'Staff Gudang'),
    (105, 'Pelanggan');

-- 2. user
INSERT INTO user (iduser, username, password, idrole) VALUES
    (201, 'manajer', SHA2('manajer123', 256), 101),
    (202, 'admin01', SHA2('admin123', 256), 102),
    (203, 'kasir01', SHA2('kasir123', 256), 103),
    (204, 'gudang01', SHA2('gudang123', 256), 104);

-- 3. satuan
INSERT INTO satuan (idsatuan, nama_satuan, status) VALUES
    (301, 'Pcs', 1),
    (302, 'Box', 1),
    (303, 'Kg', 1),
    (304, 'Liter', 1),
    (305, 'Lusin', 1),
    (306, 'Karung', 1);

-- 4. vendor
INSERT INTO vendor (idvendor, nama_vendor, badan_hukum, status) VALUES
    (401, 'PT Sumber Makmur', 'P', 'A'),
    (402, 'CV Maju Jaya', 'C', 'A'),
    (403, 'UD Berkah Abadi', 'U', 'A'),

-- 5. margin_penjualan
INSERT INTO margin_penjualan
    (idmargin_penjualan, created_at, persen, status, iduser) VALUES
    (501, '2026-10-08 08:00:00', 10, 0, 202),
    (502, '2026-10-08 09:00:00', 15, 0, 202),
    (503, '2026-10-08 10:00:00', 20, 1, 202),
    (504, '2026-10-08 11:00:00', 25.5, 0, 201),
    (505, '2026-10-08 12:00:00', 30, 0, 201);

-- 6. barang (F = Food, D = Drink, N = Non-Food)
INSERT INTO barang
    (idbarang, jenis, nama, idsatuan, status, harga) VALUES
    (601, 'F', 'Mie Instan Goreng', 301, 1, 3500),
    (602, 'F', 'Beras Premium 5kg', 303, 1, 68000),
    (603, 'D', 'Air Mineral 600ml', 301, 1, 4200),
    (604, 'D', 'Pocari Sweat 500ml', 301, 1, 9200),
    (605, 'N', 'Tisu', 301, 1, 12000),
    (606, 'N', 'Sabun Lifeboy', 301, 1, 3000);
    
SELECT * FROM role;
SELECT * FROM user;
SELECT * FROM satuan;
SELECT * FROM vendor;
SELECT * FROM margin_penjualan;
SELECT * FROM barang;
SELECT * FROM pengadaan;
SELECT * FROM detail_pengadaan;
SELECT * FROM penerimaan;
SELECT * FROM detail_penerimaan;
SELECT * FROM penjualan;
SELECT * FROM detail_penjualan;
SELECT * FROM retur;
SELECT * FROM detail_retur;
SELECT * FROM kartu_stok;