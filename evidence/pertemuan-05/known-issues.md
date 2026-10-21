# Modul 5 — batasan yang diketahui

- Migrasi menambahkan generated-column unique guard pada database `kantin`. Akun MariaDB lokal tidak dapat membuat trigger karena binary logging aktif dan tidak memiliki hak `SUPER`; akibatnya perlindungan immutability audit di MariaDB berada pada model aplikasi, sedangkan SQLite test juga membuktikannya dengan trigger.
- Login admin ke halaman tenant memerlukan membership `manager` pada kantin. Jika membership akun demo belum ada, jalankan `php artisan db:seed --class=AdminCanteenManagerSeeder`; seeder ini hanya menambahkan membership dan tidak mengubah menu/status tenant yang sudah ada.
- Email tautan atur kata sandi mengikuti konfigurasi mail Laravel. Untuk demo lokal, set `MAIL_MAILER=log` lalu ambil tautan dari `storage/logs/laravel.log`.
- Tes otomatis membuktikan perilaku aplikasi pada SQLite in-memory. Migrasi produksi MariaDB diterapkan pada database lokal; tidak dilakukan uji konkurensi paralel terhadap data langsung.
