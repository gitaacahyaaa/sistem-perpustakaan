<?php
require_once __DIR__ . '/../includes/auth.php';
$pageTitle = 'Log Aktivitas';

$daftarLog = $pdo->query(
    "SELECT l.*, u.nama AS nama_user
     FROM log_aktivitas l
     LEFT JOIN users u ON u.id = l.user_id
     ORDER BY l.waktu DESC
     LIMIT 200"
)->fetchAll();

require __DIR__ . '/../includes/header.php';
?>

<div class="card shadow-sm">
  <div class="card-header bg-white">Riwayat aktivitas sistem (200 terbaru)</div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0 align-middle">
        <thead>
          <tr><th style="width:160px;">Waktu</th><th>Aktivitas</th><th>Keterangan</th><th>Oleh</th></tr>
        </thead>
        <tbody>
          <?php if (empty($daftarLog)): ?>
            <tr><td colspan="4" class="text-center text-muted py-4">Belum ada aktivitas.</td></tr>
          <?php else: foreach ($daftarLog as $log): ?>
          <tr>
            <td><?= date('d/m/Y H:i', strtotime($log['waktu'])) ?></td>
            <td><span class="badge bg-secondary"><?= htmlspecialchars($log['aktivitas']) ?></span></td>
            <td><?= htmlspecialchars($log['keterangan']) ?></td>
            <td><?= htmlspecialchars($log['nama_user'] ?? 'System') ?></td>
          </tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
