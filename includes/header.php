<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' - Sistem Perpustakaan' : 'Sistem Perpustakaan' ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
</head>
<body>

<nav class="navbar navbar-dark px-3 shadow-sm" style="background:#2C2756;">
  <span class="navbar-brand mb-0 h1"><i class="bi bi-book-half"></i> Perpus App</span>
  <div class="d-flex align-items-center text-white">
    <span class="me-3 d-none d-sm-inline">
      <i class="bi bi-person-circle"></i>
      <?= htmlspecialchars($_SESSION['user_nama'] ?? '') ?>
      <span class="badge bg-light text-dark ms-1"><?= htmlspecialchars($_SESSION['user_role'] ?? '') ?></span>
    </span>
    <a href="<?= BASE_URL ?>logout.php" class="btn btn-sm btn-outline-light">
      <i class="bi bi-box-arrow-right"></i> Keluar
    </a>
  </div>
</nav>

<div class="d-flex">
  <?php include __DIR__ . '/sidebar.php'; ?>

  <main class="flex-grow-1 p-4">
    <h4 class="mb-4"><?= isset($pageTitle) ? htmlspecialchars($pageTitle) : '' ?></h4>
    <?php tampilkanFlash(); ?>
