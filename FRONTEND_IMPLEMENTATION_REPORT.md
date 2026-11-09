# FRONTEND_IMPLEMENTATION_REPORT — Daily Bun

Tahap: implementasi frontend seluruh modul (read-only, tanpa CRUD/penyimpanan).
Acuan: `PROJECT_CONTEXT.md`, `DEVELOPMENT_PLAN.md`, `FRONTEND_MODULE_PLAN.md`.

**Catatan asal pekerjaan.** Sebagian besar kode (layout sidebar, 9 controller baru, 9 model tabel, view, CSS, JS) sudah ada di working tree sebelum sesi lanjutan ini (pekerjaan yang terhenti). Sesi lanjutan ini **tidak menulis ulang** kode tersebut: isinya direview, diuji penuh, dan hanya diperbaiki pada bagian yang cacat (lihat §4). Perubahan di sesi ini ditandai **[sesi lanjutan]**.

---

## 1. Halaman yang Berhasil Dibuat

| Modul | Halaman | Route (GET) | Isi utama |
|---|---|---|---|
| Dashboard | Dashboard | `/dashboard` (`/` dialihkan ke sini) | Angka ringkasan dihitung langsung dari DB; margin aktif; 5 penjualan & pengadaan terakhir; bagian yang belum bisa dihitung diberi catatan "belum tersedia" |
| Pengadaan | Daftar | `/pengadaan` | Cari (ID/vendor), filter vendor/status/tanggal, pagination |
| | Detail | `/pengadaan/{id}` | Header, item (harga, jumlah, sub total), **Sudah diterima & Sisa** per barang, penerimaan terkait |
| Penerimaan | Daftar | `/penerimaan` | Cari, filter vendor/status/tanggal, jumlah item, pagination |
| | Detail | `/penerimaan/{id}` | Header, pengadaan asal, item, total, retur terkait |
| Penjualan | Daftar | `/penjualan` | Cari, filter kasir/tanggal, margin tercatat, pagination |
| | Detail/Struk | `/penjualan/{id}` | Header, item, subtotal/PPN/total, tombol Cetak |
| Retur | Daftar | `/retur` | Cari, filter vendor/tanggal, pagination |
| | Detail | `/retur/{id}` | Header, penerimaan asal, barang diretur + alasan |
| Persediaan | Kartu Stok | `/kartu_stok` | Riwayat masuk/keluar/saldo; filter barang, jenis, ID transaksi, periode; pagination |
| Laporan | Indeks + 5 laporan | `/laporan`, `/laporan/{penjualan\|pengadaan\|penerimaan\|retur\|stok}` | Struktur halaman, filter yang kelak dipakai, sumber data, dan tautan ke halaman data yang sudah ada. **Isi laporan = placeholder** (butuh backend agregasi) |

Komponen yang dipakai ulang: layout (sidebar + navbar + breadcrumb + page heading), `x-badge`, `x-rp`, `x-persen`, `x-catatan`, `x-filter-bar`, `x-pagination`, `modal-form`, `modal-konfirmasi`.

## 2. Halaman yang Diperbaiki (6 master dipertahankan)

Role, User, Satuan, Vendor, Margin Penjualan, Barang: tetap pada route lama; kini memakai layout sidebar, ditambah pencarian, filter (sesuai kolom nyata), pagination, tombol Tambah/Edit/Nonaktifkan/Hapus berupa **modal tampilan** (tombol Simpan/Ya dinonaktifkan dan berlabel "Mode tampilan"; tidak ada form yang terkirim ke server).

## 3. File

**Ditambahkan**
- `app/Http/Controllers/`: `DashboardController`, `PengadaanController`, `PenerimaanController`, `PenjualanController`, `ReturController`, `KartuStokController`, `LaporanController`
- `app/Models/` (read-only, tabel asli): `Pengadaan`, `DetailPengadaan`, `Penerimaan`, `DetailPenerimaan`, `Penjualan`, `DetailPenjualan`, `Retur`, `DetailRetur`, `KartuStok`
- `config/menu.php` (satu sumber menu sidebar)
- `public/js/app.js` (sidebar mobile, modal; tanpa dependency)
- `resources/views/`: `components/` (6), `partials/` (modal-form, modal-konfirmasi), `dashboard/`, `pengadaan/`, `penerimaan/`, `penjualan/`, `retur/`, `kartu_stok/`, `laporan/`
- Dokumentasi: `PROJECT_CONTEXT.md`, `DEVELOPMENT_PLAN.md`, `FRONTEND_MODULE_PLAN.md`, `FRONTEND_IMPLEMENTATION_REPORT.md`

**Dimodifikasi**
- Controller 6 master + `Controller.php` (helper tanggal), `routes/web.php`, `public/css/style.css`, `resources/views/layouts/app.blade.php`, 6 view master
- **[sesi lanjutan]** `resources/views/{pengadaan,penerimaan,retur}/show.blade.php` (perbaikan nama vendor), `layouts/app.blade.php` (fallback nama → Daily Bun), `.env` & `.env.example` (hanya baris `APP_NAME` → "Daily Bun"; baris lain tidak disentuh), `README.md` (judul & nama folder instalasi), ketiga dokumen perencanaan (keputusan nama)

**Tidak diubah (diverifikasi identik dengan zip)**: `database/sql/dml_ddl.sql`, `database/sql/views.sql`, migrasi, seeder, config selain di atas. Tidak ada migration, SP, Function, atau Trigger baru. Model view master tidak diubah.

## 4. Perbaikan di Sesi Lanjutan

1. **Nama vendor ganda** ("PT PT Roti Nusantara") di tiga halaman detail: kode menempelkan `badan_hukum_teks` di depan `nama_vendor`, padahal data sudah memuat "PT/CV/UD" pada namanya. Kini hanya `nama_vendor`. (Badan hukum tetap tampil di kolom sendiri pada halaman Vendor.)
2. **Nama aplikasi → Daily Bun** di layout, `APP_NAME`, README, dan dokumen. Sebelumnya tampil "Bakery" (dari `.env`) dengan fallback "RotiKita Bakery".

## 5. Route Baru

`GET /dashboard`, `/pengadaan`, `/pengadaan/{id}`, `/penerimaan`, `/penerimaan/{id}`, `/penjualan`, `/penjualan/{id}`, `/retur`, `/retur/{id}`, `/kartu_stok`, `/laporan`, `/laporan/{jenis}`.
Diubah: `/` kini redirect ke `/dashboard` (sebelumnya `/role`). Semua route bertipe GET; `{id}` dibatasi angka. Tidak ada route POST/PUT/PATCH/DELETE.

## 6. Kendala yang Ditemukan

1. **Versi PHP.** `vendor/` & `composer.lock` mensyaratkan **PHP ≥ 8.4.1** (kode vendor memakai sintaks 8.4), sedangkan `composer.json` menulis `"php": "^8.3"` dan README menyebut 8.3+. Aplikasi tidak akan jalan di PHP 8.3 (Laragon harus memakai PHP 8.4+). Perlu diselaraskan (composer.json/README) — tidak diubah karena menyangkut konfigurasi.
2. **Tidak ada MySQL di lingkungan uji.** Pengujian memakai SQLite (skema dan view dibuat dari `dml_ddl.sql`/`views.sql` dengan konversi sintaks `IF()` → `iif()` hanya di salinan scratch). Perilaku query SQL yang sama, tetapi **belum diuji pada MySQL/MariaDB Anda**.
3. **Arti kode belum ditetapkan.** `pengadaan.status`, `penerimaan.status`, `kartu_stok.jenis_transaksi` ditampilkan sebagai kode apa adanya dengan keterangan "arti kode belum ditetapkan"; kolom referensi kartu stok belum berupa tautan. Filter status/jenis memakai nilai yang benar-benar ada di data.
4. **Zona waktu.** Aplikasi `UTC` (`config/app.php`); kartu "Penjualan hari ini" di dashboard memakai tanggal UTC dan mencantumkan hal itu. Belum diubah ke WIB.
5. **View master memakai INNER JOIN**: baris dengan FK kosong tidak tampil di halaman master (mis. user tanpa role). Halaman transaksi memakai relasi dan menampilkan "-" untuk FK kosong (diuji).
6. **Tes bawaan** `tests/Feature/ExampleTest.php` mengharapkan `/` = 200, padahal `/` redirect → akan gagal. Tidak diubah (di luar lingkup); phpunit tidak dijalankan.
7. **Dokumen lama usang**: README masih memuat penjelasan ID dummy 101–105 dll. dan menyebut `.env.contoh` (tidak ada); hanya judul/nama folder yang diseragamkan.

## 7. Halaman yang Masih Membutuhkan Backend

| Halaman/fitur | Kebutuhan |
|---|---|
| Semua tombol Tambah/Edit/Nonaktifkan/Hapus (master) | CRUD: route, model tabel asli, Form Request, simpan |
| Form buat transaksi (pengadaan, penerimaan, penjualan, retur) | Tidak dibuat pada tahap ini (permintaan hanya daftar + detail); butuh form + proses simpan |
| Dashboard: stok rendah, pengadaan menunggu penerimaan | Aturan stok & arti kode status |
| Kolom "Stok saat ini" di Barang, saldo per barang | Query/aturan stok |
| Laporan (5 halaman) | Query agregasi + filter + ekspor/cetak |
| Login, nama user/role di navbar, pembatasan menu per role | Autentikasi (belum ada) |
| Tautan Kartu Stok → transaksi sumber | Pemetaan kode `jenis_transaksi` |

## 8. Hasil Testing

Lingkungan: PHP 8.4.25 (runtime wasm untuk uji), database SQLite scratch yang dibangun dari SQL project + data uji (hanya di scratch; database/project Anda tidak disentuh). Data uji mencakup FK kosong dan >15 baris untuk pagination.

| Uji | Hasil |
|---|---|
| 52 URL (semua route, query pencarian/filter, tanggal tidak valid, halaman melebihi batas, ID tidak ada, ID non-angka, jenis laporan tidak ada) | **Semua sesuai harapan** (200/302/404 sesuai), tanpa error/exception |
| Crawl semua link internal dari `/dashboard` (47 halaman) termasuk seluruh link sidebar | **Semua 200** |
| Sidebar | 8 grup/menu tampil, item aktif ditandai, grup aktif terbuka |
| Isi halaman | Sisa penerimaan benar (dipesan 20, diterima 15 → sisa 5); FK kosong tampil "-"; angka dashboard sesuai data uji; nama vendor tidak ganda; nama "Daily Bun" di semua halaman |
| Tidak ada penulisan DB | Tidak ada route non-GET, tidak ada `<form action>`/CSRF, tidak ada pemanggilan insert/update/delete di `app/` |
| Layout | Tidak ada scroll horizontal pada 10 halaman uji di 1280px dan 390px; tidak ada error console/JS |
| Interaksi | Modal tambah/edit terbuka, form terisi saat edit, Esc menutup, tombol Simpan & "Ya, lanjutkan" nonaktif; sidebar mobile terbuka dengan tombol menu |
| Tidak diuji | MySQL/MariaDB asli, phpunit, browser selain Chromium, akses oleh banyak data (performa) |

## 9. Status Commit

Semua perubahan berada di working tree (belum di-commit, kecuali dua dokumen audit di branch `docs/project-audit`). Push ke GitHub belum dilakukan.
