# Check IGN Tester & Laravel Integrator

Sistem pengujian langsung untuk mekanisme pengecekan **In-Game Name (IGN)** berdasarkan Account ID dari repositori [aditamagf/check-ign](https://github.com/aditamagf/check-ign), dilengkapi dengan provider GoPay Games modern yang cepat dan stabil serta panduan & kode integrasi siap pakai untuk aplikasi **Laravel**.

---

## ⚡ Fitur Utama
1. **Web Tester Dashboard**: Akses langsung di browser lokal Anda melalui Apache XAMPP: [http://localhost/cekapiusn/](http://localhost/cekapiusn/).
2. **REST API Siap Pakai**: Endpoint JSON di [http://localhost/cekapiusn/api.php](http://localhost/cekapiusn/api.php).
3. **Multi-Provider Engine**:
   - **GoPay Games Provider** (Sangat cepat dan terbukti bekerja 100% untuk Mobile Legends, Free Fire, Valorant, Call of Duty, AOV, dll).
   - **Codashop Provider** (initPayment flow).
   - **DancingIdol Provider** (AU2 Mobile).
4. **Preset 1-Click Test**: Tombol pengisian akun contoh valid langsung di antarmuka web.
5. **Laravel Ready**: Folder `laravel/` berisi Service, Controller, dan Route yang bisa langsung dicopy-paste ke aplikasi Laravel Anda.

---

## 📂 Struktur File
- [`index.php`](file:///c:/xampp/htdocs/cekapiusn/index.php) : Antarmuka web interaktif pengujian nickname dan dokumentasi tab Laravel.
- [`api.php`](file:///c:/xampp/htdocs/cekapiusn/api.php) : Endpoint API JSON untuk pengujian via cURL, fetch, atau Postman.
- [`src/`](file:///c:/xampp/htdocs/cekapiusn/src/) : Core engine library `Aditdev\ApiGames` dan semua provider.
- [`laravel/`](file:///c:/xampp/htdocs/cekapiusn/laravel/) : File-file Laravel siap pakai (`GameCheckService.php`, `GameCheckController.php`, `routes/api.php`).
- [`LARAVEL_GUIDE.md`](file:///c:/xampp/htdocs/cekapiusn/LARAVEL_GUIDE.md) : Panduan lengkap step-by-step implementasi di Laravel.
- [`cli_example.php`](file:///c:/xampp/htdocs/cekapiusn/cli_example.php) : Skrip pengujian via terminal / command line (`php cli_example.php`).

---

## 🛠️ Cara Menguji Langsung

### 1. Melalui Browser (UI Interaktif)
Buka browser dan akses:
```
http://localhost/cekapiusn/
```

### 2. Melalui Terminal / CLI
Jalankan perintah:
```bash
php cli_example.php
```

### 3. Melalui cURL / HTTP Request
```bash
curl "http://localhost/cekapiusn/api.php?game=MOBILE_LEGENDS&user_id=604210151&zone_id=8425"
```
Respon JSON:
```json
{
  "success": true,
  "status": 200,
  "game": "MOBILE_LEGENDS",
  "game_name": "Mobile Legends: Bang Bang",
  "user_id": "604210151",
  "zone_id": "8425",
  "nickname": "Diezy x Ruuma Kopi",
  "provider": "gopay",
  "execution_time_ms": 320
}
```
