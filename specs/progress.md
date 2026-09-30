# Progress — MiniShop

> Catatan serah-terima antar sesi. **Dibaca agen di awal setiap sesi sebagai pengganti menjelajah codebase.**
> Diperbarui agen di akhir setiap sub-tugas. **Tulis ulang bagian yang berubah, jangan menambah riwayat panjang.** Jaga di bawah 80 baris.

## Status

- Fase aktif: Fase 0 (Setup & Fondasi)
- Terakhir selesai: 0.1 Lingkungan
- **Tugas berikutnya:** 0.2 Repository (lihat `task.md`)
- Terakhir diperbarui: 2026-09-30

## Cara menjalankan (isi saat sudah berfungsi)

| Bagian | Perintah | Keterangan |
|---|---|---|
| Backend | `cd backend` lalu `php artisan serve` | http://localhost:8000 |
| Frontend | `cd frontend` lalu `npm run dev` | http://localhost:5173 |
| Tes backend | `cd backend` lalu `php artisan test` | memakai database `minishop_test` |
| Reset data | `php artisan migrate:fresh --seed` | minta izin dulu |

## Peta kode (isi seiring file dibuat; satu baris per file penting)

<!-- contoh:
- backend/app/Services/CheckoutService.php — transaksi checkout + kunci stok (REQ-CO-06..08)
- frontend/src/stores/cart.js — state keranjang + persist localStorage (REQ-CART-*)
-->

## Keputusan & penyimpangan dari spec

<!-- Catat hanya bila berbeda dari spec atau menambah keputusan baru. Format:
- [tanggal] Keputusan singkat — alasan. Spec terkait sudah diperbarui? ya/tidak
-->

## Masalah terbuka

<!-- Bug, hal yang belum jelas, atau pertanyaan untuk pemilik. Hapus bila sudah selesai. -->

## Catatan lingkungan

- Windows PowerShell / CMD; path proyek berspasi (`E:\project bray\MiniShop`), selalu dikutip.
- Versi: PHP 8.4.23, Composer 2.10.1, Node.js v26.5.0, PostgreSQL 18.4
- DB User: `minishop` (Password: `minishop_secret`) | DB: `minishop`, `minishop_test`
