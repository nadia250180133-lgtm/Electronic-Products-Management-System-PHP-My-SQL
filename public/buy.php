<?php
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validasi CSRF Token
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $_SESSION['flash_error'] = "Validasi keamanan gagal!";
        header('Location: index.php');
        exit;
    }

    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $qty = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT) ?? 1;

    if (!$id || $qty <= 0) {
        $_SESSION['flash_error'] = "Jumlah pembelian tidak valid!";
        header('Location: index.php');
        exit;
    }

    try {
        // Ambil data produk
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $product = $stmt->fetch();

        if (!$product) {
            $_SESSION['flash_error'] = "Produk tidak ditemukan!";
            header('Location: index.php');
            exit;
        }

        if ($product['stok'] < $qty) {
            $_SESSION['flash_error'] = "Stok tidak mencukupi! Sisa stok: " . $product['stok'];
            header('Location: index.php');
            exit;
        }

        // Mulai Transaksi Database
        $pdo->beginTransaction();

        // 1. Kurangi Stok Produk
        $updateStmt = $pdo->prepare("UPDATE products SET stok = stok - ? WHERE id = ?");
        $updateStmt->execute([$qty, $id]);

        // 2. Catat Transaksi Penjualan ke tabel sales
        $totalPrice = $product['harga'] * $qty;
        $saleStmt = $pdo->prepare("INSERT INTO sales (product_id, quantity, total_price) VALUES (?, ?, ?)");
        $saleStmt->execute([$id, $qty, $totalPrice]);

        // Commit Transaksi
        $pdo->commit();

        $_SESSION['flash_success'] = "Berhasil membeli " . htmlspecialchars($product['nama']) . " ($qty unit)! Total: Rp " . number_format($totalPrice, 0, ',', '.');

    } catch (PDOException $e) {
        $pdo->rollBack();
        $_SESSION['flash_error'] = "Gagal memproses transaksi: " . $e->getMessage();
    }

    header('Location: index.php');
    exit;
}