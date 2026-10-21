# Checklist Modul 6

- [x] Model lifecycle meja, token QR, sesi pelanggan, dan jam operasional.
- [x] CRUD meja terbatas pada keanggotaan pengelola kantin.
- [x] Token acak minimal 128 bit; database hanya menyimpan SHA-256.
- [x] Rotasi atomik mencabut token QR lama dan mencatat audit.
- [x] Endpoint `/q/{token}` memvalidasi token dan dibatasi rate limiter.
- [x] Sesi anonim 4 jam memakai cookie HttpOnly dan SameSite=Lax.
- [x] QR SVG siap unduh/cetak; URL token mentah ditampilkan satu kali.
- [x] Halaman sambutan, jam tenant, halaman kantin tutup, dan 404 generik.
- [x] Automated tests dan production asset build dijalankan.
- [ ] Uji scan hasil cetak dengan beberapa ponsel secara manual.
