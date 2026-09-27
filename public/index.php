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
    // Jika tabel sales belum dibuat, default ke 0
    $totalRevenue = 0;
}

// Ambil Daftar Produk
$stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC");
$products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Manajemen Produk Elektronik</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container">
        <h1>Sistem Manajemen Produk Elektronik</h1>

        <!-- Flash Message Notification -->
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

        <!-- Dashboard Widgets -->
        <div class="stats-grid">
            <div class="stat-card">
                <p class="stat-label">Total Jenis Produk</p>
                <p class="stat-value"><?= number_format($totalItems, 0, ',', '.'); ?></p>
            </div>
            <div class="stat-card">
                <p class="stat-label">Total Unit Stok</p>
                <p class="stat-value"><?= number_format($totalStock, 0, ',', '.'); ?></p>
            </div>
            <div class="stat-card">
                <p class="stat-label">Total Nilai Stok</p>
                <p class="stat-value">Rp <?= number_format($totalValue, 0, ',', '.'); ?></p>
            </div>
            <div class="stat-card">
                <p class="stat-label">Pendapatan Penjualan</p>
                <p class="stat-value">Rp <?= number_format($totalRevenue, 0, ',', '.'); ?></p>
            </div>
        </div>

        <!-- Action Header -->
        <div style="margin-bottom: 20px;">
            <a href="create.php" class="btn btn-primary">+ Tambah Produk Baru</a>
        </div>

        <!-- Product Cards Grid -->
        <div class="product-grid">
            <?php if (empty($products)): ?>
                <p style="grid-column: 1/-1; text-align: center; color: #666;">Belum ada data produk.</p>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <div class="product-header">
                            <h3><?= htmlspecialchars($product['nama']); ?></h3>
                            <span class="badge"><?= htmlspecialchars($product['kategori']); ?></span>
                        </div>
                        <div class="product-body">
                            <p><strong>Harga:</strong> Rp <?= number_format($product['harga'], 0, ',', '.'); ?></p>
                            <p><strong>Stok:</strong> <?= number_format($product['stok'], 0, ',', '.'); ?> unit</p>
                        </div>
                        
                        <!-- Form Beli / Transaksi Penjualan -->
                        <div style="margin-top: 15px; padding-top: 10px; border-top: 1px solid #eee;">
                            <?php if ($product['stok'] > 0): ?>
                                <form action="buy.php" method="POST" style="display: flex; gap: 8px; align-items: center; justify-content: space-between;">
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
                                    <input type="hidden" name="id" value="<?= $product['id']; ?>">
                                    
                                    <div style="display: flex; align-items: center; gap: 5px;">
                                        <label for="qty-<?= $product['id']; ?>" style="font-size: 0.85rem; color: #555;">Qty:</label>
                                        <input type="number" 
                                               id="qty-<?= $product['id']; ?>"
                                               name="quantity" 
                                               value="1" 
                                               min="1" 
                                               max="<?= $product['stok']; ?>" 
                                               style="width: 50px; padding: 4px; border: 1px solid #ccc; border-radius: 4px; text-align: center;" 
                                               required>
                                    </div>
                                    
                                    <button type="submit" 
                                            style="background-color: #28a745; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-weight: bold; font-size: 0.85rem;"
                                            onclick="return confirm('Konfirmasi pembelian produk ini?')">
                                        🛒 Beli
                                    </button>
                                </form>
                            <?php else: ?>
                                <span style="background-color: #dc3545; color: white; padding: 4px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: bold; display: inline-block;">
                                    Stok Habis
                                </span>
                            <?php endif; ?>
                        </div>

                        <!-- Edit & Delete Actions -->
                        <div class="product-actions" style="margin-top: 10px; display: flex; gap: 8px;">
                            <a href="edit.php?id=<?= $product['id']; ?>" class="btn btn-warning" style="flex: 1; text-align: center;">Edit</a>
                            
                            <form action="delete.php" method="POST" style="flex: 1;" onsubmit="return confirm('Yakin ingin menghapus produk ini?');">
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
                                <input type="hidden" name="id" value="<?= $product['id']; ?>">
                                <button type="submit" class="btn btn-danger" style="width: 100%;">Hapus</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>