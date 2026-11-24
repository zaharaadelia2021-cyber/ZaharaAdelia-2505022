# Tanya-Jawab Presentasi Sistem

## 1. Masalah apa yang ingin diselesaikan aplikasi ini?

Aplikasi membantu pemilik warung mencatat stok dan perubahan persediaan secara konsisten. Dengan ringkasan kondisi stok dan perkiraan kebutuhan restock berdasarkan penjualan terbaru, pemilik memiliki dasar yang lebih terukur untuk menentukan barang yang perlu dibeli.

## 2. Bagaimana alur pengguna dari pertama kali memakai aplikasi?

Pemilik mendaftar menggunakan identitas warung dan akun, lalu masuk ke Beranda. Setelah menambahkan barang dan stok awal, pemilik mencatat transaksi kulakan atau penjualan. Beranda menampilkan kondisi stok dan layar Belanja menghitung rekomendasi. Pengaturan target serta data akun dikelola di Profil.

## 3. Bagaimana stok barang berubah ketika transaksi dicatat?

Kulakan dicatat sebagai transaksi `masuk` dan jumlahnya ditambahkan pada stok. Penjualan dicatat sebagai `keluar` dan mengurangi stok. Setiap transaksi terkait dengan satu barang dan memiliki jumlah serta tanggal.

## 4. Apa yang terjadi jika stok tidak cukup untuk transaksi penjualan?

Pengajuan ditolak agar stok tidak menjadi negatif. Untuk pengajuan berisi beberapa barang, pembaruan dilakukan dalam transaksi database; jika satu baris gagal, seluruh pengajuan dibatalkan sehingga tidak ada stok atau transaksi yang tersimpan sebagian.

## 5. Bagaimana aplikasi menentukan rata-rata penjualan?

Untuk setiap barang, aplikasi menjumlahkan kuantitas transaksi keluar pada hari ini dan enam hari sebelumnya, kemudian membaginya dengan tujuh. Hari tanpa penjualan tetap termasuk dalam pembagi, sedangkan transaksi masuk tidak dihitung.

## 6. Bagaimana jumlah rekomendasi restock dihitung?

Aplikasi menghitung `max(0, ceil((rata-rata harian × target hari) − stok saat ini))`. Pembulatan ke atas memastikan stok mencapai kebutuhan target dalam satuan barang bulat. Perkiraan biaya setiap barang adalah jumlah beli dikalikan harga beli; total belanja menjumlahkan biaya barang rekomendasi.

## 7. Mengapa target stok di layar Profil dan Belanja selalu sama?

Kedua layar membaca dan memperbarui satu baris pengaturan yang dimiliki akun (`pengaturan.user_id` unik). Dengan begitu tidak ada nilai target ganda yang dapat berbeda antara Profil, Belanja, dan Beranda.

## 8. Bagaimana aplikasi membatasi akses data antarwarung?

Setiap barang memiliki `user_id`; daftar, pencatatan, dan perubahan barang dibatasi pada pemilik yang sedang masuk. Pemeriksaan otorisasi mencegah pengguna lain melihat atau mengubah barang tersebut. Transaksi terhubung ke barangnya dan pengaturan terhubung ke akun pengguna.

## 9. Apa fungsi stok minimum dan bagaimana status barang ditentukan?

Stok minimum adalah ambang per barang yang dipakai untuk memantau kondisi stok, bukan target hari untuk rekomendasi belanja. Stok nol berstatus Habis; stok positif yang kurang dari atau sama dengan stok minimum berstatus Menipis; stok di atasnya berstatus Aman.

## 10. Apakah centang “Sudah dibeli” mengubah stok atau menyimpan kulakan?

Belum. Centang hanya mengubah tampilan daftar dan estimasi biaya di halaman Belanja saat itu. Untuk mengubah stok, pemilik perlu mencatat kulakan melalui alur Catat. Pemisahan ini menghindari perubahan stok tanpa jumlah dan tanggal transaksi yang terkonfirmasi.
