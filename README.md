# Bintang Agung Tani - E-Commerce Agritech

![Logo](public/images/logo.png)

Bintang Agung Tani adalah platform e-commerce agritech berbasis **Laravel 12** untuk penjualan produk pertanian dengan tampilan responsif, dashboard terpisah untuk user dan admin, alur checkout, pembayaran, invoice PDF, dan data demo yang sudah tersedia.

## Fitur Utama

- Dashboard user dan admin
- Katalog produk, keranjang, checkout, dan riwayat pesanan
- Manajemen alamat pengiriman
- Upload bukti pembayaran dan verifikasi pembayaran
- Generate invoice PDF untuk pesanan
- Profil user dengan foto profil
- Manajemen user, produk, kategori, dan stok dari dashboard admin
- Data seed demo lengkap untuk pengujian dan demo

## Tech Stack

- PHP 8.2+
- Laravel 12
- Tailwind CSS v4
- Alpine.js
- Vite
- MySQL / MariaDB
- DOMPDF untuk invoice PDF
- Intervention Image untuk pemrosesan gambar
- Endroid QR Code untuk QR/pembayaran

## Kebutuhan Sistem

- PHP 8.2 atau lebih baru
- Composer 2.5+
- Node.js 18+
- MySQL 8.0+ atau MariaDB 10.5+

## Instalasi

### 1. Install dependensi

```bash
composer install
npm install
```

### 2. Siapkan environment

```bash
copy .env.example .env
php artisan key:generate
```

Lalu sesuaikan database di file `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bintang_agung_tani
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Jalankan migrasi dan seed

```bash
php artisan migrate
php artisan db:seed
```

Jika ingin reset total database beserta demo data:

```bash
php artisan migrate:fresh --seed
```

### 4. Buat symbolic link storage

Karena aplikasi menyimpan foto profil, invoice, dan file upload di storage publik:

```bash
php artisan storage:link
```

### 5. Jalankan aplikasi

Opsi 1, dua terminal terpisah:

```bash
npm run dev
php artisan serve
```

Opsi 2, jalankan semua sekaligus dengan script Composer:

```bash
composer run dev
```

## Kredensial Demo

Setelah `php artisan db:seed`, gunakan akun berikut.

### Admin

| Email           | Password    |
| --------------- | ----------- |
| admin@gmail.com | password123 |

### User

| Email          | Password    |
| -------------- | ----------- |
| user@gmail.com | password123 |
| budi@email.com | password123 |
| siti@email.com | password123 |
| dewi@email.com | password123 |
| agus@email.com | password123 |
| rini@email.com | password123 |

## Data Demo

Seeder utama membuat data demo berikut:

- 1 akun admin
- 6 akun user
- 8 kategori produk
- 40 produk demo
- 20 pesanan sampel
- alamat user, metode pembayaran, log stok, dan bukti pembayaran demo

## Struktur Proyek

```text
app/
  Actions/
  DTOs/
  Events/
  Exceptions/
  Http/
    Controllers/
    Requests/
    Resources/
  Jobs/
  Listeners/
  Models/
  Notifications/
  Policies/
  Repositories/
  Services/
bootstrap/
config/
database/
  factories/
  migrations/
  seeders/
public/
resources/
  css/
  js/
  views/
routes/
tests/
```

## Fitur yang Tersedia Saat Ini

### User

- Login dan registrasi
- Lihat produk dan detail produk
- Keranjang belanja
- Checkout dengan alamat pengiriman
- Tambah, ubah, hapus alamat
- Upload bukti pembayaran
- Lihat status pesanan
- Update profil, password, dan foto profil

### Admin

- Dashboard admin
- Kelola user
- Kelola produk dan kategori
- Kelola pesanan
- Verifikasi pembayaran
- Melihat invoice dan data transaksi

## Testing

Jalankan seluruh test:

```bash
php artisan test
```

Test yang sering dipakai saat development:

```bash
php artisan test tests/Feature/User/ProfileTest.php
php artisan test tests/Feature/User/CheckoutTest.php
php artisan test tests/Feature/User/AddressTest.php
php artisan test tests/Feature/Admin/UserTest.php
```

## Build Produksi

```bash
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Troubleshooting

### Aplikasi tidak bisa akses file upload atau invoice

Pastikan storage link sudah dibuat:

```bash
php artisan storage:link
```

### Database kosong setelah install

Jalankan seeder:

```bash
php artisan db:seed
```

### Kredensial admin gagal

Pastikan sudah menjalankan seeder dan gunakan:

- Email: admin@gmail.com
- Password: password123

## Lisensi

Proyek ini menggunakan lisensi MIT.

---

Bintang Agung Tani

