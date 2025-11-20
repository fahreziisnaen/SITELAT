# Aplikasi Rekap Keterlambatan Siswa

Aplikasi web untuk mengelola dan merekap data keterlambatan siswa di sekolah.

## Fitur Utama

### 1. Login dengan Username dan Password
- Login menggunakan username dan password (bukan email)
- Sistem autentikasi Laravel Breeze yang telah dimodifikasi

### 2. User Management
- CRUD (Create, Read, Update, Delete) untuk users
- Field: username, password, nama_lengkap, role (Admin/Walikelas)
- Akses melalui menu: Data Master → User Management

### 3. Data Kelas
- CRUD untuk data kelas
- Field: kelas (contoh: X-1, XI-2, XII-3), Nama Walikelas (dari users dengan role Walikelas)
- Akses melalui menu: Data Master → Data Kelas

### 4. Data Murid
- CRUD untuk data murid
- Field: NIS, nama_lengkap, gender (Laki-laki/Perempuan), kelas (pilih dari tabel kelas)
- Akses melalui menu: Data Master → Data Murid

### 5. Data Keterlambatan
- CRUD untuk data keterlambatan
- Field:
  - Nama Murid: Searchbar dropdown dari tabel murid
  - Gender: Auto-fill berdasarkan murid yang dipilih
  - Tanggal: Date picker
  - Waktu: Time picker
  - Keterangan: Text area
  - Bukti: Upload gambar/foto (JPG, PNG, GIF, max 2MB)
- Akses melalui menu: Data Keterlambatan

### 6. Report Keterlambatan
- Summary/rekap keterlambatan per kelas
- Filter berdasarkan:
  - Kelas (dropdown)
  - Range tanggal (tanggal mulai - tanggal akhir)
- Menampilkan:
  - Daftar semua murid dalam kelas yang dipilih
  - Total keterlambatan per murid dalam periode yang dipilih
  - Total keseluruhan keterlambatan
- Fitur print report
- Akses melalui menu: Report

## Struktur Menu Navigasi

```
Dashboard
Data Master (Dropdown)
  ├── User Management
  ├── Data Kelas
  └── Data Murid
Data Keterlambatan
Report
```

## Instalasi

### Prasyarat
- PHP >= 8.2
- Composer
- MySQL/MariaDB
- Node.js & NPM

### Langkah Instalasi

1. Clone repository atau extract project

2. Install dependencies PHP:
```bash
composer install
```

3. Install dependencies JavaScript:
```bash
npm install
```

4. Copy file environment:
```bash
copy .env.example .env
```

5. Generate application key:
```bash
php artisan key:generate
```

6. Konfigurasi database di file `.env`:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=rekap_keterlambatan
DB_USERNAME=root
DB_PASSWORD=
```

7. Buat database baru dengan nama `rekap_keterlambatan`

8. Jalankan migrasi dan seeder:
```bash
php artisan migrate:fresh --seed
```

9. Buat symbolic link untuk storage:
```bash
php artisan storage:link
```

10. Compile assets:
```bash
npm run build
```

11. Jalankan development server:
```bash
php artisan serve
```

12. Akses aplikasi di browser: `http://localhost:8000`

## Login Credentials

### Admin
- Username: `admin`
- Password: `admin123`

### Walikelas (Sample)
- Username: `wali_x2`
- Password: `password`

## Struktur Database

### Tabel: users
- username (PK)
- password
- nama_lengkap
- role (Admin/Walikelas)

### Tabel: kelas
- kelas (PK)
- username (FK ke users)

### Tabel: murid
- NIS (PK)
- nama_lengkap
- gender (Laki-laki/Perempuan)
- kelas (FK ke kelas)

### Tabel: keterlambatan
- id (PK)
- NIS (FK ke murid)
- tanggal
- waktu
- keterangan
- bukti (path gambar)

## Teknologi yang Digunakan

- **Framework**: Laravel 11
- **Authentication**: Laravel Breeze
- **Frontend**: Blade Templates, Tailwind CSS, Alpine.js
- **Database**: MySQL/MariaDB

## Catatan Penting

1. **Upload Bukti**: File bukti keterlambatan disimpan di `storage/app/public/bukti-keterlambatan/`
2. **Validasi**: 
   - Username harus unik
   - NIS harus unik
   - Kelas harus unik
   - File bukti maksimal 2MB
3. **Relasi Database**:
   - Jika user dihapus, kelas yang terkait akan ikut terhapus (cascade)
   - Jika kelas dihapus, murid yang terkait akan ikut terhapus (cascade)
   - Jika murid dihapus, data keterlambatan akan ikut terhapus (cascade)

## Troubleshooting

### Error: Storage link already exists
Jika muncul error ini, hapus folder `public/storage` dan jalankan kembali:
```bash
php artisan storage:link
```

### Error: Class not found
Jalankan:
```bash
composer dump-autoload
```

### Error: Mix manifest not found
Jalankan:
```bash
npm run build
```

## Pengembangan Lebih Lanjut

Beberapa fitur yang bisa ditambahkan:
1. Export report ke PDF/Excel
2. Notifikasi email untuk keterlambatan berulang
3. Dashboard dengan grafik statistik
4. Role-based access control yang lebih detail
5. Fitur pencarian dan filter yang lebih advanced
6. Bulk upload data murid via Excel

## Lisensi

Project ini dibuat untuk keperluan pembelajaran dan pengembangan sistem informasi sekolah.