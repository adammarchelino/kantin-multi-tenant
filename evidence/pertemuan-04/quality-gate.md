# Quality gate — Pertemuan 04

| Pemeriksaan | Hasil |
|---|---|
| `php artisan test --compact` | Lulus: 65 test, 205 assertion |
| `vendor/bin/pint --test` | Lulus |
| `vendor/bin/phpstan analyse --no-progress --memory-limit=1G` | Lulus, 0 error |
| `npm run build` | Lulus; ada pemberitahuan opsional paket `fontaine` |
| Uji MariaDB khusus Modul 4 | Tidak dijalankan; tidak ada perubahan schema dan test memakai SQLite in-memory |

