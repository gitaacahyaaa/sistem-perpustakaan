<?php
require_once __DIR__ . '/../includes/auth.php';
$pageTitle = 'Denda';

$filter = $_GET['status'] ?? 'belum_lunas';

$sql = "SELECT p.*, a.nama AS nama_anggota, a.kode_anggota, b.judul
        FROM peminjaman p
        JOIN anggota a ON a.id = p.anggota_id
        JOIN buku b ON b.id = p.buku_id
        WHERE p.denda > 0";

if ($filter === 'belum_lunas') {
    $sql .= " AND p.status_denda = 'belum_lunas'";
} elseif ($filter === 'lunas') {
    $sql .= " AND p.status_denda = 'lunas'";
}
$sql .= " ORDER BY p.id DESC";

$daftarDenda = $pdo->query($sql)->fetchAll();
$totalBelumLunas = $pdo->query("SELECT COALESCE(SUM(denda),0) AS total FROM peminjaman WHERE status_denda = 'belum_lunas'")->fetch()['total'];

require __DIR__ . '/../includes/header.php';
?>

<div class="alert alert-warning d-flex justify-content-between align-items-center">
  <span><i class="bi bi-cash-coin"></i> Total denda belum lunas</span>
  <strong><?= formatRupiah($totalBelumLunas) ?></strong>
</div>

<div class="card shadow-sm">
  <div class="card-header bg-white">
    <div class="btn-group btn-group-sm" role="group">
      <a href="?status=belum_lunas" class="btn btn-outline-secondary <?= $filter === 'belum_lunas' ? 'active' : '' ?>">Belum Lunas</a>
      <a href="?status=lunas" class="btn btn-outline-secondary <?= $filter === 'lunas' ? 'active' : '' ?>">Lunas</a>
      <a href="?status=semua" class="btn btn-outline-secondary <?= $filter === 'semua' ? 'active' : '' ?>">Semua</a>
    </div>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0 align-middle">
        <thead>
          <tr><th>Kode Pinjam</th><th>Anggota</th><th>Buku</th><th>Tgl Kembali</th><th>Denda</th><th>Status</th><th style="width:110px;">Aksi</th></tr>
        </thead>
        <tbody>
          <?php if (empty($daftarDenda)): ?>
            <tr><td colspan="7" class="text-center text-muted py-4">Tidak ada data denda.</td></tr>
          <?php else: foreach ($daftarDenda as $d): ?>
          <tr>
            <td><?= htmlspecialchars($d['kode_pinjam']) ?></td>
            <td><?= htmlspecialchars($d['nama_anggota']) ?> <small class="text-muted"><?= htmlspecialchars($d['kode_anggota']) ?></small></td>
            <td><?= htmlspecialchars($d['judul']) ?></td>
            <td><?= formatTanggal($d['tanggal_kembali_aktual']) ?></td>
            <td><?= formatRupiah($d['denda']) ?></td>
            <td>
              <?php if ($d['status_denda'] === 'lunas'): ?>
                <span class="badge bg-success">Lunas</span>
              <?php else: ?>
                <span class="badge bg-danger">Belum Lunas</span>
              <?php endif; ?>
            </td>
            <td>
              <?php if ($d['status_denda'] === 'belum_lunas'): ?>
                <a href="bayar.php?id=<?= $d['id'] ?>" class="btn btn-sm btn-success" onclick="return confirm('Tandai denda ini sudah lunas?');">
                  <i class="bi bi-check-lg"></i> Lunas
                </a>
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
