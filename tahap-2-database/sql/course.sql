-- =====================================================
-- TAHAP 2 : Pembuatan database dan tabel (studi kasus kursus)
-- Jalankan lewat phpMyAdmin > tab SQL (atau Import)
-- =====================================================
CREATE DATABASE IF NOT EXISTS course
  CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE course;

-- Tabel 1 : daftar kursus (dikelola admin)
CREATE TABLE IF NOT EXISTS course (
  id_course       INT(3)       NOT NULL AUTO_INCREMENT,
  name_course     VARCHAR(50)  NOT NULL,
  descript_course TEXT         NOT NULL,
  date_course     DATE         NOT NULL,
  photo_course    VARCHAR(255) NOT NULL,
  PRIMARY KEY (id_course)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel 2 : peserta yang mendaftar ke sebuah kursus
CREATE TABLE IF NOT EXISTS user_course (
  id_user       INT(3)       NOT NULL AUTO_INCREMENT,
  id_course     INT(3)       NOT NULL,
  fullname      VARCHAR(50)  NOT NULL,
  phone         VARCHAR(25)  NOT NULL,
  email         VARCHAR(100) NOT NULL,
  user_register TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id_user),
  KEY idx_user_course (id_course),
  CONSTRAINT fk_user_course FOREIGN KEY (id_course)
    REFERENCES course (id_course) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Data contoh (7 kursus agar pagination terlihat)
INSERT INTO course (name_course, descript_course, date_course, photo_course) VALUES
('Dasar Pemrograman Web', 'Belajar HTML dan CSS dari nol sampai dapat membuat halaman web responsif pertama Anda. Cocok untuk pemula yang belum pernah menulis kode.', '2026-12-01', 'photo/course-1.png'),
('PHP dan MySQL untuk Pemula', 'Membuat aplikasi web dinamis dengan PHP dan MySQL: koneksi database, query, form, dan operasi CRUD lengkap dengan studi kasus nyata.', '2026-12-08', 'photo/course-2.png'),
('Bootstrap 5 untuk Antarmuka Modern', 'Membangun tampilan web yang rapi dan responsif dengan grid, komponen, dan utilitas Bootstrap 5 tanpa menulis banyak CSS.', '2026-12-15', 'photo/course-3.png'),
('Scratch untuk Anak', 'Mengenalkan logika pemrograman kepada anak usia 6-9 tahun melalui blok visual, animasi, dan game sederhana yang menyenangkan.', '2027-01-05', 'photo/course-4.png'),
('Desain UX untuk Aplikasi', 'Memahami riset pengguna, wireframe, dan prototipe agar aplikasi yang dibuat mudah dipakai dan nyaman dilihat.', '2027-01-12', 'photo/course-5.png'),
('Dasar Kecerdasan Buatan', 'Pengenalan konsep AI, machine learning, dan pemanfaatan AI generatif untuk membantu pekerjaan sehari-hari secara bertanggung jawab.', '2027-01-19', 'photo/course-6.png'),
('Pengembangan Aplikasi Mobile', 'Membuat aplikasi Android sederhana dengan pendekatan visual, mulai dari antarmuka, penyimpanan data, sampai pengujian di perangkat.', '2027-01-26', 'photo/course-7.png');

INSERT INTO user_course (id_course, fullname, phone, email) VALUES
(2, 'Siti Aisyah', '081234567890', 'siti@contoh.test'),
(2, 'Budi Santoso', '082198765432', 'budi@contoh.test'),
(1, 'Dewi Lestari', '085712345678', 'dewi@contoh.test');
