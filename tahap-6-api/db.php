<?php
// db.php - koneksi ke database "course"
$host   = "localhost";
$user   = "root";
$pass   = "";
$dbname = "course";

// Aktifkan exception supaya error query mudah ditemukan saat belajar
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $connection = mysqli_connect($host, $user, $pass, $dbname);
    mysqli_set_charset($connection, 'utf8mb4');
} catch (mysqli_sql_exception $ex) {
    die('Gagal melakukan koneksi ke Database : ' . $ex->getMessage());
}

date_default_timezone_set('Asia/Jakarta');
// Jika membuka localhost/kursus/db.php tampil halaman kosong (blank) = koneksi BERHASIL
