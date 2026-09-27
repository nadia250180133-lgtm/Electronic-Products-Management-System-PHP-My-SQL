<?php
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $csrf_token = $_POST['csrf_token'] ?? '';

    // Validasi CSRF Token
    if (!hash_equals($_SESSION['csrf_token'], $csrf_token)) {
        $_SESSION['error'] = "Akses ditolak (Invalid Token).";
        header("Location: index.php");
        exit;
    }

    if ($id) {
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['success'] = "Produk berhasil dihapus.";
    }
}

// Redirect ke index (PRG Pattern)
header("Location: index.php");
exit;