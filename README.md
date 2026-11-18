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

