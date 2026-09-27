ngelolaan Produk Elektronik

Aplikasi web sederhana berbasis PHP Native dan MySQL untuk mengelola data produk elektronik.

## Cara Menjalankan Aplikasi
1. Pastikan Apache dan MySQL di XAMPP/Laragon sudah dalam kondisi **Active / Running**.
2. Salin folder proyek ini ke dalam direktori server:
   - XAMPP: `C:/xampp/htdocs/`
   - Laragon: `C:/laragon/www/`
3. Buka **phpMyAdmin** (`http://localhost/phpmyadmin`), buat database baru bernama `store_db`.
4. Import file SQL yang berada di folder `database/store_db.sql` ke dalam database `store_db`.
5. Buka browser dan akses alamat URL berikut:
   `http://localhost/electronic products/public/index.php`

## Fitur Utama
- **Create**: Tambah produk dengan validasi (Nama min 3 char & unik, Harga > 0, Stok >= 0).
- **Read**: Tampilan card responsif dengan statistik total produk dan stok.
- **Update**: Edit data produk berdasarkan ID.
- **Delete**: Hapus produk menggunakan metode POST yang dilengkapi proteksi CSRF Token.
- **Keamanan**: PDO Prepared Statements, HTML Escaping (Anti XSS), dan Pola PRG (Post/Redirect/Get).