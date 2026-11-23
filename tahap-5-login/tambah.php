<?php
require 'db.php';
require 'functions.php';
require_login();                       // Tahap 5

$judul = 'Tambah Kursus';
$area  = 'admin';
$errors = [];
$name_course = $descript_course = $date_course = '';

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

    // Upload gambar hanya diproses jika isian lain sudah benar
    $photo_course = null;
    if (!$errors) {
        list($photo_course, $err_foto) = upload_foto($_FILES['photo_course'] ?? null, true);
        if ($err_foto !== '') {
            $errors[] = $err_foto;
        }
    }

    // CREATE
    if (!$errors) {
        $stmt = mysqli_prepare($connection,
            "INSERT INTO course (name_course, descript_course, date_course, photo_course) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, 'ssss', $name_course, $descript_course, $date_course, $photo_course);
        mysqli_stmt_execute($stmt);
        set_flash('success', 'Data berhasil disimpan.');
        redirect('dashboard.php');
    }
}

require 'partials/header.php';
?>
<div class="row justify-content-center"><div class="col-lg-7">
  <div class="card shadow-sm">
    <div class="card-header bg-white fw-semibold">Tambah Kursus</div>
    <div class="card-body">
      <?php if ($errors): ?>
        <div class="alert alert-danger"><ul class="mb-0">
          <?php foreach ($errors as $er): ?><li><?= e($er) ?></li><?php endforeach; ?>
        </ul></div>
      <?php endif; ?>
      <form method="post" enctype="multipart/form-data">
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
          <label class="form-label" for="photo_course">Foto Kursus</label>
          <input type="file" class="form-control" id="photo_course" name="photo_course"
                 accept="image/jpeg,image/png,image/gif" required>
          <div class="form-text">Format JPG, PNG, atau GIF. Maksimal 2 MB.</div>
        </div>
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="dashboard.php" class="btn btn-outline-secondary">Batal</a>
      </form>
    </div>
  </div>
</div></div>
<?php require 'partials/footer.php'; ?>
