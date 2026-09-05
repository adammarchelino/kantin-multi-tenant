# Arsitektur Modular Monolith - Kantin Multi-Tenant

## Struktur Modul
- **Admin**: Pengelolaan sistem utama
- **Catalog**: Pengelolaan menu & tenant
- **Ordering**: Transaksi & pemesanan
- **Payments**: Integrasi pembayaran
- **Kitchen**: Operasional dapur tenant
- **Reporting**: Laporan & analitik

## Konvensi Route
- Customer: `kantin/{canteen:slug}` -> `customer.`
- Tenant: `tenant/{tenant:slug}` -> `tenant.`
- Admin: `admin` -> `admin.`