# Arsitektur — Kantin Multi-Tenant

Pertemuan 2 menata proyek sebagai **modular monolith**: satu aplikasi Laravel dan satu deployment, dengan batas tanggung jawab yang jelas di `app/Modules/*`. Semua modul memakai shared kernel Laravel dan database yang sama.

## Bounded context

| Modul | Tanggung jawab | Direncanakan pada |
|---|---|---|
| `Admin` | Administrasi kantin, tenant, role, komisi, rekening | Pertemuan 5 |
| `Catalog` | Kategori, menu, modifier, stok tenant, katalog publik | Pertemuan 7 |
| `Ordering` | Sesi pelanggan, keranjang, checkout, order dan snapshot | Pertemuan 8–9 |
| `Payments` | Gateway, webhook, settlement dan ledger | Pertemuan 10–11 |
| `Kitchen` | Antrean dapur, status order dan notifikasi | Pertemuan 12 |
| `Reporting` | Laporan, rekonsiliasi dan ekspor | Pertemuan 13 |

Satu fitur ditempatkan di folder modul pemiliknya. Provider modul memuat `routes/*.php`, namespace view `{alias}::`, dan namespace Livewire `{alias}::`. Komunikasi lintas modul selanjutnya memakai kontrak/service untuk permintaan atau event untuk memberitahu bahwa sesuatu telah terjadi.

```text
app/Modules/{Modul}/
├── {Modul}ServiceProvider.php
├── Actions/ Data/ Services/
├── Http/Controllers/
├── routes/{portal}.php
└── resources/views/{livewire}/
```

Model Eloquent (`app/Models`), middleware (`app/Http/Middleware`), route-group bersama (`app/Support`), layout dan komponen UI (`resources/views`) tetap di lokasi standar karena dipakai lintas modul.

## Request flow dan dependency

```text
HTTP request → route portal → middleware → action/controller/Livewire → view
                                   ↓
                         role dan status pengguna
```

```text
UI (routes, Livewire, Blade) → Application (Actions/Services) → Domain/Contracts
Infrastructure (Eloquent, HTTP, vendor) ─────────────────────→ kontrak/domain
```

Framework tidak otomatis menegakkan batas modul. Review dan test menjaga agar satu modul tidak mengakses controller atau tabel internal modul lain secara sembarang.

## Route portal

| Konteks | Prefix | Nama | Middleware |
|---|---|---|---|
| Pelanggan publik | `kantin/{canteen}` | `customer.*` | `web` |
| Operator tenant | `tenant/{tenant}` | `tenant.*` | `web`, `auth`, `verified`, `role:tenant` |
| Pengelola kantin | `admin` | `admin.*` | `web`, `auth`, `verified`, `role:admin` |

Definisi grup berada di satu tempat, `App\Support\Routing\PortalRoutes`. `bootstrap/app.php` memuat tiga file route inti melalui `withRouting(then: ...)`; route fitur modul memanggil grup portal yang sama. Route model binding dan `scopeBindings()` menunggu model Tenant/Canteen pada pertemuan lanjutan.

## Authentication dan authorization

Fortify tetap mengelola login, verifikasi email, reset password dan autentikasi dua faktor. Middleware `auth` memeriksa identitas; middleware `role` memeriksa role dan status aktif pada server. Menyembunyikan tautan navigasi saja tidak melindungi URL langsung.

Kolom `users.role` dan `users.status` adalah kontrak sementara untuk fondasi ini. Pemeriksaan role belum memeriksa membership pengguna terhadap tenant tertentu; isolasi data tenant dan policy akan dirancang saat model tenant tersedia.

## Layout dan komponen

- `x-layouts.customer`: mobile-first, lalu melebar hingga `max-w-7xl` pada layar desktop.
- `x-layouts.tenant`: sidebar dengan toggle untuk layar kecil.
- `x-layouts.admin`: konten lebar dan tabel yang dapat digulir horizontal.
- Komponen bersama: `x-button`, `x-input`, `x-status-badge`, `x-empty-state`.
- Target kontrol interaktif minimal `min-h-11` (44 px); Tailwind CSS v4 dan Flux appearance tetap dipakai.

## Konvensi

- Namespace modul: `App\Modules\{Modul}\...`; alias view/Livewire memakai nama modul huruf kecil.
- Nama route berbentuk `{konteks}.{aksi}`.
- Blade `{{ }}` melakukan escaping; gunakan `{!! !!}` hanya untuk HTML yang telah disanitasi.
- Berkas Blade/Livewire di `app/Modules/*/resources` bukan kelas PHP murni; konvensinya diuji melalui feature test.
