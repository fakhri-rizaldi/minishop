# Requirement — MiniShop

> Sumber kebenaran untuk **APA** yang harus dilakukan sistem. Cara membuatnya ada di `design.md`, urutan pengerjaan ada di `task.md`, tampilan ada di `ui.md`.
> Sumber: Case Study Fullstack Engineer — "MiniShop: Product Catalog & Cart".

**Pola penulisan:** `SAAT [kejadian/kondisi], MAKA SISTEM HARUS [perilaku yang bisa diuji]`

**Tag:** `[Wajib]` dari case study · `[Bonus]` opsional di case study · `[Asumsi]` keputusan saya karena case study tidak menyebutkan (boleh dicoret)

## Aktor

| Aktor | Keterangan |
|---|---|
| Pengunjung | Siapa pun yang membuka toko. Tanpa akun. Bisa belanja dan checkout sebagai tamu. |
| Admin | Mengelola produk dan melihat order. Login dengan akun yang dibuat lewat seeder. |

## Di luar cakupan

Payment gateway, akun pelanggan, status order (dibayar/dikirim), upload gambar, CRUD kategori, multi-bahasa, idempotency key untuk checkout. Hal-hal ini dicatat di *Known limitations* pada README.

---

## 1. Katalog Produk (REQ-CAT)

- **REQ-CAT-01** — SAAT pengunjung membuka halaman katalog, MAKA SISTEM HARUS menampilkan daftar produk dari API berisi nama, harga (format Rupiah), deskripsi singkat, gambar, kategori, dan stok. `[Wajib]`
- **REQ-CAT-02** — SAAT jumlah produk melebihi satu halaman, MAKA SISTEM HARUS membagi hasil per halaman (default 12, maksimum 50 per permintaan) dan menyediakan navigasi halaman. `[Asumsi]`
- **REQ-CAT-03** — SAAT pengunjung mengetik kata kunci pada kolom pencarian, MAKA SISTEM HARUS menampilkan hanya produk yang namanya mengandung kata kunci tersebut tanpa membedakan huruf besar/kecil, dengan jeda 250 ms setelah ketikan terakhir sebelum memanggil API dan memperbarui data. `[Wajib]`
- **REQ-CAT-04** — SAAT pengunjung memilih sebuah kategori, MAKA SISTEM HARUS menampilkan hanya produk pada kategori tersebut. `[Wajib]`
- **REQ-CAT-05** — SAAT pencarian dan filter kategori aktif bersamaan, MAKA SISTEM HARUS menerapkan keduanya sekaligus (AND) dan menyimpan keduanya di query string URL agar hasil bisa dibagikan dan bertahan saat refresh. `[Asumsi]`
- **REQ-CAT-06** — SAAT pencarian atau filter tidak menghasilkan produk, MAKA SISTEM HARUS menampilkan pesan hasil kosong dengan transisi *fade-slide* lembut beserta tombol untuk mereset pencarian dan filter. `[Asumsi]`
- **REQ-CAT-07** — SAAT pengunjung memilih sebuah produk, MAKA SISTEM HARUS membuka halaman detail produk (`/products/:id`) berisi gambar, nama, harga, deskripsi, kategori, stok, dan tombol tambah ke keranjang. `[Wajib]`
- **REQ-CAT-08** — SAAT stok sebuah produk bernilai 0, MAKA SISTEM HARUS menandai produk "Stok habis" dan menonaktifkan tombol tambah ke keranjang pada list maupun detail. `[Asumsi]`
- **REQ-CAT-09** — SAAT stok sebuah produk 5 atau kurang (dan lebih dari 0), MAKA SISTEM HARUS menampilkan penanda "Stok menipis" beserta jumlah stok. `[Asumsi]`
- **REQ-CAT-10** — SAAT pengunjung membuka detail produk dengan id yang tidak ada atau sudah dihapus, MAKA SISTEM HARUS menampilkan halaman "Produk tidak ditemukan" dengan tautan kembali ke katalog. `[Asumsi]`
- **REQ-CAT-11** — SAAT data katalog sedang dimuat, MAKA SISTEM HARUS menampilkan placeholder loading; SAAT pemuatan gagal, MAKA SISTEM HARUS menampilkan pesan error beserta tombol coba lagi. `[Asumsi]`
- **REQ-CAT-12** — SAAT gambar produk gagal dimuat atau URL gambar kosong, MAKA SISTEM HARUS menampilkan gambar pengganti (placeholder) tanpa merusak tata letak. `[Asumsi]`
- **REQ-CAT-13** — SAAT pengunjung mengganti kategori atau hasil pencarian berkurang, MAKA SISTEM HARUS menganimasikan kartu produk bergeser (*reorder*) mengisi posisi baru secara mulus (teknik FLIP, durasi 250–300 ms) tanpa kedipan layar. `[Asumsi]`
- **REQ-CAT-14** — SAAT kartu produk keluar dari tampilan karena filter, MAKA SISTEM HARUS menonaktifkan interaksi klik/pointer (`pointer-events: none`) selama fase keluar berlangsung. `[Asumsi]`
- **REQ-CAT-15** — SAAT sistem mendeteksi preferensi `prefers-reduced-motion: reduce`, MAKA SISTEM HARUS mematikan seluruh animasi pergeseran dan transisi secara instan. `[Asumsi]`
- **REQ-CAT-16** — SAAT pengunjung pertama kali membuka halaman utama (`/`), MAKA SISTEM HARUS menyajikan seksi sambutan (*Hero Landing Section*) berisi judul sambutan ketenangan botani, deskripsi kurasi produk, animasi masuk teks (*staggered entrance animation*), serta tombol eksplorasi "Jelajahi Koleksi" yang melakukan *smooth scroll* ke seksi katalog. `[Asumsi]`

## 2. Keranjang (REQ-CART)

Keranjang disimpan di sisi klien (state management). Server baru dilibatkan saat checkout.

- **REQ-CART-01** — SAAT pengunjung menekan "Tambah ke keranjang" dari list atau detail produk, MAKA SISTEM HARUS menambahkan produk ke keranjang dan memunculkan/menganimasikan tombol lingkaran melayang (*floating cart button*) di pojok kanan bawah berlogo keranjang dengan badge lingkaran merah berisi total barang di keranjang, yang jika diklik akan mengarahkan pengunjung ke halaman keranjang (`/cart`). `[Wajib]`
- **REQ-CART-02** — SAAT produk yang ditambahkan sudah ada di keranjang, MAKA SISTEM HARUS menambah jumlah item yang ada, bukan membuat baris baru. `[Wajib]`
- **REQ-CART-03** — SAAT jumlah hasil penambahan akan melebihi stok produk, MAKA SISTEM HARUS menolak penambahan tersebut, mempertahankan jumlah sebelumnya, dan menampilkan pesan yang menyebut stok tersedia. `[Wajib]`
- **REQ-CART-04** — SAAT pengunjung mengubah jumlah item di keranjang (tombol +/− atau input angka), MAKA SISTEM HARUS memperbarui jumlah tersebut dan menghitung ulang subtotal item serta total keranjang saat itu juga. `[Wajib]`
- **REQ-CART-05** — SAAT jumlah yang dimasukkan melebihi stok, MAKA SISTEM HARUS membatasi jumlah pada stok maksimum dan menampilkan pesan. `[Wajib]`
- **REQ-CART-06** — SAAT jumlah yang dimasukkan kurang dari 1 atau bukan bilangan bulat, MAKA SISTEM HARUS mengembalikan jumlah ke 1; tombol − pada jumlah 1 harus nonaktif. Penghapusan hanya lewat tombol hapus. `[Asumsi]`
- **REQ-CART-07** — SAAT pengunjung menekan hapus pada sebuah item, MAKA SISTEM HARUS menghapus item itu dari keranjang dan menghitung ulang total. `[Wajib]`
- **REQ-CART-08** — SAAT isi keranjang berubah, MAKA SISTEM HARUS menghitung subtotal per item (harga × jumlah) dan total seluruh item secara otomatis, serta memperbarui jumlah item pada ikon keranjang di header. `[Wajib]`
- **REQ-CART-09** — SAAT keranjang kosong, MAKA SISTEM HARUS menampilkan keadaan kosong dengan tautan ke katalog dan menonaktifkan tombol checkout. `[Asumsi]`
- **REQ-CART-10** — SAAT halaman dimuat ulang (refresh), MAKA SISTEM HARUS memulihkan isi keranjang dari `localStorage`. `[Bonus]`
- **REQ-CART-11** — SAAT data keranjang di `localStorage` rusak, tidak sesuai format, atau berisi jumlah tidak valid, MAKA SISTEM HARUS mengabaikan data tersebut dan memulai keranjang kosong tanpa menampilkan error ke pengunjung. `[Asumsi]`

## 3. Checkout (REQ-CO)

- **REQ-CO-01** — SAAT pengunjung membuka halaman checkout dengan keranjang berisi item, MAKA SISTEM HARUS menampilkan formulir data pemesan (nama dan email) beserta ringkasan item dan total. `[Asumsi]`
- **REQ-CO-02** — SAAT keranjang kosong dan pengunjung membuka halaman checkout, MAKA SISTEM HARUS mengarahkan pengunjung ke halaman keranjang. `[Asumsi]`
- **REQ-CO-03** — SAAT pengunjung menekan "Buat pesanan", MAKA SISTEM HARUS mengirim daftar item (id produk dan jumlah) beserta data pemesan ke `POST /api/orders` dan menonaktifkan tombol selama permintaan berjalan agar tidak terkirim dua kali. `[Wajib]`
- **REQ-CO-04** — SAAT data yang dikirim tidak valid (nama kosong, email tidak valid, daftar item kosong, id produk tidak ada, jumlah bukan bilangan bulat ≥ 1, id produk ganda), MAKA SISTEM HARUS menolak permintaan dengan status 422 beserta pesan per kolom, dan tidak mengubah data apa pun. `[Wajib]`
- **REQ-CO-05** — SAAT jumlah salah satu item melebihi stok terkini di database (atau produk sudah dihapus), MAKA SISTEM HARUS menolak seluruh pesanan dengan status 409, tidak membuat Order, tidak mengurangi stok produk mana pun, dan menyertakan daftar produk bermasalah beserta stok yang tersedia. `[Wajib]`
- **REQ-CO-06** — SAAT validasi lolos, MAKA SISTEM HARUS dalam satu transaksi database: membuat Order dengan nomor unik, membuat baris item order yang menyimpan salinan nama dan harga produk saat itu, mengurangi stok tiap produk sebesar jumlah yang dipesan, dan menyimpan total. `[Wajib]`
- **REQ-CO-07** — SAAT total order dihitung, MAKA SISTEM HARUS memakai harga dari database, bukan harga yang dikirim klien. `[Wajib]`
- **REQ-CO-08** — SAAT dua checkout terjadi bersamaan untuk produk dengan stok terbatas, MAKA SISTEM HARUS menjamin stok tidak pernah bernilai negatif dan menolak checkout yang stoknya tidak lagi mencukupi. `[Wajib]`
- **REQ-CO-09** — SAAT sebagian langkah transaksi gagal (kesalahan tak terduga), MAKA SISTEM HARUS membatalkan seluruh perubahan (rollback) dan mengembalikan status 500 dengan pesan umum tanpa membocorkan detail internal. `[Wajib]`
- **REQ-CO-10** — SAAT checkout berhasil, MAKA SISTEM HARUS mengosongkan keranjang dan membawa pengunjung ke halaman "Order berhasil" (`/orders/:orderNumber/success`) yang menampilkan nomor order, data pemesan, daftar item (nama, harga, jumlah, subtotal), dan total. `[Wajib]`
- **REQ-CO-11** — SAAT checkout ditolak karena stok (409), MAKA SISTEM HARUS memperbarui keranjang (jumlah dibatasi ke stok tersedia, item dihapus jika stok 0) dan memberi tahu pengunjung item mana yang berubah tanpa mengosongkan keranjang. `[Asumsi]`
- **REQ-CO-12** — SAAT halaman "Order berhasil" dibuka dengan nomor order yang tidak ada, MAKA SISTEM HARUS menampilkan halaman "Order tidak ditemukan". `[Asumsi]`

## 4. Autentikasi Admin (REQ-AUTH)

- **REQ-AUTH-01** — SAAT admin mengirim email dan password yang benar ke endpoint login, MAKA SISTEM HARUS mengembalikan token akses dan data admin. `[Asumsi]`
- **REQ-AUTH-02** — SAAT email atau password salah, MAKA SISTEM HARUS menolak dengan pesan yang sama untuk kedua kasus (tidak menyebut mana yang salah). `[Asumsi]`
- **REQ-AUTH-03** — SAAT login gagal lebih dari 5 kali dalam satu menit dari alamat yang sama, MAKA SISTEM HARUS menolak percobaan berikutnya dengan status 429. `[Asumsi]`
- **REQ-AUTH-04** — SAAT permintaan ke `/api/admin/*` tidak membawa token yang valid, MAKA SISTEM HARUS menolak dengan status 401. `[Asumsi]`
- **REQ-AUTH-05** — SAAT pengunjung yang belum login membuka halaman `/admin/*` (selain login), MAKA SISTEM HARUS mengarahkannya ke `/admin/login`. `[Asumsi]`
- **REQ-AUTH-06** — SAAT admin menekan keluar, MAKA SISTEM HARUS mencabut token di server, menghapus sesi di klien, dan mengarahkan ke halaman login. `[Asumsi]`
- **REQ-AUTH-07** — SAAT API admin mengembalikan 401 ketika halaman sedang dipakai, MAKA SISTEM HARUS menghapus sesi klien dan mengarahkan ke login. `[Asumsi]`

## 5. Admin — Kelola Produk (REQ-ADM)

- **REQ-ADM-01** — SAAT admin membuka daftar produk, MAKA SISTEM HARUS menampilkan tabel produk (nama, kategori, harga, stok) dengan pembagian halaman, pencarian nama, dan filter kategori. `[Wajib]`
- **REQ-ADM-02** — SAAT admin mengisi formulir tambah produk dengan data valid dan menyimpan, MAKA SISTEM HARUS membuat produk baru dan menampilkannya pada daftar. `[Wajib]`
- **REQ-ADM-03** — SAAT data produk tidak valid, MAKA SISTEM HARUS menolak dengan status 422 dan pesan per kolom. Aturan: nama wajib (maks. 150 karakter); harga bilangan bulat ≥ 0 dalam Rupiah; stok bilangan bulat ≥ 0; deskripsi maks. 500 karakter; URL gambar opsional dan harus URL yang valid; kategori wajib dan harus ada. `[Wajib]`
- **REQ-ADM-04** — SAAT admin membuka formulir ubah produk, MAKA SISTEM HARUS mengisi formulir dengan data produk saat ini; SAAT disimpan dengan data valid, MAKA SISTEM HARUS memperbarui produk. `[Wajib]`
- **REQ-ADM-05** — SAAT admin menekan hapus pada sebuah produk, MAKA SISTEM HARUS meminta konfirmasi terlebih dahulu; SAAT dikonfirmasi, MAKA SISTEM HARUS menghapus produk secara *soft delete* sehingga hilang dari katalog publik namun order lama tetap utuh dengan salinan nama dan harganya. `[Wajib]`
- **REQ-ADM-06** — SAAT admin mengubah harga, stok, atau menghapus produk, MAKA SISTEM HARUS membuat katalog publik dan checkout berikutnya langsung memakai data terbaru (tanpa cache). `[Asumsi]`

## 6. Admin — Order (REQ-ORD)

- **REQ-ORD-01** — SAAT admin membuka daftar order, MAKA SISTEM HARUS menampilkan order terbaru lebih dulu dengan nomor order, nama pemesan, jumlah item, total, dan waktu, dengan pembagian halaman. `[Wajib]`
- **REQ-ORD-02** — SAAT admin membuka detail sebuah order, MAKA SISTEM HARUS menampilkan data pemesan, seluruh item (nama, harga, jumlah, subtotal sesuai saat order dibuat), dan total. `[Wajib]`
- **REQ-ORD-03** — SAAT admin membuka detail order yang tidak ada, MAKA SISTEM HARUS menampilkan halaman "Order tidak ditemukan". `[Asumsi]`

## 7. Data & Seed (REQ-DATA)

- **REQ-DATA-01** — SAAT perintah `php artisan migrate --seed` dijalankan pada database kosong, MAKA SISTEM HARUS membuat seluruh tabel, 4 kategori, 23 produk dummy (minimal satu produk berstok 0 dan beberapa berstok ≤ 5), dan satu akun admin. `[Wajib]`
- **REQ-DATA-02** — SAAT `php artisan migrate:fresh --seed` dijalankan berulang, MAKA SISTEM HARUS menghasilkan keadaan yang sama tanpa error. `[Asumsi]`
- **REQ-DATA-03** — SAAT harga disimpan, MAKA SISTEM HARUS menyimpannya sebagai bilangan bulat Rupiah (bukan desimal/float) agar tidak ada galat pembulatan. `[Asumsi]`
- **REQ-DATA-04** — SAAT ada upaya menulis stok atau harga bernilai negatif langsung ke database, MAKA SISTEM HARUS menolaknya lewat *check constraint* sebagai pengaman terakhir. `[Asumsi]`

## 8. Non-Fungsional (REQ-NFR)

- **REQ-NFR-01** — SAAT aplikasi dibuka pada lebar layar 360 px hingga 1440 px, MAKA SISTEM HARUS tampil dan berfungsi penuh tanpa scroll horizontal pada halaman. `[Wajib]`
- **REQ-NFR-02** — SAAT API menerima input dari klien mana pun, MAKA SISTEM HARUS memvalidasi di sisi server; validasi di frontend hanya untuk kenyamanan pengguna. `[Wajib]`
- **REQ-NFR-03** — SAAT API mengembalikan error, MAKA SISTEM HARUS memakai format JSON yang konsisten (lihat *Format Error* di `design.md`) dan status HTTP yang sesuai (401, 404, 409, 422, 429, 500). `[Asumsi]`
- **REQ-NFR-04** — SAAT frontend di origin berbeda memanggil API, MAKA SISTEM HARUS hanya mengizinkan origin frontend yang dikonfigurasi lewat environment (CORS). `[Asumsi]`
- **REQ-NFR-05** — SAAT endpoint list mengambil data yang berelasi (kategori, item order), MAKA SISTEM HARUS memakai eager loading sehingga jumlah query tidak bertambah seiring jumlah baris (tanpa N+1). `[Asumsi]`
- **REQ-NFR-06** — SAAT pengguna memakai keyboard atau pembaca layar, MAKA SISTEM HARUS menyediakan fokus yang terlihat, label pada semua kontrol form, dan kontras warna sesuai `ui.md`. `[Asumsi]`
- **REQ-NFR-07** — SAAT reviewer mengikuti README pada mesin bersih, MAKA SISTEM HARUS dapat dijalankan lokal dengan langkah yang ringkas (install, salin `.env`, migrate + seed, jalankan backend dan frontend). `[Wajib]`
- **REQ-NFR-08** — SAAT repository dipublikasikan, MAKA SISTEM HARUS tidak memuat rahasia (password, key); yang tersedia hanya `.env.example`. `[Asumsi]`
- **REQ-NFR-09** — SAAT proyek diserahkan, MAKA SISTEM HARUS menyertakan `README.md` (overview, tech stack + alasan, cara menjalankan, daftar endpoint, known limitations) dan `AI_USAGE.md` (tools AI + 1–2 contoh prompt krusial). `[Wajib]`
