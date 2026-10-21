# Verifikasi database — Pertemuan 04

Modul 4 tidak menambah migration. Ia memakai composite foreign key dan unique
constraint dari skema Modul 3. Feature test memakai SQLite in-memory sesuai
konfigurasi PHPUnit agar tidak mereset database `kantin`.

Isolasi query dibuktikan melalui context dan global scope; konsistensi tenant
lintas relasi tetap ditegakkan oleh foreign key komposit MariaDB yang sudah
diterapkan pada Modul 3. Tidak ada perubahan data bisnis di database MariaDB
selama pengujian Modul 4.
