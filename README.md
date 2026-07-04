# Sistem Monitoring Kambing (SPK K-Means Clustering)

Sistem Monitoring Kambing adalah aplikasi berbasis web cerdas yang dirancang untuk membantu peternak dalam menganalisis karakteristik dan produktivitas kambing secara sistematis. Aplikasi ini memanfaatkan algoritma **K-Means Clustering** untuk mengelompokkan data produktivitas ke dalam beberapa tingkatan (Rendah, Sedang, Tinggi) guna mendukung pengambilan keputusan yang lebih baik di peternakan.

## 🚀 Fitur Utama

- **Manajemen Data Kambing**: Pengelolaan data master kambing (Kode, Jenis Kelamin, Tanggal Lahir).
- **Pencatatan Produktivitas**: Input data parameter produktivitas yang meliputi:
  - Bobot Badan (kg)
  - Tingkat Kelahiran (ekor) - *khusus betina*
  - Produksi Susu (Liter) - *khusus betina*
- **K-Means Clustering Engine**: 
  - Proses *clustering* otomatis berdasarkan data produktivitas yang ada.
  - Penentuan nilai *centroid* awal secara acak atau terstruktur.
  - Iterasi perhitungan jarak menggunakan *Euclidean Distance* hingga *centroid* stabil.
  - Pengelompokan menjadi 3 kluster: **Tinggi**, **Sedang**, dan **Rendah**.
- **Pelaporan & Ekspor**: Cetak hasil *clustering* dan statistik dalam format PDF yang rapi (lengkap dengan informasi sesi dan tanda tangan petugas).
- **Manajemen Pengguna**: Pengelolaan akses akun administrator.
- **Antarmuka Modern**: UI/UX interaktif yang dibangun dengan standar estetika *glassmorphism*, *micro-animations*, dan desain responsif.

## 🛠️ Teknologi yang Digunakan

Aplikasi ini dikembangkan menggunakan tumpukan teknologi (*tech stack*) modern:

- **Backend**: [Laravel 13.x](https://laravel.com/) (PHP)
- **Frontend / Styling**: [Tailwind CSS 4.x](https://tailwindcss.com/)
- **Interaktivitas (Vanilla JS Alternative)**: [Alpine.js](https://alpinejs.dev/)
- **Ikonografi**: [FontAwesome 6](https://fontawesome.com/) & Kustom SVG
- **Database**: MySQL / MariaDB
- **Build Tool**: [Vite 8.x](https://vitejs.dev/) & [NPM](https://www.npmjs.com/)

## 📋 Persyaratan Sistem

Sebelum menjalankan aplikasi, pastikan sistem Anda memiliki komponen berikut:

- PHP >= 8.3
- Composer
- Node.js & NPM
- MySQL atau MariaDB Server

## ⚙️ Instalasi & Setup

Ikuti langkah-langkah berikut untuk menjalankan proyek ini di lingkungan lokal Anda:

1. **Clone Repository (Jika menggunakan Git)**
   ```bash
   git clone <url-repo-anda>
   cd kambingclustering
   ```

2. **Instalasi Dependensi PHP (Backend)**
   ```bash
   composer install
   ```

3. **Instalasi Dependensi Node (Frontend)**
   ```bash
   npm install
   ```

4. **Konfigurasi Environment**
   Salin file konfigurasi bawaan dan sesuaikan pengaturan database Anda:
   ```bash
   cp .env.example .env
   ```
   Buka file `.env` dan atur konfigurasi database:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=db_kambing
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Generate Application Key**
   ```bash
   php artisan key:generate
   ```

6. **Migrasi dan Seeding Database**
   Jalankan migrasi untuk membuat tabel beserta data awal (*dummy* data dan akun admin):
   ```bash
   php artisan migrate --seed
   ```
   > **Catatan:** Secara default, *seeder* akan membuat dua jenis akun:
   > - **Administrator**: Username `admin` / Password `admin123`
   > - **Petugas**: Username `user` / Password `user123`

7. **Kompilasi Aset Frontend dengan Vite**
   Aplikasi ini menggunakan Vite untuk memproses aset CSS/JS.
   Untuk lingkungan *development* (Hot Module Replacement):
   ```bash
   npm run dev
   ```
   Atau untuk membangun (*build*) file aset production:
   ```bash
   npm run build
   ```

8. **Jalankan Server Development**
   Buka terminal baru dan jalankan:
   ```bash
   php artisan serve
   ```
   Aplikasi sekarang dapat diakses melalui browser di `http://localhost:8000`.

## 🧮 Cara Kerja Algoritma K-Means di Aplikasi Ini

1. **Inisialisasi Centroid**: Sistem memilih 3 titik *centroid* awal berdasarkan kriteria (bisa diacak dari data yang ada).
2. **Perhitungan Jarak**: Menghitung jarak antara setiap data produktivitas kambing terhadap ke-3 *centroid* menggunakan rumus jarak *Euclidean*.
3. **Pengelompokan (Clustering)**: Data kambing dimasukkan ke dalam kluster yang memiliki jarak terdekat dengan *centroid* tersebut.
4. **Pembaruan Centroid**: Menghitung rata-rata dari semua data pada masing-masing kluster untuk mendapatkan nilai *centroid* baru.
5. **Iterasi**: Langkah 2-4 diulangi hingga posisi *centroid* tidak lagi berubah (konvergen).

## 📄 Lisensi

Hak Cipta &copy; 2026 Sistem Monitoring Kambing. Seluruh hak cipta dilindungi undang-undang.
