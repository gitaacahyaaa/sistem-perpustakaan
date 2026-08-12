-- =======================================================
-- Database: perpustakaan
-- Import file ini lewat phpMyAdmin, atau:
--   mysql -u root -p < perpustakaan.sql
-- =======================================================

CREATE DATABASE IF NOT EXISTS perpustakaan CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE perpustakaan;

-- ---------------------------------------------
-- Tabel users (login admin/petugas)
-- ---------------------------------------------
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama VARCHAR(100) NOT NULL,
    role ENUM('admin','petugas') NOT NULL DEFAULT 'petugas',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------
-- Tabel anggota
-- ---------------------------------------------
CREATE TABLE anggota (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_anggota VARCHAR(20) NOT NULL UNIQUE,
    nama VARCHAR(100) NOT NULL,
    alamat VARCHAR(255),
    no_hp VARCHAR(20),
    email VARCHAR(100),
    tanggal_daftar DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------
-- Tabel buku
-- ---------------------------------------------
CREATE TABLE buku (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_buku VARCHAR(20) NOT NULL UNIQUE,
    judul VARCHAR(200) NOT NULL,
    penulis VARCHAR(100),
    penerbit VARCHAR(100),
    tahun_terbit YEAR,
    kategori VARCHAR(50),
    stok INT NOT NULL DEFAULT 0,
    stok_tersedia INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------
-- Tabel peminjaman
-- ---------------------------------------------
CREATE TABLE peminjaman (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_pinjam VARCHAR(20) NOT NULL UNIQUE,
    anggota_id INT NOT NULL,
    buku_id INT NOT NULL,
    tanggal_pinjam DATE NOT NULL,
    tanggal_kembali_rencana DATE NOT NULL,
    tanggal_kembali_aktual DATE DEFAULT NULL,
    status ENUM('dipinjam','dikembalikan') NOT NULL DEFAULT 'dipinjam',
    denda INT NOT NULL DEFAULT 0,
    status_denda ENUM('-','belum_lunas','lunas') NOT NULL DEFAULT '-',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (anggota_id) REFERENCES anggota(id) ON DELETE CASCADE,
    FOREIGN KEY (buku_id) REFERENCES buku(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------
-- Tabel log_aktivitas
-- ---------------------------------------------
CREATE TABLE log_aktivitas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    aktivitas VARCHAR(100) NOT NULL,
    keterangan VARCHAR(255),
    waktu DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ---------------------------------------------
-- Data contoh (opsional, hapus jika tidak perlu)
-- ---------------------------------------------
INSERT INTO buku (kode_buku, judul, penulis, penerbit, tahun_terbit, kategori, stok, stok_tersedia) VALUES
('BK001', 'Laskar Pelangi', 'Andrea Hirata', 'Bentang Pustaka', 2005, 'Fiksi', 5, 5),
('BK002', 'Bumi Manusia', 'Pramoedya Ananta Toer', 'Hasta Mitra', 1980, 'Fiksi', 3, 3),
('BK003', 'Filosofi Teras', 'Henry Manampiring', 'Kompas', 2018, 'Non-Fiksi', 4, 4);

INSERT INTO anggota (kode_anggota, nama, alamat, no_hp, email, tanggal_daftar) VALUES
('AG001', 'Budi Santoso', 'Jl. Merdeka No. 1', '081234567890', 'budi@email.com', CURDATE()),
('AG002', 'Siti Aminah', 'Jl. Sudirman No. 5', '081298765432', 'siti@email.com', CURDATE());

-- Catatan: akun admin TIDAK dibuat di sini karena kolom password
-- harus di-hash oleh PHP (password_hash). Jalankan buat_admin.php
-- satu kali setelah import untuk membuat akun admin, lalu hapus file itu.
