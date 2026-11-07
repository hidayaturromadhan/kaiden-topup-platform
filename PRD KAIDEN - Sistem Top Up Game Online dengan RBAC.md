# PRD KAIDEN: Sistem Top Up Game Online dengan Keamanan RBAC

Versi 0.1 (draf) | 31 Oktober 2026 | Penulis: M. Rifki Rahmani | Stack: Laravel, MySQL, Digiflazz, Midtrans

Dokumen ini disusun dari Kontrol Proposal, README, dan Laravel Guide pengecekan IGN. Poin yang bertentangan dengan keputusan sebelumnya atau belum diputuskan ditandai **\[PERLU KEPUTUSAN\]**.

## 1. Ringkasan Produk

KAIDEN adalah website top up game online. Produk (voucher dan top up game) diambil dari Digiflazz lewat API, pembayaran pelanggan diproses oleh Midtrans, dan seluruh operasional dikelola lewat dashboard admin. Pembeda utama sistem ini adalah keamanan berbasis Role-Based Access Control (RBAC) yang mencakup role, permission, pemeriksaan kepemilikan data, dan audit log.

**Brand:** KAIDEN. Warna: merah #6D0808, hitam #212121, putih #F9F8F6.

## 2. Perubahan Lingkup yang Perlu Dikonfirmasi

Kontrol Proposal menetapkan **validasi User ID/nickname game tidak dipakai** (keputusan FIX no. 6, Batasan no. 6). Kini API pengecekan IGN sudah ditemukan (repositori check-ign dengan provider GoPay Games). PRD ini memasukkannya sebagai fitur, sehingga:

- **\[PERLU KEPUTUSAN\]** Keputusan FIX no. 6 harus diubah menjadi "validasi IGN dipakai".
- Sesuai aturan sinkronisasi di Kontrol Proposal, perubahan ini harus dirambatkan ke Latar Belakang, Permasalahan, Batasan (no. 6), Tujuan, Manfaat, dan risiko no. 7 (mitigasi "halaman konfirmasi ulang" tetap dipertahankan sebagai cadangan).
- Catatan risiko: endpoint GoPay yang dipakai bukan API resmi yang terdokumentasi untuk pihak ketiga. Library memanggilnya dengan header Origin dan Referer gopay.co.id. Endpoint dapat berubah, dibatasi, atau diblokir sewaktu-waktu, dan penggunaannya dapat melanggar ketentuan layanan penyedia. Karena itu fitur ini dirancang **tidak boleh menjadi satu-satunya penentu transaksi** (lihat FR-IGN-5) dan dibungkus feature flag. Untuk laporan skripsi, batasan ini sebaiknya ditulis terbuka.

## 3. Tujuan dan Indikator Keberhasilan

Karena KAIDEN baru dirintis dan belum punya data lama, indikator ditulis sebagai target pengujian, bukan klaim kondisi sebelumnya.

| Tujuan | Indikator |
| --- | --- |
| Transaksi top up otomatis dari bayar sampai item terkirim | Alur pembayaran sukses, pending, gagal, dan kedaluwarsa lolos uji |
| Hak akses sesuai peran | 100% skenario matriks uji RBAC menghasilkan hasil yang diharapkan (diizinkan/ditolak) |
| Aktivitas penting tertelusuri | Setiap aksi berisiko tercatat di audit log dengan pelaku, waktu, data sebelum/sesudah |
| Integrasi andal | Callback Midtrans ganda tidak memproses transaksi dua kali |
| Kesalahan ID game ditekan | Pelanggan melihat nickname sebelum membayar (bila checker tersedia) |

## 4. Pengguna dan Peran

| Peran | Deskripsi |
| --- | --- |
| Pelanggan | Membeli top up, melihat riwayat transaksi miliknya sendiri |
| Admin | Operasional harian: produk, harga, stok, pelanggan, transaksi, laporan, saldo Digiflazz |
| Superadmin | Aksi berisiko tinggi: kelola pengguna dan role, atur integrasi/kunci API, lihat audit log |

**\[PERLU KEPUTUSAN\]** (a) tetap 3 peran atau 2 peran dengan permission detail; (b) apakah admin dan superadmin dipegang satu orang dengan dua akun terpisah (disarankan, prinsip least privilege). PRD ini memakai 3 peran sesuai Bab 1, dan desainnya berbasis permission sehingga mudah disesuaikan.

## 5. Lingkup

**Masuk lingkup**

- Website top up game dengan registrasi/login pelanggan.
- Katalog produk dari Digiflazz, harga jual diatur admin.
- Input ID akun game, pengecekan nickname, pembayaran Midtrans, pemenuhan pesanan via Digiflazz.
- Dashboard admin: harga, stok/ketersediaan, pelanggan, transaksi, saldo deposit Digiflazz, laporan.
- RBAC: role, permission, ownership, audit log.

**Di luar lingkup**

- Saldo atau dompet pelanggan di website.
- Payment gateway selain Midtrans.
- SSO dan 2FA pelanggan (2FA superadmin opsional).
- Keamanan jaringan secara mendalam.
- Upload bukti bayar dan pengiriman item manual.
- Modul iklan.

**\[PERLU KEPUTUSAN\]** Notifikasi email/WhatsApp serta format dan periode laporan belum dibahas. PRD ini mengasumsikan: notifikasi di luar lingkup versi 1, laporan harian/bulanan dengan ekspor CSV.

## 6. Kebutuhan Fungsional

Prioritas: **P0** wajib, **P1** penting, **P2** opsional.

### 6.1 Autentikasi dan Akun

| ID | Kebutuhan | Prioritas |
| --- | --- | --- |
| FR-AUTH-1 | Pelanggan dapat registrasi dan login dengan email dan password (password di-hash) | P0 |
| FR-AUTH-2 | Pembatasan percobaan login (rate limit) dan pengelolaan sesi aman | P0 |
| FR-AUTH-3 | Akun admin dan superadmin dibuat hanya oleh superadmin, bukan lewat registrasi publik | P0 |
| FR-AUTH-4 | Kolom role tidak boleh diisi dari input pengguna (cegah mass assignment) | P0 |
| FR-AUTH-5 | Autentikasi tambahan untuk superadmin | P2 |

### 6.2 Katalog dan Produk (Digiflazz)

| ID | Kebutuhan | Prioritas |
| --- | --- | --- |
| FR-PRD-1 | Sinkronisasi daftar produk dan harga modal dari Digiflazz (manual oleh admin dan terjadwal) | P0 |
| FR-PRD-2 | Admin mengatur harga jual per produk (harga modal + margin) | P0 |
| FR-PRD-3 | Admin mengatur ketersediaan produk (aktif/nonaktif), dan status dari Digiflazz ikut dihormati | P0 |
| FR-PRD-4 | Pelanggan melihat daftar game dan nominal yang aktif | P0 |
| FR-PRD-5 | Harga yang dibayar pelanggan dikunci (snapshot) pada saat pesanan dibuat | P0 |

### 6.3 Pesanan dan Validasi ID Game (IGN)

| ID | Kebutuhan | Prioritas |
| --- | --- | --- |
| FR-ORD-1 | Pelanggan memilih game, mengisi User ID (dan Zone/Server bila diperlukan), memilih nominal | P0 |
| FR-IGN-1 | Setelah ID diisi, sistem mengecek nickname lewat service sisi server (GameCheckService, Laravel Http client) dan menampilkannya ke pelanggan | P0 |
| FR-IGN-2 | Pemetaan kode game checker (misalnya MOBILE\_LEGENDS, FREEFIRE, VALORANT, CALL\_OF\_DUTY, AOV) ke produk Digiflazz disimpan di tabel konfigurasi | P0 |
| FR-IGN-3 | Hasil pengecekan di-cache (misalnya 1 hari) per game+user+zone, dan endpoint cek dibatasi rate limit agar tidak menjadi proxy terbuka | P0 |
| FR-IGN-4 | Pelanggan wajib mengonfirmasi nickname yang tampil sebelum lanjut ke pembayaran | P0 |
| FR-IGN-5 | Jika checker gagal atau game tidak didukung, sistem menampilkan peringatan dan meminta konfirmasi ulang ID secara manual; transaksi tidak otomatis diblokir **\[PERLU KEPUTUSAN\]** | P0 |
| FR-IGN-6 | Fitur dapat dimatikan lewat feature flag oleh superadmin | P1 |
| FR-IGN-7 | Nickname yang dikonfirmasi disimpan pada pesanan sebagai bukti | P1 |

### 6.4 Pembayaran (Midtrans)

| ID | Kebutuhan | Prioritas |
| --- | --- | --- |
| FR-PAY-1 | Pesanan dibuat berstatus menunggu bayar, lalu pelanggan diarahkan ke pembayaran Midtrans | P0 |
| FR-PAY-2 | Endpoint notifikasi Midtrans memverifikasi signature sebelum memproses | P0 |
| FR-PAY-3 | Pemrosesan notifikasi bersifat idempotent: notifikasi ganda tidak memicu dua kali pemenuhan | P0 |
| FR-PAY-4 | Status pending, settlement, expire, cancel, dan deny ditangani dan memperbarui status pesanan | P0 |
| FR-PAY-5 | Data pembayaran disimpan di tabel terpisah dari pesanan | P0 |

### 6.5 Pemenuhan Pesanan (Digiflazz)

| ID | Kebutuhan | Prioritas |
| --- | --- | --- |
| FR-FUL-1 | Setelah pembayaran terkonfirmasi, sistem mengirim transaksi ke Digiflazz dengan `ref_id` unik per pesanan | P0 |
| FR-FUL-2 | Status Digiflazz (sukses, pending, gagal) diterima lewat webhook dan/atau pengecekan ulang terjadwal, lalu dicocokkan lewat `ref_id` | P0 |
| FR-FUL-3 | Penanganan transaksi gagal atau pending: pesanan ditandai dan masuk antrean tindak lanjut admin; kebijakan refund Midtrans **\[PERLU KEPUTUSAN\]** | P0 |
| FR-FUL-4 | Admin melihat saldo deposit Digiflazz di dashboard, dengan peringatan saat saldo di bawah ambang | P0 |

### 6.6 Dashboard Admin dan Laporan

| ID | Kebutuhan | Prioritas |
| --- | --- | --- |
| FR-ADM-1 | Kelola harga jual, stok/ketersediaan, dan produk | P0 |
| FR-ADM-2 | Lihat dan cari data pelanggan | P0 |
| FR-ADM-3 | Lihat daftar dan detail seluruh transaksi beserta statusnya | P0 |
| FR-ADM-4 | Laporan transaksi per periode dengan ekspor CSV, sesuai permission | P1 |
| FR-ADM-5 | Superadmin mengelola pengguna internal, role, permission, dan konfigurasi integrasi (kunci API) | P0 |

### 6.7 RBAC dan Audit Log

| ID | Kebutuhan | Prioritas |
| --- | --- | --- |
| FR-RBAC-1 | Hak akses berbasis role dan permission, dengan CRUD sebagai dasar ditambah aksi khusus (sinkron produk, ubah harga, lihat saldo, atur kunci API, ekspor laporan) | P0 |
| FR-RBAC-2 | Penegakan di sisi server (middleware, policy/gate). Menyembunyikan menu bukan otorisasi | P0 |
| FR-RBAC-3 | Ownership: pelanggan hanya dapat membuka transaksinya sendiri (cegah IDOR) | P0 |
| FR-RBAC-4 | Penolakan akses mengembalikan respons 403/404 tanpa membocorkan informasi internal | P0 |
| FR-LOG-1 | Audit log mencatat: ubah harga, ubah stok, ubah role/permission, ubah konfigurasi integrasi, login admin, ekspor laporan | P0 |
| FR-LOG-2 | Setiap entri memuat pelaku, aksi, objek, data sebelum/sesudah, IP, dan waktu | P0 |
| FR-LOG-3 | Tabel bersifat append-only; hanya superadmin yang dapat melihat, tidak ada yang dapat menghapus lewat aplikasi | P0 |

## 7. Matriks Role dan Permission (Draf)

| Permission | Pelanggan | Admin | Superadmin |
| --- | --- | --- | --- |
| produk.lihat | Ya | Ya | Ya |
| pesanan.buat | Ya | Tidak | Tidak |
| transaksi.lihat\_milik\_sendiri | Ya | Tidak | Tidak |
| transaksi.lihat (semua) | Tidak | Ya | Ya |
| produk.ubah / stok.ubah | Tidak | Ya | Ya |
| harga.ubah | Tidak | Ya | Ya |
| produk.sinkron | Tidak | Ya | Ya |
| pelanggan.lihat | Tidak | Ya | Ya |
| laporan.lihat / laporan.ekspor | Tidak | Ya | Ya |
| saldo\_digiflazz.lihat | Tidak | Ya | Ya |
| pengguna.kelola | Tidak | Tidak | Ya |
| role.kelola | Tidak | Tidak | Ya |
| integrasi.atur | Tidak | Tidak | Ya |
| ign.konfigurasi | Tidak | Tidak | Ya |
| audit.lihat | Tidak | Tidak | Ya |

## 8. Alur Transaksi dan Status

1. Pelanggan memilih game, mengisi ID akun, dan sistem menampilkan nickname untuk dikonfirmasi.
2. Pelanggan memilih nominal dan checkout. Pesanan dibuat dengan status **MENUNGGU\_BAYAR** dan harga dikunci.
3. Pelanggan membayar lewat Midtrans.
4. Midtrans mengirim notifikasi. Setelah signature valid dan belum pernah diproses, status menjadi **DIBAYAR**. Jika expire/cancel/deny, status menjadi **KEDALUWARSA** atau **DIBATALKAN**.
5. Sistem mengirim transaksi ke Digiflazz dan status menjadi **DIPROSES**.
6. Digiflazz mengembalikan hasil: **SUKSES**, atau **GAGAL** yang masuk antrean tindak lanjut admin.
7. Pelanggan melihat status dan riwayat pada halaman transaksinya.

## 9. Integrasi Eksternal

| Layanan | Fungsi | Catatan keamanan dan risiko |
| --- | --- | --- |
| Digiflazz | Daftar produk, pemesanan, cek saldo, webhook status | Kunci API di `.env`/terenkripsi, tidak disimpan mentah di database; pencocokan `ref_id`; verifikasi webhook |
| Midtrans | Pembayaran dan notifikasi | Verifikasi signature, idempotency, kunci server tidak terekspos ke klien |
| Pengecekan IGN (GoPay Games, via GameCheckService) | Menampilkan nickname dari User ID | Endpoint tidak resmi/tidak terdokumentasi; dapat berubah atau diblokir; dipanggil hanya dari server; timeout 12 detik; cache; rate limit; feature flag; fallback konfirmasi manual |

## 10. Kebutuhan Non-Fungsional

- **Keamanan:** password hash, proteksi CSRF dan XSS bawaan Laravel, validasi input, query aman (Eloquent/binding), HTTPS, rahasia hanya di `.env`.
- **Privasi log:** log aplikasi tidak memuat data sensitif berlebihan dan pesan error internal tidak ditampilkan ke pengguna.
- **Ketahanan:** gangguan Digiflazz, Midtrans, atau checker IGN tidak boleh membuat sistem mati; kegagalan ditangani dengan status jelas.
- **Kinerja:** pengecekan IGN dibatasi timeout dan memakai cache; halaman katalog memakai data lokal hasil sinkronisasi.
- **Konsistensi:** skema database dan kode seragam (contoh: `role_id` dan relasi, bukan campuran).
- **Antarmuka:** responsif, bahasa Indonesia, mengikuti palet brand KAIDEN.

## 11. Model Data (Tabel Utama)

| Tabel | Isi utama |
| --- | --- |
| users | id, nama, email, password hash, status |
| roles, permissions | daftar role dan permission |
| role\_permission, user\_role | relasi role-permission dan user-role |
| games | nama game, kode checker, butuh zone, status |
| products | kode SKU Digiflazz, game\_id, nama, harga modal, harga jual, status aktif |
| orders | id, user\_id, product\_id, user\_id\_game, zone\_id, nickname terkonfirmasi, harga terkunci, status |
| payments | order\_id, order\_id Midtrans, metode, status, payload notifikasi, waktu |
| fulfillments | order\_id, ref\_id Digiflazz, status, respons ringkas |
| settings | konfigurasi integrasi (nilai sensitif terenkripsi), feature flag IGN |
| audit\_logs | actor\_id, aksi, objek, data\_sebelum, data\_sesudah, IP, waktu |

Catatan: PK transaksi adalah id sendiri (bukan player\_id seperti kesalahan pada proposal lama), dan tidak ada tabel yang tidak berkaitan dengan fitur.

## 12. Panduan Antarmuka

| Elemen | Warna |
| --- | --- |
| Aksen utama dan tombol aksi | Merah #6D0808 |
| Teks, header, latar gelap | Hitam #212121 |
| Latar halaman dan kartu | Putih #F9F8F6 |

Halaman pelanggan: beranda dan daftar game, halaman produk (input ID, nickname, nominal), checkout, riwayat transaksi. Halaman admin: ringkasan (saldo Digiflazz, transaksi hari ini), produk dan harga, transaksi, pelanggan, laporan. Halaman superadmin: pengguna dan role, konfigurasi integrasi, audit log.

## 13. Rencana Pengujian

- **Fungsional:** login, katalog, pesanan, pembayaran, riwayat.
- **Matriks uji RBAC:** setiap peran dikalikan setiap aksi, hasil harus sesuai (diizinkan atau ditolak).
- **Akses tidak sah:** IDOR pada URL transaksi, forced browsing ke halaman admin, eskalasi peran, manipulasi parameter dan mass assignment.
- **Midtrans:** sukses, pending, gagal, kedaluwarsa, callback ganda, signature palsu.
- **Digiflazz:** sukses, pending, gagal, `ref_id` ganda.
- **IGN:** ID valid, ID salah, checker timeout atau diblokir (fallback berjalan), rate limit aktif.
- **Audit log:** setiap aksi penting tercatat dan tidak dapat dihapus lewat aplikasi.

## 14. Risiko dan Mitigasi

| Risiko | Mitigasi |
| --- | --- |
| Endpoint IGN tidak resmi berubah atau diblokir | Feature flag, fallback konfirmasi manual, tidak jadi syarat mutlak transaksi, dicatat sebagai batasan |
| Kunci API bocor | Simpan di `.env`/terenkripsi, hanya superadmin yang dapat mengatur |
| Callback Midtrans dipalsukan atau terkirim ganda | Verifikasi signature dan idempotency |
| Saldo Digiflazz habis saat ada pembayaran | Peringatan ambang saldo, status gagal ditangani admin |
| Penyalahgunaan hak akses internal | RBAC, pemisahan akun admin dan superadmin, audit log |
| Salah ID game tetap lolos | Konfirmasi nickname dan konfirmasi ulang sebelum bayar |
| Proposal terlihat sebagai "Kurniawan tanpa SSO" | Jaga kedalaman RBAC (permission, ownership, audit log) dan buktikan lewat pengujian |

## 15. Tahapan Pengembangan (Waterfall)

1. **Analisis kebutuhan:** finalisasi PRD dan keputusan terbuka.
2. **Perancangan:** ERD, matriks RBAC, alur transaksi, antarmuka.
3. **Implementasi:** fondasi RBAC dan audit log, katalog Digiflazz, pembayaran Midtrans, pemenuhan pesanan, pengecekan IGN, dashboard dan laporan.
4. **Pengujian:** fungsional, RBAC, akses tidak sah, integrasi.
5. **Penyusunan laporan dan perbaikan.**

## 16. Daftar Keputusan Terbuka

| No | Pertanyaan | Usulan |
| --- | --- | --- |
| 1 | Validasi IGN dipakai (mengubah keputusan FIX no. 6) | Ya, dengan fallback dan dicatat sebagai batasan |
| 2 | Jika checker gagal, apakah transaksi diblokir | Tidak diblokir, wajib konfirmasi ulang manual |
| 3 | Jumlah peran RBAC | 3 peran |
| 4 | Admin dan superadmin satu orang dengan dua akun | Ya |
| 5 | Audit log masuk lingkup | Ya |
| 6 | Notifikasi email/WhatsApp | Di luar lingkup versi 1 |
| 7 | Format dan periode laporan | Harian/bulanan, ekspor CSV |
| 8 | Kebijakan refund jika Digiflazz gagal setelah bayar | Refund manual lewat Midtrans, dicatat di audit log |
| 9 | Profil KAIDEN (nama resmi, skala, rencana operasional) | Perlu data dari penulis |

## 17. Sumber

- Kontrol Proposal Rifki (keputusan FIX, konsep RBAC, risiko teknis, rencana pengujian).
- README dan LARAVEL\_GUIDE (mekanisme cek IGN, GameCheckService, contoh caching).
- Referensi sitasi menyusul sesuai aturan proposal.
