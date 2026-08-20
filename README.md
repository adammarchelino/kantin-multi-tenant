# Kantin Multi-Tenant

Aplikasi kantin multi-tenant berbasis Laravel 13 dan Livewire 4, dikembangkan untuk praktikum mata kuliah Pemrograman Web Lanjut, Politeknik Negeri Banyuwangi.

## Tech Stack

- **Backend**: Laravel 13, PHP 8.4
- **Frontend**: Livewire 4 (single-file components)
- **Database**: MariaDB 12.0
- **Cache / Session / Queue**: Redis
- **Realtime**: Laravel Reverb
- **Testing**: PHPUnit
- **Code Style**: Laravel Pint

## Requirements

Pastikan environment lokal sudah memenuhi kebutuhan berikut sebelum instalasi:

| Tool | Versi Minimal |
|------|----------------|
| PHP | 8.3+ |
| Composer | Terbaru |
| Node.js | 18+ |
| NPM | Terbaru |
| MariaDB | 10.x+ |
| Redis | Terbaru |
| Git | Terbaru |

Ekstensi PHP wajib aktif: `pdo_mysql`, `mbstring`, `openssl`, `ctype`, `curl`, `fileinfo`, `xml`, `tokenizer`.

## Instalasi

1. Clone repository ini dan masuk ke foldernya:

   ```bash
   git clone <url-repository> kantin-multi-tenant
   cd kantin-multi-tenant
   ```

2. Install dependency PHP dan JavaScript:

   ```bash
   composer install
   npm install
   ```

3. Salin file environment dan generate application key:

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. Buka `.env`, sesuaikan konfigurasi berikut dengan environment lokal masing-masing:

   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=nama_database_anda
   DB_USERNAME=root
   DB_PASSWORD=

   REDIS_CLIENT=phpredis
   REDIS_HOST=127.0.0.1
   REDIS_PORT=6379

   SESSION_DRIVER=redis
   CACHE_STORE=redis
   QUEUE_CONNECTION=redis

   BROADCAST_CONNECTION=reverb
   REVERB_APP_ID=
   REVERB_APP_KEY=
   REVERB_APP_SECRET=
   REVERB_HOST=localhost
   REVERB_PORT=8080
   REVERB_SCHEME=http
   ```

   > Buat database kosong terlebih dahulu sesuai nama yang diisi pada `DB_DATABASE`, sebelum menjalankan migrasi.

5. Jalankan migrasi database beserta seeder:

   ```bash
   php artisan migrate:fresh --seed
   ```

6. Build asset frontend:

   ```bash
   npm run build
   ```

## Menjalankan Aplikasi

Jalankan seluruh proses pengembangan (HTTP server, Vite, queue worker) dengan satu perintah:

```bash
composer run dev
```

Aplikasi dapat diakses di `http://localhost:8000`.

Jika Reverb tidak ikut berjalan otomatis, jalankan di terminal terpisah:

```bash
php artisan reverb:start
```

## Testing dan Quality Gate

Sebelum melakukan commit, pastikan seluruh pemeriksaan berikut berhasil (exit code 0):

```bash
php artisan test
./vendor/bin/pint --test
npm run build
```

Jika `pint --test` menemukan masalah format, jalankan tanpa flag `--test` untuk memperbaiki otomatis:

```bash
./vendor/bin/pint
```

## Struktur Environment

| Layanan | Kegunaan |
|---------|----------|
| MariaDB | Data transaksional yang butuh integritas dan tahan gagal (tenant, order, payment, stok, ledger) |
| Redis | Data berumur pendek atau berkecepatan tinggi (session, cache, cart, lock, queue) |
| Reverb | Broadcasting event realtime melalui WebSocket |

Gunakan database dan kredensial terpisah antara environment development dan testing, agar `migrate:fresh` atau flush Redis tidak memengaruhi data kerja.

## Troubleshooting

| Gejala | Kemungkinan Penyebab | Perbaikan |
|--------|------------------------|-----------|
| `could not find driver` | Ekstensi `pdo_mysql` belum aktif | Aktifkan ekstensi di `php.ini`, lalu restart PHP |
| Port `3306` atau `6379` bentrok | Service lain sudah memakai port tersebut | Ganti port di konfigurasi service dan `.env`, lalu verifikasi dengan `db:show` dan `redis-cli ping` |
| Vite manifest tidak ditemukan / halaman tanpa styling | Asset belum diinstal atau dibangun | Jalankan `npm install` lalu `npm run build` |
| Error saat broadcasting/Reverb | Variabel `REVERB_*` kosong di `.env` | Lengkapi `REVERB_APP_ID`, `REVERB_APP_KEY`, `REVERB_APP_SECRET`, lalu jalankan `php artisan config:clear` |

## Catatan Zona Waktu

Waktu transaksi disimpan dalam UTC pada database, dan dikonversi ke `Asia/Jakarta` (WIB) hanya pada batas presentasi (tampilan ke pengguna).

## Lisensi

Project ini dibuat untuk keperluan akademik dan bukan untuk produksi.
