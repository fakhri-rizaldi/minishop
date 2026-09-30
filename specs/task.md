# Task — MiniShop

> Daftar periksa pengerjaan. Kerjakan **berurutan per fase**, centang saat selesai. Tiap tugas menyebut requirement yang dipenuhi.
> Aturan untuk agen: satu fase (atau sub-bagian) per sesi, baca `requirement.md` + `design.md` bagian terkait dulu, jangan lompat fase, jangan tambah fitur di luar spec.
> Deadline case study: **2 hari** sejak diberikan.

**Rencana waktu (saran):** Hari 1 = Fase 0–5 (backend selesai + teruji) dan mulai Fase 6. Hari 2 = Fase 6–11 (frontend) lalu Fase 12 (dokumentasi & kirim). Fase 13 (deploy) hanya bila ada sisa waktu.

---

## Fase 0 — Setup & Fondasi

### 0.1 Lingkungan
- [x] Pastikan PHP, Composer, Node.js, dan PostgreSQL terpasang; catat versinya untuk README
- [x] Buat database `minishop` dan `minishop_test` di PostgreSQL
- [x] Buat user database khusus proyek (bukan superuser) dan beri hak ke kedua database

### 0.2 Repository
- [x] `git init` di root `minishop/` (bila belum) dan tambahkan `.gitignore` root (`node_modules/`, `vendor/`, `.env`, `.env.*` kecuali `.env.example`, `dist/`, `storage/*.key`, log)
- [x] Pastikan `AGENTS.md` dan `.agents/rules/` sudah ada dan menyebut folder `specs/`
- [x] Commit awal: struktur folder + spec

### 0.3 Backend (Laravel 13)
- [x] Salin `backend/.env.example` → `.env`, isi koneksi `pgsql`, jalankan `php artisan key:generate`
- [x] Ubah `.env.example` agar memuat variabel di `design.md` §6 (tanpa nilai rahasia) (REQ-NFR-08)
- [x] Pasang API + Sanctum: `php artisan install:api` (menghasilkan `routes/api.php` dan tabel token)
- [x] Publikasikan config CORS (`php artisan config:publish cors`) dan set `allowed_origins` dari `FRONTEND_URL` (REQ-NFR-04)
- [x] Set locale/timezone aplikasi ke `Asia/Jakarta` dan pesan validasi bahasa Indonesia (bila perlu, tambah file bahasa)
- [x] Set format error JSON untuk 404 dan 500 pada permintaan `api/*` sesuai `design.md` §3.3 (REQ-NFR-03)
- [x] Verifikasi: `php artisan serve` berjalan dan `GET /api/up` atau route uji mengembalikan JSON

### 0.4 Frontend (Vue)
- [x] Salin `frontend/.env.example` → `.env` dengan `VITE_API_BASE_URL`
- [x] Pasang dependensi: `vue-router`, `pinia`, `axios`
- [x] Pasang Tailwind CSS v4 dengan plugin Vite (atau putuskan CSS biasa dan catat di `design.md` §1.3)
- [x] Pasang font: `@fontsource-variable/fraunces` dan `@fontsource-variable/manrope` (lihat `ui.md` §4)
- [x] Buat struktur folder `src/` sesuai `design.md` §1.4 (folder kosong boleh)
- [x] Verifikasi: `npm run dev` menampilkan halaman awal tanpa error konsol

---

## Fase 1 — Database, Model, Seeder (REQ-DATA)

### 1.1 Migration (urutan penting karena foreign key)
- [x] `create_categories_table`: `name` (100, unique), `slug` (120, unique), timestamps
- [x] `create_products_table`: kolom sesuai `design.md` §2.2, `foreignId('category_id')->constrained()->restrictOnDelete()`, `softDeletes()`
- [x] Di migration products: tambah `CHECK (price >= 0)` dan `CHECK (stock >= 0)` lewat `DB::statement` (REQ-DATA-04)
- [x] `create_orders_table`: `order_number` (20, unique), `customer_name`, `customer_email`, `total`, timestamps; index `created_at`
- [x] `create_order_items_table`: FK `order_id` (cascade), FK `product_id` (restrict), snapshot `product_name`, `unit_price`, `quantity`, `subtotal`; `UNIQUE(order_id, product_id)`; `CHECK (quantity >= 1)`
- [x] Pastikan `price`, `total`, `unit_price`, `subtotal` bertipe `unsignedBigInteger` (REQ-DATA-03)
- [x] Jalankan `php artisan migrate:fresh` — tanpa error
- [x] Uji manual: coba `UPDATE products SET stock = -1` di psql → ditolak constraint

### 1.2 Model
- [x] `Category`: `$fillable`, relasi `products()`
- [x] `Product`: `SoftDeletes`, `$fillable`, `casts` integer untuk price/stock, relasi `category()`
- [x] `Product`: scope `search($term)` (ILIKE, escape `%` `_` `\`) dan scope `inCategory($slug)`
- [x] `Order`: `$fillable`, relasi `items()`, helper pembuat `order_number` (`MS-YYMMDD-XXXXXX`, ulangi bila bentrok)
- [x] `OrderItem`: `$fillable`, relasi `order()` dan `product()` (pakai `withTrashed()`), `$timestamps = false`
- [x] `User`: pastikan memakai `HasApiTokens`

### 1.3 Factory & Seeder
- [x] `CategoryFactory`, `ProductFactory`, `OrderFactory` (untuk tes)
- [x] `CategorySeeder`: 4 kategori beserta slug
- [x] `ProductSeeder`: 10 produk dengan nama, deskripsi singkat, harga, stok, `image_url` nyata (mis. `https://picsum.photos/seed/<slug>/600/600`)
- [x] Pastikan di seeder produk: ≥ 1 stok 0, ≥ 1 stok ≤ 5, harga bervariasi, tersebar di semua kategori (REQ-DATA-01)
- [x] `AdminSeeder`: user `admin@minishop.test` dengan password ter-hash
- [x] Daftarkan semua seeder di `DatabaseSeeder` dengan urutan kategori → produk → admin
- [x] Verifikasi: `php artisan migrate:fresh --seed` dua kali berturut-turut tanpa error (REQ-DATA-02)

---

## Fase 2 — API Publik: Katalog (REQ-CAT)

- [x] `CategoryResource` dan `ProductResource` sesuai contoh di `design.md` §3.2
- [x] `GET /api/categories` → daftar kategori terurut nama
- [x] `GET /api/products`: eager load `category` (REQ-NFR-05), filter `search` dan `category` (slug), urut terbaru/nama konsisten, paginasi `per_page` default 12 maks 50 (REQ-CAT-02..05)
- [x] Validasi query: `per_page` di luar batas dipaksa ke batas; `category` yang tidak ada → hasil kosong (bukan error)
- [x] `GET /api/products/{id}`: 404 JSON untuk id tidak ada/terhapus (REQ-CAT-10)
- [x] Uji manual dengan curl/Postman: search huruf besar/kecil, kombinasi search + category, `search=%` tidak membocorkan semua data
- [x] Verifikasi jumlah query tidak bertambah saat `per_page` naik (Telescope/`DB::listen` atau log)

---

## Fase 3 — Checkout (REQ-CO)

### 3.1 Validasi
- [x] `StoreOrderRequest` dengan aturan di `design.md` §3.4, termasuk `distinct` dan `exists` yang mengabaikan produk soft-deleted
- [x] Pesan validasi berbahasa Indonesia; kunci error memakai notasi titik (`customer.email`, `items.0.quantity`)

### 3.2 Service & exception
- [x] `InsufficientStockException` membawa daftar `{product_id, name, requested, available}`
- [x] `CheckoutService::place()` sesuai pseudocode `design.md` §4.3: transaksi, urut id, `lockForUpdate`, cek stok, buat order + item (snapshot), kurangi stok, hitung total dari harga DB (REQ-CO-06, REQ-CO-07, REQ-CO-08)
- [x] Bungkus dalam `DB::transaction(..., 3)` untuk retry deadlock
- [x] Render exception → JSON 409 dengan `code: INSUFFICIENT_STOCK` (REQ-CO-05)
- [x] Kesalahan lain → rollback otomatis dan 500 tanpa detail internal (REQ-CO-09)

### 3.3 Endpoint
- [x] `OrderResource` + `OrderItemResource` (bentuk sesuai contoh)
- [x] `POST /api/orders` → 201 `OrderResource`
- [x] `GET /api/orders/{order_number}` → 200 / 404 JSON (REQ-CO-12)
- [x] Throttle wajar untuk `POST /api/orders` (mis. 30/menit per IP)

### 3.4 Verifikasi manual
- [x] Checkout sukses: stok berkurang tepat sebesar qty, total benar
- [x] Kirim `price` palsu di payload → diabaikan, total tetap dari DB
- [x] Qty melebihi stok pada salah satu dari dua item → 409, **tidak ada** Order baru, stok item lain **tidak** berubah
- [x] Produk yang sudah di-soft-delete di payload → 422/409 sesuai desain, tanpa perubahan data
- [x] Payload ganda `product_id` sama → 422

---

## Fase 4 — Auth & API Admin (REQ-AUTH, REQ-ADM, REQ-ORD)

### 4.1 Auth
- [x] `LoginRequest` + `POST /api/admin/login` dengan `throttle:5,1` (REQ-AUTH-01..03)
- [x] Pesan gagal login sama untuk email/password salah (REQ-AUTH-02)
- [x] `POST /api/admin/logout` mencabut token saat ini → 204 (REQ-AUTH-06)
- [x] Kelompokkan route admin di `Route::middleware('auth:sanctum')->prefix('admin')`
- [x] Verifikasi: semua `/api/admin/*` tanpa token → 401 JSON (REQ-AUTH-04)

### 4.2 Produk admin
- [x] `StoreProductRequest` dan `UpdateProductRequest` sesuai `design.md` §3.4 (REQ-ADM-03)
- [x] `GET /api/admin/products` (search, category, paginasi) (REQ-ADM-01)
- [x] `GET /api/admin/products/{id}`
- [x] `POST /api/admin/products` → 201 (REQ-ADM-02)
- [x] `PUT /api/admin/products/{id}` → 200 (REQ-ADM-04)
- [x] `DELETE /api/admin/products/{id}` → soft delete, 204 (REQ-ADM-05)
- [x] Verifikasi: produk terhapus hilang dari `/api/products` tetapi order lama yang memuatnya tetap tampil utuh

### 4.3 Order admin
- [x] `GET /api/admin/orders` terbaru dulu, eager load item (atau `withCount`), paginasi (REQ-ORD-01)
- [x] `GET /api/admin/orders/{id}` dengan item (REQ-ORD-02) dan 404 JSON (REQ-ORD-03)

---

## Fase 5 — Tes Backend

- [x] Konfigurasi `phpunit.xml` memakai database `minishop_test` (PostgreSQL), bukan SQLite, dan `RefreshDatabase`
- [x] `CatalogTest`: list, search (case-insensitive), filter kategori, kombinasi, paginasi, detail 404
- [x] `CheckoutTest`: sukses (order, item snapshot, stok berkurang, total)
- [x] `CheckoutTest`: stok kurang → 409, nol Order, stok tidak berubah
- [x] `CheckoutTest`: validasi 422 (email salah, items kosong, qty 0, id ganda, produk tidak ada)
- [x] `CheckoutTest`: harga dari klien diabaikan
- [x] `CheckoutTest`: produk soft-deleted ditolak
- [x] `CheckoutTest`: dua item, item kedua gagal → rollback penuh (item pertama tidak berkurang)
- [x] `AdminProductTest`: 401 tanpa token; create/update/delete valid; 422 untuk data salah; soft delete tidak menghapus order lama
- [x] `AuthTest`: login benar/salah, throttle 429
- [x] Jalankan `php artisan test` — semua hijau

---

## Fase 6 — Fondasi Frontend

### 6.1 Token & gaya dasar (lihat `ui.md`)
- [x] `assets/styles.css`: variabel warna, tipografi, radius, spasi sesuai `ui.md` §2–§4
- [x] Hubungkan token ke Tailwind lewat `@theme` (bila memakai Tailwind)
- [x] Impor font variabel di `main.js`; set `font-family` body dan heading
- [x] Reset fokus: cincin fokus global sesuai `ui.md` §8
- [x] Dukungan `prefers-reduced-motion`

### 6.2 Infrastruktur
- [x] `api/http.js`: instance axios dengan `baseURL`, header `Authorization` bila ada token, interceptor 401 untuk `/admin/*` (REQ-AUTH-07)
- [x] `api/catalog.js`, `api/orders.js`, `api/admin.js` sebagai fungsi tipis
- [x] `utils/format.js`: `formatRupiah(n)` (`Intl.NumberFormat('id-ID', {style:'currency', currency:'IDR', maximumFractionDigits:0})`), format tanggal Indonesia
- [x] `utils/storage.js`: baca/tulis `localStorage` dengan `try/catch` dan validasi bentuk
- [x] `router/index.js`: semua route di `design.md` §1.5 + guard `/admin/*` (REQ-AUTH-05)
- [x] `stores/auth.js`: token, user, `login`, `logout`, `isLoggedIn`, pulihkan dari storage
- [x] `stores/cart.js`: lihat Fase 8

### 6.3 Komponen dasar
- [x] `AppHeader`: wordmark, kolom cari (di katalog), tautan keranjang dengan badge jumlah item
- [x] `PriceText`, `StockBadge` (tersedia / menipis ≤ 5 / habis) (REQ-CAT-08, 09)
- [x] `EmptyState`, `ErrorState` (dengan tombol coba lagi), `SkeletonCard`
- [x] `Toast` (satu pusat notifikasi, `aria-live="polite"`)
- [x] `ConfirmDialog` (fokus terjebak di dalam, Esc menutup, fokus kembali ke pemicu)
- [x] `NotFoundView`

---

## Fase 7 — Katalog (REQ-CAT)

- [x] `useDebounce` (250 ms) (REQ-CAT-03)
- [x] `SearchInput` dengan label tersembunyi yang terbaca pembaca layar
- [x] `CategoryFilter` (memuat `/api/categories`, opsi "Semua")
- [x] `ProductCard`: gambar (dengan `alt`, rasio tetap, placeholder saat gagal — REQ-CAT-12), nama, kategori, harga, `StockBadge`, tombol tambah
- [x] `ProductGrid` dengan kolom responsif (`ui.md` §5.1)
- [x] `Pagination`
- [x] `CatalogView`: sinkronkan `q`, `category`, `page` dengan query URL (REQ-CAT-05); reset `page` ke 1 saat filter berubah
- [x] `CatalogView`: keadaan loading (skeleton), kosong (REQ-CAT-06), error + retry (REQ-CAT-11)
- [x] Batalkan/abaikan respons lama saat query berubah cepat (cegah hasil balapan)
- [x] `ProductDetailView`: gambar besar, info lengkap, pemilih jumlah, tombol tambah, 404 (REQ-CAT-07, 10)
- [x] Verifikasi manual: search, filter, gabungan, refresh mempertahankan hasil, tombol back browser bekerja

---

## Fase 8 — Keranjang (REQ-CART)

### 8.1 Store
- [x] State `items`: `{ id, name, price, image_url, stock, quantity }`
- [x] Action `add(product, qty)`: gabung bila sudah ada, tolak bila melebihi stok dan kembalikan pesan (REQ-CART-01..03)
- [x] Action `setQuantity(id, qty)`: jepit ke `[1, stock]` dan kembalikan pesan bila dijepit (REQ-CART-04..06)
- [x] Action `remove(id)` (REQ-CART-07) dan `clear()`
- [x] Getter `subtotal(item)`, `total`, `count` (REQ-CART-08)
- [x] Action `applyStockConflicts(items409)`: jepit qty ke `available`, hapus bila 0, kembalikan daftar perubahan (REQ-CO-11)
- [x] Persist: `watch` store → `localStorage`; saat inisialisasi validasi bentuk data (REQ-CART-10, 11)

### 8.2 UI
- [x] `QuantityStepper` (− / input angka / +), tombol − nonaktif di 1, tombol + nonaktif di stok maksimum, label aksesibel
- [x] `CartLine`: gambar, nama, harga satuan, stepper, subtotal, tombol hapus
- [x] `CartSummary`: total, tombol "Lanjut ke checkout"
- [x] `CartView`: daftar + ringkasan; keadaan kosong (REQ-CART-09)
- [x] `FloatingCartButton`: tombol lingkaran melayang di pojok kanan bawah berlogo cart dengan badge merah total item di keranjang yang dapat diklik ke `/cart` (REQ-CART-01, ui.md §6.10)
- [x] Badge jumlah di `AppHeader` bereaksi terhadap perubahan
- [x] Verifikasi: tambah dari list & detail, tambah ganda menggabung, lewat stok ditolak, refresh mempertahankan, `localStorage` diisi sampah → aplikasi tetap jalan

---

## Fase 9 — Checkout (REQ-CO)

- [x] `CheckoutView`: guard cart kosong → redirect `/cart` (REQ-CO-02)
- [x] Formulir nama + email dengan label, `autocomplete`, dan pesan error per kolom dari 422 (REQ-CO-04)
- [x] Ringkasan item dan total (hanya baca)
- [x] Submit: kirim `{customer, items:[{product_id, quantity}]}` saja; nonaktifkan tombol + tampilkan status "Memproses…" (REQ-CO-03)
- [x] Tangani 409: `applyStockConflicts`, tampilkan daftar perubahan, arahkan kembali ke keranjang (REQ-CO-11)
- [x] Tangani 422 dan kesalahan jaringan tanpa mengosongkan form
- [x] Sukses: simpan `order_number`, `cart.clear()`, redirect ke halaman sukses (REQ-CO-10)
- [x] `OrderSuccessView`: ambil `GET /api/orders/{order_number}` (tetap benar saat refresh), tampilkan nomor, pemesan, item, total; 404 (REQ-CO-12)
- [x] Verifikasi ujung ke ujung: katalog → tambah → checkout → sukses → stok di katalog berkurang
- [x] Uji dua tab: tab A menghabiskan stok, tab B checkout → muncul penanganan 409 yang benar

---

## Fase 10 — Panel Admin (REQ-AUTH, REQ-ADM, REQ-ORD)

- [x] `AdminLayout`: navigasi (Produk, Order), tombol Keluar, tetap responsif
- [x] `LoginView`: form, pesan error, redirect ke tujuan semula (REQ-AUTH-01, 02, 03)
- [x] `ProductListView`: tabel + search + filter + paginasi; di layar kecil berubah jadi kartu (REQ-ADM-01, REQ-NFR-01)
- [x] `ProductFormView` (tambah & ubah): semua kolom, pilihan kategori, pratinjau gambar dari URL, error 422 per kolom (REQ-ADM-02..04)
- [x] Hapus produk lewat `ConfirmDialog` yang menyebut nama produk; setelah sukses muat ulang daftar (REQ-ADM-05)
- [x] `OrderListView`: tabel order terbaru dulu + paginasi (REQ-ORD-01)
- [x] `OrderDetailView`: pemesan, item snapshot, total, 404 (REQ-ORD-02, 03)
- [x] Tombol Keluar memanggil logout API lalu membersihkan sesi (REQ-AUTH-06)
- [x] Verifikasi: akses `/admin/products` tanpa login → redirect; token dihapus manual → API 401 → redirect

---

## Fase 11 — Poles, Responsif, Aksesibilitas & Transisi (REQ-NFR, REQ-CAT-13..16)

- [x] Seksi Sambutan Hero Landing di halaman utama dengan wallpaper botani, fade gradient, animasi teks (*staggered fade-up*), dan CTA smooth scroll ke katalog (REQ-CAT-16)
- [x] Transisi Grid FLIP `<TransitionGroup>` pada katalog (`.catalog-grid-*`, `v-move`, `leave-active: absolute`) (REQ-CAT-13, 14)
- [x] Transisi status kosong *fade-slide* (`.fade-slide-*`)
- [x] Mode hemat gerak `prefers-reduced-motion: reduce` mematikan semua transisi instan (REQ-CAT-15)
- [x] Uji tampilan di 360, 768, 1024, 1440 px pada semua halaman, tanpa scroll horizontal (REQ-NFR-01)
- [x] Navigasi penuh dengan keyboard: header, kartu, stepper, dialog, form
- [x] Setiap input punya `<label>`; error form terhubung lewat `aria-describedby`; toast terbaca pembaca layar (REQ-NFR-06)
- [x] Periksa kontras sesuai tabel di `ui.md` §2.3; tidak ada teks `#647a67`
- [x] Semua `<img>` punya `alt` bermakna dan atribut lebar/tinggi (cegah layout shift)
- [x] Tidak ada `console.error`/warning Vue di alur utama
- [x] Cek `ui.md` §10 (daftar periksa anti-"AI slop"): tanpa gradien dekoratif, tanpa glassmorphism, radius berjenjang, teks aksi konsisten
- [x] Lint/format (ESLint + Prettier di frontend, Pint di backend)

---

## Fase 12 — Dokumentasi & Pengiriman

### 12.1 README.md (REQ-NFR-09)
- [x] Overview singkat aplikasi
- [x] Tech stack + alasan singkat (ambil dari `design.md` §1.3)
- [x] Cara menjalankan lokal: prasyarat, buat database, `composer install`, salin `.env`, `key:generate`, `migrate --seed`, `php artisan serve`, `npm install`, salin `.env`, `npm run dev`, akun admin dev (REQ-NFR-07)
- [x] Cara menjalankan tes
- [x] Tabel endpoint API (method, path, auth, contoh request/response singkat) dari `design.md` §3
- [x] *Known limitations*: tanpa idempotency key, pencarian belum pakai `pg_trgm`, tanpa status order, tanpa upload gambar, gambar dummy dari layanan pihak ketiga, akun admin dari seeder, dll.

### 12.2 AI_USAGE.md (REQ-NFR-09)
- [x] Tulis tools AI yang dipakai (Antigravity, skill/rules yang dipasang, termasuk bahwa spec di `specs/` disusun dengan bantuan AI lalu ditinjau manual)
- [x] Sertakan 1–2 prompt krusial yang **benar-benar dipakai**: (a) prompt `CheckoutService` (transaksi + `lockForUpdate` + rollback), (b) prompt logika keranjang (`add`/`setQuantity`/`applyStockConflicts`)
- [x] Untuk tiap prompt: tulis singkat apa yang diubah/dikoreksi manual setelah output AI

### 12.3 Pemeriksaan akhir
- [x] Ikuti panduan README, pastikan seluruh dependensi dan migration seed jalan dari nol
- [x] `migrate:fresh --seed` menghasilkan ≥ 10 produk terlihat di katalog (terdapat 23 produk) (REQ-DATA-01)
- [x] Tidak ada `.env`, key, atau password nyata di riwayat git (REQ-NFR-08)
- [ ] Repo publik/dapat diakses reviewer; tautan valid
- [ ] Kirim email: **To** albert.christanto@roketin.com · **Cc** melanie.khairunnisa@roketin.com, feyza.khairan@roketin.com, hiring@roketin.com · **Subject** `Fullstack Engineer_<NamaAnda>`; isi: tautan repo, tautan demo (bila ada), catatan singkat

---

## Fase 13 — Deployment (Opsional, nilai plus)

- [ ] Frontend ke Vercel/Netlify: set `VITE_API_BASE_URL` ke URL backend; tambahkan aturan rewrite SPA ke `index.html`
- [ ] Backend + PostgreSQL ke Railway/Render: set env, jalankan `php artisan migrate --force --seed` sekali
- [ ] Set `FRONTEND_URL` di backend ke domain frontend (CORS)
- [ ] Uji alur checkout di lingkungan deploy
- [ ] Tambahkan tautan demo di README

---

## Definition of Done (seluruh proyek)

- [x] Semua requirement bertag `[Wajib]` terpenuhi dan dapat didemokan
- [x] `php artisan test` hijau (34 tes lolos)
- [x] Stok tidak pernah negatif, bahkan pada checkout bersamaan (transaksi `lockForUpdate`)
- [x] README dan AI_USAGE lengkap; proyek jalan lokal dari nol mengikuti README
- [x] Seed ≥ 5–10 produk sehingga katalog tidak kosong saat direview (23 produk terdaftar)
