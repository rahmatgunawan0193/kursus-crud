<?php
require 'db.php';
require 'functions.php';
require_login();                       // Tahap 5

$judul = 'Edit Kursus';
$area  = 'admin';
$id    = (int)($_GET['id'] ?? 0);

// Ambil data lama
$stmt = mysqli_prepare($connection, "SELECT * FROM course WHERE id_course = ?");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$course = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
if (!$course) {
    set_flash('danger', 'Data kursus tidak ditemukan.');
    redirect('dashboard.php');
}

$errors = [];
$name_course     = $course['name_course'];
$descript_course = $course['descript_course'];
$date_course     = $course['date_course'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $name_course     = trim($_POST['name_course'] ?? '');
    $descript_course = trim($_POST['descript_course'] ?? '');
    $date_course     = $_POST['date_course'] ?? '';

    if ($name_course === '' || mb_strlen($name_course) > 50) {
        $errors[] = 'Nama kursus wajib diisi (maksimal 50 karakter).';
    }
    if ($descript_course === '') {
        $errors[] = 'Deskripsi wajib diisi.';
    }
    $d = DateTime::createFromFormat('Y-m-d', $date_course);
    if (!$d || $d->format('Y-m-d') !== $date_course) {
        $errors[] = 'Tanggal tidak valid.';
    }

    // Foto bersifat opsional saat edit
    $foto_baru = null;
    if (!$errors) {
        list($foto_baru, $err_foto) = upload_foto($_FILES['photo_course'] ?? null, false);
        if ($err_foto !== '') {
            $errors[] = $err_foto;
        }
    }

    // UPDATE
    if (!$errors) {
        $photo_course = $foto_baru ?? $course['photo_course'];
        $stmt = mysqli_prepare($connection,
            "UPDATE course SET name_course = ?, descript_course = ?, date_course = ?, photo_course = ? WHERE id_course = ?");
        mysqli_stmt_bind_param($stmt, 'ssssi', $name_course, $descript_course, $date_course, $photo_course, $id);
        mysqli_stmt_execute($stmt);
        if ($foto_baru !== null) {
            hapus_foto($course['photo_course']);   // buang foto lama
        }
        set_flash('success', 'Data berhasil diperbarui.');
        redirect('dashboard.php');
    }
}

require 'partials/header.php';
?>
<div class="row justify-content-center"><div class="col-lg-7">
  <div class="card shadow-sm">
    <div class="card-header bg-white fw-semibold">Edit Kursus</div>
    <div class="card-body">
      <?php if ($errors): ?>
        <div class="alert alert-danger"><ul class="mb-0">
          <?php foreach ($errors as $er): ?><li><?= e($er) ?></li><?php endforeach; ?>
        </ul></div>
      <?php endif; ?>
      <form method="post" enctype="multipart/form-data" action="edit.php?id=<?= (int)$id ?>">
        <?= csrf_field() ?>
        <div class="mb-3">
          <label class="form-label" for="name_course">Nama Kursus</label>
          <input type="text" class="form-control" id="name_course" name="name_course" maxlength="50"
                 value="<?= e($name_course) ?>" required>
        </div>
        <div class="mb-3">
          <label class="form-label" for="descript_course">Deskripsi</label>
          <textarea class="form-control" id="descript_course" name="descript_course" rows="4" required><?= e($descript_course) ?></textarea>
        </div>
        <div class="mb-3">
          <label class="form-label" for="date_course">Tanggal Mulai</label>
          <input type="date" class="form-control" id="date_course" name="date_course" value="<?= e($date_course) ?>" required>
        </div>
        <div class="mb-3">
          <label class="form-label d-block">Foto Saat Ini</label>
          <img src="<?= e($course['photo_course']) ?>" class="img-thumbnail" style="max-height:120px" alt="Foto saat ini">
        </div>
        <div class="mb-3">
          <label class="form-label" for="photo_course">Ganti Foto (opsional)</label>
          <input type="file" class="form-control" id="photo_course" name="photo_course"
                 accept="image/jpeg,image/png,image/gif">
          <div class="form-text">Kosongkan jika tidak ingin mengganti foto.</div>
        </div>
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        <a href="dashboard.php" class="btn btn-outline-secondary">Batal</a>
      </form>
    </div>
  </div>
</div></div>
<?php require 'partials/footer.php'; ?>
