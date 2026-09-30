# UI — MiniShop

> Sumber kebenaran untuk **TAMPILAN**. Dibaca agen sebelum menyentuh apa pun di `frontend/`.
> Bagian dari SDD lapisan *design*: `design.md` menjelaskan struktur teknis, file ini menjelaskan wujud visualnya. Bila ada konflik soal perilaku, `requirement.md` menang.

## 1. Arah Desain

**Subjek:** toko kecil dengan katalog produk rumah dan tanaman (tema seed di `design.md` §2.3). Pengunjung ingin melihat produk dengan jelas, membandingkan harga, dan checkout tanpa hambatan. Admin ingin tabel yang rapat dan mudah dipindai.

**Suasana:** tenang, botani, rapi. Latar hijau-hitam yang dalam, teks hijau pucat, satu warna aksen mint untuk aksi utama. Gambar produk adalah bagian paling berwarna di layar, jadi antarmukanya sengaja diam.

**Prinsip**
1. **Satu hal yang diingat:** wordmark dan judul halaman memakai serif berkarakter; sisanya tenang dan disiplin.
2. **Struktur dibentuk oleh permukaan, bukan bayangan:** latar → permukaan → permukaan terangkat, dipisahkan garis tipis. Tanpa bayangan, tanpa gradien, tanpa efek kaca (glassmorphism).
3. **Aksen hemat:** mint terang `#c5efcb` hanya untuk aksi utama dan fokus. Kalau semuanya mint, tidak ada yang menonjol.
4. **Kata-kata untuk pengguna:** kalimat aktif, huruf kecil biasa (sentence case), aksi bernama sama di seluruh alur.
5. **Gambar produk berbicara:** rasio tetap, sudut membulat lembut, tidak ditimpa teks atau overlay.

---

## 2. Warna

### 2.1 Palet (sesuai permintaan) dan perannya

Dipakai sebagai tema gelap tunggal. Tidak ada mode terang.

| Token | Hex | Peran |
|---|---|---|
| `--color-bg` | `#020402` | Latar halaman |
| `--color-surface` | `#1f241f` | Kartu, panel, header, tabel |
| `--color-surface-raised` | `#3c433b` | Hover permukaan, chip terpilih, garis pemisah |
| `--color-border-strong` | `#647a67` | Batas input & tombol sekunder (komponen interaktif), ikon, status nonaktif |
| `--color-text-muted` | `#758173` | Teks bantu **hanya di atas `--color-bg`** |
| `--color-text-secondary` | `#8fa38a` | Label, metadata, placeholder |
| `--color-accent` | `#a9c5a0` | Tautan, ikon aktif, tombol sekunder (teks) |
| `--color-accent-hover` | `#b8d2b3` | Hover/aktif tombol utama dan tautan |
| `--color-text` | `#c6dec6` | Teks isi utama |
| `--color-primary` | `#c5efcb` | Tombol utama, cincin fokus, judul, harga |

### 2.2 Tambahan di luar palet (status)

Palet tidak punya warna untuk error/peringatan. Dua tambahan minimal, boleh ditolak; bila ditolak, ganti dengan ikon + teks tanpa warna khusus.

| Token | Hex | Peran |
|---|---|---|
| `--color-danger` | `#e58b7f` | Error form, tombol hapus, "Stok habis" |
| `--color-warning` | `#e3c26b` | "Stok menipis" |

Status sukses memakai `--color-primary` (tidak perlu warna baru).

### 2.3 Kontras (dihitung WCAG 2.x)

| Teks / elemen | Di atas `bg` | Di atas `surface` | Di atas `raised` |
|---|---|---|---|
| `primary` `#c5efcb` | 16.2 | 12.5 | 8.1 |
| `text` `#c6dec6` | 14.4 | 11.0 | 7.1 |
| `accent-hover` `#b8d2b3` | 12.6 | 9.7 | 6.3 |
| `accent` `#a9c5a0` | 10.9 | 8.4 | 5.4 |
| `text-secondary` `#8fa38a` | 7.6 | 5.8 | **3.8** |
| `text-muted` `#758173` | 5.0 | **3.9** | **2.5** |
| `border-strong` `#647a67` | 4.4 | 3.4 | **2.2** |
| `danger` `#e58b7f` | 8.2 | 6.3 | **4.1** |
| `warning` `#e3c26b` | 12.0 | 9.2 | 5.9 |
| Teks `bg` di atas `primary` (tombol) | 16.2 | | |

Angka tebal = di bawah 4.5:1 untuk teks kecil.

### 2.4 Aturan pemakaian warna

- Teks kecil (< 18 px, atau < 14 px tebal) harus ≥ 4.5:1: gunakan `text`, `primary`, `accent`, `accent-hover`, `text-secondary` (di atas `bg`/`surface`). `text-muted` hanya di atas `bg`.
- **`--color-border-strong` (`#647a67`) tidak boleh jadi warna teks.** Ia untuk garis, ikon, dan batas kontrol (target ≥ 3:1 untuk komponen UI, terpenuhi di atas `bg`/`surface`).
- `danger` sebagai teks tidak dipakai di atas `surface-raised`.
- Garis pemisah dekoratif: `--color-surface-raised`. Batas kontrol yang harus terlihat: `--color-border-strong`.
- Tombol utama: latar `primary`, teks `bg`. Hover: latar `accent-hover`. Tekan: latar `accent`.
- Jangan pakai `#000` atau `#fff` murni; ambil dari token.
- Tanpa gradien apa pun. Tanpa `box-shadow` (pengecualian: dialog memakai overlay `rgba(2, 4, 2, 0.72)` di belakangnya, bukan bayangan).

### 2.5 CSS variables

```css
:root {
  color-scheme: dark;

  /* warna */
  --color-bg: #020402;
  --color-surface: #1f241f;
  --color-surface-raised: #3c433b;
  --color-border-strong: #647a67;
  --color-text-muted: #758173;
  --color-text-secondary: #8fa38a;
  --color-accent: #a9c5a0;
  --color-accent-hover: #b8d2b3;
  --color-text: #c6dec6;
  --color-primary: #c5efcb;
  --color-danger: #e58b7f;
  --color-warning: #e3c26b;
  --color-overlay: rgba(2, 4, 2, 0.72);

  /* tipografi */
  --font-display: "Fraunces Variable", "Iowan Old Style", Georgia, serif;
  --font-body: "Manrope Variable", system-ui, -apple-system, "Segoe UI", sans-serif;

  /* bentuk */
  --radius-control: 8px;   /* tombol, input */
  --radius-card: 14px;     /* kartu, panel, dialog */
  --radius-media: 10px;    /* gambar di dalam kartu (radius kartu − padding) */
  --radius-pill: 999px;    /* badge, chip */

  /* ruang (basis 4px) */
  --space-1: 4px;  --space-2: 8px;  --space-3: 12px; --space-4: 16px;
  --space-5: 24px; --space-6: 32px; --space-7: 48px; --space-8: 72px;

  --content-max: 1200px;
}

body {
  background: var(--color-bg);
  color: var(--color-text);
  font-family: var(--font-body);
}
```

### 2.6 Pemetaan Tailwind v4 (bila dipakai)

```css
@import "tailwindcss";

@theme {
  --color-bg: #020402;
  --color-surface: #1f241f;
  --color-raised: #3c433b;
  --color-line: #647a67;
  --color-muted: #758173;
  --color-subtle: #8fa38a;
  --color-accent: #a9c5a0;
  --color-accent-hover: #b8d2b3;
  --color-ink: #c6dec6;
  --color-primary: #c5efcb;
  --color-danger: #e58b7f;
  --color-warning: #e3c26b;

  --font-display: "Fraunces Variable", "Iowan Old Style", Georgia, serif;
  --font-sans: "Manrope Variable", system-ui, sans-serif;

  --radius-control: 8px;
  --radius-card: 14px;
  --radius-media: 10px;
}
```

---

## 3. Bentuk, Ruang, Permukaan

| Elemen | Radius | Permukaan | Batas |
|---|---|---|---|
| Halaman | — | `bg` | — |
| Header | 0 | `surface` | garis bawah 1 px `raised` |
| Kartu produk / panel | 14 px | `surface` | 1 px `raised` |
| Gambar dalam kartu | 10 px | `raised` (saat memuat) | — |
| Tombol, input | 8 px | lihat §6.1–6.2 | 1 px `border-strong` (sekunder/input) |
| Badge stok, chip kategori | pill | `raised` atau transparan | 1 px sesuai status |
| Dialog | 14 px | `surface` | 1 px `raised` |

- Radius **berjenjang** sesuai hierarki, bukan satu nilai untuk semuanya.
- Kedalaman dibuat dari perubahan permukaan (`bg` → `surface` → `raised`), bukan bayangan.
- Jarak antar bagian besar: `--space-7`; antar kartu: `--space-5` (desktop) / `--space-4` (mobile); padding kartu: `--space-4`.

---

## 4. Tipografi

### 4.1 Pilihan font

| Peran | Font | Alasan |
|---|---|---|
| Display / judul | **Fraunces** (variabel) | Serif "soft" dengan karakter hangat dan organik; cocok dengan hijau botani dan terasa elegan tanpa kaku. Dipakai untuk wordmark dan judul halaman/produk. |
| UI / isi / angka | **Manrope** (variabel) | Sans geometris yang bersih dan terbaca di ukuran kecil; kontras jelas dengan Fraunces; angka rapi untuk harga dan tabel admin. |

Alternatif bila ingin lebih dramatis: **Cormorant Garamond** untuk judul + **DM Sans** untuk isi. Cormorant sangat elegan tetapi tipis di ukuran kecil, jadi hanya pakai ≥ 28 px. Hindari Inter, Roboto, Arial.

### 4.2 Pemasangan (self-host lewat npm, tanpa request ke pihak ketiga)

```bash
npm i @fontsource-variable/fraunces @fontsource-variable/manrope
```

```js
// src/main.js
import "@fontsource-variable/fraunces";
import "@fontsource-variable/manrope";
import "./assets/styles.css";
```

Nama keluarga font di CSS: `"Fraunces Variable"` dan `"Manrope Variable"`. Cek README paket bila nama berbeda pada versi terpasang. Paket ini memakai `font-display: swap`. Bila ingin sumbu opsional Fraunces (mis. `opsz`), lihat dokumentasi paket.

### 4.3 Skala

Rasio ±1.25, basis 16 px. Judul memakai Fraunces; sisanya Manrope.

| Gaya | Font | Ukuran / tinggi baris | Bobot | Catatan |
|---|---|---|---|---|
| Wordmark | Fraunces | 24 / 1.1 | 600 | "MiniShop", satu kata, tanpa ornamen |
| Judul halaman (h1) | Fraunces | 40 / 1.1 (mobile 30) | 500 | letter-spacing −0.01em |
| Judul bagian (h2) | Fraunces | 28 / 1.2 | 500 | |
| Judul kartu / produk (h3) | Manrope | 16 / 1.35 | 600 | |
| Harga besar (detail) | Manrope | 28 / 1.1 | 700 | `font-variant-numeric: tabular-nums` |
| Harga kartu | Manrope | 16 / 1.2 | 700 | tabular-nums |
| Isi | Manrope | 16 / 1.6 | 400 | maks. lebar baris 65 karakter (`max-width: 65ch`) |
| Label form, metadata | Manrope | 14 / 1.4 | 500 | warna `text-secondary` |
| Catatan kecil | Manrope | 13 / 1.4 | 400 | tidak di bawah 13 px |

### 4.4 Aturan tipografi

- Sentence case di seluruh antarmuka. **Tanpa teks kapital semua** untuk label, dan tanpa "eyebrow" kecil di atas judul.
- Jangan menonjolkan satu kata di tengah judul (italic/berwarna/tebal sendiri).
- Tabel dan harga memakai `tabular-nums` agar angka sejajar.
- Format harga: `Rp 185.000` lewat `Intl.NumberFormat("id-ID")`, tanpa desimal.
- Garis baris isi < 80 karakter; teks serif diberi tinggi baris sedikit lebih longgar (sudah pada skala).

---

## 5. Layout & Halaman

Kerangka: `max-width: var(--content-max)`, rata tengah, padding samping 16 px (mobile) / 24 px (≥ 768) / 32 px (≥ 1200). Konten rata kiri; hanya keadaan kosong dan halaman sukses yang dipusatkan.

### 5.1 Breakpoint

| Nama | Lebar | Grid produk |
|---|---|---|
| base | 360–639 | 2 kolom, jarak 16 |
| md | 640–1023 | 3 kolom, jarak 24 |
| lg | 1024–1279 | 4 kolom |
| xl | ≥ 1280 | 4 kolom (lebar konten dibatasi 1200) |

### 5.2 Header (semua halaman publik)

```
┌────────────────────────────────────────────────────────────────┐
│ MiniShop            [ Cari produk...            ]      Keranjang (3) │
└────────────────────────────────────────────────────────────────┘
```
Sticky di atas, `surface`, garis bawah `raised`. Di mobile: wordmark + ikon keranjang, kolom cari turun ke baris kedua. Badge jumlah keranjang: pill `primary` dengan teks `bg`.

### 5.3 Katalog `/`

```
Katalog                                   (h1, Fraunces)
[ Semua ] [ Tanaman ] [ Pot & Wadah ] [ Perlengkapan ] [ Dekorasi ]   ← chip, bisa scroll horizontal di mobile
──────────────────────────────────────────────
┌───────┐ ┌───────┐ ┌───────┐ ┌───────┐
│ gambar│ │       │ │       │ │       │   gambar 1:1
├───────┤ │       │ │       │ │       │
│ Nama  │ │       │ │       │ │       │
│ Kategori (secondary)
│ Rp 185.000     [Stok menipis]
│ [ Tambah ke keranjang ]
└───────┘
              « 1 2 3 »
```
Seluruh kartu (gambar + nama) adalah tautan ke detail; tombol tambah adalah kontrol terpisah. Chip terpilih: latar `raised`, teks `primary`, batas `primary`; tidak terpilih: transparan, batas `border-strong`, teks `text`.

### 5.4 Detail produk `/products/:id`

```
Katalog › Nama produk                         (breadcrumb, teks secondary)
┌──────────────┐   Nama produk                (h1)
│              │   Tanaman (secondary)
│   gambar     │   Rp 185.000                 (harga besar)
│    1:1       │   [Stok menipis: 3]
│              │   Deskripsi (max 65ch)
└──────────────┘   Jumlah [− 1 +]  [ Tambah ke keranjang ]
```
Desktop dua kolom (gambar 5/12, info 7/12); mobile bertumpuk. Bila stok 0: stepper dan tombol nonaktif, badge "Stok habis".

### 5.5 Keranjang `/cart`

```
Keranjang                                       (h1)
┌──────────────────────────────┐  ┌──────────────┐
│ [img] Nama       [− 2 +]  Rp 370.000  [Hapus] │  │ Ringkasan     │
│ [img] Nama       [− 1 +]  Rp 100.000  [Hapus] │  │ Total  Rp 470.000
└──────────────────────────────┘  │ [ Lanjut ke checkout ]
                                   └──────────────┘
```
Desktop: daftar 8/12 + ringkasan sticky 4/12. Mobile: ringkasan di bawah daftar. Keadaan kosong: teks "Keranjang masih kosong." + tombol "Lihat katalog".

### 5.6 Checkout `/checkout`

Dua kolom di desktop: form (Nama, Email) di kiri, ringkasan item + total di kanan. Tombol "Buat pesanan" di bawah form; saat proses: teks "Memproses pesanan…" dan nonaktif.

### 5.7 Order berhasil `/orders/:orderNumber/success`

Pusat halaman, satu panel `surface`: judul "Pesanan diterima" (h1), nomor order (Manrope 600, tabular), tabel item (nama, jumlah, harga, subtotal), total, data pemesan, tombol "Belanja lagi". Tidak ada konfeti atau animasi perayaan.

### 5.8 Admin

```
┌─────────┬──────────────────────────────────────────┐
│ MiniShop│ Produk                    [ + Tambah produk ]
│ Admin   │ [cari...] [kategori ▾]
│ Produk  │ ┌──────────────────────────────────────┐
│ Order   │ │ Nama      Kategori   Harga   Stok  Aksi
│         │ │ ...                                  │
│ Keluar  │ └──────────────────────────────────────┘
└─────────┴──────────────────────────────────────────┘
```
Sidebar `surface` lebar 220 px (mobile: menjadi bilah atas dengan menu ringkas). Tabel: header `text-secondary` 14 px, baris dipisah garis `raised`, hover baris `raised`; angka rata kanan; di < 768 px tiap baris menjadi kartu bertumpuk. Form produk: satu kolom, lebar maks. 560 px, pratinjau gambar di sebelahnya pada layar lebar.

---

## 6. Komponen

### 6.1 Tombol

| Varian | Latar | Teks | Batas | Dipakai untuk |
|---|---|---|---|---|
| Utama | `primary` | `bg` | — | Satu aksi utama per tampilan: Tambah ke keranjang, Lanjut ke checkout, Buat pesanan, Simpan produk, Masuk |
| Sekunder | transparan | `accent` | 1 px `border-strong` | Aksi pendukung: Lihat katalog, Ubah, Batal |
| Teks | transparan | `accent` | — | Aksi ringan: Hapus item di keranjang, Coba lagi |
| Bahaya | transparan | `danger` | 1 px `danger` | Hapus produk (di dialog) |

Tinggi 44 px (target sentuh ≥ 44 px), padding horizontal 20 px, teks 15 px bobot 600. Hover: utama → `accent-hover`; sekunder/teks → batas/teks `accent-hover`. Nonaktif: opasitas 0.45, kursor `not-allowed`, tanpa hover. Memuat: teks berubah ("Memproses…"), tanpa spinner dekoratif.

### 6.2 Input

Tinggi 44 px, latar `bg`, batas 1 px `border-strong`, radius 8, teks `text`, placeholder `text-secondary`. Fokus: batas `primary` + cincin fokus (§8). Error: batas `danger`, pesan `danger` 14 px di bawah kolom (ikon + teks, tidak hanya warna). Label selalu terlihat di atas kolom (bukan hanya placeholder).

### 6.3 Badge stok

| Kondisi | Teks | Gaya |
|---|---|---|
| Stok > 5 | *(tidak ditampilkan di kartu)*; di detail: "Stok tersedia: N" | teks `text-secondary` |
| 1–5 | "Stok menipis: N" | pill, batas & teks `warning` |
| 0 | "Stok habis" | pill, batas & teks `danger`; gambar diberi opasitas 0.55 |

### 6.4 Stepper jumlah

Tiga bagian dalam satu kontrol berbatas `border-strong`: tombol − (44×44), input angka (lebar 56, rata tengah, `tabular-nums`), tombol +. Tombol − nonaktif di 1; + nonaktif di stok maksimum. Saat dijepit ke stok, tampilkan pesan bantu di bawahnya: "Stok hanya N."

### 6.5 Kartu produk

Permukaan `surface`, batas 1 px `raised`, radius 14, padding 16. Hover (perangkat pointer): batas berubah ke `border-strong`, tanpa naik/geser/skala. Gambar 1:1 dengan `object-fit: cover`, radius 10. Nama maks. 2 baris (`line-clamp`). Bila gambar gagal: blok `raised` dengan huruf pertama nama produk (Fraunces 40, `text-secondary`).

### 6.6 Toast

Kiri bawah (desktop) / atas tengah (mobile), `surface`, batas `border-strong`, radius 8, hilang otomatis 4 detik, dapat ditutup. `role="status"` `aria-live="polite"`. Maks. satu toast pada satu waktu.

### 6.7 Dialog konfirmasi

Overlay `--color-overlay`; panel `surface` lebar maks. 420 px. Judul (h2 Fraunces 24) menyebut objek: "Hapus Monstera Deliciosa?". Isi satu kalimat konsekuensi: "Produk hilang dari katalog. Order lama tidak terpengaruh." Tombol: "Batal" (sekunder, fokus awal) dan "Hapus produk" (bahaya). Esc menutup; fokus dikembalikan ke pemicu.

### 6.8 Loading, kosong, error

- **Loading:** kartu skeleton dengan blok `raised` berdenyut lembut (opasitas 0.6↔1, 1.4 detik); dimatikan bila `prefers-reduced-motion`.
- **Kosong:** ikon garis sederhana (bukan ilustrasi), satu kalimat, satu tombol aksi.
- **Error:** satu kalimat tentang apa yang terjadi + tombol "Coba lagi". Tidak meminta maaf berlebihan.

### 6.9 Paginasi

Tombol "Sebelumnya", nomor halaman (maks. 5 terlihat), "Berikutnya". Halaman aktif: latar `raised`, teks `primary`, `aria-current="page"`.

---

## 7. Copywriting (Bahasa Indonesia)

Sentence case, kata kerja aktif, nama aksi konsisten dari tombol sampai toast.

| Konteks | Teks |
|---|---|
| Tombol tambah | Tambah ke keranjang → toast "Ditambahkan ke keranjang" |
| Melewati stok | "Stok hanya 3." |
| Keranjang kosong | "Keranjang masih kosong." + [Lihat katalog] |
| Checkout | [Lanjut ke checkout] → [Buat pesanan] → "Memproses pesanan…" |
| Sukses | "Pesanan diterima" |
| Hasil kosong | "Tidak ada produk untuk pencarian ini." + [Reset pencarian] |
| Error muat | "Produk gagal dimuat. Periksa koneksi lalu coba lagi." + [Coba lagi] |
| Konflik stok (409) | "Stok berubah. Monstera Deliciosa disesuaikan menjadi 2, Pot Terakota dihapus dari keranjang." |
| Login gagal | "Email atau password salah." |
| Admin simpan | [Simpan produk] → toast "Produk disimpan" |
| Admin hapus | [Hapus produk] → toast "Produk dihapus" |
| Order tidak ada | "Order tidak ditemukan." + [Belanja lagi] |

Hindari kata klise ("Temukan", "Nikmati", "Mulus", "Tingkatkan"). Nama dan deskripsi produk dummy memakai nama nyata yang spesifik, bukan "Produk 1" atau "Lorem ipsum".

---

## 8. Interaksi, Fokus, Gerak

- **Cincin fokus:** `outline: 2px solid var(--color-primary); outline-offset: 2px;` pada semua elemen interaktif via `:focus-visible`. Jangan pernah `outline: none` tanpa pengganti.
- **Hover** hanya untuk perangkat pointer: `@media (hover: hover)`.
- **Gerak:** hanya untuk menjawab aksi pengguna, 150 ms `ease-out`: perubahan warna/batas, badge keranjang membesar sekilas (scale 1 → 1.15 → 1) saat item ditambah, toast muncul (opasitas). Tidak ada animasi masuk pada tiap bagian halaman, tidak ada parallax.
- `@media (prefers-reduced-motion: reduce)`: matikan skeleton berdenyut, pembesaran badge, dan transisi selain perubahan warna.

---

## 9. Aksesibilitas

- Kontras mengikuti §2.3–2.4; status tidak pernah hanya lewat warna (selalu ada teks/ikon).
- Semua kontrol dapat dijangkau dan dioperasikan dengan keyboard; urutan tab mengikuti urutan visual.
- Satu `<h1>` per halaman; hierarki heading berurutan.
- Gambar produk: `alt` = nama produk; gambar dekoratif `alt=""`.
- Form: `<label for>` untuk setiap input, `aria-invalid` + `aria-describedby` ke pesan error, `autocomplete` yang sesuai (`name`, `email`).
- Landmark: `<header>`, `<nav>`, `<main>`, `<footer>`; tautan lewati ke konten pada halaman publik.
- Target sentuh ≥ 44×44 px.
- Zoom 200% tidak merusak tata letak.

---

## 10. Daftar Periksa "Bukan AI Slop"

Centang sebelum menganggap UI selesai (juga dipakai di `task.md` Fase 11):

- [ ] Tidak ada gradien, glassmorphism, atau bayangan berlapis
- [ ] Radius berjenjang (control 8 / media 10 / card 14), bukan satu nilai di semua tempat
- [ ] Tidak ada label kapital semua bertitik-titik ("NEW · SALE") atau eyebrow di atas judul
- [ ] Tidak ada satu kata yang diwarnai/dimiringkan di tengah judul
- [ ] Tidak ada penomoran 01/02/03 kecuali konten benar-benar berurutan
- [ ] Tidak ada animasi masuk pada setiap bagian
- [ ] Aksen mint hanya untuk aksi utama, fokus, judul, dan harga
- [ ] Teks aksi spesifik ("Buat pesanan", bukan "Kirim"), tanpa panah "→" di setiap tautan
- [ ] Konten dummy spesifik dan masuk akal (nama produk, harga, deskripsi)
- [ ] Font bukan Inter/Roboto/Arial; heading Fraunces, isi Manrope
- [ ] Setiap halaman terlihat rapi pada 360 px dan 1440 px

---

## 11. Definition of Done UI

- [ ] Seluruh warna berasal dari token di §2.5 (tidak ada hex lepas di komponen)
- [ ] Font terpasang lokal via npm dan dimuat tanpa lonjakan tata letak yang mencolok
- [ ] Semua keadaan komponen ada: default, hover, fokus, aktif, nonaktif, memuat, error, kosong
- [ ] Lolos §9 (aksesibilitas) dan §10 (bukan AI slop)
