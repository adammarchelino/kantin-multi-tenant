# Modul 5 — quality gate

Hasil verifikasi lokal pada 21 Oktober 2026:

- `php artisan test --compact`: 81 passed, 316 assertions.
- `vendor\bin\pint --test`: passed.
- `npm run build`: passed.
- `php artisan route:list --path=admin/tenants`: 9 route admin terdaftar.
- `php artisan migrate`: migrasi `2026_08_17_000101_add_admin_guards_to_schema` berhasil diterapkan pada MariaDB `kantin`.

PHPUnit menjalankan database pengujian SQLite in-memory; tidak menghapus atau me-refresh database `kantin`.
