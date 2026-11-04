# DEVELOPMENT_PLAN — Project Bakery

> Pasangan dari `PROJECT_CONTEXT.md` (baca dulu). Rencana ini disusun dari kondisi project sebenarnya.
> Item bertanda **[KONFIRMASI]** harus diputuskan pemilik project sebelum diimplementasikan.
> Tahap audit tidak mengubah source code maupun database; semua perubahan di bawah baru dilakukan pada tahap pengembangan.

---

## 1. Prinsip Pengembangan

1. **Tulis ke tabel, baca dari view.** Model view yang ada (`Role`, `Pengguna`, dll.) dipertahankan untuk tampilan; tulis lewat model/query ke tabel asli (usulan: namespace `App\Models\Tabel`; putuskan satu konvensi dan dokumentasikan).
2. **Transaksi tidak di-CRUD biasa.** Pengadaan, penerimaan, penjualan, retur dibuat lewat satu proses (header + detail dalam satu `DB::transaction`), bukan edit baris bebas.
3. **Stok hanya berubah lewat satu jalur** (trigger/SP di database) agar `kartu_stok` konsisten. Aplikasi tidak menulis `kartu_stok` langsung.
4. **Hapus = nonaktifkan** untuk master yang sudah dipakai (FK), bukan DELETE.
5. Perubahan skema (index, UNIQUE, CHECK, DEFAULT) dilakukan lewat **file SQL baru bernomor** (`database/sql/02_*.sql`, dst.), tidak mengedit `dml_ddl.sql`, dan hanya setelah disetujui pemilik project.
6. Satu modul selesai (fungsi + validasi + test) sebelum modul berikutnya; UI/UX dipoles terakhir.

## 2. Masalah Prioritas (hasil audit) & Penyelesaian

| Prioritas | Masalah | Rekomendasi | Tahap |
|---|---|---|---|
| P0 | Stok tidak terlindungi (tidak ada trigger/SP, saldo `kartu_stok.stock` rawan salah) | Trigger + SP (lihat §6); satu jalur penulisan stok | Fase 4–5 |
| P0 | Tidak ada auth; `config/auth.php` menunjuk `users` bawaan | Model auth khusus tabel `user` (kolom `iduser`, `username`, `password`), konfigurasi provider custom, login/logout, middleware per role | Fase 1 |
| P0 | Model hanya VIEW, tak bisa tulis | Tambah model tabel asli (fillable, relasi) | Fase 2 |
| P1 | Semua kolom nullable, tanpa UNIQUE/CHECK/DEFAULT; view INNER JOIN menyembunyikan baris FK NULL | Validasi ketat di Form Request sejak awal; usulkan skrip `02_constraints.sql` (NOT NULL, UNIQUE username, CHECK jumlah>0, DEFAULT created_at) — **[KONFIRMASI]** | Fase 0 |
| P1 | `kartu_stok.idtransaksi` INT tanpa FK, id sumber BIGINT | Usulan: ubah ke BIGINT; definisikan kode `jenis_transaksi` — **[KONFIRMASI]** | Fase 0 |
| P1 | Perilaku TIMESTAMP tergantung versi server | Cek `SELECT VERSION()` & `SHOW CREATE TABLE`; set DEFAULT eksplisit atau isi dari aplikasi | Fase 0 |
| P1 | Timezone `UTC` | Set `config/app.php` timezone ke `Asia/Jakarta` (aplikasi) dan sesuaikan `time_zone` koneksi | Fase 1 |
| P1 | `composer setup` menjalankan `migrate --force` | Hapus/ubah script saat pengembangan; catat larangan migrate | Fase 1 |
| P2 | ExampleTest bawaan gagal (302 vs 200) | Ganti test dengan test nyata | Fase 7 |
| P2 | README usang (ID dummy salah); penamaan sudah diseragamkan menjadi "Daily Bun" | Seragamkan nama, perbarui README | Fase 1 |
| P2 | Tanpa pagination, seluruh data `get()` | `paginate()` + pencarian | Fase 2 |
| P3 | Tailwind/Vite tak terpakai, `welcome.blade.php` yatim, migrasi bawaan | Putuskan: pakai Vite atau hapus; bersihkan | Fase 8 |
| P3 | `AGENTS.md`/`CLAUDE.md` berisi bootstrap Laravel Boost | Pemilik meninjau; ganti dengan pedoman project asli (isi dari `PROJECT_CONTEXT.md`) | Fase 0 |

## 3. Daftar Modul Frontend & Pemetaan Tabel

Keterangan jenis: **CRUD** = CRUD lengkap; **CRUD-N** = CRUD dengan "nonaktifkan" bukan hapus; **TRX** = proses transaksi khusus; **RO** = hanya baca/dihasilkan sistem.

### 3.1 Dashboard
| Halaman | Fitur |
|---|---|
| `/dashboard` (menggantikan redirect ke `/role`) | Ringkasan: penjualan hari ini, pengadaan menunggu penerimaan, barang stok rendah, margin aktif. Sumber: view/query agregat |

### 3.2 Master Data
| Tabel | Fungsi | Jenis | Halaman | Fitur | Relasi | Validasi utama |
|---|---|---|---|---|---|---|
| role | Peran pengguna | CRUD (hapus ditolak jika dipakai user) | index, create, edit | CRUD, cegah hapus bila dipakai | 1─∞ user | nama_role wajib, unik, ≤100 |
| user | Akun | CRUD-N | index, create, edit, ganti password | Hash password (bcrypt), pilih role, reset password | ∞─1 role; dipakai semua transaksi | username wajib, unik, ≤45; password min 8; idrole exists |
| satuan | Satuan barang | CRUD-N (kolom status) | index, create, edit | Aktif/nonaktif | 1─∞ barang | nama wajib, unik, ≤45; status 0/1 |
| vendor | Pemasok | CRUD-N (status A/N) | index, create, edit | Filter aktif; badan hukum P/C/U | 1─∞ pengadaan | nama wajib ≤100; badan_hukum in P,C,U; status in A,N **[KONFIRMASI kode nonaktif]** |
| margin_penjualan | Persentase margin | Khusus: hanya 1 aktif | index, create, aktivasi | Membuat margin baru, mengaktifkan satu = menonaktifkan yang lain (transaksi DB); `iduser` dari login; `updated_at` diisi saat berubah | 1─∞ penjualan | persen numerik >0 (≤ batas yang disepakati); satu aktif |
| barang | Katalog produk | CRUD-N | index (filter jenis/satuan/status, pencarian), create, edit | Harga jual, jenis B/D/C/P, satuan, status | ∞─1 satuan; dipakai di detail & kartu_stok | nama wajib ≤45; jenis in B,D,C,P; idsatuan exists & aktif; harga integer ≥0 |

### 3.3 Pengadaan (TRX)
| Tabel | Jenis | Halaman | Fitur | Relasi | Validasi |
|---|---|---|---|---|---|
| pengadaan | TRX | daftar (filter status/vendor/tanggal), buat, detail/cetak | Pilih vendor aktif, tambah baris barang, hitung subtotal/ppn/total otomatis, ubah status (mis. draft→disetujui→selesai/batal) **[KONFIRMASI kode status]** | ∞─1 vendor, user; 1─∞ detail, penerimaan | vendor aktif; minimal 1 baris; total = Σdetail + ppn |
| detail_pengadaan | bagian dari form pengadaan (tanpa halaman sendiri) | baris dinamis di form | harga_satuan, jumlah, sub_total | ∞─1 barang, pengadaan | jumlah integer >0; harga ≥0; barang aktif; barang tidak ganda dalam satu pengadaan; sub_total = harga×jumlah |

Pengadaan yang sudah punya penerimaan tidak boleh diubah/dibatalkan.

### 3.4 Penerimaan (TRX)
| Tabel | Jenis | Halaman | Fitur | Relasi | Validasi |
|---|---|---|---|---|---|
| penerimaan | TRX | daftar, buat dari pengadaan, detail | Pilih pengadaan yang masih punya sisa; form terisi dari detail pengadaan; terima parsial; ubah status pengadaan bila lengkap | ∞─1 pengadaan, user; 1─∞ detail | pengadaan valid & belum selesai |
| detail_penerimaan | bagian form penerimaan | baris | jumlah_terima, harga_satuan_terima, sub_total_terima | ∞─1 penerimaan, barang | jumlah_terima >0 dan ≤ sisa pesanan **[KONFIRMASI: boleh melebihi?]**; barang harus ada di pengadaan terkait |

Efek samping wajib: menambah stok (`kartu_stok` masuk) — via trigger (§6).

### 3.5 Penjualan (TRX)
| Tabel | Jenis | Halaman | Fitur | Relasi | Validasi |
|---|---|---|---|---|---|
| penjualan | TRX (kasir) | kasir/POS, riwayat, detail/struk | Pilih barang aktif, harga dari `barang.harga`, hitung subtotal/ppn/total, `idmargin_penjualan` = margin aktif saat transaksi, `iduser` = kasir login | ∞─1 user, margin; 1─∞ detail | ada margin aktif; minimal 1 baris |
| detail_penjualan | bagian form | baris keranjang | harga_satuan (snapshot dari barang), jumlah, subtotal | ∞─1 penjualan, barang | jumlah >0; stok cukup **[KONFIRMASI: tolak bila kurang?]**; barang aktif |

Penjualan tidak bisa diedit; koreksi lewat **[KONFIRMASI]** (pembatalan/retur penjualan tidak didukung struktur saat ini).

### 3.6 Retur (TRX)
| Tabel | Jenis | Halaman | Fitur | Relasi | Validasi |
|---|---|---|---|---|---|
| retur | TRX | daftar, buat dari penerimaan, detail | Retur **pembelian** (ke vendor) berdasar penerimaan | ∞─1 penerimaan, user; 1─∞ detail | penerimaan valid |
| detail_retur | bagian form | baris | pilih `detail_penerimaan`, jumlah, alasan | ∞─1 retur, detail_penerimaan | jumlah >0 dan ≤ (jumlah_terima − total retur sebelumnya); alasan wajib ≤200 |

Efek samping: mengurangi stok (`kartu_stok` keluar). Retur penjualan: **tidak ada struktur** — keputusan pemilik **[KONFIRMASI]**.

### 3.7 Persediaan
| Tabel | Jenis | Halaman | Fitur |
|---|---|---|---|
| kartu_stok | RO (diisi sistem) | `/persediaan/stok` (stok saat ini per barang), `/persediaan/kartu/{barang}` (riwayat masuk/keluar/saldo) | Filter periode & jenis; tidak ada tombol tambah/ubah/hapus; stok rendah (batas **[KONFIRMASI]**) |

### 3.8 Laporan
Laporan penjualan (periode, per barang, per kasir), laporan pengadaan/penerimaan (per vendor), laporan retur, laporan stok & kartu stok. Dibangun di atas VIEW baru (§5) + ekspor cetak/CSV. Akses: manajer/admin.

### 3.9 Hak akses (usulan, **[KONFIRMASI]** pemetaan role)
Role dummy: Manajer, Admin, Kasir, Staff Gudang, Staff Pembelian. Usulan: Kasir→penjualan; Staff Pembelian→pengadaan; Staff Gudang→penerimaan, retur, persediaan; Admin→master data & user; Manajer→semua baca + laporan + margin.

## 4. Rencana Implementasi CRUD (master data)

Pola yang sama untuk 6 master (kerjakan berurutan sesuai dependensi: role → user → satuan → vendor → margin_penjualan → barang):

1. Route `Route::resource` (tanpa `show` bila tak perlu) di dalam grup middleware auth; aksi nonaktif memakai route `PATCH`.
2. Model tabel asli (`$table`, `$primaryKey`, `$fillable`, `$timestamps=false` kecuali margin), relasi Eloquent sesuai FK (hati-hati nama FK tak konsisten, definisikan eksplisit).
3. `FormRequest` untuk validasi (aturan di §3.2); pesan error berbahasa Indonesia.
4. Controller: `index` baca dari model view (tetap), `store/update` tulis ke model tabel, `destroy/toggle` cek pemakaian FK dahulu.
5. Blade: partial `_form`, komponen tabel, tombol aksi, flash message, pagination, token CSRF.
6. Test feature per modul (lihat §8).

Kekhususan: `user` — hash password dengan `Hash::make`, jangan tampilkan hash; `margin_penjualan` — aktivasi eksklusif dalam transaksi; `barang` — harga integer rupiah.

## 5. Rencana Proses Transaksi

Semua proses memakai `DB::transaction`, validasi di Form Request, dan baris detail di-submit sebagai array. Nomor/ID memakai AUTO_INCREMENT.

**Pengadaan**: validasi → insert `pengadaan` (status awal **[KONFIRMASI]**) → insert `detail_pengadaan` → hitung `subtotal_nilai`, `ppn`, `total_nilai` di server (jangan percaya input klien). Rumus PPN dan tarif **[KONFIRMASI]**.

**Penerimaan**: pilih pengadaan → hitung sisa per barang (`detail_pengadaan.jumlah` − Σ`detail_penerimaan.jumlah_terima`) → insert `penerimaan` + `detail_penerimaan` → trigger menambah `kartu_stok` → bila semua sisa 0, set status pengadaan selesai.

**Penjualan**: ambil margin aktif → insert `penjualan` + `detail_penjualan` (harga snapshot) → hitung total → trigger/SP mengurangi stok. Pengunci baris stok (`SELECT … FOR UPDATE`) agar tidak oversell.

**Retur**: pilih penerimaan → insert `retur` + `detail_retur` → trigger/SP mengurangi stok.

VIEW baru yang diusulkan (file SQL baru): `v_pengadaan`, `v_detail_pengadaan`, `v_penerimaan`, `v_penjualan`, `v_retur`, `v_stok_barang` (stok saat ini), `v_kartu_stok`. Gunakan LEFT JOIN bila FK nullable.

## 6. Rencana Stored Procedure, Function, Trigger (target minimal 1/1/1)

Semua dibuat lewat file SQL baru (`database/sql/03_routines.sql`), tidak lewat migrasi Laravel.

| Objek | Usulan | Tujuan |
|---|---|---|
| **Function** `fn_stok_barang(p_idbarang INT) RETURNS INT` | Mengembalikan stok saat ini dari `kartu_stok` (saldo terakhir atau SUM masuk−keluar; pilih satu, **[KONFIRMASI]**) | Dipakai penjualan, view stok, laporan |
| **Trigger** `trg_detail_penerimaan_ai` AFTER INSERT pada `detail_penerimaan` | Insert `kartu_stok` (jenis penerimaan, masuk=jumlah_terima, stock=fn_stok+masuk, idtransaksi=idpenerimaan) | Stok masuk otomatis & konsisten |
| Trigger `trg_detail_penjualan_ai` AFTER INSERT pada `detail_penjualan` | `kartu_stok` keluar; tolak (SIGNAL) bila stok tidak cukup (bila aturan disetujui) | Stok keluar otomatis |
| Trigger `trg_detail_retur_ai` AFTER INSERT pada `detail_retur` | `kartu_stok` keluar (barang diambil dari `detail_penerimaan.barang_idbarang`) | Retur mengurangi stok |
| **Stored Procedure** `sp_simpan_penjualan(...)` | Opsi: menerima JSON/tabel sementara berisi item, mengambil margin aktif, menyimpan header+detail, menghitung total, dalam satu transaksi | Transaksi atomik di sisi DB. Alternatif: tetap di Laravel dan SP hanya untuk laporan/retur |
| SP `sp_laporan_penjualan(p_dari DATE, p_sampai DATE)` | Rekap penjualan per periode | Laporan |

Catatan: trigger tidak boleh melakukan UPDATE/INSERT pada tabel yang sama dengan pemicunya; `idtransaksi` & kode `jenis_transaksi` harus ditetapkan lebih dulu (mis. `M`=penerimaan, `K`=penjualan, `R`=retur — hanya usulan, **[KONFIRMASI]**). Urutan: Function → Trigger → SP, diuji dengan data dummy sebelum UI memakainya.

## 7. Urutan Pengerjaan (berdasarkan prioritas & dependensi)

| Fase | Isi | Prasyarat | Hasil |
|---|---|---|---|
| 0 | Keputusan **[KONFIRMASI]** (§9), cek versi MySQL & skema aktual, usulan `02_constraints.sql` | – | Aturan bisnis tertulis |
| 1 | Fondasi: timezone, auth + middleware role, layout (sidebar/navbar, flash, komponen form), dashboard kosong, rapikan README/nama, script composer | 0 | Login & kerangka UI |
| 2 | CRUD master: role → user → satuan → vendor → margin_penjualan → barang (+ pagination, pencarian) | 1 | 6 master lengkap |
| 3 | Objek DB tahap 1: `kartu_stok` aturan, Function `fn_stok_barang`, VIEW transaksi & stok | 0, 2 | Fondasi stok |
| 4 | Pengadaan (+ detail) | 2, 3 | Form & status pengadaan |
| 5 | Penerimaan + Trigger stok masuk | 4 | Stok bertambah otomatis |
| 6 | Penjualan + Trigger stok keluar (+ SP bila dipilih) | 2, 3, 5 | Kasir berjalan, stok berkurang |
| 7 | Retur + Trigger | 5 | Retur pembelian |
| 8 | Persediaan (halaman stok & kartu stok), Laporan | 5–7 | Monitoring & laporan |
| 9 | Test menyeluruh, perbaikan bug, data dummy transaksi | semua | Siap uji |
| 10 | Perbaikan UI/UX (§10) | 9 | Tampilan final |

Alasan urutan: auth & layout dibutuhkan semua halaman; master harus ada sebelum transaksi (FK); pengadaan→penerimaan→(stok)→penjualan karena penjualan butuh stok; retur bergantung pada penerimaan.

## 8. Rencana Testing

1. **Basis**: PHPUnit sudah ada, tetapi `phpunit.xml` memakai SQLite `:memory:` sementara skema asli MySQL dengan view/trigger. Rekomendasi: database MySQL khusus test (mis. `projek_pbd_094_test`) yang dimuat dari file SQL, **bukan** DB utama. Putuskan sebelum menulis test **[KONFIRMASI]**.
2. **Hapus/ganti** `ExampleTest` (kini gagal karena `/` redirect).
3. **Feature test per modul**: akses tanpa login ditolak; per role; validasi (tiap aturan §3); alur sukses CRUD; penolakan hapus saat dipakai FK.
4. **Test transaksi**: pengadaan menghitung total benar; penerimaan parsial & lengkap; penjualan menolak stok kurang & memakai margin aktif; retur tidak melebihi jumlah terima; rollback bila satu baris gagal.
5. **Test database** (SQL/skrip): Function mengembalikan stok benar; trigger menambah/mengurangi `kartu_stok`; saldo berjalan konsisten setelah rangkaian transaksi; SIGNAL pada stok tidak cukup.
6. **Uji manual berskenario** (checklist): pengadaan → penerimaan → penjualan → retur dengan angka stok dicek di tiap langkah.
7. **Regresi**: 6 halaman master read-only awal tetap tampil benar.

## 9. Keputusan yang Dibutuhkan dari Pemilik Project

1. Kode `status` pengadaan & penerimaan; kode `jenis_transaksi` kartu_stok; kode nonaktif vendor (hanya `A` terlihat).
2. Peran `margin_penjualan` terhadap `barang.harga` (SQL menyebut harga sudah final).
3. Tarif & rumus PPN.
4. Stok negatif diperbolehkan atau ditolak; batas stok rendah.
5. Retur: hanya retur pembelian (sesuai struktur) atau perlu retur penjualan (butuh tabel baru → di luar aturan saat ini).
6. Boleh/tidaknya mengubah skema (UNIQUE, NOT NULL, CHECK, DEFAULT, tipe `idtransaksi`) lewat file SQL baru.
7. Pemetaan role → modul (§3.9).
8. Penerimaan boleh melebihi jumlah pesanan?
9. Strategi DB test (§8.1).
10. ~~Nama aplikasi final~~ — sudah diputuskan: **Daily Bun**.
11. Apakah menggunakan Tailwind/Vite atau tetap CSS statis.

## 10. Rencana Perbaikan UI/UX (setelah fungsi selesai)

1. Layout: sidebar bergrup (Dashboard, Master Data, Pengadaan, Penerimaan, Penjualan, Retur, Persediaan, Laporan) + navbar atas (nama user, role, logout); menu dibangun dari konfigurasi dan difilter per role, bukan array hard-coded di Blade.
2. Komponen Blade reusable: tabel, form field, tombol, badge status, modal konfirmasi, alert/flash, pagination, empty state.
3. Form transaksi: baris detail dinamis (tambah/hapus), kalkulasi total langsung, pencarian barang (autocomplete), validasi inline, cegah double submit.
4. POS kasir: layout cepat, tombol kuantitas, struk cetak.
5. Dashboard: kartu ringkas + grafik sederhana, aksesibel (kontras, label, tidak hanya warna).
6. Konsistensi: pertahankan palet cokelat/krem yang ada, tipografi, jarak, status warna (aktif/nonaktif/peringatan), responsif ponsel/tablet, `:focus-visible` yang sudah ada.
7. Bahasa: seluruh label & pesan error Indonesia; format Rupiah/tanggal seragam; timezone WIB.
8. Keputusan Vite/Tailwind (§9.11) lalu bersihkan aset yang tak dipakai.

## 11. Checklist Kesiapan Tiap Modul (Definition of Done)

- [ ] Route, controller, model, Form Request selesai & dinamai konsisten
- [ ] Validasi sesuai §3 + pesan Indonesia
- [ ] Akses per role diuji
- [ ] Efek ke stok/relasi terverifikasi
- [ ] Test feature lulus
- [ ] Halaman memakai komponen layout bersama
- [ ] `PROJECT_CONTEXT.md` diperbarui (fitur tersedia/belum)
