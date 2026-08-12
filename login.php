<?php
session_start();
require_once __DIR__ . '/config/database.php';
// PHP built-in server uses this folder as the web root.
define('BASE_URL', '/');

if (isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . 'index.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_nama'] = $user['nama'];
        $_SESSION['user_role'] = $user['role'];
        header('Location: ' . BASE_URL . 'index.php');
        exit;
    } else {
        $error = 'Username atau password salah.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - Sistem Perpustakaan</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>
<body style="background:#1A1730; min-height:100vh;" class="d-flex align-items-center">
<div class="container" style="max-width:380px;">
  <div class="card shadow-lg border-0">
    <div class="card-body p-4">
      <h4 class="text-center mb-1"><i class="bi bi-book-half"></i> Sistem Perpustakaan</h4>
      <p class="text-center text-muted mb-4">Silakan login untuk melanjutkan</p>

      <?php if ($error): ?>
        <div class="alert alert-danger py-2"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST">
        <div class="mb-3">
          <label class="form-label">Username</label>
          <input type="text" name="username" class="form-control" required autofocus>
        </div>
        <div class="mb-3">
          <label class="form-label">Password</label>
          <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Login</button>
      </form>

      <p class="text-center text-muted mt-3 mb-0" style="font-size:13px;">
        Belum punya akun admin? Jalankan <code>buat_admin.php</code> sekali, lalu hapus filenya.
      </p>
    </div>
  </div>
</div>
</body>
</html>
