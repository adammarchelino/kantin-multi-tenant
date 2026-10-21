# Checklist tahap — Pertemuan 04

- [x] Aturan akses dan data tenant-owned ditinjau.
- [x] `TenantContext` didaftarkan sebagai scoped service.
- [x] Middleware memeriksa tenant aktif, role operator, dan membership.
- [x] Global scope dan auto-fill `tenant_id` dipasang pada model tenant-owned.
- [x] Policy, scoped route binding, dan endpoint menu tenant ditambahkan.
- [x] Queue job membawa `tenant_id`, membuat context, lalu membersihkannya.
- [x] Public catalog bypass berada dalam satu query service dengan filter eksplisit.
- [x] Security regression test untuk Tenant A dan Tenant B ditambahkan.
- [ ] Commit menunggu review pengguna.
- [ ] Push tidak dilakukan.
