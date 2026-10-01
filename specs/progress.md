# Progress — MiniShop

> Catatan serah-terima antar sesi. **Dibaca agen di awal setiap sesi sebagai pengganti menjelajah codebase.**
> Diperbarui agen di akhir setiap sub-tugas. **Tulis ulang bagian yang berubah, jangan menambah riwayat panjang.** Jaga di bawah 80 baris.

## Status

- Fase aktif: Selesai (Seluruh Fase 0–12 Selesai & Lulus Definition of Done)
- Terakhir selesai: Fase 12.3 (Pemeriksaan Akhir, Sanitasi Git .gitignore, & Verifikasi Lengkap) (REQ-NFR-08, REQ-DATA-01)
- **Tugas berikutnya:** Pengiriman Repositori / Opsional: Fase 13 (Deployment)
- Terakhir diperbarui: 2026-09-30

## Cara menjalankan (isi saat sudah berfungsi)

| Bagian | Perintah | Keterangan |
|---|---|---|
| Backend | `cd backend` lalu `php artisan serve` | http://localhost:8000 |
| Frontend | `cd frontend` lalu `npm run dev` | http://localhost:5173 |
| Tes backend | `cd backend` lalu `php artisan test` | memakai database `minishop_test` |
| Reset data | `php artisan migrate:fresh --seed` | minta izin dulu |

## Peta kode (isi seiring file dibuat; satu baris per file penting)

- frontend/src/views/CatalogView.vue — landing page sambutan hero dengan animasi staggered + katalog produk publik, pencarian, filter, dan sinkron URL (REQ-CAT-01..06, 11..16)
- frontend/src/components/FloatingCartButton.vue — tombol lingkaran melayang di pojok kanan bawah berlogo cart & badge merah total item yang mengarah ke /cart (REQ-CART-01, ui.md §6.10)
- frontend/src/stores/feedback.js — manajemen state animasi bounce tombol keranjang melayang (REQ-CART-01)
- frontend/src/views/admin/{ProductListView,ProductFormView,OrderListView,OrderDetailView,LoginView}.vue — panel admin lengkap (REQ-AUTH-*, REQ-ADM-*, REQ-ORD-*)
- frontend/src/components/AdminLayout.vue — layout dan sidebar panel admin (ui.md §5.8)
- frontend/src/views/CheckoutView.vue — formulir checkout, proteksi cart kosong, penanganan konflik stok 409 & validasi 422 (REQ-CO-01..11)
- frontend/src/views/OrderSuccessView.vue — tampilan konfirmasi order diterima, snapshot item & rincian total (REQ-CO-10, 12)
- frontend/src/views/CartView.vue — halaman keranjang belanja lengkap dengan daftar item, ringkasan harga & modal konfirmasi kosongkan (REQ-CART-01..11)
- frontend/src/components/{CartLine,CartSummary}.vue — komponen baris produk di keranjang dan panel ringkasan checkout (REQ-CART-04..08)
- frontend/src/views/ProductDetailView.vue — tampilan detail produk, breadcrumb, stepper jumlah & penanganan 404 (REQ-CAT-07..10)
- frontend/src/components/QuantityStepper.vue — kontrol stepper jumlah minus/plus yang dijepit min/max stok (REQ-CART-04..06)
- frontend/src/components/{ProductCard,ProductGrid,Pagination}.vue — kartu produk rasio 1:1, grid responsif 2/3/4 kolom & paginasi (REQ-CAT-01, 02, 08, 09, 12)
- frontend/src/composables/useDebounce.js — helper debounce 250ms untuk input pencarian (REQ-CAT-03)
- frontend/src/components/{SearchInput,CategoryFilter}.vue — komponen kontrol pencarian dan filter kategori (REQ-CAT-03, 04)
- frontend/src/components/{AppHeader,PriceText,StockBadge,EmptyState,ErrorState,SkeletonCard,Toast,ConfirmDialog}.vue — komponen UI dasar (ui.md)
- frontend/src/stores/{auth,cart,toast}.js — manajemen state autentikasi admin, keranjang, notifikasi (REQ-CART-*, REQ-AUTH-*)
- frontend/src/api/{http,catalog,orders,admin}.js — modul API client axios (REQ-CAT-*, REQ-CO-*, REQ-ADM-*)
- frontend/src/utils/{format,storage}.js — helper format Rupiah/tanggal & wrapper localStorage aman (REQ-CART-04)
- frontend/src/router/index.js — routing SPA dan guard navigasi admin (design.md §1.5)
- frontend/src/assets/styles.css — CSS variables token palet gelap, font Fraunces/Manrope, animasi hero, & reset fokus (ui.md)
- backend/app/Http/Controllers/Api/Admin/{AuthController,ProductController,OrderController}.php — API admin (REQ-AUTH-*, REQ-ADM-*, REQ-ORD-*)
- backend/app/Services/CheckoutService.php — transaksi database checkout, lockForUpdate, cek stok, potong stok (REQ-CO-06..08)
- backend/app/Http/Controllers/Api/{CategoryController,ProductController,OrderController}.php — API publik (REQ-CAT-*, REQ-CO-*)

## Keputusan & penyimpangan dari spec

- [2026-10-01] Perbaikan Binding Prop Dialog Konfirmasi Hapus Produk Admin — Memperbaiki ketidaksesuaian nama prop antara `ProductListView.vue` (sebelumnya `:show` dan `:danger`) dengan `ConfirmDialog.vue` (yang mendefinisikan `isOpen` dan `isDanger`). `ConfirmDialog.vue` kini mendukung kedua nama prop secara kompatibel (`activeOpen`, `activeDanger`, dan multi-event emits), serta `ProductListView.vue` telah diselaraskan ke `:is-open` dan `:is-danger` sehingga modal konfirmasi hapus produk muncul dan berfungsi dengan baik.
- [2026-09-30] Perbaikan Typehint Parameter Controller Admin Order & Produk — Mengubah deklarasi parameter metode `show`, `update`, `destroy` pada controller backend (`Admin\OrderController.php`, `Admin\ProductController.php`, `ProductController.php`) menjadi `string|int $id` dan menambahkan dukungan pencarian ganda (berdasarkan numeric `id` maupun string `order_number`) untuk mencegah galat PHP `TypeError: Argument #1 ($id) must be of type int, string given` saat parameter rute dikirim oleh dispatcher Laravel.
- [2026-09-30] Perbaikan Tampilan Harga & Subtotal Struk Pesanan (Receipt) — Menyelaraskan nama properti harga satuan (`unit_price` / `price`), subtotal (`subtotal`), dan total pesanan (`total` / `total_price`) pada `OrderSuccessView.vue`, `OrderDetailView.vue`, `OrderListView.vue`, serta `OrderResource.php` dan `OrderItemResource.php` sehingga struk pesanan menampilkan nilai Rupiah riil yang akurat tanpa terpotong menjadi 0.
- [2026-09-30] Perbaikan Struktur Payload & Binding Validasi Checkout — Memperbaiki struktur payload `POST /api/orders` pada `CheckoutView.vue` agar mengirim objek bersarang `customer: { name, email }` sesuai kontrak `StoreOrderRequest.php` dan `design.md` §3.2 (sebelumnya terkirim flat `customer_name` dan `customer_email`), serta memperbaiki pembacaan error validasi 422 untuk key `customer.name` dan `customer.email`.
- [2026-09-30] Penambahan Data Produk Seeder (Total 23 Produk) — Menambahkan 13 produk botani baru ke `ProductSeeder.php` sehingga total produk seeder menjadi 23 produk yang tersebar di 4 kategori (Tanaman Indoor, Pot & Wadah, Perlengkapan Taman, Dekorasi Rumah) dengan variasi harga, stok habis (0), stok menipis (≤ 5), dan stok normal. Spec terkait (`requirement.md`, `SeederTest.php`) sudah disinkronkan. Spec terkait sudah diperbarui: ya
- [2026-09-30] Tampilan Jumlah Stok Asli Polos (Tanpa Badge) — Mengubah tampilan kuantitas stok di `StockBadge.vue` menjadi teks bersih (*plain text*) tanpa bungkus border/pill badge (`text-xs tabular-nums`) dengan pewarnaan semantik kontekstual (merah untuk 'Habis', kuning untuk 'Sisa X', dan muted untuk 'Stok: X') agar visual kartu produk lebih rapi, minimalis, dan tidak terdistraksi bingkai berlebih.
- [2026-09-30] Navigasi Balik Langsung ke Seksi Katalog — Mengubah tautan kembali pada breadcrumb, 404 detail produk, dan keranjang belanja menjadi `/#katalog` sehingga saat pengguna kembali dari detail produk, layar langsung meluncur ke seksi katalog tanpa mengulang tampilan hero banner. Spec terkait (`requirement.md`, `ui.md`, `design.md`, `task.md`) sudah disinkronkan. Spec terkait sudah diperbarui: ya
- [2026-09-30] Navbar Slide-Down Black Blur Panel — Menghapus teks brand pada header, menyisakan tombol burger mengambang di kanan atas yang ketika diklik memicu animasi slide-down panel kotak hitam semi-transparan ber-blur (`rgba(0, 0, 0, 0.58)` + `blur(16px)`) menutupi layar dengan teks tautan rata kiri agak tengah dan animasi garis bawah (*underline*) interaktif saat di-hover. Spec terkait (`requirement.md`, `ui.md`, `design.md`, `task.md`) sudah disinkronkan. Spec terkait sudah diperbarui: ya
- [2026-09-30] Landing Hero Section dengan Animasi Masuk — Menambahkan seksi hero sambutan di bagian atas katalog utama dengan animasi teks berjenjang (staggered delay 120ms), deskripsi kurasi botani, dan CTA scroll halus menuju seksi katalog. Spec terkait (`requirement.md`, `ui.md`, `task.md`) sudah disinkronkan. Spec terkait sudah diperbarui: ya
- [2026-09-30] Pop-up Lingkaran Umpan Balik Keranjang — Mengganti banner toast teks sukses "Tambah ke keranjang" dengan pop-up lingkaran mengambang berlogo cart dengan badge angka kuantitas merah sesuai permintaan pengguna. Spec terkait (`requirement.md`, `design.md`, `ui.md`, `task.md`) sudah disinkronkan. Spec terkait sudah diperbarui: ya
- [2026-09-30] Transisi Interaktif Katalog & Debounce 250ms — Menambahkan transisi FLIP `<TransitionGroup>` pada grid produk, fade-slide empty state, dan debounce search 250ms sesuai `specs/spec.md`. Spec terkait (`requirement.md`, `design.md`, `ui.md`, `task.md`) sudah disinkronkan. Spec terkait sudah diperbarui: ya

## Masalah terbuka

<!-- Bug, hal yang belum jelas, atau pertanyaan untuk pemilik. Hapus bila sudah selesai. -->

## Catatan lingkungan

- Windows PowerShell / CMD; path proyek berspasi (`.:\...\MiniShop`), selalu dikutip.
- Versi: PHP 8.4.23, Composer 2.10.1, Node.js v26.5.0, PostgreSQL 18.4
- DB User: `minishop` (Password: `minishop_secret`) | DB: `minishop`, `minishop_test`
