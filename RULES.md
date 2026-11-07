# PROJECT RULES & ARCHITECTURE GUIDE: KAIDEN (Top Up Game Online dengan RBAC)

Kamu adalah Senior Full-Stack Laravel Engineer, Security Architect, dan Product Designer. Seluruh kode yang kamu hasilkan wajib mematuhi spesifikasi PRD KAIDEN, prinsip keamanan RBAC tingkat lanjut, serta standar desain di bawah ini agar antarmuka terlihat profesional (bukan tampilan template AI generik).

---

## 1. IDENTITAS BRAND & ATURAN VISUAL (ANTI-"AI LOOK")

### A. Palet Warna Resmi KAIDEN (Wajib Dipatuhi)
Gunakan konfigurasi warna kustom ini di Tailwind CSS (`tailwind.config.js` / `@theme`):
- **Aksen Utama & Tombol Aksi (Kaiden Crimson):** `#6D0808` (Hover: `#540606`, Ring/Focus: `#6D0808/30`, Badge halus: `bg-[#6D0808]/10 text-[#6D0808]`).
- **Teks Utama, Header & Elemen Gelap (Kaiden Charcoal):** `#212121` (Teks sekunder: `text-[#212121]/70`, Teks redup: `text-[#212121]/50`).
- **Latar Halaman & Kartu (Kaiden Off-White):** `#F9F8F6` untuk background utama, `#FFFFFF` untuk permukaan kartu aktif dengan garis tepi tipis `border-[#212121]/10`.
- **Warna Status Fungsional:**
  - Sukses (`SUKSES` / `DIBAYAR`): Emerald (`text-emerald-700 bg-emerald-50 border-emerald-200`)
  - Proses/Menunggu (`MENUNGGU_BAYAR` / `DIPROSES`): Amber (`text-amber-700 bg-amber-50 border-amber-200`)
  - Gagal/Batal (`GAGAL` / `DIBATALKAN` / `KEDALUWARSA`): Red (`text-red-700 bg-red-50 border-red-200`)

### B. Larangan Keras Desain AI (Anti-AI Cliché)
1. **Dilarang memakai warna di luar palet:** Jangan pernah gunakan `bg-blue-500`, `bg-indigo-600`, atau *gradient* ungu-biru khas AI.
2. **Dilarang menaruh emoji di komponen UI:** Jangan gunakan emoji (🔥, 🚀, 💎, ✨, 🛡️, 🎮) pada tombol, navbar, heading, atau alert. Gunakan SVG dari **Lucide Icons** atau **Heroicons** (`w-4 h-4` atau `w-5 h-5`, `stroke-width="1.75"`).
3. **Dilarang memakai bayangan berlebihan & sudut membulat ekstrem:** Hindari `shadow-2xl` dan `rounded-3xl`. Gunakan estetika tegas ala *Shadcn/Linear* dengan `rounded-lg` atau `rounded-xl`, `shadow-xs`, dan `border border-[#212121]/10`.
4. **Tipografi & Format Angka:**
   - Gunakan font `'Plus Jakarta Sans'` atau `'Inter'` dengan `tracking-tight` pada heading.
   - Semua nominal harga (`Rp`), `ref_id`, `order_id`, User ID Game, dan alamat IP wajib menggunakan class `font-mono tabular-nums`.
   - Seluruh antarmuka wajib menggunakan **Bahasa Indonesia** yang lugas dan formal-komersial.

### C. Tata Letak (Layout) Halaman Utama
1. **Halaman Produk / Pemesanan Pelanggan:**
   - Gunakan layout 2 kolom asimetris di desktop (`grid-cols-1 lg:grid-cols-12 gap-6`).
   - Kolom Kiri (`lg:col-span-4`): Informasi game, deskripsi singkat, dan panduan pengisian ID.
   - Kolom Kanan (`lg:col-span-8`): Step-by-step berurutan:
     - **Langkah 1:** Input `user_id_game` (dan `zone_id` jika `games.butuh_zone == true`) + pengecekan nickname otomatis/tombol cek.
     - **Langkah 2:** Grid kartu nominal produk (`grid-cols-2 sm:grid-cols-3 gap-3`). Kartu yang dipilih menggunakan `ring-2 ring-[#6D0808] bg-[#6D0808]/5`.
     - **Langkah 3:** Ringkasan pesanan, konfirmasi nickname, dan tombol Checkout Midtrans (`bg-[#6D0808] text-[#F9F8F6]`).
2. **Dashboard Admin & Superadmin:**
   - Sidebar gelap (`bg-[#212121] text-[#F9F8F6]`) dengan indikator menu aktif berwarna merah `#6D0808`.
   - Area konten utama bersih (`bg-[#F9F8F6]`), tabel padat informasi dengan filter, pencarian, dan badge status yang jelas.

---

## 2. ATURAN LINGKUP FITUR (SESUAI PRD KAIDEN)

1. **Stack:** Laravel, MySQL, Eloquent ORM, Laravel Http Client.
2. **DI LUAR LINGKUP (Jangan Pernah Dibuat):**
   - TIDAK ADA fitur saldo/dompet (e-wallet) untuk pelanggan.
   - TIDAK ADA payment gateway selain Midtrans.
   - TIDAK ADA login SSO (Google/OAuth) untuk pelanggan.
   - TIDAK ADA fitur upload bukti transfer manual atau kirim item manual.
   - TIDAK ADA tabel atau modul iklan (`ads`).

---

## 3. SKEMA BASIS DATA INTI (WAJIB KONSISTEN)

Jangan mencampur pendekatan kolom string role dengan relasi. Gunakan struktur 10 tabel inti berikut secara konsisten:
1. `users`: `id`, `name`, `email`, `password` (hashed), `status`, `timestamps`. (Cegah *mass assignment* pada role!).
2. `roles` & `permissions`: Master daftar peran (`pelanggan`, `admin`, `superadmin`) dan 15 izin.
3. `role_permission` & `user_role`: Tabel pivot relasi RBAC.
4. `games`: `id`, `name`, `slug`, `checker_code` (contoh: `MOBILE_LEGENDS`, `FREEFIRE`, `VALORANT`, `CALL_OF_DUTY`, `AOV`), `needs_zone` (boolean), `status`.
5. `products`: `id`, `game_id`, `digiflazz_sku`, `name`, `cost_price` (harga modal), `sell_price` (harga jual), `is_active` (status lokal & menghormati status Digiflazz).
6. `orders`: `id` (PK auto-increment/UUID, JANGAN gunakan `player_id` sebagai PK), `order_code`, `user_id`, `product_id`, `user_id_game`, `zone_id`, `confirmed_nickname`, `locked_price` (snapshot harga saat pesanan dibuat), `status` (`MENUNGGU_BAYAR`, `DIBAYAR`, `DIPROSES`, `SUKSES`, `GAGAL`, `KEDALUWARSA`, `DIBATALKAN`).
7. `payments`: Terpisah dari `orders`. Berisi `id`, `order_id`, `midtrans_order_id`, `payment_method`, `status`, `notification_payload` (JSON), `paid_at`.
8. `fulfillments`: `id`, `order_id`, `digiflazz_ref_id` (unik), `status` (`pending`, `sukses`, `gagal`), `response_summary`.
9. `settings`: Konfigurasi integrasi (nilai sensitif/API Key wajib dienkripsi dengan `Crypt::encryptString` jika disimpan di DB, atau ambil dari `.env`), serta *feature flag* `ign_checker_enabled`.
10. `audit_logs`: `id`, `actor_id` (FK ke `users`), `action`, `target_type`, `target_id`, `old_values` (JSON), `new_values` (JSON), `ip_address`, `user_agent`, `created_at`.

---

## 4. KEAMANAN RBAC & AUDIT LOG (FOKUS UTAMA SKRIPSI)

### A. Matriks 3 Peran & 15 Permission
Gunakan *permission checking* di sisi server (Middleware + Policy/Gate) berdasarkan matriks ini:
- **Pelanggan:** `produk.lihat`, `pesanan.buat`, `transaksi.lihat_milik_sendiri`.
- **Admin:** `produk.lihat`, `transaksi.lihat`, `produk.ubah`, `stok.ubah`, `harga.ubah`, `produk.sinkron`, `pelanggan.lihat`, `laporan.lihat`, `laporan.ekspor`, `saldo_digiflazz.lihat`.
- **Superadmin:** Memiliki seluruh permission Admin **DITAMBAH** `pengguna.kelola`, `role.kelola`, `integrasi.atur`, `ign.konfigurasi`, `audit.lihat`. (Catatan: Admin & Superadmin TIDAK memiliki `pesanan.buat`).

### B. Penegakan Otorisasi & Anti-IDOR
1. Menyembunyikan menu di Blade (`@can`) BUKAN otorisasi. Setiap route dan aksi controller wajib memvalidasi permission di server.
2. **Ownership (Anti-IDOR):** Pada endpoint detail transaksi pelanggan, wajib gunakan Policy untuk memastikan `$order->user_id === $user->id`.
3. **Penanganan Error Aman:** Penolakan akses harus mengembalikan HTTP `403` atau `404` bersih tanpa membocorkan *stack trace* atau struktur internal.
4. **Pembuatan Akun Internal:** Akun `admin` dan `superadmin` hanya bisa dibuat lewat dashboard oleh `superadmin` (`pengguna.kelola`), tidak boleh dari form registrasi publik.

### C. Aturan Tabel `audit_logs` (Append-Only)
1. Model `AuditLog` dilarang memiliki fitur `update()` atau `delete()` dari level aplikasi.
2. Wajib mencatat log otomatis beserta `old_values`, `new_values`, dan `ip_address` pada saat:
   - Login Admin/Superadmin
   - Perubahan harga jual (`harga.ubah`) & perubahan stok/status produk (`stok.ubah`)
   - Sinkronisasi produk Digiflazz (`produk.sinkron`)
   - Perubahan role/permission & manajemen user (`role.kelola`, `pengguna.kelola`)
   - Perubahan konfigurasi API / *feature flag* IGN (`integrasi.atur`, `ign.konfigurasi`)
   - Ekspor laporan CSV (`laporan.ekspor`)

---

## 5. ATURAN INTEGRASI EKSTERNAL (IGN, MIDTRANS, DIGIFLAZZ)

### A. Validasi ID Game (`GameCheckService` - GoPay Games Provider)
1. Pengecekan *nickname* hanya dipanggil dari sisi server menggunakan `Illuminate\Support\Facades\Http` dengan `timeout(12)`.
2. Wajib pasang **Cache** (misal 24 jam per kombinasi `game_code + user_id + zone_id`) dan **Rate Limiter** pada route pengecekan agar endpoint tidak disalahgunakan sebagai *open proxy*.
3. **Feature Flag & Fallback (Sangat Penting):**
   - Cek status *feature flag* `ign_checker_enabled`.
   - Jika checker berhasil: tampilkan *nickname*, minta pelanggan mengonfirmasi, dan simpan ke `orders.confirmed_nickname`.
   - Jika checker gagal/timeout/diblokir atau game tidak didukung: **JANGAN blokir transaksi secara mati**. Tampilkan peringatan jelas dan wajibkan pelanggan mencentang/mengonfirmasi ulang ID secara manual sebelum lanjut ke Midtrans.

### B. Pembayaran Midtrans
1. Kunci harga produk ke kolom `orders.locked_price` saat pesanan dibuat (`MENUNGGU_BAYAR`).
2. Webhook/Callback Midtrans wajib memverifikasi `signature_key` (`SHA512(order_id + status_code + gross_amount + ServerKey)`).
3. **Idempotency:** Gunakan database transaction / atomic lock. Jika pesanan sudah berstatus `DIBAYAR`, `DIPROSES`, atau `SUKSES`, abaikan callback ganda agar tidak memicu pembelian berulang ke Digiflazz.

### C. Pemenuhan Pesanan Digiflazz
1. Hanya dipanggil setelah status pembayaran sah `DIBAYAR` (`settlement` / `capture`).
2. Gunakan `ref_id` unik untuk setiap transaksi dan simpan di tabel `fulfillments`.
3. Jika respons Digiflazz `GAGAL`, tandai pesanan untuk masuk ke antrean tindak lanjut Admin (refund manual via Midtrans & dicatat di `audit_logs`).
4. Tampilkan saldo deposit Digiflazz di dashboard Admin (`saldo_digiflazz.lihat`) beserta *warning badge* jika saldo berada di bawah ambang batas aman.
