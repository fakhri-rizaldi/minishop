# Catatan Penggunaan AI — MiniShop

Dokumen ini mencatat transparansi penggunaan *Artificial Intelligence* (AI) selama perancangan, pengembangan, dan pengujian proyek MiniShop.

---

## 1. Alat & Lingkungan AI yang Digunakan

- **AI Assistant:** Google DeepMind **Antigravity** (Advanced Agentic Pair-Programming Assistant).
- **Skill & Aturan Khusus:**
  - `specs/`: Spesifikasi lengkap (`requirement.md`, `design.md`, `task.md`, `ui.md`) dirancang terlebih dahulu dengan panduan spec-driven development dan ditinjau secara manual sebelum kode ditulis.
  - `.agents/rules/`: Aturan tata kelola penulisan kode, arsitektur *clean controller*, dan mode belajar interaktif.
  - `.agents/skills/no-slop-ui`: Panduan antarmuka humanis untuk memastikan desain terbebas dari pola generik (*anti-AI slop*), menggunakan palet warna *Dark Botanical*, tipografi terkurasi (Fraunces & Manrope), dan interaksi yang tenang.

---

## 2. Prompt Krusial & Koreksi Manual

Berikut adalah 2 prompt krusial yang benar-benar digunakan dalam membangun logika inti sistem, beserta catatan tinjauan dan koreksi manual yang dilakukan:

---

### A. Prompt 1: Logika Transaksi Checkout Database & Pessimistic Locking (`CheckoutService.php`)

#### 💬 Teks Prompt
```
Buat service CheckoutService di Laravel 13 untuk memproses pesanan dalam satu DB::transaction.
Kunci baris produk dengan lockForUpdate terurut berdasarkan id untuk mencegah deadlock dan race condition.
Periksa stok untuk setiap item; jika ada stok yang tidak mencukupi, batalkan transaksi dan lempar InsufficientStockException
yang membawa payload rincian item bermasalah (product_id, name, requested, available).
Buat order dengan nomor unik MS-YYMMDD-XXXXXX, simpan snapshot item menggunakan harga database (bukan harga dari klien),
potong stok produk secara atomik, dan kembalikan model Order beserta relasi items.
```

#### 🔍 Koreksi & Penyesuaian Manual
1. **Pencegahan Deadlock:** Memastikan `Product::whereIn('id', $productIds)->orderBy('id')->lockForUpdate()->get()` selalu mengurutkan ID secara menaik (*ascending*) sebelum mengunci baris di PostgreSQL, sehingga transaksi yang berjalan bersamaan tidak saling menunggu (*circular wait*).
2. **Integritas Harga:** Memastikan nilai `unit_price` dan `subtotal` diambil 100% dari kolom database `products.price` untuk menepis potensi manipulasi harga dari sisi klien.
3. **Penanganan Exception:** Mengonfigurasi `bootstrap/app.php` agar menangkap `InsufficientStockException` dan menghasilkan respons JSON berstatus `409 Conflict` dengan kode `INSUFFICIENT_STOCK`.

---

### B. Prompt 2: Manajemen State Keranjang Pinia & Rekonsiliasi Konflik Stok 409 (`cart.js`)

#### 💬 Teks Prompt
```
Buat Pinia store cart di Vue 3 dengan state items: [{ id, name, price, image_url, stock, quantity }].
Implementasikan:
1. Action add(product, qty): menambah produk atau menambah kuantitas jika sudah ada (dijepit maksimal pada stok).
2. Action setQuantity(id, qty): mengubah kuantitas dengan penjepitan pada rentang [1, stock].
3. Action applyStockConflicts(conflictItems): merestrukturisasi isi keranjang saat menerima error 409 dari server
   (menyesuaikan kuantitas ke stok yang tersedia atau menghapus item jika stok 0) serta mengembalikan ringkasan perubahan.
4. Persistensi localStorage yang aman dan tahan terhadap data korup/rusak.
```

#### 🔍 Koreksi & Penyesuaian Manual
1. **Validasi Penyimpanan Aman:** Membungkus pembacaan dan penulisan `localStorage` dalam modul utilitas `storage.js` dengan `try/catch` dan validasi skema array untuk mencegah aplikasi *white-screen* saat storage lokal berisi format JSON tidak valid.
2. **Sinkronisasi Payload Checkout:** Memperbaiki pengiriman payload di `CheckoutView.vue` agar membungkus data pembeli dalam objek bersarang `customer: { name, email }` sesuai spesifikasi kontrak FormRequest backend.
3. **Umpan Balik Visual:** Mengintegrasikan notifikasi pop-up keranjang dinamis dan badge kuantitas merah di tombol keranjang mengambang.

---

## 3. Refleksi & Pembelajaran

Penggunaan AI dengan pendekatan **Spec-Driven Development** (menyusun `specs/` terlebih dahulu sebelum mengode) terbukti mempercepat implementasi sekaligus menjaga arsitektur tetap bersih, teruji (34 PHPUnit tests 100% hijau), dan mudah dirawat. Tinjauan manual tetap menjadi lapisan krusial untuk memastikan kepatuhan kontrak API, konsistensi penamaan properti JSON, dan kesempurnaan pengalaman pengguna.
