# DATABASE_OBJECTS_IMPLEMENTATION_REPORT — Daily Bun

Tahap: implementasi **tepat 1 Function, 1 Stored Procedure, dan 1 Trigger** yang semuanya dapat didemonstrasikan langsung dari frontend Laravel tanpa data seed.

| Objek | Nama | Dipakai di frontend |
|---|---|---|
| FUNCTION | `fn_stok_barang(idbarang)` | kolom **Stok** pada halaman `/barang` |
| PROCEDURE | `sp_hapus_penerimaan(idpenerimaan)` | tombol **Hapus** pada halaman `/penerimaan` |
| TRIGGER | `trg_detail_penerimaan_after_insert` | otomatis saat detail penerimaan dibuat (stok masuk) |

Tidak ada objek database lain. Skema tabel, kolom, index, dan data tidak diubah.

---

## 1. Function — `fn_stok_barang`

```sql
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
END
```

- Tabel `barang` tidak punya kolom stok; stok = `kartu_stok.stock` pada baris terakhir per barang (saldo berjalan).
- Barang tanpa pergerakan stok atau parameter `NULL` menghasilkan `0`.
- Dibaca oleh `BarangService::daftar()` sebagai `fn_stok_barang(idbarang) AS stok`, ditampilkan di kolom **Stok** (rata kanan, `class="angka"`).

## 2. Stored Procedure — `sp_hapus_penerimaan`

```sql
CREATE PROCEDURE sp_hapus_penerimaan(IN p_idpenerimaan BIGINT)
BEGIN
    -- deklarasi variabel + cursor per barang (GROUP BY barang_idbarang, SUM(jumlah_terima))
    -- CONTINUE HANDLER FOR NOT FOUND

    SELECT COUNT(*) INTO v_ada_retur FROM retur WHERE idpenerimaan = p_idpenerimaan;

    IF v_ada_retur > 0 THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Penerimaan tidak bisa dihapus karena sudah memiliki retur.';
    END IF;

    -- untuk tiap barang: INSERT kartu_stok jenis 'K',
    -- keluar = total diterima, stock = saldo terakhir - total diterima

    DELETE FROM detail_penerimaan WHERE idpenerimaan = p_idpenerimaan;
    DELETE FROM penerimaan        WHERE idpenerimaan = p_idpenerimaan;
END
```

- Menolak penghapusan (`SIGNAL 45000`) bila penerimaan sudah punya retur.
- Mencatat koreksi stok `K` per barang, lalu menghapus detail dan header dalam satu transaksi.
- Dipanggil `PenerimaanService::hapus()` dengan `CALL sp_hapus_penerimaan(?)`. Pesan `45000` diteruskan controller ke UI (`errorInfo[2]`), sehingga pengguna melihat alasan penolakan apa adanya.

## 3. Trigger — `trg_detail_penerimaan_after_insert`

`AFTER INSERT ON detail_penerimaan`, satu baris `kartu_stok` per detail:

- `jenis_transaksi = 'M'`, `masuk = NEW.jumlah_terima`, `keluar = 0`, `stock = saldo + jumlah_terima`, `idtransaksi = NEW.idpenerimaan`.
- Menolak `barang_idbarang NULL` atau `jumlah_terima <= 0` dengan `SIGNAL 45000`, sehingga INSERT detail ikut batal dan tidak ada stok setengah jadi.
- Baris `barang` dikunci (`FOR UPDATE`) saat menghitung saldo agar dua penerimaan bersamaan tetap berurutan.
- Trigger adalah satu-satunya penulis `kartu_stok` untuk penerimaan; aplikasi tidak menulis kartu stok penerimaan agar tidak dobel.

## 4. Sumber SQL

- Definisi ketiga objek: `database/sql/routines.sql` (aman dijalankan ulang, memakai `DROP ... IF EXISTS`).
- Skrip uji: `database/sql/routines_test.sql` (dijalankan di database uji, seluruh data uji di-`ROLLBACK`).
- `READS SQL DATA` membuat function/procedure dapat dibuat pada MySQL dengan binary log aktif tanpa mengubah `log_bin_trust_function_creators`.

## 5. Objek Lama yang Dihapus

| Objek lama | Alasan dihapus | Pengganti |
|---|---|---|
| `fn_hitung_total_penjualan` | Tidak dapat didemokan dari UI (tabel penjualan kosong, tidak ada alur input penjualan) | SQL biasa di `PenjualanService::totalPenjualan()` |
| `sp_laporan_penjualan_periode` | Sama; laporan tetap harus jalan | SQL biasa di `PenjualanService::laporanPeriode()` |
| 8 stored procedure CRUD | Duplikat logika aplikasi, tidak dipakai UI | Tetap di service Laravel |
| `trg_detail_retur_after_insert` | Alur retur sudah menulis stok dari `app/Support/Stok.php` | Logika aplikasi |

Verifikasi database setelah perubahan:

```sql
SELECT ROUTINE_TYPE, ROUTINE_NAME FROM information_schema.ROUTINES
 WHERE ROUTINE_SCHEMA = 'projek_pbd_094'
 ORDER BY ROUTINE_TYPE, ROUTINE_NAME;
-- FUNCTION  fn_stok_barang
-- PROCEDURE sp_hapus_penerimaan

SELECT TRIGGER_NAME FROM information_schema.TRIGGERS
 WHERE TRIGGER_SCHEMA = 'projek_pbd_094';
-- trg_detail_penerimaan_after_insert
```

## 6. Demonstrasi Lewat Frontend

Urutan yang disarankan (semua data transaksi awalnya kosong):

1. **Trigger** — buka `/pengadaan/create`, buat pengadaan (mis. 2 barang) → lalu `/penerimaan/create` dari pengadaan tersebut, isi jumlah terima → simpan. Periksa `/kartu_stok`: muncul baris `M` sebesar jumlah terima untuk tiap barang.
2. **Function** — buka `/barang`. Kolom **Stok** menampilkan saldo hasil `fn_stok_barang(idbarang)`, sama dengan baris terakhir kartu stok.
3. **Procedure** — buka `/penerimaan`, tekan **Hapus** pada penerimaan yang baru dibuat. Header dan detail hilang, `/kartu_stok` bertambah baris `K`, dan kolom **Stok** di `/barang` kembali ke nilai sebelumnya.
4. **Penolakan procedure** — buat penerimaan baru, buat retur untuk salah satu detailnya di `/retur/create`, lalu coba hapus penerimaan tersebut. Muncul pesan *“Penerimaan tidak bisa dihapus karena sudah memiliki retur.”* dan data tidak berubah.
5. **Regresi laporan** — buka `/laporan/penjualan`, isi rentang tanggal, tekan **Cari**. Laporan tetap tampil walau SP/function penjualan sudah dihapus (dihitung dengan SQL biasa).

## 7. Hasil Uji

### `database/sql/routines_test.sql` — 13/13 LULUS

```text
A1. stok awal barang pertama | 0 | 0 | LULUS
A2. barang tanpa pergerakan stok -> 0 | 0 | 0 | LULUS
A3. parameter NULL -> 0 | 0 | 0 | LULUS
B1. trigger menulis kartu_stok M untuk tiap detail | 2 | 2 | LULUS
B2. saldo barang 1 = awal + 15 | 15 | 0 | 15 | LULUS
B2. saldo barang 2 = awal + 7 | 7 | 0 | 7 | LULUS
B3. fn_stok_barang membaca saldo terbaru | 15 | 15 | LULUS
B4. konsistensi saldo barang 1 | 15 | 15 | LULUS
B4. konsistensi saldo barang 2 | 7 | 7 | LULUS
C1. penerimaan tanpa detail terhapus | 0 | 0 | LULUS
C2. kartu_stok K per barang + stok kembali | 2 | 0 | 0 | LULUS
C3. header + detail penerimaan terhapus | 0 | 0 | LULUS
D. setelah ROLLBACK: tidak ada data uji tersisa | 0 | 0 | LULUS
```

### Uji integrasi Laravel → MySQL — 11/11 PASS

Dijalankan dalam satu transaksi database lalu di-`ROLLBACK`, memakai service aplikasi yang sebenarnya:

```text
PASS | function cocok dengan saldo terakhir
PASS | kolom stok dari function tampil di daftar barang | stok=0
PASS | trigger menambah stok | b1=3
PASS | kartu_stok M dibuat trigger
PASS | SP mengembalikan stok saat hapus penerimaan
PASS | SP menulis kartu_stok K per barang
PASS | SP menghapus header penerimaan
PASS | SP menghapus detail penerimaan
PASS | SP menolak hapus penerimaan bereturn | Penerimaan tidak bisa dihapus karena sudah memiliki retur.
PASS | laporan penjualan (SQL biasa) berjalan
PASS | total penjualan transaksi tidak ada -> null
```

## 8. File yang Berubah

**Dibuat**
- `database/sql/routines.sql` — definisi final 3 objek
- `database/sql/routines_test.sql` — skrip uji 13 kasus

**Diubah**
- `app/Services/BarangService.php` — `fn_stok_barang(idbarang) AS stok`
- `app/Services/PenerimaanService.php` — `hapus()` memanggil `sp_hapus_penerimaan`
- `app/Services/PenjualanService.php` — laporan & total penjualan memakai SQL biasa
- `app/Http/Controllers/LaporanController.php` — tanpa penanganan objek hilang, tambah validasi rentang
- `app/Http/Controllers/PenjualanController.php` — `$totalDetail`
- `resources/views/barang/index.blade.php` — kolom **Stok**
- `resources/views/penjualan/show.blade.php` — catatan subtotal dari detail
- `resources/views/laporan/_penjualan.blade.php`, `resources/views/laporan/index.blade.php` — tanpa teks SP lama
- `PROJECT_CONTEXT.md` — dokumentasi objek baru

**Dihapus**
- `database/sql/crud_routines.sql`

**Tidak diubah:** `dml_ddl.sql`, `views.sql`, migration, model, route, konfigurasi, `.env`.

## 9. Catatan

1. `kartu_stok.idtransaksi` bertipe `INT` sedangkan `penerimaan.idpenerimaan` `BIGINT`; aman selama id < 2.147.483.647.
2. Trigger hanya menangani `INSERT` detail penerimaan. `UPDATE`/`DELETE` langsung pada detail tidak menyesuaikan stok; koreksi dilakukan lewat retur atau `sp_hapus_penerimaan`.
3. Retur menulis `kartu_stok` dari aplikasi (`app/Support/Stok.php`: `R` saat retur, `M` saat retur dihapus) dan tidak memakai trigger.
4. Kode `jenis_transaksi`: `M` = masuk (trigger penerimaan), `K` = koreksi (SP hapus penerimaan), `R` = retur (aplikasi).
5. Melepas objek: `DROP TRIGGER trg_detail_penerimaan_after_insert; DROP PROCEDURE sp_hapus_penerimaan; DROP FUNCTION fn_stok_barang;` — halaman Barang dan Penerimaan akan gagal sampai `routines.sql` dijalankan kembali.
