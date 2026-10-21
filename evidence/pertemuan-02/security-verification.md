# Verifikasi Otorisasi

Feature test mengirim request HTTP langsung, termasuk tanpa melalui navigasi UI.

| Request | Hasil yang diuji |
|---|---|
| Guest → dashboard tenant | Redirect ke login |
| Guest → dashboard admin | Redirect ke login |
| Role tenant aktif → dashboard tenant | HTTP 200 |
| Role tenant aktif → dashboard admin | HTTP 403 |
| Role admin aktif → dashboard admin | HTTP 200 |
| Role admin aktif → dashboard tenant | HTTP 403 |
| Pengguna tanpa role → dashboard internal | HTTP 403 |
| Akun tenant suspended → dashboard tenant | HTTP 403 |

Test: `tests/Feature/ModularFoundationTest.php` dan `tests/Feature/ModuleConventionTest.php`.

Role saat ini belum mengikat pengguna kepada tenant tertentu. Route memakai slug placeholder; binding model dan policy isolasi tenant dijadwalkan saat model Tenant tersedia.
