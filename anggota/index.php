<?php
require_once __DIR__ . '/../includes/auth.php';
$pageTitle = 'Data Anggota';

$keyword = trim($_GET['q'] ?? '');

if ($keyword !== '') {
    $stmt = $pdo->prepare("SELECT * FROM anggota WHERE nama LIKE ? OR kode_anggota LIKE ? ORDER BY id DESC");
    $like = "%$keyword%";
    $stmt->execute([$like, $like]);
} else {
    $stmt = $pdo->query("SELECT * FROM anggota ORDER BY id DESC");
}
$daftarAnggota = $stmt->fetchAll();

require __DIR__ . '/../includes/header.php';
?>

<div class="card shadow-sm">
  <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
    <form class="d-flex" method="GET" style="max-width:320px;">
      <input type="text" name="q" class="form-control form-control-sm me-2" placeholder="Cari nama / kode anggota..." value="<?= htmlspecialchars($keyword) ?>">
      <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-search"></i></button>
    </form>
    <a href="tambah.php" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Tambah Anggota</a>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0 align-middle">
        <thead>
          <tr><th>Kode</th><th>Nama</th><th>Alamat</th><th>No. HP</th><th>Email</th><th>Terdaftar</th><th style="width:120px;">Aksi</th></tr>
        </thead>
        <tbody>
          <?php if (empty($daftarAnggota)): ?>
            <tr><td colspan="7" class="text-center text-muted py-4">Belum ada data anggota.</td></tr>
          <?php else: foreach ($daftarAnggota as $a): ?>
          <tr>
            <td><?= htmlspecialchars($a['kode_anggota']) ?></td>
            <td><?= htmlspecialchars($a['nama']) ?></td>
            <td><?= htmlspecialchars($a['alamat']) ?></td>
            <td><?= htmlspecialchars($a['no_hp']) ?></td>
            <td><?= htmlspecialchars($a['email']) ?></td>
            <td><?= formatTanggal($a['tanggal_daftar']) ?></td>
            <td>
              <a href="edit.php?id=<?= $a['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
              <a href="hapus.php?id=<?= $a['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus anggota ini?');"><i class="bi bi-trash"></i></a>
            </td>
          </tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
