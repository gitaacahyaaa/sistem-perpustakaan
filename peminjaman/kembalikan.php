<?php
require_once __DIR__ . '/../includes/auth.php';
$pageTitle = 'Pengembalian Buku';

$id = (int) ($_GET['id'] ?? 0);
$stmt = $pdo->prepare(
    "SELECT p.*, a.nama AS nama_anggota, b.judul, b.id AS buku_id
     FROM peminjaman p
     JOIN anggota a ON a.id = p.anggota_id
     JOIN buku b ON b.id = p.buku_id
     WHERE p.id = ?"
);
$stmt->execute([$id]);
$pinjam = $stmt->fetch();

if (!$pinjam || $pinjam['status'] !== 'dipinjam') {
    redirectDengan('index.php', 'danger', 'Data peminjaman tidak ditemukan atau sudah dikembalikan.');
}

$tglKembaliAktual = date('Y-m-d');
$hariTelat = max(0, (strtotime($tglKembaliAktual) - strtotime($pinjam['tanggal_kembali_rencana'])) / 86400);
$hariTelat = (int) $hariTelat;
$denda = $hariTelat * TARIF_DENDA;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo->beginTransaction();
    try {
        $statusDenda = $denda > 0 ? 'belum_lunas' : '-';

        $stmt = $pdo->prepare(
            "UPDATE peminjaman SET tanggal_kembali_aktual = ?, status = 'dikembalikan', denda = ?, status_denda = ? WHERE id = ?"
        );
        $stmt->execute([$tglKembaliAktual, $denda, $statusDenda, $id]);

        $pdo->prepare("UPDATE buku SET stok_tersedia = stok_tersedia + 1 WHERE id = ?")->execute([$pinjam['buku_id']]);

        $ket = "{$pinjam['nama_anggota']} mengembalikan \"{$pinjam['judul']}\" ({$pinjam['kode_pinjam']})";
        if ($denda > 0) {
            $ket .= " — telat {$hariTelat} hari, denda " . formatRupiah($denda);
        }
        catatLog($pdo, $_SESSION['user_id'], 'Pengembalian', $ket);

        $pdo->commit();
        redirectDengan('index.php', 'success', "Pengembalian berhasil dicatat." . ($denda > 0 ? " Denda: " . formatRupiah($denda) . "." : ""));
    } catch (\Exception $e) {
        $pdo->rollBack();
        redirectDengan('index.php', 'danger', 'Terjadi kesalahan, silakan coba lagi.');
    }
}

require __DIR__ . '/../includes/header.php';
?>

<div class="card shadow-sm" style="max-width:560px;">
  <div class="card-body">
    <dl class="row mb-0">
      <dt class="col-sm-4">Kode Pinjam</dt><dd class="col-sm-8"><?= htmlspecialchars($pinjam['kode_pinjam']) ?></dd>
      <dt class="col-sm-4">Anggota</dt><dd class="col-sm-8"><?= htmlspecialchars($pinjam['nama_anggota']) ?></dd>
      <dt class="col-sm-4">Buku</dt><dd class="col-sm-8"><?= htmlspecialchars($pinjam['judul']) ?></dd>
      <dt class="col-sm-4">Tanggal Pinjam</dt><dd class="col-sm-8"><?= formatTanggal($pinjam['tanggal_pinjam']) ?></dd>
      <dt class="col-sm-4">Batas Kembali</dt><dd class="col-sm-8"><?= formatTanggal($pinjam['tanggal_kembali_rencana']) ?></dd>
      <dt class="col-sm-4">Tanggal Kembali</dt><dd class="col-sm-8"><?= formatTanggal($tglKembaliAktual) ?> (hari ini)</dd>
    </dl>
    <hr>
    <?php if ($hariTelat > 0): ?>
      <div class="alert alert-warning">
        <i class="bi bi-exclamation-triangle"></i>
        Terlambat <strong><?= $hariTelat ?> hari</strong> ×  <?= formatRupiah(TARIF_DENDA) ?>
        = <strong><?= formatRupiah($denda) ?></strong>
      </div>
    <?php else: ?>
      <div class="alert alert-success"><i class="bi bi-check-circle"></i> Tidak ada keterlambatan, tidak ada denda.</div>
    <?php endif; ?>

    <form method="POST">
      <button class="btn btn-success"><i class="bi bi-box-arrow-in-left"></i> Konfirmasi Pengembalian</button>
      <a href="index.php" class="btn btn-outline-secondary">Batal</a>
    </form>
  </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
