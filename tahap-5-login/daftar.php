<?php
require 'db.php';
require 'functions.php';

$judul = 'Daftar Kursus';
$area  = 'publik';
$id    = (int)($_GET['id'] ?? 0);

$stmt = mysqli_prepare($connection, "SELECT * FROM course WHERE id_course = ?");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$course = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
if (!$course) {
    set_flash('danger', 'Kursus tidak ditemukan.');
    redirect('index.php');
}

$errors = [];
$fullname = $phone = $email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $fullname = trim($_POST['fullname'] ?? '');
    $phone    = trim($_POST['phone'] ?? '');
    $email    = trim($_POST['email'] ?? '');

    if ($fullname === '' || mb_strlen($fullname) > 50) {
        $errors[] = 'Nama lengkap wajib diisi (maksimal 50 karakter).';
    }
    if (!preg_match('/^[0-9+ -]{8,25}$/', $phone)) {
        $errors[] = 'Nomor telepon tidak valid (8-25 karakter: angka, +, -, spasi).';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 100) {
        $errors[] = 'Format email tidak valid.';
    }

    // Cegah pendaftaran ganda (email yang sama pada kursus yang sama)
    if (!$errors) {
        $stmt = mysqli_prepare($connection, "SELECT COUNT(*) FROM user_course WHERE id_course = ? AND email = ?");
        mysqli_stmt_bind_param($stmt, 'is', $id, $email);
        mysqli_stmt_execute($stmt);
        if ((int)mysqli_fetch_row(mysqli_stmt_get_result($stmt))[0] > 0) {
            $errors[] = 'Email ini sudah terdaftar pada kursus tersebut.';
        }
    }

    // CREATE pendaftaran
    if (!$errors) {
        $stmt = mysqli_prepare($connection,
            "INSERT INTO user_course (id_course, fullname, phone, email) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, 'isss', $id, $fullname, $phone, $email);
        mysqli_stmt_execute($stmt);
        set_flash('success', 'Pendaftaran berhasil! Sampai jumpa di kelas ' . $course['name_course'] . '.');
        redirect('index.php');
    }
}

require 'partials/header.php';
?>
<div class="row g-4">
  <div class="col-lg-5">
    <div class="card shadow-sm">
      <img src="<?= e($course['photo_course']) ?>" class="card-img-top foto-kursus" alt="<?= e($course['name_course']) ?>">
      <div class="card-body">
        <h1 class="h4"><?= e($course['name_course']) ?></h1>
        <p class="text-muted small">Mulai: <?= e(tgl_indo($course['date_course'])) ?></p>
        <p class="mb-0"><?= nl2br(e($course['descript_course'])) ?></p>
      </div>
    </div>
  </div>
  <div class="col-lg-7">
    <div class="card shadow-sm">
      <div class="card-header bg-white fw-semibold">Formulir Pendaftaran</div>
      <div class="card-body">
        <?php if ($errors): ?>
          <div class="alert alert-danger"><ul class="mb-0">
            <?php foreach ($errors as $er): ?><li><?= e($er) ?></li><?php endforeach; ?>
          </ul></div>
        <?php endif; ?>
        <form method="post" action="daftar.php?id=<?= (int)$id ?>">
          <?= csrf_field() ?>
          <div class="mb-3">
            <label class="form-label" for="fullname">Nama Lengkap</label>
            <input type="text" class="form-control" id="fullname" name="fullname" maxlength="50" value="<?= e($fullname) ?>" required>
          </div>
          <div class="mb-3">
            <label class="form-label" for="phone">Nomor Telepon</label>
            <input type="tel" class="form-control" id="phone" name="phone" maxlength="25" value="<?= e($phone) ?>" required>
          </div>
          <div class="mb-3">
            <label class="form-label" for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" maxlength="100" value="<?= e($email) ?>" required>
          </div>
          <button type="submit" class="btn btn-primary">Daftar</button>
          <a href="index.php" class="btn btn-outline-secondary">Kembali</a>
        </form>
      </div>
    </div>
  </div>
</div>
<?php require 'partials/footer.php'; ?>
