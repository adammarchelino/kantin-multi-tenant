# Verifikasi keamanan QR dan sesi

- Token dibuat dengan `random_bytes(32)` dan Base64 URL-safe; token pendek di bawah 128 bit ditolak.
- `table_qr_tokens` dan `customer_sessions` menyimpan hash SHA-256 biner, bukan token mentah.
- Rotasi berjalan dalam transaksi dengan row lock, mencabut QR aktif sebelumnya, lalu mencatat audit tanpa token.
- Token rusak, lama, kedaluwarsa, meja nonaktif, kantin nonaktif, dan token numerik ditolak dengan HTTP 404 generik.
- Cookie sesi memiliki `HttpOnly`, `SameSite=Lax`, masa hidup 240 menit, dan `Secure` pada HTTPS/production.
- Endpoint QR scan dibatasi 30 permintaan per menit per alamat IP.
- Pengujian otomatis mencakup perilaku tersebut; TLS dan pemindaian fisik tetap perlu dicek saat deployment.
