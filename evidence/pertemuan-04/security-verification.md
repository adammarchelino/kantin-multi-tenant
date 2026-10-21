# Verifikasi keamanan — Pertemuan 04

- Operator aktif yang menjadi anggota tenant dapat membuka dashboard dan daftar
  menu tenant tersebut.
- Pengguna tanpa role tenant, akun nonaktif, non-member, atau tenant suspended
  ditolak dengan 403.
- Menu tenant lain pada route tenant aktif menghasilkan 404 lewat scoped binding.
- Policy menolak update menu, tenant order, dan withdrawal yang bukan milik
  tenant aktif.
- Global scope membatasi menu dan kategori saat context diisi; auto-fill saat
  create mengambil `tenant_id` dari context, bukan nilai yang dipaksakan.
- Middleware dan job mengosongkan context setelah selesai.
- Public query mengabaikan menu tenant suspended, menu tidak tersedia, tenant
  pada canteen lain, dan tenant pada canteen nonaktif.

Pengujian otomatis memakai SQLite in-memory. Hasil rinci ada di
`test-results/phpunit-summary.txt`.
