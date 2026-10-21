# Catatan dan pekerjaan lanjutan — Pertemuan 04

- Global scope hanya membatasi query jika `TenantContext` sudah diisi. Ini
  mengikuti contoh kode dan test modul; query console/service tanpa context
  masih lintas tenant. Tetapkan context atau buat bypass khusus dengan filter
  eksplisit sebelum menambah query semacam itu.
- Audit log akses admin lintas tenant ditunda ke service `AuditLogger` Modul 5.
- Otorisasi action Livewire dicatat sebagai aturan untuk Modul 7 karena belum
  ada komponen Livewire tenant yang mengubah data pada increment ini.
- Menu controller saat ini adalah endpoint JSON demonstrasi; UI katalog tenant
  menyusul pada modul katalog.
- Perubahan belum di-commit atau di-push sampai ditinjau pengguna.
