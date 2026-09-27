<?php
require_once '../config/db.php';

// Ambil Statistik Produk
$statStmt = $pdo->query("SELECT COUNT(*) AS total_items, SUM(stok) AS total_stock, SUM(stok * harga) AS total_value FROM products");
$stats = $statStmt->fetch();

$totalItems = $stats['total_items'] ?? 0;
$totalStock = $stats['total_stock'] ?? 0;
$totalValue = $stats['total_value'] ?? 0;

// Ambil Total Pendapatan Penjualan dari Tabel Sales
try {
    $salesStmt = $pdo->query("SELECT SUM(total_price) AS total_revenue FROM sales");
    $salesData = $salesStmt->fetch();
    $totalRevenue = $salesData['total_revenue'] ?? 0;
} catch (PDOException $e) {
    $totalRevenue = 0;
}

// Ambil Data Produk
$products = $pdo->query("SELECT * FROM products ORDER BY id ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penjualan Produk - Sistem Informasi Data Produk</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="main-container">
        
        <!-- Header Utama -->
        <div class="header-card">
            <div>
                <h1 class="header-title">Electronic Products</h1>
                <p class="header-subtitle">Electronic Products Management System</p>
            </div>
            <div class="date-badge">
                <?= date('d M Y'); ?>
            </div>
        </div>

        <!-- Flash Messages -->
        <?php if (isset($_SESSION['flash_success'])): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($_SESSION['flash_success']); unset($_SESSION['flash_success']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['flash_error'])): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?>
            </div>
        <?php endif; ?>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <span class="stat-label">Total Produk</span>
                <div class="stat-value"><?= number_format($totalItems, 0, ',', '.'); ?></div>
            </div>
            <div class="stat-card">
                <span class="stat-label">Total Stok</span>
                <div class="stat-value"><?= number_format($totalStock, 0, ',', '.'); ?> <span class="unit-text">unit</span></div>
            </div>
            <div class="stat-card">
                <span class="stat-label">Nilai Total Stok</span>
                <div class="stat-value">Rp <?= number_format($totalValue, 0, ',', '.'); ?></div>
            </div>
            <div class="stat-card">
                <span class="stat-label">Pendapatan Penjualan</span>
                <div class="stat-value">Rp <?= number_format($totalRevenue, 0, ',', '.'); ?></div>
            </div>
        </div>

        <!-- Section Tabel Produk -->
        <div class="table-container-card">
            <div class="table-header-title" style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h2>Daftar Produk</h2>
                    <p>Informasi produk dan pengelolaan stok</p>
                </div>
                <a href="create.php" class="btn-tambah-produk">+ Tambah Produk</a>
            </div>

            <table class="custom-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>PRODUK</th>
                        <th>KATEGORI</th>
                        <th>HARGA</th>
                        <th>STOK</th>
                        <th>KELOLA</th>
                        <th style="text-align: center;">PENJUALAN</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($products)): ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 30px; color: #888;">Belum ada data produk.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($products as $product): ?>
                            <?php 
                                $stok = $product['stok'];
                                $deskripsi = !empty($product['deskripsi']) ? $product['deskripsi'] : 'Informasi produk dan pengelolaan stok.';
                            ?>
                            <tr>
                                <td class="text-id">#<?= $product['id']; ?></td>
                                <td>
                                    <div class="product-info-cell">
                                        <div class="product-box-icon">📦</div>
                                        <div>
                                            <div class="product-name-title"><?= htmlspecialchars($product['nama']); ?></div>
                                            <div class="product-desc-text"><?= htmlspecialchars($deskripsi); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="category-badge"><?= htmlspecialchars($product['kategori']); ?></span>
                                </td>
                                <td class="price-text">Rp <?= number_format($product['harga'], 0, ',', '.'); ?></td>
                                <td class="stock-text"><strong><?= $stok; ?></strong> <span class="unit-text">unit</span></td>
                                <td>
                                    <div style="display: flex; gap: 6px;">
                                        <a href="edit.php?id=<?= $product['id']; ?>" class="btn-edit-action">Edit</a>
                                    </div>
                                </td>
                                <td>
                                    <form action="buy.php" method="POST" class="action-buttons-form">
                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
                                        <input type="hidden" name="id" value="<?= $product['id']; ?>">
                                        
                                        <input type="number" name="quantity" value="1" min="1" max="<?= $stok; ?>" style="width: 45px; padding: 4px; border: 1px solid #cbd5e1; border-radius: 6px; text-align: center;" required>
                                        <button type="submit" class="btn-jual" <?= ($stok <= 0) ? 'disabled' : ''; ?>>Jual</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</body>
</html>