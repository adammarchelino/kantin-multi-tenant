# Catatan dan pekerjaan tertunda — Pertemuan 03

- SQL baseline dan ERD yang dirujuk tidak tersedia di proyek, sehingga schema
  diturunkan dari isi modul dan diverifikasi lewat migration serta tes.
- Error code vendor-specific 1452/1062 belum diverifikasi pada MariaDB; tes
  menguji penolakan operasi melalui exception database.
- EXPLAIN KDS masih memakai `Using filesort` untuk pengurutan `created_at`;
  index tenant/status tetap terpakai. Index tenant/status/waktu bisa dievaluasi
  jika volume data dan beban KDS meningkat.
- Laravel schema dump mengeluarkan warning `column-statistics=0` dari binary
  dump yang tersedia. Dump tetap memuat definisi tabel, tetapi perlu dibuat
  ulang setelah tool diperbarui.
- Migration menggunakan CHECK saldo nonnegatif pada MySQL/MariaDB dan trigger
  pada SQLite; versi MariaDB target perlu mendukung enforcement CHECK.
- Modul meminta commit, tetapi sesuai instruksi pengguna perubahan belum
  di-commit atau di-push.
