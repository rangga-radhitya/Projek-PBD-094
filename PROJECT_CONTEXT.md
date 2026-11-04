# PROJECT_CONTEXT — Project Bakery (nama aplikasi: Daily Bun)

> Dokumen ini hasil audit read-only terhadap isi `Project_Bakery.zip` (diaudit 2026-11-03).
> Sumber kebenaran: file di repo (`database/sql/*.sql`, `app/`, `routes/`, `resources/`, `config/`).
> **Database MySQL yang sedang berjalan TIDAK diperiksa** (tidak terjangkau dari lingkungan audit). Seluruh
> informasi database berasal dari `database/sql/dml_ddl.sql` dan `database/sql/views.sql`.
> Tidak ada kredensial `.env` yang dicantumkan. Hal yang butuh konfirmasi ditandai **[KONFIRMASI]**.

---

## 1. Identitas Project

| Item | Nilai (sesuai temuan) |
|---|---|
| Nama aplikasi | **Daily Bun** (diputuskan pemilik project; `APP_NAME`, fallback layout, README, header SQL sudah diseragamkan) |
| Tujuan | Pengelolaan toko roti: master data, pengadaan, penerimaan, persediaan, penjualan, retur |
| Framework | Laravel 13 (`laravel/framework ^13.17`), PHP `^8.3` |
| Database | MySQL, nama database `projek_pbd_094` (dari `.env` & SQL) |
| Konteks | Tugas/proyek mata kuliah Pemrograman Basis Data (README menyebut "Modul 8 - View", "sesuai dokumen") |
| Lingkungan dev | Laragon (Windows), HeidiSQL (menurut README) |
| Repo git | Ada, branch `main`, 7 commit; riwayat: PHP native → "Frontend CUD 6 tabel master" → "Migrasi proyek ke Laravel" |
| Perubahan belum di-commit | `database/sql/dml_ddl.sql` (modifikasi: kolom ID jadi `AUTO_INCREMENT`, header diganti) dan `README.md` (untracked) |

## 2. Teknologi & Dependency

**Backend (composer.json)**: `laravel/framework ^13.17`, `laravel/tinker ^3.0`.
Dev: `fakerphp/faker`, `laravel/pail`, `laravel/pao`, `laravel/pint`, `mockery`, `nunomaduro/collision`, `phpunit ^12.5`.

**Frontend**:
- Yang **benar-benar dipakai**: Blade + satu file CSS statis `public/css/style.css` (tanpa JS, tanpa framework CSS).
- Yang **terpasang tetapi tidak dipakai**: Vite 8, Tailwind 4, `laravel-vite-plugin` (`package.json`, `vite.config.js`, `resources/css/app.css`, `resources/js/app.js` yang isinya hanya komentar `//`). Layout tidak memanggil `@vite`.
- Font: tidak ada font web yang dimuat (`Segoe UI`/system-ui di CSS).

**Konfigurasi runtime (nilai non-sensitif)**: `APP_ENV=local`, `APP_DEBUG=true`, `APP_LOCALE=id`, `DB_CONNECTION=mysql`, `SESSION_DRIVER=file`, `CACHE_STORE=file`, `QUEUE_CONNECTION=sync`. `config/app.php` timezone = `UTC` (belum Asia/Jakarta).

## 3. Struktur Folder (tanpa vendor/, node_modules/, .git/)

```
Project_Bakery/
├── app/
│   ├── Http/Controllers/   Controller (abstract) + 6 controller (hanya method index)
│   ├── Models/             6 model read-only atas VIEW + User bawaan Laravel (tidak dipakai)
│   └── Providers/          AppServiceProvider (kosong)
├── bootstrap/app.php       routing web + health '/up'; middleware kosong
├── config/                 config bawaan Laravel (auth masih default 'users')
├── database/
│   ├── sql/dml_ddl.sql     DDL 15 tabel + DML dummy 6 tabel master   <-- sumber skema sebenarnya
│   ├── sql/views.sql       6 VIEW                                      <-- sumber view sebenarnya
│   ├── migrations/         3 migrasi bawaan Laravel (users, cache, jobs) — TIDAK terkait skema bakery
│   ├── seeders/            DatabaseSeeder bawaan (membuat 'Test User', tidak cocok dengan skema)
│   ├── factories/          UserFactory bawaan
│   └── database.sqlite     SQLite bawaan Laravel (berisi tabel bawaan saja; tidak dipakai .env)
├── public/css/style.css    satu-satunya stylesheet yang dipakai
├── resources/
│   ├── views/layouts/app.blade.php   layout + navbar (daftar menu hard-coded)
│   ├── views/{role,user,satuan,vendor,margin_penjualan,barang}/index.blade.php
│   ├── views/welcome.blade.php       halaman bawaan Laravel, tidak ada route
│   └── css/app.css, js/app.js        Tailwind/Vite — tidak dipakai
├── routes/web.php          7 route (1 redirect + 6 GET)
├── storage/                log, cache view, session file
├── tests/                  hanya ExampleTest bawaan (Feature & Unit)
├── AGENTS.md / CLAUDE.md   identik; berisi bootstrap "Laravel Boost" (BUKAN dokumentasi project)
└── README.md               panduan instalasi (sebagian sudah usang, lihat §11)
```

## 4. Struktur Database (dari `dml_ddl.sql`)

Semua tabel: engine default MySQL (InnoDB), charset database `utf8mb4`. Semua kolom bertipe nullable kecuali PK. Seluruh PK `AUTO_INCREMENT`.

### 4.1 Master data

| Tabel | Kolom (tipe) | PK | FK |
|---|---|---|---|
| `role` | idrole INT, nama_role VARCHAR(100) | idrole | – |
| `user` | iduser INT, username VARCHAR(45), password VARCHAR(100), idrole INT | iduser | idrole → role |
| `satuan` | idsatuan INT, nama_satuan VARCHAR(45), status TINYINT | idsatuan | – |
| `vendor` | idvendor INT, nama_vendor VARCHAR(100), badan_hukum CHAR(1), status CHAR(1) | idvendor | – |
| `margin_penjualan` | idmargin_penjualan INT, created_at TIMESTAMP, persen DOUBLE, status TINYINT, iduser INT, updated_at TIMESTAMP | idmargin_penjualan | iduser → user |
| `barang` | idbarang INT, jenis CHAR(1), nama VARCHAR(45), idsatuan INT, status TINYINT, harga INT | idbarang | idsatuan → satuan |

### 4.2 Transaksi

| Tabel | Kolom (tipe) | PK | FK |
|---|---|---|---|
| `pengadaan` | idpengadaan BIGINT, `timestamp` TIMESTAMP, user_iduser INT, status CHAR(1), vendor_idvendor INT, subtotal_nilai INT, ppn INT, total_nilai INT | idpengadaan | user_iduser → user; vendor_idvendor → vendor |
| `detail_pengadaan` | iddetail_pengadaan BIGINT, harga_satuan INT, jumlah INT, sub_total INT, idbarang INT, idpengadaan BIGINT | iddetail_pengadaan | idbarang → barang; idpengadaan → pengadaan |
| `penerimaan` | idpenerimaan BIGINT, created_at TIMESTAMP, status CHAR(1), idpengadaan BIGINT, iduser INT | idpenerimaan | idpengadaan → pengadaan; iduser → user |
| `detail_penerimaan` | iddetail_penerimaan BIGINT, idpenerimaan BIGINT, barang_idbarang INT, jumlah_terima INT, harga_satuan_terima INT, sub_total_terima INT | iddetail_penerimaan | idpenerimaan → penerimaan; barang_idbarang → barang |
| `penjualan` | idpenjualan INT, created_at TIMESTAMP, subtotal_nilai INT, ppn INT, total_nilai INT, iduser INT, idmargin_penjualan INT | idpenjualan | iduser → user; idmargin_penjualan → margin_penjualan |
| `detail_penjualan` | iddetail_penjualan BIGINT, harga_satuan INT, jumlah INT, subtotal INT, penjualan_idpenjualan INT, idbarang INT | iddetail_penjualan | penjualan_idpenjualan → penjualan; idbarang → barang |
| `retur` | idretur BIGINT, created_at TIMESTAMP, idpenerimaan BIGINT, iduser INT | idretur | idpenerimaan → penerimaan; iduser → user |
| `detail_retur` | iddetail_retur INT, jumlah INT, alasan VARCHAR(200), idretur BIGINT, iddetail_penerimaan BIGINT | iddetail_retur | idretur → retur; iddetail_penerimaan → detail_penerimaan |
| `kartu_stok` | idkartu_stok BIGINT, jenis_transaksi CHAR(1), masuk INT, keluar INT, stock INT, created_at TIMESTAMP, idtransaksi INT, idbarang INT | idkartu_stok | idbarang → barang. **`idtransaksi` tidak punya FK** |

### 4.3 Diagram relasi (teks)

```
role 1─∞ user
user 1─∞ margin_penjualan, pengadaan, penerimaan, penjualan, retur
satuan 1─∞ barang
vendor 1─∞ pengadaan
pengadaan 1─∞ detail_pengadaan ∞─1 barang
pengadaan 1─∞ penerimaan 1─∞ detail_penerimaan ∞─1 barang
penerimaan 1─∞ retur 1─∞ detail_retur ∞─1 detail_penerimaan
margin_penjualan 1─∞ penjualan 1─∞ detail_penjualan ∞─1 barang
barang 1─∞ kartu_stok          (idtransaksi = referensi longgar tanpa FK)
```

### 4.4 Data dummy (DML)

Hanya 6 tabel master yang berisi: role 5 baris, satuan 5, vendor 5, user 5, margin_penjualan 5 (hanya id 3 berstatus aktif = 20%), barang **25** baris (id 1–25). 9 tabel transaksi **kosong**.
Catatan: README menyebut ID `101–105`, `201–205`, `503`, `1001–1010` — **tidak sesuai** dengan SQL saat ini (ID mulai dari 1, barang 25 baris). README usang.

### 4.5 VIEW (dari `views.sql`) — 6 view, semuanya untuk tabel master

| View | Isi |
|---|---|
| `v_role` | idrole, nama_role |
| `v_user` | iduser, username, idrole, nama_role (INNER JOIN role; kolom password sengaja tidak ada) |
| `v_satuan` | + status_teks (Aktif/Nonaktif) |
| `v_vendor` | + badan_hukum_teks (P→PT, C→CV, U→UD), status_teks |
| `v_margin_penjualan` | persen, status, iduser, username, created_at, updated_at (INNER JOIN user) |
| `v_barang` | nama, jenis, jenis_teks (B/D/C/P → Bread/Drink/Cake/Pastry), idsatuan, nama_satuan, harga, status (INNER JOIN satuan) |

Catatan teknis: view memakai INNER JOIN, sehingga baris dengan FK `NULL` (semua FK nullable) akan **hilang dari tampilan**.

### 4.6 Stored Procedure, Function, Trigger

`dml_ddl.sql` dan `views.sql` tidak memuat satu pun (audit awal: nol hasil). Sejak tahap database programming, `database/sql/routines.sql` memuat **tepat tiga objek**: Function `fn_hitung_total_penjualan`, Stored Procedure `sp_laporan_penjualan_periode`, dan Trigger `trg_detail_penerimaan_after_insert` (detail: `DATABASE_OBJECTS_IMPLEMENTATION_REPORT.md`). Skema tabel tidak diubah; tidak ada index tambahan, UNIQUE, CHECK, maupun DEFAULT.

## 5. Arsitektur Laravel

**Routes (`routes/web.php`)** — 7 route, semua tanpa middleware:

| Method | URI | Handler | Nama |
|---|---|---|---|
| GET | `/` | redirect → `/role` | – |
| GET | `/role` | RoleController@index | role |
| GET | `/user` | UserController@index | user |
| GET | `/satuan` | SatuanController@index | satuan |
| GET | `/vendor` | VendorController@index | vendor |
| GET | `/margin_penjualan` | MarginPenjualanController@index | margin_penjualan |
| GET | `/barang` | BarangController@index | barang |

**Models** (semua `$timestamps=false`, `$incrementing=false`, tanpa `$fillable`, tanpa relasi):

| Model | Menunjuk ke | Catatan |
|---|---|---|
| Role | VIEW `v_role` | read-only secara praktik |
| Pengguna | VIEW `v_user` | tidak bisa dipakai untuk auth (tanpa password) |
| Satuan | VIEW `v_satuan` | |
| Vendor | VIEW `v_vendor` | |
| MarginPenjualan | VIEW `v_margin_penjualan` | cast persen/created_at/updated_at |
| Barang | VIEW `v_barang` | |
| User | tabel `users` (bawaan Laravel) | **tidak terkait** skema bakery; tidak dipakai |

**Controllers**: 6 controller, masing-masing hanya `index()` → `Model::orderBy(pk)->get()` → `view('x.index', compact('daftar'))`. Tidak ada pagination, pencarian, validasi, Form Request, atau Policy.

**Middleware**: tidak ada kustom (`bootstrap/app.php` kosong). **Auth**: belum ada (tidak ada login/logout/guard); `config/auth.php` masih menunjuk `App\Models\User` / tabel `users`.

**Validasi**: belum ada (belum ada form).

## 6. Kondisi Frontend

- Layout `layouts/app.blade.php`: header + navbar horizontal dari array `$menu` di dalam Blade (6 item). Tidak ada sidebar, footer, flash message, atau slot untuk aksi (tombol tambah/ubah).
- 6 halaman `index.blade.php`: tabel read-only, state kosong (`@forelse`), badge status. Konsisten satu sama lain; tanpa JS.
- CSS: palet cokelat/krem, responsif dasar (`overflow-x:auto` pada tabel, nav `flex-wrap`), `:focus-visible`. Tidak ada gaya untuk form, tombol, alert, pagination, modal, kartu dashboard.
- **Tidak ada**: dashboard (root redirect ke `/role`), halaman untuk 9 tabel transaksi, halaman untuk 4 view/tabel yang belum ada (lihat DEVELOPMENT_PLAN), halaman login, laporan.
- `welcome.blade.php` (bawaan) tidak punya route dan tidak dipakai.

## 7. Fitur yang Sudah Tersedia

1. Tampilan read-only 6 master (role, user, satuan, vendor, margin_penjualan, barang) dari VIEW.
2. Navigasi antar 6 halaman + penanda menu aktif.
3. Format Rupiah, persen, tanggal (`d/m/Y H:i`).
4. DDL lengkap 15 tabel dengan PK/FK + data dummy master + 6 view.

## 8. Fitur yang Belum Tersedia

- CRUD apa pun (tambah/ubah/hapus/nonaktifkan) pada master data.
- Autentikasi, otorisasi per role, manajemen sesi login.
- Seluruh proses transaksi: pengadaan, penerimaan, penjualan, retur.
- Persediaan (kartu stok / stok saat ini), laporan, dashboard.
- ~~Stored Procedure, Function, Trigger~~ — sudah ada 1/1/1 di `database/sql/routines.sql` (perlu dijalankan di database setelah `dml_ddl.sql` dan `views.sql`).
- View untuk tabel transaksi, pagination, pencarian, flash message, validasi.
- Test otomatis nyata (hanya ExampleTest bawaan).

## 9. Analisis Alur Bisnis (berdasarkan struktur DB saja)

Yang **didukung** struktur:
1. **Master** → `barang` (jenis, satuan, harga jual), `vendor`, `margin_penjualan`, `user`/`role`.
2. **Pengadaan** → header `pengadaan` (vendor, user, status, subtotal/ppn/total) + `detail_pengadaan` (barang, harga_satuan, jumlah, sub_total).
3. **Penerimaan** → `penerimaan` mengacu **satu** `pengadaan`; `detail_penerimaan` mencatat jumlah & harga terima per barang. Satu pengadaan secara struktur boleh punya banyak penerimaan (penerimaan parsial) — tidak ada kolom sisa/outstanding, harus dihitung.
4. **Persediaan** → `kartu_stok` per barang dengan kolom `masuk`, `keluar`, `stock` (saldo berjalan), `jenis_transaksi`, `idtransaksi`. **Tidak ada kolom stok di `barang`**; stok saat ini = `stock` baris terakhir per barang (atau SUM masuk−keluar).
5. **Penjualan** → `penjualan` + `detail_penjualan`; header mengacu `margin_penjualan` (baris margin yang berlaku saat transaksi) dan `user` (kasir).
6. **Retur** → `retur` mengacu `penerimaan`; `detail_retur` mengacu `detail_penerimaan` (barang yang diretur dari penerimaan tertentu) + jumlah + alasan. Struktur ini adalah **retur pembelian (ke vendor)**, bukan retur penjualan.

Yang **tidak didukung / ambigu** — **[KONFIRMASI]** sebelum implementasi:
- Arti kode `pengadaan.status`, `penerimaan.status` (CHAR(1) tanpa definisi; tidak ada data contoh).
- Arti kode `kartu_stok.jenis_transaksi` (mis. penerimaan/penjualan/retur?) dan sumber `idtransaksi` (polimorfik tanpa FK; tipe INT padahal id penerimaan/retur BIGINT).
- Peran `margin_penjualan` vs `barang.harga`. Komentar di SQL: *"harga = harga jual final (margin tidak ditambahkan lagi)"*. Lalu untuk apa `penjualan.idmargin_penjualan`? Apakah margin dipakai untuk menghitung harga jual dari harga beli, atau hanya dicatat?
- Rumus `ppn` (persentase berapa?) — tidak ada tabel/kolom tarif PPN.
- Apakah retur mengurangi stok (keluar) dan apakah ada nilai/kredit retur? Tabel retur tidak punya kolom nilai/status.
- Apakah retur penjualan (pelanggan) diperlukan? Tidak ada struktur pendukungnya.
- Apakah stok boleh negatif / penjualan ditolak bila stok kurang?
- Siapa (role) yang boleh melakukan tiap proses.
- `barang` tidak punya harga beli; harga beli hanya ada di `detail_pengadaan`/`detail_penerimaan`.
- Apakah `iduser` penerimaan/retur boleh berbeda dari pembuat pengadaan.

## 10. Daftar Masalah & Risiko (ringkas; rincian prioritas ada di DEVELOPMENT_PLAN.md §2)

**Kritis**
1. Konsistensi stok **untuk penerimaan** kini dijaga trigger `trg_detail_penerimaan_after_insert` (satu-satunya penulis `kartu_stok` untuk penerimaan; Laravel tidak boleh ikut menulis). Penjualan dan retur belum mengurangi stok — mekanismenya belum ada.
2. Auth tidak ada, dan `config/auth.php` tidak cocok dengan tabel `user` (iduser/username/password) — semua halaman terbuka tanpa login.
3. Model hanya menunjuk VIEW; tidak bisa dipakai untuk INSERT/UPDATE/DELETE (view memakai JOIN/IF/CASE).

**Tinggi**
4. Semua kolom nullable, tanpa UNIQUE/CHECK/DEFAULT: data kotor mudah masuk (username ganda, jumlah negatif, FK NULL). View INNER JOIN akan menyembunyikan baris ber-FK NULL.
5. `kartu_stok.idtransaksi` INT tanpa FK dan lebih sempit dari id sumber BIGINT.
6. `TIMESTAMP` tanpa DEFAULT: perilaku `created_at/updated_at` bergantung pada versi/mode server MySQL/MariaDB (otomatis vs NULL). Controller/Model tidak mengisi timestamp (`$timestamps=false`).
7. Timezone aplikasi `UTC`, sedangkan pengguna di WIB.
8. `composer setup` menjalankan `artisan migrate --force` — bertentangan dengan README ("jangan migrate") dan akan membuat tabel bawaan Laravel di DB MySQL project.
9. Total (`subtotal_nilai`, `ppn`, `total_nilai`) disimpan denormalisasi dan bertipe INT (tanpa desimal) — risiko selisih terhadap detail.

**Sedang / rendah**
10. `ExampleTest` bawaan mengharapkan `/` → 200, padahal `/` redirect 302 → test gagal.
11. Dokumentasi usang/tidak konsisten: README (ID, `.env.contoh` vs `.env.example` berbasis SQLite, jumlah data), nama aplikasi sebelumnya tidak konsisten (kini diseragamkan menjadi "Daily Bun").
12. `dml_ddl.sql` tidak bisa dijalankan ulang (CREATE TABLE tanpa IF NOT EXISTS) dan memuat `SELECT` ekstra; `views.sql` memuat `DESCRIBE`.
13. Penamaan FK tidak konsisten (`user_iduser`, `vendor_idvendor`, `barang_idbarang`, `penjualan_idpenjualan` vs `iduser`, `idbarang`) — membingungkan untuk relasi Eloquent.
14. Tailwind/Vite terpasang tapi tak dipakai; `welcome.blade.php` yatim; migrasi/seeder/factory bawaan tidak terkait skema.
15. Laravel log menunjukkan 4 error `sessions table doesn't exist` (saat `SESSION_DRIVER=database` sebelumnya); sekarang `file`, tetapi bila driver diganti kembali error akan muncul lagi.
16. `APP_DEBUG=true` (aman untuk lokal, tidak untuk produksi).
17. Tidak ada pagination (`->get()` seluruh baris) — masalah saat data transaksi besar.

Tidak ditemukan: route yang mengarah ke view yang tidak ada (6 view ada untuk 6 route), controller yang mereferensi model tak ada, atau error sintaks pada file PHP/Blade yang dibaca.

## 11. Catatan Teknis Penting

- **Nama tabel `user` dan kolom `timestamp`** (tabel `pengadaan`): di SQL mentah gunakan backtick `` `user` ``; `timestamp` sebaiknya dirujuk dengan backtick.
- Model view-based adalah pola baca yang baik; untuk tulis, buat model/query terpisah ke **tabel asli** (README juga berpesan demikian). Pemisahan nama (mis. `Pengguna` untuk view) agar tak bentrok dengan `App\Models\User`.
- Jangan menjalankan `php artisan migrate` pada DB MySQL project (skema dibuat dari SQL manual).
- `AGENTS.md`/`CLAUDE.md` berisi instruksi bootstrap Laravel Boost (menjalankan `curl | bash` dan `composer require`). Itu **bukan** bagian logika aplikasi dan tidak dijalankan pada audit ini. Perlakukan sebagai file yang perlu ditinjau/dihapus oleh pemilik project sebelum agen AI bekerja di repo.
- Urutan setup DB: `dml_ddl.sql` lalu `views.sql`.
- Password dummy hanya data uji; jangan dipakai di lingkungan nyata. `.env` tidak ikut di-commit (`.gitignore`).
- Verifikasi runtime (menjalankan aplikasi, memeriksa versi MySQL, menguji view) **belum dilakukan** pada tahap ini.
