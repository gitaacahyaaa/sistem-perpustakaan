<?php
require_once __DIR__ . '/../includes/auth.php';
$pageTitle = 'Tambah Buku';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul    = trim($_POST['judul'] ?? '');
    $penulis  = trim($_POST['penulis'] ?? '');
    $penerbit = trim($_POST['penerbit'] ?? '');
    $tahun    = trim($_POST['tahun_terbit'] ?? '');
    $kategori = trim($_POST['kategori'] ?? '');
    $stok     = (int) ($_POST['stok'] ?? 0);

    if ($judul === '' || $stok < 0) {
        $error = 'Judul wajib diisi dan stok tidak boleh negatif.';
    } else {
        $kode = generateKode($pdo, 'buku', 'kode_buku', 'BK');
        $stmt = $pdo->prepare(
            "INSERT INTO buku (kode_buku, judul, penulis, penerbit, tahun_terbit, kategori, stok, stok_tersedia)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([$kode, $judul, $penulis, $penerbit, $tahun ?: null, $kategori, $stok, $stok]);

        catatLog($pdo, $_SESSION['user_id'], 'Tambah Buku', "Menambahkan buku \"$judul\" ($kode)");
        redirectDengan('index.php', 'success', "Buku \"$judul\" berhasil ditambahkan.");
    }
}

require __DIR__ . '/../includes/header.php';
?>

<div class="card shadow-sm" style="max-width:600px;">
  <div class="card-body">
    <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <form method="POST">
      <div class="mb-3">
        <label class="form-label">Judul Buku</label>
        <input type="text" name="judul" class="form-control" required value="<?= htmlspecialchars($_POST['judul'] ?? '') ?>">
      </div>
      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">Penulis</label>
          <input type="text" name="penulis" class="form-control" value="<?= htmlspecialchars($_POST['penulis'] ?? '') ?>">
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Penerbit</label>
          <input type="text" name="penerbit" class="form-control" value="<?= htmlspecialchars($_POST['penerbit'] ?? '') ?>">
        </div>
      </div>
      <div class="row">
        <div class="col-md-4 mb-3">
          <label class="form-label">Tahun Terbit</label>
          <input type="number" name="tahun_terbit" class="form-control" min="1900" max="<?= date('Y') ?>" value="<?= htmlspecialchars($_POST['tahun_terbit'] ?? '') ?>">
        </div>
        <div class="col-md-4 mb-3">
          <label class="form-label">Kategori</label>
          <input type="text" name="kategori" class="form-control" placeholder="Fiksi, Non-Fiksi, dll" value="<?= htmlspecialchars($_POST['kategori'] ?? '') ?>">
        </div>
        <div class="col-md-4 mb-3">
          <label class="form-label">Jumlah Stok</label>
          <input type="number" name="stok" class="form-control" min="0" required value="<?= htmlspecialchars($_POST['stok'] ?? '1') ?>">
        </div>
      </div>
      <button class="btn btn-primary"><i class="bi bi-save"></i> Simpan</button>
      <a href="index.php" class="btn btn-outline-secondary">Batal</a>
    </form>
  </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
