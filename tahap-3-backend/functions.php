<?php
// functions.php - fungsi bantu yang dipakai oleh semua halaman
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Escape output agar aman dari XSS
function e($teks) {
    return htmlspecialchars((string)$teks, ENT_QUOTES, 'UTF-8');
}

function redirect($url) {
    header('Location: ' . $url);
    exit;
}

// ---------- Notifikasi (pengganti variabel $notif di slide) ----------
function set_flash($tipe, $pesan) {
    $_SESSION['flash'] = ['tipe' => $tipe, 'pesan' => $pesan];
}

function show_flash() {
    if (!empty($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        echo '<div class="alert alert-' . e($f['tipe']) . ' alert-dismissible fade show" role="alert">'
           . e($f['pesan'])
           . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button></div>';
    }
}

// ---------- Login dan CSRF (mulai dipakai di Tahap 5) ----------
function is_login() {
    return !empty($_SESSION['admin_id']);
}

function require_login() {
    if (!is_login()) {
        redirect('login.php');
    }
}

function csrf_token() {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_field() {
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

function csrf_check() {
    if (!isset($_POST['csrf'], $_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $_POST['csrf'])) {
        http_response_code(419);
        exit('Token keamanan tidak valid. Silakan muat ulang halaman.');
    }
}

// ---------- Tanggal bahasa Indonesia ----------
function tgl_indo($tanggal) {
    $bulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
              'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    $t = strtotime($tanggal);
    return date('j', $t) . ' ' . $bulan[(int)date('n', $t)] . ' ' . date('Y', $t);
}

// ---------- Pagination Bootstrap 5 ----------
function render_pagination($halaman, $total_halaman, $params = []) {
    if ($total_halaman <= 1) {
        return;
    }
    $url = function ($h) use ($params) {
        return '?' . http_build_query(array_merge($params, ['halaman' => $h]));
    };
    echo '<nav aria-label="Navigasi halaman"><ul class="pagination justify-content-center mb-0">';
    echo '<li class="page-item' . ($halaman <= 1 ? ' disabled' : '') . '">'
       . '<a class="page-link" href="' . e($url($halaman - 1)) . '">Sebelumnya</a></li>';
    for ($i = 1; $i <= $total_halaman; $i++) {
        echo '<li class="page-item' . ($i === $halaman ? ' active' : '') . '">'
           . '<a class="page-link" href="' . e($url($i)) . '">' . $i . '</a></li>';
    }
    echo '<li class="page-item' . ($halaman >= $total_halaman ? ' disabled' : '') . '">'
       . '<a class="page-link" href="' . e($url($halaman + 1)) . '">Berikutnya</a></li>';
    echo '</ul></nav>';
}

// ---------- Upload gambar ----------
// Mengembalikan array: [path_file | null, pesan_error]
function upload_foto($file, $wajib = true) {
    if (!is_array($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return $wajib ? [null, 'Anda belum memilih gambar.'] : [null, ''];
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return [null, 'Upload gagal (kode error ' . $file['error'] . ').'];
    }
    if ($file['size'] > 2 * 1024 * 1024) {
        return [null, 'Ukuran gambar maksimal 2 MB.'];
    }
    // Cek jenis file dari ISI file, bukan dari nama/ekstensi yang dikirim pengguna
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime  = $finfo->file($file['tmp_name']);
    $boleh = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif'];
    if (!isset($boleh[$mime])) {
        return [null, 'Jenis gambar yang Anda kirim salah. Harus .jpg, .gif, atau .png'];
    }
    $folder = __DIR__ . '/photo/';
    if (!is_dir($folder)) {
        mkdir($folder, 0755, true);
    }
    $nama = 'course_' . bin2hex(random_bytes(8)) . '.' . $boleh[$mime];
    if (!move_uploaded_file($file['tmp_name'], $folder . $nama)) {
        return [null, 'Gambar gagal dikirim ke direktori.'];
    }
    return ['photo/' . $nama, ''];
}

function hapus_foto($path) {
    $file = __DIR__ . '/photo/' . basename((string)$path);
    if (is_file($file)) {
        @unlink($file);
    }
}
