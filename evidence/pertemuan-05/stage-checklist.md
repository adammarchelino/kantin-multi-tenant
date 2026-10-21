# Modul 5 — checklist tahapan

- [x] Tahap 1: status tenant, role kantin dan tenant, policy, audit append-only.
- [x] Tahap 2: onboarding tenant melalui Form Request, transaksi, owner, saldo, komisi awal, rekening, dan tautan atur kata sandi.
- [x] Tahap 3: penugasan/pencabutan role dan perlindungan owner terakhir.
- [x] Tahap 4: komisi bertanggal efektif, transaksi, penguncian, dan guard satu komisi aktif.
- [x] Tahap 5: rekening terenkripsi, tampilan empat digit terakhir, status verifikasi, dan audit.
- [x] Tahap 6: tes policy, onboarding, komisi, rekening, audit, status, dan login.
- [x] Tahap 7: dokumentasi dan hasil quality gate.
- [x] Tahap 8: penguncian login, pesan generik, redirect dashboard sesuai role, dan sesi 8 jam.

Catatan: halaman admin dapat diuji dengan `admin@kantin.test` setelah relasi manager kantin tersedia. Seeder demo menambahkan relasi tersebut secara idempotent; untuk memperbaiki membership saja tanpa menyentuh data tenant/menu, jalankan `php artisan db:seed --class=AdminCanteenManagerSeeder`.
