# Verifikasi Database

Migrasi `2026_08_16_000001_add_role_and_status_to_users_table.php` menambahkan:

- `role`: string nullable (`admin`, `tenant`, atau `null` untuk akun biasa).
- `status`: string dengan default `active`.

Migrasi dijalankan oleh feature suite melalui `RefreshDatabase` terhadap SQLite in-memory. Pengujian model memastikan role hanya aktif saat role cocok dan status akun `active`. Database lokal aplikasi tidak di-reset.

Untuk memeriksa schema lokal setelah menyiapkan koneksi sendiri: `php artisan migrate:status` dan `php artisan db:table users`.
