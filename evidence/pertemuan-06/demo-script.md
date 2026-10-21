# Demo singkat Modul 6

1. Login sebagai admin yang memiliki role pengelola kantin.
2. Buka `/admin/tables`, buat meja dengan kode unik dalam kantin itu.
3. Klik **Terbitkan QR**. Unduh SVG atau gunakan tombol **Cetak** saat token baru tampil.
4. Pindai URL QR; aplikasi membuat sesi anonim dan menuju halaman `/kantin/{slug}/meja`.
5. Rotasi QR meja, lalu buka URL lama. Aplikasi harus memberi halaman 404 yang menyarankan menghubungi petugas.
6. Nonaktifkan meja dan coba scan QR; aktifkan kembali untuk mengizinkan QR aktif yang sama kembali digunakan.
