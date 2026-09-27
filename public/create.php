<?php
require_once '../config/db.php';

$errors = [];
$nama = $kategori = $harga = $stok = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama     = trim($_POST['nama'] ?? '');
    $kategori = trim($_POST['kategori'] ?? '');
    $harga    = filter_var($_POST['harga'], FILTER_VALIDATE_FLOAT);
    $stok     = filter_var($_POST['stok'], FILTER_VALIDATE_INT);

    // Validasi data 
    if (strlen($nama) < 3) {
        $errors[] = "Nama produk minimal 3 karakter.";
    } else {
        // Cek Nama Unik
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE nama = ?");
        $stmt->execute([$nama]);
        if ($stmt->fetchColumn() > 0) {
            $errors[] = "Nama produk sudah digunakan! Gunakan nama lain.";
        }
    }

    if ($harga === false || $harga <= 0) {
        $errors[] = "Harga harus angka dan lebih besar dari 0.";
    }

    if ($stok === false || $stok < 0) {
        $errors[] = "Stok harus angka dan tidak boleh kurang dari 0.";
    }

    // Simpan jika tidak ada error
    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO products (nama, kategori, stok, harga) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nama, $kategori, $stok, $harga]);

        $_SESSION['success'] = "Produk berhasil ditambahkan!";
        header("Location: index.php"); // PRG Pattern (Mencegah data ganda saat refresh)
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Produk Elektronik</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container">
        <div class="form-card">
            <h2>Tambah Produk Baru</h2><br>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <?php foreach ($errors as $e): ?>
                        <p>• <?= htmlspecialchars($e); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="form-group">
                    <label>Nama Produk (Min 3 Karakter)</label>
                    <input type="text" name="nama" value="<?= htmlspecialchars($nama); ?>" required>
                </div>
<div class="form-group">
    <label>Kategori</label>
    <select name="kategori" required>
        <option value="Smartphone">Smartphone</option>
        <option value="Tablet / iPad">Tablet / iPad</option>
        <option value="Laptop">Laptop</option>
        <option value="Komponen PC">Komponen PC</option>
        <option value="Casing PC">Casing PC</option>
        <option value="Power Supply">Power Supply (PSU)</option>
        <option value="Printer">Printer</option>
        <option value="Kabel & Konektor">Kabel & Konektor</option>
        <option value="Aksesori">Aksesori</option>
        <option value="Audio">Audio</option>
        <option value="Lainnya">Lainnya</option>
    </select>
</div>
                <div class="form-group">
                    <label>Harga (Rp)</label>
                    <input type="number" step="0.01" name="harga" value="<?= htmlspecialchars($harga); ?>" required>
                </div>
                <div class="form-group">
                    <label>Stok</label>
                    <input type="number" name="stok" value="<?= htmlspecialchars($stok); ?>" required>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;">Simpan Produk</button>
                <a href="index.php" class="btn btn-secondary" style="width:100%; text-align:center; margin-top:10px;">Batal</a>
            </form>
        </div>
    </div>
</body>
</html>