<?php
require 'db.php';
require 'functions.php';
// require_login();   // diaktifkan di Tahap 5

$judul = 'Dashboard';
$area  = 'admin';

// --- Ringkasan ---
$total_kursus    = (int)mysqli_fetch_row(mysqli_query($connection, "SELECT COUNT(*) FROM course"))[0];
$total_pendaftar = (int)mysqli_fetch_row(mysqli_query($connection, "SELECT COUNT(*) FROM user_course"))[0];

// --- Pagination ---
$batas         = 5;
$halaman       = max(1, (int)($_GET['halaman'] ?? 1));
$total_halaman = max(1, (int)ceil($total_kursus / $batas));
$halaman       = min($halaman, $total_halaman);
$halaman_awal  = ($halaman - 1) * $batas;

// --- READ: ambil data kursus untuk halaman ini ---
$stmt = mysqli_prepare($connection, "SELECT * FROM course ORDER BY id_course DESC LIMIT ?, ?");
mysqli_stmt_bind_param($stmt, 'ii', $halaman_awal, $batas);
mysqli_stmt_execute($stmt);
$hasil = mysqli_stmt_get_result($stmt);
$nomor = $halaman_awal + 1;

require 'partials/header.php';
?>
<div class="row g-3 mb-4">
  <div class="col-sm-6"><div class="card shadow-sm"><div class="card-body">
    <div class="text-muted small">Total Kursus</div><div class="fs-2 fw-bold"><?= $total_kursus ?></div>
  </div></div></div>
  <div class="col-sm-6"><div class="card shadow-sm"><div class="card-body">
    <div class="text-muted small">Total Pendaftar</div><div class="fs-2 fw-bold"><?= $total_pendaftar ?></div>
  </div></div></div>
</div>

<div class="card shadow-sm">
  <div class="card-header bg-white d-flex justify-content-between align-items-center">
    <span class="fw-semibold">Data Kursus</span>
    <a href="tambah.php" class="btn btn-primary btn-sm">+ Tambah Kursus</a>
  </div>
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr><th>No</th><th>Foto</th><th>Nama Kursus</th><th>Deskripsi</th><th>Tanggal</th><th>Aksi</th></tr>
      </thead>
      <tbody>
        <?php if ($total_kursus === 0): ?>
          <tr><td colspan="6" class="text-center text-muted py-4">Belum ada data kursus.</td></tr>
        <?php endif; ?>
        <?php while ($data = mysqli_fetch_assoc($hasil)): ?>
          <tr>
            <td><?= $nomor++ ?></td>
            <td><img src="<?= e($data['photo_course']) ?>" class="foto-kecil" alt="Foto <?= e($data['name_course']) ?>"></td>
            <td class="fw-semibold"><?= e($data['name_course']) ?></td>
            <td><?= e(mb_strimwidth($data['descript_course'], 0, 100, '...')) ?></td>
            <td class="text-nowrap"><?= e(tgl_indo($data['date_course'])) ?></td>
            <td class="text-nowrap">
              <a class="btn btn-sm btn-warning" href="edit.php?id=<?= (int)$data['id_course'] ?>">Edit</a>
              <form method="post" action="hapus.php" class="d-inline"
                    onsubmit="return confirm('Yakin hapus kursus ini? Data pendaftarnya ikut terhapus.');">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= (int)$data['id_course'] ?>">
                <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
              </form>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
  <div class="card-footer bg-white"><?php render_pagination($halaman, $total_halaman); ?></div>
</div>
<?php require 'partials/footer.php'; ?>
