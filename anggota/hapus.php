<?php
require_once __DIR__ . '/../includes/auth.php';

$id = (int) ($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM anggota WHERE id = ?");
$stmt->execute([$id]);
$anggota = $stmt->fetch();

if (!$anggota) {
    redirectDengan('index.php', 'danger', 'Anggota tidak ditemukan.');
}

$cek = $pdo->prepare("SELECT COUNT(*) AS jumlah FROM peminjaman WHERE anggota_id = ? AND status = 'dipinjam'");
$cek->execute([$id]);
if ((int) $cek->fetch()['jumlah'] > 0) {
    redirectDengan('index.php', 'danger', 'Anggota tidak bisa dihapus karena masih memiliki pinjaman aktif.');
}

$stmt = $pdo->prepare("DELETE FROM anggota WHERE id = ?");
$stmt->execute([$id]);

catatLog($pdo, $_SESSION['user_id'], 'Hapus Anggota', "Menghapus anggota \"{$anggota['nama']}\" ({$anggota['kode_anggota']})");
redirectDengan('index.php', 'success', "Anggota \"{$anggota['nama']}\" berhasil dihapus.");
