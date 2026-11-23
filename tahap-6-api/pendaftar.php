<?php
require 'db.php';
require 'functions.php';
require_login();                       // Tahap 5

$judul = 'Data Pendaftar';
$area  = 'admin';

$total = (int)mysqli_fetch_row(mysqli_query($connection, "SELECT COUNT(*) FROM user_course"))[0];
$batas         = 10;
$halaman       = max(1, (int)($_GET['halaman'] ?? 1));
$total_halaman = max(1, (int)ceil($total / $batas));
$halaman       = min($halaman, $total_halaman);
$halaman_awal  = ($halaman - 1) * $batas;

// JOIN dua tabel: nama kursus diambil dari tabel course
$stmt = mysqli_prepare($connection,
    "SELECT u.fullname, u.phone, u.email, u.user_register, c.name_course
       FROM user_course u
       JOIN course c ON c.id_course = u.id_course
      ORDER BY u.user_register DESC, u.id_user DESC
      LIMIT ?, ?");
mysqli_stmt_bind_param($stmt, 'ii', $halaman_awal, $batas);
mysqli_stmt_execute($stmt);
$hasil = mysqli_stmt_get_result($stmt);
$nomor = $halaman_awal + 1;

require 'partials/header.php';
?>
<div class="card shadow-sm">
  <div class="card-header bg-white fw-semibold">Data Pendaftar (<?= $total ?>)</div>
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr><th>No</th><th>Nama</th><th>Kursus</th><th>Telepon</th><th>Email</th><th>Waktu Daftar</th></tr>
      </thead>
      <tbody>
        <?php if ($total === 0): ?>
          <tr><td colspan="6" class="text-center text-muted py-4">Belum ada pendaftar.</td></tr>
        <?php endif; ?>
        <?php while ($u = mysqli_fetch_assoc($hasil)): ?>
          <tr>
            <td><?= $nomor++ ?></td>
            <td class="fw-semibold"><?= e($u['fullname']) ?></td>
            <td><?= e($u['name_course']) ?></td>
            <td><?= e($u['phone']) ?></td>
            <td><?= e($u['email']) ?></td>
            <td class="text-nowrap"><?= e(date('d/m/Y H:i', strtotime($u['user_register']))) ?></td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
  <div class="card-footer bg-white"><?php render_pagination($halaman, $total_halaman); ?></div>
</div>
<?php require 'partials/footer.php'; ?>
