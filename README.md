# Breeze — Pencatatan Stok dan Rekomendasi Restock Warung

Breeze adalah aplikasi web berbahasa Indonesia untuk membantu pemilik warung kecil mencatat persediaan dan transaksi harian, memantau barang yang perlu diperiksa, serta menyusun rekomendasi belanja berdasarkan penjualan terbaru.

## Latar belakang masalah

Pencatatan stok secara manual menyulitkan pemilik warung mengetahui jumlah barang terkini dan memperkirakan kapan harus kulakan. Stok yang habis dapat menghambat penjualan, sedangkan pembelian berlebih mengikat modal. Aplikasi ini menyimpan perubahan stok dan transaksi secara terstruktur, lalu memberi perkiraan kebutuhan restock menggunakan data penjualan selama tujuh hari kalender terakhir.

## Fitur

- Registrasi warung dan pemilik, login, sesi ingat saya, dan logout.
- Ringkasan kondisi stok dan daftar barang yang paling perlu diperhatikan.
- Pengelolaan barang: tambah, ubah, hapus, cari, dan filter berdasarkan status.
- Pencatatan transaksi kulakan (stok masuk) dan barang terjual (stok keluar), termasuk lebih dari satu barang dalam satu catatan.
- Validasi agar penjualan tidak mengurangi stok di bawah nol; satu catatan multi-barang disimpan secara transaksional.
- Rekomendasi jumlah dan estimasi biaya restock dari rata-rata penjualan per hari selama tujuh hari terakhir.
- Target stok dalam hari, dapat diubah dari layar Belanja maupun Profil. Keduanya menggunakan satu pengaturan per akun.
- Profil berisi identitas warung/pemilik, menu ubah identitas, ganti kata sandi, dan keluar.

## Teknologi

- PHP 8.2 atau lebih baru, Laravel 12, Blade, dan Eloquent.
- SQLite secara bawaan (dapat dikonfigurasi untuk database lain yang didukung Laravel).
- Node.js/npm, Vite, dan Tailwind CSS 4 untuk aset antarmuka.
- Pest untuk pengujian.

## Instalasi dan menjalankan aplikasi

Pastikan PHP beserta ekstensi yang dibutuhkan Laravel, Composer, Node.js, dan npm telah tersedia.

1. Pasang dependensi PHP dan salin konfigurasi:

   ```sh
   composer install
   cp .env.example .env
   php artisan key:generate
   ```

   Di Windows PowerShell, gunakan `Copy-Item .env.example .env` sebagai pengganti `cp`.

2. Secara bawaan `.env.example` menggunakan SQLite. Buat berkas database kosong `database/database.sqlite` jika belum ada:

   ```sh
   touch database/database.sqlite
   ```

   Di Windows PowerShell: `New-Item -ItemType File -Path database/database.sqlite` (jalankan hanya jika berkas belum ada). Untuk memakai database lain, sesuaikan variabel `DB_*` di `.env` dan siapkan databasenya.

3. Jalankan migrasi, pasang dependensi frontend, dan bangun aset:

   ```sh
   php artisan migrate
   npm install
   npm run build
   ```

4. (Opsional) Isi data contoh, lalu jalankan server:

   ```sh
   php artisan db:seed
   php artisan serve
   ```

   Buka URL yang ditampilkan Artisan (biasanya `http://127.0.0.1:8000`). Seeder membuat akun contoh `sari@warungberkah.test` dengan kata sandi `password`; gunakan hanya untuk pengembangan lokal.

   Untuk pengembangan frontend dengan hot reload, jalankan `npm run dev` di terminal terpisah bersama `php artisan serve`.

### Pengujian

```sh
php artisan test
npm run build
```

Checklist pengujian manual tersedia di [docs/checklist-pengujian-manual.md](docs/checklist-pengujian-manual.md), dan bahan tanya-jawab presentasi tersedia di [docs/tanya-jawab-dosen.md](docs/tanya-jawab-dosen.md).

## Alur sistem

1. **Daftar/masuk.** Pemilik membuat akun dengan nama warung, nama pemilik, email unik, dan kata sandi. Setelah berhasil masuk, aplikasi membuat sesi terautentikasi.
2. **Kelola barang.** Pemilik menambahkan nama barang, satuan, harga beli/jual, stok saat ini, dan stok minimum. Daftar hanya menampilkan data milik akun aktif; nama barang harus unik di dalam akun tersebut. Status berasal dari stok: `Habis` jika stok nol, `Menipis` jika stok tidak melebihi stok minimum, selain itu `Aman`.
3. **Catat perubahan.** Kulakan menambah stok dan membuat transaksi `masuk`; barang terjual mengurangi stok dan membuat transaksi `keluar`. Untuk catatan berisi beberapa barang, seluruh perubahan dilakukan dalam satu transaksi database agar tidak tersimpan sebagian jika ada validasi yang gagal.
4. **Pantau dan rekomendasikan.** Beranda menampilkan ringkasan stok dan tiga barang paling mendesak. Layar Belanja menghitung rata-rata harian tiap barang dari jumlah transaksi `keluar` pada hari ini dan enam hari sebelumnya (total dibagi tujuh, termasuk hari tanpa penjualan). Transaksi `masuk` tidak dihitung.
5. **Atur target dan profil.** Target stok adalah jumlah hari stok yang ingin dijaga. Nilai ini disimpan satu kali per akun, sehingga perubahan di Profil langsung digunakan Belanja dan Beranda, dan sebaliknya. Profil juga mengizinkan pemilik mengubah identitas/email dan mengganti kata sandi dengan verifikasi kata sandi saat ini.
6. **Keluar.** Aplikasi mengakhiri sesi dan mengarahkan pemilik ke halaman masuk.

### Perhitungan rekomendasi

Untuk setiap barang:

```text
rata-rata harian = jumlah transaksi keluar dalam 7 hari kalender / 7
jumlah beli = max(0, ceil((rata-rata harian × target hari) − stok saat ini))
biaya = jumlah beli × harga beli
```

Rekomendasi yang perlu dibeli diurutkan berdasarkan perkiraan waktu stok tersisa, lalu nama barang. Estimasi belanja merupakan jumlah biaya seluruh rekomendasi. Daftar centang “sudah dibeli” pada layar Belanja hanya mengubah tampilan dan estimasi di halaman saat itu; saat ini centang tersebut tidak menyimpan transaksi kulakan dan tidak mengubah stok.

## Struktur data

Tabel aplikasi dibuat oleh migrasi `database/migrations/2026_11_20_000000_create_inventory_tables_and_add_nama_warung_to_users.php`:

| Tabel | Kolom penting | Relasi/kegunaan |
| --- | --- | --- |
| `users` | `id`, `name`, `nama_warung`, `email`, `password`, `remember_token`, timestamps | Akun pemilik; email unik. Memiliki banyak barang dan satu pengaturan. |
| `barang` | `id`, `user_id`, `nama`, `satuan`, `harga_beli`, `harga_jual`, `stok`, `stok_minimum`, timestamps | Barang milik pengguna. Nama unik per `user_id`; harga disimpan sebagai bilangan bulat, jumlah stok dengan dua angka desimal. Penghapusan akun menghapus barang terkait. |
| `transaksi` | `id`, `barang_id`, `jenis`, `jumlah`, `tanggal`, timestamps | Riwayat perubahan stok untuk satu barang. `jenis` bernilai `masuk` atau `keluar`; terindeks menurut barang, jenis, dan tanggal. Penghapusan barang menghapus transaksinya. |
| `pengaturan` | `id`, `user_id`, `hari_target_stok`, timestamps | Satu target stok per pengguna; `hari_target_stok` bilangan bulat positif dan default 3. |

Laravel juga memakai tabel infrastruktur bawaan untuk sesi/cache/antrian sesuai konfigurasi. Model utama berada di `app/Models`; rumus rekomendasi ada di `app/Services/RestockService.php`; rute web ada di `routes/web.php`.

## Struktur direktori penting

```text
app/
  Http/Controllers/   Logika endpoint aplikasi
  Http/Requests/      Validasi input
  Models/             User, Barang, Transaksi, Pengaturan
  Services/           Perhitungan restock
database/
  migrations/         Skema database
  seeders/            Data demo
resources/
  views/              Halaman Blade dan komponen
  css/                Tailwind CSS
  js/                 Interaksi formulir dan daftar belanja
routes/web.php        Rute aplikasi
tests/Feature/        Pengujian alur fitur
docs/                 Checklist manual dan tanya-jawab presentasi
```
