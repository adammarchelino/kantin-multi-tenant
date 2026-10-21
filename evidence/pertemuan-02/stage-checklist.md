# Checklist Pertemuan 02

- [x] Tahap 1 — Bounded context enam modul dan batas shared kernel dicatat di `docs/architecture.md`.
- [x] Tahap 2 — Enam provider termuat; setiap modul memiliki folder Actions, Data, Services, Http/Controllers, routes, serta resources/views/livewire.
- [x] Tahap 3 — Route publik customer serta route internal tenant/admin memakai grup PortalRoutes; route internal memerlukan login, verifikasi email, dan role aktif.
- [x] Tahap 4 — Tiga shell Blade dan empat komponen UI bersama tersedia. Feature test memeriksa shell dan kontrol sidebar; browser visual/interaksi pada lebar 375 px dan 1024 px perlu diperiksa manual.
- [x] Tahap 5 — Kolom role/status, middleware role, dan akun demo admin/tenant diterapkan. Redirect berbasis konteks dan relasi tenant penuh tetap menunggu pertemuan yang disebut modul.
- [x] Tahap 6 — Test fondasi, otorisasi, role, provider, namespace view, route modul, dan Livewire tersedia serta lulus.
- [x] Tahap 7 — Dokumentasi arsitektur dan catatan evidence praktikum dibuat. Tidak ada commit yang dibuat.

## Catatan verifikasi visual

Output HTML masing-masing layout telah diperiksa lewat feature test dan Tailwind berhasil dibangun. Screenshot browser bukan bagian dari verifikasi otomatis pada lingkungan ini; simpan tiga screenshot di `evidence/pertemuan-02/screenshots/` setelah membuka customer, tenant, dan admin pada lebar 375 px serta 1024 px.
