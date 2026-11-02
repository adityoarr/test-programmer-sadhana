# Test Programmer Sadhana

## Sistem Pembelian (Procurement & Inventory)

Aplikasi web berbasis Laravel untuk mengelola proses pembelian dan persediaan barang,
mulai dari master data barang, transaksi pembelian (Purchase Order),
penerimaan barang, hingga pengelolaan stok gudang.

## Tech Stack

- PHP 8.5.12
- Laravel 12 (terbaru via Composer)
- PostgreSQL 17 (Docker)
- Bootstrap 5 (UI)

## Fitur

### Master Barang
- CRUD barang (create, read, update, delete + soft delete)
- Pencarian kode/nama barang dan pagination
- Relasi kategori dan satuan
- Harga beli, harga jual, minimum stok, dan status aktif

### Transaksi Pembelian
- CRUD Purchase Order dengan pola header–detail
- Penomoran PO otomatis (format `PO-YYYYMMDD-XXXX`)
- Form detail barang dinamis (tambah/hapus baris) dengan perhitungan subtotal, diskon, pajak, dan total otomatis
- Status PO otomatis: `ordered`, `partial`, `received`, `cancelled`
- Penerimaan barang dari PO (penuh maupun sebagian)
- Update stok gudang otomatis saat penerimaan
- Pencatatan riwayat pergerakan stok (`stock_movements`)
- Riwayat penerimaan barang per PO

## Skema Database

Terdiri dari 11 tabel:

`suppliers`, `categories`, `units`, `warehouses`, `items`,
`stocks`, `stock_movements`, `purchase_orders`,
`purchase_order_items`, `goods_receipts`, `goods_receipt_items`

Diagram ERD lengkap tersedia pada dokumen/gambar ERD terpisah.

## Requirements

- PHP 8.5.12
- Composer
- Docker & Docker Compose

## Cara Menjalankan

1. Masuk ke folder project, lalu install dependency:
    ```bash
    composer install

2. Salin dan sesuaikan file environment:
    ```bash
    cp .env.example .env
    php artisan key:generate

3. Jalankan database PostgreSQL dengan Docker:
    ```bash
    docker compose up -d

4. Jalankan migration beserta data awal (seeder):
    ```bash
    php artisan migrate --seed
   
5. Jalankan aplikasi:
    ```bash
    php artisan serve

6. Buka aplikasi di browser:
    ```bash
    http://localhost:8000