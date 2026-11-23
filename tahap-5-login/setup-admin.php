<?php
// Jalankan SATU KALI di browser, lalu HAPUS file ini.
require 'db.php';

$jumlah = (int)mysqli_fetch_row(mysqli_query($connection, "SELECT COUNT(*) FROM admin"))[0];
if ($jumlah > 0) {
    exit('Admin sudah ada. Hapus file setup-admin.php ini dari folder kursus.');
}

$username = 'admin';
$password = 'admin123';                         // ganti setelah praktikum
$nama     = 'Administrator';
$hash     = password_hash($password, PASSWORD_DEFAULT);   // hasil hash acak, panjang +-60 karakter

$stmt = mysqli_prepare($connection, "INSERT INTO admin (username, password, nama) VALUES (?, ?, ?)");
mysqli_stmt_bind_param($stmt, 'sss', $username, $hash, $nama);
mysqli_stmt_execute($stmt);

echo 'Admin dibuat. Username: ' . htmlspecialchars($username) . ' | Password: ' . htmlspecialchars($password)
   . '<br>Hash tersimpan di database: ' . htmlspecialchars($hash)
   . '<br><strong>Segera hapus file setup-admin.php.</strong>';
