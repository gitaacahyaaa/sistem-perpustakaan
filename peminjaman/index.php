<?php
require_once __DIR__ . '/../includes/auth.php';
$pageTitle = 'Peminjaman';

$filter = $_GET['status'] ?? 'semua';

$sql = "SELECT p.*, a.nama AS nama_anggota, a.kode_anggota, b.judul, b.kode_buku
        FROM peminjaman p
        JOIN anggota a ON a.id = p.anggota_id
        JOIN buku b ON b.id = p.buku_id";

if ($filter === 'dipinjam') {
    $sql .= " WHERE p.status = 'dipinjam'";
} elseif ($filter === 'terlambat') {
    $sql .= " WHERE p.status = 'dipinjam' AND p.tanggal_kembali_rencana < CURDATE()";
} elseif ($filter === 'dikembalikan') {
    $sql .= " WHERE p.status = 'dikembalikan'";
}
$sql .= " ORDER BY p.id DESC";

$daftarPeminjaman = $pdo->query($sql)->fetchAll();

require __DIR__ . '/../includes/header.php';
?>

<div class="card shadow-sm">
  <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div class="btn-group btn-group-sm" role="group">
      <a href="?status=semua" class="btn btn-outline-secondary <?= $filter === 'semua' ? 'active' : '' ?>">Semua</a>
      <a href="?status=dipinjam" class="btn btn-outline-secondary <?= $filter === 'dipinjam' ? 'active' : '' ?>">Dipinjam</a>
      <a href="?status=terlambat" class="btn btn-outline-secondary <?= $filter === 'terlambat' ? 'active' : '' ?>">Terlambat</a>
      <a href="?status=dikembalikan" class="btn btn-outline-secondary <?= $filter === 'dikembalikan' ? 'active' : '' ?>">Dikembalikan</a>
    </div>
    <a href="tambah.php" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Pinjam Buku</a>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0 align-middle">
        <thead>
          <tr>
            <th>Kode</th><th>Anggota</th><th>Buku</th><th>Tgl Pinjam</th>
            <th>Batas Kembali</th><th>Tgl Kembali</th><th>Status</th><th style="width:110px;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($daftarPeminjaman)): ?>
            <tr><td colspan="8" class="text-center text-muted py-4">Tidak ada data peminjaman.</td></tr>
          <?php else: foreach ($daftarPeminjaman as $p):
              $telat = $p['status'] === 'dipinjam' && strtotime($p['tanggal_kembali_rencana']) < strtotime(date('Y-m-d'));
          ?>
          <tr>
            <td><?= htmlspecialchars($p['kode_pinjam']) ?></td>
            <td><?= htmlspecialchars($p['nama_anggota']) ?> <small class="text-muted"><?= htmlspecialchars($p['kode_anggota']) ?></small></td>
            <td><?= htmlspecialchars($p['judul']) ?></td>
            <td><?= formatTanggal($p['tanggal_pinjam']) ?></td>
            <td class="<?= $telat ? 'text-danger fw-bold' : '' ?>"><?= formatTanggal($p['tanggal_kembali_rencana']) ?></td>
            <td><?= formatTanggal($p['tanggal_kembali_aktual']) ?></td>
            <td>
              <?php if ($p['status'] === 'dikembalikan'): ?>
                <span class="badge bg-success">Dikembalikan</span>
              <?php elseif ($telat): ?>
                <span class="badge bg-danger">Terlambat</span>
              <?php else: ?>
                <span class="badge bg-primary">Dipinjam</span>
              <?php endif; ?>
              <?php if ($p['denda'] > 0): ?>
                <br><small class="text-muted"><?= formatRupiah($p['denda']) ?> · <?= $p['status_denda'] === 'lunas' ? 'lunas' : 'belum lunas' ?></small>
              <?php endif; ?>
            </td>
            <td>
              <?php if ($p['status'] === 'dipinjam'): ?>
                <a href="kembalikan.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-success"><i class="bi bi-box-arrow-in-left"></i> Kembalikan</a>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
