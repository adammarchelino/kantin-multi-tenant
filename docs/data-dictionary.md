# Data Dictionary - Aplikasi Kantin Multi-Tenant

Dokumen ini memuat kamus data dan pengelompokan 30 tabel baseline ke dalam 8 domain utama untuk menjaga isolasi data, ketertelusuran transaksi, serta integritas multi-tenant architecture.

---

## 1. Domain Identitas & Akses (Identity & Access Domain)
Mengelola data pengguna, autentikasi, otorisasi, peran (RBAC), serta struktur hirarki kantin dan tenant.

* **`users`**: Menyimpan identitas pengguna (Super Admin, Pengelola Kantin, Pemilik Tenant, Kasir).
* **`passkeys`**: Menyimpan data kredensial WebAuthn/Passkey pengguna untuk autentikasi tanpa kata sandi.
* **`two_factor_authentications`**: Menyimpan konfigurasi 2FA (TOTP) pengguna.
* **`canteens`**: Data entitas lokasi fisik/area kantin utama.
* **`tenants`**: Data penyewa/merchant toko yang beroperasi di dalam suatu kantin.
* **`tenant_user`**: Tabel pivot relasi multi-tenant untuk mengasosiasikan pengguna ke tenant/kantin tertentu.

---

## 2. Domain Meja & Sesi (Table & Session Domain)
Mengelola inventaris meja fisik kantin dan sesi kunjungan pelanggan berbasis QR/token.

* **`tables`**: Menyimpan lokasi dan nomor meja fisik di area kantin beserta `token_hash` unik.
* **`table_sessions`**: Mengacak dan mencatat sesi aktif pemesanan pelanggan di meja tertentu.
* **`table_session_devices`**: Mencatat identitas perangkat/browser yang terhubung ke dalam satu sesi meja.

---

## 3. Domain Katalog (Catalog Domain)
Mengelola penawaran produk, struktur kategori, opsi tambahan (modifiers), dan aturan komisi.

* **`categories`**: Kategori pengelompokan menu milik tenant.
* **`menus`**: Item makanan/minuman yang dijual oleh tenant.
* **`modifiers`**: Opsi tambahan/topping untuk item menu tertentu (terikat via composite key `[tenant_id, menu_id]`).
* **`commissions`**: Aturan tarif potongan komisi sistem per tenant berbasis rentang waktu (*effective-dated*).

---

## 4. Domain Pesanan (Order Domain)
Mengisi alur siklus hidup transaksi pemesanan dari pelanggan hingga pemrosesan Dapur/Kasir.

* **`orders`**: Header utama transaksi pesanan pelanggan.
* **`order_items`**: Rincian item menu yang dipesan dalam satu order.
* **`order_item_modifiers`**: Rincian opsi tambahan/topping yang dipilih pada item order.
* **`order_statuses`**: Log histori perubahan status pesanan (*cancellation, kitchen processing, ready, completed*).

---

## 5. Domain Pembayaran (Payment Domain)
Mengelola integrasi gateway pembayaran, status transaksi elektronik, dan pengembalian dana (*refund*).

* **`payments`**: Header transaksi pembayaran untuk suatu pesanan.
* **`payment_attempts`**: Catatan riwayat percobaan pembayaran (misal: percakapan ke Payment Gateway/QRIS).
* **`refunds`**: Catatan pengajuan dan eksekusi pengembalian dana transaksi.
* **`refund_items`**: Detail rincian item pesanan yang dikembalikan nilainya.

---

## 6. Domain Buku Besar (Ledger / Financial Domain)
Sistem pembukuan entri ganda (*double-entry accounting*) untuk mencatat pergerakan dana, komisi, dan pencairan (*payout*).

* **`accounts`**: Rekening/akun akuntansi internal (Kas, Penampungan, Pendapatan Tenant, Komisi Platform).
* **`journals`**: Header entri transaksi jurnal keuangan.
* **`journal_entries`**: Rincian posisi *debit* dan *kredit* untuk setiap jurnal keuangan.
* **`settlements`**: Rekapitulasi penyelesaian transaksi harian/berkala per tenant.
* **`payouts`**: Transaksi pencairan dana bersih dari platform ke rekening bank milik tenant.

---

## 7. Domain Outbox (Outbox Messaging Domain)
Pola *Transactional Outbox* untuk menjamin konsistensi pemrosesan event asinkron (misal: notifikasi, webhook).

* **`outbox_messages`**: Antrean pesan/event internal yang harus dipublikasikan setelah transaksi database sukses.
* **`outbox_failures`**: Catatan kegagalan isolasi pemrosesan pesan outbox untuk keperluan *retry* atau analisis.

---

## 8. Domain Audit & Sistem (Audit & System Domain)
Menyediakan jejak audit (*audit trail*) sistem dan pengelolaan tugas latar belakang (*background jobs*).

* **`audit_logs`**: Catatan aktivitas penting dan perubahan data sensitif oleh pengguna/sistem.
* **`cache`**: Penyimpanan sementara (*caching*) data aplikasi.
* **`cache_locks`**: Penanganan kunci atomik untuk mencegah *race-condition*.
* **`jobs`**: Antrean tugas latar belakang (*queued jobs*).
* **`job_batches`**: Pengelompokan eksekusi *batch job*.
* **`failed_jobs`**: Catatan tugas latar belakang yang gagal dieksekusi.