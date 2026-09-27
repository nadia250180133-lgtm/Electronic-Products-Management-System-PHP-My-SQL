<?php
require_once '../config/db.php';

$id = $_GET['id'] ?? null;
if (!$id) { header("Location: index.php"); exit; }

// Ambil data lama
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) { header("Location: index.php"); exit; }

$errors = [];
$nama     = $product['nama'];
$kategori = $product['kategori'];
$harga    = $product['harga'];
$stok     = $product['stok'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama     = trim($_POST['nama'] ?? '');
    $kategori = trim($_POST['kategori'] ?? '');
    $harga    = filter_var($_POST['harga'], FILTER_VALIDATE_FLOAT);
    $stok     = filter_var($_POST['stok'], FILTER_VALIDATE_INT);

    // Validasi
    if (strlen($nama) < 3) {
        $errors[] = "Nama produk minimal 3 karakter.";
    } else {
        // Cek Nama Unik (Kecuali ID produk ini sendiri)
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE nama = ? AND id != ?");
        $stmt->execute([$nama, $id]);
        if ($stmt->fetchColumn() > 0) {
            $errors[] = "Nama produk sudah ada, gunakan nama lain.";
        }
    }

    if ($harga === false || $harga <= 0) { $errors[] = "Harga harus > 0."; }
    if ($stok === false || $stok < 0) { $errors[] = "Stok harus ≥ 0."; }

    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE products SET nama = ?, kategori = ?, stok = ?, harga = ? WHERE id = ?");
        $stmt->execute([$nama, $kategori, $stok, $harga, $id]);

        $_SESSION['success'] = "Produk berhasil diperbarui!";
        header("Location: index.php"); // PRG Pattern
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Produk Elektronik</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container">
        <div class="form-card">
            <h2>Edit Produk</h2><br>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <?php foreach ($errors as $e): ?>
                        <p>• <?= htmlspecialchars($e); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="form-group">
                    <label>Nama Produk</label>
                    <input type="text" name="nama" value="<?= htmlspecialchars($nama); ?>" required>
                </div>
                <div class="form-group">
                    <label>Kategori</label>
                    <div class="form-group">
                    <label>Kategori</label>
                    <select name="kategori" required>
                     <?php 
                    $categories = [
                         'Smartphone', 
                         'Tablet / iPad', 
                         'Laptop', 
                         'Komponen PC', 
                         'Casing PC', 
                         'Power Supply', 
                         'Printer', 
                         'Kabel & Konektor', 
                         'Aksesori', 
                         'Audio', 
                         'Lainnya'
                     ];
                 foreach ($categories as $cat): 
                    ?>
                      <option value="<?= $cat; ?>" <?= $kategori === $cat ? 'selected' : ''; ?>><?= $cat; ?></option>
                    <?php endforeach; ?>
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
                <button type="submit" class="btn btn-primary" style="width:100%;">Update Produk</button>
                <a href="index.php" class="btn btn-secondary" style="width:100%; text-align:center; margin-top:10px;">Batal</a>
            </form>
        </div>
    </div>
</body>
</html>