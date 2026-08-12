<?php
require_once __DIR__ . '/../includes/auth.php';

$id = (int) ($_GET['id'] ?? 0);
$stmt = $pdo->prepare(
    "SELECT p.*, a.nama AS nama_anggota, b.judul
     FROM peminjaman p
     JOIN anggota a ON a.id = p.anggota_id
     JOIN buku b ON b.id = p.buku_id
     WHERE p.id = ?"
);
$stmt->execute([$id]);
$pinjam = $stmt->fetch();

if (!$pinjam || $pinjam['status_denda'] !== 'belum_lunas') {
    redirectDengan('index.php', 'danger', 'Data denda tidak ditemukan atau sudah lunas.');
}

$pdo->prepare("UPDATE peminjaman SET status_denda = 'lunas' WHERE id = ?")->execute([$id]);

catatLog(
    $pdo, $_SESSION['user_id'], 'Pembayaran Denda',
    "{$pinjam['nama_anggota']} melunasi denda " . formatRupiah($pinjam['denda']) . " untuk \"{$pinjam['judul']}\" ({$pinjam['kode_pinjam']})"
);

redirectDengan('index.php', 'success', "Denda {$pinjam['nama_anggota']} berhasil ditandai lunas.");
