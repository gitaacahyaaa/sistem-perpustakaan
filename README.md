# Sistem Perpustakaan (PHP + MySQL)

Sistem manajemen perpustakaan sederhana dengan fitur CRUD data buku, data anggota,
peminjaman, pengembalian, denda otomatis, dan log aktivitas. Login admin, UI Bootstrap 5.

## Fitur
- **Login** admin/petugas (session-based)
- **Data Buku** — tambah, edit, hapus, cari, stok otomatis (total & tersedia)
- **Data Anggota** — tambah, edit, hapus, cari
- **Peminjaman** — pinjam buku (stok berkurang otomatis), filter status (dipinjam/terlambat/dikembalikan)
- **Pengembalian** — hitung denda keterlambatan otomatis (default Rp 2.000/hari), stok bertambah lagi
- **Denda** — daftar denda belum lunas/lunas, tandai lunas
- **Log Aktivitas** — mencatat semua aksi penting (tambah buku, pinjam, kembalikan, bayar denda, dll)

## Struktur folder
```
perpustakaan/
├── config/database.php     ← koneksi database 
├── includes/                ← header, sidebar, footer, auth guard, helper functions
├── sql/perpustakaan.sql     ← skema database + data contoh
├── buku/                    ← CRUD data buku
├── anggota/                 ← CRUD data anggota
├── peminjaman/               ← peminjaman & pengembalian
├── denda/                   ← daftar & pembayaran denda
├── laporan/log.php          ← log aktivitas
├── login.php / logout.php
├── buat_admin.php           ← jalankan sekali untuk buat akun admin, lalu HAPUS
└── index.php                ← dashboard
```

## Cara menjalankan (XAMPP / Laragon)

1. Copy folder `perpustakaan` ke `htdocs` (XAMPP) atau `www` (Laragon).
2. Buat database lewat phpMyAdmin, lalu import `sql/perpustakaan.sql`
   (ini otomatis membuat database `perpustakaan` beserta semua tabel + data contoh).
3. Cek `config/database.php` — sesuaikan `user`/`pass` MySQL kalau bukan default `root` / kosong.
4. Buka `http://localhost/perpustakaan/buat_admin.php` di browser **satu kali**
   untuk membuat akun admin pertama (username: `admin`, password: `admin123`).
   **Setelah berhasil, hapus file `buat_admin.php`.**
5. Buka `http://localhost/perpustakaan/login.php` dan login.

Kalau nama foldernya bukan `perpustakaan`, ubah nilai `BASE_URL` di `includes/init.php` dan `login.php`
supaya sesuai (contoh: `define('BASE_URL', '/nama-folder-kamu/');`).

## Catatan teknis
- Koneksi database pakai PDO + prepared statements (aman dari SQL Injection).
- Password admin di-hash dengan `password_hash()` (bcrypt), bukan disimpan plain text.
- Denda dihitung otomatis saat tombol "Kembalikan" ditekan: `(tanggal kembali aktual − batas kembali) × Rp 2.000`.
  Kalau tidak telat, denda = 0 dan buku langsung berstatus "dikembalikan".
- Tarif denda bisa diubah di `includes/functions.php`, konstanta `TARIF_DENDA`.
- Buku/anggota yang masih punya peminjaman aktif tidak bisa dihapus (dicegah di `hapus.php`).
