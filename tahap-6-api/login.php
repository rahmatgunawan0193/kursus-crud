<?php
require 'db.php';
require 'functions.php';

if (is_login()) {
    redirect('dashboard.php');
}

$judul = 'Login Admin';
$area  = 'publik';
$error = '';
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = mysqli_prepare($connection, "SELECT id_admin, password, nama FROM admin WHERE username = ?");
    mysqli_stmt_bind_param($stmt, 's', $username);
    mysqli_stmt_execute($stmt);
    $admin = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    // password_verify membandingkan password yang diketik dengan HASH di database
    if ($admin && password_verify($password, $admin['password'])) {
        session_regenerate_id(true);               // cegah session fixation
        $_SESSION['admin_id']   = $admin['id_admin'];
        $_SESSION['admin_nama'] = $admin['nama'];
        redirect('dashboard.php');
    }
    // Pesan sengaja sama untuk username salah maupun password salah
    $error = 'Username atau password salah.';
}

require 'partials/header.php';
?>
<div class="row justify-content-center"><div class="col-md-6 col-lg-4">
  <div class="card shadow-sm">
    <div class="card-body p-4">
      <h1 class="h4 mb-3 text-center">Login Admin</h1>
      <?php if (isset($_GET['keluar'])): ?>
        <div class="alert alert-success">Anda telah keluar.</div>
      <?php endif; ?>
      <?php if ($error !== ''): ?>
        <div class="alert alert-danger"><?= e($error) ?></div>
      <?php endif; ?>
      <form method="post">
        <?= csrf_field() ?>
        <div class="mb-3">
          <label class="form-label" for="username">Username</label>
          <input type="text" class="form-control" id="username" name="username" value="<?= e($username) ?>" required autofocus>
        </div>
        <div class="mb-3">
          <label class="form-label" for="password">Password</label>
          <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="d-grid"><button type="submit" class="btn btn-primary">Masuk</button></div>
      </form>
    </div>
  </div>
</div></div>
<?php require 'partials/footer.php'; ?>
