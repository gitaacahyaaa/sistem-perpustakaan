<?php
require_once __DIR__ . '/../includes/auth.php';
$pageTitle = 'Edit Buku';

$id = (int) ($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM buku WHERE id = ?");
$stmt->execute([$id]);
$buku = $stmt->fetch();

if (!$buku) {
    redirectDengan('index.php', 'danger', 'Buku tidak ditemukan.');
}

// Hitung berapa eksemplar buku ini yang sedang dipinjam,
// supaya stok tersedia tetap konsisten saat stok total diubah.
$stmtPinjam = $pdo->prepare("SELECT COUNT(*) AS jumlah FROM peminjaman WHERE buku_id = ? AND status = 'dipinjam'");
$stmtPinjam->execute([$id]);
$sedangDipinjam = (int) $stmtPinjam->fetch()['jumlah'];

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul    = trim($_POST['judul'] ?? '');
    $penulis  = trim($_POST['penulis'] ?? '');
    $penerbit = trim($_POST['penerbit'] ?? '');
    $tahun    = trim($_POST['tahun_terbit'] ?? '');
    $kategori = trim($_POST['kategori'] ?? '');
    $stok     = (int) ($_POST['stok'] ?? 0);

    if ($judul === '' || $stok < $sedangDipinjam) {
        $error = "Judul wajib diisi dan stok tidak boleh kurang dari jumlah yang sedang dipinjam ({$sedangDipinjam}).";
    } else {
        $stokTersedia = $stok - $sedangDipinjam;
        $stmt = $pdo->prepare(
            "UPDATE buku SET judul=?, penulis=?, penerbit=?, tahun_terbit=?, kategori=?, stok=?, stok_tersedia=? WHERE id=?"
        );
        $stmt->execute([$judul, $penulis, $penerbit, $tahun ?: null, $kategori, $stok, $stokTersedia, $id]);

        catatLog($pdo, $_SESSION['user_id'], 'Edit Buku', "Mengubah data buku \"$judul\" ({$buku['kode_buku']})");
        redirectDengan('index.php', 'success', "Buku \"$judul\" berhasil diperbarui.");
    }
}

require __DIR__ . '/../includes/header.php';
?>

<div class="card shadow-sm" style="max-width:600px;">
  <div class="card-body">
    <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <p class="text-muted">Kode buku: <strong><?= htmlspecialchars($buku['kode_buku']) ?></strong>
      <?php if ($sedangDipinjam > 0): ?>
        <span class="badge bg-warning text-dark ms-2"><?= $sedangDipinjam ?> eksemplar sedang dipinjam</span>
      <?php endif; ?>
    </p>
    <form method="POST">
      <div class="mb-3">
        <label class="form-label">Judul Buku</label>
        <input type="text" name="judul" class="form-control" required value="<?= htmlspecialchars($_POST['judul'] ?? $buku['judul']) ?>">
      </div>
      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">Penulis</label>
          <input type="text" name="penulis" class="form-control" value="<?= htmlspecialchars($_POST['penulis'] ?? $buku['penulis']) ?>">
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Penerbit</label>
          <input type="text" name="penerbit" class="form-control" value="<?= htmlspecialchars($_POST['penerbit'] ?? $buku['penerbit']) ?>">
        </div>
      </div>
      <div class="row">
        <div class="col-md-4 mb-3">
          <label class="form-label">Tahun Terbit</label>
          <input type="number" name="tahun_terbit" class="form-control" min="1900" max="<?= date('Y') ?>" value="<?= htmlspecialchars($_POST['tahun_terbit'] ?? $buku['tahun_terbit']) ?>">
        </div>
        <div class="col-md-4 mb-3">
          <label class="form-label">Kategori</label>
          <input type="text" name="kategori" class="form-control" value="<?= htmlspecialchars($_POST['kategori'] ?? $buku['kategori']) ?>">
        </div>
        <div class="col-md-4 mb-3">
          <label class="form-label">Jumlah Stok</label>
          <input type="number" name="stok" class="form-control" min="<?= $sedangDipinjam ?>" required value="<?= htmlspecialchars($_POST['stok'] ?? $buku['stok']) ?>">
        </div>
      </div>
      <button class="btn btn-primary"><i class="bi bi-save"></i> Simpan Perubahan</button>
      <a href="index.php" class="btn btn-outline-secondary">Batal</a>
    </form>
  </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
