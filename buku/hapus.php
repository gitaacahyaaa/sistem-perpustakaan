<?php
require_once __DIR__ . '/../includes/auth.php';

$id = (int) ($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM buku WHERE id = ?");
$stmt->execute([$id]);
$buku = $stmt->fetch();

if (!$buku) {
    redirectDengan('index.php', 'danger', 'Buku tidak ditemukan.');
}

// Cegah hapus buku yang masih sedang dipinjam
$cek = $pdo->prepare("SELECT COUNT(*) AS jumlah FROM peminjaman WHERE buku_id = ? AND status = 'dipinjam'");
$cek->execute([$id]);
if ((int) $cek->fetch()['jumlah'] > 0) {
    redirectDengan('index.php', 'danger', 'Buku tidak bisa dihapus karena masih ada yang dipinjam.');
}

$stmt = $pdo->prepare("DELETE FROM buku WHERE id = ?");
$stmt->execute([$id]);

catatLog($pdo, $_SESSION['user_id'], 'Hapus Buku', "Menghapus buku \"{$buku['judul']}\" ({$buku['kode_buku']})");
redirectDengan('index.php', 'success', "Buku \"{$buku['judul']}\" berhasil dihapus.");
