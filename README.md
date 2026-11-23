# CRUD Kursus: PHP, MySQL, Bootstrap 5 (bertahap)

Repositori praktikum Pemrograman Web. Setiap folder `tahap-N-...` adalah **hasil lengkap pada akhir tahap itu** dan bisa dijalankan sendiri. Mahasiswa yang tertinggal tinggal mengambil folder tahap sebelumnya.

| Folder | Isi | Tag Git |
|---|---|---|
| `tahap-1-desain` | Desain statis Bootstrap 5 (front-end dan backend) | `tahap-1` |
| `tahap-2-database` | Database `course` dan tabel (`sql/course.sql`) | `tahap-2` |
| `tahap-3-backend` | Koneksi dan CRUD kursus (admin belum dikunci) | `tahap-3` |
| `tahap-4-frontend` | Beranda dinamis, pencarian, pendaftaran, data pendaftar | `tahap-4` |
| `tahap-5-login` | Login admin, session, password hash | `tahap-5` |
| `tahap-6-api` | API JSON dan `api-demo.html` (hasil akhir) | `tahap-6` |

Setiap folder punya `LANGKAH.md`: cara menjalankan, file baru, dan file yang berubah dibanding tahap sebelumnya.

## Cara memakai
1. Pasang XAMPP (PHP 7.4+), jalankan Apache dan MySQL.
2. Salin isi folder tahap yang dikerjakan ke `C:\xampp\htdocs\kursus`.
3. Jalankan `sql/course.sql` di phpMyAdmin (tahap 5 dan 6 juga `sql/tahap5-admin.sql`).

## Melihat perbedaan antartahap
```
git diff tahap-3 tahap-4 --stat
git diff tahap-3 tahap-4 -- tahap-4-frontend/partials/header.php
```
Di GitHub: buka tab Compare, atau halaman Releases untuk mengunduh ZIP tiap tahap.

Panduan lengkap (teori, penjelasan kode, latihan) ada pada dokumen modul.
