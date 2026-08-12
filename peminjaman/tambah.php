<?php
require_once __DIR__ . '/../includes/auth.php';
$pageTitle = 'Pinjam Buku';

$daftarAnggota = $pdo->query("SELECT id, kode_anggota, nama FROM anggota ORDER BY nama")->fetchAll();
$daftarBuku    = $pdo->query("SELECT id, kode_buku, judul, stok_tersedia FROM buku WHERE stok_tersedia > 0 ORDER BY judul")->fetchAll();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $anggotaId = (int) ($_POST['anggota_id'] ?? 0);
    $bukuId    = (int) ($_POST['buku_id'] ?? 0);
    $tglPinjam = $_POST['tanggal_pinjam'] ?? date('Y-m-d');
    $tglKembali = $_POST['tanggal_kembali_rencana'] ?? date('Y-m-d', strtotime('+7 days'));

    // Pastikan buku masih tersedia (cegah race condition sederhana)
    $stmtBuku = $pdo->prepare("SELECT * FROM buku WHERE id = ? AND stok_tersedia > 0");
    $stmtBuku->execute([$bukuId]);
    $buku = $stmtBuku->fetch();

    $stmtAnggota = $pdo->prepare("SELECT * FROM anggota WHERE id = ?");
    $stmtAnggota->execute([$anggotaId]);
    $anggota = $stmtAnggota->fetch();

    if (!$buku) {
        $error = 'Buku tidak tersedia (stok habis atau tidak valid).';
    } elseif (!$anggota) {
        $error = 'Anggota tidak valid.';
    } elseif (strtotime($tglKembali) <= strtotime($tglPinjam)) {
        $error = 'Tanggal batas kembali harus setelah tanggal pinjam.';
    } else {
        $pdo->beginTransaction();
        try {
            $kode = generateKode($pdo, 'peminjaman', 'kode_pinjam', 'PJ');

            $stmt = $pdo->prepare(
                "INSERT INTO peminjaman (kode_pinjam, anggota_id, buku_id, tanggal_pinjam, tanggal_kembali_rencana, status)
                 VALUES (?, ?, ?, ?, ?, 'dipinjam')"
            );
            $stmt->execute([$kode, $anggotaId, $bukuId, $tglPinjam, $tglKembali]);

            $pdo->prepare("UPDATE buku SET stok_tersedia = stok_tersedia - 1 WHERE id = ?")->execute([$bukuId]);

            catatLog($pdo, $_SESSION['user_id'], 'Peminjaman', "{$anggota['nama']} meminjam \"{$buku['judul']}\" ($kode)");

            $pdo->commit();
            redirectDengan('index.php', 'success', "Peminjaman \"{$buku['judul']}\" oleh {$anggota['nama']} berhasil dicatat.");
        } catch (\Exception $e) {
            $pdo->rollBack();
            $error = 'Terjadi kesalahan, silakan coba lagi.';
        }
    }
}

require __DIR__ . '/../includes/header.php';
?>

<div class="card shadow-sm" style="max-width:600px;">
  <div class="card-body">
    <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>

    <?php if (empty($daftarBuku)): ?>
      <p class="text-muted">Tidak ada buku dengan stok tersedia saat ini.</p>
    <?php else: ?>
    <form method="POST">
      <div class="mb-3">
        <label class="form-label">Anggota</label>
        <select name="anggota_id" class="form-select" required>
          <option value="">-- Pilih Anggota --</option>
          <?php foreach ($daftarAnggota as $a): ?>
            <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['nama']) ?> (<?= htmlspecialchars($a['kode_anggota']) ?>)</option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="mb-3">
        <label class="form-label">Buku</label>
        <select name="buku_id" class="form-select" required>
          <option value="">-- Pilih Buku --</option>
          <?php foreach ($daftarBuku as $b): ?>
            <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['judul']) ?> — tersedia <?= $b['stok_tersedia'] ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">Tanggal Pinjam</label>
          <input type="date" name="tanggal_pinjam" class="form-control" value="<?= date('Y-m-d') ?>" required>
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Batas Kembali</label>
          <input type="date" name="tanggal_kembali_rencana" class="form-control" value="<?= date('Y-m-d', strtotime('+7 days')) ?>" required>
        </div>
      </div>
      <p class="text-muted small">Denda keterlambatan: <?= formatRupiah(TARIF_DENDA) ?> / hari, dihitung otomatis saat buku dikembalikan.</p>
      <button class="btn btn-primary"><i class="bi bi-save"></i> Simpan Peminjaman</button>
      <a href="index.php" class="btn btn-outline-secondary">Batal</a>
    </form>
    <?php endif; ?>
  </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
