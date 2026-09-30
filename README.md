# MiniShop — Catalog, Cart & Admin

> Case Study Fullstack Engineer: Aplikasi e-commerce katalog tanaman hias & botani modern, keranjang belanja interaktif, transaksi checkout database dengan row locking (`lockForUpdate`) anti-race condition, dan panel admin terproteksi Laravel Sanctum.

---

## 📌 Ringkasan Proyek

MiniShop dibangun dengan arsitektur **decoupled SPA**:
- **Backend:** REST API berbasis Laravel 13 dan PostgreSQL dengan pemisahan tanggung jawab yang ketat (Controller tipis, validasi via FormRequest, serialisasi via API Resource, dan logika transaksi database di `CheckoutService`).
- **Frontend:** Single Page Application (SPA) berbasis Vue 3 (Vite), Pinia state management untuk keranjang belanja dengan persistensi `localStorage`, serta desain bertema botani gelap (*Dark Botanical*) menggunakan tipografi elegan Fraunces & Manrope.

---

## 🛠️ Tech Stack & Alasan Pemilihan

| Komponen | Teknologi | Alasan Pemilihan |
|---|---|---|
| **Backend Framework** | **Laravel 13** (PHP 8.4) | Framework standar industri yang tangguh dengan ekosistem ORM Eloquent, migration, FormRequest, dan seeder bawaan. |
| **Database** | **PostgreSQL 18** | Mendukung transaksi ACID dengan *pessimistic row locking* (`FOR UPDATE`) untuk mencegah race condition pada pengurangan stok, operator `ILIKE` untuk pencarian case-insensitive, dan *check constraints* tingkat database. |
| **Autentikasi Admin** | **Laravel Sanctum** | Autentikasi berbasis *Personal Access Token* yang ringan, aman, dan tanpa kompleksitas OAuth untuk REST API. |
| **Frontend Framework** | **Vue 3** (Vite) | Composition API dengan `<script setup>` yang reaktif, cepat, dan modular. |
| **State Management** | **Pinia** | Pengelolaan state terpusat untuk keranjang belanja (`cart`) dan autentikasi admin (`auth`), dilengkapi mekanisme sinkronisasi dan pemulihan `localStorage`. |
| **Routing** | **Vue Router** | Navigasi SPA dengan sinkronisasi query string URL (pencarian & filter kategori tetap bertahan saat refresh) serta route guard untuk area admin. |
| **Styling & UI** | **Vanilla CSS + Tailwind CSS Tokens** | Desain bertema *Dark Botanical* dengan palet warna semantik berdasar token CSS variables, tipografi Google Fonts (Fraunces & Manrope), transisi grid FLIP yang mulus, dan kepatuhan aksesibilitas WAI-ARIA. |
| **Format Uang** | **Integer Rupiah (`bigint`)** | Semua nilai nominal disimpan sebagai bilangan bulat Rupiah (bukan float/desimal) untuk mencegah galat pembulatan matematis. |

---

## 🚀 Panduan Menjalankan Proyek Secara Lokal

### Prasyarat Sistem
Pastikan perangkat Anda telah terpasang:
- **PHP** >= 8.2 (direkomendasikan PHP 8.4) & Ekstensi `pdo_pgsql`, `pgsql`, `mbstring`, `openssl`
- **Composer** >= 2.x
- **Node.js** >= 18.x & **npm**
- **PostgreSQL** Server aktif

---

### 1. Setup Backend (Laravel API)

1. Masuk ke folder backend:
   ```bash
   cd backend
   ```

2. Pasang dependensi PHP:
   ```bash
   composer install
   ```

3. Buat berkas konfigurasi `.env`:
   - Windows PowerShell / CMD:
     ```bash
     copy .env.example .env
     ```
   - Linux / macOS:
     ```bash
     cp .env.example .env
     ```

4. Generate Application Key:
   ```bash
   php artisan key:generate
   ```

5. Buat database di PostgreSQL:
   - Database utama: `minishop`
   - Database testing (opsional untuk tes): `minishop_test`

6. Sesuaikan konfigurasi database di file `backend/.env`:
   ```env
   DB_CONNECTION=pgsql
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=minishop
   DB_USERNAME=postgres
   DB_PASSWORD=password_anda
   ```

7. Jalankan migrasi dan seeder awal (membuat tabel, 4 kategori, 23 produk dummy, dan 1 akun admin):
   ```bash
   php artisan migrate --seed
   ```

8. Jalankan development server backend:
   ```bash
   php artisan serve
   ```
   *Backend API aktif di: `http://localhost:8000`*

---

### 2. Setup Frontend (Vue 3 SPA)

1. Buka terminal baru dan masuk ke folder frontend:
   ```bash
   cd frontend
   ```

2. Pasang dependensi Node.js:
   ```bash
   npm install
   ```

3. Jalankan development server frontend:
   ```bash
   npm run dev
   ```
   *Aplikasi web aktif di: `http://localhost:5173`*

---

### 🔑 Kredensial Admin Dev

Akun default yang dibuat melalui Database Seeder:
- **URL Login Admin:** `http://localhost:5173/admin/login`
- **Email:** `admin@minishop.test`
- **Password:** `password`

> ⚠️ *Catatan Keamanan:* Kredensial ini hanya ditujukan untuk keperluan pengujian lokal (*development*). Pada lingkungan produksi (*production*), kredensial harus diubah dengan password yang kuat dan aman.

---

## 🧪 Menjalankan Pengujian Otomatis

### Pengujian Backend (PHPUnit Test Suite)
Memastikan seluruh endpoint publik, kalkulasi keranjang, transaksi pesanan database, dan CRUD admin lulus uji:
```bash
cd backend
php artisan test
```
*(34 tests, 292 assertions — 100% Passed)*

### Pemeriksaan Standar Kode (Laravel Pint)
```bash
cd backend
vendor/bin/pint --test
```

### Build Production Frontend
```bash
cd frontend
npm run build
```

---

## 📡 Daftar Endpoint REST API

Base URL: `http://localhost:8000/api`

### 1. Katalog Publik (Tanpa Autentikasi)
| Method | Path | Deskripsi | Parameter Query / Body |
|---|---|---|---|
| `GET` | `/categories` | Mengambil seluruh daftar kategori produk | — |
| `GET` | `/products` | Mengambil daftar produk (paginasi) | `search`, `category` (slug), `page`, `per_page` |
| `GET` | `/products/{id}` | Mengambil detail 1 produk | — |

### 2. Pesanan & Checkout Publik
| Method | Path | Deskripsi | Format Payload / Respons |
|---|---|---|---|
| `POST` | `/orders` | Membuat pesanan baru (transaksi database) | **Request:** `{"customer": {"name": "...", "email": "..."}, "items": [{"product_id": 1, "quantity": 2}]}`<br>**Status:** `201 Created` / `409 Conflict` / `422 Unprocessable` |
| `GET` | `/orders/{order_number}` | Mengambil rincian struk pesanan publik | Format `order_number`: `MS-YYMMDD-XXXXXX` |

### 3. Autentikasi Admin
| Method | Path | Auth | Deskripsi |
|---|---|---|---|
| `POST` | `/admin/login` | Publik (Throttle 5/menit) | Login admin dan memperoleh token Sanctum |
| `POST` | `/admin/logout` | `Bearer Token` | Mencabut token sesi admin yang sedang aktif |

### 4. Manajemen Admin (Wajib Bearer Token)
| Method | Path | Auth | Deskripsi |
|---|---|---|---|
| `GET` | `/admin/products` | `Bearer Token` | Daftar seluruh produk untuk admin (search & filter) |
| `POST` | `/admin/products` | `Bearer Token` | Menambahkan produk baru |
| `GET` | `/admin/products/{id}` | `Bearer Token` | Mengambil data produk untuk formulir edit |
| `PUT` | `/admin/products/{id}` | `Bearer Token` | Memperbarui data produk |
| `DELETE`| `/admin/products/{id}` | `Bearer Token` | Menghapus produk (Soft delete) |
| `GET` | `/admin/orders` | `Bearer Token` | Daftar riwayat seluruh pesanan (terbaru dulu) |
| `GET` | `/admin/orders/{id}` | `Bearer Token` | Rincian detail pesanan dan snapshot item |

---

## ⚠️ Known Limitations (Batasan Saat Ini)

1. **Payment Gateway:** Pembayaran disederhanakan sebagai simulasi pesanan langsung (tanpa integrasi gateway pihak ketiga seperti Midtrans/Xendit).
2. **Akun Pelanggan:** Checkout dirancang sebagai *Guest Checkout* (tanpa registrasi akun pelanggan).
3. **Upload Berkas Gambar:** Gambar produk saat ini menggunakan input URL eksternal (Unsplash/Picsum).
4. **CRUD Kategori:** Kategori produk dikelola secara statis melalui seeder dan belum memiliki antarmuka CRUD mandiri di panel admin.
5. **Idempotency Key:** Proteksi checkout mengandalkan *database row lock* (`lockForUpdate`) dan pemotongan stok atomik; belum menggunakan *Idempotency-Key* HTTP header.
