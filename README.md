# LBA Cloud (cmslba)

Boilerplate **Laravel 13 + Filament** dengan panel admin bergaya **TailAdmin** dan **DataTables server-side**. Dilengkapi multi-skin, multi-bahasa, RBAC dinamis, menu drag & drop, CRUD Generator, serta arsitektur **multi-mitra** dengan database terpisah per mitra.

## Fitur

1. **TailAdmin + DataTables server-side** — antarmuka admin berbasis Blade + Tailwind + jQuery DataTables (Yajra), dengan autentikasi panel Filament.
2. **Multi skin** — tema warna tersimpan per pengguna: **LBA Cloud** (default, navy + teal seperti `dev.lbacloud.com`, premium: **Midnight**, **Obsidian**, **Royal Indigo**, serta **Emerald**, **Slate**, **Rose**.
3. **Multi bahasa** — Bahasa Indonesia, English, dan Bahasa Malaysia.
4. **7 Role** dengan cakupan akses berbeda:
   - `admin_principal`, `user_principal` — lingkup pusat (principal)
   - `pemilik_mitra`, `admin_mitra`, `staff_admin`, `operator`, `keuangan_penjualan` — lingkup mitra + multi cabang
5. **Menu dinamis (nested)** — pengurutan dengan drag & drop (SortableJS).
6. **Assign Menu & permission CRUD** — setiap role dapat diberi hak `view/create/update/delete/export` per menu.
7. **Slug di setiap tabel** — semua resource diedit berdasarkan slug.
8. **CRUD Generator** — membuat migrasi, model, controller, dan baris menu secara otomatis, dengan pilihan target **database pusat (central)** atau **database mitra (tenant)**.
9. **Konfigurasi terpusat di database `lbacloud_pusat`** — role, menu, dan pemetaan hak akses tersimpan di sana.
10. **Multi mitra** — pendaftaran mitra otomatis membuat database baru `lbacloud_mitra_<uuid>` beserta frontpage publik di `/{nama-mitra}`.

## Persyaratan

- PHP 8.3+
- Composer
- Node.js & npm

## Instalasi

```bash
git clone https://github.com/k0d0k/cmslba.git
cd cmslba
composer install
cp .env.example .env
php artisan key:generate
touch database/lbacloud_pusat.sqlite
php artisan migrate --seed
npm install && npm run build
php artisan serve
```

Atau jalankan skrip bawaan:

```bash
composer setup
```

Buka `http://localhost:8000`.

## Akun demo (password: `password`)

**Level pusat (principal)**

| Role | Email |
| --- | --- |
| Admin Principal | admin@lbacloud.test |
| User Principal | user@lbacloud.test |

**Mitra "Mitra Demo"**

| Role | Email |
| --- | --- |
| Pemilik Mitra | pemilik@demo.test |
| Admin Mitra | admin@demo.test |
| Staff Admin | staff@demo.test |
| Operator LBA/Dokter/Peninjau/Konsultan | operator@demo.test |
| Keuangan & Penjualan | keuangan@demo.test |

**Mitra "Klinik Sehat Bersama"**

| Role | Email |
| --- | --- |
| Pemilik Mitra | pemilik@klinik.test |
| Staff Admin | staff@klinik.test |

Frontpage mitra: `/mitra-demo` dan `/klinik-sehat`.

Setiap mitra memiliki database terpisah (`lbacloud_mitra_<uuid>`) berisi data cabang & pelanggan contoh. Untuk me-reset seluruh data demo: `php artisan migrate:fresh --seed`.

## Arsitektur database

- **Central** (`lbacloud_pusat`) — menyimpan user, role, menu, mitra, skin, dan definisi CRUD.
- **Tenant** (`lbacloud_mitra_<uuid>`) — database terpisah untuk setiap mitra (mis. cabang, pelanggan, dan modul hasil generator). Setiap tabel tenant memiliki kolom `mitra_uuid` yang diisi otomatis dari mitra aktif.

Secara default proyek memakai **SQLite**. Untuk **MySQL**, cukup ubah `.env`:

```env
CENTRAL_DB_DRIVER=mysql
CENTRAL_DB_DATABASE=lbacloud_pusat
CENTRAL_DB_HOST=127.0.0.1
CENTRAL_DB_USERNAME=root
CENTRAL_DB_PASSWORD=

TENANT_DB_DRIVER=mysql
TENANT_DB_HOST=127.0.0.1
TENANT_DB_USERNAME=root
TENANT_DB_PASSWORD=
TENANT_DB_PREFIX=lbacloud_mitra_
```

`App\Services\TenantService` akan otomatis menjalankan `CREATE DATABASE` / `DROP DATABASE` sesuai driver.

## CRUD Generator

Buka **Pengaturan → CRUD Generator**, tentukan judul, model, **database target**, parent menu, daftar field (nama, tipe, label, nullable/unik/opsi), dan role yang diberi akses. Berdasarkan pilihan database, sistem akan membuat:

- **Database Mitra (tenant)** — migrasi di `database/migrations/tenant/`, model di `app/Models/Tenant/`, dan setiap tabel diberi kolom `mitra_uuid` (`App\Models\Concerns\HasMitraUuid`).
- **Database Pusat (central)** — migrasi di `database/migrations/`, model di `app/Models/`.

Selain itu dibuat controller di `app/Http/Controllers/Admin/`, baris menu + definisi CRUD, dan langsung tersedia di sidebar. Migrasi otomatis dijalankan pada database target.

## Lisensi

Dirilis di bawah lisensi [MIT](https://opensource.org/licenses/MIT).
