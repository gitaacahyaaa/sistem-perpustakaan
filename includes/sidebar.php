<?php
$currentDir  = basename(dirname($_SERVER['SCRIPT_NAME']));
$currentFile = basename($_SERVER['SCRIPT_NAME']);
$isRoot = ($currentDir === 'perpustakaan' || $currentDir === '');

function navActive($cond) {
    return $cond ? 'active' : '';
}
?>
<div class="bg-white border-end" style="width:230px; min-height: calc(100vh - 56px);">
  <div class="list-group list-group-flush pt-2">
    <a href="<?= BASE_URL ?>index.php" class="list-group-item list-group-item-action <?= navActive($isRoot && $currentFile === 'index.php') ?>">
      <i class="bi bi-speedometer2 me-2"></i>Dashboard
    </a>
    <a href="<?= BASE_URL ?>buku/index.php" class="list-group-item list-group-item-action <?= navActive($currentDir === 'buku') ?>">
      <i class="bi bi-journal-bookmark me-2"></i>Data Buku
    </a>
    <a href="<?= BASE_URL ?>anggota/index.php" class="list-group-item list-group-item-action <?= navActive($currentDir === 'anggota') ?>">
      <i class="bi bi-people me-2"></i>Data Anggota
    </a>
    <a href="<?= BASE_URL ?>peminjaman/index.php" class="list-group-item list-group-item-action <?= navActive($currentDir === 'peminjaman') ?>">
      <i class="bi bi-arrow-left-right me-2"></i>Peminjaman
    </a>
    <a href="<?= BASE_URL ?>denda/index.php" class="list-group-item list-group-item-action <?= navActive($currentDir === 'denda') ?>">
      <i class="bi bi-cash-coin me-2"></i>Denda
    </a>
    <a href="<?= BASE_URL ?>laporan/log.php" class="list-group-item list-group-item-action <?= navActive($currentDir === 'laporan') ?>">
      <i class="bi bi-clock-history me-2"></i>Log Aktivitas
    </a>
  </div>
</div>
