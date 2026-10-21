# Data Dictionary — Baseline Modul 3

Schema ini diturunkan dari instruksi tabel dan invariant di Modul 3. SQL baseline
`references/00_database` yang disebut modul tidak tersedia di proyek; lihat
[DOC-03-001](MODULE_REVISION_LOG.md). Karena itu dokumen ini memetakan skema yang
benar-benar dibuat oleh migration, bukan mengklaim perbandingan dengan SQL yang
hilang.

Scope: **P** platform, **T** dimiliki tenant, **M** campuran/support. Nilai uang
disimpan sebagai integer Rupiah (`BIGINT`), rate sebagai `DECIMAL(6,4)`, dan
hash token sebagai `BINARY(32)`. Tabel yang tidak memiliki model Eloquent dapat
diakses lewat query builder sampai fitur terkait dibangun.

| Tabel | Scope | Model | Constraint / tujuan |
|---|:--:|---|---|
| canteens | P | `Canteen` | Kode dan slug unik |
| tenants | T | `Tenant` | Kode/slug unik per kantin; soft delete |
| tenant_balances | T | `TenantBalance` | Satu saldo per tenant; saldo tidak negatif |
| tenant_bank_accounts | T | — | Nomor terenkripsi, hanya empat digit akhir terlihat |
| users | P | `User` | Email dan telepon unik |
| user_canteen_roles | P | — | Role unik per pengguna dan kantin |
| user_tenant_roles | T | `UserTenantRole` | Role unik per pengguna dan tenant |
| dining_tables | P | `DiningTable` | Kode meja unik per kantin |
| table_qr_tokens | P | — | Hash token unik, bukan token mentah |
| customer_sessions | P | — | Sesi customer dengan hash token unik |
| tenant_operating_hours | T | — | Satu jadwal per hari per tenant |
| commission_schemes | T | `CommissionScheme` | Riwayat komisi efektif per tenant |
| menu_categories | T | `MenuCategory` | Nama unik per tenant |
| modifier_groups | T | `ModifierGroup` | Grup pilihan milik tenant |
| menus | T | `Menu` | Composite FK kategori harus milik tenant yang sama |
| modifier_options | T | `ModifierOption` | Composite FK grup harus milik tenant yang sama |
| menu_modifier_groups | T | — | Pivot menu dan modifier dalam tenant yang sama |
| orders | P | `Order` | Checkout lintas tenant; sengaja tanpa `tenant_id` |
| tenant_orders | T | `TenantOrder` | Satu pecahan order per tenant; snapshot komisi |
| order_items | T | `OrderItem` | Composite FK item, menu, dan tenant |
| order_item_modifiers | T | — | Pilihan modifier tersimpan sebagai snapshot |
| menu_stock_movements | T | — | Kunci idempotensi unik untuk perubahan stok |
| payments | P | — | Referensi dan idempotency key unik |
| payment_attempts | P | — | Referensi provider unik |
| payment_events | P | — | ID event provider unik untuk mencegah replay |
| withdrawals | T | `Withdrawal` | Idempotency key dan penguncian aktif tenant |
| ledger_entries | M | — | Entri append-only dengan idempotency key |
| notification_deliveries | M | — | Event dan channel unik |
| outbox_events | M | — | Event unik untuk pola transactional outbox |
| audit_logs | M | — | Catatan perubahan append-only |

## Invariant utama

- Composite foreign key menolak menu atau modifier yang mengacu ke tenant lain.
- Satu `orders` induk dapat memiliki beberapa `tenant_orders` tanpa mengikatnya
  ke satu tenant saja.
- Nominal uang integer menghindari galat pembulatan floating point.
- Kunci idempotensi mencegah pemrosesan event/transaksi yang sama dua kali.
- Check saldo nonnegatif berupa CHECK di MariaDB dan trigger ekuivalen di SQLite.
