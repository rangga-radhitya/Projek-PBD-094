# DATABASE_OBJECTS_IMPLEMENTATION_REPORT — Daily Bun

Tahap: implementasi **tepat 1 Function, 1 Stored Procedure, dan 1 Trigger** yang dapat didemonstrasikan langsung dari frontend Laravel.

| Objek | Nama | Dipakai di frontend |
|---|---|---|
| FUNCTION | `fn_hitung_subtotal(harga_satuan, jumlah)` | perhitungan sub total saat menyimpan pengadaan |
| PROCEDURE | `sp_aktifkan_margin_penjualan(idmargin)` | tombol **Aktifkan** di `/margin_penjualan` |
| TRIGGER | `trg_detail_penerimaan_after_insert` | otomatis saat detail penerimaan dibuat (stok masuk) |

Tidak ada objek database lain. Skema tabel, kolom, index, dan data tidak diubah.

---

## 1. Function — `fn_hitung_subtotal`

- Menerima dua parameter: harga satuan dan jumlah.
- Mengembalikan hasil kali keduanya sebagai bilangan bulat.
- Jika salah satu parameter kosong atau negatif, hasilnya `0`.
- Tidak memakai percabangan IF: nilai kosong/negatif dianggap 0 memakai CASE sebelum dikalikan.
- Bersifat `DETERMINISTIC` dan `NO SQL`: hasil hanya bergantung pada parameter dan tidak membaca/menulis tabel.

Dipakai di `PengadaanController::tambah()` saat menyimpan setiap baris barang. Nilai hasil function disimpan ke kolom `detail_pengadaan.sub_total`. Setelah semua detail tersimpan, subtotal pengadaan diambil dari jumlah kolom tersebut, lalu:

- PPN dihitung di PHP: `round(subtotal × 11 / 100)`.
- Total = subtotal + PPN.

Tarif tetap `Controller::PPN_PERSEN = 11`.

Function lama `fn_hitung_ppn` sudah dihapus.

## 2. Stored Procedure — `sp_aktifkan_margin_penjualan`

Perilaku:

1. Semua margin yang sedang aktif (`status = 1`) dinonaktifkan, kecuali margin yang dipilih.
2. Margin yang dipilih diaktifkan (`status = 1`) dan `updated_at` diperbarui.

Hasilnya selalu tepat satu margin aktif. Dipanggil `MarginPenjualanController::aktifkan()` saat tombol **Aktifkan** ditekan di `/margin_penjualan`. Tombol dan tampilan tidak berubah.

Fitur tambah dan edit margin tetap ditangani aplikasi seperti sebelumnya.

Procedure lama `sp_hitung_ulang_total_pengadaan` sudah dihapus, sehingga tombol **Hitung Ulang** di detail pengadaan juga dihapus beserta route dan method-nya.

## 3. Trigger — `trg_detail_penerimaan_after_insert`

`AFTER INSERT ON detail_penerimaan`, satu baris `kartu_stok` per detail:

- `jenis_transaksi = 'M'`, `masuk = NEW.jumlah_terima`, `keluar = 0`.
- `stock = saldo terakhir barang + jumlah_terima`.
- `idtransaksi = NEW.idpenerimaan`, `idbarang = NEW.barang_idbarang`.
- Baris `barang` dikunci (`FOR UPDATE`) saat menghitung saldo agar dua penerimaan bersamaan tetap berurutan.
- Saldo kosong dibersihkan menjadi 0 tanpa percabangan IF.

Trigger **tidak melakukan validasi**. Validasi data penerimaan dilakukan aplikasi:

- `PenerimaanController::store()` mewajibkan `jumlah_terima` bilangan bulat dan tidak negatif.
- `PenerimaanController::tambah()` menolak jumlah terima ≤ 0 dan jumlah yang melebihi pesanan.

Konsekuensinya: penulisan langsung ke database tanpa lewat aplikasi tidak divalidasi oleh trigger.

Trigger dipasang pada **detail** penerimaan, bukan header, karena saat header dibuat daftar barang yang diterima belum ada.

## 4. Penghapusan penerimaan

`PenerimaanController::destroy()` melakukan, dalam satu transaksi:

1. Tolak bila penerimaan sudah punya retur.
2. Kelompokkan `detail_penerimaan` per barang.
3. Untuk tiap barang: kunci baris `barang`, ambil saldo terakhir `kartu_stok`, lalu tulis baris `K` (`keluar = total`, `stock = saldo − total`).
4. Hapus `detail_penerimaan` lalu `penerimaan`.

Tidak ada stored procedure untuk penghapusan penerimaan.

## 5. Perhitungan stok di halaman Barang

Function lama `fn_stok_barang` tidak dipakai. Kolom **Stok** di `/barang` dihitung dengan subquery ke baris `kartu_stok` terakhir milik barang tersebut di `BarangController::daftar()`.

## 6. Sumber SQL

- Definisi ketiga objek: `database/sql/routines.sql` (aman dijalankan ulang, memakai `DROP ... IF EXISTS`).
- Skrip uji: `database/sql/routines_test.sql` (dijalankan di database uji, seluruh data uji di-`ROLLBACK`).

## 7. Demonstrasi Lewat Frontend

1. **Function (sub total)** — buka `/pengadaan/create`, isi 1 barang (mis. 10 × Rp10.000). Simpan. Header menyimpan `subtotal_nilai = 100000`, `ppn = 11000`, `total_nilai = 111000`; detail menyimpan `sub_total = 100000`.
2. **Procedure (margin)** — buka `/margin_penjualan`, klik **Aktifkan** pada margin yang masih nonaktif. Margin tersebut menjadi aktif dan margin yang sebelumnya aktif otomatis nonaktif.
3. **Trigger (stok masuk)** — buat penerimaan dari pengadaan. `/kartu_stok` menampilkan baris `M` dengan `stock = saldo + jumlah terima`; kolom Stok di `/barang` ikut berubah.
4. **Hapus penerimaan** — tekan **Hapus** di `/penerimaan`. Baris `K` muncul, stok kembali, header/detail hilang.
5. **Penolakan hapus** — buat retur untuk salah satu detail penerimaan, lalu coba hapus penerimaan. Muncul pesan *“Penerimaan tidak bisa dihapus karena sudah memiliki retur.”* dan data tidak berubah.

## 8. Hasil Uji

### Function

| Uji | Hasil |
|---|---|
| `fn_hitung_subtotal(10000, 10)` | `100000` |
| `fn_hitung_subtotal(12500, 3)` | `37500` |
| `fn_hitung_subtotal(5000, 0)` | `0` |
| `fn_hitung_subtotal(NULL, 5)` | `0` |
| `fn_hitung_subtotal(5000, NULL)` | `0` |
| `fn_hitung_subtotal(-1000, 5)` | `0` |

### Procedure

| Uji | Hasil |
|---|---|
| Aktifkan margin B (A sedang aktif) | B `status = 1`, A `status = 0`, jumlah margin aktif `1` |
| Aktifkan margin A kembali | A `status = 1`, B `status = 0`, jumlah margin aktif `1` |
| Klik **Aktifkan** di `/margin_penjualan` | redirect + pesan sukses, status tersimpan benar |

### Trigger

| Uji | Hasil |
|---|---|
| Insert 1 detail penerimaan (jumlah 4) | baris `M` dengan `masuk = 4`, `stock = saldo + 4` |
| Insert 2 detail penerimaan | jumlah baris `M` sama dengan jumlah detail |
| Konsistensi saldo | saldo terakhir = jumlah seluruh mutasi masuk − keluar |

### Integrasi Laravel → MySQL (data asli)

| Uji | Hasil |
|---|---|
| Simpan pengadaan lewat UI (10 × Rp10.000) | `sub_total = 100000`, `ppn = 11000`, `total = 111000` |
| Simpan penerimaan | baris `kartu_stok` `M`, stok bertambah |
| Hapus penerimaan (versi PHP) | baris `K`, stok kembali, header + detail terhapus |
| Hapus penerimaan bereturn | ditolak, data tetap |
| 10 halaman utama | semua `200` |
| Data uji dibersihkan | pengadaan `5`, detail pengadaan `8`, penerimaan `6`, detail penerimaan `10`, retur `0`, kartu stok `10`, margin `5` |

### Skrip `routines_test.sql`

Berisi 6 uji function (A1–A6), 4 uji trigger (B1–B4), 3 uji procedure (C1–C3), dan 1 uji rollback (D). Kolom `hasil` bernilai `LULUS`/`GAGAL`.

## 9. File yang Berubah

**Diubah**
- `database/sql/routines.sql` — tiga objek final
- `database/sql/routines_test.sql` — uji function, trigger, procedure
- `app/Http/Controllers/PengadaanController.php` — sub total memakai function; PPN dihitung di PHP; `hitungUlang()` dihapus
- `app/Http/Controllers/MarginPenjualanController.php` — `aktifkan()` memanggil stored procedure
- `routes/web.php` — route `pengadaan.hitung_ulang` dihapus
- `resources/views/pengadaan/show.blade.php` — tombol **Hitung Ulang** dihapus
- `resources/views/pengadaan/create.blade.php` — teks bantuan diperbaiki
- `DATABASE_OBJECTS_IMPLEMENTATION_REPORT.md` — dokumen ini

**Dihapus dari database**
- Function `fn_hitung_ppn`
- Procedure `sp_hitung_ulang_total_pengadaan`

## 10. Catatan

1. `kartu_stok.idtransaksi` bertipe `INT` sedangkan `penerimaan.idpenerimaan` `BIGINT`; aman selama id < 2.147.483.647.
2. Trigger hanya menangani `INSERT` detail penerimaan. `UPDATE`/`DELETE` langsung pada detail tidak menyesuaikan stok; koreksi dilakukan lewat retur atau hapus penerimaan.
3. Retur menulis `kartu_stok` dari aplikasi (`ReturController`: `R` saat retur, `M` saat retur dihapus) dan tidak memakai trigger.
4. Kode `jenis_transaksi`: `M` = masuk (trigger penerimaan), `K` = koreksi (hapus penerimaan), `R` = retur (aplikasi).
5. Tidak ada percabangan IF maupun kode error di objek database mana pun. Penolakan data tidak valid dilakukan aplikasi.
6. Melepas objek: hapus trigger, procedure, lalu function. Tombol **Aktifkan** margin akan gagal sampai `routines.sql` dijalankan kembali.
