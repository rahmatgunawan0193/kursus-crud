<?php
require 'db.php';
require 'functions.php';
// require_login();   // diaktifkan di Tahap 5

// Hapus hanya boleh lewat POST (bukan link biasa) agar tidak terhapus tanpa sengaja
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('dashboard.php');
}
csrf_check();

$id = (int)($_POST['id'] ?? 0);

$stmt = mysqli_prepare($connection, "SELECT photo_course FROM course WHERE id_course = ?");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if ($row) {
    // DELETE (data di user_course ikut terhapus karena ON DELETE CASCADE)
    $stmt = mysqli_prepare($connection, "DELETE FROM course WHERE id_course = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    hapus_foto($row['photo_course']);
    set_flash('success', 'Berhasil hapus data.');
} else {
    set_flash('danger', 'Gagal hapus data.');
}
redirect('dashboard.php');
