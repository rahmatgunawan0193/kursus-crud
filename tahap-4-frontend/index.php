<?php
require 'db.php';
require 'functions.php';

$judul = 'Beranda';
$area  = 'publik';

// --- Pencarian + pagination ---
$q       = trim($_GET['q'] ?? '');
$like    = '%' . $q . '%';
$batas   = 6;
$halaman = max(1, (int)($_GET['halaman'] ?? 1));

$stmt = mysqli_prepare($connection,
    "SELECT COUNT(*) FROM course WHERE name_course LIKE ? OR descript_course LIKE ?");
mysqli_stmt_bind_param($stmt, 'ss', $like, $like);
mysqli_stmt_execute($stmt);
$total = (int)mysqli_fetch_row(mysqli_stmt_get_result($stmt))[0];

$total_halaman = max(1, (int)ceil($total / $batas));
$halaman       = min($halaman, $total_halaman);
$halaman_awal  = ($halaman - 1) * $batas;

$stmt = mysqli_prepare($connection,
    "SELECT * FROM course WHERE name_course LIKE ? OR descript_course LIKE ?
     ORDER BY date_course ASC, id_course ASC LIMIT ?, ?");
mysqli_stmt_bind_param($stmt, 'ssii', $like, $like, $halaman_awal, $batas);
mysqli_stmt_execute($stmt);
$hasil = mysqli_stmt_get_result($stmt);

require 'partials/header.php';
?>
<section class="hero p-4 p-md-5 mb-4">
  <h1 class="display-6 fw-bold">Belajar teknologi dengan kursus singkat</h1>
  <p class="lead mb-4">Pilih kursus, daftar dalam satu menit, dan mulai belajar.</p>
  <form class="row g-2" method="get" action="index.php">
    <div class="col-md-8">
      <input type="search" name="q" class="form-control form-control-lg" placeholder="Cari kursus..." value="<?= e($q) ?>">
    </div>
    <div class="col-md-4 d-grid">
      <button class="btn btn-warning btn-lg" type="submit">Cari</button>
    </div>
  </form>
</section>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h2 class="h4 mb-0">Daftar Kursus</h2>
  <span class="text-muted small"><?= $total ?> kursus<?= $q !== '' ? ' untuk "' . e($q) . '"' : '' ?></span>
</div>

<?php if ($total === 0): ?>
  <div class="alert alert-info">Kursus tidak ditemukan. Coba kata kunci lain.</div>
<?php endif; ?>

<div class="row g-4 mb-4">
  <?php while ($c = mysqli_fetch_assoc($hasil)): ?>
    <div class="col-md-6 col-lg-4">
      <div class="card h-100 shadow-sm">
        <img src="<?= e($c['photo_course']) ?>" class="card-img-top foto-kursus" alt="<?= e($c['name_course']) ?>">
        <div class="card-body d-flex flex-column">
          <h3 class="h5 card-title"><?= e($c['name_course']) ?></h3>
          <p class="text-muted small mb-2">Mulai: <?= e(tgl_indo($c['date_course'])) ?></p>
          <p class="card-text"><?= e(mb_strimwidth($c['descript_course'], 0, 100, '...')) ?></p>
          <a href="daftar.php?id=<?= (int)$c['id_course'] ?>" class="btn btn-primary mt-auto">Daftar Sekarang</a>
        </div>
      </div>
    </div>
  <?php endwhile; ?>
</div>

<?php render_pagination($halaman, $total_halaman, $q !== '' ? ['q' => $q] : []); ?>
<?php require 'partials/footer.php'; ?>
