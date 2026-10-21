# Verifikasi keamanan data — Pertemuan 03

- Isolasi tenant diuji melalui composite foreign key menu-kategori dan
  modifier-option-grup; kasus lintas tenant ditolak dalam SQLite.
- Hash QR/tracking disimpan pada kolom binary, bukan token mentah.
- Saldo tenant dicegah bernilai negatif oleh CHECK di MySQL/MariaDB dan trigger
  setara saat tes memakai SQLite.
- Perilaku append-only dan proteksi database produksi belum diuji pada tahap ini.
