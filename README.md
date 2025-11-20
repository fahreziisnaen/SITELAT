# Aplikasi Rekap Keterlambatan Siswa

Aplikasi web berbasis Laravel untuk mengelola dan merekap data keterlambatan siswa di sekolah. Aplikasi ini dirancang untuk membantu sekolah dalam mencatat, memantau, dan melaporkan keterlambatan siswa secara sistematis dan terstruktur.

## 🚀 Fitur Utama

### 1. **Autentikasi & Manajemen User**
- Login menggunakan username dan password (bukan email)
- Sistem autentikasi berbasis Laravel Breeze yang telah dimodifikasi
- Role-based access control dengan 3 level:
  - **Admin**: Akses penuh ke semua fitur
  - **Walikelas**: Hanya dapat melihat dan mengelola kelas yang dipegangnya
  - **TATIB**: Akses khusus untuk data keterlambatan dan report

### 2. **Manajemen Data Master**

#### User Management
- CRUD lengkap untuk pengguna sistem
- Field: `username`, `password`, `nama_lengkap`, `role`
- Akses: Data Master → User Management

#### Data Kelas
- CRUD untuk data kelas
- Field: `kelas` (contoh: X-1, XI-2, XII-3), `username` (walikelas)
- Menampilkan jumlah murid per kelas
- Proteksi: Tidak dapat menghapus kelas yang masih memiliki murid
- Akses: Data Master → Data Kelas

#### Data Murid
- CRUD untuk data murid
- Field: `NIS`, `nama_lengkap`, `gender` (Laki-laki/Perempuan), `kelas`, `status` (Aktif/Lulus)
- **Import Bulk via CSV**: 
  - Upload file CSV untuk import data murid dalam jumlah besar
  - Format gender: `L` untuk Laki-laki, `P` untuk Perempuan
  - Download template CSV yang sudah disediakan
  - Validasi otomatis dan error reporting
- Akses: Data Master → Data Murid

### 3. **Data Keterlambatan**
- CRUD untuk pencatatan keterlambatan siswa
- Field:
  - **Nama Murid**: Searchbar dropdown dari tabel murid
  - **Gender**: Auto-fill berdasarkan murid yang dipilih
  - **Tanggal**: Date picker
  - **Waktu**: Time picker
  - **Keterangan**: Text area
  - **Bukti**: Upload gambar/foto (JPG, PNG, GIF, max 2MB)
- **Sistem Snapshot**: 
  - Data keterlambatan menyimpan snapshot lengkap (NIS, nama, gender, kelas, walikelas)
  - Data historis tidak berubah meskipun data master (murid, kelas, walikelas) diubah atau dihapus
  - Memastikan integritas data laporan historis
- Akses: Data Keterlambatan

### 4. **Report & Export**

#### Report Bulanan
- Filter berdasarkan:
  - Tahun Ajaran
  - Bulan
  - Kelas (opsional)
- Menampilkan detail keterlambatan per murid dalam periode yang dipilih
- Fitur print report

#### Report Semester
- Export langsung ke Excel (format .xlsx)
- Menampilkan data Semester 1 dan Semester 2 dalam satu file
- **Sheet Keterlambatan**:
  - Tahun Pelajaran, Kelas, WALIKELAS, NIS, Nama Murid, Gender
  - Detail tanggal keterlambatan per murid
  - Format tanggal: M/D/Y (contoh: 11/16/2025)
- **Sheet Data Murid** (Hidden):
  - Data murid aktif lengkap dengan tanggal keterlambatan
  - Disediakan 40 baris per kelas untuk data siswa
  - Format: NIS, Nama Lengkap, Gender, Tanggal Keterlambatan (comma-separated)
- Menggunakan template Excel yang sudah ada
- Optimasi performa dengan OpenSpout dan ZipArchive untuk handling file besar
- Akses: Report → Pilih "Laporan Semester"

### 5. **Naik Kelas**
- Fitur untuk memproses kenaikan kelas siswa
- Filter murid berdasarkan kelas dan status
- Proses batch update kelas siswa
- Akses: Naik Kelas

### 6. **Dashboard**
- Statistik ringkas berdasarkan role:
  - **Admin/TATIB**: Total users, kelas, murid, keterlambatan
  - **Walikelas**: Total kelas yang dipegang, murid, keterlambatan
- Menampilkan 5 keterlambatan terbaru
- Akses: Dashboard (setelah login)

## 📋 Struktur Menu Navigasi

```
Dashboard
├── Data Master (Dropdown)
│   ├── User Management
│   ├── Data Kelas
│   └── Data Murid
│       └── Import (Sub-menu)
├── Data Keterlambatan
├── Report
│   ├── Laporan Bulanan
│   └── Laporan Semester (Export Excel)
└── Naik Kelas
```

## 🛠️ Teknologi yang Digunakan

- **Framework**: Laravel 12
- **PHP**: 8.4+
- **Authentication**: Laravel Breeze v2
- **Frontend**: 
  - Blade Templates
  - Tailwind CSS v3
  - Alpine.js v3
- **Database**: MySQL/MariaDB
- **Excel Processing**: 
  - PhpOffice/PhpSpreadsheet v5.2
  - OpenSpout v4.32 (untuk performa optimal)
- **Testing**: Pest v4, PHPUnit v12
- **Code Quality**: Laravel Pint v1

## 📦 Instalasi

### Prasyarat
- PHP >= 8.4
- Composer
- MySQL/MariaDB
- Node.js & NPM

### Langkah Instalasi

1. **Clone repository atau extract project**
   ```bash
   git clone <repository-url>
   cd Rekap-Keterlambatan
   ```

2. **Install dependencies PHP**
   ```bash
   composer install
   ```

3. **Install dependencies JavaScript**
   ```bash
   npm install
   ```

4. **Copy file environment**
   ```bash
   copy .env.example .env
   # atau di Linux/Mac:
   cp .env.example .env
   ```

5. **Generate application key**
   ```bash
   php artisan key:generate
   ```

6. **Konfigurasi database di file `.env`**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=rekap_keterlambatan
   DB_USERNAME=root
   DB_PASSWORD=
   ```

7. **Buat database baru**
   ```sql
   CREATE DATABASE rekap_keterlambatan;
   ```

8. **Jalankan migrasi dan seeder**
   ```bash
   php artisan migrate:fresh --seed
   ```

9. **Buat symbolic link untuk storage**
   ```bash
   php artisan storage:link
   ```

10. **Compile assets**
    ```bash
    npm run build
    # atau untuk development:
    npm run dev
    ```

11. **Jalankan development server**
    ```bash
    php artisan serve
    ```

12. **Akses aplikasi di browser**
    ```
    http://localhost:8000
    ```

## 🔐 Login Credentials (Default)

### Admin
- Username: `admin`
- Password: `admin123`

### Walikelas (Sample)
- Username: `wali_x2`
- Password: `password`

> **Catatan**: Disarankan untuk mengubah password default setelah instalasi pertama.

## 📊 Struktur Database

### Tabel: `users`
- `username` (PK) - String, unique
- `password` - String, hashed
- `nama_lengkap` - String
- `role` - Enum: 'Admin', 'Walikelas', 'TATIB'

### Tabel: `kelas`
- `kelas` (PK) - String, unique (contoh: X-1, XI-2, XII-3)
- `username` (FK) - String, nullable, references `users.username`

### Tabel: `murid`
- `NIS` (PK) - String, unique
- `nama_lengkap` - String
- `gender` - Enum: 'Laki-laki', 'Perempuan'
- `kelas` (FK) - String, nullable, references `kelas.kelas`
- `status` - Enum: 'Aktif', 'Lulus'
- `tahun_lulus` - Integer, nullable

### Tabel: `keterlambatan`
- `id` (PK) - Big Integer, auto increment
- `NIS` - String, nullable (snapshot, tidak ada foreign key)
- `nama_murid` - String, nullable (snapshot)
- `gender` - String, nullable (snapshot)
- `kelas` - String, nullable (snapshot)
- `username` - String, nullable (snapshot walikelas)
- `tanggal` - Date
- `waktu` - Time
- `keterangan` - Text, nullable
- `bukti` - String, nullable (path file)
- `created_at` - Timestamp
- `updated_at` - Timestamp

> **Catatan Penting**: Tabel `keterlambatan` menggunakan sistem snapshot. Data NIS, nama_murid, gender, kelas, dan username disimpan sebagai snapshot historis dan tidak akan berubah meskipun data master diubah atau dihapus.

## 📝 Format Import CSV

### Template Format
```csv
NIS,Nama Lengkap,Gender,Kelas
1234567890,Andi Prasetyo,L,X-1
1234567891,Siti Nurhaliza,P,X-2
```

### Aturan Format
- **Header**: Harus ada dan sesuai: `NIS, Nama Lengkap, Gender, Kelas`
- **Gender**: 
  - `L` untuk Laki-laki
  - `P` untuk Perempuan
  - Format lama (`Laki-laki`/`Perempuan`) masih didukung
- **Kelas**: Harus sudah ada di sistem (contoh: X-1, XI-2, XII-3)
- **NIS**: Harus unik (tidak boleh duplikat)
- **Encoding**: UTF-8 dengan BOM (untuk kompatibilitas Excel)

## ⚙️ Konfigurasi Penting

### Storage
- **Upload Bukti**: File disimpan di `storage/app/public/bukti-keterlambatan/`
- **Template Excel**: File template harus ada di `storage/template/template-rekap.xlsx`
- **Export Temp**: File sementara export disimpan di `storage/app/temp/`

### Memory & Performance
- Untuk export Excel besar, pastikan `memory_limit` di `php.ini` minimal 1024M
- `max_execution_time` disarankan minimal 300 detik untuk export

### Validasi
- Username harus unik
- NIS harus unik
- Kelas harus unik
- File bukti maksimal 2MB
- File import CSV maksimal 10MB

## 🔍 Fitur Khusus

### Sistem Snapshot Data Keterlambatan
Aplikasi menggunakan sistem snapshot untuk memastikan integritas data historis:

- **Saat Input**: Data NIS, nama, gender, kelas, dan walikelas disimpan sebagai snapshot
- **Saat Update**: Snapshot hanya diupdate jika NIS berubah atau snapshot belum lengkap
- **Saat Delete**: Data master bisa dihapus, tapi snapshot keterlambatan tetap tersimpan
- **Manfaat**: 
  - Laporan historis tetap akurat
  - Data tidak hilang meskipun murid/kelas dihapus
  - Audit trail yang lengkap

### Optimasi Export Excel
- Menggunakan **OpenSpout** untuk membuat sheet baru dengan cepat
- Menggunakan **ZipArchive** untuk menggabungkan sheet ke template tanpa memuat semua data
- Tidak perlu memuat 30,000+ baris dari template, hanya copy file dan manipulasi XML
- Hasil: Export lebih cepat dan hemat memory

## 🐛 Troubleshooting

### Error: Storage link already exists
```bash
# Hapus folder public/storage terlebih dahulu
rm -rf public/storage  # Linux/Mac
# atau
rmdir /s public\storage  # Windows

# Kemudian jalankan lagi
php artisan storage:link
```

### Error: Class not found
```bash
composer dump-autoload
```

### Error: Mix manifest not found
```bash
npm run build
# atau
npm run dev
```

### Error: Memory limit exceeded saat export
1. Edit `php.ini`:
   ```ini
   memory_limit = 1024M
   max_execution_time = 300
   ```
2. Restart web server/PHP-FPM

### Error: Template Excel tidak ditemukan
Pastikan file `storage/template/template-rekap.xlsx` ada. Buat folder jika belum ada:
```bash
mkdir -p storage/template
# Kemudian copy template Excel ke folder tersebut
```

## 🚧 Pengembangan Lebih Lanjut

Fitur yang bisa ditambahkan di masa depan:
- [ ] Export report ke PDF
- [ ] Notifikasi email untuk keterlambatan berulang
- [ ] Dashboard dengan grafik statistik interaktif
- [ ] Fitur pencarian dan filter yang lebih advanced
- [ ] Export/Import data kelas
- [ ] Backup dan restore database
- [ ] Multi-sekolah support
- [ ] Mobile app companion

## 📄 Lisensi

Project ini dibuat untuk keperluan pembelajaran dan pengembangan sistem informasi sekolah.

## 👤 Developer

**Fahrezi Isnaen Fauzan**

## 👥 Kontribusi

Project ini dikembangkan menggunakan Laravel 12 dengan mengikuti best practices dan coding standards Laravel.

---

**Dibuat dengan ❤️ oleh Fahrezi Isnaen Fauzan menggunakan Laravel**
