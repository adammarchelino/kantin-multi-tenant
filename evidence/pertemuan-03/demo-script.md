# Demo mandiri — Pertemuan 03

Gunakan database development yang boleh dihapus untuk reset penuh:

```sh
php artisan migrate:fresh --seed
php artisan test --compact tests/Feature/DatabaseConstraintTest.php
```

`migrate:fresh` menghapus seluruh tabel pada koneksi aktif. Pastikan `.env`
menunjuk ke database disposable sebelum menjalankannya; jangan gunakan terhadap
database penting atau produksi. Pengujian dalam proyek ini memakai SQLite
in-memory sehingga tidak menghapus database MySQL `kantin`.

Untuk inspeksi MariaDB setelah migrasi aman diterapkan, jalankan dua query
EXPLAIN yang dicantumkan pada modul dan simpan output aktual di
`database-verification.md`. Schema dump juga dibuat dari database aktual,
bukan dari tebakan migration.
