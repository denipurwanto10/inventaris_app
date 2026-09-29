# Gudang.in — Sistem Inventaris & Manajemen Aset Kantor
 
Aplikasi Laravel untuk mengelola inventaris kantor: barang, kategori, supplier, multi-gudang, stok masuk/keluar/pindah, peminjaman dengan alur persetujuan, maintenance, barcode/QR, notifikasi, manajemen pengguna, dan laporan.

## Fitur
 
- **Barang** — data master barang lengkap dengan kode, barcode otomatis, foto, lokasi, gudang, harga.
- **Kategori & Supplier** — CRUD cepat lewat modal, tanpa reload halaman (Alpine.js).
- **Multi-Gudang** — kelola beberapa gudang/lokasi, lihat stok per gudang di setiap barang, dan **pindahkan stok antar gudang** dengan riwayat lengkap.
- **Stok** — dihitung otomatis dari histori Barang Masuk, Barang Keluar & Perpindahan Gudang (bukan input manual).
- **Barang Masuk / Keluar** — pencatatan transaksi dengan nomor referensi otomatis, pilihan gudang, update stok real-time & validasi stok tidak boleh minus.
- **Barcode / QR Code** — setiap barang punya barcode unik, bisa dicetak sebagai label, dan bisa dipindai lewat kamera HP/laptop untuk pencarian instan.
- **Peminjaman + Alur Persetujuan** — karyawan mengajukan peminjaman, admin **menyetujui atau menolak** sebelum stok dikurangi; lacak status (menunggu/dipinjam/terlambat/dikembalikan/ditolak), proses pengembalian per-item dengan kondisi barang.
- **Maintenance** — perbaikan, servis rutin, kalibrasi; lacak biaya & status.
- **Notifikasi** — lonceng notifikasi otomatis untuk stok menipis, peminjaman jatuh tempo/terlambat, dan pengajuan yang menunggu persetujuan.
- **Manajemen Pengguna & Hak Akses** — admin dapat menambah/mengubah/menonaktifkan pengguna dan mengatur peran (Admin/Staff). Akun nonaktif otomatis tidak bisa login.
- **Laporan** — ringkasan stok, nilai barang, transaksi per periode, stok per gudang, barang paling laku, barang menipis; **export ke Excel (CSV)** untuk laporan stok/transaksi/peminjaman, dan siap dicetak ke PDF lewat browser.
- **Dasbor Analitik** — KPI ringkas, tren arus stok 6 bulan, komposisi nilai stok per kategori (grafik donat), barang paling sering keluar, dan distribusi stok antar gudang.

## Tumpukan Teknologi

- Laravel 11 (PHP 8.2+)
- MySQL / MariaDB
- Blade + Tailwind CSS (CDN) + Alpine.js — tanpa build step, langsung jalan
- Chart.js (grafik dasbor), JsBarcode (cetak barcode), html5-qrcode (pindai kamera)

## Cara Menjalankan

1. **Buat proyek Laravel baru**, lalu salin folder `app`, `bootstrap`, `database`, `routes`, dan `resources` dari paket ini ke dalamnya (timpa file yang sama):

   ```bash
   composer create-project laravel/laravel gudang-in "^11.0"
   cd gudang-in
   # salin/timpa app/, bootstrap/, database/, routes/, resources/ dari paket yang diberikan
   ```

   > Paket ini sudah menyertakan `bootstrap/app.php` lengkap dengan registrasi middleware `admin`, `EnsureUserIsActive`, dan `SyncNotifications`, jadi untuk proyek baru Anda tinggal timpa saja. Jika proyek Anda **bukan** proyek baru dan `bootstrap/app.php` sudah Anda modifikasi sebelumnya, jangan ditimpa — cukup gabungkan manual bagian `withMiddleware(...)` berikut ke file Anda:
   >
   > ```php
   > ->withMiddleware(function (Middleware $middleware) {
   >     $middleware->alias([
   >         'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
   >     ]);
   >     $middleware->appendToGroup('web', [
   >         \App\Http\Middleware\EnsureUserIsActive::class,
   >         \App\Http\Middleware\SyncNotifications::class,
   >     ]);
   > })
   > ```
   >
   > - `admin` melindungi halaman Gudang & Pengguna agar hanya bisa diakses oleh peran Admin.
   > - `EnsureUserIsActive` otomatis mengeluarkan pengguna yang akunnya dinonaktifkan admin.
   > - `SyncNotifications` menghasilkan notifikasi (stok menipis, jatuh tempo, menunggu persetujuan) otomatis di setiap request, dan membagikan jumlah notifikasi belum dibaca ke lonceng di header.

2. **Konfigurasi database** di file `.env`:

   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=gudang_in
   DB_USERNAME=root
   DB_PASSWORD=
   ```

   Buat database kosong bernama `gudang_in` terlebih dahulu (`CREATE DATABASE gudang_in;`). File `database/inventaris_schema.sql` disertakan hanya sebagai referensi struktur — Anda **tidak perlu** mengimpornya secara manual karena migration akan membuat semuanya otomatis.

3. **Install dependency, migrasi, & isi data contoh:**

   ```bash
   composer install
   php artisan key:generate
   php artisan migrate --seed
   ```

   Seeder aman dijalankan berulang kali (tidak akan gagal karena data duplikat) — jika Anda pernah menjalankannya sebelumnya dan ingin data contoh yang bersih dengan tabel gudang baru, gunakan `php artisan migrate:fresh --seed` sebagai gantinya.

4. **Jalankan aplikasi:**

   ```bash
   php artisan serve
   ```

   Buka `http://127.0.0.1:8000` di browser.

5. **Login dengan akun contoh** (dibuat oleh seeder):

   | Role  | Email             | Password |
   |-------|-------------------|----------|
   | Admin | admin@gudang.in   | password |
   | Staff | staff@gudang.in   | password |

   ⚠️ Segera ganti password ini sebelum dipakai di lingkungan produksi.

## Peran & Hak Akses

| Aksi                                              | Admin | Staff |
|----------------------------------------------------|:-----:|:-----:|
| Kelola barang, kategori, supplier, stok masuk/keluar | ✅ | ✅ |
| Ajukan peminjaman                                  | ✅ | ✅ |
| **Setujui / tolak peminjaman**                     | ✅ | ❌ |
| Pindahkan stok antar gudang                        | ✅ | ✅ |
| **Kelola gudang (tambah/ubah/hapus)**              | ✅ | ❌ |
| **Kelola pengguna & peran**                        | ✅ | ❌ |
| Lihat laporan & dasbor                             | ✅ | ✅ |

## Alur Peminjaman (Baru)

1. Pengguna mengisi form **Peminjaman Baru** → status tercatat `menunggu`, **stok belum berkurang**.
2. Admin membuka detail peminjaman, memilih **Setujui** (stok langsung dikurangi dari gudang barang) atau **Tolak** (dengan alasan opsional).
3. Setelah disetujui, alur pengembalian berjalan seperti biasa (per-item, dengan kondisi barang).
4. Notifikasi otomatis muncul untuk admin saat ada pengajuan baru, dan untuk semua pengguna saat peminjaman mendekati/melewati jatuh tempo.

## Struktur Database Singkat

- `categories`, `suppliers`, `warehouses` — data master pendukung.
- `items` — barang, menyimpan `stock` (total) yang selalu disinkronkan lewat transaksi, plus `warehouse_id` sebagai gudang utama barang.
- `item_stocks` — rincian stok per gudang untuk tiap barang (`item_id` + `warehouse_id` + `qty`), dijaga tetap sinkron dengan `items.stock`.
- `stock_movements` — satu tabel untuk Barang Masuk & Barang Keluar (dibedakan lewat kolom `type`), kini juga menyimpan `warehouse_id`.
- `stock_transfers` — riwayat perpindahan stok antar gudang.
- `loans` + `loan_items` — kepala & rincian peminjaman, dengan kolom `approval_status`, `approved_by`, `approved_at`, `rejection_reason` untuk alur persetujuan.
- `maintenances` — riwayat servis/perbaikan per barang.
- `app_notifications` — notifikasi in-app (stok menipis, jatuh tempo, peminjaman baru), dibuat otomatis dan idempoten lewat `unique_key`.

## Export Laporan

Halaman **Laporan** menyediakan tombol **Export Excel (CSV)** untuk tiga jenis laporan (Stok Barang, Transaksi Periode Ini, Peminjaman) — file CSV terbuka langsung di Excel/Google Sheets. Untuk PDF, gunakan tombol **Cetak / Simpan PDF** yang memakai dialog cetak bawaan browser (pilih "Save as PDF"), sehingga tidak perlu library PHP tambahan di server.

## Catatan Desain

Sidebar bisa disembunyikan sepenuhnya lewat tombol panah di pojok atasnya (posisi tersimpan di browser), dan setiap grup menu (Ringkasan, Data Master, dst.) bisa dilipat/dibuka sendiri-sendiri — hanya grup yang sedang aktif yang otomatis terbuka saat halaman dimuat, supaya sidebar tidak penuh sesak.

Tampilan sengaja dibuat dengan palet warna khas (hijau lumut gelap + aksen kuning keemasan), tipografi Plus Jakarta Sans, dan gaya panel admin yang padat data — bukan template generik. Semua halaman responsif dari layar HP hingga desktop; sidebar otomatis berubah jadi menu geser (drawer) di layar kecil.

Barcode & QR dibuat/dipindai sepenuhnya di sisi browser (JsBarcode & html5-qrcode), jadi tidak butuh library PHP tambahan atau ekstensi server khusus — cukup izinkan akses kamera saat memindai.
