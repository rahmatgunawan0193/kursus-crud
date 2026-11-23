<?php
// Variabel yang diisi halaman pemanggil: $judul dan $area ('publik' atau 'admin')
$area  = $area  ?? 'publik';
$judul = $judul ?? 'Kursus Online';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($judul) ?> | Kursus Online</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/style.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark <?= $area === 'admin' ? 'bg-dark' : 'bg-primary' ?>">
  <div class="container">
    <a class="navbar-brand fw-bold" href="<?= $area === 'admin' ? 'dashboard.php' : 'index.php' ?>">
      Kursus Online<?= $area === 'admin' ? ' &middot; Admin' : '' ?>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuUtama" aria-label="Buka menu">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="menuUtama">
      <ul class="navbar-nav ms-auto">
        <?php if ($area === 'admin'): ?>
          <li class="nav-item"><a class="nav-link" href="dashboard.php">Kursus</a></li>
          <li class="nav-item"><a class="nav-link" href="pendaftar.php">Pendaftar</a></li>
          <li class="nav-item"><a class="nav-link" href="index.php" target="_blank">Lihat Situs</a></li>
          <?php if (is_login()): ?>
            <li class="nav-item"><a class="nav-link" href="logout.php">Keluar (<?= e($_SESSION['admin_nama'] ?? '') ?>)</a></li>
          <?php endif; ?>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="index.php">Beranda</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= is_login() ? 'dashboard.php' : 'login.php' ?>">Admin</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
<main class="container py-4">
<?php show_flash(); ?>
