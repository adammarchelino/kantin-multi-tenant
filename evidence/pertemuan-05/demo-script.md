# Modul 5 — skenario demo mandiri

1. Jalankan `php artisan route:list --path=admin/tenants` dan pastikan route administrasi tersedia.
2. Login sebagai `admin@kantin.test`, buka `/admin/tenants`, lalu daftarkan tenant dengan surel penanggung jawab baru.
3. Periksa daftar: nama penanggung jawab, rekening dengan empat digit terakhir saja, komisi, dan status aktif.
4. Buka detail tenant; jadwalkan komisi untuk waktu mendatang dan periksa versi lama tetap ada pada riwayat.
5. Verifikasi rekening melalui tombol Verifikasi; status berubah tanpa menampilkan nomor rekening lengkap.
6. Tambahkan user yang sudah ada ke tenant dengan role operator. Coba cabut owner terakhir dan amati pesan validasi.
7. Nonaktifkan tenant; menu hilang dari katalog, sementara baris menu tetap tersimpan. Aktifkan kembali jika diperlukan.
8. Untuk lockout, gunakan akun uji: masukkan kata sandi salah enam kali, lalu kata sandi benar; login tetap ditolak selama masa kunci.

Gunakan surel dan tenant uji agar data demo yang sudah ada tetap terjaga.
