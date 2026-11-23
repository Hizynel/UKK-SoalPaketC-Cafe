# README - Sistem Manajemen Kedai Kopi Senja

## 1. Ringkasan
Sistem ini bernama **Cafe Senja**, dirancang untuk mengelola operasional kafe dengan tiga jenis pengguna:
1. **Manajer (Admin):** Mengatur seluruh data master kafe (bahan baku, menu, resep, stok masuk) dan memantau laporan operasional (penjualan, stok bahan hampir habis, HPP, serta margin laba).
2. **Kasir:** Melayani transaksi pemesanan langsung di kasir, mencetak struk, menjadi penghubung alur pesanan ke dapur hingga selesai, serta membantu reset kata sandi pelanggan yang lupa password.
3. **Pelanggan:** Memesan menu secara mandiri melalui aplikasi Android (APK) dari meja kafe (meja 1–10) tanpa harus antre di kasir, serta memantau status pembuatan pesanan dan struknya secara *real-time*.

Alur kerja sistem mencakup dua skema pemesanan:
- **Pemesanan Langsung:** Pelanggan memesan di kasir, kasir menginput pesanan ke sistem web, pesanan diproses dapur, dan kasir menyelesaikan transaksi serta mencetak struk.
- **Pemesanan Mandiri (Self-Order APK):** Pelanggan login di aplikasi Android, memilih nomor meja, memilih menu, dan mengirim pesanan. Kasir memproses pesanan tersebut secara berjenjang hingga siap saji/selesai.

---

## 2. Teknologi
- **Backend Server:** PHP (Laravel Framework v13 / v11 compatible)
- **Panel Manajer (Admin):** Filament Admin Panel (v5 / v3 compatible)
- **Tampilan Web Kasir:** HTML5, Blade Templating, dan framework CSS Tailwind
- **Database:** Relasional MySQL
- **Aplikasi Mobile (APK Pelanggan):** Framework Ionic (React + TypeScript + Capacitor)

**Alasan Pemilihan Teknologi:**
- **Laravel & Filament:** Laravel menyediakan arsitektur MVC yang rapi, keamanan bawaan, migrasi skema database yang tangguh, serta ekosistem REST API yang handal. Filament mempermudah pengelolaan data master, validasi relasi resep, dan visualisasi laporan Manajer secara terintegrasi.
- **MySQL:** Database relasional yang stabil, mudah dikonfigurasi, dan memiliki integritas data relasional yang ketat untuk pencatatan transaksi serta stok bahan.
- **Tailwind CSS:** Mempercepat implementasi tata letak responsif antarmuka Kasir agar sesuai dengan rancangan mockup desain.
- **Ionic (React + Capacitor):** Memungkinkan pengembangan aplikasi Android berkinerja tinggi berbasis React dan TypeScript, serta integrasi native Capacitor yang stabil untuk komunikasi REST API ke server backend.

---

## 3. Cara Menjalankan Server
> Petunjuk instalasi dan langkah kerja ini dibuat dan diuji pada perangkat dengan sistem operasi **Linux**.

### Prasyarat Sistem
- PHP >= 8.2 (dengan ekstensi `pdo_mysql`, `mbstring`, `openssl`, `bcmath`, `curl`, `intl`)
- Composer >= 2
- Node.js >= 20 & NPM
- Database Server MySQL / MariaDB

### Langkah-langkah Menjalankan Server:
1. **Clone Repositori & Masuk ke Folder Proyek**
   ```bash
   git clone https://github.com/Hizynel/cafe.git
   cd cafe
   ```

2. **Salin Berkas Konfigurasi Lingkungan (`.env`)**
   ```bash
   cp .env.example .env
   ```

3. **Pasang Dependensi PHP & Frontend Web**
   ```bash
   composer install
   npm install
   ```

4. **Generate Application Key**
   ```bash
   php artisan key:generate
   ```

5. **Sesuaikan Konfigurasi `.env`**
   Buka berkas `.env`, lalu atur koneksi database dan kredensial API Supplier:
   ```env
   APP_NAME="Cafe Senja"
   APP_ENV=local
   APP_KEY=[Otomatis dari key:generate]
   APP_DEBUG=true
   APP_URL=http://[IP_ADDRESS]:8000

   DB_CONNECTION=mysql
   DB_HOST=[IP_ADDRESS]
   DB_PORT=3306
   DB_DATABASE=cafe_senja
   DB_USERNAME=[USERNAME_DATABASE]
   DB_PASSWORD=[PASSWORD_DATABASE]

   # Integrasi Layanan Supplier
   SUPPLIER_URL=https://ukksmkn1katapang.next-it.co.id/kafe/harga-bahan
   SUPPLIER_API_KEY=[API_KEY]
   ```
   *Catatan: Pastikan database `cafe_senja` telah dibuat di MySQL:*
   ```sql
   CREATE DATABASE cafe_senja;
   ```

6. **Jalankan Migrasi Database**
   ```bash
   php artisan migrate:fresh
   ```

7. **Jalankan Seeder Data Pengguna Demo**
   ```bash
   php artisan db:seed
   ```

8. **Impor Data Awal CSV (Bahan Baku, Menu, dan Resep)**
   Impor dapat dilakukan melalui panel Manajer di web:
   1. Login ke panel Manajer (`http://[IP_ADDRESS]:8000/manager/login`).
   2. Buka menu **Bahan Baku** (atau **Menu** / **Resep**), lalu klik tombol **Import CSV**.
   3. Pilih dan unggah berkas CSV data awal yang disediakan penguji.
   4. Sistem menampilkan notifikasi ringkasan hasil impor (jumlah bahan, menu, dan resep yang berhasil, serta baris yang dilewati).

9. **(Opsional) Sinkronisasi Harga Terkini ke API Supplier**
   Perbarui harga bahan baku langsung dari layanan Supplier melalui tombol di panel Manajer (**Bahan Baku** -> klik **Perbarui Harga dari Supplier**) atau melalui perintah terminal:
   ```bash
   php artisan supplier:sync-price
   ```

10. **Build Asset Frontend Web & Jalankan Server**
    Jalankan perintah berikut secara berurutan:
    ```bash
    npm run build
    php artisan serve --host=0.0.0.0 --port=8000
    ```
    *Catatan: `npm run build` hanya perlu dijalankan sekali untuk mengompilasi asset frontend web. Jika sedang dalam tahap pengembangan, Anda dapat menjalankan `npm run dev` pada terminal terpisah untuk live reload.*
    *Parameter `--host=0.0.0.0` wajib disertakan agar server Laravel dapat diakses oleh HP Android pada jaringan lokal yang sama.*

Akses aplikasi web melalui browser:
- Login Kasir / Sistem Utama: `http://[IP_ADDRESS]:8000/login`
- Panel Manajer (Filament Admin): `http://[IP_ADDRESS]:8000/manager/login`

---

## 4. Cara Memasang APK
Berkas APK siap pasang diletakkan pada folder `CafeApk/apk` di repositori ini (`CafeApk/apk/cafe-senja.apk`).

### Masalah `localhost` pada HP / Perangkat Fisik
> **PENTING:** APK yang memanggil alamat `http://localhost:8000` atau `http://127.0.0.1:8000` **TIDAK BISA** menghubungi server dari HP Android fisik, karena `localhost` pada HP merujuk ke HP itu sendiri. APK Android harus memanggil alamat IP lokal komputer server (misalnya: `http://[IP_ADDRESS]:8000/api`).

### Konfigurasi Cleartext Traffic (HTTP) & CORS
- **CORS:** Server Laravel telah dikonfigurasi pada berkas `config/cors.php` dengan `allowed_origins => ['*']` untuk mengizinkan request API dari aplikasi mobile.
- **Cleartext Traffic:** Android versi 8.0 ke atas secara bawaan memblokir koneksi HTTP biasa (*cleartext*). Konfigurasi pada `CafeApk/capacitor.config.ts` telah diatur agar mengizinkan koneksi HTTP lokal:
  ```typescript
  server: {
    androidScheme: 'http',
    cleartext: true
  }
  ```

### Cara Menyesuaikan IP Server dan Build Ulang APK (Jika IP Server Berubah):
> **Catatan Keterbatasan:** Alamat IP server pada file APK bersifat statis saat dikompilasi. Jika komputer server berpindah jaringan Wi-Fi atau IP lokal berubah, alamat server pada APK perlu disesuaikan dan di-build ulang:

1. Pastikan HP Android dan komputer server terhubung ke **jaringan Wi-Fi/Hotspot yang sama**.
2. Cari IP lokal komputer server (jalankan `hostname -I` atau `ip a` di Linux, atau `ipconfig` di Windows, contoh: `192.168.1.15`).
3. Buka berkas [CafeApk/src/services/api.ts](CafeApk/src/services/api.ts) dan sesuaikan konstanta `API_BASE_URL`:
   ```typescript
   const API_BASE_URL = 'http://192.168.1.15:8000/api';
   ```
4. Masuk ke folder `CafeApk`, instal dependensi, lalu build:
   ```bash
   cd CafeApk
   npm install
   npm run build
   npx cap sync android
   ```
5. Kompilasi APK menggunakan Gradle (atau buka folder `CafeApk/android` via Android Studio):
   ```bash
   cd android
   ./gradlew assembleDebug
   ```
   Salin berkas APK hasil build ke direktori `CafeApk/apk`:
   ```bash
   mkdir -p ../apk
   cp app/build/outputs/apk/debug/app-debug.apk ../apk/cafe-senja.apk
   ```

### Cara Memasang APK di HP Android:
1. Salin berkas `cafe-senja.apk` dari folder `CafeApk/apk/` ke perangkat HP Android (melalui kabel USB, WhatsApp Web, Google Drive, atau download langsung).
2. Di HP Android, buka File Manager dan pilih `cafe-senja.apk`.
3. Aktifkan izin **"Izinkan pemasangan dari sumber tidak dikenal" (Allow from unknown sources)** jika diminta.
4. Tekan **Install / Pasang** hingga selesai, lalu buka aplikasi **Cafe Senja**.

---

## 5. Akun Uji
Data akun pengguna telah disediakan melalui seeder database (`DatabaseSeeder.php`):

- **Akun Manajer (Admin Panel Web):**
  - Nomor Telepon: `08123456789`
  - Password: `manager123`
  - Hak Akses: Mengelola bahan baku, menu, resep, mencatat stok masuk, impor CSV, sinkronisasi harga API supplier, serta membaca seluruh laporan (penjualan, stok hampir habis, margin menu, riwayat pesanan).

- **Akun Kasir (Web Kasir):**
  - Nomor Telepon: `081234567899`
  - Password: `kasir123`
  - Hak Akses: Membuat pesanan di kasir, memperbarui status pesanan berjenjang (Baru -> Diproses -> Siap Saji -> Selesai / Batal), mencetak struk, melihat ketersediaan menu, serta mereset kata sandi pelanggan yang lupa password.

- **Akun Pelanggan (Aplikasi APK):**
  - Nomor Telepon: `081234567890`
  - Password: `pelanggan123`
  - Hak Akses: Login ke APK, melihat daftar menu dan status ketersediaan porsi, memilih nomor meja (1–10) dan metode pembayaran, membuat pesanan mandiri, memantau riwayat & struk pesanan milik sendiri, serta mengganti kata sandi.

---

## 6. Letak Berkas
1. **Letak Berkas Migrasi Database:**
   - Direktori: `database/migrations/`
   - Berkas-berkas tabel utama:
     - `database/migrations/0001_01_01_000000_create_users_table.php` (Pengguna & peran)
     - `database/migrations/2026_11_13_042757_create_menus_table.php` (Menu & status aktif)
     - `database/migrations/2026_11_13_054729_create_bahan_bakus_table.php` (Master bahan baku)
     - `database/migrations/2026_11_13_055146_create_reseps_table.php` (Komposisi resep menu)
     - `database/migrations/2026_11_13_055855_create_pesanans_table.php` (Data transaksi pesanan)
     - `database/migrations/2026_11_13_060212_create_detail_pesanans_table.php` (Detail item menu pesanan)
     - `database/migrations/2026_11_14_080808_create_stock_masuks_table.php` (Riwayat stok masuk bahan)
     - `database/migrations/2026_11_17_021350_create_pesanan_bahans_table.php` (Snapshot bahan baku terpotong per pesanan)

2. **Letak Logika Impor Data Awal:**
   - Berkas Service Parsing & Normalisasi: `app/Services/CsvImportService.php`
   - Aksi unggah CSV di panel Manajer:
     - `app/Filament/Resources/BahanBakus/Pages/ListBahanBakus.php` (Aksi `importCsv` pada header tabel Bahan Baku)
     - `app/Filament/Resources/Menus/Pages/ListMenus.php` (Aksi `importCsv` pada header tabel Menu)
     - `app/Filament/Resources/Reseps/Pages/ListReseps.php` (Aksi `importCsv` pada header tabel Resep)
   - Berkas CSV disediakan oleh penguji dan dapat diimpor langsung via antarmuka Manajer.

3. **Letak Seeder Data Demo:**
   - Berkas Seeder: `database/seeders/DatabaseSeeder.php`

4. **Perintah Menjalankan Seeder Data Demo:**
   ```bash
   php artisan db:seed
   ```
   *(Atau secara spesifik: `php artisan db:seed --class=DatabaseSeeder`)*

---

## 7. Keputusan Desain dan Teknis
Berikut adalah keputusan teknis untuk hal-hal yang ditandai *"Anda tentukan sendiri"* pada dokumen spesifikasi:

1. **Penanganan Data Awal CSV yang Belum Rapi (`CsvImportService.php`):**
   - **Normalisasi Satuan:** Sistem secara otomatis mengonversi satuan ke satuan dasar (`kg` dikonversi menjadi `g` dengan pengali 1.000; `liter`/`l` dikonversi menjadi `ml` dengan pengali 1.000; penyeragaman variasi teks seperti `gram`/`gr` -> `g`, `biji`/`lembar`/`buah` -> `pcs`).
   - **Penanganan Variasi Ejaan / Salah Ketik (Typo):** Pembersihan spasi ganda, pengubahan teks menjadi huruf kecil (*case-insensitive*), serta pemetaan kamus normalisasi nama bahan pada kode (contoh: `'biji kopi arabica'` dipetakan ke `'biji kopi arabika'`, `'gula pasri'` dipetakan ke `'gula pasir'`).
   - **Bahan Tidak Dikenal:** Baris resep yang merujuk pada bahan baku yang tidak ada dalam database dilewati (*skipped*), sehingga impor bahan dan menu lainnya tetap berhasil masuk tanpa menggugurkan transaksi impor.
   - **Batas Minimum Awal:** Inisialisasi awal nilai kolom `batas_minimun` (stok minimum) disetel ke `0`, dan dapat diperbarui sewaktu-waktu oleh Manajer.

2. **Format Validasi Nomor Telepon Pelanggan:**
   - Nomor telepon wajib numerik (hanya angka 0–9).
   - Panjang nomor dibatasi minimal 10 digit.
   - Karakter non-digit atau huruf akan ditolak saat registrasi di APK.

3. **Batasan Nomor Meja di APK:**
   - Nomor meja dibatasi hanya dari angka `1` sampai `10`. Input di luar rentang tersebut ditolak oleh validasi sisi server REST API.

4. **Batasan Pemesanan Aktif di APK:**
   - Setiap akun pelanggan hanya diizinkan memiliki **1 pesanan berstatus Baru** pada satu waktu untuk menghindari pesanan ganda yang tidak disengaja.

5. **Kebijakan Pembatalan Pesanan:**
   - Pembatalan pesanan hanya dapat dilakukan oleh Kasir dan hanya saat pesanan masih berstatus **Baru**. Stok bahan baku yang sempat terpotong otomatis dikembalikan (*restocked*) ke database.

---

## 8. Lembar Self-Check
Tabel 14 butir penerimaan dari Lampiran A, dengan status klaim dan langkah uji singkat:

| No | Butir Penerimaan | Klaim (Ya/Tidak) | Langkah Uji Singkat |
|:--:|:---|:---:|:---|
| 1 | Kelola bahan (satuan dasar, nama tidak ganda), menu (bisa dinonaktifkan), dan resep (multi bahan, takaran) | **Ya** | Login Manajer di `/manager/login`, tambah bahan baru dengan nama yang sama (pastikan ditolak). Nonaktifkan salah satu menu, lalu pastikan menu tersebut tidak muncul di Kasir dan APK. Buat/cek menu dengan resep multi-bahan. |
| 2 | Pencatatan stok masuk; batas minimum per bahan diatur Manajer dan daftar bahan menipis langsung mengikutinya | **Ya** | Pada panel Manajer, catat Stok Masuk baru di menu **Stock Masuks**. Ubah batas minimum bahan; buka menu **Stock Hampir Habis** di grup Laporan dan pastikan daftar bahan yang stoknya di bawah batas minimum langsung muncul secara dinamis. |
| 3 | Pesanan dengan beberapa item; stok bahan berkurang sesuai resep × jumlah porsi; menu tanpa resep atau nonaktif tidak dapat dipesan | **Ya** | Buat pesanan berisi 2 menu berbeda (misal masing-masing 2 porsi). Verifikasi stok bahan baku di database/web berkurang tepat sesuai takaran resep × 2. Pastikan menu tanpa resep atau menu nonaktif tidak dapat dipesan. |
| 4 | Pesanan ditolak bila bahan tidak cukup, kebutuhan gabungan antar item dihitung benar (butir inti) | **Ya** | Pesan 2 menu berbeda yang sama-sama membutuhkan susu dalam jumlah banyak melebihi stok yang ada. Pastikan sistem menolak pesanan dengan pesan bahwa stok bahan gabungan tidak mencukupi. |
| 5 | Menu otomatis berstatus Habis bila bahan tidak cukup untuk 1 porsi | **Ya** | Kurangi stok bahan baku suatu menu hingga kurang dari takaran 1 porsi. Periksa daftar menu di Kasir dan APK; menu otomatis berstatus "Habis" dan tidak bisa dipilih. |
| 6 | Status pesanan berjenjang, loncat ditolak; batal hanya saat Baru, hanya oleh Kasir, dan stok kembali | **Ya** | Pada Kasir, uji perubahan status bertahap (Baru -> Diproses -> Siap Saji -> Selesai). Coba loncat status langsung ke Selesai (ditolak). Batalkan pesanan saat status Baru; pastikan stok bahan kembali utuh. |
| 7 | Struk: subtotal, service 5%, pajak 10% (urutan benar), total; struk lama tidak berubah bila harga jual menu diubah (butir inti) | **Ya** | Buat pesanan, buka struk. Verifikasi perhitungan: Subtotal -> Service 5% -> Pajak 10% -> Total. Ubah harga jual menu di Manajer; buka kembali struk lama dan pastikan angka harga dan total pada struk lama tetap tidak berubah. |
| 8 | HPP dan margin per menu dari resep | **Ya** | Buka menu **Margin Menu** di grup Laporan panel Manajer. Pastikan nilai HPP terhitung otomatis dari total (harga bahan × takaran resep), serta margin laba terhitung dari selisih harga jual dikurangi HPP. |
| 9 | Laporan penjualan per menu, bahan menipis, margin per menu; hak akses: kelola menu, resep, bahan, stok masuk, dan laporan hanya Manajer (peran lain ditolak, juga bila halaman dibuka lewat alamat langsung); Kasir melihat daftar menu tanpa resep, HPP, margin; Manajer tidak membuat pesanan dan tidak mengubah statusnya | **Ya** | Buka URL panel `/manager` saat login sebagai Kasir (akses ditolak/403). Di web Kasir, periksa daftar menu (tidak ada data resep, HPP, dan margin). Login Manajer dan pastikan laporan (Penjualan, Stock Hampir Habis, Margin Menu, Riwayat Pesanan) dapat diakses, serta tidak ada aksi untuk membuat atau mengubah status pesanan. |
| 10 | Data awal CSV: satuan dinormalkan, nama bahan salah ketik di resep dicocokkan, bahan tidak dikenal tidak diimpor, batas minimum awal 0 | **Ya** | Login Manajer, buka menu **Bahan Baku**, klik **Import CSV**, unggah berkas CSV data awal. Periksa bahwa satuan terkonversi, variasi ejaan bahan resep terpetakan, bahan tidak dikenal dilewati, dan nilai stok minimum awal adalah 0. |
| 11 | Integrasi Supplier (dengan API key), fallback harga terakhir + tanggal | **Ya** | Jalankan sinkronisasi via tombol **Perbarui Harga dari Supplier** di menu Bahan Baku atau command `php artisan supplier:sync-price` dengan API key valid. Simulasikan supplier offline/gagal koneksi, lalu pastikan sistem tetap berjalan normal menggunakan data harga terakhir beserta tanggal pencatatannya. |
| 12 | Akun pelanggan lengkap (APK): daftar sendiri dengan nomor HP unik + password minimal 8 karakter, login dengan nomor HP, ganti password, lupa password direset Kasir (wajib ganti saat login berikutnya), logout | **Ya** | Pada APK, registrasi akun baru (password < 8 karakter ditolak). Login, lalu coba ubah password. Pada web Kasir, lakukan aksi reset password akun tersebut. Saat login ulang dengan password sementara, pastikan APK mewajibkan ganti password sebelum bisa memesan. Uji logout. |
| 13 | Pelanggan (APK) memesan dengan memilih nomor meja; aturan pesanan dan stok sama dengan pesanan dari Kasir; hanya boleh satu pesanan berstatus Baru pada satu waktu | **Ya** | Di APK, pilih meja (1–10), pilih metode bayar (Tunai/QRIS), dan kirim pesanan. Stok bahan berkurang. Coba kirim pesanan kedua saat pesanan pertama masih berstatus Baru; pastikan sistem menolak pesanan kedua. |
| 14 | Pelanggan (APK) memantau status dan struk pesanannya sendiri; pesanan orang lain tidak bisa dibuka | **Ya** | Login sebagai Pelanggan A dan buat pesanan. Buka Riwayat Pesanan di APK dan cek struknya. Login sebagai Pelanggan B, pastikan riwayat dan struk pesanan milik Pelanggan A tidak terlihat oleh Pelanggan B. |

### Fitur Opsional:
- **O1 (Ukuran Reguler dan Large Minuman):** Tidak Dikerjakan.
- **O2 (Promo Happy Hour Diskon 20% Minuman):** Tidak Dikerjakan.
*(Pengembangan difokuskan untuk memastikan seluruh 14 butir wajib berjalan stabil dan memenuhi syarat kelulusan).*

---

## 9. Belum Selesai / Keterbatasan
Seluruh 14 butir wajib telah dikerjakan dan diuji.
**Keterbatasan:**
- Alamat IP server backend pada file APK bersifat statis (terkompilasi di dalam paket APK). Jika komputer server berpindah jaringan Wi-Fi atau IP lokal berubah, nilai `API_BASE_URL` di `CafeApk/src/services/api.ts` harus disesuaikan dan APK perlu di-build ulang.

---

## 10. Referensi dan Bantuan
- **Claude (Anthropic):** Digunakan untuk membantu menganalisis butir-butir kebutuhan pada dokumen soal Lampiran A dan merencanakan alur kerja sistem.
- **Antigravity AI Agent:** Digunakan untuk membantu penyusunan styling Tailwind CSS agar presisi dengan mockup, pembuatan template komponen antarmuka, serta integrasi endpoint REST API dan skrip parsing data CSV.
- **Dokumentasi Resmi:** Dokumentasi Laravel, Filament PHP, dan Ionic Framework / Capacitor.

Seluruh kode hasil saran alat bantu telah dipelajari, disesuaikan dengan arsitektur proyek, dirapikan, serta diuji secara langsung pada sistem Cafe Senja.
