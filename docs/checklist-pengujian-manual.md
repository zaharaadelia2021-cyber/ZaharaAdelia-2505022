# Checklist Pengujian Manual

Jalankan aplikasi sesuai petunjuk di [README.md](../README.md). Tandai setiap butir setelah hasil aktual sesuai dengan hasil yang diharapkan. Untuk menguji batas kepemilikan, siapkan dua akun berbeda.

## 1. Registrasi, login, dan sesi

- [ ] Daftarkan akun dengan nama warung, nama pemilik, email, serta kata sandi dan konfirmasi yang sama. **Harapan:** akun tersimpan dan pengguna masuk ke Beranda.
- [ ] Coba mendaftar dengan email yang sudah dipakai. **Harapan:** validasi menolak duplikasi dan akun lama tidak berubah.
- [ ] Coba mendaftar dengan kata sandi di bawah 8 karakter atau konfirmasi berbeda. **Harapan:** pesan validasi tampil dan pendaftaran tidak berhasil.
- [ ] Keluar, lalu masuk dengan kredensial yang benar. **Harapan:** pengguna diarahkan ke Beranda.
- [ ] Masuk dengan kata sandi salah dan coba opsi “Ingat saya”. **Harapan:** kredensial salah ditolak; opsi ingat saya tetap dapat dipilih dengan kredensial benar.
- [ ] Saat keluar, pastikan halaman masuk tampil dan halaman yang memerlukan login mengarahkan kembali ke halaman masuk.

## 2. Navigasi, beranda, dan profil

- [ ] Buka Beranda, Barang, Catat, Belanja, dan Profil melalui navigasi bawah. **Harapan:** setiap layar terbuka dan tab yang aktif ditandai.
- [ ] Buka Beranda tanpa barang. **Harapan:** ringkasan dan keadaan kosong tampil tanpa error.
- [ ] Buka Profil. **Harapan:** nama warung, nama pemilik, email, target stok, informasi rata-rata tujuh hari, menu ubah data/ganti kata sandi, serta tombol Keluar terlihat.
- [ ] Ubah target stok dari Profil, lalu buka Belanja dan Beranda. **Harapan:** ketiganya memakai nilai baru yang sama.
- [ ] Ubah target dari Belanja, kembali ke Profil. **Harapan:** nilai Profil ikut berubah.
- [ ] Simpan target 1 dan 65535 hari. **Harapan:** nilai batas diterima. Coba nilai 0, pecahan, kosong, dan lebih dari 65535. **Harapan:** validasi menolak dan target tersimpan tidak berubah.
- [ ] Buka menu Ubah data warung; ubah nama warung dan nama pemilik. **Harapan:** setelah disimpan, identitas terbaru tampil di Profil dan halaman aplikasi terkait.
- [ ] Ubah email ke email lain yang belum dipakai. **Harapan:** email tersimpan. Coba email milik akun lain atau format email tidak valid. **Harapan:** validasi menolak.
- [ ] Pada Ganti kata sandi, masukkan kata sandi saat ini yang salah. **Harapan:** perubahan ditolak. Ulangi dengan kata sandi baru yang terlalu pendek atau konfirmasi berbeda; perubahan tetap ditolak.
- [ ] Ganti kata sandi dengan kata sandi saat ini yang benar dan konfirmasi sesuai. **Harapan:** pesan sukses tampil; kata sandi baru dapat dipakai untuk login dan kata sandi lama tidak dapat dipakai.
- [ ] Tekan Keluar dari Profil. **Harapan:** sesi diakhiri dan pengguna diarahkan ke halaman Masuk.

## 3. Barang dan batas kepemilikan

- [ ] Tambah barang dengan semua data valid. **Harapan:** barang muncul di daftar dengan stok, harga, satuan, dan status sesuai.
- [ ] Tambah barang dengan nama sama pada akun yang sama. **Harapan:** ditolak. Tambah nama sama pada akun kedua. **Harapan:** diperbolehkan dan data akun tidak tercampur.
- [ ] Uji nilai stok/harga negatif, stok desimal lebih dari dua angka, dan kolom wajib kosong. **Harapan:** validasi menolak dan data tidak tersimpan.
- [ ] Ubah nama, harga, stok, satuan, dan stok minimum. **Harapan:** nilai baru tersimpan.
- [ ] Hapus barang. **Harapan:** barang hilang dan tidak lagi ditawarkan untuk pencatatan.
- [ ] Uji status dengan stok 0, stok positif sama dengan stok minimum, dan stok di atas minimum. **Harapan:** berturut-turut Habis, Menipis, dan Aman.
- [ ] Cari barang dengan kata kunci yang cocok dan tidak cocok; gabungkan pencarian dengan filter Menipis/Habis. **Harapan:** hanya hasil yang sesuai ditampilkan dan pencarian kosong memiliki pesan yang jelas.
- [ ] Dari akun kedua, coba buka URL ubah/hapus barang milik akun pertama. **Harapan:** akses ditolak; data barang tidak berubah.

## 4. Catat transaksi

- [ ] Catat kulakan untuk satu barang dengan kuantitas valid. **Harapan:** stok bertambah dan transaksi masuk tercatat.
- [ ] Catat penjualan untuk satu barang. **Harapan:** stok berkurang dan transaksi keluar tercatat.
- [ ] Catat beberapa barang sekaligus, termasuk kuantitas desimal yang sah. **Harapan:** setiap stok berubah dan tiap transaksi tercatat sesuai tanggal/jenis/jumlah.
- [ ] Pada transaksi kulakan, tambahkan dan hapus baris barang; ubah jenis transaksi, barang, dan jumlah. **Harapan:** total pratinjau menghitung ulang memakai harga beli untuk kulakan dan harga jual untuk penjualan.
- [ ] Catat penjualan melebihi stok. **Harapan:** transaksi ditolak, stok tidak negatif, dan tidak ada baris lain dari pengajuan multi-barang yang tersimpan sebagian.
- [ ] Coba kuantitas nol/negatif, tanggal tidak valid, barang duplikat, dan barang milik akun lain. **Harapan:** validasi menolak pengajuan.

## 5. Rekomendasi belanja dan rata-rata tujuh hari

- [ ] Buka Belanja ketika belum ada barang yang perlu direstock. **Harapan:** keadaan stok mencukupi dan rekomendasi kosong tampil.
- [ ] Dengan data penjualan yang diketahui, cek rata-rata, jumlah beli, dan estimasi biaya. **Harapan:** rata-rata memakai total transaksi keluar dibagi 7; jumlah beli memakai target hari, stok saat ini, dan pembulatan ke atas; biaya memakai harga beli.
- [ ] Uji transaksi keluar hari ini dan enam hari kalender sebelumnya, transaksi keluar tujuh hari lalu, transaksi masuk, serta transaksi bertanggal esok. **Harapan:** hanya transaksi keluar dalam rentang tujuh hari termasuk hari ini yang dihitung.
- [ ] Ubah target stok dan periksa kembali jumlah rekomendasi, total biaya, dan nilai target di layar Belanja. **Harapan:** rekomendasi dihitung ulang memakai target baru.
- [ ] Centang dan batalkan centang “Sudah dibeli” pada beberapa barang. **Harapan:** total dan jumlah sisa di layar berubah. Muat ulang halaman; centang bukan catatan transaksi dan stok tidak berubah.

## 6. Tampilan dan regresi dasar

- [ ] Uji semua layar pada lebar ponsel dan desktop. **Harapan:** konten tetap terbaca, form dapat digunakan, dan navigasi tidak menutupi aksi utama.
- [ ] Periksa tampilan validasi gagal, keadaan kosong, dan pesan sukses pada perangkat kecil.
- [ ] Jalankan `php artisan test` dan `npm run build`. **Harapan:** seluruh tes lulus dan aset frontend terbangun.
