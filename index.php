<?php
require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'Dashboard';

$totalBuku    = $pdo->query("SELECT COALESCE(SUM(stok),0) AS total FROM buku")->fetch()['total'];
$totalAnggota = $pdo->query("SELECT COUNT(*) AS total FROM anggota")->fetch()['total'];
$sedangDipinjam = $pdo->query("SELECT COUNT(*) AS total FROM peminjaman WHERE status = 'dipinjam'")->fetch()['total'];
$totalDenda   = $pdo->query("SELECT COALESCE(SUM(denda),0) AS total FROM peminjaman WHERE status_denda = 'belum_lunas'")->fetch()['total'];

$aktivitasTerbaru = $pdo->query(
    "SELECT l.*, u.nama AS nama_user
     FROM log_aktivitas l
     LEFT JOIN users u ON u.id = l.user_id
     ORDER BY l.waktu DESC
     LIMIT 8"
)->fetchAll();

$telatBelumKembali = $pdo->query(
    "SELECT p.*, a.nama AS nama_anggota, b.judul
     FROM peminjaman p
     JOIN anggota a ON a.id = p.anggota_id
     JOIN buku b ON b.id = p.buku_id
     WHERE p.status = 'dipinjam' AND p.tanggal_kembali_rencana < CURDATE()
     ORDER BY p.tanggal_kembali_rencana ASC
     LIMIT 5"
)->fetchAll();

require __DIR__ . '/includes/header.php';
?>

<div class="row g-3 mb-4">
  <div class="col-6 col-lg-3">
    <div class="card card-stat bg-buku p-3">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <div class="stat-number"><?= (int) $totalBuku ?></div>
          <div>Total Buku</div>
        </div>
        <i class="bi bi-journal-bookmark fs-1 opacity-50"></i>
      </div>
    </div>
  </div>
  <div class="col-6 col-lg-3">
    <div class="card card-stat bg-anggota p-3">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <div class="stat-number"><?= (int) $totalAnggota ?></div>
          <div>Total Anggota</div>
        </div>
        <i class="bi bi-people fs-1 opacity-50"></i>
      </div>
    </div>
  </div>
  <div class="col-6 col-lg-3">
    <div class="card card-stat bg-pinjam p-3">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <div class="stat-number"><?= (int) $sedangDipinjam ?></div>
          <div>Sedang Dipinjam</div>
        </div>
        <i class="bi bi-arrow-left-right fs-1 opacity-50"></i>
      </div>
    </div>
  </div>
  <div class="col-6 col-lg-3">
    <div class="card card-stat bg-denda p-3">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <div class="stat-number" style="font-size:20px;"><?= formatRupiah($totalDenda) ?></div>
          <div>Denda Belum Lunas</div>
        </div>
        <i class="bi bi-cash-coin fs-1 opacity-50"></i>
      </div>
    </div>
  </div>
</div>

<div class="row g-3">
  <div class="col-lg-6">
    <div class="card shadow-sm h-100">
      <div class="card-header bg-white"><i class="bi bi-exclamation-triangle text-danger"></i> Peminjaman Terlambat</div>
      <div class="card-body p-0">
        <?php if (empty($telatBelumKembali)): ?>
          <p class="text-muted p-3 mb-0">Tidak ada peminjaman yang terlambat. 🎉</p>
        <?php else: ?>
          <table class="table table-sm mb-0">
            <thead><tr><th>Anggota</th><th>Buku</th><th>Batas Kembali</th></tr></thead>
            <tbody>
              <?php foreach ($telatBelumKembali as $t): ?>
              <tr>
                <td><?= htmlspecialchars($t['nama_anggota']) ?></td>
                <td><?= htmlspecialchars($t['judul']) ?></td>
                <td class="text-danger"><?= formatTanggal($t['tanggal_kembali_rencana']) ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="col-lg-6">
    <div class="card shadow-sm h-100">
      <div class="card-header bg-white"><i class="bi bi-clock-history"></i> Aktivitas Terbaru</div>
      <div class="card-body p-0">
        <?php if (empty($aktivitasTerbaru)): ?>
          <p class="text-muted p-3 mb-0">Belum ada aktivitas.</p>
        <?php else: ?>
          <ul class="list-group list-group-flush">
            <?php foreach ($aktivitasTerbaru as $log): ?>
            <li class="list-group-item">
              <div class="d-flex justify-content-between">
                <strong><?= htmlspecialchars($log['aktivitas']) ?></strong>
                <small class="text-muted"><?= date('d/m/Y H:i', strtotime($log['waktu'])) ?></small>
              </div>
              <small class="text-muted"><?= htmlspecialchars($log['keterangan']) ?> — oleh <?= htmlspecialchars($log['nama_user'] ?? 'System') ?></small>
            </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
