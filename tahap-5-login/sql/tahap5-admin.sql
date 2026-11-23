-- =====================================================
-- TAHAP 5 : Tabel admin untuk login
-- Setelah dijalankan, buka http://localhost/kursus/setup-admin.php satu kali
-- =====================================================
USE course;

CREATE TABLE IF NOT EXISTS admin (
  id_admin INT(3)       NOT NULL AUTO_INCREMENT,
  username VARCHAR(30)  NOT NULL,
  password VARCHAR(255) NOT NULL,
  nama     VARCHAR(50)  NOT NULL,
  PRIMARY KEY (id_admin),
  UNIQUE KEY uq_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
-- Kolom password berisi HASH dari password_hash(), bukan teks asli.
