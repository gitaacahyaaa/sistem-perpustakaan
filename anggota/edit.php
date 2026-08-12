<?php
require_once __DIR__ . '/../includes/auth.php';
$pageTitle = 'Edit Anggota';

$id = (int) ($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM anggota WHERE id = ?");
$stmt->execute([$id]);
$anggota = $stmt->fetch();

if (!$anggota) {
    redirectDengan('index.php', 'danger', 'Anggota tidak ditemukan.');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama    = trim($_POST['nama'] ?? '');
    $alamat  = trim($_POST['alamat'] ?? '');
    $no_hp   = trim($_POST['no_hp'] ?? '');
    $email   = trim($_POST['email'] ?? '');

    if ($nama === '') {
        $error = 'Nama wajib diisi.';
    } else {
        $stmt = $pdo->prepare("UPDATE anggota SET nama=?, alamat=?, no_hp=?, email=? WHERE id=?");
        $stmt->execute([$nama, $alamat, $no_hp, $email, $id]);

        catatLog($pdo, $_SESSION['user_id'], 'Edit Anggota', "Mengubah data anggota \"$nama\" ({$anggota['kode_anggota']})");
        redirectDengan('index.php', 'success', "Anggota \"$nama\" berhasil diperbarui.");
    }
}

require __DIR__ . '/../includes/header.php';
?>

<div class="card shadow-sm" style="max-width:600px;">
  <div class="card-body">
    <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <p class="text-muted">Kode anggota: <strong><?= htmlspecialchars($anggota['kode_anggota']) ?></strong></p>
    <form method="POST">
      <div class="mb-3">
        <label class="form-label">Nama Lengkap</label>
        <input type="text" name="nama" class="form-control" required value="<?= htmlspecialchars($_POST['nama'] ?? $anggota['nama']) ?>">
      </div>
      <div class="mb-3">
        <label class="form-label">Alamat</label>
        <input type="text" name="alamat" class="form-control" value="<?= htmlspecialchars($_POST['alamat'] ?? $anggota['alamat']) ?>">
      </div>
      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">No. HP</label>
          <input type="text" name="no_hp" class="form-control" value="<?= htmlspecialchars($_POST['no_hp'] ?? $anggota['no_hp']) ?>">
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Email</label>
          <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($_POST['email'] ?? $anggota['email']) ?>">
        </div>
      </div>
      <button class="btn btn-primary"><i class="bi bi-save"></i> Simpan Perubahan</button>
      <a href="index.php" class="btn btn-outline-secondary">Batal</a>
    </form>
  </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
