# Verifikasi database — Pertemuan 03

Migration Modul 3 berhasil diterapkan pada MariaDB 12.0.2 di database `kantin`
dengan `php artisan migrate`; tidak ada tabel yang dihapus. Seeder dijalankan
berulang. Hasil data demo: 1 canteen, 2 tenants, 3 meja, 4 menu, 2 modifier
groups, 4 modifier options, dan 4 relasi menu-modifier. Pengujian otomatis memakai SQLite in-memory:
53 tests dan 165 assertions lulus. Error vendor-specific 1452/1062 belum diuji
di MariaDB; pengujian otomatis hanya memastikan operasi constraint ditolak.

EXPLAIN aktual:

| Query | type | key | Catatan |
|---|---|---|---|
| Menu tenant dan ketersediaan | `ref` | `menus_tenant_id_id_unique` | `Using where` |
| Tenant order berdasarkan status dan waktu | `ref` | `tenant_orders_tenant_id_id_unique` | `Using where; Using filesort` |

Schema dump terdapat di [schema-dump.sql](schema-dump.sql), dengan 39 definisi
tabel. Perintah Laravel mencetak warning `column-statistics=0` tidak dikenal
oleh binary `mysqldump` terpasang, tetapi file yang dihasilkan berisi definisi
tabel. Hasil dump perlu regenerasi bila tool dump diperbarui.
