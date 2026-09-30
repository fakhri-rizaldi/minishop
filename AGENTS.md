# MiniShop — Aturan untuk Agen

Proyek case study Fullstack Engineer: katalog produk, keranjang, checkout, dan admin.
Pemilik proyek sedang **belajar sambil membangun**. Tugasmu ada dua: menulis kode yang benar, dan menjelaskan supaya pemilik paham kenapa kodenya begitu.

## 1. Struktur & sumber kebenaran

```
minishop/
├── specs/       # SUMBER KEBENARAN
│   ├── requirement.md   # APA yang harus dilakukan (SAAT ... MAKA SISTEM HARUS ...)
│   ├── design.md        # BAGAIMANA: arsitektur, data model, alur, kontrak API
│   ├── task.md          # URUTAN kerja (checklist per fase)
│   └── ui.md            # TAMPILAN: warna, font, layout, komponen
├── backend/     # Laravel 13 + PostgreSQL
└── frontend/    # Vue 3 (Vite)
```

Urutan otoritas bila ada konflik: `requirement.md` > `design.md` > `ui.md` > `task.md` > aturan di file ini soal gaya kode.
Aturan `ponytail` (di `.agents/rules/`) mengatur **seberapa banyak kode** yang ditulis; spec mengatur **apa yang harus ada**. Jangan memangkas sesuatu yang diminta spec (validasi, transaksi checkout, tes) atas nama "minimal".

## 2. Alur kerja Spec-Driven (wajib)

1. **Baca dulu:** `specs/task.md` untuk menentukan tugas berikutnya, lalu bagian terkait di `requirement.md` dan `design.md`. Untuk pekerjaan di `frontend/`, baca juga `specs/ui.md`.
2. **Satu tugas kecil per giliran.** Kerjakan satu sub-bagian task (mis. "1.1 Migration"), bukan satu fase penuh, kecuali pemilik meminta.
3. **Jangan menambah fitur di luar spec.** Bila menemukan celah atau ide, berhenti dan usulkan perubahan spec dulu; ubah spec setelah pemilik setuju, baru kodekan.
4. **Perubahan perilaku = perubahan spec** di giliran yang sama (requirement/design yang terdampak).
5. **Verifikasi sebelum mencentang.** Jalankan tes atau perintah yang membuktikan tugas berjalan. Baru ubah `- [ ]` menjadi `- [x]` di `task.md`. Bila tidak bisa memverifikasi, katakan terus terang dan jangan centang.
6. **Sebut ID requirement** (mis. `REQ-CO-05`) di penjelasan dan pesan commit agar terlacak.
7. Bila spec ambigu, ajukan **satu pertanyaan** yang paling menentukan, jangan menebak diam-diam.

## 3. Mode belajar (default aktif)

Setelah **setiap tugas** selesai dan terverifikasi, tulis penjelasan singkat dalam Bahasa Indonesia dengan format ini (target 150–250 kata, tidak lebih kecuali diminta):

```
### Selesai: <nama tugas> (REQ-xxx)

**Apa yang dibuat** — 1–2 kalimat + daftar file yang dibuat/diubah.

**Kenapa begini** — konsep di baliknya. Jelaskan alasan desain, bukan sekadar isi kode
(contoh: kenapa harga disimpan bigint, kenapa stok dikunci dengan FOR UPDATE).

**Kode kunci** — tunjukkan 5–15 baris yang paling penting dan jelaskan baris demi baris.

**Cara membuktikan** — perintah atau langkah yang bisa dijalankan pemilik sendiri, dan hasil yang diharapkan.

**Istilah baru** — 1 baris per istilah yang baru muncul (definisi singkat).

**Kesalahan umum** — 1–2 jebakan pemula terkait topik ini.

**Cek pemahaman** — 1 pertanyaan singkat untuk pemilik (jawaban tidak wajib; berikan kunci jawaban hanya bila diminta).
```

Aturan mode belajar:
- Penjelasan ditulis di **chat**, bukan di komentar kode. Komentar kode hanya untuk hal yang tidak jelas dari kodenya sendiri.
- Definisikan istilah teknis saat pertama kali muncul; anggap pemilik cerdas tetapi baru pada topik ini.
- Utamakan analogi konkret dan contoh dari proyek ini daripada teori umum.
- Bila ada dua cara wajar, tunjukkan cara yang dipilih, sebut alternatifnya satu kalimat, dan kenapa tidak dipilih.
- Jangan mengulang penjelasan yang sudah diberikan; rujuk ke tugas sebelumnya.
- **Berhenti setelah penjelasan dan tanya "Lanjut ke tugas berikutnya?"** Jangan melompat ke tugas berikutnya sebelum pemilik menjawab.
- Perintah **"mode ringkas"** dari pemilik: hanya tulis 2–3 kalimat hasil + cara verifikasi. Perintah **"mode belajar"** mengaktifkannya kembali.
- Perintah **"jelaskan lebih dalam"**: jabarkan konsep terakhir lebih detail dengan contoh tambahan.

### Titik belajar khusus (jelaskan lebih dalam dari biasa)
- Transaksi database, `lockForUpdate`, dan race condition (Fase 3)
- Validasi di server vs klien, FormRequest (Fase 3)
- Token Sanctum dan middleware (Fase 4)
- Reaktivitas Pinia dan persist `localStorage` (Fase 8)
- Sinkron state dengan query URL di Vue Router (Fase 7)

### Catatan `AI_USAGE.md`
Setelah tugas `CheckoutService` dan logika keranjang selesai, ingatkan pemilik untuk mencatat prompt yang dipakai dan koreksi manual yang dilakukan di `AI_USAGE.md`. Jangan menulis klaim di sana yang tidak benar-benar terjadi.

## 4. Lingkungan pemilik (Windows)

- Shell: Windows CMD/PowerShell. Jangan pakai sintaks bash (`mkdir -p`, `cp`, `rm -rf`, `export`). Gunakan `mkdir`, `copy`, `rmdir /s /q`, `set`.
- Path proyek mengandung spasi (`E:\project bray\MiniShop`). **Selalu beri tanda kutip** pada path.
- `php artisan`, `composer`, `npm`, `git` sama di semua platform.
- Beri perintah satu per satu dengan penjelasan singkat fungsinya bila perintah itu baru bagi pemilik.

## 5. Keselamatan

- Minta persetujuan sebelum menjalankan perintah yang menghapus data atau berkas: `migrate:fresh`, `db:wipe`, `rmdir`, `git reset --hard`, `git clean`, `npm audit fix --force`.
- Jangan menaruh rahasia (password, key) di berkas yang di-commit. `.env` tidak boleh masuk git; hanya `.env.example`.
- Kredensial admin seeder (`admin@minishop.test` / `password`) hanya untuk lokal dan harus disebut sebagai tidak aman untuk produksi.
- Jangan memasang paket baru di luar yang disebut `design.md` tanpa bertanya dan menjelaskan alasannya.
- Jangan menyentuh berkas di luar folder proyek.

## 6. Konvensi backend (Laravel 13, PostgreSQL)

- Bila ragu soal API Laravel 13 (versi baru, mungkin berbeda dari pengetahuanmu), cek `composer show laravel/framework` dan dokumentasi resmi sebelum menulis, jangan menebak.
- Validasi lewat **FormRequest**; respons JSON lewat **API Resource**; logika transaksi di **Service** (`CheckoutService`), bukan di controller. Controller tipis.
- Harga = integer Rupiah (`unsignedBigInteger`), tidak pernah float.
- Checkout: satu `DB::transaction`, kunci produk `lockForUpdate` diurutkan berdasarkan `id`, hitung total dari harga database, abaikan harga dari klien (lihat `design.md` §4.3).
- Cegah N+1: gunakan eager loading (`with`) pada endpoint list.
- Pencarian pakai `ILIKE` dengan karakter `%`, `_`, `\` di-escape.
- Route admin di bawah `auth:sanctum`. Semua error API berbentuk JSON sesuai `design.md` §3.3.
- Tes memakai PostgreSQL (`minishop_test`), bukan SQLite, karena perilaku `FOR UPDATE` dan check constraint harus sama.
- Format kode dengan Laravel Pint.

## 7. Konvensi frontend (Vue 3)

- Composition API dengan `<script setup>`; Pinia untuk `cart` dan `auth`; panggilan API hanya lewat folder `src/api/`.
- Tampilan mengikuti `specs/ui.md`: **semua warna dari CSS variable/token**, tidak ada hex lepas di komponen; font Fraunces (judul) dan Manrope (isi/angka).
- Dilarang: gradien, glassmorphism, bayangan berlapis, label kapital semua, animasi masuk di tiap bagian, font Inter/Roboto/Arial. Daftar lengkap di `ui.md` §10.
- **Skill `no-slop-ui`:** sebelum membuat atau mengubah UI di `frontend/`, baca `.agents/skills/no-slop-ui/SKILL.md` bila folder itu ada. Setelah UI selesai, tinjau hasilnya dengan `.agents/skills/no-slop-ui/examples/review-checklist.md`. Bila folder tidak ada, lewati dan andalkan `ui.md` §10.
- **Prioritas bila skill dan `ui.md` berbeda:** `ui.md` menang untuk palet warna, font, dan token (itu keputusan proyek). Skill dipakai untuk prinsip umum (hierarki, kepadatan informasi, pola yang harus dihindari) dan untuk tinjauan akhir. Bila ada benturan, sebutkan ke pemilik dan jelaskan mana yang diikuti; jangan diam-diam mengganti palet atau font.
- Setiap keadaan harus ada: memuat, kosong, error, nonaktif. Setiap input punya `<label>`. Fokus keyboard terlihat.
- Teks antarmuka Bahasa Indonesia, sentence case, aksi konsisten (lihat `ui.md` §7).
- Server adalah sumber kebenaran harga dan stok; store cart hanya untuk tampilan.
- Format harga lewat `Intl.NumberFormat("id-ID")` di `utils/format.js`, bukan string manual.
- Jangan pakai `localStorage` tanpa `try/catch` dan validasi bentuk data.

## 8. Git

- Satu commit per sub-tugas selesai. Format: `feat(scope): ringkasan singkat (REQ-XXX)`, mis. `feat(checkout): kurangi stok dalam transaksi (REQ-CO-06)`.
- Scope yang dipakai: `db`, `catalog`, `checkout`, `auth`, `admin`, `cart`, `ui`, `docs`, `test`.
- Jangan commit atau push tanpa diminta; sarankan pesan commit di akhir penjelasan tugas.

## 9. Definisi "selesai" untuk sebuah tugas

1. Perilaku sesuai requirement yang dirujuk.
2. Ada bukti verifikasi (tes hijau, atau langkah manual + hasil).
3. Tidak ada peringatan/galat baru di konsol atau log.
4. Checklist `task.md` diperbarui.
5. Penjelasan mode belajar sudah ditulis (kecuali "mode ringkas").

## 10. Sesi baru & hemat konteks

Kamu tidak punya ingatan antar sesi. Jangan mencoba "memahami seluruh codebase" di awal sesi; itu boros dan tidak perlu. Ikuti urutan ini:

**Awal setiap sesi (maksimal ini saja):**
1. Baca `AGENTS.md` (sudah dimuat otomatis) dan `specs/progress.md`.
2. Buka `specs/task.md` hanya pada **sub-bagian tugas berikutnya** yang tertera di `progress.md`, bukan seluruh file.
3. Baca hanya bagian `requirement.md` / `design.md` / `ui.md` yang dirujuk tugas itu (cari lewat ID `REQ-...` atau judul bagian).
4. Untuk orientasi cepat, boleh `git status` dan `git log --oneline -5`. Jangan menjalankan `git log -p` atau `git diff` besar.
5. Buka file kode **hanya** yang akan diubah, atau yang tercantum di "Peta kode" pada `progress.md`. Bila butuh mencari sesuatu, cari dengan kata kunci; jangan membuka seluruh folder.

**Larangan membaca (kecuali diminta pemilik):** `composer.lock`, `package-lock.json`, `vendor/`, `node_modules/`, `dist/`, `storage/logs/` (untuk log, baca beberapa baris terakhir saja), dan berkas biner/gambar.

**Batasi pencarian** ke `backend/app`, `backend/routes`, `backend/database`, `backend/tests`, `frontend/src`, dan `specs/`.

**Akhir setiap sub-tugas (wajib, sebelum menulis penjelasan mode belajar):**
1. Centang tugas di `task.md`.
2. Perbarui `specs/progress.md`: status, tugas berikutnya, peta kode (tambah file penting yang baru), keputusan/penyimpangan, masalah terbuka. **Tulis ulang bagian yang berubah; jangan menambah riwayat.**
3. Bila keputusan baru mengubah perilaku, perbarui spec yang terkait (lihat §2 poin 4).

**Bila konteks terasa penuh atau sesi akan berakhir** (mis. pemilik bilang limit hampir habis): berhenti di titik yang bersih, selesaikan langkah "Akhir setiap sub-tugas" di atas, dan tulis di `progress.md` apa yang setengah jadi beserta langkah pertama untuk melanjutkan.

Jangan mengulang penjelasan atau membaca ulang berkas yang baru saja kamu buat di sesi yang sama.
