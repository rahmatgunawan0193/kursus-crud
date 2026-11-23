<?php
// API READ : GET /kursus/api/courses.php
//   ?id=2                      -> detail satu kursus
//   ?q=php&halaman=1&batas=6   -> daftar kursus (pencarian + pagination)
require __DIR__ . '/../db.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

function kirim($data, $kode = 200) {
    http_response_code($kode);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    kirim(['status' => 'error', 'pesan' => 'Metode tidak diizinkan. Gunakan GET.'], 405);
}

// Alamat dasar aplikasi, dipakai untuk membuat URL foto yang lengkap
$skema = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$base  = $skema . '://' . $_SERVER['HTTP_HOST'] . rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'])), '/') . '/';

function format_kursus($row, $base) {
    return [
        'id'        => (int)$row['id_course'],
        'nama'      => $row['name_course'],
        'deskripsi' => $row['descript_course'],
        'tanggal'   => $row['date_course'],
        'foto'      => $base . $row['photo_course'],
    ];
}

// --- Detail satu kursus ---
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = mysqli_prepare($connection, "SELECT * FROM course WHERE id_course = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    if (!$row) {
        kirim(['status' => 'error', 'pesan' => 'Kursus tidak ditemukan.'], 404);
    }
    kirim(['status' => 'sukses', 'data' => format_kursus($row, $base)]);
}

// --- Daftar kursus ---
$q       = trim($_GET['q'] ?? '');
$like    = '%' . $q . '%';
$halaman = max(1, (int)($_GET['halaman'] ?? 1));
$batas   = min(50, max(1, (int)($_GET['batas'] ?? 10)));
$awal    = ($halaman - 1) * $batas;

$stmt = mysqli_prepare($connection,
    "SELECT COUNT(*) FROM course WHERE name_course LIKE ? OR descript_course LIKE ?");
mysqli_stmt_bind_param($stmt, 'ss', $like, $like);
mysqli_stmt_execute($stmt);
$total = (int)mysqli_fetch_row(mysqli_stmt_get_result($stmt))[0];

$stmt = mysqli_prepare($connection,
    "SELECT * FROM course WHERE name_course LIKE ? OR descript_course LIKE ?
     ORDER BY date_course ASC, id_course ASC LIMIT ?, ?");
mysqli_stmt_bind_param($stmt, 'ssii', $like, $like, $awal, $batas);
mysqli_stmt_execute($stmt);
$hasil = mysqli_stmt_get_result($stmt);

$data = [];
while ($row = mysqli_fetch_assoc($hasil)) {
    $data[] = format_kursus($row, $base);
}

kirim([
    'status'        => 'sukses',
    'total'         => $total,
    'halaman'       => $halaman,
    'total_halaman' => max(1, (int)ceil($total / $batas)),
    'data'          => $data,
]);
