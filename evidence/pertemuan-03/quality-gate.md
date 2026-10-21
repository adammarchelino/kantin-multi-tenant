# Quality gate — Pertemuan 03

| Pemeriksaan | Hasil |
|---|---|
| `php artisan test --compact` | Lulus: 53 test, 165 assertion, SQLite in-memory |
| `vendor/bin/pint --test` | Lulus |
| `vendor/bin/phpstan analyse --no-progress --memory-limit=1G` | Lulus, 0 error |
| `npm run build` | Lulus |
| MariaDB migration | Lulus pada MariaDB 12.0.2; migration Modul 3 batch 3 |
| Seeder MariaDB | Berhasil dijalankan berulang; satu kantin dan dua tenant tetap terjaga |
| EXPLAIN MariaDB | Kedua query memakai index; query KDS masih `Using filesort` |
| Schema dump | Berkas dibuat; perintah Laravel mencetak warning opsi `column-statistics` tak dikenal, isi dump tetap berisi definisi tabel |

SQLite digunakan untuk pengujian otomatis karena konfigurasi PHPUnit menunjuk
ke database in-memory. Migration additive dan seeder demo dijalankan pada
MariaDB `kantin`; perintah reset database tidak dijalankan.
