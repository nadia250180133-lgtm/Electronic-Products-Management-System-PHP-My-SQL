<?php
// public/index.php
require_once '../config/db.php';

// Query Ringkasan Statistik
$statStmt = $pdo->query("SELECT 
    COUNT(*) AS total_produk, 
    IFNULL(SUM(stok), 0) AS total_stok, 
    IFNULL(SUM(stok * harga), 0) AS nilai_total_stok 
FROM products");
$stats = $statStmt->fetch();

// Pendapatan Penjualan (Default Rp 0 jika belum ada tabel transaksi)
$pendapatan_penjualan = 0; 

// Query Ambil Semua Data Produk
$stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
$products = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penjualan Produk - Dashboard</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body style="background-color: #a3b8cc;"> <!-- Warna Latar Belakang disesuaikan -->
    <div class="container" style="max-width: 1200px; margin: 20px auto;">
        
        <!-- Header Banner -->
        <div class="header-box">
            <div class="header-title">
                <h1>Electronic Products</h1>
                <p>Electronic Products Management System</p>
            </div>
            <div class="date-badge">
                <?= date('d M Y'); ?>
            </div>
        </div>

        <!-- Dashboard Ringkasan Statistik -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Total Produk</div>
                <div class="stat-value"><?= $stats['total_produk']; ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Total Stok</div>
                <div class="stat-value"><?= $stats['total_stok']; ?> unit</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Nilai Total Stok</div>
                <div class="stat-value">Rp <?= number_format($stats['nilai_total_stok'], 0, ',', '.'); ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Pendapatan Penjualan</div>
                <div class="stat-value">Rp <?= number_format($pendapatan_penjualan, 0, ',', '.'); ?></div>
            </div>
        </div>

        <!-- Tombol Tambah & Alert Flash Message -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 style="color: #ffffff;">Daftar Produk</h2>
            <a href="create.php" class="btn btn-primary">+ Tambah Produk</a>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <!-- List Products (Card Layout) -->
        <?php if (empty($products)): ?>
            <div style="background: white; padding: 30px; border-radius: 10px; text-align: center; color: #64748b;">
                Belum ada produk elektronik yang tersedia.
            </div>
        <?php else: ?>
            <div class="grid">
                <?php foreach ($products as $p): ?>
                    <div class="card">
                        <div>
                            <span class="card-tag"><?= htmlspecialchars($p['kategori']); ?></span>
                            <h2 class="card-title"><?= htmlspecialchars($p['nama']); ?></h2>
                            <div class="card-price">Rp <?= number_format($p['harga'], 0, ',', '.'); ?></div>
                            <div class="card-stock">Sisa Stok: <strong><?= $p['stok']; ?></strong> unit</div>
                        </div>
                        <div class="card-actions">
                            <a href="edit.php?id=<?= $p['id']; ?>" class="btn btn-secondary" style="flex:1; text-align:center;">Edit</a>
                            
                            <!-- Delete via POST + CSRF Protection -->
                            <form action="delete.php" method="POST" style="flex:1;" onsubmit="return confirm('Yakin ingin menghapus produk ini?');">
                                <input type="hidden" name="id" value="<?= $p['id']; ?>">
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
                                <button type="submit" class="btn btn-danger" style="width:100%;">Hapus</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</body>
</html>