# RotiKita Bakery (Laravel) - tampilan data 6 tabel master

Versi Laravel dari proyek PHP native sebelumnya. **Belum ada CRUD**: setiap halaman
hanya menampilkan data dari **VIEW** MySQL (read-only), seperti versi sebelumnya.

## Isi paket
Ini berisi file yang ditambahkan ke proyek Laravel baru (bukan seluruh proyek,
karena folder `vendor/` dibuat oleh Composer).

```
database/sql/dml_ddl.sql   15 tabel (DDL) + data dummy 6 tabel master (DML)
database/sql/views.sql     6 view (v_role, v_user, v_satuan, v_vendor, v_margin_penjualan, v_barang)
app/Models/                Role, Pengguna, Satuan, Vendor, MarginPenjualan, Barang  -> membaca view
app/Http/Controllers/      satu controller per halaman (method index saja)
routes/web.php             6 route GET; "/" diarahkan ke /role
resources/views/           layout + 6 halaman Blade
public/css/style.css       tampilan
.env.contoh                baris yang perlu diubah di .env
```

## Cara memasang (Laragon, Windows)

**1. Siapkan database (HeidiSQL)**
- Kalau database `projek_pbd_094` versi lama sudah ada, hapus dulu: `DROP DATABASE projek_pbd_094;`
- Jalankan `database/sql/dml_ddl.sql`, lalu `database/sql/views.sql` (urutannya harus begitu).

**2. Buat proyek Laravel baru** (Terminal Laragon, di `C:\laragon\www`)
```
composer create-project laravel/laravel rotikita
```
Laravel 13 butuh PHP 8.3+. Kalau `php -v` masih 8.2, pakai `laravel/laravel:^12.0`.
Pakai folder baru supaya tidak bercampur dengan proyek PHP native lama.

**3. Salin file paket ini** ke dalam folder `rotikita`, gabungkan folder dan pilih
*Replace* kalau diminta (`routes/web.php` akan menggantikan bawaan).

**4. Atur `.env`** di folder `rotikita` sesuai `.env.contoh`.

**5. Jalankan**
```
cd rotikita
php artisan config:clear
php artisan serve
```
Buka http://127.0.0.1:8000 (otomatis ke halaman Role).
Jangan menjalankan `php artisan migrate`; semua tabel sudah dibuat dari `dml_ddl.sql`.

## Data dummy
| Tabel | Isi |
|---|---|
| role | 1-5: Manajer, Admin, Kasir, Staff Gudang, Staff Pembelian |
| satuan | 1-5: Pcs, Cup, Box, Pack, Set |
| vendor | 101-105 (P = PT, C = CV, U = UD; status A = aktif) |
| user | 201-205, password tersimpan sebagai hash bcrypt |
| margin_penjualan | 501-505, hanya 503 yang aktif |
| barang | 1001-1010, jenis B = Bread, D = Drink, C = Cake, P = Pastry |

Password dummy (hanya data uji): `manajer123`, `admin123`, `kasir123`, `gudang123`, `supervisor123`
untuk manajer01, admin01, kasir01, gudang01, supervisor01.

## Catatan
- Model user bernama `Pengguna` agar tidak bentrok dengan `App\Models\User` bawaan Laravel.
- View memakai `JOIN`/`CASE`, jadi tidak bisa diisi langsung. Saat CRUD dibuat nanti,
  tulis ke tabel aslinya, bukan ke view.
- Kolom `password` sengaja tidak ada di `v_user`.
