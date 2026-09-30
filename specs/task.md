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
- [ ] `git init` di root `minishop/` (bila belum) dan tambahkan `.gitignore` root (`node_modules/`, `vendor/`, `.env`, `.env.*` kecuali `.env.example`, `dist/`, `storage/*.key`, log)
- [ ] Pastikan `AGENTS.md` dan `.agents/rules/` sudah ada dan menyebut folder `specs/`
- [ ] Commit awal: struktur folder + spec

### 0.3 Backend (Laravel 13)
- [ ] Salin `backend/.env.example` → `.env`, isi koneksi `pgsql`, jalankan `php artisan key:generate`
- [ ] Ubah `.env.example` agar memuat variabel di `design.md` §6 (tanpa nilai rahasia) (REQ-NFR-08)
- [ ] Pasang API + Sanctum: `php artisan install:api` (menghasilkan `routes/api.php` dan tabel token)
- [ ] Publikasikan config CORS (`php artisan config:publish cors`) dan set `allowed_origins` dari `FRONTEND_URL` (REQ-NFR-04)
- [ ] Set locale/timezone aplikasi ke `Asia/Jakarta` dan pesan validasi bahasa Indonesia (bila perlu, tambah file bahasa)
- [ ] Set format error JSON untuk 404 dan 500 pada permintaan `api/*` sesuai `design.md` §3.3 (REQ-NFR-03)
- [ ] Verifikasi: `php artisan serve` berjalan dan `GET /api/up` atau route uji mengembalikan JSON

### 0.4 Frontend (Vue)
- [ ] Salin `frontend/.env.example` → `.env` dengan `VITE_API_BASE_URL`
- [ ] Pasang dependensi: `vue-router`, `pinia`, `axios`
- [ ] Pasang Tailwind CSS v4 dengan plugin Vite (atau putuskan CSS biasa dan catat di `design.md` §1.3)
- [ ] Pasang font: `@fontsource-variable/fraunces` dan `@fontsource-variable/manrope` (lihat `ui.md` §4)
- [ ] Buat struktur folder `src/` sesuai `design.md` §1.4 (folder kosong boleh)
- [ ] Verifikasi: `npm run dev` menampilkan halaman awal tanpa error konsol

---

## Fase 1 — Database, Model, Seeder (REQ-DATA)

### 1.1 Migration (urutan penting karena foreign key)
- [ ] `create_categories_table`: `name` (100, unique), `slug` (120, unique), timestamps
- [ ] `create_products_table`: kolom sesuai `design.md` §2.2, `foreignId('category_id')->constrained()->restrictOnDelete()`, `softDeletes()`
- [ ] Di migration products: tambah `CHECK (price >= 0)` dan `CHECK (stock >= 0)` lewat `DB::statement` (REQ-DATA-04)
- [ ] `create_orders_table`: `order_number` (20, unique), `customer_name`, `customer_email`, `total`, timestamps; index `created_at`
- [ ] `create_order_items_table`: FK `order_id` (cascade), FK `product_id` (restrict), snapshot `product_name`, `unit_price`, `quantity`, `subtotal`; `UNIQUE(order_id, product_id)`; `CHECK (quantity >= 1)`
- [ ] Pastikan `price`, `total`, `unit_price`, `subtotal` bertipe `unsignedBigInteger` (REQ-DATA-03)
- [ ] Jalankan `php artisan migrate:fresh` — tanpa error
- [ ] Uji manual: coba `UPDATE products SET stock = -1` di psql → ditolak constraint

### 1.2 Model
- [ ] `Category`: `$fillable`, relasi `products()`
- [ ] `Product`: `SoftDeletes`, `$fillable`, `casts` integer untuk price/stock, relasi `category()`
- [ ] `Product`: scope `search($term)` (ILIKE, escape `%` `_` `\`) dan scope `inCategory($slug)`
- [ ] `Order`: `$fillable`, relasi `items()`, helper pembuat `order_number` (`MS-YYMMDD-XXXXXX`, ulangi bila bentrok)
- [ ] `OrderItem`: `$fillable`, relasi `order()` dan `product()` (pakai `withTrashed()`), `$timestamps = false`
- [ ] `User`: pastikan memakai `HasApiTokens`

### 1.3 Factory & Seeder
- [ ] `CategoryFactory`, `ProductFactory`, `OrderFactory` (untuk tes)
- [ ] `CategorySeeder`: 4 kategori beserta slug
- [ ] `ProductSeeder`: 10 produk dengan nama, deskripsi singkat, harga, stok, `image_url` nyata (mis. `https://picsum.photos/seed/<slug>/600/600`)
- [ ] Pastikan di seeder produk: ≥ 1 stok 0, ≥ 1 stok ≤ 5, harga bervariasi, tersebar di semua kategori (REQ-DATA-01)
- [ ] `AdminSeeder`: user `admin@minishop.test` dengan password ter-hash
- [ ] Daftarkan semua seeder di `DatabaseSeeder` dengan urutan kategori → produk → admin
- [ ] Verifikasi: `php artisan migrate:fresh --seed` dua kali berturut-turut tanpa error (REQ-DATA-02)

---

## Fase 2 — API Publik: Katalog (REQ-CAT)

- [ ] `CategoryResource` dan `ProductResource` sesuai contoh di `design.md` §3.2
- [ ] `GET /api/categories` → daftar kategori terurut nama
- [ ] `GET /api/products`: eager load `category` (REQ-NFR-05), filter `search` dan `category` (slug), urut terbaru/nama konsisten, paginasi `per_page` default 12 maks 50 (REQ-CAT-02..05)
- [ ] Validasi query: `per_page` di luar batas dipaksa ke batas; `category` yang tidak ada → hasil kosong (bukan error)
- [ ] `GET /api/products/{id}`: 404 JSON untuk id tidak ada/terhapus (REQ-CAT-10)
- [ ] Uji manual dengan curl/Postman: search huruf besar/kecil, kombinasi search + category, `search=%` tidak membocorkan semua data
- [ ] Verifikasi jumlah query tidak bertambah saat `per_page` naik (Telescope/`DB::listen` atau log)

---

## Fase 3 — Checkout (REQ-CO)

### 3.1 Validasi
- [ ] `StoreOrderRequest` dengan aturan di `design.md` §3.4, termasuk `distinct` dan `exists` yang mengabaikan produk soft-deleted
- [ ] Pesan validasi berbahasa Indonesia; kunci error memakai notasi titik (`customer.email`, `items.0.quantity`)

### 3.2 Service & exception
- [ ] `InsufficientStockException` membawa daftar `{product_id, name, requested, available}`
- [ ] `CheckoutService::place()` sesuai pseudocode `design.md` §4.3: transaksi, urut id, `lockForUpdate`, cek stok, buat order + item (snapshot), kurangi stok, hitung total dari harga DB (REQ-CO-06, REQ-CO-07, REQ-CO-08)
- [ ] Bungkus dalam `DB::transaction(..., 3)` untuk retry deadlock
- [ ] Render exception → JSON 409 dengan `code: INSUFFICIENT_STOCK` (REQ-CO-05)
- [ ] Kesalahan lain → rollback otomatis dan 500 tanpa detail internal (REQ-CO-09)

### 3.3 Endpoint
- [ ] `OrderResource` + `OrderItemResource` (bentuk sesuai contoh)
- [ ] `POST /api/orders` → 201 `OrderResource`
- [ ] `GET /api/orders/{order_number}` → 200 / 404 JSON (REQ-CO-12)
- [ ] Throttle wajar untuk `POST /api/orders` (mis. 30/menit per IP)

### 3.4 Verifikasi manual
- [ ] Checkout sukses: stok berkurang tepat sebesar qty, total benar
- [ ] Kirim `price` palsu di payload → diabaikan, total tetap dari DB
- [ ] Qty melebihi stok pada salah satu dari dua item → 409, **tidak ada** Order baru, stok item lain **tidak** berubah
- [ ] Produk yang sudah di-soft-delete di payload → 422/409 sesuai desain, tanpa perubahan data
- [ ] Payload ganda `product_id` sama → 422

---

## Fase 4 — Auth & API Admin (REQ-AUTH, REQ-ADM, REQ-ORD)

### 4.1 Auth
- [ ] `LoginRequest` + `POST /api/admin/login` dengan `throttle:5,1` (REQ-AUTH-01..03)
- [ ] Pesan gagal login sama untuk email/password salah (REQ-AUTH-02)
- [ ] `POST /api/admin/logout` mencabut token saat ini → 204 (REQ-AUTH-06)
- [ ] Kelompokkan route admin di `Route::middleware('auth:sanctum')->prefix('admin')`
- [ ] Verifikasi: semua `/api/admin/*` tanpa token → 401 JSON (REQ-AUTH-04)

### 4.2 Produk admin
- [ ] `StoreProductRequest` dan `UpdateProductRequest` sesuai `design.md` §3.4 (REQ-ADM-03)
- [ ] `GET /api/admin/products` (search, category, paginasi) (REQ-ADM-01)
- [ ] `GET /api/admin/products/{id}`
- [ ] `POST /api/admin/products` → 201 (REQ-ADM-02)
- [ ] `PUT /api/admin/products/{id}` → 200 (REQ-ADM-04)
- [ ] `DELETE /api/admin/products/{id}` → soft delete, 204 (REQ-ADM-05)
- [ ] Verifikasi: produk terhapus hilang dari `/api/products` tetapi order lama yang memuatnya tetap tampil utuh

### 4.3 Order admin
- [ ] `GET /api/admin/orders` terbaru dulu, eager load item (atau `withCount`), paginasi (REQ-ORD-01)
- [ ] `GET /api/admin/orders/{id}` dengan item (REQ-ORD-02) dan 404 JSON (REQ-ORD-03)

---

## Fase 5 — Tes Backend

- [ ] Konfigurasi `phpunit.xml` memakai database `minishop_test` (PostgreSQL), bukan SQLite, dan `RefreshDatabase`
- [ ] `CatalogTest`: list, search (case-insensitive), filter kategori, kombinasi, paginasi, detail 404
- [ ] `CheckoutTest`: sukses (order, item snapshot, stok berkurang, total)
- [ ] `CheckoutTest`: stok kurang → 409, nol Order, stok tidak berubah
- [ ] `CheckoutTest`: validasi 422 (email salah, items kosong, qty 0, id ganda, produk tidak ada)
- [ ] `CheckoutTest`: harga dari klien diabaikan
- [ ] `CheckoutTest`: produk soft-deleted ditolak
- [ ] `CheckoutTest`: dua item, item kedua gagal → rollback penuh (item pertama tidak berkurang)
- [ ] `AdminProductTest`: 401 tanpa token; create/update/delete valid; 422 untuk data salah; soft delete tidak menghapus order lama
- [ ] `AuthTest`: login benar/salah, throttle 429
- [ ] Jalankan `php artisan test` — semua hijau

---

## Fase 6 — Fondasi Frontend

### 6.1 Token & gaya dasar (lihat `ui.md`)
- [ ] `assets/styles.css`: variabel warna, tipografi, radius, spasi sesuai `ui.md` §2–§4
- [ ] Hubungkan token ke Tailwind lewat `@theme` (bila memakai Tailwind)
- [ ] Impor font variabel di `main.js`; set `font-family` body dan heading
- [ ] Reset fokus: cincin fokus global sesuai `ui.md` §8
- [ ] Dukungan `prefers-reduced-motion`

### 6.2 Infrastruktur
- [ ] `api/http.js`: instance axios dengan `baseURL`, header `Authorization` bila ada token, interceptor 401 untuk `/admin/*` (REQ-AUTH-07)
- [ ] `api/catalog.js`, `api/orders.js`, `api/admin.js` sebagai fungsi tipis
- [ ] `utils/format.js`: `formatRupiah(n)` (`Intl.NumberFormat('id-ID', {style:'currency', currency:'IDR', maximumFractionDigits:0})`), format tanggal Indonesia
- [ ] `utils/storage.js`: baca/tulis `localStorage` dengan `try/catch` dan validasi bentuk
- [ ] `router/index.js`: semua route di `design.md` §1.5 + guard `/admin/*` (REQ-AUTH-05)
- [ ] `stores/auth.js`: token, user, `login`, `logout`, `isLoggedIn`, pulihkan dari storage
- [ ] `stores/cart.js`: lihat Fase 8

### 6.3 Komponen dasar
- [ ] `AppHeader`: wordmark, kolom cari (di katalog), tautan keranjang dengan badge jumlah item
- [ ] `PriceText`, `StockBadge` (tersedia / menipis ≤ 5 / habis) (REQ-CAT-08, 09)
- [ ] `EmptyState`, `ErrorState` (dengan tombol coba lagi), `SkeletonCard`
- [ ] `Toast` (satu pusat notifikasi, `aria-live="polite"`)
- [ ] `ConfirmDialog` (fokus terjebak di dalam, Esc menutup, fokus kembali ke pemicu)
- [ ] `NotFoundView`

---

## Fase 7 — Katalog (REQ-CAT)

- [ ] `useDebounce` (300 ms) (REQ-CAT-03)
- [ ] `SearchInput` dengan label tersembunyi yang terbaca pembaca layar
- [ ] `CategoryFilter` (memuat `/api/categories`, opsi "Semua")
- [ ] `ProductCard`: gambar (dengan `alt`, rasio tetap, placeholder saat gagal — REQ-CAT-12), nama, kategori, harga, `StockBadge`, tombol tambah
- [ ] `ProductGrid` dengan kolom responsif (`ui.md` §5.1)
- [ ] `Pagination`
- [ ] `CatalogView`: sinkronkan `q`, `category`, `page` dengan query URL (REQ-CAT-05); reset `page` ke 1 saat filter berubah
- [ ] `CatalogView`: keadaan loading (skeleton), kosong (REQ-CAT-06), error + retry (REQ-CAT-11)
- [ ] Batalkan/abaikan respons lama saat query berubah cepat (cegah hasil balapan)
- [ ] `ProductDetailView`: gambar besar, info lengkap, pemilih jumlah, tombol tambah, 404 (REQ-CAT-07, 10)
- [ ] Verifikasi manual: search, filter, gabungan, refresh mempertahankan hasil, tombol back browser bekerja

---

## Fase 8 — Keranjang (REQ-CART)

### 8.1 Store
- [ ] State `items`: `{ id, name, price, image_url, stock, quantity }`
- [ ] Action `add(product, qty)`: gabung bila sudah ada, tolak bila melebihi stok dan kembalikan pesan (REQ-CART-01..03)
- [ ] Action `setQuantity(id, qty)`: jepit ke `[1, stock]` dan kembalikan pesan bila dijepit (REQ-CART-04..06)
- [ ] Action `remove(id)` (REQ-CART-07) dan `clear()`
- [ ] Getter `subtotal(item)`, `total`, `count` (REQ-CART-08)
- [ ] Action `applyStockConflicts(items409)`: jepit qty ke `available`, hapus bila 0, kembalikan daftar perubahan (REQ-CO-11)
- [ ] Persist: `watch` store → `localStorage`; saat inisialisasi validasi bentuk data (REQ-CART-10, 11)

### 8.2 UI
- [ ] `QuantityStepper` (− / input angka / +), tombol − nonaktif di 1, tombol + nonaktif di stok maksimum, label aksesibel
- [ ] `CartLine`: gambar, nama, harga satuan, stepper, subtotal, tombol hapus
- [ ] `CartSummary`: total, tombol "Lanjut ke checkout"
- [ ] `CartView`: daftar + ringkasan; keadaan kosong (REQ-CART-09)
- [ ] Badge jumlah di `AppHeader` bereaksi terhadap perubahan
- [ ] Verifikasi: tambah dari list & detail, tambah ganda menggabung, lewat stok ditolak, refresh mempertahankan, `localStorage` diisi sampah → aplikasi tetap jalan

---

## Fase 9 — Checkout (REQ-CO)

- [ ] `CheckoutView`: guard cart kosong → redirect `/cart` (REQ-CO-02)
- [ ] Formulir nama + email dengan label, `autocomplete`, dan pesan error per kolom dari 422 (REQ-CO-04)
- [ ] Ringkasan item dan total (hanya baca)
- [ ] Submit: kirim `{customer, items:[{product_id, quantity}]}` saja; nonaktifkan tombol + tampilkan status "Memproses…" (REQ-CO-03)
- [ ] Tangani 409: `applyStockConflicts`, tampilkan daftar perubahan, arahkan kembali ke keranjang (REQ-CO-11)
- [ ] Tangani 422 dan kesalahan jaringan tanpa mengosongkan form
- [ ] Sukses: simpan `order_number`, `cart.clear()`, redirect ke halaman sukses (REQ-CO-10)
- [ ] `OrderSuccessView`: ambil `GET /api/orders/{order_number}` (tetap benar saat refresh), tampilkan nomor, pemesan, item, total; 404 (REQ-CO-12)
- [ ] Verifikasi ujung ke ujung: katalog → tambah → checkout → sukses → stok di katalog berkurang
- [ ] Uji dua tab: tab A menghabiskan stok, tab B checkout → muncul penanganan 409 yang benar

---

## Fase 10 — Panel Admin (REQ-AUTH, REQ-ADM, REQ-ORD)

- [ ] `AdminLayout`: navigasi (Produk, Order), tombol Keluar, tetap responsif
- [ ] `LoginView`: form, pesan error, redirect ke tujuan semula (REQ-AUTH-01, 02, 03)
- [ ] `ProductListView`: tabel + search + filter + paginasi; di layar kecil berubah jadi kartu (REQ-ADM-01, REQ-NFR-01)
- [ ] `ProductFormView` (tambah & ubah): semua kolom, pilihan kategori, pratinjau gambar dari URL, error 422 per kolom (REQ-ADM-02..04)
- [ ] Hapus produk lewat `ConfirmDialog` yang menyebut nama produk; setelah sukses muat ulang daftar (REQ-ADM-05)
- [ ] `OrderListView`: tabel order terbaru dulu + paginasi (REQ-ORD-01)
- [ ] `OrderDetailView`: pemesan, item snapshot, total, 404 (REQ-ORD-02, 03)
- [ ] Tombol Keluar memanggil logout API lalu membersihkan sesi (REQ-AUTH-06)
- [ ] Verifikasi: akses `/admin/products` tanpa login → redirect; token dihapus manual → API 401 → redirect

---

## Fase 11 — Poles, Responsif, Aksesibilitas (REQ-NFR)

- [ ] Uji tampilan di 360, 768, 1024, 1440 px pada semua halaman, tanpa scroll horizontal (REQ-NFR-01)
- [ ] Navigasi penuh dengan keyboard: header, kartu, stepper, dialog, form
- [ ] Setiap input punya `<label>`; error form terhubung lewat `aria-describedby`; toast terbaca pembaca layar
- [ ] Periksa kontras sesuai tabel di `ui.md` §2.3; tidak ada teks `#647a67`
- [ ] Semua `<img>` punya `alt` bermakna dan atribut lebar/tinggi (cegah layout shift)
- [ ] Tidak ada `console.error`/warning Vue di alur utama
- [ ] Cek `ui.md` §10 (daftar periksa anti-"AI slop"): tanpa gradien dekoratif, tanpa glassmorphism, radius berjenjang, teks aksi konsisten
- [ ] Lint/format (ESLint + Prettier di frontend, Pint di backend)

---

## Fase 12 — Dokumentasi & Pengiriman

### 12.1 README.md (REQ-NFR-09)
- [ ] Overview singkat aplikasi
- [ ] Tech stack + alasan singkat (ambil dari `design.md` §1.3)
- [ ] Cara menjalankan lokal: prasyarat, buat database, `composer install`, salin `.env`, `key:generate`, `migrate --seed`, `php artisan serve`, `npm install`, salin `.env`, `npm run dev`, akun admin dev (REQ-NFR-07)
- [ ] Cara menjalankan tes
- [ ] Tabel endpoint API (method, path, auth, contoh request/response singkat) dari `design.md` §3
- [ ] *Known limitations*: tanpa idempotency key, pencarian belum pakai `pg_trgm`, tanpa status order, tanpa upload gambar, gambar dummy dari layanan pihak ketiga, akun admin dari seeder, dll.

### 12.2 AI_USAGE.md (REQ-NFR-09)
- [ ] Tulis tools AI yang dipakai (Antigravity, skill/rules yang dipasang, termasuk bahwa spec di `specs/` disusun dengan bantuan AI lalu ditinjau manual)
- [ ] Sertakan 1–2 prompt krusial yang **benar-benar dipakai**: (a) prompt `CheckoutService` (transaksi + `lockForUpdate` + rollback), (b) prompt logika keranjang (`add`/`setQuantity`/`applyStockConflicts`)
- [ ] Untuk tiap prompt: tulis singkat apa yang diubah/dikoreksi manual setelah output AI

### 12.3 Pemeriksaan akhir
- [ ] Clone repo ke folder baru, ikuti README apa adanya, pastikan jalan dari nol
- [ ] `migrate:fresh --seed` menghasilkan ≥ 10 produk terlihat di katalog (REQ-DATA-01)
- [ ] Tidak ada `.env`, key, atau password nyata di riwayat git (REQ-NFR-08)
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

- [ ] Semua requirement bertag `[Wajib]` terpenuhi dan dapat didemokan
- [ ] `php artisan test` hijau
- [ ] Stok tidak pernah negatif, bahkan pada checkout bersamaan
- [ ] README dan AI_USAGE lengkap; proyek jalan lokal dari nol mengikuti README
- [ ] Seed ≥ 5–10 produk sehingga katalog tidak kosong saat direview
