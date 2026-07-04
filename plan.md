# Product Requirements Document (PRD)

## 1. Tujuan Sistem

Sistem Monitoring Produktivitas Kambing Berbasis Web dikembangkan untuk membantu proses pencatatan data kambing, monitoring produktivitas secara periodik, analisis menggunakan algoritma K-Means Clustering, serta penyajian hasil analisis sebagai pendukung pengambilan keputusan.

- **Nama Aplikasi:** Sistem Monitoring Kambing
- **Logo:** Goat
- **Framework:** Laravel 13
- **Styling:** Tailwind CSS v4
- **Database:** MySQL / SQLite

---

## 2. Role Pengguna

| Role | Hak Akses |
|------|-----------|
| **Admin** | Mengelola seluruh data, menjalankan proses clustering, mengelola pengguna, serta mencetak laporan. |
| **User** | Hanya dapat melihat (read-only) data kambing, data produktivitas, dashboard, dan hasil clustering. |

---

## 3. Daftar Halaman

| Halaman | Admin | User |
|---------|:-----:|:----:|
| Login | ✅ | ✅ |
| Dashboard (Stat Card & Grafik) | ✅ | ✅ |
| Data Kambing | CRUD, Import Excel | Read Only |
| Data Produktivitas | CRUD | Read Only |
| Proses K-Means | ✅ | ❌ |
| Hasil Clustering | Export Excel/PDF | Lihat |
| Kelola Pengguna | CRUD | ❌ |
| Profil & Pengaturan Akun | ✅ | ✅ |
| Logout | ✅ | ✅ |

---

## 4. Fitur Utama

### 4.1. Dashboard

- Stat card: total kambing, total kambing jantan, total kambing betina, total data produktivitas.
- Stat card: hasil clustering terakhir — jumlah per cluster (Rendah, Sedang, Tinggi).
- Grafik distribusi hasil clustering (Pie/Doughnut chart).
- Grafik tren bobot badan rata-rata per bulan (Line chart).
- Grafik produksi susu rata-rata per bulan (Bar chart).
- Informasi sesi clustering terakhir (tanggal, iterasi, admin yang menjalankan).

### 4.2. Data Kambing

- Tambah data kambing (manual input).
- Edit data kambing.
- Hapus data kambing (soft delete / confirmation modal).
- Hapus massal (bulk delete dengan checkbox).
- Import data dari Excel (mapping kolom otomatis dari dataset).
- Pencarian (search by kode kambing).
- Sorting per kolom.
- Filter berdasarkan jenis kelamin (Jantan / Betina).
- Pagination (10, 25, 50 per halaman).

### 4.3. Data Produktivitas

- Tambah data produktivitas per kambing per tanggal.
- Edit data produktivitas.
- Hapus data produktivitas (confirmation modal).
- Hapus massal (bulk delete).
- Pencarian (search by kode kambing).
- Sorting per kolom.
- Filter berdasarkan kambing, rentang tanggal.
- Pagination.
- Validasi: bobot_badan wajib diisi, produksi_susu wajib (default 0 untuk jantan), tingkat_kelahiran wajib (default 0 untuk jantan).
- Info: pencatatan bobot bisa per bulan, produksi susu bisa per hari.

### 4.4. Proses K-Means

- Menampilkan jumlah data kambing yang akan diproses.
- Menampilkan centroid awal (dipilih otomatis dengan metode persentil).
- Konfirmasi apakah sudah ada hasil clustering sebelumnya (akan ditimpa).
- Tombol "Jalankan K-Means" untuk memulai proses.
- Setelah proses:
  - Menampilkan centroid awal yang digunakan.
  - Menampilkan log setiap iterasi:
    - Tabel jarak setiap kambing ke masing-masing centroid (C1, C2, C3).
    - Cluster sementara yang dipilih per kambing.
    - Centroid baru hasil perhitungan ulang.
  - Menampilkan total iterasi hingga konvergen.
  - Menampilkan centroid akhir.
- Agregasi data untuk clustering:
  - **Bobot Badan:** rata-rata dari seluruh pencatatan per kambing.
  - **Tingkat Kelahiran:** nilai terbesar (max) dari seluruh pencatatan per kambing.
  - **Produksi Susu:** rata-rata dari seluruh pencatatan per kambing.

### 4.5. Hasil Clustering

- Menampilkan tabel hasil cluster setiap kambing:
  - Kode kambing, jenis kelamin.
  - Nilai bobot, kelahiran, susu yang digunakan saat clustering.
  - Jarak ke C1, C2, C3.
  - Cluster (Rendah / Sedang / Tinggi).
- Filter berdasarkan cluster.
- Sorting dan pagination.
- Menampilkan ringkasan per cluster (jumlah anggota, rata-rata nilai kriteria).
- Export ke PDF (laporan cetak).
- Export ke Excel.

### 4.6. Kelola Pengguna

- Tambah pengguna (nama, username, password, role).
- Edit pengguna.
- Hapus pengguna (confirmation modal, tidak bisa hapus diri sendiri).
- Tabel pengguna dengan search dan pagination.

### 4.7. Profil & Pengaturan Akun

- Mengubah nama.
- Mengubah username.
- Mengubah password (wajib input password lama).

### 4.8. Autentikasi

- Login (username + password).
- Logout (invalidate session).
- Middleware proteksi halaman berdasarkan role.
- Redirect ke dashboard setelah login.

---

## 5. Kluster K-Means

| Cluster | Label | Keterangan |
|---------|-------|------------|
| Cluster 1 | **Produktivitas Rendah** | Kambing dengan skor produktivitas terendah |
| Cluster 2 | **Produktivitas Sedang** | Kambing dengan skor produktivitas menengah |
| Cluster 3 | **Produktivitas Tinggi** | Kambing dengan skor produktivitas tertinggi |

### Kriteria (Parameter Clustering)

| Kode | Kriteria | Satuan | Tipe Pencatatan |
|------|----------|--------|-----------------|
| C1 | Bobot Badan | kg | Per bulan |
| C2 | Tingkat Kelahiran | jumlah | Per pencatatan |
| C3 | Produksi Susu | Liter | Per hari |

### Inisialisasi Centroid

Menggunakan **metode persentil** dari rata-rata skor agregat:
- **Centroid Rendah:** data di persentil ke-15
- **Centroid Sedang:** data di persentil ke-50 (median)
- **Centroid Tinggi:** data di persentil ke-85

### Jarak

Menggunakan rumus **Euclidean Distance 3-Dimensi**:

```
d = √((C1a - C1b)² + (C2a - C2b)² + (C3a - C3b)²)
```

### Konvergensi

Iterasi berhenti ketika:
- Centroid tidak bergeser (konvergen), **atau**
- Mencapai batas maksimal iterasi (default: 100).

---

## 6. Rancangan Database

### Tabel `users`

| Kolom | Tipe Data | Keterangan |
|-------|-----------|------------|
| id | BIGINT UNSIGNED (PK) | Auto increment |
| name | VARCHAR(255) | Nama lengkap |
| username | VARCHAR(50) UNIQUE | Username untuk login |
| password | VARCHAR(255) | Password (bcrypt hash) |
| role | ENUM('admin', 'user') | Hak akses, default: 'user' |
| remember_token | VARCHAR(100) NULL | Token remember me |
| created_at | TIMESTAMP | Tanggal dibuat |
| updated_at | TIMESTAMP | Tanggal diupdate |

---

### Tabel `kambing`

| Kolom | Tipe Data | Keterangan |
|-------|-----------|------------|
| id | BIGINT UNSIGNED (PK) | Auto increment |
| kode_kambing | VARCHAR(50) UNIQUE | Kode kambing (K001, K002, ...) |
| jenis_kelamin | ENUM('Jantan', 'Betina') | Jenis kelamin |
| created_at | TIMESTAMP | Tanggal dibuat |
| updated_at | TIMESTAMP | Tanggal diupdate |

---

### Tabel `data_produktivitas`

| Kolom | Tipe Data | Keterangan |
|-------|-----------|------------|
| id | BIGINT UNSIGNED (PK) | Auto increment |
| kambing_id | BIGINT UNSIGNED (FK) | Relasi ke tabel kambing |
| tanggal_pencatatan | DATE | Tanggal pencatatan |
| bobot_badan | DECIMAL(5,2) | Berat badan dalam kg |
| tingkat_kelahiran | INT DEFAULT 0 | Jumlah kelahiran |
| produksi_susu | DECIMAL(5,2) DEFAULT 0 | Produksi susu dalam Liter |
| created_at | TIMESTAMP | Tanggal dibuat |
| updated_at | TIMESTAMP | Tanggal diupdate |

> **Catatan:** Satu kambing bisa memiliki banyak data produktivitas (pencatatan periodik). Bobot badan dicatat per bulan, produksi susu bisa per hari. Untuk kambing jantan, `tingkat_kelahiran` dan `produksi_susu` default 0.

---

### Tabel `sesi_clustering`

| Kolom | Tipe Data | Keterangan |
|-------|-----------|------------|
| id | BIGINT UNSIGNED (PK) | Auto increment |
| user_id | BIGINT UNSIGNED (FK) | Admin yang menjalankan proses |
| jumlah_cluster | INT DEFAULT 3 | Jumlah K (selalu 3) |
| total_iterasi | INT | Jumlah iterasi hingga konvergen |
| centroid_awal | JSON | Nilai centroid awal `{C1, C2, C3}` × 3 cluster |
| centroid_akhir | JSON | Nilai centroid akhir `{C1, C2, C3}` × 3 cluster |
| total_data | INT | Jumlah kambing yang diproses |
| created_at | TIMESTAMP | Tanggal proses dijalankan |
| updated_at | TIMESTAMP | Tanggal diupdate |

> **Catatan:** Setiap kali proses K-Means dijalankan, sesi baru dibuat. Hasil clustering sebelumnya tetap tersimpan sebagai riwayat.

---

### Tabel `hasil_clustering`

| Kolom | Tipe Data | Keterangan |
|-------|-----------|------------|
| id | BIGINT UNSIGNED (PK) | Auto increment |
| sesi_id | BIGINT UNSIGNED (FK) | Relasi ke tabel sesi_clustering |
| kambing_id | BIGINT UNSIGNED (FK) | Relasi ke tabel kambing |
| cluster | VARCHAR(20) | Rendah / Sedang / Tinggi |
| bobot_badan_val | DECIMAL(5,2) | Nilai bobot badan yang digunakan (agregat) |
| tingkat_kelahiran_val | INT | Nilai tingkat kelahiran yang digunakan (agregat) |
| produksi_susu_val | DECIMAL(5,2) | Nilai produksi susu yang digunakan (agregat) |
| jarak_c1 | DOUBLE | Jarak ke Centroid 1 (Rendah) |
| jarak_c2 | DOUBLE | Jarak ke Centroid 2 (Sedang) |
| jarak_c3 | DOUBLE | Jarak ke Centroid 3 (Tinggi) |
| created_at | TIMESTAMP | Tanggal dibuat |
| updated_at | TIMESTAMP | Tanggal diupdate |

> **Catatan:** Menyimpan jarak ke setiap centroid untuk transparansi perhitungan. Kolom `*_val` menyimpan nilai agregat yang dipakai saat proses clustering agar hasil bisa di-trace meskipun data produktivitas berubah di kemudian hari.

---

## 7. Relasi Database

```text
users
 └───< sesi_clustering
           └───< hasil_clustering >───┐
kambing ──────────────────────────────┘
 └───< data_produktivitas
```

### Kardinalitas

| Tabel | Relasi | Tabel |
|-------|--------|-------|
| users | 1 : N | sesi_clustering |
| sesi_clustering | 1 : N | hasil_clustering |
| kambing | 1 : N | hasil_clustering |
| kambing | 1 : N | data_produktivitas |

### Eloquent Relationships

| Model | Method | Tipe | Target |
|-------|--------|------|--------|
| User | sesiClustering() | hasMany | SesiClustering |
| Kambing | produktivitas() | hasMany | DataProduktivitas |
| Kambing | hasilClustering() | hasMany | HasilClustering |
| DataProduktivitas | kambing() | belongsTo | Kambing |
| SesiClustering | user() | belongsTo | User |
| SesiClustering | hasilClustering() | hasMany | HasilClustering |
| HasilClustering | sesi() | belongsTo | SesiClustering |
| HasilClustering | kambing() | belongsTo | Kambing |

---

## 8. Arsitektur Aplikasi

### Struktur Direktori Utama

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/
│   │   │   └── LoginController.php
│   │   ├── DashboardController.php
│   │   ├── KambingController.php
│   │   ├── ProduktivitasController.php
│   │   ├── ClusteringController.php      # Proses K-Means & Hasil
│   │   ├── UserController.php
│   │   └── ProfileController.php
│   └── Middleware/
│       └── RoleMiddleware.php            # Cek role admin/user
├── Imports/
│   └── KambingImport.php                 # Maatwebsite Excel import
├── Models/
│   ├── User.php
│   ├── Kambing.php
│   ├── DataProduktivitas.php
│   ├── SesiClustering.php
│   └── HasilClustering.php
├── Services/
│   └── KMeansService.php                 # Core algoritma K-Means
└── Exports/
    └── HasilClusteringExport.php         # Export Excel
```

### Route Groups

```
# Auth (Guest)
GET    /login                    → LoginController@showLoginForm
POST   /login                    → LoginController@login
POST   /logout                   → LoginController@logout

# Protected (Auth Required)
GET    /                         → redirect to /dashboard
GET    /dashboard                → DashboardController@index

# Data Kambing
GET    /kambing                  → KambingController@index
POST   /kambing                  → KambingController@store
PUT    /kambing/{id}             → KambingController@update
DELETE /kambing/{id}             → KambingController@destroy
POST   /kambing/import           → KambingController@import
POST   /kambing/destroy-bulk     → KambingController@destroyBulk

# Data Produktivitas
GET    /produktivitas            → ProduktivitasController@index
POST   /produktivitas            → ProduktivitasController@store
PUT    /produktivitas/{id}       → ProduktivitasController@update
DELETE /produktivitas/{id}       → ProduktivitasController@destroy
POST   /produktivitas/destroy-bulk → ProduktivitasController@destroyBulk

# K-Means Clustering (Admin Only)
GET    /clustering/proses        → ClusteringController@prosesForm
POST   /clustering/proses        → ClusteringController@proses
GET    /clustering/hasil         → ClusteringController@hasil
GET    /clustering/export-excel  → ClusteringController@exportExcel
GET    /clustering/export-pdf    → ClusteringController@exportPdf

# Kelola Pengguna (Admin Only)
GET    /user                     → UserController@index
POST   /user                     → UserController@store
PUT    /user/{id}                → UserController@update
DELETE /user/{id}                → UserController@destroy

# Profil
GET    /profile                  → ProfileController@edit
PUT    /profile                  → ProfileController@update
PUT    /profile/password         → ProfileController@updatePassword
```

---

## 9. Import Excel Mapping

Dari dataset `dataset kambing pawit.xlsx`:

| Kolom Excel | Kolom Database (kambing) | Kolom Database (data_produktivitas) |
|-------------|--------------------------|-------------------------------------|
| No | — (skip) | — |
| Kode Kambing | `kode_kambing` | — |
| Jenis Kelamin | `jenis_kelamin` | — |
| Bobot Badan (kg) | — | `bobot_badan` |
| Tingkat Kelahiran | — | `tingkat_kelahiran` |
| Produksi Susu (Liter) | — | `produksi_susu` |

**Logika Import:**
1. Baca setiap baris Excel.
2. Cek apakah `kode_kambing` sudah ada → update, belum ada → create.
3. Buat 1 record `data_produktivitas` per kambing dengan `tanggal_pencatatan` = tanggal import.

---

## 10. Style Guide

- **Framework CSS:** Tailwind CSS v4
- **Border Radius:** rounded-md
- **Font:** Poppins (Google Fonts)
  - Heading: font-semibold / font-bold, text-gray-800
  - Body: font-normal / font-medium, text-gray-700
  - Small/Label: font-medium, text-gray-500
- **Color Palette:**
  - Primary: `#FF8400` (orange)
  - Primary Hover: `#E67600`
  - Primary Light: `#FFF3E0` (background subtle)
  - Danger: `#EF4444` (red-500)
  - Warning: `#F59E0B` (yellow-500)
  - Info: `#3B82F6` (blue-500)
  - Success: `#10B981` (green-500)
  - Background: `#F9FAFB` (gray-50)
  - Sidebar: `#1F2937` (gray-800)
  - Card: `#FFFFFF` with shadow-sm
- **Cluster Colors:**
  - Rendah: `#EF4444` (red-500)
  - Sedang: `#F59E0B` (yellow-500)
  - Tinggi: `#10B981` (green-500)
- **UI Components:**
  - Confirmation modal sebelum delete.
  - Toast notification untuk success/error/warning.
  - Loading state saat proses clustering berjalan.
  - Empty state dengan ilustrasi saat data kosong.
  - Responsive sidebar navigation.
- **Prinsip:**
  - Modern, clean, dan profesional.
  - Teks harus readable di atas background color.
  - Konsisten spacing dan alignment.
  - Mobile responsive (sidebar collapse).

---

## 11. Package Dependencies

| Package | Kegunaan |
|---------|----------|
| `maatwebsite/excel` | Import & Export Excel |
| `barryvdh/laravel-dompdf` | Export PDF |
| `laravel/prompts` | CLI prompts (sudah built-in) |

---

## 12. Seeder Data

### UserSeeder

| Name | Username | Password | Role |
|------|----------|----------|------|
| Administrator | admin | admin123 | admin |
| Petugas | user | user123 | user |

### KambingSeeder + ProduktivitasSeeder

Import dari `dataset kambing pawit.xlsx` menggunakan `KambingImport`.