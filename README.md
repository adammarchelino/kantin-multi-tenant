
# Kantin Multi-Tenant

Aplikasi kantin multi-tenant yang dibangun secara bertahap selama 14 pertemuan.

## Tech Stack

- Laravel 13
- Livewire 4
- MariaDB
- Redis
- Laravel Reverb
- Vite
- PHPUnit
- Laravel Pint

## Requirements

- PHP 8.3 atau lebih baru
- Composer 2.x
- Node.js 22.18 atau lebih baru
- npm
- MariaDB 10.6 atau lebih baru
- Redis
- Git

## Setup Lokal

1. Pastikan MariaDB dan Redis berjalan melalui ServBay.
2. Siapkan file `.env` berdasarkan `.env.example`.
3. Isi kredensial database lokal di `.env`.
4. Pasang dependency PHP dan frontend.

```cmd
composer install
npm install
npm run build
```

5. Buat APP_KEY jika belum tersedia.

```cmd
php artisan key:generate
```

6. Jalankan migrasi database pengembangan.

```cmd
php artisan migrate
```

## Menjalankan Aplikasi

Terminal pertama:

```cmd
composer run dev
```

Terminal kedua:

```cmd
php artisan reverb:start
```

Buka http://127.0.0.1:8000 di browser.

## Testing dan Quality Gate

```cmd
php artisan test
vendor\bin\pint --test
npm run build
```

Database pengujian menggunakan `kantin_test`, terpisah dari database pengembangan `kantin`.

## Troubleshooting

- `could not find driver`: periksa ekstensi PHP `pdo_mysql`.
- `Access denied`: periksa user, password, dan hak akses database.
- `Connection refused`: pastikan layanan MariaDB atau Redis berjalan.
- `Vite manifest not found`: jalankan `npm install` dan `npm run build`.
- Reverb gagal: periksa `BROADCAST_CONNECTION` dan variabel `REVERB_APP_*` pada `.env`.

## Struktur Direktori

- `app/`: kode aplikasi.
- `config/`: konfigurasi Laravel.
- `database/`: migrasi, factory, dan seeder.
- `resources/`: aset frontend.
- `routes/`: definisi route.
- `tests/`: pengujian otomatis.
- `references/`: SRS, ERD, mockup, SQL, dan modul praktikum.
