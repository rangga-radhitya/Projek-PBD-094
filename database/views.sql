USE projek_pbd_094;

-- 1. v_role
CREATE OR REPLACE VIEW v_role AS
SELECT idrole, nama_role
FROM role;

-- 2. v_user (gabung role, kolom password sengaja tidak ikut)
CREATE OR REPLACE VIEW v_user AS
SELECT u.iduser, u.username, u.idrole, r.nama_role
FROM `user` u
JOIN role r ON r.idrole = u.idrole;

-- 3. v_satuan (status angka diubah jadi teks)
CREATE OR REPLACE VIEW v_satuan AS
SELECT idsatuan, nama_satuan, status,
       IF(status = 1, 'Aktif', 'Nonaktif') AS status_teks
FROM satuan;

-- 4. v_vendor (kode badan hukum dan status diubah jadi teks)
CREATE OR REPLACE VIEW v_vendor AS
SELECT idvendor, nama_vendor, badan_hukum,
       CASE badan_hukum WHEN 'P' THEN 'PT' WHEN 'C' THEN 'CV' WHEN 'U' THEN 'UD' ELSE badan_hukum END AS badan_hukum_teks,
       status,
       IF(status = 'A', 'Aktif', 'Nonaktif') AS status_teks
FROM vendor;

-- 5. v_margin_penjualan (gabung user pembuat)
CREATE OR REPLACE VIEW v_margin_penjualan AS
SELECT m.idmargin_penjualan, m.persen, m.status, m.iduser, u.username,
       m.created_at, m.updated_at
FROM margin_penjualan m
JOIN `user` u ON u.iduser = m.iduser;

-- 6. v_barang (gabung satuan, jenis diubah jadi teks)
CREATE OR REPLACE VIEW v_barang AS
SELECT b.idbarang, b.nama, b.jenis,
       CASE b.jenis WHEN 'F' THEN 'Food' WHEN 'D' THEN 'Drink' WHEN 'N' THEN 'Non-Food' ELSE b.jenis END AS jenis_teks,
       b.idsatuan, s.nama_satuan, b.harga, b.status
FROM barang b
JOIN satuan s ON s.idsatuan = b.idsatuan;

SELECT * FROM v_role;
SELECT * FROM v_user;
SELECT * FROM v_satuan;
SELECT * FROM v_vendor;
SELECT * FROM v_margin_penjualan;
SELECT * FROM v_barang;

