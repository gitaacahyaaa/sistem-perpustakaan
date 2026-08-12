<?php
require_once __DIR__ . '/../includes/auth.php';
$pageTitle = 'Tambah Anggota';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama    = trim($_POST['nama'] ?? '');
    $alamat  = trim($_POST['alamat'] ?? '');
    $no_hp   = trim($_POST['no_hp'] ?? '');
    $email   = trim($_POST['email'] ?? '');

    if ($nama === '') {
        $error = 'Nama wajib diisi.';
    } else {
        $kode = generateKode($pdo, 'anggota', 'kode_anggota', 'AG');
        $stmt = $pdo->prepare(
            "INSERT INTO anggota (kode_anggota, nama, alamat, no_hp, email, tanggal_daftar) VALUES (?, ?, ?, ?, ?, CURDATE())"
        );
        $stmt->execute([$kode, $nama, $alamat, $no_hp, $email]);

        catatLog($pdo, $_SESSION['user_id'], 'Tambah Anggota', "Menambahkan anggota \"$nama\" ($kode)");
        redirectDengan('index.php', 'success', "Anggota \"$nama\" berhasil ditambahkan.");
    }
}

require __DIR__ . '/../includes/header.php';
?>

<div class="card shadow-sm" style="max-width:600px;">
  <div class="card-body">
    <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <form method="POST">
      <div class="mb-3">
        <label class="form-label">Nama Lengkap</label>
        <input type="text" name="nama" class="form-control" required value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>">
      </div>
      <div class="mb-3">
        <label class="form-label">Alamat</label>
        <input type="text" name="alamat" class="form-control" value="<?= htmlspecialchars($_POST['alamat'] ?? '') ?>">
      </div>
      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">No. HP</label>
          <input type="text" name="no_hp" class="form-control" value="<?= htmlspecialchars($_POST['no_hp'] ?? '') ?>">
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Email</label>
          <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        </div>
      </div>
      <button class="btn btn-primary"><i class="bi bi-save"></i> Simpan</button>
      <a href="index.php" class="btn btn-outline-secondary">Batal</a>
    </form>
  </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
