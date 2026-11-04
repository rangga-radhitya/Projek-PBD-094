# FRONTEND_MODULE_PLAN — Project Bakery

> Perencanaan frontend saja. Tidak ada kode, route, controller, migration, atau SQL yang dibuat/diubah.
> Referensi: `PROJECT_CONTEXT.md`, `DEVELOPMENT_PLAN.md`, `database/sql/dml_ddl.sql`, `database/sql/views.sql`, source Laravel saat ini
> (kolom di bawah sudah dicocokkan ulang dengan SQL; tidak ada ketidaksesuaian dengan PROJECT_CONTEXT.md).
> Stored Procedure, Function, dan Trigger **tidak dibahas** di dokumen ini (tahap terpisah).

**Legenda penanda**

| Tanda | Arti |
|---|---|
| **[FE]** | Cukup pekerjaan frontend (Blade/CSS/JS) |
| **[BE]** | Butuh route/controller/model/validasi baru (belum dibuat; dikerjakan saat implementasi modul) |
| **[DB]** | Butuh perubahan struktur database — perlu persetujuan, tidak dilakukan sekarang |
| **[?]** | Butuh konfirmasi pemilik project |

---

## 1. Struktur Navigasi Aplikasi

### 1.1 Sidebar yang direkomendasikan

Rancangan awal Anda dipertahankan, dengan **penggabungan halaman header–detail** (alasan di §1.2).

```
A. Dashboard
B. Master Data
   ├─ Role
   ├─ User
   ├─ Satuan
   ├─ Vendor
   ├─ Margin Penjualan
   └─ Barang
C. Pengadaan
   └─ Data Pengadaan            (detail ada di dalam halaman detail/ form)
D. Penerimaan
   └─ Data Penerimaan
E. Penjualan
   └─ Data Penjualan
F. Retur
   └─ Data Retur
G. Persediaan
   └─ Kartu Stok
H. Laporan
   ├─ Laporan Penjualan
   ├─ Laporan Pengadaan
   ├─ Laporan Penerimaan
   ├─ Laporan Retur
   └─ Laporan Stok
```

Perilaku sidebar: grup bisa dilipat; item aktif ditandai (memakai gaya `aktif` yang sudah ada); di layar kecil sidebar menjadi menu geser yang dibuka dari tombol di navbar. Menu dibangun dari satu daftar konfigurasi (bukan array di dalam Blade seperti sekarang) **[BE kecil]**. Pembatasan menu per role **[?]** (pemetaan role → modul belum diputuskan; role di data dummy: Manajer, Admin, Kasir, Staff Gudang, Staff Pembelian).

### 1.2 Rekomendasi penggabungan header–detail

| Rancangan awal | Rekomendasi | Alasan (manfaat bagi pengguna) |
|---|---|---|
| Data Pengadaan + Detail Pengadaan | **Satu menu** "Data Pengadaan"; detail tampil di halaman detail pengadaan dan sebagai baris item di form | `detail_pengadaan` tidak bermakna tanpa header-nya; daftar semua baris detail lintas transaksi tidak dibutuhkan untuk pekerjaan harian |
| Data Penerimaan + Detail Penerimaan | **Satu menu** | Sama |
| Data Penjualan + Detail Penjualan | **Satu menu** | Sama; kasir bekerja per struk |
| Data Retur + Detail Retur | **Satu menu** | Sama |
| Kartu Stok | **Tetap halaman sendiri** | Tabel `kartu_stok` berdiri sendiri (per barang), dipakai untuk monitoring |

Jika kelak ada kebutuhan analisis "barang apa saja yang pernah dipesan/dijual" lintas transaksi, itu dipenuhi lewat **Laporan** (per barang), bukan halaman detail terpisah. Karena itu tidak ada halaman "Detail Pengadaan/Penerimaan/Penjualan/Retur" mandiri.

### 1.3 Navbar (atas)

Berisi: tombol buka-sidebar (mobile), nama aplikasi, judul/breadcrumb halaman, nama user + role yang login, tombol keluar. Nama user/role dan logout membutuhkan autentikasi **[BE]** (belum ada; lihat §9).

---

## 2. Daftar Seluruh Halaman

| # | Halaman | Rute (usulan, belum dibuat) | Jenis | Tabel utama |
|---|---|---|---|---|
| 0 | Login | `/login` | form | `user` **[BE]** |
| 1 | Dashboard | `/dashboard` | ringkasan | agregat dari beberapa tabel |
| 2 | Role | `/role` | daftar + modal form | `role` |
| 3 | User | `/user` | daftar + modal form | `user`, `role` |
| 4 | Satuan | `/satuan` | daftar + modal form | `satuan` |
| 5 | Vendor | `/vendor` | daftar + modal form | `vendor` |
| 6 | Margin Penjualan | `/margin_penjualan` | daftar + modal form | `margin_penjualan`, `user` |
| 7 | Barang | `/barang` | daftar + modal form | `barang`, `satuan` |
| 8 | Daftar Pengadaan | `/pengadaan` | daftar | `pengadaan` |
| 9 | Buat Pengadaan | `/pengadaan/create` | form header + baris item | `pengadaan`, `detail_pengadaan` |
| 10 | Detail Pengadaan | `/pengadaan/{id}` | detail | + `penerimaan` terkait |
| 11 | Daftar Penerimaan | `/penerimaan` | daftar | `penerimaan` |
| 12 | Buat Penerimaan | `/penerimaan/create?pengadaan={id}` | form dari pengadaan | `penerimaan`, `detail_penerimaan` |
| 13 | Detail Penerimaan | `/penerimaan/{id}` | detail | + `retur` terkait |
| 14 | Daftar Penjualan | `/penjualan` | daftar | `penjualan` |
| 15 | Buat Penjualan (kasir) | `/penjualan/create` | form keranjang | `penjualan`, `detail_penjualan` |
| 16 | Detail/Struk Penjualan | `/penjualan/{id}` | detail + cetak | |
| 17 | Daftar Retur | `/retur` | daftar | `retur` |
| 18 | Buat Retur | `/retur/create?penerimaan={id}` | form dari penerimaan | `retur`, `detail_retur` |
| 19 | Detail Retur | `/retur/{id}` | detail | |
| 20 | Kartu Stok | `/kartu_stok` | daftar read-only | `kartu_stok` |
| 21 | Laporan (5 halaman) | `/laporan/...` | tabel + filter + cetak | beberapa tabel |

Semua rute di atas **usulan**; belum ada yang dibuat. Pola penamaan mengikuti rute yang sudah ada (`/margin_penjualan`, huruf kecil, underscore).

---

## 3. Detail Fitur Setiap Halaman

Aturan umum semua daftar: pagination, kotak pencarian, tombol aksi per baris, keadaan kosong (pola `kosong` yang sudah ada dipertahankan), notifikasi berhasil/gagal.

### 3.1 Dashboard
- **Tujuan**: ringkasan kondisi toko saat masuk aplikasi (menggantikan redirect `/` → `/role` saat ini).
- **Data (semuanya turunan dari kolom yang ada)**: jumlah & total penjualan hari ini (`penjualan.created_at`, `total_nilai`); pengadaan yang belum ada penerimaan; margin aktif saat ini (`margin_penjualan.status=1`); barang dengan stok terendah (dari `kartu_stok`); 5 transaksi terakhir.
- **Aksi**: tautan cepat ke "Buat Penjualan", "Buat Pengadaan".
- **Catatan**: "pengadaan belum selesai" butuh definisi status **[?]**; "stok rendah" butuh batas **[?]** — tampilkan "stok terendah" tanpa batas sampai diputuskan. Query agregat **[BE]**.

### 3.2 Master Data (pola sama untuk 6 halaman)

Pola: **daftar + modal form tambah/edit + modal konfirmasi**. Semua master berkolom ≤ 5 isian, jadi cukup modal (tidak perlu halaman form terpisah). Pencarian dan filter bekerja pada kolom yang benar-benar ada. Data tampil dari VIEW yang sudah ada; tulis ke tabel asli **[BE]**.

**Hapus vs nonaktifkan (berdasarkan kolom yang ada):**
- `role`, `user`: **tidak punya kolom status** → hanya tombol Hapus; ditolak dengan pesan jelas bila masih dipakai FK (mis. role dipakai user; user dipakai transaksi).
- `satuan`, `vendor`, `margin_penjualan`, `barang`: punya kolom `status` → tombol **Nonaktifkan/Aktifkan**, bukan hapus; Hapus hanya ditawarkan bila belum dipakai **[?]** (jika ingin hapus tetap tersedia).

#### Role (`role`)
| Item | Rancangan |
|---|---|
| Kolom tabel | ID (`idrole`), Nama role (`nama_role`), Jumlah user (hitungan dari `user` **[BE]**), Aksi |
| Form | Nama role — wajib, teks ≤100, unik (aturan unik sebagai validasi aplikasi; constraint DB **[DB][?]**) |
| Aksi | Tambah, Edit, Hapus (konfirmasi; ditolak bila dipakai) |
| Cari/Filter | Cari nama role |
| Relasi | 1─∞ `user` |

#### User (`user`, tampil dari `v_user`)
| Item | Rancangan |
|---|---|
| Kolom tabel | ID (`iduser`), Username, Role (`nama_role`), Aksi |
| Form | Username (wajib, ≤45, unik); Password (wajib saat tambah, kosong = tidak diubah saat edit; min 8 karakter — angka minimum **[?]**; disimpan ter-hash, tidak pernah ditampilkan); Role (select dari `role`, wajib) |
| Aksi | Tambah, Edit, Ubah password, Hapus (ditolak bila sudah dipakai di transaksi/margin) |
| Cari/Filter | Cari username; filter Role |
| Relasi | ∞─1 `role`; dipakai di `margin_penjualan`, `pengadaan` (`user_iduser`), `penerimaan`, `penjualan`, `retur` |

#### Satuan (`satuan`)
| Item | Rancangan |
|---|---|
| Kolom tabel | ID, Nama satuan, Status (badge Aktif/Nonaktif dari `status_teks`), Jumlah barang **[BE]**, Aksi |
| Form | Nama satuan (wajib, ≤45, unik); Status (Aktif=1 / Nonaktif=0) |
| Aksi | Tambah, Edit, Nonaktifkan/Aktifkan |
| Cari/Filter | Cari nama; filter Status |
| Relasi | 1─∞ `barang` |

#### Vendor (`vendor`)
| Item | Rancangan |
|---|---|
| Kolom tabel | ID, Nama vendor, Badan hukum (`badan_hukum_teks`: PT/CV/UD), Status (badge), Aksi |
| Form | Nama vendor (wajib, ≤100); Badan hukum (select P=PT, C=CV, U=UD); Status (A = Aktif; kode nonaktif **[?]** — data hanya memuat `A`, view memperlakukan selain `A` sebagai Nonaktif) |
| Aksi | Tambah, Edit, Nonaktifkan/Aktifkan |
| Cari/Filter | Cari nama; filter Badan hukum, Status |
| Relasi | 1─∞ `pengadaan` |

#### Margin Penjualan (`margin_penjualan`, tampil dari `v_margin_penjualan`)
| Item | Rancangan |
|---|---|
| Kolom tabel | ID, Persen, Status (badge), Dibuat oleh (`username`), Dibuat (`created_at`), Diubah (`updated_at`), Aksi |
| Form | Persen (wajib, angka > 0; batas atas **[?]**); Status (default Nonaktif). `iduser` otomatis dari user login (tidak diisi manual) |
| Aksi | Tambah, Edit, **Aktifkan** (mengaktifkan satu = menonaktifkan lainnya; konfirmasi), tidak ada Hapus bila sudah dipakai di penjualan |
| Cari/Filter | Filter Status |
| Aturan tampilan | Margin aktif ditandai menonjol di atas daftar; data dummy memiliki tepat satu aktif |
| Relasi | ∞─1 `user`; 1─∞ `penjualan` |
| Catatan | Hubungan margin dengan `barang.harga` belum jelas (komentar SQL: harga sudah final) **[?]** |

#### Barang (`barang`, tampil dari `v_barang`)
| Item | Rancangan |
|---|---|
| Kolom tabel | ID (`idbarang` — **tidak ada kolom kode barang di DB; jangan ditambahkan di UI**), Nama, Jenis (`jenis_teks`), Satuan (`nama_satuan`), Harga (format Rp), Status (badge), Stok saat ini (turunan dari `kartu_stok` **[BE]**), Aksi |
| Form | Nama (wajib, ≤45); Jenis (select B=Bread, D=Drink, C=Cake, P=Pastry); Satuan (select satuan aktif, wajib); Harga (integer ≥ 0, label "Harga jual"); Status |
| Aksi | Tambah, Edit, Nonaktifkan/Aktifkan |
| Cari/Filter | Cari nama; filter Jenis, Satuan, Status |
| Relasi | ∞─1 `satuan`; dipakai di `detail_pengadaan`, `detail_penerimaan` (`barang_idbarang`), `detail_penjualan`, `kartu_stok` |
| Catatan | `barang` tidak punya harga beli — harga beli hanya ada di detail pengadaan/penerimaan |

### 3.3 Pengadaan

#### Daftar Pengadaan
- **Tujuan**: melihat semua pesanan ke vendor.
- **Kolom**: ID (`idpengadaan`), Tanggal (kolom `timestamp`), Vendor (`nama_vendor`), Dibuat oleh (`username` dari `user_iduser`), Status (badge dari kode `status` CHAR(1) — arti kode **[?]**; sementara tampilkan kode apa adanya), Subtotal (`subtotal_nilai`), PPN (`ppn`), Total (`total_nilai`), Aksi.
- **Aksi**: Buat pengadaan, Lihat detail; Terima barang (menuju form penerimaan dengan pengadaan terpilih) — hanya muncul bila masih ada sisa **[BE]**.
- **Cari/Filter**: cari ID; filter Vendor, Status, rentang tanggal.
- **View**: tidak ada view transaksi saat ini; daftar membutuhkan join (vendor, user) → query/model baru **[BE]**; view baru bersifat opsional **[DB]** dan dibahas di tahap lain.

#### Buat Pengadaan (form)
- **Header**: Vendor (select vendor aktif, wajib). Tanggal & user otomatis (`timestamp`, `user_iduser`). Status awal **[?]**.
- **Baris item** (tabel dinamis, bisa tambah/hapus): Barang (select barang aktif), Harga satuan (`harga_satuan`, input), Jumlah (`jumlah`), Sub total (`sub_total`, otomatis = harga × jumlah, hanya baca).
- **Ringkasan**: Subtotal (`subtotal_nilai`) = Σ sub total; PPN (`ppn`) dan Total (`total_nilai`) tampil otomatis. Tarif PPN tidak ada di DB **[?]**; tampilan PPN disembunyikan/0 sampai rumus diputuskan.
- **Validasi**: vendor wajib; minimal 1 baris; barang tidak boleh ganda; jumlah bilangan bulat > 0; harga bilangan bulat ≥ 0. Total dihitung ulang di server **[BE]**.
- **Aksi**: Simpan, Batal.

#### Detail Pengadaan
- Kartu header (ID, tanggal, vendor, pembuat, status) + tabel item (Barang, Harga satuan, Jumlah, Sub total) + ringkasan nilai.
- Tambahan berguna: kolom "Sudah diterima" dan "Sisa" per item (hasil penjumlahan `detail_penerimaan.jumlah_terima` **[BE]**) + daftar penerimaan terkait (tautan).
- Aksi: Terima barang, Kembali, Cetak. Tidak ada Edit/Hapus setelah ada penerimaan **[?]**.

### 3.4 Penerimaan

#### Daftar Penerimaan
- **Kolom**: ID (`idpenerimaan`), Tanggal (`created_at`), Pengadaan (`idpengadaan` + nama vendor), Diterima oleh (`username`), Status (kode `status` CHAR(1) **[?]**), Aksi.
- **Cari/Filter**: cari ID penerimaan/pengadaan; filter Vendor, Status, tanggal.

#### Buat Penerimaan (dari pengadaan)
- **Langkah 1**: pilih Pengadaan (daftar pengadaan yang masih memiliki sisa; atau datang dari tombol "Terima barang").
- **Langkah 2**: tabel item otomatis terisi dari `detail_pengadaan`: Barang, Dipesan (`jumlah`), Sudah diterima, Sisa, **Jumlah diterima** (`jumlah_terima`, input), Harga satuan terima (`harga_satuan_terima`, awalnya = harga pengadaan, boleh diubah **[?]**), Sub total terima (`sub_total_terima`, otomatis).
- **Validasi**: minimal 1 baris berjumlah > 0; `jumlah_terima` ≤ Sisa (penerimaan melebihi pesanan diperbolehkan atau tidak **[?]**); barang hanya yang ada di pengadaan terkait.
- **Dampak stok**: penambahan stok dari penerimaan harus terjadi; mekanismenya diputuskan di tahap terpisah. Frontend hanya perlu menampilkan hasilnya di Kartu Stok dan kolom "Stok saat ini". Penerimaan parsial didukung struktur (satu pengadaan boleh punya banyak penerimaan).
- **Aksi**: Simpan, Batal.

#### Detail Penerimaan
- Header + item (Barang, Jumlah terima, Harga satuan terima, Sub total terima) + daftar retur terkait + tautan ke pengadaan. Aksi: Buat retur dari penerimaan ini, Cetak.

### 3.5 Penjualan

#### Daftar Penjualan
- **Kolom**: ID (`idpenjualan`), Tanggal (`created_at`), Kasir (`username`), Margin (persen dari `idmargin_penjualan`), Subtotal, PPN, Total, Aksi.
- **Cari/Filter**: cari ID; filter Kasir, rentang tanggal.
- **Aksi**: Buat penjualan, Lihat detail/struk. Tidak ada Edit/Hapus (struktur DB tidak mendukung pembatalan/retur penjualan — **[DB][?]** bila dibutuhkan).

#### Buat Penjualan (kasir)
- **Layout**: kiri = pencarian & daftar barang aktif (filter Jenis), kanan = keranjang.
- **Keranjang**: Barang, Harga satuan (otomatis `barang.harga`, hanya baca), Jumlah (±), Subtotal (`subtotal`), hapus baris. Ringkasan: Subtotal, PPN, Total (rumus PPN **[?]**).
- **Info**: tampilkan margin aktif yang akan tercatat di `idmargin_penjualan`; bila tidak ada margin aktif, tombol Simpan dinonaktifkan dengan pesan.
- **Validasi**: minimal 1 baris; jumlah bilangan bulat > 0; barang aktif; stok mencukupi atau tidak **[?]**.
- **Setelah simpan**: pindah ke Detail/Struk dengan notifikasi berhasil; tombol Cetak.

#### Detail/Struk Penjualan
- Header + item (Barang, Harga satuan, Jumlah, Subtotal) + Subtotal/PPN/Total + tampilan siap cetak.

### 3.6 Retur

Struktur DB: `retur` mengacu ke `penerimaan` (jadi ini **retur pembelian ke vendor**); `detail_retur` mengacu ke `detail_penerimaan`. Tidak ada kolom status, nilai, atau vendor di tabel retur. **Retur penjualan tidak didukung struktur** **[DB][?]**.

#### Daftar Retur
- **Kolom**: ID (`idretur`), Tanggal (`created_at`), Penerimaan asal (`idpenerimaan`), Vendor (via penerimaan → pengadaan), Dibuat oleh, Jumlah item, Aksi.
- **Cari/Filter**: cari ID/penerimaan; filter Vendor, tanggal.

#### Buat Retur (dari penerimaan)
- Pilih Penerimaan (atau datang dari tombol di Detail Penerimaan) → tabel item dari `detail_penerimaan`: Barang, Jumlah diterima, Sudah diretur, **Jumlah retur** (`jumlah`), **Alasan** (`alasan`, wajib).
- **Validasi**: jumlah retur bilangan bulat > 0 dan ≤ (jumlah diterima − sudah diretur); alasan wajib ≤200 karakter; minimal 1 baris.
- **Dampak stok**: pengurangan stok akibat retur diputuskan di tahap terpisah; frontend menampilkan hasilnya di Kartu Stok.

#### Detail Retur
- Header + item (Barang, Jumlah, Alasan) + tautan ke penerimaan asal.

### 3.7 Persediaan — Kartu Stok (`kartu_stok`)
- **Tujuan**: riwayat barang masuk/keluar dan saldo per barang.
- **Kolom**: Tanggal (`created_at`), Barang (`nama` via `idbarang`), Jenis transaksi (`jenis_transaksi`, kode **[?]** → tampilkan label setelah dipetakan), Masuk (`masuk`), Keluar (`keluar`), Saldo (`stock`), Referensi (`idtransaksi`; menjadi tautan ke penerimaan/penjualan/retur **hanya setelah arti kode jenis diketahui [?]**).
- **Filter**: Barang (wajib untuk tampilan "kartu" per barang), Jenis transaksi, rentang tanggal.
- **Tambahan**: ringkasan "Stok saat ini per barang" di bagian atas atau tab kedua.
- **Aksi**: tidak ada tambah/edit/hapus (data diisi oleh proses transaksi). Hanya baca.
- **Catatan**: `kartu_stok.idtransaksi` bertipe INT tanpa FK dan lebih sempit dari id sumber (BIGINT) **[DB][?]**.

### 3.8 Laporan

Setiap laporan: filter periode (+ filter relevan), tabel hasil, baris total, tombol Cetak. Hanya memakai kolom yang ada. Ekspor (CSV/PDF) **[?]**.

| Laporan | Isi | Filter |
|---|---|---|
| Penjualan | Daftar penjualan + total per periode; rekap per barang (dari `detail_penjualan`) | Periode, Kasir, Barang |
| Pengadaan | Daftar pengadaan + total per vendor | Periode, Vendor, Status |
| Penerimaan | Daftar penerimaan + nilai terima | Periode, Vendor |
| Retur | Daftar retur + alasan | Periode, Vendor |
| Stok | Stok saat ini + mutasi periode | Barang, Jenis, Periode |

---

## 4. Rancangan Kolom Tabel — ringkasan sumber

| Halaman | Sumber data | Perlu query/join baru [BE]? |
|---|---|---|
| Role, User, Satuan, Vendor, Margin, Barang | VIEW yang sudah ada (`v_role`, `v_user`, `v_satuan`, `v_vendor`, `v_margin_penjualan`, `v_barang`) | Ya hanya untuk kolom hitung (jumlah user/barang, stok saat ini) |
| Daftar Pengadaan/Penerimaan/Penjualan/Retur | Tabel header + join `user`, `vendor`, `margin_penjualan`, `pengadaan` | Ya (belum ada model/view) |
| Detail transaksi | Header + tabel detail + join `barang` | Ya |
| Kartu Stok | `kartu_stok` join `barang` | Ya |
| Dashboard/Laporan | Agregat | Ya |

Catatan: view-view yang ada memakai INNER JOIN, sehingga baris dengan FK `NULL` tidak tampil. Pada halaman master, pastikan form tidak mengizinkan FK kosong (wajib pilih Role/Satuan/User).

---

## 5. Rancangan Form Tambah & Edit

**Pola master**: tombol "+ Tambah" di kepala halaman → modal form; tombol "Edit" per baris → modal form terisi. Validasi sisi server **[BE]** + pesan di bawah field; bila gagal, modal terbuka kembali dengan isian dan pesan error. Tombol: Simpan (utama), Batal.

**Pola transaksi**: halaman penuh dengan tiga bagian — (1) kartu header, (2) tabel baris item dinamis, (3) ringkasan nilai + tombol Simpan/Batal. Cegah submit ganda.

**Aturan input umum**: label di atas field, penanda wajib, bilangan bulat untuk jumlah dan harga (kolom DB bertipe INT), pemisah ribuan hanya pada tampilan (tidak pada nilai kirim).

---

## 6. Rancangan Proses Transaksi (alur pengguna)

```
Master siap (vendor, barang, satuan, margin aktif, user)
        │
        ▼
[Buat Pengadaan] → Detail Pengadaan ──► [Buat Penerimaan] (boleh parsial, berulang)
                                              │            │
                                              ▼            ▼
                                       Detail Penerimaan  Kartu Stok (masuk)
                                              │
                                              ▼
                                        [Buat Retur] ──► Kartu Stok (keluar)

[Buat Penjualan] (kasir) ──► Detail/Struk ──► Kartu Stok (keluar)
```

Langkah per proses:
1. **Pengadaan**: pilih vendor → tambah barang, harga, jumlah → lihat total → simpan → halaman detail.
2. **Penerimaan**: dari detail pengadaan klik "Terima barang" → isi jumlah diterima per barang (dibatasi sisa) → simpan → detail penerimaan.
3. **Penjualan**: pilih barang → atur jumlah → total → simpan → struk.
4. **Retur**: dari detail penerimaan klik "Buat retur" → pilih barang, jumlah, alasan → simpan.
5. **Persediaan**: pengguna memantau di Kartu Stok.

Setiap proses menyimpan header dan detail sebagai satu kesatuan (berhasil semua atau gagal semua) **[BE]**. Perhitungan total dilakukan ulang di server.

**Keputusan yang masih terbuka [?]**: kode status pengadaan/penerimaan; kode `jenis_transaksi`; rumus/tarif PPN; stok boleh negatif; penerimaan boleh melebihi pesanan; peran margin terhadap `barang.harga`; kebutuhan retur penjualan.

---

## 7. Hubungan Antarhalaman

| Dari | Ke | Cara |
|---|---|---|
| Daftar Pengadaan | Detail Pengadaan, Buat Pengadaan | klik baris / tombol |
| Detail Pengadaan | Buat Penerimaan (membawa id pengadaan), Detail Penerimaan terkait | tombol / tautan |
| Detail Penerimaan | Pengadaan asal, Buat Retur (membawa id penerimaan), Detail Retur terkait | tautan / tombol |
| Detail Retur | Penerimaan asal | tautan |
| Daftar/Detail Penjualan | Buat Penjualan | tombol |
| Barang | Kartu Stok (filter barang terpilih) | tautan "Lihat kartu stok" |
| Vendor | Daftar Pengadaan (filter vendor) | tautan |
| Kartu Stok | Penerimaan/Penjualan/Retur sumber | tautan, setelah kode jenis diketahui **[?]** |
| Dashboard | Semua daftar & form cepat | kartu/tautan |
| Margin Penjualan | Buat Penjualan menampilkan margin aktif | informasi |

Breadcrumb di setiap halaman: `Grup › Halaman › (Detail #id)`.

---

## 8. Komponen UI yang Dapat Digunakan Ulang

Kondisi sekarang (dari source): satu layout dengan navbar atas, tabel, badge `lencana`, state kosong, token warna di `:root`. **Belum ada** gaya/komponen untuk: sidebar, tombol, form, modal, alert, pagination, kartu, filter bar.

Penamaan kelas CSS mengikuti konvensi Indonesia yang sudah ada (`tabel-bungkus`, `lencana`, `kosong`, `angka`, `aktif`).

| Komponen | Fungsi | Dipakai oleh |
|---|---|---|
| Layout aplikasi (sidebar + navbar + area konten) | Kerangka semua halaman | Semua |
| Sidebar bergrup (dari konfigurasi menu) | Navigasi | Semua |
| Page heading (judul + breadcrumb + tombol aksi utama) | Konsistensi kepala halaman | Semua |
| Tabel data + kolom `angka` + baris aksi | Daftar | Semua daftar |
| Filter bar (cari + select + rentang tanggal + Reset) | Pencarian/filter | Semua daftar |
| Pagination | Navigasi data banyak | Semua daftar |
| Badge status (pemetaan kode → teks/warna) | Aktif/Nonaktif, status transaksi | Master, transaksi |
| Field form (input, select, angka, textarea, pesan error) | Isian | Semua form |
| Modal form | Tambah/edit master | 6 master |
| Modal konfirmasi | Hapus/nonaktifkan/aktifkan | Master, aksi penting |
| Notifikasi (flash berhasil/gagal/peringatan) | Umpan balik | Semua |
| Editor baris item (tambah/hapus baris, hitung sub total & total) | Form transaksi | Pengadaan, Penerimaan, Penjualan, Retur |
| Ringkasan nilai (subtotal/PPN/total) | Transaksi | Pengadaan, Penjualan |
| Kartu header transaksi | Detail | 4 detail transaksi |
| Format angka (Rp, persen, tanggal d/m/Y H:i) | Konsistensi tampilan | Semua |
| Kartu ringkasan | Dashboard | Dashboard |
| Tampilan cetak (struk/laporan) | Cetak | Penjualan, Laporan |
| Empty state | Data kosong | Semua |

Kekurangan desain saat ini & rekomendasi (belum diubah):
1. Lebar konten dibatasi 1000px dan navigasi hanya di atas → saat ada sidebar, atur ulang lebar; tabel transaksi butuh ruang lebih.
2. Navbar mendatar tidak muat untuk >6 menu → sidebar bergrup.
3. Menu hard-coded di Blade → satu sumber konfigurasi.
4. Belum ada gaya tombol/form/alert/modal → tambahkan memakai token warna yang sudah ada (`--aksen`, `--garis`, `--ok`; tambah token `--bahaya`, `--peringatan` bila diperlukan).
5. Tanpa JavaScript sama sekali → editor baris item dan modal membutuhkan JS ringan; pilihan: JS biasa (tanpa dependency) atau library kecil — **[?]**. Vite/Tailwind sudah terpasang tetapi tidak dipakai; putuskan tetap CSS statis atau pindah ke Vite **[?]**.
6. Tidak ada pagination, `->get()` memuat semua baris → wajib sebelum data transaksi tumbuh **[BE]**.
7. Aksesibilitas: pertahankan `:focus-visible`; tambahkan label field, fokus-trap pada modal, kontras badge.
8. Responsif: sidebar menjadi menu geser; tabel tetap `overflow-x:auto`.

---

## 9. Prioritas Pengerjaan

| Prioritas | Pekerjaan | Alasan |
|---|---|---|
| **P0** | Fondasi UI: layout sidebar/navbar, page heading, tombol, form, modal konfirmasi, alert, pagination, filter bar | Semua halaman memakainya; menghindari pengerjaan ulang |
| **P0** | Autentikasi (login, user/role di navbar) **[BE]** | Navbar, `iduser` otomatis di margin/transaksi, pembatasan menu |
| **P1** | Master Data CRUD, urutan: **Role → Satuan → User → Vendor → Margin Penjualan → Barang** | Transaksi bergantung pada master; urutan mengikuti FK |
| **P2** | Pengadaan → Penerimaan | Rantai pasok; penerimaan butuh pengadaan |
| **P2** | Penjualan | Butuh barang & margin aktif; idealnya setelah stok punya sumber (penerimaan) |
| **P3** | Retur | Butuh penerimaan |
| **P3** | Kartu Stok | Read-only; paling bermakna setelah ada transaksi yang mengisinya |
| **P4** | Dashboard, Laporan | Memakai data semua modul |
| **P5** | Penyempurnaan UI/UX | Setelah fungsi selesai |

Karena Anda meminta frontend + CRUD dahulu: halaman daftar & form transaksi dapat dibuat lebih awal dengan data uji, tetapi **dampaknya ke stok baru benar** setelah tahap terpisah dikerjakan.

## 10. Ketergantungan Antarfitur

| Fitur | Bergantung pada |
|---|---|
| Semua halaman | Layout & komponen dasar (P0) |
| `iduser` otomatis (margin, transaksi) | Autentikasi |
| Form User | Master Role |
| Form Barang | Master Satuan |
| Form Pengadaan | Master Vendor, Barang |
| Form Penerimaan | Pengadaan (dengan item) |
| Form Retur | Penerimaan (dengan item) |
| Form Penjualan | Barang, Margin aktif (dan stok bila ditolak saat kurang **[?]**) |
| Kolom "Stok saat ini" di Barang, Kartu Stok, Dashboard stok, Laporan Stok | Sumber isi `kartu_stok` (tahap terpisah) |
| Tautan referensi di Kartu Stok | Pemetaan kode `jenis_transaksi` **[?]** |
| Badge status transaksi | Pemetaan kode status **[?]** |
| Perhitungan PPN | Keputusan tarif **[?]** |
| Laporan | Modul sumber terkait |

---

## 11. Ringkasan Modul

| Modul | Halaman | Fitur | Sumber Data | Prioritas |
|---|---|---|---|---|
| Fondasi | Layout, sidebar, navbar, komponen | Navigasi, notifikasi, modal, tabel, form, pagination | — | P0 |
| Autentikasi | Login | Masuk/keluar, tampil user & role | `user`, `role` | P0 |
| Dashboard | Dashboard | Ringkasan penjualan, margin aktif, pengadaan menunggu, stok terendah | Agregat `penjualan`, `margin_penjualan`, `pengadaan`, `kartu_stok` | P4 |
| Master Data | Role | CRUD, cegah hapus bila dipakai | `v_role` / `role` | P1 |
| Master Data | Satuan | CRUD + aktif/nonaktif, filter status | `v_satuan` / `satuan` | P1 |
| Master Data | User | CRUD, hash password, filter role | `v_user` / `user` | P1 |
| Master Data | Vendor | CRUD + aktif/nonaktif, filter badan hukum | `v_vendor` / `vendor` | P1 |
| Master Data | Margin Penjualan | Tambah/edit, aktifkan eksklusif | `v_margin_penjualan` / `margin_penjualan` | P1 |
| Master Data | Barang | CRUD + aktif/nonaktif, filter jenis/satuan | `v_barang` / `barang` | P1 |
| Pengadaan | Daftar, Buat, Detail | Form header + item, total, filter, status **[?]** | `pengadaan`, `detail_pengadaan`, `vendor`, `user`, `barang` | P2 |
| Penerimaan | Daftar, Buat (dari pengadaan), Detail | Terima parsial, validasi sisa, tautan retur | `penerimaan`, `detail_penerimaan`, `pengadaan`, `detail_pengadaan` | P2 |
| Penjualan | Daftar, Buat (kasir), Detail/Struk | Keranjang, total, cetak struk | `penjualan`, `detail_penjualan`, `barang`, `margin_penjualan` | P2 |
| Retur | Daftar, Buat (dari penerimaan), Detail | Pilih item diterima, jumlah ≤ sisa, alasan | `retur`, `detail_retur`, `penerimaan`, `detail_penerimaan` | P3 |
| Persediaan | Kartu Stok | Riwayat masuk/keluar/saldo, filter barang/jenis/periode (read-only) | `kartu_stok`, `barang` | P3 |
| Laporan | 5 laporan | Filter periode, total, cetak | Tabel transaksi + master | P4 |

---

## 12. Rangkuman Kebutuhan Backend & Database (bukan dikerjakan sekarang)

**[BE]** — route/controller/model tabel asli per modul; Form Request validasi; autentikasi + provider khusus tabel `user`; pagination & pencarian; query agregat (stok, jumlah relasi, dashboard); penyimpanan header+detail satu kesatuan; konfigurasi menu.

**[DB]/[?]** — (tanpa dikerjakan di tahap ini) definisi kode `status` pengadaan/penerimaan, kode `jenis_transaksi`, tipe `kartu_stok.idtransaksi`; constraint UNIQUE/NOT NULL/CHECK yang ingin ditegakkan; kebutuhan retur penjualan; sumber tarif PPN.

## 13. Pertanyaan Konfirmasi Sebelum Implementasi

1. Kode & arti `pengadaan.status`, `penerimaan.status`, `kartu_stok.jenis_transaksi`, serta kode vendor nonaktif.
2. Tarif/rumus PPN; apakah `ppn` ditampilkan sekarang.
3. Peran margin vs `barang.harga`.
4. Stok boleh negatif / penjualan ditolak bila stok kurang.
5. Penerimaan boleh melebihi pesanan; harga terima boleh berbeda dari harga pesan.
6. Retur penjualan diperlukan atau tidak.
7. Pemetaan role → menu.
8. Hapus untuk master ber-`status`: hanya nonaktifkan, atau hapus tersedia bila belum dipakai.
9. JavaScript ringan polos atau library; tetap CSS statis atau pindah ke Vite.
10. Perlu ekspor CSV/PDF di laporan.
11. ~~Nama aplikasi final~~ — sudah diputuskan: **Daily Bun**.
