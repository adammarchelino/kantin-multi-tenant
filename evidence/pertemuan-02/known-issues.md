# Catatan dan Batas Implementasi

- Tenant/Canteen masih berupa parameter slug. Model binding dan `scopeBindings()` belum dapat diterapkan sebelum model tersebut dibuat pada pertemuan lanjutan.
- Middleware role memisahkan admin dan tenant, tetapi belum memverifikasi membership tenant per pengguna.
- Navigasi modul katalog, pesanan, KDS, dan administrasi ditandai sebagai fitur pertemuan berikutnya.
- Layout lolos render test dan asset CSS berhasil dibangun. Interaksi sidebar sudah memakai JavaScript untuk buka/tutup, backdrop, Escape, dan close saat tautan dipilih. Verifikasi visual browser 375 px / 1024 px tetap perlu dilengkapi saat demo.
- Login redirect per role belum diubah karena modul menjadwalkan perilaku itu pada Pertemuan 5.
