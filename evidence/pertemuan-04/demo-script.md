# Demo mandiri — Pertemuan 04

1. Jalankan `php artisan route:list --path=tenant -v` dan pastikan route memakai
   middleware `tenant` serta parameter tenant terikat dengan `slug`.
2. Jalankan `php artisan test --compact tests/Feature/TenantIsolationTest.php`.
3. Login sebagai `tenant@kantin.test`, lalu buka
   `/tenant/ayam-pusat/dashboard` dan `/tenant/ayam-pusat/menus`.
4. Ubah slug atau ID menu pada URL agar menunjuk tenant lain; akses harus 404
   atau 403 dan menu tenant lain tidak boleh berubah.
5. Jalankan suite penuh serta Pint dan PHPStan seperti di `quality-gate.md`.

Jangan jalankan `migrate:fresh` pada database `kantin`. Test otomatis memakai
SQLite in-memory dan tidak memerlukan reset MariaDB.
