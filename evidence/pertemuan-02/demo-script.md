# Naskah Demo

1. Jalankan `composer run dev` dari root proyek.
2. Buka `http://127.0.0.1:8000/kantin/demo` untuk portal pelanggan publik.
3. Login dengan `tenant@kantin.test` / `password`; buka `http://127.0.0.1:8000/tenant/demo/dashboard`.
4. Ketik langsung `http://127.0.0.1:8000/admin/dashboard`; pengguna tenant harus mendapat HTTP 403.
5. Logout, login dengan `admin@kantin.test` / `password`, lalu buka `/admin/dashboard`.
6. Login kembali sebagai tenant dan buka tampilan 375 px serta 1024 px melalui device toolbar browser.

Akun demo dibuat oleh `DatabaseSeeder`; untuk database lokal kosong jalankan `php artisan migrate --seed`. Password demo hanya untuk lingkungan lokal.
