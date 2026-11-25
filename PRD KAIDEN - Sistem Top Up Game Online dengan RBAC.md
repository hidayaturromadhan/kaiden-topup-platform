# PRD KAIDEN: Sistem Top Up Game Online dengan Keamanan RBAC

**Versi 1.0 (Produksi)** | **Tanggal Pembaruan:** 25 November 2026 | **Penulis:** M. Rifki Rahmani  
**Stack Teknologi:** Laravel 12 (PHP 8.2+), MySQL / MariaDB, Tailwind CSS v4, Vite, Digiflazz Live API, Midtrans Snap, Chart.js, Notiflix & Notyf

Dokumen ini merupakan pembaruan lengkap *Product Requirements Document* (PRD) yang mencerminkan kondisi aktual sistem **KAIDEN** yang telah diimplementasikan, diuji secara otomatis, dan disiapkan untuk lingkungan produksi (*cPanel shared hosting* / VPS). Seluruh poin yang sebelumnya berstatus **\[PERLU KEPUTUSAN\]** pada versi draf `0.1` telah difinalisasi dan diimplementasikan.

---

## 1. Ringkasan Produk

**KAIDEN** adalah platform *top up* game online dan voucher digital resmi yang terintegrasi penuh secara otomatis. Daftar game dan nominal produk disinkronkan langsung dari **Digiflazz Live API**, pembayaran pelanggan diproses multi-kanal melalui **Midtrans Snap**, verifikasi akun game dilakukan melalui **IGN Checker (*GameCheckService*)**, dan seluruh operasional bisnis dikelola lewat dashboard admin yang terproteksi ketat oleh sistem **Role-Based Access Control (RBAC)**.

Pembeda utama sistem ini adalah penerapan keamanan berbasis peran (*role*), izin granular (*permission*), pemeriksaan kepemilikan transaksi (*anti-IDOR*), proteksi anti-rugi harga modal, pemulihan transaksi gagal (*manual retry*), serta catatan jejak audit (*audit log*) yang bersifat *append-only* (tidak dapat diubah maupun dihapus).

- **Brand:** KAIDEN
- **Palet Warna Resmi:** Merah Crimson `#6D0808`, Hitam Charcoal `#212121`, Putih Off-White `#F9F8F6` (dengan dukungan penuh **Dark Mode**).

---

## 2. Finalisasi Keputusan Lingkup (Pembaruan dari Draf v0.1)

Seluruh pertanyaan terbuka pada draf awal PRD telah diputuskan dan diterapkan dalam sistem saat ini:

1. **Validasi ID & Nickname Game (IGN) Dipakai:** Fitur pengecekan *nickname* diaktifkan melalui `GameCheckService` menggunakan *GoPay Games Engine* sebagai penyedia utama dan pustaka `aditamagf/check-ign` (`ApiGames`) sebagai cadangan (*fallback*).
2. **Mekanisme Fallback IGN Tanpa Blocking:** Jika layanan pihak ketiga mengalami *timeout*, gangguan jaringan, batasan cakupan gateway (seperti akun PUBG Mobile 11-digit), atau *feature flag* dimatikan, sistem **tidak memblokir transaksi**. Pelanggan diberi peringatan transparan dan opsi **konfirmasi ulang ID secara manual**.
3. **Arsitektur 3 Peran & Izin Granular:** Sistem menggunakan 3 peran (`pelanggan`, `admin`, `superadmin`) dengan **18 izin granular** (*permission*) berbasis database yang dapat diatur secara dinamis oleh *Superadmin*. Prinsip *Separation of Duties* diterapkan: akun staf internal dilarang membuat pesanan langsung.
4. **Penanganan Kegagalan Supplier (Cut-Off / Gangguan Seller):** Jika pembayaran Midtrans sukses (`DIBAYAR`) namun pemenuhan Digiflazz gagal akibat gangguan *seller* atau *cut-off*, admin memiliki fitur **Proses Ulang Manual (*Retry Fulfillment*)** atau **Tandai Refund Manual** yang seluruhnya terekam dalam `audit_logs`.
5. **Fitur Tambahan yang Masuk Lingkup v1.0:** Manajemen **Banner & Promo Beranda** (`slider` & `side`), **Ulasan & Rating Pelanggan** (termasuk moderasi pop-up testimoni *live* dengan sensor privasi *nickname*), halaman **Katalog Lengkap** (`/katalog`), halaman **Panduan ID Interaktif & FAQ** (`/panduan`), serta tautan **Customer Care WhatsApp** dinamis.

---

## 3. Tujuan dan Indikator Keberhasilan

Seluruh tujuan perancangan sistem telah tervalidasi melalui pengujian otomatis PHPUnit (*58 test cases, 328 assertions, 100% lulus*):

| Tujuan | Indikator Keberhasilan Aktual |
| --- | --- |
| Transaksi top up otomatis dari bayar sampai item terkirim | Alur `MENUNGGU_BAYAR`, `DIBAYAR`, `DIPROSES`, `SUKSES`, `GAGAL`, `KEDALUWARSA`, `DIBATALKAN`, serta *retry fulfillment* teruji 100% |
| Hak akses sesuai peran (RBAC) | 45 pengecekan matriks RBAC (3 peran × 15 kelompok izin) serta 6 skenario serangan akses tidak sah (IDOR, *forced browsing*, eskalasi, *mass assignment*) lulus 100% |
| Aktivitas penting tertelusuri | 7 skenario audit log (mutasi harga, stok, peran, *masking* kunci API, login internal, ekspor CSV, dan penolakan ubah/hapus *append-only*) lulus 100% |
| Integrasi pembayaran & pemenuhan andal | Verifikasi *signature* SHA-512 Midtrans, idempotensi *callback* ganda, pencocokan `ref_id` Digiflazz, dan peringatan saldo rendah tervalidasi |
| Kesalahan ID game ditekan | Pelanggan melihat *nickname* asli sebelum membayar (dengan *cache* 24 jam, *rate limit* 8 req/menit, sanitasi input, dan *fallback* manual) |

---

## 4. Pengguna dan Peran

| Peran | Deskripsi Tugas & Wewenang |
| --- | --- |
| **Pelanggan** (`pelanggan`) | Registrasi/login publik, melihat katalog & panduan ID, cek *nickname*, melakukan pemesanan top up & pembayaran Midtrans, melihat riwayat & detail transaksi miliknya sendiri, serta mengirim ulasan/rating setelah pesanan selesai |
| **Admin** (`admin`) | Operasional harian: memantau ringkasan analitik & saldo Digiflazz, sinkronisasi & pengelolaan produk/harga/stok/gambar game, pengelolaan banner & promo, pengelolaan seluruh transaksi (termasuk *retry* & *refund manual*), melihat data pelanggan, moderasi feedback/ulasan, dan ekspor laporan CSV |
| **Superadmin** (`superadmin`) | Pemegang kendali keamanan tertinggi: memiliki seluruh izin operasional Admin **ditambah** manajemen akun staf internal (`pengguna.kelola`), pengaturan matriks izin peran (`role.kelola`), konfigurasi API & *feature flag* IGN/WhatsApp (`integrasi.atur`, `ign.konfigurasi`), dan peninjauan *Audit Log* (`audit.lihat`) |

> **Catatan Keamanan:** Sesuai prinsip *Separation of Duties*, peran `admin` dan `superadmin` tidak memiliki izin `pesanan.buat` dan diblokir di sisi server (`403 Forbidden`) apabila mencoba melakukan *checkout*.

---

## 5. Lingkup Sistem (Aktual v1.0)

### Masuk Lingkup (Sudah Diimplementasikan)

- **Area Publik & Pelanggan:**
  - Beranda dinamis: Banner *slider* & *side banner* promo, *carousel* & *grid* game aktif dengan filter kategori AJAX tanpa *reload*, metode pembayaran, serta pop-up testimoni *live* dengan penyamaran *nickname* (contoh: `gamerabah` $\rightarrow$ `ga******bah`).
  - Halaman Katalog (`/katalog`): Tab tampilan berdasarkan Game maupun Daftar Nominal Produk, pencarian, filter kategori/publisher, dan pengurutan harga/nama.
  - Halaman Panduan Pengisian ID (`/panduan`): Pencarian langsung (*live search*), tombol pilih game responsif (*showcase tab* per game dengan mekanisme *hide/show*), contoh format salin 1-klik, dan FAQ minimalis, dengan penyaringan ketat terhadap produk non-game (pulsa/PPOB/PLN/gas/TV).
  - Halaman Pemesanan Game (`/game/{slug}`): Tata letak 2 kolom asimetris, input User ID & Zone ID otomatis, pengecekan IGN *real-time*, pemilihan nominal dengan informasi harga normal/diskon, serta validasi kesiapan modal Digiflazz sebelum *checkout*.
  - Pembayaran Midtrans Snap, pelacakan status invoice (`/order/{order_code}`) dengan sinkronisasi status langsung ke Midtrans API, riwayat pesanan (`/my-orders`), dan form ulasan bintang 1–5.
  - Dukungan Tema Gelap/Terang (*Dark Mode / Light Mode*), indikator waktu *real-time* WIB di navbar, dan tombol Customer Care WhatsApp (`wa.me`).
- **Area Admin Operasional:**
  - Dashboard ringkasan: Metrik omset, modal, laba kotor, *average order value*, tingkat keberhasilan, grafik tren 7/14/30 hari (Chart.js), performa per game, serta pemantauan saldo deposit Digiflazz dan peringatan saldo di bawah ambang batas.
  - Manajemen Katalog & Harga: Sinkronisasi Digiflazz Live API (penanganan *cooldown* `RC 83` dengan *cache* JSON lokal), auto-register game baru, pemfilteran kategori non-game, proteksi **Anti-Rugi Otomatis** (mencegah harga jual < harga modal), pengaturan harga promo/coret (`original_price`), toggle status aktif, dan unggah poster/banner game.
  - Manajemen Banner & Promo (`/admin/banners`): CRUD banner *slider* dan *side banner* dengan pilihan tema gradien dan urutan tampil.
  - Manajemen Transaksi (`/admin/orders`): Filter status/game/pencarian, detail lengkap pembayaran & riwayat *fulfillment*, tombol **Retry Pemenuhan Digiflazz** untuk pesanan gagal/*cut-off*, dan tombol **Tandai Refund Manual**.
  - Manajemen Feedback & Ulasan (`/admin/reviews`): Statistik rating, filter ulasan, toggle tampil/sembunyi di pop-up beranda, dan fitur balas ulasan oleh admin.
  - Data Pelanggan (`/admin/customers`) & Laporan Penjualan (`/admin/reports`) dengan ekspor file CSV (UTF-8 BOM).
- **Area Keamanan Superadmin:**
  - Manajemen Pengguna Internal (`/superadmin/users`): Pembuatan akun staf, pembaruan peran, dan toggle status aktif/nonaktif pengguna.
  - Manajemen Role & Matriks Izin (`/superadmin/roles`): Pengaturan izin dinamis via form maupun *AJAX toggle* instan, dilengkapi pengaman *master lockout* khusus Superadmin pada rute `role.kelola`.
  - Konfigurasi Integrasi (`/superadmin/settings`): Pengaturan Midtrans, Digiflazz, ambang batas saldo rendah, *feature flag* IGN Checker, dan nomor WhatsApp CS dengan enkripsi AES-256 (`Crypt::encryptString`) untuk kredensial rahasia.
  - Viewer Audit Log (`/superadmin/audit-logs`): Penelusuran riwayat aktivitas lengkap (*append-only*).
- **Penjadwalan Otomatis (`routes/console.php`):**
  - `kaiden:check-pending-orders` (tiap 10 menit): Mengubah pesanan `MENUNGGU_BAYAR` > 24 jam menjadi `KEDALUWARSA`.
  - `kaiden:sync-products` (harian pukul 04:00): Sinkronisasi katalog dan harga modal Digiflazz terjadwal.

### Di Luar Lingkup

- Saldo, dompet, atau deposit akun pelanggan di dalam website.
- Payment gateway selain Midtrans.
- Login SSO (Google/OAuth) dan 2FA pelanggan.
- Unggah bukti transfer manual maupun pengiriman item game secara manual tanpa supplier.
- Modul periklanan (*ads*).
- Pengiriman notifikasi otomatis (WhatsApp Gateway / SMTP *blast*) di luar tautan manual `wa.me` Customer Care.

---

## 6. Kebutuhan Fungsional (Terimplementasi)

Prioritas: **P0** wajib, **P1** penting, **P2** opsional.

### 6.1 Autentikasi dan Akun

| ID | Kebutuhan | Prioritas | Status |
| --- | --- | --- | --- |
| FR-AUTH-1 | Pelanggan dapat registrasi dan login dengan email dan kata sandi (min. 8 karakter, di-hash, dukungan *remember me*) | P0 | Terimplementasi |
| FR-AUTH-2 | Pembatasan percobaan login (*rate limit* 5 kali/menit per IP+email), verifikasi status akun `aktif`, serta regenerasi sesi | P0 | Terimplementasi |
| FR-AUTH-3 | Akun `admin` dan `superadmin` hanya dapat dibuat oleh Superadmin lewat dashboard, bukan registrasi publik | P0 | Terimplementasi |
| FR-AUTH-4 | Kolom/relasi peran tidak boleh diisi dari input registrasi publik (registrasi publik otomatis menetapkan peran `pelanggan`) | P0 | Terimplementasi |
| FR-AUTH-5 | Pencatatan otomatis audit log untuk setiap login dan logout akun internal (`admin` & `superadmin`) | P0 | Terimplementasi |

### 6.2 Katalog dan Produk (Digiflazz)

| ID | Kebutuhan | Prioritas | Status |
| --- | --- | --- | --- |
| FR-PRD-1 | Sinkronisasi daftar produk & harga modal dari Digiflazz (`/price-list`) secara manual oleh admin dan terjadwal harian (`kaiden:sync-products`), termasuk penanganan `RC 83` via *cache* lokal | P0 | Terimplementasi |
| FR-PRD-2 | Admin mengatur harga jual (`sell_price`) dan harga normal/coret (`original_price`), disertai proteksi **anti-rugi** (menolak harga jual di bawah modal serta auto-adjust saat modal supplier naik melebihi harga jual) | P0 | Terimplementasi |
| FR-PRD-3 | Admin mengatur ketersediaan produk (`is_active`), dan status *buyer/seller* dari Digiflazz ikut disinkronkan | P0 | Terimplementasi |
| FR-PRD-4 | Pelanggan melihat daftar game dan nominal aktif di Beranda dan halaman Katalog (`/katalog`), dengan pemfilteran ketat produk non-game (pulsa, data, PLN, gas, TV, *e-money*) | P0 | Terimplementasi |
| FR-PRD-5 | Harga yang dibayar pelanggan dikunci ke `orders.locked_price` pada saat pesanan dibuat | P0 | Terimplementasi |
| FR-PRD-6 | Admin dapat memperbarui poster *thumbnail*, *banner header*, publisher, dan kategori game (`produk.ubah`) | P1 | Terimplementasi |
| FR-PRD-7 | Validasi kesiapan saldo deposit Digiflazz sebelum *checkout*: produk dengan `cost_price > saldo` dicegah diproses dengan pesan aman tanpa membocorkan data modal | P0 | Terimplementasi |

### 6.3 Pesanan, Panduan ID, dan Validasi ID Game (IGN)

| ID | Kebutuhan | Prioritas | Status |
| --- | --- | --- | --- |
| FR-ORD-1 | Pelanggan memilih game, mengisi User ID (dan Zone/Server ID bila `needs_zone = true`), dan memilih nominal pada tata letak 2 kolom asimetris | P0 | Terimplementasi |
| FR-ORD-2 | Halaman Panduan Pengisian ID (`/panduan`) menyajikan tata cara menemukan ID per game dengan antarmuka tab interaktif (*hide/show*), pencarian langsung, salin format 1-klik, dan FAQ | P1 | Terimplementasi |
| FR-IGN-1 | Sistem mengecek *nickname* di sisi server (`GameCheckService`) via GoPay Games Engine + fallback `ApiGames` dengan *timeout* 12 detik | P0 | Terimplementasi |
| FR-IGN-2 | Pemetaan dan normalisasi kode game *checker* (misal: `MOBILE_LEGENDS`, `FREEFIRE`, `VALORANT`, `CALL_OF_DUTY`, `AOV`, `HOK`, `PUBG_ID`, `FC_MOBILE`, `MAGIC_CHESS_GO_GO`, `BLOOD_STRIKE`) tersimpan di `games.checker_code` | P0 | Terimplementasi |
| FR-IGN-3 | Hasil pengecekan di-cache selama 24 jam per `game + user_id + zone_id`; endpoint `/api/check-ign` dibatasi *rate limit* (8 req/menit/IP) serta dilengkapi sanitasi karakter berbahaya (anti-SQLi/XSS/path traversal) | P0 | Terimplementasi |
| FR-IGN-4 | *GameCheckService* otomatis memisahkan gabungan ID+Zone atau ID+Nickname yang ditempel pengguna, serta melakukan *fallback* Moonton MLBB untuk Magic Chess | P1 | Terimplementasi |
| FR-IGN-5 | Pelanggan mengonfirmasi *nickname* yang tampil sebelum *checkout*; jika *checker* gagal/*timeout*/tidak didukung (termasuk PUBG 11-digit), sistem menampilkan peringatan dan menyediakan **konfirmasi manual** tanpa memblokir transaksi | P0 | Terimplementasi |
| FR-IGN-6 | Fitur IGN Checker dapat diaktifkan/dinonaktifkan lewat *feature flag* `ign_checker_enabled` oleh Superadmin | P1 | Terimplementasi |
| FR-IGN-7 | *Nickname* yang dikonfirmasi disimpan pada kolom `orders.confirmed_nickname` | P1 | Terimplementasi |

### 6.4 Pembayaran (Midtrans)

| ID | Kebutuhan | Prioritas | Status |
| --- | --- | --- | --- |
| FR-PAY-1 | Pesanan dibuat berstatus `MENUNGGU_BAYAR`, transaksi Midtrans Snap dibuat dengan `midtrans_order_id` unik, lalu pelanggan membayar lewat Snap pop-up/redirect | P0 | Terimplementasi |
| FR-PAY-2 | Endpoint webhook `/midtrans/notification` memverifikasi `signature_key` (SHA-512) sebelum memproses payload | P0 | Terimplementasi |
| FR-PAY-3 | Pemrosesan notifikasi bersifat **idempoten** menggunakan transaksi database dan `lockForUpdate()`: notifikasi ganda tidak memicu pemenuhan Digiflazz dua kali | P0 | Terimplementasi |
| FR-PAY-4 | Status `settlement`, `capture` (`accept`), `pending`, `expire`, `cancel`, dan `deny` ditangani untuk memperbarui status pesanan (`DIBAYAR`, `KEDALUWARSA`, `DIBATALKAN`) | P0 | Terimplementasi |
| FR-PAY-5 | Data pembayaran disimpan terpisah di tabel `payments` (termasuk `snap_token`, `snap_redirect_url`, `notification_payload`, `paid_at`) | P0 | Terimplementasi |
| FR-PAY-6 | Halaman detail pesanan (`/order/{order_code}`) melakukan sinkronisasi status langsung ke Midtrans API jika status masih `MENUNGGU_BAYAR` | P1 | Terimplementasi |

### 6.5 Pemenuhan Pesanan (Digiflazz)

| ID | Kebutuhan | Prioritas | Status |
| --- | --- | --- | --- |
| FR-FUL-1 | Setelah status pembayaran `DIBAYAR`, sistem mengirim transaksi ke Digiflazz (`/transaction`) dengan `digiflazz_ref_id` unik per upaya pemenuhan | P0 | Terimplementasi |
| FR-FUL-2 | Status Digiflazz (`sukses`, `pending`, `gagal`) diterima dari respons langsung maupun webhook `/digiflazz/webhook` yang dicocokkan melalui `digiflazz_ref_id` | P0 | Terimplementasi |
| FR-FUL-3 | Jika pemenuhan gagal (*cut-off* / gangguan *seller*), pesanan bertanda `GAGAL` dan admin dapat melakukan **Proses Ulang Manual (*Retry Fulfillment*)** atau menandai **Refund Manual** (`markRefund` $\rightarrow$ `DIBATALKAN`), keduanya dicatat ke `audit_logs` | P0 | Terimplementasi |
| FR-FUL-4 | Admin dapat melihat saldo deposit Digiflazz di dashboard (`saldo_digiflazz.lihat`), disertai peringatan visual apabila saldo berada di bawah ambang batas (`digiflazz_low_balance_threshold`) | P0 | Terimplementasi |

### 6.6 Dashboard Admin, Banner, Ulasan, dan Laporan

| ID | Kebutuhan | Prioritas | Status |
| --- | --- | --- | --- |
| FR-ADM-1 | Dashboard analitik menampilkan ringkasan omset, modal, laba, grafik tren 7/14/30 hari (Chart.js), performa game teratas, dan pesanan terbaru | P1 | Terimplementasi |
| FR-ADM-2 | Kelola harga jual, harga promo, status aktif produk, sinkronisasi Digiflazz, dan aset gambar game | P0 | Terimplementasi |
| FR-ADM-3 | Kelola banner & promo beranda (`slider` & `side`) sesuai permission `banner.kelola` | P1 | Terimplementasi |
| FR-ADM-4 | Lihat, cari, filter daftar & detail seluruh transaksi, termasuk eksekusi *retry* Digiflazz dan *refund manual* | P0 | Terimplementasi |
| FR-ADM-5 | Lihat dan cari data pelanggan (`/admin/customers`) | P0 | Terimplementasi |
| FR-ADM-6 | Pelanggan dapat mengirim ulasan & rating bintang 1–5 setelah pesanan selesai; Admin dapat mengelola visibilitas pop-up testimoni dan membalas ulasan (`/admin/reviews`) | P1 | Terimplementasi |
| FR-ADM-7 | Laporan transaksi berdasarkan rentang tanggal, game, dan status dengan ekspor file CSV ber-BOM UTF-8 yang tercatat di `audit_logs` | P1 | Terimplementasi |
| FR-ADM-8 | Superadmin mengelola pengguna internal, peran & matriks izin, konfigurasi API terenkripsi, *feature flag* IGN, nomor WhatsApp CS, serta melihat *Audit Log* | P0 | Terimplementasi |

### 6.7 RBAC dan Audit Log

| ID | Kebutuhan | Prioritas | Status |
| --- | --- | --- | --- |
| FR-RBAC-1 | Hak akses berbasis peran dan **18 permission granular** | P0 | Terimplementasi |
| FR-RBAC-2 | Penegakan di sisi server via middleware `permission` (`CheckPermission`) dan `OrderPolicy` | P0 | Terimplementasi |
| FR-RBAC-3 | *Ownership* (Anti-IDOR): pelanggan hanya dapat melihat detail pesanan maupun mengirim ulasan untuk pesanannya sendiri | P0 | Terimplementasi |
| FR-RBAC-4 | Penolakan akses mengembalikan halaman error kustom `403` / `404` (atau JSON 403/401) tanpa membocorkan *stack trace* internal | P0 | Terimplementasi |
| FR-LOG-1 | Audit log merekam: login/logout internal, pembuatan/perubahan/status user, ubah peran/izin, ubah harga, ubah stok, ubah aset game, sinkronisasi Digiflazz, settlement pembayaran, pemenuhan/webhook/*retry*/*refund* Digiflazz, kelola banner, moderasi/balas ulasan, perubahan setting API (dengan penyamaran kunci), ekspor laporan CSV, dan auto-kedaluwarsa | P0 | Terimplementasi |
| FR-LOG-2 | Setiap entri mencatat `actor_id`, `action`, `target_type`, `target_id`, `old_values`, `new_values`, `ip_address`, `user_agent`, dan `created_at` | P0 | Terimplementasi |
| FR-LOG-3 | Model `AuditLog` bersifat *append-only* (event `updating` dan `deleting` melempar `RuntimeException`); hanya Superadmin yang dapat melihat melalui `/superadmin/audit-logs` | P0 | Terimplementasi |

---

## 7. Matriks Role dan Permission (Aktual Terimplementasi)

Sesuai `DatabaseSeeder.php`, terdapat **18 izin granular** yang dipetakan ke 3 peran:

| No | Permission | Pelanggan | Admin | Superadmin |
| :---: | --- | :---: | :---: | :---: |
| 1 | `produk.lihat` | Ya | Ya | Ya |
| 2 | `pesanan.buat` | Ya | Tidak | Tidak |
| 3 | `transaksi.lihat_milik_sendiri` | Ya | Tidak | Tidak |
| 4 | `transaksi.lihat` | Tidak | Ya | Ya |
| 5 | `produk.ubah` | Tidak | Ya | Ya |
| 6 | `stok.ubah` | Tidak | Ya | Ya |
| 7 | `harga.ubah` | Tidak | Ya | Ya |
| 8 | `produk.sinkron` | Tidak | Ya | Ya |
| 9 | `pelanggan.lihat` | Tidak | Ya | Ya |
| 10 | `laporan.lihat` | Tidak | Ya | Ya |
| 11 | `laporan.ekspor` | Tidak | Ya | Ya |
| 12 | `saldo_digiflazz.lihat` | Tidak | Ya | Ya |
| 13 | `banner.kelola` | Tidak | Ya | Ya |
| 14 | `pengguna.kelola` | Tidak | Tidak | Ya |
| 15 | `role.kelola` | Tidak | Tidak | Ya |
| 16 | `integrasi.atur` | Tidak | Tidak | Ya |
| 17 | `ign.konfigurasi` | Tidak | Tidak | Ya |
| 18 | `audit.lihat` | Tidak | Tidak | Ya |

*(Dalam pengujian matriks akademik `RbacMatrixTableTest`, beberapa izin sejenis seperti `produk.ubah / stok.ubah` dan `laporan.lihat / laporan.ekspor` diuji berpasangan menjadi 15 baris × 3 peran = 45 pengecekan valid).*

---

## 8. Alur Transaksi dan Status

1. Pelanggan membuka halaman game (`/game/{slug}`), mengisi User ID (dan Zone ID bila wajib), lalu sistem memverifikasi *nickname* melalui `GameCheckService` (atau menyediakan konfirmasi manual jika *checker* tidak tersedia/dimatikan).
2. Pelanggan memilih nominal produk (sistem memvalidasi kecukupan saldo modal Digiflazz terlebih dahulu sebelum *checkout*).
3. Pelanggan melakukan *checkout* (`POST /order/checkout`). Pesanan dibuat dengan kode `KDN-YYYYMMDD-XXXXXX`, status **MENUNGGU\_BAYAR**, harga jual dikunci di `locked_price`, dan transaksi Snap dibuat di tabel `payments`.
4. Pelanggan membayar lewat Midtrans Snap. Status dapat diperbarui melalui webhook `/midtrans/notification` (terverifikasi SHA-512 & idempoten) ataupun pengecekan langsung API saat pelanggan membuka `/order/{order_code}`.
5. Jika pembayaran berhasil (`settlement`/`capture`), status menjadi **DIBAYAR**, tercatat di `audit_logs`, dan `DigiflazzService::fulfill()` langsung dipanggil sehingga status pesanan menjadi **DIPROSES**.
6. Jika pembayaran `expire` (atau melebihi 24 jam lewat penjadwalan `kaiden:check-pending-orders`), status menjadi **KEDALUWARSA**. Jika `cancel`/`deny`, status menjadi **DIBATALKAN**.
7. Digiflazz mengembalikan hasil sinkron atau melalui webhook `/digiflazz/webhook`:
   - **SUKSES:** Serial Number (`sn`) dan ringkasan tersimpan di `fulfillments`, status pesanan menjadi **SUKSES**. Pelanggan dapat mengirim **ulasan & rating** pada halaman invoice.
   - **GAGAL:** Pesanan masuk daftar tindak lanjut Admin. Admin dapat menekan tombol **Proses Ulang (Retry)** setelah *seller* normal (mengembalikan status sementara ke `DIBAYAR` $\rightarrow$ `DIPROSES` dan membuat record `fulfillments` baru dengan `ref_id` unik), atau memproses **Refund Manual** yang mengubah status menjadi **DIBATALKAN**.

---

## 9. Integrasi Eksternal

| Layanan | Fungsi | Mekanisme Keamanan & Keandalan |
| --- | --- | --- |
| **Digiflazz Live API** | Cek saldo (`/cek-saldo`), sinkronisasi katalog (`/price-list`), transaksi (`/transaction`), dan webhook (`/digiflazz/webhook`) | Kredensial dari `settings` terenkripsi atau `.env`, validasi signature MD5, pencocokan `digiflazz_ref_id` unik, proteksi anti-rugi harga modal, cache pricelist untuk `RC 83`, cache saldo 30 detik, mode `production` vs `development` |
| **Midtrans Snap** | Pembuatan token pembayaran, notifikasi status, pengecekan status langsung (`checkStatus`) | Verifikasi signature `SHA512`, *atomic lock* (`lockForUpdate`) & idempotensi untuk mencegah eksekusi ganda, server key terenkripsi / hanya di sisi server |
| **IGN Checker** (`GameCheckService`) | Verifikasi *nickname* akun game (*GoPay Games Engine* + fallback `aditamagf/check-ign`) | Dipanggil hanya dari sisi server, `timeout(12)`, *cache* 24 jam, *rate limit* 8 req/menit/IP, sanitasi regex anti-SQLi/XSS, *feature flag* `ign_checker_enabled`, *fallback* konfirmasi manual |
| **WhatsApp (`wa.me`)** | Kanal Customer Care interaktif untuk bantuan pembayaran & komplain transaksi | Nomor tujuan dikelola secara terpusat oleh Superadmin lewat `settings` (`whatsapp_number`) |

---

## 10. Kebutuhan Non-Fungsional

- **Keamanan:** Password *hashing* (Bcrypt/Argon), proteksi CSRF bawaan Laravel (pengecualian eksplisit hanya pada route webhook eksternal), *escaping* XSS di Blade, *query binding* Eloquent, *rate limiting* login dan cek IGN, enkripsi AES-256 pada pengaturan sensitif di database, serta *immutability* pada `audit_logs`.
- **Privasi Log & Data:** Kunci API disamarkan pada antarmuka/log; *nickname* pelanggan pada pop-up testimoni beranda disamarkan otomatis; pesan kesalahan internal & saldo modal supplier tidak dibocorkan ke pelanggan.
- **Ketahanan (*Resilience*):** Kegagalan endpoint IGN tidak mematikan *checkout*; error Digiflazz tidak membuat status sukses palsu pada mode `production` melainkan dicatat rapi untuk *retry* admin.
- **Kinerja:** Penggunaan *cache* terarah untuk kategori, katalog beranda/halaman katalog, daftar game panduan, metrik dashboard, saldo Digiflazz, dan hasil cek IGN; dilengkapi *database indexes* khusus performa (`2026_11_01_204942_add_performance_indexes_to_tables.php`).
- **Portabilitas Deployment:** Dukungan otomatis struktur `public_html` cPanel shared hosting di `bootstrap/app.php`.
- **Antarmuka:** Responsif di *mobile*, *tablet*, dan *desktop*, berbahasa Indonesia, mematuhi palet brand KAIDEN (`#6D0808`, `#212121`, `#F9F8F6`), serta mendukung *Dark Mode*.

---

## 11. Model Data (14 Tabel Aplikasi)

Berdasarkan file migrasi aktual di `database/migrations/`, berikut adalah tabel inti aplikasi (di luar tabel bawaan sistem Laravel `cache`, `jobs`, `sessions`, `password_reset_tokens`):

| Tabel | Isi Utama & Kolom Penting |
| --- | --- |
| `users` | `id`, `name`, `email`, `password` (hash), `status` (`aktif`/`nonaktif`), `email_verified_at`, `remember_token` |
| `roles` | `id`, `name` (`pelanggan`, `admin`, `superadmin`), `display_name`, `description` |
| `permissions` | `id`, `name` (18 izin granular), `display_name`, `description` |
| `role_permission` | Pivot `role_id`, `permission_id` |
| `user_role` | Pivot `user_id`, `role_id` |
| `games` | `id`, `name`, `slug`, `checker_code`, `needs_zone`, `status`, `publisher`, `category`, `thumbnail`, `banner_image`, `description`, `guide_text` |
| `products` | `id`, `game_id`, `digiflazz_sku`, `name`, `cost_price`, `original_price`, `sell_price`, `is_active`, `category` |
| `orders` | `id`, `order_code`, `user_id`, `product_id`, `user_id_game`, `zone_id`, `confirmed_nickname`, `locked_price`, `status` (7 status), `notes` |
| `payments` | `id`, `order_id`, `midtrans_order_id`, `payment_method`, `status`, `snap_token`, `snap_redirect_url`, `notification_payload` (JSON), `paid_at` |
| `fulfillments` | `id`, `order_id`, `digiflazz_ref_id`, `status` (`pending`/`sukses`/`gagal`), `response_summary` |
| `banners` | `id`, `title`, `subtitle`, `badge_text`, `discount_tag`, `discount_value`, `discount_subtext`, `button_text`, `button_url`, `image`, `banner_type` (`slider`/`side`), `gradient_theme`, `sort_order`, `is_active` |
| `reviews` | `id`, `order_id` (unique), `user_id`, `game_id`, `rating` (1–5), `comment`, `is_anonymous`, `is_visible`, `admin_reply` |
| `settings` | `id`, `key`, `value`, `is_encrypted`, `description` |
| `audit_logs` | `id`, `actor_id`, `action`, `target_type`, `target_id`, `old_values` (JSON), `new_values` (JSON), `ip_address`, `user_agent`, `created_at` *(tanpa `updated_at`)* |

---

## 12. Panduan Antarmuka

| Elemen | Palet Light Mode | Palet Dark Mode |
| --- | --- | --- |
| Aksen utama & tombol aksi | Merah Crimson `#6D0808` | Merah Crimson `#6D0808` / Aksen Rose `#FB7185` |
| Teks utama, header, sidebar admin | Hitam Charcoal `#212121` | Putih Terang `#F4F4F5` / `#18181B` |
| Latar halaman & kartu | Off-White `#F9F8F6` & Kartu `#FFFFFF` | Gelap Netral `#09090B` & Kartu `#18181B` |

- **Halaman Pelanggan:** Beranda (`/`), Katalog Produk & Game (`/katalog`), Panduan ID & FAQ (`/panduan`), Halaman Pemesanan (`/game/{slug}`), Detail & Pembayaran Pesanan (`/order/{order_code}`), Riwayat Transaksi (`/my-orders`), Masuk/Daftar (`/login`, `/register`).
- **Halaman Admin:** Dashboard Analitik (`/admin/dashboard`), Katalog & Harga (`/admin/products`), Banner & Promo (`/admin/banners`), Semua Transaksi & Detail (`/admin/orders`), Data Pelanggan (`/admin/customers`), Feedback & Ulasan (`/admin/reviews`), Laporan & Ekspor CSV (`/admin/reports`).
- **Halaman Superadmin:** Pengguna Internal (`/superadmin/users`), Peran & Izin (`/superadmin/roles`), Pengaturan Integrasi (`/superadmin/settings`), Audit Log (`/superadmin/audit-logs`).

---

## 13. Cakupan & Hasil Pengujian Otomatis

Seluruh skenario pengujian telah diimplementasikan dalam *Automated Test Suite* (`tests/Feature/`) dan berhasil dijalankan menggunakan `php artisan test` dengan capaian **58 tests passed, 328 assertions (100% lulus)**:

1. **Matriks RBAC (`RbacMatrixTableTest`, `RbacSecurityTest`):** Validasi 45 pengecekan kombinasi peran-izin, larangan order oleh admin/superadmin, proteksi registrasi publik, dan autentikasi *remember me*.
2. **Akses Tidak Sah & OWASP (`UnauthorizedAccessTableTest`):** Validasi blokir IDOR (403), *forced browsing* ke rute admin/superadmin, eskalasi peran, *mass assignment*, *parameter tampering* (404), dan pengalihan pengguna belum login (302).
3. **Integrasi Midtrans (`MidtransIntegrationTableTest`, `IntegrationFlowTest`):** Simulasi *settlement*, *pending*, *expire*, *cancel*, penolakan *signature* palsu, dan idempotensi *callback* ganda.
4. **Integrasi Digiflazz & Retry (`DigiflazzIntegrationTableTest`, `OrderRetryTest`):** Simulasi sukses, *pending*, gagal/*cut-off*, pencocokan `ref_id`, peringatan saldo rendah, *manual retry fulfillment*, dan pencegahan *retry* pada order `SUKSES`.
5. **Validasi IGN (`IgnCheckerIntegrationTableTest`):** Pengujian ID valid, ID salah, *timeout/error* (*fallback* manual), *rate limiting* (HTTP 429), dan *feature flag*.
6. **Audit Log (`AuditLogVerificationTableTest`):** Verifikasi pencatatan mutasi harga, stok, role, penyamaran kunci API, login internal, ekspor CSV, dan penolakan ubah/hapus (*append-only*).
7. **Fitur Bisnis & Katalog (`ProductPricingTest`, `BannerAdminTest`, `ReviewFeedbackTest`, `CatalogTest`):** Validasi proteksi anti-rugi harga jual, diskon promo, CRUD banner & hak akses `banner.kelola`, pengiriman & moderasi ulasan serta sensor *nickname*, serta penyaringan game/non-game pada katalog & panduan ID.

---

## 14. Risiko dan Mitigasi

| Risiko | Mitigasi yang Diimplementasikan |
| --- | --- |
| Endpoint IGN tidak resmi berubah atau diblokir | *Feature flag* `ign_checker_enabled`, *fallback* `ApiGames`, konfirmasi manual tanpa blokir *checkout*, dicatat terbuka sebagai batasan sistem |
| Supplier Digiflazz mengalami *cut-off* / gangguan *seller* setelah pembayaran masuk | Validasi saldo sebelum *checkout*, penandaan status `GAGAL` beserta catatan alasan, fitur **Manual Retry Fulfillment**, dan **Refund Manual** tercatat di `audit_logs` |
| Kenaikan harga modal Digiflazz mendadak | Penyesuaian harga jual otomatis (*Proteksi Anti-Rugi*) saat sinkronisasi dan validasi penolakan harga rugi saat edit manual admin |
| Limitasi *cooldown* `/price-list` Digiflazz (`RC 83`) | Penyimpanan salinan mentah JSON (`digiflazz_pricelist_raw.json`) sebagai *cache* cadangan |
| Kebocoran kunci API | Disimpan di `.env` atau tabel `settings` terenkripsi AES-256 (`Crypt`), disamarkan di UI, hanya Superadmin yang dapat mengatur |
| *Callback* Midtrans dipalsukan atau terkirim ganda | Verifikasi `signature_key` SHA-512, database *transaction*, dan `lockForUpdate()` untuk idempotensi |
| Penyalahgunaan hak akses internal | RBAC 3 peran & 18 izin granular, *middleware* & *policy* server-side, pemisahan akun Admin dan Superadmin, serta `audit_logs` *append-only* |

---

## 15. Tahapan Pengembangan (Waterfall)

1. **Analisis Kebutuhan:** Finalisasi PRD, matriks RBAC, dan skema integrasi *(Selesai)*.
2. **Perancangan:** Desain ERD 14 tabel aplikasi, alur status pesanan, arsitektur layanan eksternal, dan desain antarmuka *(Selesai)*.
3. **Implementasi:** Fondasi RBAC & audit log, integrasi Digiflazz Live API & proteksi anti-rugi, Midtrans Snap, GameCheckService, manajemen banner & ulasan, panduan ID, serta dashboard admin/superadmin *(Selesai)*.
4. **Pengujian:** Verifikasi menyeluruh lewat 58 skenario uji otomatis (328 assertions, 100% lulus) *(Selesai)*.
5. **Deployment & Dokumentasi:** Pembaruan PRD, README, panduan cPanel `public_html`, dan kesiapan produksi *(Berjalan / Siap Produksi)*.

---

## 16. Sumber Rujukan

- Basis Kode Aktual Repositori KAIDEN (`app/`, `database/migrations/`, `database/seeders/`, `routes/`, `tests/Feature/`).
- `RULES.md` (Aturan Arsitektur, Palet Desain KAIDEN, Skema Database, dan Standar Keamanan RBAC).
- `README.md` (Dokumentasi Fitur, Lingkungan `.env`, dan Ringkasan Pengujian Otomatis).
- Dokumentasi Resmi API Midtrans Snap & API Digiflazz Prabayar.
