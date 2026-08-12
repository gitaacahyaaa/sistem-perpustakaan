<?php
require_once __DIR__ . '/../includes/auth.php';
$pageTitle = 'Data Buku';

$keyword = trim($_GET['q'] ?? '');

if ($keyword !== '') {
    $stmt = $pdo->prepare(
        "SELECT * FROM buku WHERE judul LIKE ? OR penulis LIKE ? OR kode_buku LIKE ? ORDER BY id DESC"
    );
    $like = "%$keyword%";
    $stmt->execute([$like, $like, $like]);
} else {
    $stmt = $pdo->query("SELECT * FROM buku ORDER BY id DESC");
}
$daftarBuku = $stmt->fetchAll();

require __DIR__ . '/../includes/header.php';
?>

<div class="card shadow-sm">
  <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
    <form class="d-flex" method="GET" style="max-width:320px;">
      <input type="text" name="q" class="form-control form-control-sm me-2" placeholder="Cari judul / penulis / kode..." value="<?= htmlspecialchars($keyword) ?>">
      <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-search"></i></button>
    </form>
    <a href="tambah.php" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Tambah Buku</a>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0 align-middle">
        <thead>
          <tr>
            <th>Kode</th><th>Judul</th><th>Penulis</th><th>Kategori</th>
            <th>Tahun</th><th>Stok</th><th>Tersedia</th><th style="width:120px;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($daftarBuku)): ?>
            <tr><td colspan="8" class="text-center text-muted py-4">Belum ada data buku.</td></tr>
          <?php else: foreach ($daftarBuku as $b): ?>
          <tr>
            <td><?= htmlspecialchars($b['kode_buku']) ?></td>
            <td><?= htmlspecialchars($b['judul']) ?></td>
            <td><?= htmlspecialchars($b['penulis']) ?></td>
            <td><span class="badge bg-secondary"><?= htmlspecialchars($b['kategori']) ?></span></td>
            <td><?= htmlspecialchars($b['tahun_terbit']) ?></td>
            <td><?= (int) $b['stok'] ?></td>
            <td>
              <?php if ($b['stok_tersedia'] > 0): ?>
                <span class="badge bg-success"><?= (int) $b['stok_tersedia'] ?></span>
              <?php else: ?>
                <span class="badge bg-danger">Habis</span>
              <?php endif; ?>
            </td>
            <td>
              <a href="edit.php?id=<?= $b['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
              <a href="hapus.php?id=<?= $b['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus buku ini?');"><i class="bi bi-trash"></i></a>
            </td>
          </tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
