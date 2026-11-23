<?php
// API CREATE : POST /kursus/api/daftar.php
// Body JSON: {"id_course":2,"fullname":"Nama","phone":"0812...","email":"a@b.com"}
require __DIR__ . '/../db.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

function kirim($data, $kode = 200) {
    http_response_code($kode);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {      // preflight browser
    http_response_code(204);
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    kirim(['status' => 'error', 'pesan' => 'Metode tidak diizinkan. Gunakan POST.'], 405);
}

// Terima JSON, atau form biasa sebagai cadangan
$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input)) {
    $input = $_POST;
}

$id_course = (int)($input['id_course'] ?? 0);
$fullname  = trim((string)($input['fullname'] ?? ''));
$phone     = trim((string)($input['phone'] ?? ''));
$email     = trim((string)($input['email'] ?? ''));

$errors = [];
if ($fullname === '' || mb_strlen($fullname) > 50) {
    $errors[] = 'fullname wajib diisi (maksimal 50 karakter).';
}
if (!preg_match('/^[0-9+ -]{8,25}$/', $phone)) {
    $errors[] = 'phone tidak valid.';
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 100) {
    $errors[] = 'email tidak valid.';
}
if ($errors) {
    kirim(['status' => 'error', 'pesan' => 'Data tidak valid.', 'kesalahan' => $errors], 422);
}

// Kursus harus ada
$stmt = mysqli_prepare($connection, "SELECT COUNT(*) FROM course WHERE id_course = ?");
mysqli_stmt_bind_param($stmt, 'i', $id_course);
mysqli_stmt_execute($stmt);
if ((int)mysqli_fetch_row(mysqli_stmt_get_result($stmt))[0] === 0) {
    kirim(['status' => 'error', 'pesan' => 'Kursus tidak ditemukan.'], 404);
}

// Cegah pendaftaran ganda
$stmt = mysqli_prepare($connection, "SELECT COUNT(*) FROM user_course WHERE id_course = ? AND email = ?");
mysqli_stmt_bind_param($stmt, 'is', $id_course, $email);
mysqli_stmt_execute($stmt);
if ((int)mysqli_fetch_row(mysqli_stmt_get_result($stmt))[0] > 0) {
    kirim(['status' => 'error', 'pesan' => 'Email ini sudah terdaftar pada kursus tersebut.'], 409);
}

$stmt = mysqli_prepare($connection,
    "INSERT INTO user_course (id_course, fullname, phone, email) VALUES (?, ?, ?, ?)");
mysqli_stmt_bind_param($stmt, 'isss', $id_course, $fullname, $phone, $email);
mysqli_stmt_execute($stmt);

kirim([
    'status' => 'sukses',
    'pesan'  => 'Pendaftaran berhasil.',
    'id'     => mysqli_insert_id($connection),
], 201);
