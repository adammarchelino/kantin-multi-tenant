# Modul 5 — catatan keamanan

- Semua route administrasi berada di middleware admin terverifikasi dan aksi tenant diperiksa lewat `TenantPolicy`.
- Tenant dibuat dalam transaksi bersama owner, membership owner, saldo awal, komisi, dan rekening utama.
- Nomor rekening memakai encrypted cast; daftar admin hanya menampilkan bank dan empat digit terakhir.
- Audit membersihkan field sensitif. Model menolak perubahan/penghapusan audit; SQLite juga memakai trigger. MariaDB menggunakan perlindungan model karena akun DB ServBay tidak memiliki hak membuat trigger saat binary logging aktif.
- Unique generated column menjaga maksimal satu komisi aktif dan satu rekening utama per tenant.
- Login mengunci kunci surel ternormalisasi yang di-hash setelah 6 kegagalan dalam 10 menit selama 15 menit; limiter terpisah membatasi laju per IP.
- Sesi default dan `.env.example` adalah 480 menit. Nilai `.env` lokal juga disesuaikan menjadi 480 menit.
