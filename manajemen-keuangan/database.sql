-- ============================================
-- Database: Sistem Manajemen Keuangan Pribadi
-- ============================================

-- Buat database
CREATE DATABASE IF NOT EXISTS manajemen_keuangan;
USE manajemen_keuangan;

-- ============================================
-- Tabel: users
-- ============================================
DROP TABLE IF EXISTS users;
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    nama_lengkap VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    foto_profil VARCHAR(255) DEFAULT 'default-avatar.png',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabel: tbl_kategori
-- ============================================
DROP TABLE IF EXISTS tbl_kategori;
CREATE TABLE tbl_kategori (
    kategori_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    nama_kategori VARCHAR(100) NOT NULL,
    jenis_kategori ENUM('Pemasukan', 'Pengeluaran') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_user_jenis (user_id, jenis_kategori)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabel: tbl_bank
-- ============================================
DROP TABLE IF EXISTS tbl_bank;
CREATE TABLE tbl_bank (
    bank_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    nama_bank VARCHAR(100) NOT NULL,
    nomor_rekening VARCHAR(50),
    saldo_awal DECIMAL(15,2) DEFAULT 0.00,
    saldo_saat_ini DECIMAL(15,2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabel: tbl_transaksi
-- ============================================
DROP TABLE IF EXISTS tbl_transaksi;
CREATE TABLE tbl_transaksi (
    transaksi_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    tanggal DATE NOT NULL,
    jenis_transaksi ENUM('Pemasukan', 'Pengeluaran') NOT NULL,
    kategori_id INT NOT NULL,
    bank_id INT NULL,
    jumlah DECIMAL(15,2) NOT NULL,
    keterangan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (kategori_id) REFERENCES tbl_kategori(kategori_id) ON DELETE RESTRICT,
    FOREIGN KEY (bank_id) REFERENCES tbl_bank(bank_id) ON DELETE SET NULL,
    INDEX idx_user_tanggal (user_id, tanggal),
    INDEX idx_user_jenis (user_id, jenis_transaksi),
    INDEX idx_kategori (kategori_id),
    INDEX idx_bank (bank_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabel: tbl_hutang
-- ============================================
DROP TABLE IF EXISTS tbl_hutang;
CREATE TABLE tbl_hutang (
    hutang_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    nama_pemberi_hutang VARCHAR(100) NOT NULL,
    jumlah_hutang DECIMAL(15,2) NOT NULL,
    sisa_hutang DECIMAL(15,2) NOT NULL,
    tanggal_hutang DATE NOT NULL,
    jatuh_tempo DATE,
    status ENUM('Belum Lunas', 'Lunas') DEFAULT 'Belum Lunas',
    keterangan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_user_status (user_id, status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabel: tbl_piutang
-- ============================================
DROP TABLE IF EXISTS tbl_piutang;
CREATE TABLE tbl_piutang (
    piutang_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    nama_peminjam VARCHAR(100) NOT NULL,
    jumlah_piutang DECIMAL(15,2) NOT NULL,
    sisa_piutang DECIMAL(15,2) NOT NULL,
    tanggal_piutang DATE NOT NULL,
    jatuh_tempo DATE,
    status ENUM('Belum Lunas', 'Lunas') DEFAULT 'Belum Lunas',
    keterangan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_user_status (user_id, status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Data Sample untuk Testing
-- ============================================

-- Sample User (password: admin123)
INSERT INTO users (nama_lengkap, username, email, password) VALUES
('Administrator', 'admin', 'admin@example.com', '$2y$10$YzJ5NzE2ZjE2ZjE2ZjE2ZeXxXxXxXxXxXxXxXxXxXxXxXxXxXxXx');

-- Sample Kategori Default (akan di-copy untuk setiap user baru)
-- User bisa tambah/edit sendiri
INSERT INTO tbl_kategori (user_id, nama_kategori, jenis_kategori) VALUES
(1, 'Gaji', 'Pemasukan'),
(1, 'Bonus', 'Pemasukan'),
(1, 'Investasi', 'Pemasukan'),
(1, 'Lain-lain', 'Pemasukan'),
(1, 'Makanan & Minuman', 'Pengeluaran'),
(1, 'Transportasi', 'Pengeluaran'),
(1, 'Belanja', 'Pengeluaran'),
(1, 'Tagihan', 'Pengeluaran'),
(1, 'Hiburan', 'Pengeluaran'),
(1, 'Kesehatan', 'Pengeluaran'),
(1, 'Pendidikan', 'Pengeluaran'),
(1, 'Lain-lain', 'Pengeluaran');

-- Sample Bank Account
INSERT INTO tbl_bank (user_id, nama_bank, nomor_rekening, saldo_awal, saldo_saat_ini) VALUES
(1, 'Cash', '-', 0.00, 0.00),
(1, 'Bank Mandiri', '1234567890', 5000000.00, 5000000.00),
(1, 'BCA', '0987654321', 3000000.00, 3000000.00);

-- Sample Transaksi
INSERT INTO tbl_transaksi (user_id, tanggal, jenis_transaksi, kategori_id, bank_id, jumlah, keterangan) VALUES
(1, '2025-12-01', 'Pemasukan', 1, 2, 8000000.00, 'Gaji Bulan Desember'),
(1, '2025-12-02', 'Pengeluaran', 5, 1, 150000.00, 'Makan siang'),
(1, '2025-12-03', 'Pengeluaran', 6, 2, 100000.00, 'Bensin motor'),
(1, '2025-12-05', 'Pengeluaran', 8, 3, 500000.00, 'Bayar listrik');

-- Update saldo bank berdasarkan transaksi
UPDATE tbl_bank SET saldo_saat_ini = saldo_awal + 8000000.00 WHERE bank_id = 2;
UPDATE tbl_bank SET saldo_saat_ini = saldo_awal - 150000.00 WHERE bank_id = 1;
UPDATE tbl_bank SET saldo_saat_ini = saldo_awal - 100000.00 WHERE bank_id = 2;
UPDATE tbl_bank SET saldo_saat_ini = saldo_saat_ini - 500000.00 WHERE bank_id = 3;

-- Sample Hutang
INSERT INTO tbl_hutang (user_id, nama_pemberi_hutang, jumlah_hutang, sisa_hutang, tanggal_hutang, jatuh_tempo, status, keterangan) VALUES
(1, 'Bank BRI', 10000000.00, 7000000.00, '2025-06-01', '2026-06-01', 'Belum Lunas', 'Kredit kendaraan'),
(1, 'Teman A', 2000000.00, 0.00, '2025-11-01', '2025-12-01', 'Lunas', 'Pinjaman pribadi');

-- Sample Piutang
INSERT INTO tbl_piutang (user_id, nama_peminjam, jumlah_piutang, sisa_piutang, tanggal_piutang, jatuh_tempo, status, keterangan) VALUES
(1, 'Teman B', 1500000.00, 1000000.00, '2025-11-15', '2025-12-15', 'Belum Lunas', 'Pinjaman untuk usaha'),
(1, 'Saudara C', 500000.00, 0.00, '2025-10-01', '2025-11-01', 'Lunas', 'Pinjaman darurat');
