# Spec — Transisi Interaktif Katalog, Filter Kategori & Pencarian

> Spec fitur visual dan interaksi. Melengkapi `requirement.md`, `design.md`, `ui.md`, dan spec kartu sebelumnya; bila bentrok, `requirement.md` menang.
> Warna, font, radius, dan token mengikuti `ui.md`. Jangan menulis hex atau font baru di komponen.
> Tag: `[Asumsi]` = keputusan teknis/desain untuk mengatasi tampilan kaku pada filter & search tanpa mengorbankan performa.

---

## 1. User Stories

1. Sebagai **pengunjung**, saya ingin melihat daftar produk bergeser (*reorder*) secara mulus saat berganti kategori, agar mata saya tidak terkejut oleh perubahan tata letak yang mendadak.
2. Sebagai **pengunjung**, saya ingin hasil pencarian muncul secara beruntun (*staggered entrance*) yang halus, agar katalog terasa hidup dan responsif terhadap apa yang saya ketik.
3. Sebagai **pengunjung**, saya ingin transisi ke tampilan "Produk Tidak Ditemukan" (*empty state*) mengalir lembut saat kata kunci tidak cocok, agar tidak terkesan terjadi galat (*error*).
4. Sebagai **pengguna dengan koneksi internet hemat daya / sensitivitas vestibular**, saya ingin animasi tidak lag dan otomatis mati saat mode *reduced motion* aktif di perangkat saya.

---

## 2. Tech Stack & Constraints

**Boleh dipakai**
- Vue 3 `<script setup>` pada komponen `CatalogView.vue` (atau `ProductList.vue`)
- `<TransitionGroup>` bawaan Vue 3 dengan pemanfaatan kelas `v-move` (teknik FLIP murni, 0 KB beban pustaka tambahan)
- Util/komposabel fungsi debounce murni sisi klien untuk input pencarian (delay 250 ms)
- Variabel CSS dan kurva *cubic-bezier* dari `ui.md`
- Tag `:key="product.id"` unik untuk setiap item kartu

**Tidak boleh**
- Memakai indeks *array* (`:key="index"`) pada elemen yang dianimasikan
- Menambahkan pustaka animasi berat pihak ketiga bila cukup diselesaikan dengan `<TransitionGroup>` bawaan Vue
- Memicu kalkulasi ulang tata letak (*layout thrashing*) beruntun pada setiap penekanan tombol (*keystroke*) tanpa debounce
- Menghilangkan *outline* aksesibilitas fokus keyboard selama transisi berlangsung

---

## 3. Business & Interaction Rules

**Pencarian & Debounce**
- **BR-CAT-1** — **Debounced Query**: Perubahan kata kunci pencarian wajib diberi jeda (*debounce*) 250 ms sebelum memicu pemfilteran data dan transisi kartu, guna mencegah animasi tersendat saat pengetikan cepat. `[Asumsi]`
- **BR-CAT-2** — **Preservasi Identitas Kartu**: Setiap kartu produk wajib menggunakan ID produk unik (`product.id`) sebagai atribut `key` agar mekanisme FLIP dapat melacak perpindahan posisi koordinat kartu.

**Perilaku Transisi Grid (FLIP Layout Reordering)**
- **BR-CAT-3** — **Fluid Reordering**: Saat kategori diganti atau hasil pencarian berkurang, kartu yang tidak relevan memudar dan mengecil, sementara kartu yang bertahan bergeser mengisi slot kosong secara bersamaan (*simultaneous slide*).
- **BR-CAT-4** — **Pelepasan Aliran Dokumen (Leave Absolute)**: Kartu yang sedang dalam fase keluar (*leaving*) wajib diberi gaya `position: absolute` sesaat selama animasi berlangsung, agar kartu di sampingnya dapat langsung bergerak mulus tanpa menunggu kartu lama hilang sepenuhnya.
- **BR-CAT-5** — **Stagger Capping**: Efek masuk beruntun (*stagger*) dibatasi maksimal untuk 12 kartu teratas di area pandang (*viewport*) guna mencegah antrean animasi yang memperlambat aksesibilitas pengguna. `[Asumsi]`

**Transisi Status Kosong (Empty State)**
- **BR-CAT-6** — **Cross-Fade Empty State**: Saat filter atau kata kunci menghasilkan 0 produk, grid bertransisi keluar secara bersamaan dengan munculnya pesan produk kosong melalui pergeseran vertikal lembut 8 px.

---

## 4. Acceptance Criteria (SAAT ... MAKA SISTEM HARUS ...)

- **REQ-CAT-01** — SAAT pengunjung memilih tab kategori baru, MAKA SISTEM HARUS menganimasikan kartu produk yang relevan untuk bergeser mengisi posisi baru dalam kurun waktu 250–300 ms tanpa kedipan layar (*flicker*).
- **REQ-CAT-02** — SAAT pengunjung mengetik kata kunci pencarian, MAKA SISTEM HARUS menunggu 250 ms setelah ketukan terakhir sebelum menyaring data dan memunculkan transisi pembaruan kartu.
- **REQ-CAT-03** — SAAT produk yang dicari tidak ditemukan (panjang data = 0), MAKA SISTEM HARUS menampilkan komponen status kosong dengan animasi *fade-slide* lembut dan mematikan grid produk.
- **REQ-CAT-04** — SAAT kartu produk keluar dari tampilan karena filter, MAKA SISTEM HARUS menonaktifkan interaksi klik/pointer (`pointer-events: none`) pada kartu tersebut selama fase keluar.
- **REQ-CAT-05** — SAAT sistem mendeteksi `prefers-reduced-motion: reduce`, MAKA SISTEM HARUS menonaktifkan seluruh perpindahan posisi (kelas `v-move`) dan transisi masuk/keluar secara instan.

---

## 5. Desain Visual & Aturan CSS
Berikut berkas spesifikasi lengkap dari Bagian 1 hingga Bagian 8 plus panduan verifikasi manual, siap disimpan utuh sebagai spec-catalog-transitions.md:

Markdown
# Spec — Transisi Interaktif Katalog, Filter Kategori & Pencarian

> Spec fitur visual dan interaksi. Melengkapi `requirement.md`, `design.md`, `ui.md`, dan spec kartu sebelumnya; bila bentrok, `requirement.md` menang.
> Warna, font, radius, dan token mengikuti `ui.md`. Jangan menulis hex atau font baru di komponen.
> Tag: `[Asumsi]` = keputusan teknis/desain untuk mengatasi tampilan kaku pada filter & search tanpa mengorbankan performa.

---

## 1. User Stories

1. Sebagai **pengunjung**, saya ingin melihat daftar produk bergeser (*reorder*) secara mulus saat berganti kategori, agar mata saya tidak terkejut oleh perubahan tata letak yang mendadak.
2. Sebagai **pengunjung**, saya ingin hasil pencarian muncul secara beruntun (*staggered entrance*) yang halus, agar katalog terasa hidup dan responsif terhadap apa yang saya ketik.
3. Sebagai **pengunjung**, saya ingin transisi ke tampilan "Produk Tidak Ditemukan" (*empty state*) mengalir lembut saat kata kunci tidak cocok, agar tidak terkesan terjadi galat (*error*).
4. Sebagai **pengguna dengan koneksi internet hemat daya / sensitivitas vestibular**, saya ingin animasi tidak lag dan otomatis mati saat mode *reduced motion* aktif di perangkat saya.

---

## 2. Tech Stack & Constraints

**Boleh dipakai**
- Vue 3 `<script setup>` pada komponen `CatalogView.vue` (atau `ProductList.vue`)
- `<TransitionGroup>` bawaan Vue 3 dengan pemanfaatan kelas `v-move` (teknik FLIP murni, 0 KB beban pustaka tambahan)
- Util/komposabel fungsi debounce murni sisi klien untuk input pencarian (delay 250 ms)
- Variabel CSS dan kurva *cubic-bezier* dari `ui.md`
- Tag `:key="product.id"` unik untuk setiap item kartu

**Tidak boleh**
- Memakai indeks *array* (`:key="index"`) pada elemen yang dianimasikan
- Menambahkan pustaka animasi berat pihak ketiga bila cukup diselesaikan dengan `<TransitionGroup>` bawaan Vue
- Memicu kalkulasi ulang tata letak (*layout thrashing*) beruntun pada setiap penekanan tombol (*keystroke*) tanpa debounce
- Menghilangkan *outline* aksesibilitas fokus keyboard selama transisi berlangsung

---

## 3. Business & Interaction Rules

**Pencarian & Debounce**
- **BR-CAT-1** — **Debounced Query**: Perubahan kata kunci pencarian wajib diberi jeda (*debounce*) 250 ms sebelum memicu pemfilteran data dan transisi kartu, guna mencegah animasi tersendat saat pengetikan cepat. `[Asumsi]`
- **BR-CAT-2** — **Preservasi Identitas Kartu**: Setiap kartu produk wajib menggunakan ID produk unik (`product.id`) sebagai atribut `key` agar mekanisme FLIP dapat melacak perpindahan posisi koordinat kartu.

**Perilaku Transisi Grid (FLIP Layout Reordering)**
- **BR-CAT-3** — **Fluid Reordering**: Saat kategori diganti atau hasil pencarian berkurang, kartu yang tidak relevan memudar dan mengecil, sementara kartu yang bertahan bergeser mengisi slot kosong secara bersamaan (*simultaneous slide*).
- **BR-CAT-4** — **Pelepasan Aliran Dokumen (Leave Absolute)**: Kartu yang sedang dalam fase keluar (*leaving*) wajib diberi gaya `position: absolute` sesaat selama animasi berlangsung, agar kartu di sampingnya dapat langsung bergerak mulus tanpa menunggu kartu lama hilang sepenuhnya.
- **BR-CAT-5** — **Stagger Capping**: Efek masuk beruntun (*stagger*) dibatasi maksimal untuk 12 kartu teratas di area pandang (*viewport*) guna mencegah antrean animasi yang memperlambat aksesibilitas pengguna. `[Asumsi]`

**Transisi Status Kosong (Empty State)**
- **BR-CAT-6** — **Cross-Fade Empty State**: Saat filter atau kata kunci menghasilkan 0 produk, grid bertransisi keluar secara bersamaan dengan munculnya pesan produk kosong melalui pergeseran vertikal lembut 8 px.

---

## 4. Acceptance Criteria (SAAT ... MAKA SISTEM HARUS ...)

- **REQ-CAT-01** — SAAT pengunjung memilih tab kategori baru, MAKA SISTEM HARUS menganimasikan kartu produk yang relevan untuk bergeser mengisi posisi baru dalam kurun waktu 250–300 ms tanpa kedipan layar (*flicker*).
- **REQ-CAT-02** — SAAT pengunjung mengetik kata kunci pencarian, MAKA SISTEM HARUS menunggu 250 ms setelah ketukan terakhir sebelum menyaring data dan memunculkan transisi pembaruan kartu.
- **REQ-CAT-03** — SAAT produk yang dicari tidak ditemukan (panjang data = 0), MAKA SISTEM HARUS menampilkan komponen status kosong dengan animasi *fade-slide* lembut dan menyembunyikan grid produk.
- **REQ-CAT-04** — SAAT kartu produk keluar dari tampilan karena filter, MAKA SISTEM HARUS menonaktifkan interaksi klik/pointer (`pointer-events: none`) pada kartu tersebut selama fase keluar.
- **REQ-CAT-05** — SAAT sistem mendeteksi `prefers-reduced-motion: reduce`, MAKA SISTEM HARUS menonaktifkan seluruh perpindahan posisi (kelas `v-move`) dan transisi masuk/keluar secara instan.

---

## 5. Desain Visual & Aturan CSS

[ Semua ] [ Tanaman ] [ Pot ]   ← Tab Kategori (Pilih "Pot")
───────────────────────────────────────────────────────────
[ Item Tanaman ]  → (Fade out & Scale down 0.88, leave-active: absolute)
[ Item Pot A   ]  → (Bergeser meluncur mulus mengisi slot kiri)
[ Item Pot B   ]  → (Bergeser meluncur mulus mengisi slot kanan)


| Elemen Transisi | Status Awal / State Masuk | Status Keluar / Berpindah | Durasi & Kurva |
|---|---|---|---|
| **Grid Items (`enter`)** | Opasitas 0, `scale(0.92)`, `translateY(10px)` | Opasitas 1, `scale(1)`, `translateY(0)` | 250 ms `cubic-bezier(0.16, 1, 0.3, 1)` |
| **Grid Items (`leave`)** | Opasitas 1, `scale(1)` | Opasitas 0, `scale(0.88)`, `position: absolute` | 200 ms `ease-out` |
| **Grid Move (`v-move`)** | Posisi koordinat lama ($X_1, Y_1$) | Meluncur ke koordinat target ($X_2, Y_2$) | 300 ms `cubic-bezier(0.16, 1, 0.3, 1)` |
| **Empty State** | Opasitas 0, `translateY(8px)` | Opasitas 1, `translateY(0)` | 200 ms `ease-out` |
| **Input Search Focus** | Batas `--color-border-subtle` | Cincin fokus `2px solid var(--color-primary)`, offset 2 px | 150 ms `ease` |

### Aturan CSS Wajib

```css
/* 1. Pembungkus Grid Wajib Punya Position Relative */
.catalog-grid-wrapper {
  position: relative;
}

/* 2. Kartu Masuk dan Keluar */
.catalog-grid-enter-active,
.catalog-grid-leave-active {
  transition: opacity 250ms cubic-bezier(0.16, 1, 0.3, 1),
              transform 250ms cubic-bezier(0.16, 1, 0.3, 1);
}

.catalog-grid-enter-from {
  opacity: 0;
  transform: scale(0.92) translateY(10px);
}

.catalog-grid-leave-to {
  opacity: 0;
  transform: scale(0.88);
}

/* 3. FLIP Reordering: Menggerakkan kartu yang tersisa ke posisi baru */
.catalog-grid-move {
  transition: transform 300ms cubic-bezier(0.16, 1, 0.3, 1);
}

/* 4. Mencegah Layout Loncat Saat Item Dihapus dari Aliran Dokumen */
.catalog-grid-leave-active {
  position: absolute;
  pointer-events: none;
  z-index: 0;
}

/* 5. Transisi Status Kosong (Empty State) */
.fade-slide-enter-active,
.fade-slide-leave-active {
  transition: opacity 200ms ease, transform 200ms ease;
}

.fade-slide-enter-from,
.fade-slide-leave-to {
  opacity: 0;
  transform: translateY(8px);
}

/* 6. Aksesibilitas: Hormati Pengaturan Mode Hemat Gerak */
@media (prefers-reduced-motion: reduce) {
  .catalog-grid-enter-active,
  .catalog-grid-leave-active,
  .catalog-grid-move,
  .fade-slide-enter-active,
  .fade-slide-leave-active {
    transition: none !important;
    transform: none !important;
    animation: none !important;
  }
}