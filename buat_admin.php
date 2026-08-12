<?php
// =========================================================================
// JALANKAN FILE INI SATU KALI LEWAT BROWSER untuk membuat akun admin.
// Setelah berhasil, HAPUS file ini agar tidak disalahgunakan.
// Akses: http://localhost/perpustakaan/buat_admin.php
// =========================================================================
require_once __DIR__ . '/config/database.php';

$username = 'admin';
$password = 'admin123';
$nama     = 'Administrator';
$role     = 'admin';

$cek = $pdo->prepare("SELECT COUNT(*) AS jumlah FROM users WHERE username = ?");
$cek->execute([$username]);

if ((int) $cek->fetch()['jumlah'] > 0) {
    die("Akun admin sudah ada. Silakan hapus file buat_admin.php ini dan login lewat login.php.");
}

$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("INSERT INTO users (username, password, nama, role) VALUES (?, ?, ?, ?)");
$stmt->execute([$username, $hash, $nama, $role]);

echo "Akun admin berhasil dibuat!<br>";
echo "Username: <b>{$username}</b><br>";
echo "Password: <b>{$password}</b><br><br>";
echo "<b>PENTING:</b> Hapus file buat_admin.php ini sekarang, lalu login lewat login.php.";
