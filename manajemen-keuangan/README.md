# 📊 Sistem Informasi Manajemen Keuangan Pribadi

Sistem manajemen keuangan pribadi berbasis web dengan fitur lengkap untuk mengelola pemasukan, pengeluaran, hutang, piutang, dan rekening bank.

## 🌟 Fitur Utama

### ✅ Multi-User System
- Registrasi dan login dengan password hashing
- Setiap user memiliki data terpisah dan aman
- Session management untuk keamanan

### 📈 Dashboard Interaktif
- 8 Card summary (pemasukan & pengeluaran: hari ini, bulan ini, tahun ini, total)
- 3 Grafik interaktif dengan Chart.js:
  - Bar Chart: Perbandingan pemasukan vs pengeluaran per bulan
  - Line Chart: Tren keuangan 12 bulan terakhir
  - Pie Chart: Komposisi pengeluaran per kategori
- Daftar transaksi terbaru

### 💰 Manajemen Transaksi
- Tambah, edit, hapus transaksi
- Filter berdasarkan tanggal, kategori, dan jenis
- Integrasi otomatis dengan saldo rekening bank
- DataTables untuk pencarian dan sorting

### 🏷️ Manajemen Kategori
- Kategori pemasukan dan pengeluaran
- CRUD lengkap dengan validasi foreign key

### 💳 Manajemen Rekening Bank
- Kelola multiple rekening bank
- Saldo diupdate otomatis berdasarkan transaksi
- Detail transaksi per rekening

### 📊 Hutang & Piutang
- Catat hutang dan piutang
- Track status pembayaran (Lunas/Belum Lunas)
- Jatuh tempo dan sisa pembayaran

### 📑 Laporan Keuangan
- Filter laporan: Harian, Mingguan, Bulanan, Tahunan
- Ringkasan dan detail transaksi
- Export ke PDF (via browser print)

### 🎨 UI Modern
- Desain profesional dengan warna pastel yang menarik
- Bootstrap 5 framework
- Responsive design
- Animasi smooth dan modern

## 🛠️ Teknologi yang Digunakan

- **Backend**: PHP 8+ dengan PDO
- **Frontend**: HTML5, CSS3, JavaScript ES6
- **UI Framework**: Bootstrap 5
- **Database**: MySQL
- **Charts**: Chart.js 4.x
- **Tables**: DataTables
- **Icons**: Font Awesome 6.x
- **Server**: XAMPP

## 📦 Instalasi

### 1. Persiapan

Pastikan XAMPP sudah terinstall dan running (Apache + MySQL).

### 2. Copy Project

Copy folder `manajemen-keuangan` ke dalam folder `htdocs` di XAMPP:
```
C:\xampp\htdocs\manajemen-keuangan\
```

### 3. Import Database

1. Buka phpMyAdmin: `http://localhost/phpmyadmin`
2. Klik tab "SQL"
3. Copy seluruh isi file `database.sql`
4. Paste dan klik "Go" / "Kirim"
5. Database `manajemen_keuangan` akan otomatis dibuat dengan semua tabel dan data sample

### 4. Konfigurasi Database (Opsional)

Jika menggunakan kredensial MySQL yang berbeda, edit file:
```
config/database.php
```

Ubah konstanta berikut:
```php
define('DB_HOST', 'localhost');  // Host database
define('DB_NAME', 'manajemen_keuangan');  // Nama database
define('DB_USER', 'root');  // Username MySQL
define('DB_PASS', '');  // Password MySQL
```

### 5. Akses Aplikasi

Buka browser dan akses:
```
http://localhost/manajemen-keuangan
```

## 👤 Data Login Sample

Setelah import database, gunakan kredensial berikut untuk testing:

**Username**: `admin`  
**Password**: `admin123`

> **Catatan**: Password di-hash menggunakan `password_hash()` PHP. Untuk production, ganti password ini!

## 📁 Struktur Folder

```
manajemen-keuangan/
├── assets/
│   ├── css/
│   │   └── style.css           # Custom CSS dengan warna pastel
│   └── js/
│       ├── main.js             # JavaScript utilities
│       └── dashboard.js        # Chart.js initialization
├── config/
│   ├── database.php            # Konfigurasi database
│   └── session.php             # Session management
├── includes/
│   ├── header.php              # HTML header
│   ├── sidebar.php             # Sidebar navigation
│   └── footer.php              # Footer dengan CDN scripts
├── pages/
│   ├── kategori/               # Module kategori
│   │   ├── index.php
│   │   ├── tambah.php
│   │   ├── edit.php
│   │   └── hapus.php
│   ├── transaksi/              # Module transaksi
│   │   ├── index.php
│   │   ├── tambah.php
│   │   ├── edit.php
│   │   └── hapus.php
│   ├── hutang/                 # Module hutang
│   │   ├── index.php
│   │   ├── tambah.php
│   │   ├── edit.php
│   │   └── hapus.php
│   ├── piutang/                # Module piutang
│   │   ├── tambah.php
│   │   ├── edit.php
│   │   └── hapus.php
│   ├── bank/                   # Module bank
│   │   ├── index.php
│   │   ├── tambah.php
│   │   ├── edit.php
│   │   ├── hapus.php
│   │   └── detail.php
│   ├── laporan/                # Module laporan
│   │   ├── index.php
│   │   └── export_pdf.php
│   └── profile/
│       └── change_password.php
├── database.sql                # Database schema & sample data
├── index.php                   # Redirect ke login
├── login.php                   # Halaman login
├── register.php                # Halaman registrasi
├── dashboard.php               # Dashboard utama
├── logout.php                  # Logout handler
└── README.md                   # Dokumentasi ini
```

## 🎨 Skema Warna Pastel

Sistem menggunakan palet warna pastel yang menarik:

- **Mint** (#B8F3D1): Untuk pemasukan
- **Coral** (#FFB5B5): Untuk pengeluaran
- **Lavender** (#C8B6E2): Untuk accent dan total
- **Peach** (#FFD5B5): Untuk highlight
- **Pink** (#FFC8DD): Untuk secondary
- **Blue** (#A8D8EA): Untuk info

## 🔐 Keamanan

- ✅ Password hashing dengan `password_hash()` dan `PASSWORD_DEFAULT`
- ✅ Prepared statements PDO untuk mencegah SQL Injection
- ✅ Session-based authentication
- ✅ Protected pages (redirect ke login jika belum login)
- ✅ User data isolation (setiap user hanya bisa akses data sendiri)
- ✅ XSS protection dengan `htmlspecialchars()`

## 💡 Tips Penggunaan

1. **Registrasi User Baru**: Setiap user baru otomatis mendapat kategori default dan akun Cash
2. **Tambah Transaksi**: Pilih bank untuk otomatis update saldo, atau kosongkan jika cash
3. **Edit Transaksi**: Saldo bank akan di-rollback dan di-update ulang sesuai perubahan
4. **Hapus Kategori**: Tidak bisa dihapus jika masih digunakan dalam transaksi
5. **Laporan PDF**: Klik "Export PDF" lalu gunakan CTRL+P atau klik tombol Print
6. **Detail Bank**: Klik "Detail" di card rekening untuk melihat semua transaksi bank tersebut

## 🐛 Troubleshooting

### Error Database Connection
- Pastikan MySQL di XAMPP sudah running
- Cek kredensial di `config/database.php`
- Pastikan database sudah di-import

### Style Tidak Muncul
- Cek path CSS di `includes/header.php`
- Clear browser cache (CTRL+F5)
- Pastikan folder `assets/css/` ada

### Chart Tidak Tampil
- Pastikan internet tersambung (Chart.js dari CDN)
- Cek console browser (F12) untuk error JavaScript
- Pastikan ada data transaksi

## 📝 Catatan Developer

- Gunakan XAMPP PHP versi 8.0 atau lebih baru
- Untuk production, aktifkan HTTPS dan ganti default password
- Database menggunakan `utf8mb4_unicode_ci` untuk support emoji dan karakter khusus
- Semua timestamp menggunakan `CURRENT_TIMESTAMP` MySQL

## 📧 Support

Jika ada pertanyaan atau butuh bantuan, silakan hubungi developer.

---

**Developed with ❤️ using PHP, MySQL, Bootstrap 5, and Chart.js**

**Versi**: 1.0.0  
**Tanggal Rilis**: Desember 2025
