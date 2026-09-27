<?php
require_once '../config/db.php';

$errors = [];
$nama = '';
$kategori = 'Smartphone';
$harga = '';
$stok = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("Validasi CSRF Token gagal.");
    }

    $nama = trim($_POST['nama'] ?? '');
    $kategori = trim($_POST['kategori'] ?? '');
    $harga = filter_input(INPUT_POST, 'harga', FILTER_VALIDATE_FLOAT);
    $stok = filter_input(INPUT_POST, 'stok', FILTER_VALIDATE_INT);

    // Validasi Server Side
    if (strlen($nama) < 3) {
        $errors[] = "Nama produk minimal harus 3 karakter.";
    }

    if ($harga === false || $harga <= 0) {
        $errors[] = "Harga produk harus berupa angka lebih dari 0.";
    }

    if ($stok === false || $stok < 0) {
        $errors[] = "Stok produk tidak boleh negatif.";
    }

    // Cek Nama Unik
    if (empty($errors)) {
        $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM products WHERE LOWER(nama) = LOWER(?)");
        $stmtCheck->execute([$nama]);
        if ($stmtCheck->fetchColumn() > 0) {
            $errors[] = "Nama produk sudah terdaftar. Gunakan nama lain.";
        }
    }

    // Simpan Data jika tidak ada error
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO products (nama, kategori, harga, stok) VALUES (?, ?, ?, ?)");
            $stmt->execute([$nama, $kategori, $harga, $stok]);

            $_SESSION['flash_success'] = "Produk '$nama' berhasil ditambahkan!";
            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            $errors[] = "Gagal menyimpan data: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk Baru - Penjualan Produk</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        .form-container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            padding: 30px 35px;
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .form-container h1 {
            font-size: 1.5rem;
            color: #1e3a8a;
            margin-bottom: 6px;
            font-weight: 700;
        }

        .form-container p.subtitle {
            color: #64748b;
            font-size: 0.88rem;
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            color: #334155;
            font-size: 0.9rem;
            margin-bottom: 8px;
        }

        .form-control {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 0.9rem;
            color: #0f172a;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-control:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        }

        .form-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #f1f5f9;
        }

        .btn-simpan {
            background-color: #2563eb;
            color: #ffffff;
            border: none;
            padding: 10px 22px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .btn-simpan:hover {
            background-color: #1d4ed8;
        }

        .btn-batal {
            background-color: #f1f5f9;
            color: #64748b;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: background-color 0.2s;
        }

        .btn-batal:hover {
            background-color: #e2e8f0;
            color: #334155;
        }

        .alert-error-box {
            background-color: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.88rem;
        }

        .alert-error-box ul {
            margin-left: 18px;
            margin-top: 4px;
        }
    </style>
</head>
<body>

    <div class="form-container">
        <h1>Tambah Produk Baru</h1>
        <p class="subtitle">Isi formulir di bawah ini untuk menambahkan data produk ke sistem[cite: 3].</p>

        <?php if (!empty($errors)): ?>
            <div class="alert-error-box">
                <strong>Gagal menyimpan data:</strong>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="create.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">

            <div class="form-group">
                <label for="nama">Nama Produk <span style="color:#ef4444;">*</span></label>
                <input type="text" id="nama" name="nama" class="form-control" placeholder="Contoh: Laptop ASUS" value="<?= htmlspecialchars($nama); ?>" required minlength="3">
                <small style="color:#94a3b8; font-size:0.78rem;">Minimal 3 karakter[cite: 3].</small>
            </div>

            <div class="form-group">
                <label for="kategori">Kategori Produk <span style="color:#ef4444;">*</span></label>
                <select id="kategori" name="kategori" class="form-control">
                    <option value="Smartphone" <?= $kategori === 'Smartphone' ? 'selected' : ''; ?>>Smartphone</option>
                    <option value="Laptop" <?= $kategori === 'Laptop' ? 'selected' : ''; ?>>Laptop</option>
                    <option value="Elektronik" <?= $kategori === 'Elektronik' ? 'selected' : ''; ?>>Elektronik</option>
                    <option value="Aksesoris" <?= $kategori === 'Aksesoris' ? 'selected' : ''; ?>>Aksesoris</option>
                    <option value="Kabel & Konektor" <?= $kategori === 'Kabel & Konektor' ? 'selected' : ''; ?>>Kabel & Konektor</option>
                </select>
            </div>

            <div class="form-group">
                <label for="harga">Harga (Rp) <span style="color:#ef4444;">*</span></label>
                <input type="number" id="harga" name="harga" class="form-control" placeholder="Contoh: 7500000" value="<?= htmlspecialchars($harga); ?>" required min="1" step="any">
            </div>

            <div class="form-group">
                <label for="stok">Jumlah Stok <span style="color:#ef4444;">*</span></label>
                <input type="number" id="stok" name="stok" class="form-control" placeholder="Contoh: 10" value="<?= htmlspecialchars($stok); ?>" required min="0">
            </div>

            <div class="form-actions">
                <a href="index.php" class="btn-batal">Batal</a>
                <button type="submit" class="btn-simpan">Simpan Produk</button>
            </div>
        </form>
    </div>

</body>
</html>