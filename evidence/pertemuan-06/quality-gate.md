# Quality gate Modul 6

- `php artisan route:list --path=admin/tables` — 5 route meja tampil.
- `php artisan route:list --path=q/` — route `customer.scan` tampil dengan throttle `qr-scan`.
- Test terpilih Modul 6: 12 test lulus (79 assertions).
- `php artisan test --compact` — 93 test lulus (417 assertions).
- `vendor/bin/pint --test` — lulus untuk file yang disentuh Modul 6.
- `npm run build` — berhasil.
- Route meja menampilkan 5 route; route `/q/{token}` terdaftar dengan throttle `qr-scan`.
- Uji scan melalui kamera ponsel/printer belum dilakukan.
